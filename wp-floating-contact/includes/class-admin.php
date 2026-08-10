<?php
/**
 * Admin Class
 *
 * Registers the admin menu, settings, asset enqueueing, and all admin-side
 * AJAX handlers. Renders three pages: Dashboard, Settings, and Analytics.
 *
 * @package WP_Floating_Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFC_Admin {

    /** Fallback values for every setting key. */
    private array $defaults = array(
        'phone_number'     => '',
        'whatsapp_number'  => '',
        'whatsapp_message' => 'Hello! I would like to get in touch.',
        'button_position'  => 'bottom-right',
        'enable_plugin'    => '1',
        'phone_color'      => '#1362bc',
        'phone_label'      => 'اتصل بنا',
        'whatsapp_label'   => 'واتساب',
    );

    // ─── Bootstrap ────────────────────────────────────────────────────────────

    public function __construct() {
        add_action( 'admin_menu',                    array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init',                    array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts',         array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_wpfc_clear_logs',       array( $this, 'ajax_clear_logs' ) );
        add_action( 'wp_ajax_wpfc_get_stats',        array( $this, 'ajax_get_stats' ) );
        add_action( 'wp_ajax_wpfc_activate_license', array( $this, 'ajax_activate_license' ) );
        add_action( 'wp_ajax_wpfc_deactivate_license', array( $this, 'ajax_deactivate_license' ) );
        add_action( 'add_meta_boxes',                array( $this, 'add_contact_meta_box' ) );
        add_action( 'save_post',                     array( $this, 'save_contact_meta_box' ) );
    }

    // ─── Menu ─────────────────────────────────────────────────────────────────

    public function add_admin_menu(): void {
        // ── Trial expired AND no valid license → show only activation screen ──
        if ( ! WPFC_License::can_use() ) {
            add_menu_page(
                __( 'Floating Contact — Activate', 'wp-floating-contact' ),
                __( 'Floating Contact', 'wp-floating-contact' ),
                'manage_options',
                'wp-floating-contact',
                array( $this, 'render_license_page' ),
                'dashicons-lock',
                58
            );
            return;
        }

        // ── Licensed: full menu ───────────────────────────────────────────────
        add_menu_page(
            __( 'Floating Contact', 'wp-floating-contact' ),
            __( 'Floating Contact', 'wp-floating-contact' ),
            'manage_options',
            'wp-floating-contact',
            array( $this, 'render_dashboard_page' ),
            'dashicons-phone',
            58
        );

        add_submenu_page(
            'wp-floating-contact',
            __( 'Dashboard', 'wp-floating-contact' ),
            __( 'Dashboard', 'wp-floating-contact' ),
            'manage_options',
            'wp-floating-contact',
            array( $this, 'render_dashboard_page' )
        );

        add_submenu_page(
            'wp-floating-contact',
            __( 'Settings', 'wp-floating-contact' ),
            __( 'Settings', 'wp-floating-contact' ),
            'manage_options',
            'wpfc-settings',
            array( $this, 'render_settings_page' )
        );

        add_submenu_page(
            'wp-floating-contact',
            __( 'Analytics', 'wp-floating-contact' ),
            __( 'Analytics', 'wp-floating-contact' ),
            'manage_options',
            'wpfc-analytics',
            array( $this, 'render_analytics_page' )
        );

        add_submenu_page(
            'wp-floating-contact',
            __( 'License', 'wp-floating-contact' ),
            __( 'License', 'wp-floating-contact' ),
            'manage_options',
            'wpfc-license',
            array( $this, 'render_license_page' )
        );
    }

    // ─── Settings API ─────────────────────────────────────────────────────────

    public function register_settings(): void {
        register_setting(
            'wpfc_settings_group',
            'wpfc_settings',
            array( 'sanitize_callback' => array( $this, 'sanitize_settings' ) )
        );
    }

    /**
     * Sanitizes and validates each settings field before it is stored.
     *
     * @param mixed $raw  Raw POST data from the settings form.
     * @return array      Clean, validated settings array.
     */
    public function sanitize_settings( $raw ): array {
        $input = is_array( $raw ) ? $raw : array();

        $clean = array();

        $clean['phone_number']     = sanitize_text_field( $input['phone_number'] ?? '' );
        $clean['whatsapp_number']  = sanitize_text_field( $input['whatsapp_number'] ?? '' );
        $clean['whatsapp_message'] = sanitize_textarea_field( $input['whatsapp_message'] ?? '' );
        $clean['enable_plugin']    = isset( $input['enable_plugin'] ) ? '1' : '0';
        $clean['phone_color']      = sanitize_hex_color( $input['phone_color'] ?? '#1362bc' ) ?: '#1362bc';

        // Whitelist the position value so nothing unexpected is stored.
        $allowed_positions         = array( 'bottom-right', 'bottom-left' );
        $clean['button_position']  = in_array( $input['button_position'] ?? '', $allowed_positions, true )
                                        ? $input['button_position']
                                        : 'bottom-right';

        $clean['phone_label']    = sanitize_text_field( $input['phone_label']    ?? 'اتصل بنا' );
        $clean['whatsapp_label'] = sanitize_text_field( $input['whatsapp_label'] ?? 'واتساب' );

        return $clean;
    }

    /**
     * Returns saved settings merged with defaults so callers always get a full array.
     */
    public function get_settings(): array {
        return wp_parse_args( get_option( 'wpfc_settings', array() ), $this->defaults );
    }

    // ─── Assets ───────────────────────────────────────────────────────────────

    public function enqueue_assets( string $hook ): void {
        $plugin_pages = array(
            'toplevel_page_wp-floating-contact',
            'floating-contact_page_wpfc-settings',
            'floating-contact_page_wpfc-analytics',
            'floating-contact_page_wpfc-license',
        );

        if ( ! in_array( $hook, $plugin_pages, true ) ) {
            return;
        }

        // ── License page assets (always, regardless of active state) ──────────
        wp_enqueue_style(
            'wpfc-license',
            WPFC_PLUGIN_URL . 'assets/css/license.css',
            array(),
            WPFC_VERSION
        );

        wp_enqueue_script(
            'wpfc-license',
            WPFC_PLUGIN_URL . 'assets/js/license.js',
            array(),
            WPFC_VERSION,
            true
        );

        wp_localize_script(
            'wpfc-license',
            'wpfc_license',
            array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'nonce'              => wp_create_nonce( 'wpfc_license_nonce' ),
                'msg_empty'          => __( 'Please enter your serial number.', 'wp-floating-contact' ),
                'msg_invalid'        => __( 'Invalid serial number. Please check and try again.', 'wp-floating-contact' ),
                'msg_success'        => __( 'License activated! Loading your dashboard…', 'wp-floating-contact' ),
                'msg_error'          => __( 'Connection error. Please try again.', 'wp-floating-contact' ),
                'activate_label'     => __( 'Activate License', 'wp-floating-contact' ),
                'activating'         => __( 'Activating…', 'wp-floating-contact' ),
                'confirm_deactivate' => __( 'Deactivate this license? The plugin will stop working until a new serial is entered.', 'wp-floating-contact' ),
            )
        );

        // Load full admin assets whenever the plugin can be used (trial or licensed).
        if ( ! WPFC_License::can_use() ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_style(
            'wpfc-admin',
            WPFC_PLUGIN_URL . 'assets/css/admin.css',
            array( 'wp-color-picker' ),
            WPFC_VERSION
        );

        wp_enqueue_script(
            'wpfc-admin',
            WPFC_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery', 'wp-color-picker' ),
            WPFC_VERSION,
            true
        );

        wp_localize_script(
            'wpfc-admin',
            'wpfc_admin',
            array(
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                'nonce'         => wp_create_nonce( 'wpfc_admin_nonce' ),
                'confirm_clear' => __( 'Are you sure you want to permanently delete ALL click logs? This cannot be undone.', 'wp-floating-contact' ),
                'clearing'      => __( 'Clearing…', 'wp-floating-contact' ),
                'cleared'       => __( 'All logs cleared successfully!', 'wp-floating-contact' ),
                'error'         => __( 'An error occurred. Please try again.', 'wp-floating-contact' ),
                'poll_interval' => 6000,  // ms — live stats refresh rate
                'is_dashboard'  => ( strpos( $hook, 'wp-floating-contact' ) !== false && strpos( $hook, 'settings' ) === false && strpos( $hook, 'analytics' ) === false ) ? '1' : '0',
            )
        );
    }

    // ─── AJAX ─────────────────────────────────────────────────────────────────

    public function ajax_clear_logs(): void {
        check_ajax_referer( 'wpfc_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'wp-floating-contact' ) ), 403 );
        }

        $result = WPFC_Database::clear_logs();

        if ( false !== $result ) {
            wp_send_json_success( array( 'message' => __( 'All logs cleared.', 'wp-floating-contact' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Database error — could not clear logs.', 'wp-floating-contact' ) ), 500 );
        }
    }

    /**
     * Returns live click counts for the real-time dashboard poller.
     */
    public function ajax_get_stats(): void {
        check_ajax_referer( 'wpfc_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( null, 403 );
        }

        wp_send_json_success( array(
            'total'    => WPFC_Database::get_total_count(),
            'phone'    => WPFC_Database::get_count_by_type( 'phone' ),
            'whatsapp' => WPFC_Database::get_count_by_type( 'whatsapp' ),
        ) );
    }

    // ─── License AJAX ─────────────────────────────────────────────────────────

    public function ajax_activate_license(): void {
        check_ajax_referer( 'wpfc_license_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ), 403 );
        }

        $serial = isset( $_POST['serial'] ) ? sanitize_text_field( wp_unslash( $_POST['serial'] ) ) : '';

        if ( empty( $serial ) ) {
            wp_send_json_error( array( 'message' => __( 'Serial number is required.', 'wp-floating-contact' ) ), 400 );
        }

        if ( WPFC_License::activate( $serial ) ) {
            wp_send_json_success( array( 'message' => __( 'License activated successfully.', 'wp-floating-contact' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Invalid serial number. Please check and try again.', 'wp-floating-contact' ) ), 400 );
        }
    }

    public function ajax_deactivate_license(): void {
        check_ajax_referer( 'wpfc_license_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ), 403 );
        }

        WPFC_License::deactivate();
        wp_send_json_success( array( 'message' => __( 'License deactivated.', 'wp-floating-contact' ) ) );
    }

    // ─── Page: License ────────────────────────────────────────────────────────

    public function render_license_page(): void {
        $is_active      = WPFC_License::is_active();
        $trial_active   = WPFC_License::is_trial_active();
        $trial_expired  = WPFC_License::trial_expired();
        $days_left      = WPFC_License::trial_days_remaining();
        $buy_url        = WPFC_License::BUY_WHATSAPP;
        ?>
        <div class="wpfc-license-wrap">
            <div class="wpfc-license-card">

                <!-- Icon -->
                <div class="wpfc-license-icon <?php echo $trial_expired && ! $is_active ? 'wpfc-icon-expired' : ''; ?>">
                    <span class="dashicons <?php echo $is_active ? 'dashicons-yes-alt' : ( $trial_expired ? 'dashicons-warning' : 'dashicons-clock' ); ?>"></span>
                </div>

                <?php if ( $is_active ) : ?>
                <!-- ══ State 1: Licensed ══ -->
                <h1><?php esc_html_e( 'License Active', 'wp-floating-contact' ); ?></h1>
                <p class="wpfc-license-subtitle">
                    <?php esc_html_e( 'Your plugin is fully activated and running.', 'wp-floating-contact' ); ?>
                </p>

                <div class="wpfc-license-active-badge">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php esc_html_e( 'Licensed', 'wp-floating-contact' ); ?>
                </div>

                <div class="wpfc-license-serial-display">
                    <?php echo esc_html( WPFC_License::get_masked_display() ); ?>
                </div>

                <button id="wpfc-deactivate-btn" class="wpfc-deactivate-btn">
                    <span class="dashicons dashicons-no-alt" style="vertical-align:middle;font-size:14px;width:14px;height:14px;margin-right:4px;"></span>
                    <?php esc_html_e( 'Deactivate License', 'wp-floating-contact' ); ?>
                </button>

                <?php elseif ( $trial_expired ) : ?>
                <!-- ══ State 2: Trial Expired ══ -->
                <h1><?php esc_html_e( 'Trial Period Ended', 'wp-floating-contact' ); ?></h1>
                <p class="wpfc-license-subtitle">
                    <?php esc_html_e( 'Your 3-day free trial has expired. Enter a serial number to continue using the plugin, or purchase a license via WhatsApp.', 'wp-floating-contact' ); ?>
                </p>

                <a href="<?php echo esc_url( $buy_url ); ?>" target="_blank" rel="noopener noreferrer" class="wpfc-buy-btn wpfc-buy-btn-large">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    <?php esc_html_e( 'Buy License via WhatsApp', 'wp-floating-contact' ); ?>
                </a>

                <div class="wpfc-license-divider">
                    <span><?php esc_html_e( 'or enter a serial number', 'wp-floating-contact' ); ?></span>
                </div>

                <form id="wpfc-license-form" class="wpfc-license-form" autocomplete="off">
                    <label for="wpfc-serial-input"><?php esc_html_e( 'Serial Number', 'wp-floating-contact' ); ?></label>
                    <div class="wpfc-serial-input-wrap">
                        <input type="text" id="wpfc-serial-input" class="wpfc-serial-input"
                               placeholder="WPFC-XXXXXX-XXXXXX-XXXXXX" maxlength="25"
                               spellcheck="false" autocomplete="off" autocorrect="off" autocapitalize="characters">
                    </div>
                    <button type="submit" id="wpfc-activate-btn" class="wpfc-activate-btn">
                        <span class="dashicons dashicons-shield-alt" style="font-size:18px;width:18px;height:18px;line-height:1;"></span>
                        <?php esc_html_e( 'Activate License', 'wp-floating-contact' ); ?>
                    </button>
                    <div id="wpfc-license-msg" class="wpfc-license-msg"></div>
                </form>

                <?php else : ?>
                <!-- ══ State 3: Trial Active ══ -->
                <h1><?php esc_html_e( 'Free Trial Active', 'wp-floating-contact' ); ?></h1>
                <p class="wpfc-license-subtitle">
                    <?php esc_html_e( 'You are using the free trial. Activate a license before it expires to keep the plugin running.', 'wp-floating-contact' ); ?>
                </p>

                <!-- Trial countdown badge -->
                <div class="wpfc-trial-countdown <?php echo $days_left <= 3 ? 'wpfc-trial-urgent' : ''; ?>">
                    <span class="wpfc-trial-days"><?php echo esc_html( $days_left ); ?></span>
                    <span class="wpfc-trial-label">
                        <?php echo esc_html(
                            $days_left === 1
                                ? __( 'day remaining', 'wp-floating-contact' )
                                : __( 'days remaining', 'wp-floating-contact' )
                        ); ?>
                    </span>
                </div>

                <form id="wpfc-license-form" class="wpfc-license-form" autocomplete="off">
                    <label for="wpfc-serial-input"><?php esc_html_e( 'Serial Number', 'wp-floating-contact' ); ?></label>
                    <div class="wpfc-serial-input-wrap">
                        <input type="text" id="wpfc-serial-input" class="wpfc-serial-input"
                               placeholder="WPFC-XXXXXX-XXXXXX-XXXXXX" maxlength="25"
                               spellcheck="false" autocomplete="off" autocorrect="off" autocapitalize="characters">
                    </div>
                    <button type="submit" id="wpfc-activate-btn" class="wpfc-activate-btn">
                        <span class="dashicons dashicons-shield-alt" style="font-size:18px;width:18px;height:18px;line-height:1;"></span>
                        <?php esc_html_e( 'Activate License', 'wp-floating-contact' ); ?>
                    </button>
                    <div id="wpfc-license-msg" class="wpfc-license-msg"></div>
                </form>

                <?php endif; ?>

                <div class="wpfc-license-footer">
                    <strong><?php esc_html_e( 'WP Floating Contact Buttons', 'wp-floating-contact' ); ?></strong>
                    <?php esc_html_e( '— Each serial number is for one site only.', 'wp-floating-contact' ); ?>
                </div>

            </div>
        </div>
        <?php
    }

    // ─── Page: Dashboard ───────────────────────────────────────────────────────

    public function render_dashboard_page(): void {
        $settings     = $this->get_settings();
        $total_clicks = WPFC_Database::get_total_count();
        $phone_clicks = WPFC_Database::get_count_by_type( 'phone' );
        $wa_clicks    = WPFC_Database::get_count_by_type( 'whatsapp' );
        $recent_logs  = WPFC_Database::get_logs( 1, 10 );
        $is_active    = $settings['enable_plugin'] === '1';
        $is_licensed  = WPFC_License::is_active();
        $trial_active = WPFC_License::is_trial_active();
        $days_left    = WPFC_License::trial_days_remaining();
        $buy_url      = WPFC_License::BUY_WHATSAPP;
        ?>
        <div class="wrap wpfc-wrap">

            <h1 class="wpfc-page-title">
                <span class="dashicons dashicons-phone"></span>
                <?php esc_html_e( 'Floating Contact — Dashboard', 'wp-floating-contact' ); ?>
                <span class="wpfc-live-badge" title="<?php esc_attr_e( 'Live — updates every 6 seconds', 'wp-floating-contact' ); ?>">
                    <span class="wpfc-live-dot"></span>
                    <?php esc_html_e( 'LIVE', 'wp-floating-contact' ); ?>
                </span>
            </h1>

            <?php if ( ! $is_licensed && $trial_active ) : ?>
            <!-- ── Trial banner ── -->
            <div class="wpfc-trial-banner <?php echo $days_left <= 3 ? 'wpfc-trial-banner-urgent' : ''; ?>">
                <span class="dashicons dashicons-clock wpfc-trial-banner-icon"></span>
                <div class="wpfc-trial-banner-text">
                    <strong>
                        <?php printf(
                            esc_html( _n( '%d day remaining in your free trial', '%d days remaining in your free trial', $days_left, 'wp-floating-contact' ) ),
                            $days_left
                        ); ?>
                    </strong>
                    <span><?php esc_html_e( 'Activate a license to keep the plugin running after the trial ends.', 'wp-floating-contact' ); ?></span>
                </div>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpfc-license' ) ); ?>" class="wpfc-trial-banner-cta">
                    <?php esc_html_e( 'Activate License', 'wp-floating-contact' ); ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- ── Stats row ── -->
            <div class="wpfc-stats-grid">

                <div class="wpfc-stat-card wpfc-stat-total">
                    <div class="wpfc-stat-icon"><span class="dashicons dashicons-chart-bar"></span></div>
                    <div class="wpfc-stat-content">
                        <span class="wpfc-stat-number" id="wpfc-stat-total"><?php echo esc_html( number_format( $total_clicks ) ); ?></span>
                        <span class="wpfc-stat-label"><?php esc_html_e( 'Total Clicks', 'wp-floating-contact' ); ?></span>
                    </div>
                </div>

                <div class="wpfc-stat-card wpfc-stat-phone">
                    <div class="wpfc-stat-icon"><span class="dashicons dashicons-phone"></span></div>
                    <div class="wpfc-stat-content">
                        <span class="wpfc-stat-number" id="wpfc-stat-phone"><?php echo esc_html( number_format( $phone_clicks ) ); ?></span>
                        <span class="wpfc-stat-label"><?php esc_html_e( 'Phone Clicks', 'wp-floating-contact' ); ?></span>
                    </div>
                </div>

                <div class="wpfc-stat-card wpfc-stat-whatsapp">
                    <div class="wpfc-stat-icon"><span class="dashicons dashicons-whatsapp"></span></div>
                    <div class="wpfc-stat-content">
                        <span class="wpfc-stat-number" id="wpfc-stat-whatsapp"><?php echo esc_html( number_format( $wa_clicks ) ); ?></span>
                        <span class="wpfc-stat-label"><?php esc_html_e( 'WhatsApp Clicks', 'wp-floating-contact' ); ?></span>
                    </div>
                </div>

                <div class="wpfc-stat-card wpfc-stat-status">
                    <div class="wpfc-stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                    <div class="wpfc-stat-content">
                        <span class="wpfc-stat-number <?php echo $is_active ? 'wpfc-status-active' : 'wpfc-status-inactive'; ?>">
                            <?php echo $is_active ? esc_html__( 'Active', 'wp-floating-contact' ) : esc_html__( 'Inactive', 'wp-floating-contact' ); ?>
                        </span>
                        <span class="wpfc-stat-label"><?php esc_html_e( 'Plugin Status', 'wp-floating-contact' ); ?></span>
                    </div>
                </div>

            </div><!-- /.wpfc-stats-grid -->

            <!-- ── Two-column layout ── -->
            <div class="wpfc-dashboard-grid">

                <!-- Config summary -->
                <div class="wpfc-card">
                    <h2><?php esc_html_e( 'Current Configuration', 'wp-floating-contact' ); ?></h2>
                    <table class="wpfc-config-table">
                        <tr>
                            <td><?php esc_html_e( 'Phone Number:', 'wp-floating-contact' ); ?></td>
                            <td>
                                <?php if ( $settings['phone_number'] ) : ?>
                                    <strong><?php echo esc_html( $settings['phone_number'] ); ?></strong>
                                <?php else : ?>
                                    <em><?php esc_html_e( 'Not set', 'wp-floating-contact' ); ?></em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'WhatsApp Number:', 'wp-floating-contact' ); ?></td>
                            <td>
                                <?php if ( $settings['whatsapp_number'] ) : ?>
                                    <strong><?php echo esc_html( $settings['whatsapp_number'] ); ?></strong>
                                <?php else : ?>
                                    <em><?php esc_html_e( 'Not set', 'wp-floating-contact' ); ?></em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Button Position:', 'wp-floating-contact' ); ?></td>
                            <td>
                                <strong>
                                <?php echo esc_html(
                                    $settings['button_position'] === 'bottom-right'
                                        ? __( 'Bottom Right', 'wp-floating-contact' )
                                        : __( 'Bottom Left', 'wp-floating-contact' )
                                ); ?>
                                </strong>
                            </td>
                        </tr>
                    </table>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpfc-settings' ) ); ?>" class="button button-primary">
                        <?php esc_html_e( 'Edit Settings', 'wp-floating-contact' ); ?>
                    </a>
                </div>

                <!-- Recent clicks -->
                <div class="wpfc-card">
                    <h2><?php esc_html_e( 'Recent Clicks', 'wp-floating-contact' ); ?></h2>

                    <?php if ( ! empty( $recent_logs ) ) : ?>
                    <table class="wpfc-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width:120px;"><?php esc_html_e( 'Type', 'wp-floating-contact' ); ?></th>
                                <th style="width:160px;"><?php esc_html_e( 'Date & Time', 'wp-floating-contact' ); ?></th>
                                <th><?php esc_html_e( 'Page', 'wp-floating-contact' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $recent_logs as $log ) : ?>
                            <tr>
                                <td>
                                    <span class="wpfc-badge wpfc-badge-<?php echo esc_attr( $log->click_type ); ?>">
                                        <?php if ( 'phone' === $log->click_type ) : ?>
                                            <span class="dashicons dashicons-phone"></span>
                                            <?php esc_html_e( 'Phone', 'wp-floating-contact' ); ?>
                                        <?php else : ?>
                                            <span class="dashicons dashicons-whatsapp"></span>
                                            <?php esc_html_e( 'WhatsApp', 'wp-floating-contact' ); ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html( wp_date( 'Y-m-d g:i A', strtotime( $log->clicked_at . ' UTC' ), new DateTimeZone( 'Asia/Riyadh' ) ) ); ?></td>
                                <td class="wpfc-url-cell">
                                    <?php $page = $this->resolve_page_display( $log->page_url, $log->page_title ); ?>
                                    <?php if ( $page['title'] ) : ?>
                                        <span class="wpfc-page-title-cell"><?php echo esc_html( $page['title'] ); ?></span>
                                        <a href="<?php echo esc_url( $page['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="wpfc-ext-link" title="<?php echo esc_attr( $page['url'] ); ?>">
                                            <span class="dashicons dashicons-external"></span>
                                        </a>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url( $page['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="wpfc-url-link" title="<?php echo esc_attr( $page['url'] ); ?>">
                                            <?php echo esc_html( $page['url'] ); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <br>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpfc-analytics' ) ); ?>" class="button">
                        <?php esc_html_e( 'View Full Analytics →', 'wp-floating-contact' ); ?>
                    </a>

                    <?php else : ?>
                    <p class="wpfc-no-data"><?php esc_html_e( 'No clicks recorded yet. Clicks on the floating buttons will appear here.', 'wp-floating-contact' ); ?></p>
                    <?php endif; ?>
                </div>

            </div><!-- /.wpfc-dashboard-grid -->

            <!-- ── Buy license section ── -->
            <div class="wpfc-buy-section">
                <a href="<?php echo esc_url( $buy_url ); ?>" target="_blank" rel="noopener noreferrer" class="wpfc-buy-btn-dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    <?php esc_html_e( 'Purchase a License via WhatsApp', 'wp-floating-contact' ); ?>
                </a>
                <p class="wpfc-buy-section-note">
                    <?php esc_html_e( 'Contact us on WhatsApp to get your license key instantly.', 'wp-floating-contact' ); ?>
                </p>
            </div>

        </div>
        <?php
    }

    // ─── Page: Settings ───────────────────────────────────────────────────────

    public function render_settings_page(): void {
        $settings = $this->get_settings();
        ?>
        <div class="wrap wpfc-wrap">

            <h1 class="wpfc-page-title">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php esc_html_e( 'Plugin Settings', 'wp-floating-contact' ); ?>
            </h1>

            <?php settings_errors( 'wpfc_settings_group' ); ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'wpfc_settings_group' ); ?>

                <div class="wpfc-settings-container">

                    <!-- ── General ── -->
                    <div class="wpfc-card">
                        <h2><?php esc_html_e( 'General', 'wp-floating-contact' ); ?></h2>
                        <table class="form-table wpfc-form-table" role="presentation">

                            <tr>
                                <th scope="row">
                                    <label for="wpfc_enable"><?php esc_html_e( 'Enable Plugin', 'wp-floating-contact' ); ?></label>
                                </th>
                                <td>
                                    <label class="wpfc-toggle" aria-label="<?php esc_attr_e( 'Enable or disable floating buttons', 'wp-floating-contact' ); ?>">
                                        <input type="checkbox" id="wpfc_enable" name="wpfc_settings[enable_plugin]" value="1"
                                               <?php checked( $settings['enable_plugin'], '1' ); ?>>
                                        <span class="wpfc-toggle-slider"></span>
                                    </label>
                                    <p class="description"><?php esc_html_e( 'Uncheck to hide the buttons on the frontend without deactivating the plugin.', 'wp-floating-contact' ); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="wpfc_position"><?php esc_html_e( 'Button Position', 'wp-floating-contact' ); ?></label>
                                </th>
                                <td>
                                    <select id="wpfc_position" name="wpfc_settings[button_position]" class="wpfc-select">
                                        <option value="bottom-right" <?php selected( $settings['button_position'], 'bottom-right' ); ?>>
                                            <?php esc_html_e( 'Bottom Right', 'wp-floating-contact' ); ?>
                                        </option>
                                        <option value="bottom-left" <?php selected( $settings['button_position'], 'bottom-left' ); ?>>
                                            <?php esc_html_e( 'Bottom Left', 'wp-floating-contact' ); ?>
                                        </option>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Choose which corner the floating buttons appear in.', 'wp-floating-contact' ); ?></p>
                                </td>
                            </tr>

                        </table>
                    </div>

                    <!-- ── Phone ── -->
                    <div class="wpfc-card">
                        <h2>
                            <span class="wpfc-card-icon wpfc-card-icon-phone">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            </span>
                            <?php esc_html_e( 'Phone Call Button', 'wp-floating-contact' ); ?>
                        </h2>
                        <table class="form-table wpfc-form-table" role="presentation">

                            <tr>
                                <th scope="row">
                                    <label for="wpfc_phone"><?php esc_html_e( 'Phone Number', 'wp-floating-contact' ); ?></label>
                                </th>
                                <td>
                                    <input type="tel" id="wpfc_phone" name="wpfc_settings[phone_number]"
                                           value="<?php echo esc_attr( $settings['phone_number'] ); ?>"
                                           class="regular-text" placeholder="+1234567890"
                                           autocomplete="off">
                                    <p class="description"><?php esc_html_e( 'Include the country code, e.g. +966501234567. Leave empty to hide this button.', 'wp-floating-contact' ); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="wpfc_phone_color"><?php esc_html_e( 'Button Color', 'wp-floating-contact' ); ?></label>
                                </th>
                                <td>
                                    <input type="text"
                                           id="wpfc_phone_color"
                                           name="wpfc_settings[phone_color]"
                                           value="<?php echo esc_attr( $settings['phone_color'] ); ?>"
                                           class="wpfc-color-picker"
                                           data-default-color="#1362bc">
                                    <p class="description"><?php esc_html_e( 'Choose the phone button background color. WhatsApp color is fixed green.', 'wp-floating-contact' ); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="wpfc_phone_label"><?php esc_html_e( 'Button Label', 'wp-floating-contact' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="wpfc_phone_label" name="wpfc_settings[phone_label]"
                                           value="<?php echo esc_attr( $settings['phone_label'] ); ?>"
                                           class="regular-text" placeholder="اتصل بنا">
                                    <p class="description"><?php esc_html_e( 'The text shown on the phone call button.', 'wp-floating-contact' ); ?></p>
                                </td>
                            </tr>

                        </table>
                    </div>

                    <!-- ── WhatsApp ── -->
                    <div class="wpfc-card">
                        <h2>
                            <span class="wpfc-card-icon wpfc-card-icon-whatsapp">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </span>
                            <?php esc_html_e( 'WhatsApp Button', 'wp-floating-contact' ); ?>
                        </h2>
                        <table class="form-table wpfc-form-table" role="presentation">

                            <tr>
                                <th scope="row">
                                    <label for="wpfc_whatsapp"><?php esc_html_e( 'WhatsApp Number', 'wp-floating-contact' ); ?></label>
                                </th>
                                <td>
                                    <input type="tel" id="wpfc_whatsapp" name="wpfc_settings[whatsapp_number]"
                                           value="<?php echo esc_attr( $settings['whatsapp_number'] ); ?>"
                                           class="regular-text" placeholder="966501234567"
                                           autocomplete="off">
                                    <p class="description"><?php esc_html_e( 'Digits only, no + or spaces, e.g. 966501234567. Leave empty to hide this button.', 'wp-floating-contact' ); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="wpfc_wa_message"><?php esc_html_e( 'Pre-filled Message', 'wp-floating-contact' ); ?></label>
                                </th>
                                <td>
                                    <textarea id="wpfc_wa_message" name="wpfc_settings[whatsapp_message]"
                                              rows="3" class="large-text"
                                              placeholder="<?php esc_attr_e( 'Hello! I would like to get in touch.', 'wp-floating-contact' ); ?>"><?php echo esc_textarea( $settings['whatsapp_message'] ); ?></textarea>
                                    <p class="description"><?php esc_html_e( 'This text is pre-loaded in the WhatsApp chat input when the user taps the button.', 'wp-floating-contact' ); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="wpfc_whatsapp_label"><?php esc_html_e( 'Button Label', 'wp-floating-contact' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="wpfc_whatsapp_label" name="wpfc_settings[whatsapp_label]"
                                           value="<?php echo esc_attr( $settings['whatsapp_label'] ); ?>"
                                           class="regular-text" placeholder="واتساب">
                                    <p class="description"><?php esc_html_e( 'The text shown on the WhatsApp button.', 'wp-floating-contact' ); ?></p>
                                </td>
                            </tr>

                        </table>
                    </div>

                </div><!-- /.wpfc-settings-container -->

                <?php submit_button( __( 'Save Settings', 'wp-floating-contact' ), 'primary large' ); ?>
            </form>
        </div>
        <?php
    }

    // ─── Page: Analytics ──────────────────────────────────────────────────────

    public function render_analytics_page(): void {
        // Sanitize the paged param; default to 1.
        $current_page = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $per_page     = 20;
        $total_logs   = WPFC_Database::get_total_count();
        $total_pages  = max( 1, (int) ceil( $total_logs / $per_page ) );
        $logs         = WPFC_Database::get_logs( $current_page, $per_page );
        $phone_clicks = WPFC_Database::get_count_by_type( 'phone' );
        $wa_clicks    = WPFC_Database::get_count_by_type( 'whatsapp' );
        ?>
        <div class="wrap wpfc-wrap">

            <h1 class="wpfc-page-title">
                <span class="dashicons dashicons-chart-area"></span>
                <?php esc_html_e( 'Click Analytics', 'wp-floating-contact' ); ?>
            </h1>

            <!-- ── Summary bar ── -->
            <div class="wpfc-analytics-summary">
                <div class="wpfc-summary-item">
                    <strong id="wpfc-total-count"><?php echo esc_html( number_format( $total_logs ) ); ?></strong>
                    <span><?php esc_html_e( 'Total Clicks', 'wp-floating-contact' ); ?></span>
                </div>
                <div class="wpfc-summary-item wpfc-summary-phone">
                    <strong id="wpfc-phone-count"><?php echo esc_html( number_format( $phone_clicks ) ); ?></strong>
                    <span><?php esc_html_e( 'Phone Clicks', 'wp-floating-contact' ); ?></span>
                </div>
                <div class="wpfc-summary-item wpfc-summary-whatsapp">
                    <strong id="wpfc-wa-count"><?php echo esc_html( number_format( $wa_clicks ) ); ?></strong>
                    <span><?php esc_html_e( 'WhatsApp Clicks', 'wp-floating-contact' ); ?></span>
                </div>
            </div>

            <!-- ── Monthly PDF report ── -->
            <div class="wpfc-report-card">
                <div class="wpfc-report-info">
                    <span class="dashicons dashicons-media-document"></span>
                    <div>
                        <strong><?php esc_html_e( 'Monthly Client Report', 'wp-floating-contact' ); ?></strong>
                        <span><?php esc_html_e( 'Generate a printable report and save it as a PDF to send to your client.', 'wp-floating-contact' ); ?></span>
                    </div>
                </div>
                <form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" target="_blank" class="wpfc-report-form">
                    <input type="hidden" name="action" value="wpfc_pdf_report">
                    <?php wp_nonce_field( 'wpfc_pdf_report' ); ?>
                    <select name="month" class="wpfc-select">
                        <?php foreach ( $this->get_report_months() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="button button-primary wpfc-report-btn">
                        <span class="dashicons dashicons-pdf"></span>
                        <?php esc_html_e( 'Download PDF Report', 'wp-floating-contact' ); ?>
                    </button>
                </form>
            </div>

            <!-- ── Action bar ── -->
            <div class="wpfc-actions-bar">
                <h2><?php esc_html_e( 'Click Log', 'wp-floating-contact' ); ?></h2>
                <button id="wpfc-clear-logs"
                        class="button wpfc-button-danger"
                        <?php disabled( empty( $logs ) ); ?>>
                    <span class="dashicons dashicons-trash"></span>
                    <?php esc_html_e( 'Clear All Logs', 'wp-floating-contact' ); ?>
                </button>
            </div>

            <!-- ── Logs table ── -->
            <div class="wpfc-card" id="wpfc-logs-card">
                <?php if ( ! empty( $logs ) ) : ?>

                <table class="wp-list-table widefat fixed striped wpfc-analytics-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th style="width:150px;"><?php esc_html_e( 'Action Type', 'wp-floating-contact' ); ?></th>
                            <th style="width:110px;"><?php esc_html_e( 'Date', 'wp-floating-contact' ); ?></th>
                            <th style="width:90px;"><?php esc_html_e( 'Time', 'wp-floating-contact' ); ?></th>
                            <th><?php esc_html_e( 'Page', 'wp-floating-contact' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $row_num = ( $current_page - 1 ) * $per_page + 1; ?>
                        <?php foreach ( $logs as $log ) : ?>
                        <tr>
                            <td><?php echo esc_html( $row_num++ ); ?></td>
                            <td>
                                <span class="wpfc-badge wpfc-badge-<?php echo esc_attr( $log->click_type ); ?>">
                                    <?php if ( 'phone' === $log->click_type ) : ?>
                                        <span class="dashicons dashicons-phone"></span>
                                        <?php esc_html_e( 'Phone Call', 'wp-floating-contact' ); ?>
                                    <?php else : ?>
                                        <span class="dashicons dashicons-whatsapp"></span>
                                        <?php esc_html_e( 'WhatsApp', 'wp-floating-contact' ); ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td><?php echo esc_html( wp_date( 'Y-m-d', strtotime( $log->clicked_at . ' UTC' ), new DateTimeZone( 'Asia/Riyadh' ) ) ); ?></td>
                            <td><?php echo esc_html( wp_date( 'g:i A', strtotime( $log->clicked_at . ' UTC' ), new DateTimeZone( 'Asia/Riyadh' ) ) ); ?></td>
                            <td class="wpfc-url-cell">
                                <?php $page = $this->resolve_page_display( $log->page_url, $log->page_title ); ?>
                                <?php if ( $page['title'] ) : ?>
                                    <span class="wpfc-page-title-cell"><?php echo esc_html( $page['title'] ); ?></span>
                                    <a href="<?php echo esc_url( $page['url'] ); ?>"
                                       target="_blank" rel="noopener noreferrer"
                                       class="wpfc-ext-link"
                                       title="<?php echo esc_attr( $page['url'] ); ?>">
                                        <span class="dashicons dashicons-external"></span>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php echo esc_url( $page['url'] ); ?>"
                                       target="_blank" rel="noopener noreferrer"
                                       class="wpfc-url-link"
                                       title="<?php echo esc_attr( $page['url'] ); ?>">
                                        <?php echo esc_html( $page['url'] ); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ( $total_pages > 1 ) : ?>
                <div class="wpfc-pagination">
                    <?php
                    echo paginate_links( array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'prev_text' => '&laquo; ' . esc_html__( 'Prev', 'wp-floating-contact' ),
                        'next_text' => esc_html__( 'Next', 'wp-floating-contact' ) . ' &raquo;',
                        'total'     => $total_pages,
                        'current'   => $current_page,
                    ) );
                    ?>
                </div>
                <?php endif; ?>

                <?php else : ?>

                <div class="wpfc-empty-state" id="wpfc-empty-state">
                    <span class="dashicons dashicons-chart-bar"></span>
                    <p><?php esc_html_e( 'No click data recorded yet. Clicks on the floating buttons will appear here automatically.', 'wp-floating-contact' ); ?></p>
                </div>

                <?php endif; ?>
            </div><!-- /#wpfc-logs-card -->

        </div>
        <?php
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Builds the month options for the report picker, newest first.
     *
     * Runs from the current Riyadh month back to the month of the earliest
     * recorded click (capped at 24 entries so the dropdown stays usable).
     *
     * @return array<string,string> Map of 'YYYY-MM' => localized label.
     */
    private function get_report_months(): array {
        $tz     = new DateTimeZone( 'Asia/Riyadh' );
        $cursor = new DateTime( 'now', $tz );
        $cursor->modify( 'first day of this month' )->setTime( 0, 0, 0 );

        // Stop at the month containing the oldest click, when there is one.
        // Times are zeroed so the loop's comparison comes out to a clean
        // month-vs-month test rather than an hours-apart one.
        $earliest = WPFC_Database::get_earliest_click();
        $floor    = null;
        if ( $earliest ) {
            $floor = new DateTime( $earliest, new DateTimeZone( 'UTC' ) );
            $floor->setTimezone( $tz );
            $floor->modify( 'first day of this month' )->setTime( 0, 0, 0 );
        }

        $months = array();
        for ( $i = 0; $i < 24; $i++ ) {
            $months[ $cursor->format( 'Y-m' ) ] = wp_date( 'F Y', $cursor->getTimestamp(), $tz );

            if ( $floor && $cursor <= $floor ) {
                break;
            }
            $cursor->modify( '-1 month' );
        }

        return $months;
    }

    /**
     * Resolves a stored page URL to its title for display in analytics tables.
     *
     * Priority: WordPress post title (via url_to_postid) → JS-captured title → (empty).
     * The caller falls back to showing the raw URL when the returned title is empty.
     *
     * @param string $page_url   The URL stored in the click log.
     * @param string $page_title The page title captured by the frontend JS at click time.
     * @return array{title:string,url:string}
     */
    private function resolve_page_display( string $page_url, string $page_title ): array {
        $title = '';

        if ( $page_url ) {
            $post_id = url_to_postid( $page_url );
            if ( $post_id ) {
                $title = get_the_title( $post_id );
            }
        }

        if ( ! $title ) {
            $title = $page_title;
        }

        return array(
            'title' => $title,
            'url'   => $page_url,
        );
    }

    // ─── Meta Box: Per-post contact overrides ───────────────────────────────

    public function add_contact_meta_box(): void {
        add_meta_box(
            'wpfc_contact_override',
            __( 'Floating Contact Override', 'wp-floating-contact' ),
            array( $this, 'render_contact_meta_box' ),
            null, // all public post types
            'side',
            'default'
        );
    }

    public function render_contact_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'wpfc_meta_box', 'wpfc_meta_nonce' );
        $phone    = get_post_meta( $post->ID, '_wpfc_phone', true );
        $whatsapp = get_post_meta( $post->ID, '_wpfc_whatsapp', true );
        ?>
        <p>
            <label for="wpfc_meta_phone" style="display:block;margin-bottom:4px;font-weight:600;">
                <?php esc_html_e( 'Phone Number Override', 'wp-floating-contact' ); ?>
            </label>
            <input type="tel" id="wpfc_meta_phone" name="wpfc_meta_phone"
                   value="<?php echo esc_attr( $phone ); ?>"
                   placeholder="<?php esc_attr_e( 'e.g. +966501234567', 'wp-floating-contact' ); ?>"
                   style="width:100%;">
            <span class="description" style="font-size:11px;">
                <?php esc_html_e( 'Leave empty to use the global setting.', 'wp-floating-contact' ); ?>
            </span>
        </p>
        <p>
            <label for="wpfc_meta_whatsapp" style="display:block;margin-bottom:4px;font-weight:600;">
                <?php esc_html_e( 'WhatsApp Number Override', 'wp-floating-contact' ); ?>
            </label>
            <input type="tel" id="wpfc_meta_whatsapp" name="wpfc_meta_whatsapp"
                   value="<?php echo esc_attr( $whatsapp ); ?>"
                   placeholder="<?php esc_attr_e( 'e.g. 966501234567', 'wp-floating-contact' ); ?>"
                   style="width:100%;">
            <span class="description" style="font-size:11px;">
                <?php esc_html_e( 'Leave empty to use the global setting.', 'wp-floating-contact' ); ?>
            </span>
        </p>
        <?php
    }

    public function save_contact_meta_box( int $post_id ): void {
        if ( ! isset( $_POST['wpfc_meta_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wpfc_meta_nonce'] ) ), 'wpfc_meta_box' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $phone = isset( $_POST['wpfc_meta_phone'] )
            ? sanitize_text_field( wp_unslash( $_POST['wpfc_meta_phone'] ) )
            : '';
        $whatsapp = isset( $_POST['wpfc_meta_whatsapp'] )
            ? sanitize_text_field( wp_unslash( $_POST['wpfc_meta_whatsapp'] ) )
            : '';

        update_post_meta( $post_id, '_wpfc_phone', $phone );
        update_post_meta( $post_id, '_wpfc_whatsapp', $whatsapp );
    }
}
