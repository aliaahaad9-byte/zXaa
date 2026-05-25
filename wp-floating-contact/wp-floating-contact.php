<?php
/**
 * Plugin Name:       WP Floating Contact Buttons
 * Plugin URI:        https://github.com/
 * Description:       Adds floating phone and WhatsApp contact buttons to the frontend with full click analytics tracking and an admin dashboard.
 * Version:           1.0.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Your Name
 * Text Domain:       wp-floating-contact
 * Domain Path:       /languages
 * License:           GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WPFC_VERSION',         '1.0.1' );
define( 'WPFC_PLUGIN_DIR',      plugin_dir_path( __FILE__ ) );
define( 'WPFC_PLUGIN_URL',      plugin_dir_url( __FILE__ ) );
define( 'WPFC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once WPFC_PLUGIN_DIR . 'includes/class-database.php';
require_once WPFC_PLUGIN_DIR . 'includes/class-admin.php';
require_once WPFC_PLUGIN_DIR . 'includes/class-frontend.php';
require_once WPFC_PLUGIN_DIR . 'includes/class-tracker.php';

register_activation_hook( __FILE__, array( 'WPFC_Database', 'create_table' ) );
register_deactivation_hook( __FILE__, array( 'WPFC_Database', 'deactivate' ) );
register_uninstall_hook( __FILE__, 'wpfc_uninstall' );

function wpfc_uninstall() {
    WPFC_Database::drop_table();
    delete_option( 'wpfc_settings' );
    delete_option( 'wpfc_db_version' );
}

function wpfc_init() {
    // Ensure the DB table always exists — covers manual (FTP) installs where
    // the activation hook never fires.
    WPFC_Database::maybe_create_table();

    new WPFC_Admin();
    new WPFC_Frontend();
    new WPFC_Tracker();
}
add_action( 'plugins_loaded', 'wpfc_init' );
