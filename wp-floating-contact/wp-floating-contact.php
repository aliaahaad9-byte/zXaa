<?php
/**
 * Plugin Name:       WP Floating Contact Buttons
 * Plugin URI:        https://wordpress.org/plugins/wp-floating-contact/
 * Description:       Adds floating phone and WhatsApp contact buttons to the frontend with full click analytics tracking and an admin dashboard.
 * Version:           1.0.4
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            WP Floating Contact
 * Author URI:        https://wordpress.org/plugins/wp-floating-contact/
 * Text Domain:       wp-floating-contact
 * Domain Path:       /languages
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WPFC_VERSION',         '1.0.4' );
define( 'WPFC_PLUGIN_DIR',      plugin_dir_path( __FILE__ ) );
define( 'WPFC_PLUGIN_URL',      plugin_dir_url( __FILE__ ) );
define( 'WPFC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once WPFC_PLUGIN_DIR . 'includes/class-database.php';
require_once WPFC_PLUGIN_DIR . 'includes/class-license.php';
require_once WPFC_PLUGIN_DIR . 'includes/class-admin.php';
require_once WPFC_PLUGIN_DIR . 'includes/class-frontend.php';
require_once WPFC_PLUGIN_DIR . 'includes/class-tracker.php';
require_once WPFC_PLUGIN_DIR . 'includes/class-report.php';

register_activation_hook( __FILE__, array( 'WPFC_Database', 'create_table' ) );
register_deactivation_hook( __FILE__, array( 'WPFC_Database', 'deactivate' ) );
register_uninstall_hook( __FILE__, 'wpfc_uninstall' );

function wpfc_uninstall() {
    WPFC_Database::drop_table();
    delete_option( 'wpfc_settings' );
    delete_option( 'wpfc_db_version' );
    delete_option( 'wpfc_license_hash' );
    delete_option( 'wpfc_trial_start' );
}

function wpfc_init() {
    // Load translations before anything else.
    load_plugin_textdomain( 'wp-floating-contact', false, dirname( WPFC_PLUGIN_BASENAME ) . '/languages' );

    WPFC_Database::maybe_create_table();

    // Start the 14-day trial clock on first ever page load.
    WPFC_License::maybe_start_trial();

    // Admin always loads — shows either the full UI or the activation screen.
    new WPFC_Admin();

    // Frontend buttons + tracker only run during trial OR with a valid license.
    if ( WPFC_License::can_use() ) {
        new WPFC_Frontend();
        new WPFC_Tracker();
        new WPFC_Report();
    }
}
add_action( 'plugins_loaded', 'wpfc_init' );
