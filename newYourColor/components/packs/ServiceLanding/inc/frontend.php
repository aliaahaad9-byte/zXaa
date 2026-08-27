<?php
/**
 * عرض صفحة هبوط الخدمة.
 * لا يوجد في هذا الملف أي نص محتوى — كل ما يُعرض يأتي من حقول التصنيف.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class ServiceLanding_Frontend {

	protected static $assets_done = false;

	/** طباعة الأنماط مرة واحدة داخل هذه الصفحة فقط */
	protected static function assets() {
		if ( self::$assets_done ) return;
		self::$assets_done = true;
		$css = dirname( dirname( __FILE__ ) ) . '/assets/landing.css';
		if ( file_exists( $css ) ) {
			echo '<style id="sl-landing-css">' . file_get_contents( $css ) . '</style>';
		}
	}

	protected static function scripts() {
		$js = dirname( dirname( __FILE__ ) ) . '/assets/landing.js';
		if ( file_exists( $js ) ) {
			echo '<script id="sl-landing-js">' . file_get_contents( $js ) . '</script>';
		}
	}

	/** عنوان قسم: يُطبع فقط إن كان له نص في الحقول */
	protected static function head( $title, $sub = '' ) {
		if ( $title === '' && $sub === '' ) return;
		echo '<div class="sl-head">';
		if ( $title !== '' ) {
			echo '<h2 class="sl-h2">' . esc_html( $title ) . '</h2>';
		}
		if ( $sub !== '' ) {
			echo '<p class="sl-sub">' . esc_html( $sub ) . '</p>';
		}
		echo '</div>';
	}

	/** أيقونة الميزة: كلاس فونت أوسم كما يدخله المحرر */
	protected static function icon( $class ) {
		$class = trim( (string) $class );
		if ( $class === '' ) return '';
		return '<i class="' . esc_attr( $class ) . '" aria-hidden="true"></i>';
	}

	protected static function call_buttons( $tid, $extra_class = '' ) {
		$phone    = ServiceLanding::phone( $tid );
		$whatsapp = ServiceLanding::whatsapp( $tid );
		$lbl_call = ServiceLanding::get( $tid, 'sl_cta_call' );
		$lbl_wa   = ServiceLanding::get( $tid, 'sl_cta_whatsapp' );

		if ( $phone === '' && $whatsapp === '' ) return;

		echo '<div class="sl-cta ' . esc_attr( $extra_class ) . '">';
		if ( $phone !== '' && $lbl_call !== '' ) {
			echo '<a class="sl-btn sl-btn--call btn-phone" href="tel:' . esc_attr( $phone ) . '" data-call="Phone">';
			echo '<svg viewBox="0 0 512 512" aria-hidden="true"><path d="M352 320c-32 32-32 64-64 64s-64-32-96-64-64-64-64-96 32-32 64-64-64-128-96-128-96 96-96 96c0 64 65.75 193.75 128 256s192 128 256 128c0 0 96-64 96-96s-96-128-128-96z"/></svg>';
			echo '<span>' . esc_html( $lbl_call ) . '</span>';
			echo '</a>';
		}
		if ( $whatsapp !== '' && $lbl_wa !== '' ) {
			echo '<a class="sl-btn sl-btn--wa btn-whatsapp" href="https://wa.me/' . esc_attr( trim( $whatsapp ) ) . '" target="_blank" rel="nofollow noopener noreferrer" data-call="whatsapp">';
			echo '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>';
			echo '<span>' . esc_html( $lbl_wa ) . '</span>';
			echo '</a>';
		}
		echo '</div>';
	}

	/** حقول النموذج — التسميات كلها من الحقول، ولا تُطبع تسمية فارغة */
	protected static function form( $term, $variant ) {
		$tid    = $term->term_id;
		$cities = ServiceLanding::cities( $tid );
		$l_name = ServiceLanding::get( $tid, 'sl_label_name' );
		$l_tel  = ServiceLanding::get( $tid, 'sl_label_phone' );
		$l_city = ServiceLanding::get( $tid, 'sl_label_city' );
		$l_srv  = ServiceLanding::get( $tid, 'sl_label_service' );
		$l_det  = ServiceLanding::get( $tid, 'sl_label_details' );
		$submit = ServiceLanding::get( $tid, 'sl_form_submit' );
		$note   = ServiceLanding::get( $tid, 'sl_form_note' );

		if ( $l_name === '' || $l_tel === '' || $submit === '' ) return;

		$uid  = 'sl-' . $variant . '-' . $tid;
		$full = ( $variant === 'full' );

		echo '<form class="sl-form sl-form--' . esc_attr( $variant ) . '" data-sl-form="1" novalidate>';
		echo '<div class="sl-form-grid">';

		echo '<p class="sl-field"><label for="' . esc_attr( $uid ) . '-n">' . esc_html( $l_name ) . '</label>';
		echo '<input type="text" id="' . esc_attr( $uid ) . '-n" name="names" required></p>';

		echo '<p class="sl-field"><label for="' . esc_attr( $uid ) . '-t">' . esc_html( $l_tel ) . '</label>';
		echo '<input type="tel" inputmode="tel" dir="ltr" id="' . esc_attr( $uid ) . '-t" name="phone" required></p>';

		if ( $l_city !== '' && ! empty( $cities ) ) {
			echo '<p class="sl-field"><label for="' . esc_attr( $uid ) . '-c">' . esc_html( $l_city ) . '</label>';
			echo '<select id="' . esc_attr( $uid ) . '-c" name="address">';
			foreach ( $cities as $c ) {
				echo '<option value="' . esc_attr( $c->name ) . '">' . esc_html( $c->name ) . '</option>';
			}
			echo '</select></p>';
		}

		if ( $full && $l_srv !== '' ) {
			echo '<p class="sl-field"><label for="' . esc_attr( $uid ) . '-s">' . esc_html( $l_srv ) . '</label>';
			echo '<input type="text" id="' . esc_attr( $uid ) . '-s" name="service" value="' . esc_attr( $term->name ) . '" readonly></p>';
		}

		if ( $full && $l_det !== '' ) {
			echo '<p class="sl-field sl-field--full"><label for="' . esc_attr( $uid ) . '-m">' . esc_html( $l_det ) . '</label>';
			echo '<textarea id="' . esc_attr( $uid ) . '-m" name="message" rows="3"></textarea></p>';
		}

		echo '<p class="sl-field sl-field--full sl-field--submit">';
		echo '<button type="submit" class="sl-submit">' . esc_html( $submit ) . '</button></p>';
		echo '</div>';

		if ( $note !== '' ) {
			echo '<p class="sl-form-note">' . esc_html( $note ) . '</p>';
		}
		echo '<div class="sl-form-result" role="status" aria-live="polite"></div>';
		echo '</form>';
	}

	/* ================================ الصفحة ================================ */

	public static function render( $term, $tpl = null ) {
		$tid = $term->term_id;

		self::assets();
		ServiceLanding::print_schema( $term );

		echo '<div class="sl-page" dir="rtl" data-sl-term="' . (int) $tid . '">';

		/* ---------- 1 · المقدمة ---------- */
		$hero_title   = ServiceLanding::get( $tid, 'sl_hero_title', $term->name );
		$hero_promise = ServiceLanding::get( $tid, 'sl_hero_promise' );
		$badges       = ServiceLanding::rows( $tid, 'sl_badges' );
		$form_title   = ServiceLanding::get( $tid, 'sl_form_title' );
		$form_hint    = ServiceLanding::get( $tid, 'sl_form_hint' );

		echo '<section class="sl-hero">';
		echo '<div class="sl-wrap"><div class="sl-hero-grid">';
		echo '<div class="sl-hero-main">';
		echo '<h1 class="sl-h1">' . esc_html( $hero_title ) . '</h1>';
		if ( $hero_promise !== '' ) {
			echo '<p class="sl-promise">' . esc_html( $hero_promise ) . '</p>';
		}
		if ( ! empty( $badges ) ) {
			echo '<ul class="sl-badges">';
			foreach ( $badges as $b ) {
				$txt = isset( $b['text'] ) ? trim( $b['text'] ) : '';
				if ( $txt === '' ) continue;
				echo '<li class="sl-badge"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 15.6L6.6 12.4l1.4-1.4 2.8 2.8 5.6-5.6 1.4 1.4-7 7z"/></svg>' . esc_html( $txt ) . '</li>';
			}
			echo '</ul>';
		}
		self::call_buttons( $tid, 'sl-cta--hero' );
		echo '</div>';

		echo '<aside class="sl-hero-form">';
		if ( $form_title !== '' ) {
			echo '<h2 class="sl-form-title">' . esc_html( $form_title ) . '</h2>';
		}
		if ( $form_hint !== '' ) {
			echo '<p class="sl-form-hint">' . esc_html( $form_hint ) . '</p>';
		}
		self::form( $term, 'hero' );
		echo '</aside>';

		echo '</div></div></section>';

		/* ---------- 2 · شريط الأرقام ---------- */
		$stats = ServiceLanding::rows( $tid, 'sl_stats' );
		if ( ! empty( $stats ) ) {
			echo '<section class="sl-stats"><div class="sl-wrap"><div class="sl-stats-grid">';
			foreach ( $stats as $s ) {
				$v = isset( $s['value'] ) ? trim( $s['value'] ) : '';
				$l = isset( $s['label'] ) ? trim( $s['label'] ) : '';
				if ( $v === '' && $l === '' ) continue;
				echo '<div class="sl-stat">';
				if ( $v !== '' ) echo '<b class="sl-stat-v">' . esc_html( ServiceLanding::num( $v ) ) . '</b>';
				if ( $l !== '' ) echo '<span class="sl-stat-l">' . esc_html( $l ) . '</span>';
				echo '</div>';
			}
			echo '</div></div></section>';
		}

		/* ---------- 3 · المميزات ---------- */
		$features = ServiceLanding::rows( $tid, 'sl_features' );
		if ( ! empty( $features ) ) {
			echo '<section class="sl-sec sl-sec--features"><div class="sl-wrap">';
			self::head(
				ServiceLanding::get( $tid, 'sl_features_title' ),
				ServiceLanding::get( $tid, 'sl_features_sub' )
			);
			echo '<div class="sl-feat-grid">';
			foreach ( $features as $f ) {
				$title = isset( $f['title'] ) ? trim( $f['title'] ) : '';
				if ( $title === '' ) continue;
				$text   = isset( $f['text'] ) ? trim( $f['text'] ) : '';
				$metric = isset( $f['metric'] ) ? trim( $f['metric'] ) : '';
				$ico    = self::icon( isset( $f['icon'] ) ? $f['icon'] : '' );
				echo '<article class="sl-feat">';
				if ( $ico !== '' ) echo '<span class="sl-feat-ico">' . $ico . '</span>';
				echo '<h3 class="sl-feat-title">' . esc_html( $title ) . '</h3>';
				if ( $text !== '' ) echo '<p class="sl-feat-text">' . esc_html( $text ) . '</p>';
				if ( $metric !== '' ) echo '<span class="sl-metric">' . esc_html( ServiceLanding::num( $metric ) ) . '</span>';
				echo '</article>';
			}
			echo '</div>';
			self::call_buttons( $tid, 'sl-cta--inline' );
			echo '</div></section>';
		}

		/* ---------- 4 · خطوات العمل ---------- */
		$steps = ServiceLanding::rows( $tid, 'sl_steps' );
		if ( ! empty( $steps ) ) {
			$total = count( $steps );
			echo '<section class="sl-sec sl-sec--steps"><div class="sl-wrap">';
			self::head(
				ServiceLanding::get( $tid, 'sl_steps_title' ),
				ServiceLanding::get( $tid, 'sl_steps_sub' )
			);
			echo '<ol class="sl-steps">';
			$i = 0;
			foreach ( $steps as $st ) {
				$i++;
				$title = isset( $st['title'] ) ? trim( $st['title'] ) : '';
				if ( $title === '' ) continue;
				$text = isset( $st['text'] ) ? trim( $st['text'] ) : '';
				$last = ( $i === $total ) ? ' is-last' : '';
				$pre  = ( $i === $total - 1 ) ? ' is-prelast' : '';
				echo '<li class="sl-step' . $last . $pre . '">';
				echo '<div class="sl-step-top">';
				echo '<span class="sl-step-num">' . esc_html( ServiceLanding::num( $i ) ) . '</span>';
				if ( $i < $total ) echo '<span class="sl-step-line" aria-hidden="true"></span>';
				echo '</div>';
				echo '<h3 class="sl-step-title">' . esc_html( $title ) . '</h3>';
				if ( $text !== '' ) echo '<p class="sl-step-text">' . esc_html( $text ) . '</p>';
				echo '</li>';
			}
			echo '</ol>';
			echo '</div></section>';
		}

		/* ---------- 5 · المدن ---------- */
		$cities = ServiceLanding::cities( $tid );
		if ( ! empty( $cities ) ) {
			echo '<section class="sl-sec sl-sec--cities"><div class="sl-wrap">';
			self::head(
				ServiceLanding::get( $tid, 'sl_cities_title' ),
				ServiceLanding::get( $tid, 'sl_cities_sub' )
			);
			echo '<div class="sl-cities">';
			foreach ( $cities as $c ) {
				$url = ServiceLanding::city_link( $term, $c );
				if ( $url === '' ) continue;
				echo '<a class="sl-city" href="' . esc_url( $url ) . '">' . esc_html( $c->name ) . '</a>';
			}
			echo '</div>';
			echo '</div></section>';
		}

		/* ---------- 6 · الأسئلة الشائعة ---------- */
		$faqs = ServiceLanding::faqs( $tid );
		if ( ! empty( $faqs ) ) {
			echo '<section class="sl-sec sl-sec--faq"><div class="sl-wrap">';
			self::head(
				ServiceLanding::get( $tid, 'sl_faq_title' ),
				ServiceLanding::get( $tid, 'sl_faq_sub' )
			);
			echo '<div class="sl-faqs">';
			$n = 0;
			foreach ( $faqs as $f ) {
				$n++;
				$answer = trim( wp_strip_all_tags( strip_shortcodes( $f->post_content ) ) );
				$open   = ( $n === 1 );
				echo '<div class="sl-faq' . ( $open ? ' is-open' : '' ) . '">';
				echo '<button type="button" class="sl-faq-q" aria-expanded="' . ( $open ? 'true' : 'false' ) . '">';
				echo '<span>' . esc_html( $f->post_title ) . '</span>';
				echo '<i class="sl-faq-chev" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7.41 8.59 12 13.17l4.59-4.58L18 10l-6 6-6-6z"/></svg></i>';
				echo '</button>';
				if ( $answer !== '' ) {
					echo '<div class="sl-faq-a"><p>' . esc_html( $answer ) . '</p></div>';
				}
				echo '</div>';
			}
			echo '</div>';
			self::call_buttons( $tid, 'sl-cta--inline' );
			echo '</div></section>';
		}

		/* ---------- 7 · نموذج الطلب الكامل ---------- */
		$order_title = ServiceLanding::get( $tid, 'sl_order_title' );
		if ( $order_title !== '' ) {
			echo '<section class="sl-sec sl-sec--order"><div class="sl-wrap">';
			self::head( $order_title, ServiceLanding::get( $tid, 'sl_order_sub' ) );
			echo '<div class="sl-order-box">';
			self::form( $term, 'full' );
			echo '</div>';
			echo '</div></section>';
		}

		/* ---------- 8 · المقالات — نفس الاستعلام ونفس البطاقة بلا تغيير ---------- */
		echo '<section class="sl-sec sl-sec--posts"><div class="sl-wrap">';
		$posts_title = ServiceLanding::get( $tid, 'sl_posts_title' );
		if ( $posts_title !== '' ) {
			echo '<div class="titles_concept"><h2>' . esc_html( $posts_title ) . '</h2></div>';
		}
		if ( $tpl !== null && method_exists( $tpl, 'Part' ) ) {
			$tpl->Part( 'Posts', array(
				'AutoLoadmore'  => true,
				'post__not_in'  => array( $term->term_id ),
				'UniqId'        => uniqid(),
				'term'          => array( $term ),
			) );
		}
		echo '</div></section>';

		echo '</div>';

		self::scripts();
	}
}
