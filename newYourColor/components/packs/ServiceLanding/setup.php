<?php
/**
 * ServiceLanding — تحويل تصنيف الخدمة من أرشيف عادي إلى صفحة هبوط.
 *
 * لا يحتوي هذا الملف ولا ملف العرض على أي نص محتوى: كل عنوان ورقم وميزة
 * وخطوة وسؤال يُقرأ من حقول تُضاف لشاشة تحرير التصنيف.
 *
 * إعادة استخدام: تصنيف المدن (country)، نوع محتوى الأسئلة (faq)،
 * نظام استقبال الطلبات (clint عبر AjaxCenter/sendinfo)، وجزئية المقالات (#Posts).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class ServiceLanding {

	const TAXONOMY   = 'category';
	const CITY_TAX   = 'country';
	const FAQ_TYPE   = 'faq';
	const VERSION    = '3.0.0';

	/** مفاتيح الحقول النصية المفردة */
	public static function text_fields() {
		return array(
			'sl_hero_title', 'sl_hero_promise', 'sl_cta_call', 'sl_cta_whatsapp',
			'sl_form_title', 'sl_form_hint', 'sl_form_submit',
			'sl_label_name', 'sl_label_phone', 'sl_label_city', 'sl_label_service', 'sl_label_details',
			'sl_form_note',
			'sl_features_title', 'sl_features_sub',
			'sl_steps_title', 'sl_steps_sub',
			'sl_cities_title', 'sl_cities_sub',
			'sl_faq_title', 'sl_faq_sub',
			'sl_order_title', 'sl_order_sub',
			'sl_posts_title',
			'sl_phone', 'sl_whatsapp',
		);
	}

	/** الحقول المتكررة: المفتاح => الأعمدة */
	public static function repeaters() {
		return array(
			'sl_badges'   => array( 'text' ),
			'sl_stats'    => array( 'value', 'label' ),
			'sl_features' => array( 'icon', 'title', 'text', 'metric' ),
			'sl_steps'    => array( 'title', 'text' ),
		);
	}

	/* --------------------------------- Bootstrap -------------------------------- */

	public static function init() {
		require_once dirname( __FILE__ ) . '/inc/admin.php';
		require_once dirname( __FILE__ ) . '/inc/frontend.php';

		ServiceLanding_Admin::init();
	}

	/* ---------------------------------- Data ------------------------------------ */

	/** هل صفحة الهبوط مفعّلة على هذا التصنيف؟ */
	public static function enabled( $term_id ) {
		return get_term_meta( (int) $term_id, 'sl_enabled', true ) === '1';
	}

	/** قراءة قيمة حقل */
	public static function get( $term_id, $key, $default = '' ) {
		$val = get_term_meta( (int) $term_id, $key, true );
		if ( $val === '' || $val === null || $val === false ) {
			return $default;
		}
		return $val;
	}

	/** قراءة حقل متكرر كمصفوفة صفوف نظيفة */
	public static function rows( $term_id, $key ) {
		$rows = get_term_meta( (int) $term_id, $key, true );
		if ( ! is_array( $rows ) ) {
			return array();
		}
		$out = array();
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) continue;
			$has = false;
			foreach ( $row as $v ) {
				if ( trim( (string) $v ) !== '' ) { $has = true; break; }
			}
			if ( $has ) $out[] = $row;
		}
		return $out;
	}

	/**
	 * توحيد الأرقام: لاتينية مع فاصل آلاف لاتيني (8,400).
	 * أي قيمة غير رقمية بحتة تُعاد كما هي.
	 */
	public static function num( $value ) {
		$value = trim( (string) $value );
		if ( $value === '' ) return '';
		$bare = str_replace( array( ',', ' ' ), '', $value );
		if ( $bare !== '' && ctype_digit( $bare ) ) {
			return number_format( (float) $bare, 0, '.', ',' );
		}
		return $value;
	}

	/** رقم الهاتف: من حقل التصنيف وإلا من إعدادات القالب */
	public static function phone( $term_id ) {
		$p = self::get( $term_id, 'sl_phone' );
		return $p !== '' ? $p : (string) get_option( 'Phone' );
	}

	public static function whatsapp( $term_id ) {
		$w = self::get( $term_id, 'sl_whatsapp' );
		return $w !== '' ? $w : (string) get_option( 'Whatsapp' );
	}

	/** المدن المختارة ككائنات terms من تصنيف المدن الموجود */
	public static function cities( $term_id ) {
		$ids = get_term_meta( (int) $term_id, 'sl_cities', true );
		if ( ! is_array( $ids ) || empty( $ids ) ) {
			return array();
		}
		$out = array();
		foreach ( $ids as $id ) {
			$t = get_term( (int) $id, self::CITY_TAX );
			if ( $t && ! is_wp_error( $t ) ) $out[] = $t;
		}
		return $out;
	}

	/** رابط المدينة: أرشيف المدينة الافتراضي، أو رابط الخدمة داخل المدينة */
	public static function city_link( $term, $city ) {
		if ( self::get( $term->term_id, 'sl_city_mode', 'archive' ) === 'service'
			&& class_exists( 'CityServices' ) && get_option( 'services_url' ) ) {
			return ( new CityServices )->ServiceQVar( $term, $city );
		}
		$link = get_term_link( $city );
		return is_wp_error( $link ) ? '' : $link;
	}

	/** الأسئلة المختارة من نوع محتوى الأسئلة الموجود */
	public static function faqs( $term_id ) {
		$ids = get_term_meta( (int) $term_id, 'sl_faqs', true );
		if ( ! is_array( $ids ) || empty( $ids ) ) {
			return array();
		}
		$posts = get_posts( array(
			'post_type'      => self::FAQ_TYPE,
			'post__in'       => array_map( 'intval', $ids ),
			'orderby'        => 'post__in',
			'posts_per_page' => -1,
		) );
		return $posts;
	}

	/* -------------------------------- JSON-LD ----------------------------------- */

	public static function schema( $term ) {
		$tid    = $term->term_id;
		$cities = self::cities( $tid );
		$faqs   = self::faqs( $tid );
		$graph  = array();

		$area = array();
		foreach ( $cities as $c ) {
			$area[] = array( '@type' => 'City', 'name' => $c->name );
		}

		$service = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'name'        => self::get( $tid, 'sl_hero_title', $term->name ),
			'serviceType' => $term->name,
			'url'         => get_term_link( $term ),
			'provider'    => array(
				'@type' => 'LocalBusiness',
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
			),
		);
		$desc = self::get( $tid, 'sl_hero_promise', $term->description );
		if ( $desc !== '' ) {
			$service['description'] = wp_strip_all_tags( $desc );
		}
		$phone = self::phone( $tid );
		if ( $phone !== '' ) {
			$service['provider']['telephone'] = $phone;
		}
		if ( ! empty( $area ) ) {
			$service['areaServed'] = $area;
		}
		$graph[] = $service;

		if ( ! empty( $faqs ) ) {
			$items = array();
			foreach ( $faqs as $f ) {
				$answer = wp_strip_all_tags( strip_shortcodes( $f->post_content ) );
				if ( trim( $answer ) === '' ) continue;
				$items[] = array(
					'@type'          => 'Question',
					'name'           => $f->post_title,
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $answer,
					),
				);
			}
			if ( ! empty( $items ) ) {
				$graph[] = array(
					'@context'   => 'https://schema.org',
					'@type'      => 'FAQPage',
					'mainEntity' => $items,
				);
			}
		}

		return $graph;
	}

	public static function print_schema( $term ) {
		foreach ( self::schema( $term ) as $node ) {
			$json = wp_json_encode( $node, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			if ( $json === false ) continue;
			echo '<script type="application/ld+json">' . $json . '</script>';
		}
	}

	/* --------------------------------- Render ----------------------------------- */

	/**
	 * نقطة الدخول التي يستدعيها قالب الأرشيف.
	 * @param WP_Term $term
	 * @param object  $tpl كائن ThemeStatic لإعادة استخدام جزئياته
	 */
	public static function render( $term, $tpl = null ) {
		ServiceLanding_Frontend::render( $term, $tpl );
	}
}

ServiceLanding::init();
