<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function SetupThumbnails() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
}
add_action( 'after_setup_theme', 'SetupThumbnails' );
