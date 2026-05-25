<?php
/**
 * Database Class
 *
 * Handles all direct database interactions: table creation, CRUD operations,
 * and cleanup. All methods are static — no instantiation required.
 *
 * @package WP_Floating_Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFC_Database {

    /** Database table name (without prefix). */
    private static string $table = 'wpfc_click_logs';

    // ─── Schema ───────────────────────────────────────────────────────────────

    /**
     * Creates the click-log table. Called on plugin activation via dbDelta so
     * it is safe to run on repeated activations (upgrade-safe).
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

        // Record the installed DB schema version for future migrations.
        add_option( 'wpfc_db_version', '1.0.0' );
    }

    /**
     * Drops the table on plugin uninstall (called from main file, not deactivation).
     */
    public static function drop_table(): void {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table;
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
    }

    /** No-op deactivation hook — data is intentionally kept across deactivations. */
    public static function deactivate(): void {}

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Returns the fully-prefixed table name. */
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
     * @return int|false          Number of rows inserted, or false on failure.
     */
    public static function insert_log( string $click_type, string $page_url ) {
        global $wpdb;

        return $wpdb->insert(
            self::get_table_name(),
            array(
                'click_type' => sanitize_text_field( $click_type ),
                'page_url'   => esc_url_raw( $page_url ),
                'clicked_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s' )
        );
    }

    /**
     * Truncates the entire log table (irreversible — caller must confirm intent).
     *
     * @return int|bool  Result of TRUNCATE, or false on failure.
     */
    public static function clear_logs() {
        global $wpdb;
        $table_name = self::get_table_name();
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return $wpdb->query( "TRUNCATE TABLE {$table_name}" );
    }

    // ─── Read ─────────────────────────────────────────────────────────────────

    /**
     * Returns a paginated result set ordered newest-first.
     *
     * @param int $page     1-based page number.
     * @param int $per_page Rows per page.
     * @return array        Array of stdClass row objects.
     */
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
        );
    }

    /**
     * Total row count across the entire table.
     */
    public static function get_total_count(): int {
        global $wpdb;
        $table_name = self::get_table_name();
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
    }

    /**
     * Row count filtered by click type.
     *
     * @param string $type  'phone' or 'whatsapp'.
     */
    public static function get_count_by_type( string $type ): int {
        global $wpdb;
        $table_name = self::get_table_name();
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return (int) $wpdb->get_var(
            $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE click_type = %s", $type )
        );
    }
}
