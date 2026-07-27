<?
function SetupThumbnails() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'medium', 190, 230, false );
	add_image_size( 'default', 370, 520, false );
	add_image_size( 'pinsize', 180, 270, false );
}
add_action('Initialize', 'SetupThumbnails');