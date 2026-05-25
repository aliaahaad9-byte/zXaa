<?php
/**
 * Tracker Class
 *
 * Handles the AJAX endpoint that the frontend calls on every button click.
 * Registered for both logged-in (wp_ajax_) and logged-out (wp_ajax_nopriv_)
 * users so all visitors are tracked.
 *
 * @package WP_Floating_Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFC_Tracker {

    /** Click types the endpoint will accept. Anything else is rejected. */
    private const ALLOWED_TYPES = array( 'phone', 'whatsapp' );

    public function __construct() {
        add_action( 'wp_ajax_wpfc_track_click',        array( $this, 'handle_track_click' ) );
        add_action( 'wp_ajax_nopriv_wpfc_track_click', array( $this, 'handle_track_click' ) );
    }

    /**
     * Validates the request, then delegates to the Database layer.
     *
     * Security layers applied:
     *  1. Nonce verification  — prevents CSRF.
     *  2. Type whitelist      — prevents arbitrary string injection.
     *  3. URL sanitisation    — handled by Database::insert_log().
     */
    public function handle_track_click(): void {

        // 1. Nonce check — send_json_error terminates execution on failure.
        if ( ! check_ajax_referer( 'wpfc_track_click', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed.' ), 403 );
        }

        // 2. Sanitise & validate click_type.
        $click_type = isset( $_POST['click_type'] )
            ? sanitize_key( wp_unslash( $_POST['click_type'] ) )
            : '';

        if ( ! in_array( $click_type, self::ALLOWED_TYPES, true ) ) {
            wp_send_json_error( array( 'message' => 'Invalid click type.' ), 400 );
        }

        // 3. Sanitise page_url — esc_url_raw also applied inside insert_log().
        $page_url = isset( $_POST['page_url'] )
            ? esc_url_raw( wp_unslash( $_POST['page_url'] ) )
            : '';

        if ( empty( $page_url ) ) {
            wp_send_json_error( array( 'message' => 'Missing page URL.' ), 400 );
        }

        // 4. Persist.
        $result = WPFC_Database::insert_log( $click_type, $page_url );

        if ( false !== $result ) {
            wp_send_json_success( array( 'message' => 'Tracked.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Database write failed.' ), 500 );
        }
    }
}
