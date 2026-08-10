<?php
/**
 * Tracker Class
 *
 * Why we use wp-admin/admin-ajax.php instead of the REST API:
 *
 * sendBeacon() with a Blob typed as 'application/x-www-form-urlencoded' is
 * unreliable on Safari iOS — the browser sometimes strips the Content-Type
 * header, so PHP's $_POST superglobal is empty and the request appears empty.
 *
 * sendBeacon() with a FormData object ALWAYS sends as 'multipart/form-data'
 * which PHP parses natively and reliably into $_POST on every browser and OS.
 * We therefore route tracking through admin-ajax.php (which reads $_POST)
 * instead of the REST API (which has its own body-parsing logic).
 *
 * No nonce is required: this is anonymous analytics. The only security check
 * is a type whitelist to prevent garbage being written to the DB.
 *
 * @package WP_Floating_Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFC_Tracker {

    /** Only these two values are accepted as click_type. */
    private const ALLOWED_TYPES = array( 'phone', 'whatsapp' );

    public function __construct() {
        // Both hooks needed: nopriv = visitors, wp_ajax = logged-in users.
        add_action( 'wp_ajax_wpfc_track_click',        array( $this, 'handle_track' ) );
        add_action( 'wp_ajax_nopriv_wpfc_track_click', array( $this, 'handle_track' ) );
    }

    /**
     * Handles a tracking POST from the frontend.
     *
     * Expected $_POST fields (sent as multipart/form-data via FormData):
     *   action     = 'wpfc_track_click'
     *   click_type = 'phone' | 'whatsapp'
     *   page_url   = the URL of the page where the button was clicked
     */
    public function handle_track(): void {

        // ── 0. Rate-limit: max 15 requests per IP per 60 seconds ─────────────
        // Prevents automated scripts from flooding the analytics database.
        $ip_raw  = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $ip_key  = 'wpfc_rl_' . md5( $ip_raw );
        $hits    = (int) get_transient( $ip_key );
        if ( $hits >= 15 ) {
            wp_send_json_error( null, 429 );
        }
        set_transient( $ip_key, $hits + 1, 60 );

        // ── 1. Validate click type ────────────────────────────────────────────
        $click_type = isset( $_POST['click_type'] )
            ? sanitize_key( wp_unslash( $_POST['click_type'] ) )
            : '';

        if ( ! in_array( $click_type, self::ALLOWED_TYPES, true ) ) {
            wp_send_json_error( null, 400 );
        }

        // ── 2. Sanitise and cap page URL ──────────────────────────────────────
        $raw_url  = isset( $_POST['page_url'] ) ? wp_unslash( $_POST['page_url'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $page_url = esc_url_raw( $raw_url );

        // Hard cap: 2 000 chars is sufficient for any real-world URL.
        if ( strlen( $page_url ) > 2000 ) {
            $page_url = substr( $page_url, 0, 2000 );
        }

        // ── 2b. Sanitise page title ───────────────────────────────────────────
        $page_title = isset( $_POST['page_title'] )
            ? sanitize_text_field( wp_unslash( $_POST['page_title'] ) )
            : '';

        // ── 3. Persist ────────────────────────────────────────────────────────
        $result = WPFC_Database::insert_log( $click_type, $page_url, $page_title );

        if ( false !== $result ) {
            wp_send_json_success( array( 'ok' => true ) );
        } else {
            wp_send_json_error( null, 500 );
        }
    }
}
