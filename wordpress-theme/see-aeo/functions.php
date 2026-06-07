<?php
/**
 * See AEO theme functions.
 *
 * @package See_AEO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEE_AEO_VERSION', '1.0.0' );

/**
 * Theme setup.
 */
function see_aeo_setup() {
	load_theme_textdomain( 'see-aeo', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 40,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'القائمة الرئيسية', 'see-aeo' ),
		)
	);
}
add_action( 'after_setup_theme', 'see_aeo_setup' );

/**
 * Enqueue styles and scripts.
 */
function see_aeo_assets() {
	// Cairo font from Google Fonts.
	wp_enqueue_style(
		'see-aeo-cairo',
		'https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap',
		array(),
		null
	);

	// Main stylesheet (the theme style.css contains the full design system).
	wp_enqueue_style( 'see-aeo-style', get_stylesheet_uri(), array( 'see-aeo-cairo' ), SEE_AEO_VERSION );

	// Main script in the footer.
	wp_enqueue_script(
		'see-aeo-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		SEE_AEO_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'see_aeo_assets' );

/**
 * Add the "nav-link" class to every primary-menu anchor so it matches the design.
 */
function see_aeo_menu_link_attributes( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$atts['class'] = trim( ( isset( $atts['class'] ) ? $atts['class'] : '' ) . ' nav-link' );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'see_aeo_menu_link_attributes', 10, 3 );

/**
 * Minimal walker: output bare <a> elements (no <li>) to match the static markup.
 */
class See_AEO_Nav_Walker extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class   = 'nav-link';
		if ( in_array( 'current-menu-item', $classes, true ) ) {
			$class .= ' active';
		}
		$url   = ! empty( $item->url ) ? $item->url : '#';
		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$output .= '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a>';
	}
}

/**
 * Fallback one-page menu used when no menu has been assigned to "primary".
 */
function see_aeo_default_menu() {
	$home  = esc_url( home_url( '/' ) );
	$items = array(
		'#hero'     => 'الرئيسية',
		'#services' => 'خدماتنا',
		'#blog'     => 'المدونة',
		'#works'    => 'أعمالنا',
		'#about'    => 'من نحن',
		'#contact'  => 'تواصل معنا',
	);
	$first = true;
	foreach ( $items as $anchor => $label ) {
		$active = $first ? ' active' : '';
		$first  = false;
		printf(
			'<a class="nav-link%1$s" href="%2$s">%3$s</a>',
			esc_attr( $active ),
			esc_url( is_front_page() ? $anchor : $home . $anchor ),
			esc_html( $label )
		);
	}
}

/**
 * Use the custom walker for the primary location automatically.
 */
function see_aeo_nav_menu_args( $args ) {
	if ( isset( $args['theme_location'] ) && 'primary' === $args['theme_location'] && empty( $args['walker'] ) ) {
		$args['walker'] = new See_AEO_Nav_Walker();
	}
	return $args;
}
add_filter( 'wp_nav_menu_args', 'see_aeo_nav_menu_args' );
