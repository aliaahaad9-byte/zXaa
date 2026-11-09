<?php
/**
 * Deutsche Garage — core pack.
 * Brand data, helpers, icons, schema, lead endpoint and first-run setup.
 * Everything editable lives in "إعدادات القالب" (theme options) or in posts/pages.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'DG_VERSION', '1.0.0' );
define( 'DG_DIR', trailingslashit( dirname( __FILE__ ) ) );

require DG_DIR . 'data.php';
require DG_DIR . 'icons.php';
require DG_DIR . 'sections.php';

/* -------------------------------------------------------------------------
 * Option & asset helpers
 * ---------------------------------------------------------------------- */

/** Theme option with default. Empty strings fall back to the default. */
function dg_opt( $key, $default = '' ) {
	$v = get_option( $key );
	if ( is_array( $v ) && isset( $v['url'] ) ) { $v = $v['url']; }
	return ( $v === false || $v === '' || $v === null ) ? $default : $v;
}

/** URL of a bundled theme asset (served from the site's own domain). */
function dg_asset( $rel ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $rel, '/' );
}

/**
 * Image slot. Returns the image uploaded in theme options for this slot
 * ("img_{slot}"), otherwise the bundled image in /assets/img/{slot}.webp.
 * A file dropped into /assets/img/ with the same name also replaces it.
 */
function dg_img( $slot ) {
	$custom = get_option( 'img_' . $slot );
	if ( is_array( $custom ) && ! empty( $custom['url'] ) ) { return $custom['url']; }
	if ( is_string( $custom ) && $custom !== '' ) { return $custom; }
	foreach ( array( 'jpg', 'jpeg', 'png' ) as $ext ) {
		if ( file_exists( get_template_directory() . '/assets/img/' . $slot . '.' . $ext ) ) {
			return dg_asset( 'img/' . $slot . '.' . $ext );
		}
	}
	return dg_asset( 'img/' . $slot . '.webp' );
}

function dg_logo( $white = false ) {
	$key = $white ? 'logo_white' : 'logo';
	$u = dg_opt( $key );
	return $u ? $u : dg_asset( 'img/' . ( $white ? 'logo-white.webp' : 'logo.webp' ) );
}

/** Digits only, Egyptian numbers normalised to international (20…). */
function dg_intl( $num ) {
	$d = preg_replace( '/\D+/', '', (string) $num );
	if ( strpos( $d, '00' ) === 0 ) { $d = substr( $d, 2 ); }
	if ( strpos( $d, '0' ) === 0 ) { $d = '20' . substr( $d, 1 ); }
	return $d;
}
function dg_phone()   { return dg_opt( 'Phone', '01093879382' ); }
function dg_phone2()  { return dg_opt( 'Phone2', '01284577455' ); }
function dg_wa_num()  { return dg_opt( 'Whatsapp', '01093879382' ); }
function dg_tel( $n = null ) { return 'tel:+' . dg_intl( $n ? $n : dg_phone() ); }
function dg_wa( $text = '', $n = null ) {
	$u = 'https://wa.me/' . dg_intl( $n ? $n : dg_wa_num() );
	return $text !== '' ? $u . '?text=' . rawurlencode( $text ) : $u;
}
function dg_map_url() { return dg_opt( 'map_url', 'https://maps.app.goo.gl/B9UxoYZaCoyZ2P5A9' ); }
function dg_address() { return dg_opt( 'Adress', '16 خلف شارع بورسعيد، سيدي جابر، الإسكندرية' ); }
function dg_name()    { return dg_opt( 'sitename', 'دويتش جراج' ); }

/** URL of the page using a given model, or a sensible fallback. */
function dg_page_url( $model, $fallback = '' ) {
	static $cache = array();
	if ( isset( $cache[ $model ] ) ) { return $cache[ $model ]; }
	$q = get_posts( array(
		'post_type'      => 'page',
		'posts_per_page' => 1,
		'meta_key'       => 'template',
		'meta_value'     => $model,
		'fields'         => 'ids',
	) );
	$cache[ $model ] = $q ? get_permalink( $q[0] ) : ( $fallback ? $fallback : home_url( '/' ) );
	return $cache[ $model ];
}
function dg_booking_url( $args = array() ) {
	$u = dg_page_url( 'booking', home_url( '/#estimator' ) );
	return $args ? add_query_arg( $args, $u ) : $u;
}

/**
 * Escape text and isolate Latin parentheticals, e.g. "(Deutsche Garage)",
 * so brackets render correctly inside right-to-left Arabic text.
 */
function dg_t( $text ) {
	$t = esc_html( $text );
	return preg_replace( '/\(([A-Za-z0-9][^()]*)\)/u', '<bdi dir="ltr" class="dgb">($1)</bdi>', $t );
}

/** Responsive image tag with reserved space (prevents CLS). */
function dg_image( $src, $alt, $w, $h, $attrs = array() ) {
	$a = array_merge( array( 'loading' => 'lazy', 'decoding' => 'async' ), $attrs );
	$out = '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '" width="' . (int) $w . '" height="' . (int) $h . '"';
	foreach ( $a as $k => $v ) {
		if ( $v === false ) { continue; }
		$out .= ' ' . $k . '="' . esc_attr( $v ) . '"';
	}
	return $out . '>';
}

/* -------------------------------------------------------------------------
 * Post types & taxonomy
 * ---------------------------------------------------------------------- */
add_action( 'init', function () {
	register_post_type( 'dg_case', array(
		'labels'       => array(
			'name'          => 'حالات الإصلاح',
			'singular_name' => 'حالة إصلاح',
			'add_new'       => 'إضافة حالة',
			'add_new_item'  => 'إضافة حالة إصلاح جديدة',
			'edit_item'     => 'تعديل الحالة',
			'menu_name'     => 'حالات الإصلاح',
		),
		'public'       => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-car',
		'rewrite'      => array( 'slug' => 'cases' ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'show_in_rest' => true,
		'menu_position'=> 6,
	) );
	// Lead storage used by the estimator & booking forms (shown in "طلبات العملاء").
	register_post_type( 'clint', array(
		'label'   => 'طلبات العملاء',
		'public'  => false,
		'show_ui' => false,
	) );
	register_taxonomy( 'car_brand', array( 'post', 'dg_case' ), array(
		'labels'            => array( 'name' => 'الماركات', 'singular_name' => 'ماركة', 'add_new_item' => 'إضافة ماركة' ),
		'hierarchical'      => true,
		'rewrite'           => array( 'slug' => 'brand' ),
		'show_admin_column' => true,
		'show_in_rest'      => true,
	) );
} );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'dg-card', 720, 450, true );
} );

/* -------------------------------------------------------------------------
 * Lead endpoint (estimator / booking / warning-light forms)
 * Saves the request, then the browser continues to WhatsApp.
 * ---------------------------------------------------------------------- */
function dg_lead_save() {
	if ( ! isset( $_POST['dg_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dg_nonce'] ) ), 'dg_lead' ) ) {
		wp_send_json_error( array( 'reason' => 'nonce' ), 403 );
	}
	$f = function ( $k, $max = 120 ) {
		return isset( $_POST[ $k ] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST[ $k ] ) ), 0, $max ) : '';
	};
	$name  = $f( 'name', 80 );
	$phone = preg_replace( '/[^\d+]/', '', $f( 'phone', 20 ) );
	if ( strlen( preg_replace( '/\D/', '', $phone ) ) < 8 ) {
		wp_send_json_error( array( 'reason' => 'phone' ), 422 );
	}
	// Simple flood guard: one lead per phone per 2 minutes.
	$key = 'dg_lead_' . md5( $phone );
	if ( get_transient( $key ) ) { wp_send_json_success( array( 'dup' => true ) ); }
	set_transient( $key, 1, 2 * MINUTE_IN_SECONDS );

	$car  = trim( $f( 'brand' ) . ' ' . $f( 'model' ) . ' ' . $f( 'year', 4 ) );
	$need = $f( 'need', 200 );
	$msg  = implode( ' | ', array_filter( array(
		$need,
		$f( 'km', 12 ) ? 'العداد: ' . $f( 'km', 12 ) . ' كم' : '',
		$f( 'notes', 300 ),
		$f( 'source', 40 ) ? 'المصدر: ' . $f( 'source', 40 ) : '',
	) ) );
	$id = wp_insert_post( array(
		'post_title'  => $name ? $name : $phone,
		'post_type'   => 'clint',
		'post_status' => 'publish',
	) );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, 'phone', $phone );
		update_post_meta( $id, 'email', '' );
		update_post_meta( $id, 'address', $car );
		update_post_meta( $id, 'message', $msg );
		$to = dg_opt( 'Email', get_option( 'admin_email' ) );
		if ( $to ) {
			wp_mail( $to, 'طلب جديد من الموقع — ' . ( $name ? $name : $phone ), "الاسم: $name\nالهاتف: $phone\nالسيارة: $car\nالطلب: $msg" );
		}
	}
	wp_send_json_success();
}
add_action( 'wp_ajax_dg_lead', 'dg_lead_save' );
add_action( 'wp_ajax_nopriv_dg_lead', 'dg_lead_save' );

/* -------------------------------------------------------------------------
 * Local SEO: AutoRepair JSON-LD (NAP identical to Google Maps)
 * ---------------------------------------------------------------------- */
add_action( 'wp_head', function () {
	$same = array_values( array_filter( array(
		dg_opt( 'instagram', 'https://www.instagram.com/deutsche_garage_/' ),
		dg_opt( 'tiktok', 'https://www.tiktok.com/@deutsche_garage' ),
		dg_map_url(),
		dg_opt( 'facebook' ),
		dg_opt( 'youtube' ),
	) ) );
	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'AutoRepair',
		'@id'         => home_url( '/#business' ),
		'name'        => dg_opt( 'schema_name', 'Deutsche Garage - دويتش جراج لصيانة السيارات الألمانية' ),
		'url'         => home_url( '/' ),
		'logo'        => dg_logo(),
		'image'       => dg_img( 'og-cover' ),
		'telephone'   => '+' . dg_intl( dg_phone() ),
		'priceRange'  => '$$',
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => dg_opt( 'schema_street', '16 Khalf Bor Saed, Sidi Gabir' ),
			'addressLocality' => 'Alexandria',
			'addressRegion'   => 'Alexandria Governorate',
			'postalCode'      => dg_opt( 'schema_postal', '5434005' ),
			'addressCountry'  => 'EG',
		),
		'hasMap'      => dg_map_url(),
		'areaServed'  => array( 'سيدي جابر', 'سموحة', 'كليوباترا', 'رشدي', 'الإسكندرية' ),
		'brand'       => array( 'Mercedes-Benz', 'BMW', 'MINI', 'Volkswagen', 'Audi', 'Skoda', 'Seat', 'Porsche', 'Opel' ),
		'sameAs'      => $same,
	);
	$lat = dg_opt( 'geo_lat' );
	$lng = dg_opt( 'geo_lng' );
	if ( $lat && $lng ) {
		$schema['geo'] = array( '@type' => 'GeoCoordinates', 'latitude' => (float) $lat, 'longitude' => (float) $lng );
	}
	$hours = dg_hours_spec();
	if ( $hours ) { $schema['openingHoursSpecification'] = $hours; }
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

	// OpenGraph fallback image for social shares from the bio link.
	if ( ! defined( 'WPSEO_VERSION' ) && ! defined( 'RANK_MATH_VERSION' ) ) {
		$img = ( is_singular() && has_post_thumbnail() ) ? get_the_post_thumbnail_url( null, 'large' ) : dg_img( 'og-cover' );
		echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( dg_name() ) . '">' . "\n";
		echo '<meta name="theme-color" content="#111111">' . "\n";
	}
}, 5 );

/** "Sat-Thu = 10:00-20:00" lines → OpeningHoursSpecification (optional). */
function dg_hours_spec() {
	$raw = dg_opt( 'schema_hours', "Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday=10:00-20:00" );
	$out = array();
	foreach ( preg_split( '/\r?\n/', $raw ) as $line ) {
		if ( ! preg_match( '/^\s*([A-Za-z,\s]+)\s*=\s*(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $line, $m ) ) { continue; }
		$out[] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array_map( 'trim', explode( ',', $m[1] ) ),
			'opens'     => $m[2],
			'closes'    => $m[3],
		);
	}
	return $out;
}

/* -------------------------------------------------------------------------
 * First-run setup: pages, models, menus, brand terms, defaults.
 * Runs once on activation; safe to re-run (never duplicates).
 * ---------------------------------------------------------------------- */
function dg_install() {
	$pages = dg_default_pages();
	$ids   = array();
	foreach ( $pages as $slug => $p ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			if ( ! get_post_meta( $existing->ID, 'template', true ) ) {
				update_post_meta( $existing->ID, 'template', $p['model'] );
			}
			continue;
		}
		$id = wp_insert_post( array(
			'post_title'   => $p['title'],
			'post_name'    => $slug,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => isset( $p['content'] ) ? $p['content'] : '',
			'menu_order'   => isset( $p['order'] ) ? $p['order'] : 0,
		) );
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, 'template', $p['model'] );
			$ids[ $slug ] = $id;
		}
	}

	// Menus (only when the locations are empty).
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['home-menu'] ) ) {
		$menu_id = wp_create_nav_menu( 'القائمة الرئيسية' );
		if ( ! is_wp_error( $menu_id ) ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'الرئيسية', 'menu-item-url' => home_url( '/' ), 'menu-item-status' => 'publish' ) );
			$brands_parent = 0;
			$brand_labels = array( 'mercedes-benz' => 'مرسيدس بنز', 'bmw-mini' => 'BMW وميني', 'vag-group' => 'فولكس · أودي · سكودا · سيات', 'porsche-opel' => 'بورش وأوبل' );
			foreach ( $brand_labels as $s => $label ) {
				if ( empty( $ids[ $s ] ) ) { continue; }
				if ( ! $brands_parent ) {
					$brands_parent = wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'الماركات', 'menu-item-url' => '#', 'menu-item-status' => 'publish' ) );
				}
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $label, 'menu-item-object-id' => $ids[ $s ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $brands_parent, 'menu-item-status' => 'publish' ) );
			}
			$main_labels = array( 'pre-purchase-inspection' => 'فحص قبل الشراء', 'warning-lights' => 'لمبات الأعطال', 'maintenance-schedule' => 'جداول الصيانة', 'about' => 'عن المركز', 'blog' => 'المدونة' );
			foreach ( $main_labels as $s => $label ) {
				if ( empty( $ids[ $s ] ) ) { continue; }
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $label, 'menu-item-object-id' => $ids[ $s ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			}
			$locations['home-menu'] = $menu_id;
		}
	}
	if ( empty( $locations['footer-menu'] ) ) {
		$fid = wp_create_nav_menu( 'روابط الفوتر' );
		if ( ! is_wp_error( $fid ) ) {
			foreach ( array( 'booking' => 'احجز موعد / تسعيرة', 'cases' => 'حالات الإصلاح', 'pre-purchase-inspection' => 'فحص قبل الشراء', 'warning-lights' => 'دليل لمبات الأعطال', 'maintenance-schedule' => 'جداول الصيانة', 'faq' => 'الأسئلة الشائعة', 'contact' => 'تواصل معنا' ) as $s => $label ) {
				if ( empty( $ids[ $s ] ) ) { continue; }
				wp_update_nav_menu_item( $fid, 0, array( 'menu-item-title' => $label, 'menu-item-object-id' => $ids[ $s ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			}
			$locations['footer-menu'] = $fid;
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	// Brand taxonomy terms for blog posts & cases.
	foreach ( array( 'mercedes-benz' => 'مرسيدس بنز', 'bmw' => 'بي إم دبليو', 'mini' => 'ميني', 'volkswagen' => 'فولكس فاجن', 'audi' => 'أودي', 'skoda' => 'سكودا', 'seat' => 'سيات', 'porsche' => 'بورش', 'opel' => 'أوبل' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'car_brand' ) ) {
			wp_insert_term( $name, 'car_brand', array( 'slug' => $slug ) );
		}
	}

	// Default options (only if empty).
	$defaults = array(
		'sitename'  => 'دويتش جراج',
		'yc_lang'   => 'ar-EG',
		'Phone'     => '01093879382',
		'Phone2'    => '01284577455',
		'Whatsapp'  => '01093879382',
		'Adress'    => '16 خلف شارع بورسعيد، سيدي جابر، الإسكندرية',
		'map_url'   => 'https://maps.app.goo.gl/B9UxoYZaCoyZ2P5A9',
		'instagram' => 'https://www.instagram.com/deutsche_garage_/',
		'tiktok'    => 'https://www.tiktok.com/@deutsche_garage',
		'work_hours'=> "السبت – الخميس = 10 صباحًا – 8 مساءً\nالجمعة = مغلق",
	);
	foreach ( $defaults as $k => $v ) {
		if ( get_option( $k ) === false || get_option( $k ) === '' ) { update_option( $k, $v ); }
	}
	if ( get_option( 'show_on_front' ) !== 'posts' ) { update_option( 'show_on_front', 'posts' ); }
	update_option( 'dg_installed', DG_VERSION );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'dg_install' );
add_action( 'admin_init', function () {
	if ( get_option( 'dg_installed' ) !== DG_VERSION && current_user_can( 'manage_options' ) ) { dg_install(); }
} );

/* Keep the blog page (model "blog") as the posts archive link. */
add_filter( 'excerpt_length', function () { return 24; } );
add_filter( 'excerpt_more', function () { return '…'; } );
