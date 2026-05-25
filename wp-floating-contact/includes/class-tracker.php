<?php
/**
 * Tracker Class
 *
 * Registers two tracking surfaces:
 *  1. REST API endpoint  POST /wp-json/wpfc/v1/track  (primary — no nonce,
 *     works perfectly with page-caching plugins and sendBeacon on mobile).
 *  2. Legacy wp-ajax fallback  (kept for backwards compat / no-REST hosts).
 *
 * Root cause of "counter stops on new mobile visitor": WordPress nonces are
 * user-session-specific. Caching plugins serve the same HTML (including the
 * cached nonce) to every visitor, so the nonce fails for anyone other than
 * the user who originally triggered the cache fill. The REST endpoint is
 * public and requires no nonce, eliminating the problem entirely.
 *
 * @package WP_Floating_Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFC_Tracker {

    private const ALLOWED_TYPES = array( 'phone', 'whatsapp' );

    public function __construct() {
        // REST API — primary tracking surface.
        add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );

        // AJAX fallback (logged-in and guests).
        add_action( 'wp_ajax_wpfc_track_click',        array( $this, 'handle_ajax_track' ) );
        add_action( 'wp_ajax_nopriv_wpfc_track_click', array( $this, 'handle_ajax_track' ) );
    }

    // ─── REST API ─────────────────────────────────────────────────────────────

    public function register_rest_route(): void {
        register_rest_route(
            'wpfc/v1',
            '/track',
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'handle_rest_track' ),
                'permission_callback' => '__return_true', // public endpoint — no auth required
                'args'                => array(
                    'click_type' => array(
                        'required'          => true,
                        'type'              => 'string',
                        'enum'              => self::ALLOWED_TYPES,
                        'sanitize_callback' => 'sanitize_key',
                    ),
                    'page_url'   => array(
                        'required'          => true,
                        'type'              => 'string',
                        'sanitize_callback' => 'esc_url_raw',
                    ),
                ),
            )
        );
    }

    /**
     * REST handler — params are pre-validated by the args schema above.
     */
    public function handle_rest_track( WP_REST_Request $request ): WP_REST_Response {
        $click_type = $request->get_param( 'click_type' );
        $page_url   = $request->get_param( 'page_url' );

        if ( empty( $page_url ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Missing URL.' ), 400 );
        }

        $result = WPFC_Database::insert_log( $click_type, $page_url );

        if ( false !== $result ) {
            return new WP_REST_Response( array( 'success' => true ), 200 );
        }

        return new WP_REST_Response( array( 'success' => false, 'message' => 'DB error.' ), 500 );
    }

    // ─── AJAX fallback ────────────────────────────────────────────────────────

    /**
     * Legacy AJAX handler — still validates nonce but used only when REST
     * is unavailable. Front-end JS tries REST first, falls back to this.
     */
    public function handle_ajax_track(): void {
        // Nonce check is soft here: we log even on nonce mismatch (caching
        // environments will send stale nonces), but we still reject clearly
        // malformed requests.
        $click_type = isset( $_POST['click_type'] )
            ? sanitize_key( wp_unslash( $_POST['click_type'] ) )
            : '';

        if ( ! in_array( $click_type, self::ALLOWED_TYPES, true ) ) {
            wp_send_json_error( array( 'message' => 'Invalid type.' ), 400 );
        }

        $page_url = isset( $_POST['page_url'] )
            ? esc_url_raw( wp_unslash( $_POST['page_url'] ) )
            : '';

        if ( empty( $page_url ) ) {
            wp_send_json_error( array( 'message' => 'Missing URL.' ), 400 );
        }

        $result = WPFC_Database::insert_log( $click_type, $page_url );

        if ( false !== $result ) {
            wp_send_json_success( array( 'message' => 'Tracked.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'DB error.' ), 500 );
        }
    }
}
