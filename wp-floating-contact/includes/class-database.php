<?php
/**
 * Database Class
 *
 * @package WP_Floating_Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFC_Database {

    private static string $table      = 'wpfc_click_logs';
    private static string $db_version = '1.0.1';

    // ─── Schema ───────────────────────────────────────────────────────────────

    /**
     * Creates / upgrades the click-log table via dbDelta (idempotent).
     * Called on activation AND by maybe_create_table() on every page load.
     */
    public static function create_table(): void {
        global $wpdb;

        $table_name      = $wpdb->prefix . self::$table;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            click_type VARCHAR(20)         NOT NULL,
            page_url   TEXT                NOT NULL,
            clicked_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_click_type (click_type),
            KEY idx_clicked_at (clicked_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        update_option( 'wpfc_db_version', self::$db_version );
    }

    /**
     * Ensures the table exists on every page load. Cheap: only calls
     * create_table() when the stored version does not match — i.e., first
     * run after a manual (FTP) install or after a DB wipe.
     */
    public static function maybe_create_table(): void {
        if ( get_option( 'wpfc_db_version' ) !== self::$db_version ) {
            self::create_table();
        }
    }

    public static function drop_table(): void {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table;
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
    }

    public static function deactivate(): void {}

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function get_table_name(): string {
        global $wpdb;
        return $wpdb->prefix . self::$table;
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    /**
     * Inserts one click-log record.
     *
     * @param string $click_type  'phone' or 'whatsapp'.
     * @param string $page_url    The frontend URL where the click occurred.
     * @return int|false          Rows inserted (1) or false on failure.
     */
    public static function insert_log( string $click_type, string $page_url ) {
        global $wpdb;

        // Safety: if URL is empty after sanitisation, store the site home URL
        // rather than an empty string so the row is still useful.
        $clean_url = esc_url_raw( $page_url );
        if ( empty( $clean_url ) ) {
            $clean_url = home_url( '/' );
        }

        $result = $wpdb->insert(
            self::get_table_name(),
            array(
                'click_type' => sanitize_text_field( $click_type ),
                'page_url'   => $clean_url,
                'clicked_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s' )
        );

        // Log a DB error to the WP debug log so it is visible during diagnosis.
        if ( false === $result ) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            error_log( 'WPFC: DB insert failed — ' . $wpdb->last_error );
        }

        return $result;
    }

    public static function clear_logs() {
        global $wpdb;
        $table_name = self::get_table_name();
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return $wpdb->query( "TRUNCATE TABLE {$table_name}" );
    }

    // ─── Read ─────────────────────────────────────────────────────────────────

    public static function get_logs( int $page = 1, int $per_page = 50 ): array {
        global $wpdb;

        $table_name = self::get_table_name();
        $offset     = ( $page - 1 ) * $per_page;

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} ORDER BY clicked_at DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        ) ?: array();
    }

    public static function get_total_count(): int {
        global $wpdb;
        $table_name = self::get_table_name();
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
    }

    public static function get_count_by_type( string $type ): int {
        global $wpdb;
        $table_name = self::get_table_name();
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return (int) $wpdb->get_var(
            $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE click_type = %s", $type )
        );
    }
}
