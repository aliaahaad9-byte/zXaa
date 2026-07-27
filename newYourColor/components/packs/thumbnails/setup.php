<?
function SetupThumbnails() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'small', 48, 50, false );
	add_image_size( 'logo', 186, 42, false );
	add_image_size( 'medium', 50, 50, false );
	add_image_size( 'default', 370, 520, false );
	add_image_size( 'pinsize', 180, 270, false );
	add_image_size( 'works_size', 410, 290, false );
	add_image_size( 'block_posts_size', 224, 150, false );
	add_image_size( 'block_grid3', 50, 50, false );
	add_image_size( 'block_grid2', 372, 182, false );
	add_image_size( 'fag_size', 510, 611, false );
	add_image_size( 'intro_size', 480, 500, false );
	add_image_size( 'mobile_intro_size', 284 , 284, false );
	add_image_size( 'post_single_size', 800, 600, false );
	add_image_size( 'mobile_post_size', 300, 250, false );
	add_image_size( 'gallery_image', 290, 270, false );
}
add_action('Initialize', 'SetupThumbnails');