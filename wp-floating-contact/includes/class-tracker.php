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

        // ── 1. Validate click type ────────────────────────────────────────────
        $click_type = isset( $_POST['click_type'] )
            ? sanitize_key( wp_unslash( $_POST['click_type'] ) )
            : '';

        if ( ! in_array( $click_type, self::ALLOWED_TYPES, true ) ) {
            wp_send_json_error( array( 'message' => 'Invalid click type.' ), 400 );
        }

        // ── 2. Sanitise page URL ──────────────────────────────────────────────
        $page_url = isset( $_POST['page_url'] )
            ? esc_url_raw( wp_unslash( $_POST['page_url'] ) )
            : '';

        // Allow empty URL — insert_log() will fall back to home_url('/').

        // ── 3. Persist ────────────────────────────────────────────────────────
        $result = WPFC_Database::insert_log( $click_type, $page_url );

        if ( false !== $result ) {
            // Return 200 with a minimal body so sendBeacon doesn't complain.
            wp_send_json_success( array( 'ok' => true ) );
        } else {
            wp_send_json_error( array( 'message' => 'DB write failed.' ), 500 );
        }
    }
}
