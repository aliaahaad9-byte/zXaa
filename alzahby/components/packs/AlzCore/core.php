<?php
/**
 * Alz — نواة منصة QurTap
 * الجداول: alz_profiles (النشاط) · alz_links (الروابط) · alz_stands (الاستاندات المطبوعة)
 *
 * قاعدة الحماية: أي عملية كتابة تأخذ owner_id من get_current_user_id() فقط،
 * ولا يتم قبول profile_id من المتصفح أبداً — بديل Row Level Security.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Alz {

	const DB_VERSION = '1.0.0';
	const ROLE       = 'alz_client';
	const MAX_LINKS  = 60;

	const SLUG_RE  = '/^[a-z0-9](?:[a-z0-9-]{1,38})[a-z0-9]$/';
	const CODE_RE  = '/^[A-Za-z0-9]{6,16}$/';
	const HEX_RE   = '/^#[0-9a-fA-F]{6}$/';
	const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';

	const RESERVED = array(
		'dashboard', 'login', 'logout', 'auth', 'api', 's', 'u', 'admin', 'wp-admin', 'wp-login', 'wp-json',
		'wp-content', 'wp-includes', 'about', 'pricing', 'terms', 'privacy', 'help', 'support', 'order',
		'www', 'app', 'static', 'feed', 'sitemap', 'search', 'page', 'category', 'tag', 'author', 'blog',
	);

	const CATEGORIES = array(
		'doctor'      => 'عيادة / طبيب',
		'lawyer'      => 'مكتب محاماة',
		'restaurant'  => 'مطعم',
		'cafe'        => 'كافيه',
		'supermarket' => 'سوبر ماركت',
		'company'     => 'شركة',
		'shop'        => 'محل تجاري',
		'salon'       => 'صالون / بيوتي سنتر',
		'other'       => 'نشاط آخر',
	);

	private static $icons = null;

	/* =================================================================
	 *  Install
	 * ================================================================= */
	public static function table( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'alz_' . $name;
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();
		$p       = self::table( 'profiles' );
		$l       = self::table( 'links' );
		$s       = self::table( 'stands' );

		dbDelta(
			"CREATE TABLE $p (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			owner_id bigint(20) unsigned NOT NULL,
			slug varchar(40) NOT NULL,
			business_name varchar(80) NOT NULL,
			category varchar(20) NOT NULL DEFAULT 'other',
			bio text NULL,
			logo_url varchar(500) NULL,
			cover_url varchar(500) NULL,
			logo_shape varchar(10) NOT NULL DEFAULT 'circle',
			theme_primary char(7) NOT NULL DEFAULT '#0f172a',
			theme_background char(7) NOT NULL DEFAULT '#f8fafc',
			theme_text char(7) NOT NULL DEFAULT '#0f172a',
			button_style varchar(10) NOT NULL DEFAULT 'brand',
			layout varchar(10) NOT NULL DEFAULT 'list',
			is_published tinyint(1) NOT NULL DEFAULT 1,
			view_count bigint(20) unsigned NOT NULL DEFAULT 0,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY owner_id (owner_id),
			UNIQUE KEY slug (slug)
			) $charset;"
		);

		dbDelta(
			"CREATE TABLE $l (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			profile_id bigint(20) unsigned NOT NULL,
			type varchar(20) NOT NULL,
			label varchar(60) NULL,
			value varchar(500) NOT NULL,
			position int(11) NOT NULL DEFAULT 0,
			is_active tinyint(1) NOT NULL DEFAULT 1,
			click_count bigint(20) unsigned NOT NULL DEFAULT 0,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY profile_position (profile_id,position)
			) $charset;"
		);

		// code بترميز ascii_bin حتى تكون الأكواد حساسة لحالة الأحرف
		dbDelta(
			"CREATE TABLE $s (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			code varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
			profile_id bigint(20) unsigned NULL,
			label varchar(60) NULL,
			batch varchar(40) NULL,
			claimed_at datetime NULL,
			scan_count bigint(20) unsigned NOT NULL DEFAULT 0,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY code (code),
			KEY profile_id (profile_id)
			) $charset;"
		);

		if ( ! get_role( self::ROLE ) ) {
			add_role( self::ROLE, 'عميل QurTap', array( 'read' => true ) );
		}

		update_option( 'alz_db_version', self::DB_VERSION );
		update_option( 'alz_flush_rewrite', 1 );
	}

	/* =================================================================
	 *  Helpers
	 * ================================================================= */
	public static function opt( $key, $default = '' ) {
		$v = get_option( 'alz_' . $key, '' );
		return ( '' === $v || null === $v || false === $v ) ? $default : $v;
	}

	public static function now() {
		return current_time( 'mysql', true );
	}

	public static function icon( $name, $class = '' ) {
		if ( null === self::$icons ) {
			self::$icons = require __DIR__ . '/icons.php';
		}
		$svg = isset( self::$icons[ $name ] ) ? self::$icons[ $name ] : self::$icons['link'];
		return $class ? str_replace( 'class="ic"', 'class="ic ' . esc_attr( $class ) . '"', $svg ) : $svg;
	}

	public static function icons() {
		if ( null === self::$icons ) {
			self::$icons = require __DIR__ . '/icons.php';
		}
		return self::$icons;
	}

	public static function latin_digits( $s ) {
		$ar = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
		$en = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
		return str_replace( $ar, $en, (string) $s );
	}

	/** حد بسيط لعدد المحاولات (Rate limit) باستخدام transients */
	public static function rate_limit( $key, $max, $window ) {
		$k     = 'alz_rl_' . md5( $key );
		$count = (int) get_transient( $k );
		if ( $count >= $max ) {
			return false;
		}
		set_transient( $k, $count + 1, $window );
		return true;
	}

	public static function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
	}

	public static function dashboard_url( $path = '' ) {
		return home_url( '/dashboard/' . ltrim( $path, '/' ) );
	}

	public static function login_url( $next = '' ) {
		$url = home_url( '/dashboard/login/' );
		return $next ? add_query_arg( 'next', rawurlencode( $next ), $url ) : $url;
	}

	/** يقبل فقط مسارات داخل نفس الموقع (منع Open Redirect) */
	public static function safe_next( $next, $fallback = '' ) {
		$fallback = $fallback ? $fallback : self::dashboard_url();
		$next     = is_string( $next ) ? wp_unslash( $next ) : '';
		if ( '' === $next ) {
			return $fallback;
		}
		if ( 0 === strpos( $next, '/' ) && 0 !== strpos( $next, '//' ) && 0 !== strpos( $next, '/\\' ) ) {
			$next = home_url( $next );
		}
		return wp_validate_redirect( $next, $fallback );
	}

	public static function profile_urls( $profile ) {
		return array(
			'permanent' => home_url( '/u/' . (int) $profile->id . '/' ),
			'pretty'    => home_url( '/' . $profile->slug . '/' ),
		);
	}

	/* =================================================================
	 *  Link registry  (يُصدَّر أيضاً للجافاسكربت)
	 * ================================================================= */
	public static function link_defs() {
		return array(
			'facebook'      => array( 'label' => 'فيسبوك', 'color' => '#1877F2', 'on' => '#fff', 'input' => 'handle', 'group' => 'social', 'placeholder' => 'اسم الصفحة أو رابطها', 'hint' => 'مثال: qurtap أو https://facebook.com/qurtap' ),
			'instagram'     => array( 'label' => 'انستجرام', 'color' => '#E4405F', 'on' => '#fff', 'input' => 'handle', 'group' => 'social', 'placeholder' => '@username', 'gradient' => 'linear-gradient(45deg,#feda75 0%,#fa7e1e 25%,#d62976 50%,#962fbf 75%,#4f5bd5 100%)' ),
			'tiktok'        => array( 'label' => 'تيك توك', 'color' => '#000000', 'on' => '#fff', 'input' => 'handle', 'group' => 'social', 'placeholder' => '@username' ),
			'whatsapp'      => array( 'label' => 'واتساب', 'color' => '#25D366', 'on' => '#fff', 'input' => 'phone', 'group' => 'contact', 'placeholder' => '01xxxxxxxxx', 'hint' => 'الرقم المصري يتحول تلقائياً للصيغة الدولية' ),
			'phone'         => array( 'label' => 'اتصال', 'color' => '#0EA5E9', 'on' => '#fff', 'input' => 'phone', 'group' => 'contact', 'placeholder' => '01xxxxxxxxx' ),
			'maps'          => array( 'label' => 'اللوكيشن', 'color' => '#4285F4', 'on' => '#fff', 'input' => 'url', 'group' => 'google', 'placeholder' => 'https://maps.app.goo.gl/...', 'hint' => 'انسخ رابط المشاركة من خرائط جوجل' ),
			'google_review' => array( 'label' => 'قيّمنا على جوجل', 'color' => '#FBBC04', 'on' => '#1f1300', 'input' => 'url', 'group' => 'google', 'placeholder' => 'https://g.page/r/.../review', 'hint' => 'من Google Business Profile ← اطلب تقييمات' ),
			'instapay'      => array( 'label' => 'إنستاباي', 'color' => '#5B2A86', 'on' => '#fff', 'input' => 'text', 'group' => 'payment', 'placeholder' => 'https://ipn.eg/S/... أو name@instapay', 'hint' => 'رابط الدفع يفتح التطبيق مباشرة، والعنوان (@instapay) يتم نسخه' ),
			'wallet'        => array( 'label' => 'محفظة إلكترونية', 'color' => '#E60000', 'on' => '#fff', 'input' => 'phone', 'group' => 'payment', 'placeholder' => 'رقم المحفظة', 'hint' => 'فودافون كاش / اتصالات كاش / أورنج كاش.. يتم نسخ الرقم بضغطة' ),
			'website'       => array( 'label' => 'الموقع الإلكتروني', 'color' => '#0F172A', 'on' => '#fff', 'input' => 'url', 'group' => 'other', 'placeholder' => 'https://example.com' ),
			'email'         => array( 'label' => 'البريد الإلكتروني', 'color' => '#64748B', 'on' => '#fff', 'input' => 'email', 'group' => 'contact', 'placeholder' => 'info@example.com' ),
			'snapchat'      => array( 'label' => 'سناب شات', 'color' => '#FFFC00', 'on' => '#111', 'input' => 'handle', 'group' => 'social', 'placeholder' => 'username' ),
			'youtube'       => array( 'label' => 'يوتيوب', 'color' => '#FF0000', 'on' => '#fff', 'input' => 'handle', 'group' => 'social', 'placeholder' => '@channel أو رابط القناة' ),
			'x'             => array( 'label' => 'إكس (تويتر)', 'color' => '#000000', 'on' => '#fff', 'input' => 'handle', 'group' => 'social', 'placeholder' => '@username' ),
			'telegram'      => array( 'label' => 'تيليجرام', 'color' => '#26A5E4', 'on' => '#fff', 'input' => 'handle', 'group' => 'contact', 'placeholder' => 'username' ),
			'menu'          => array( 'label' => 'المنيو', 'color' => '#B45309', 'on' => '#fff', 'input' => 'menu', 'group' => 'venue', 'placeholder' => 'https://... أو ارفع ملف PDF', 'hint' => 'ارفع المنيو PDF ليفتح داخل الصفحة، أو ضع رابط المنيو' ),
			'wifi'          => array( 'label' => 'واي فاي', 'color' => '#0891B2', 'on' => '#fff', 'input' => 'wifi', 'group' => 'venue', 'placeholder' => '', 'hint' => 'الزبون يرى اسم الشبكة وينسخ الباسورد بضغطة' ),
			'custom'        => array( 'label' => 'رابط مخصص', 'color' => '#334155', 'on' => '#fff', 'input' => 'url', 'group' => 'other', 'placeholder' => 'https://...' ),
		);
	}

	public static function link_groups() {
		return array(
			'venue'   => 'للمطاعم والكافيهات',
			'social'  => 'السوشيال ميديا',
			'google'  => 'جوجل',
			'payment' => 'طرق الدفع',
			'contact' => 'التواصل',
			'other'   => 'روابط أخرى',
		);
	}

	public static function is_link_type( $t ) {
		return is_string( $t ) && array_key_exists( $t, self::link_defs() );
	}

	private static function is_url( $v ) {
		return (bool) preg_match( '#^https?://#i', $v );
	}

	private static function handle( $v ) {
		return preg_replace( '/\s+/', '', ltrim( trim( $v ), '@' ) );
	}

	public static function normalize_phone( $v ) {
		$d = preg_replace( '/[^\d+]/', '', self::latin_digits( $v ) );
		$d = ltrim( $d, '+' );
		if ( 0 === strpos( $d, '00' ) ) {
			$d = substr( $d, 2 );
		}
		if ( preg_match( '/^01\d{9}$/', $d ) ) {
			$d = '2' . $d;
		}
		return $d;
	}

	/** قيمة الواي فاي تُخزَّن JSON: {"s":"اسم الشبكة","p":"الباسورد"} */
	public static function wifi_parse( $value ) {
		$d = json_decode( (string) $value, true );
		if ( ! is_array( $d ) || ! isset( $d['s'] ) || ! is_string( $d['s'] ) ) {
			return null;
		}
		return array(
			'ssid' => trim( $d['s'] ),
			'pass' => isset( $d['p'] ) && is_string( $d['p'] ) ? $d['p'] : '',
		);
	}

	/** ملف PDF مرفوع على نفس الموقع — يُعرض داخل الصفحة بعارض PDF المدمج */
	public static function is_local_pdf( $url ) {
		$url = (string) $url;
		if ( ! preg_match( '#\.pdf(\?.*)?$#i', $url ) ) {
			return false;
		}
		$base = set_url_scheme( wp_get_upload_dir()['baseurl'], 'https' );
		return 0 === strpos( set_url_scheme( $url, 'https' ), $base );
	}

	public static function link_action( $type, $value ) {
		if ( 'wifi' === $type ) {
			return 'wifi';
		}
		if ( 'wallet' === $type ) {
			return 'copy';
		}
		if ( 'instapay' === $type && ! self::is_url( trim( $value ) ) ) {
			return 'copy';
		}
		return 'open';
	}

	public static function copy_value( $type, $value ) {
		if ( 'wallet' === $type ) {
			return preg_replace( '/\D/', '', self::latin_digits( $value ) );
		}
		return trim( $value );
	}

	/** رابط آمن لكل منصة (https / tel / mailto فقط) */
	public static function build_href( $type, $raw ) {
		$v = trim( (string) $raw );
		if ( '' === $v ) {
			return '';
		}
		$u = self::is_url( $v );
		switch ( $type ) {
			case 'facebook':
				$href = $u ? $v : 'https://www.facebook.com/' . rawurlencode( self::handle( $v ) );
				break;
			case 'instagram':
				$href = $u ? $v : 'https://www.instagram.com/' . rawurlencode( self::handle( $v ) );
				break;
			case 'tiktok':
				$href = $u ? $v : 'https://www.tiktok.com/@' . rawurlencode( self::handle( $v ) );
				break;
			case 'snapchat':
				$href = $u ? $v : 'https://www.snapchat.com/add/' . rawurlencode( self::handle( $v ) );
				break;
			case 'youtube':
				$href = $u ? $v : 'https://www.youtube.com/@' . rawurlencode( self::handle( $v ) );
				break;
			case 'x':
				$href = $u ? $v : 'https://x.com/' . rawurlencode( self::handle( $v ) );
				break;
			case 'telegram':
				$href = $u ? $v : 'https://t.me/' . rawurlencode( self::handle( $v ) );
				break;
			case 'whatsapp':
				$href = $u ? $v : 'https://wa.me/' . self::normalize_phone( $v );
				break;
			case 'phone':
				$href = 'tel:+' . self::normalize_phone( $v );
				break;
			case 'wallet':
				$href = 'tel:' . self::copy_value( $type, $v );
				break;
			case 'email':
				$href = is_email( $v ) ? 'mailto:' . $v : '';
				break;
			case 'instapay':
				$href = $u ? $v : '';
				break;
			default:
				$href = $u ? $v : 'https://' . ltrim( $v, '/' );
		}
		return esc_url_raw( $href, array( 'https', 'http', 'tel', 'mailto' ) );
	}

	public static function display_label( $type, $label ) {
		$label = trim( (string) $label );
		$defs  = self::link_defs();
		return '' !== $label ? $label : $defs[ $type ]['label'];
	}

	/** يرجع رسالة خطأ أو سلسلة فارغة */
	public static function validate_link( $type, $value ) {
		$v = trim( (string) $value );
		if ( ! self::is_link_type( $type ) ) {
			return 'نوع رابط غير معروف';
		}
		if ( '' === $v ) {
			return 'القيمة مطلوبة';
		}
		if ( mb_strlen( $v ) > 500 ) {
			return 'القيمة طويلة جداً';
		}
		if ( 'wifi' === $type ) {
			$w = self::wifi_parse( $v );
			if ( ! $w || '' === $w['ssid'] ) {
				return 'اسم الشبكة مطلوب';
			}
			if ( mb_strlen( $w['ssid'] ) > 32 ) {
				return 'اسم الشبكة حتى 32 حرف';
			}
			if ( mb_strlen( $w['pass'] ) > 63 ) {
				return 'الباسورد حتى 63 حرف';
			}
			return '';
		}
		if ( preg_match( '/^\s*(javascript|data|vbscript|file):/i', $v ) ) {
			return 'رابط غير مسموح';
		}
		$defs = self::link_defs();
		$in   = $defs[ $type ]['input'];
		if ( 'phone' === $in && strlen( self::normalize_phone( $v ) ) < 8 ) {
			return 'رقم غير صحيح';
		}
		if ( 'email' === $in && ! is_email( $v ) ) {
			return 'بريد غير صحيح';
		}
		if ( 'open' === self::link_action( $type, $v ) && '' === self::build_href( $type, $v ) ) {
			return 'رابط غير صحيح';
		}
		return '';
	}

	/* =================================================================
	 *  Profiles
	 * ================================================================= */
	private static function cast_profile( $row ) {
		if ( ! $row ) {
			return null;
		}
		$row->id           = (int) $row->id;
		$row->owner_id     = (int) $row->owner_id;
		$row->is_published = (bool) $row->is_published;
		$row->view_count   = (int) $row->view_count;
		return $row;
	}

	public static function profile_by_owner( $user_id ) {
		global $wpdb;
		$t = self::table( 'profiles' );
		return self::cast_profile( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE owner_id = %d", $user_id ) ) );
	}

	/** للعرض العام — المنشور فقط */
	public static function public_profile( $field, $value ) {
		global $wpdb;
		$t = self::table( 'profiles' );
		if ( 'slug' === $field ) {
			$value = strtolower( (string) $value );
			if ( ! preg_match( self::SLUG_RE, $value ) ) {
				return null;
			}
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE slug = %s AND is_published = 1", $value ) );
		} else {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE id = %d AND is_published = 1", (int) $value ) );
		}
		return self::cast_profile( $row );
	}

	public static function links( $profile_id, $active_only = false ) {
		global $wpdb;
		$t   = self::table( 'links' );
		$sql = "SELECT id, type, label, value, position, is_active, click_count FROM $t WHERE profile_id = %d";
		if ( $active_only ) {
			$sql .= ' AND is_active = 1';
		}
		$rows = $wpdb->get_results( $wpdb->prepare( $sql . ' ORDER BY position ASC, id ASC', $profile_id ) ); // phpcs:ignore
		foreach ( $rows as $r ) {
			$r->id          = (int) $r->id;
			$r->position    = (int) $r->position;
			$r->is_active   = (bool) $r->is_active;
			$r->click_count = (int) $r->click_count;
		}
		return $rows;
	}

	/** يرجع رسالة خطأ أو فارغ */
	public static function check_slug( $slug, $exclude_profile = 0 ) {
		global $wpdb;
		if ( ! preg_match( self::SLUG_RE, $slug ) ) {
			return 'الرابط يجب أن يكون 3-40 حرف إنجليزي صغير أو أرقام أو (-)';
		}
		if ( in_array( $slug, self::RESERVED, true ) ) {
			return 'هذا الرابط محجوز، اختر رابطاً آخر';
		}
		// لا يسمح بتعارض مع صفحة أو مقال موجود في الموقع
		if ( get_page_by_path( $slug, OBJECT, array( 'page', 'post' ) ) || term_exists( $slug ) ) {
			return 'هذا الرابط مستخدم في الموقع، اختر رابطاً آخر';
		}
		$t     = self::table( 'profiles' );
		$taken = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $t WHERE slug = %s AND id <> %d", $slug, $exclude_profile ) );
		return $taken ? 'هذا الرابط مستخدم من نشاط آخر' : '';
	}

	public static function create_profile( $user_id, $name, $slug, $category ) {
		global $wpdb;
		$name     = sanitize_text_field( $name );
		$slug     = strtolower( sanitize_text_field( $slug ) );
		$category = array_key_exists( $category, self::CATEGORIES ) ? $category : 'other';

		if ( self::profile_by_owner( $user_id ) ) {
			return new WP_Error( 'exists', 'لديك صفحة بالفعل' );
		}
		if ( mb_strlen( $name ) < 2 || mb_strlen( $name ) > 80 ) {
			return new WP_Error( 'name', 'اسم النشاط يجب أن يكون بين 2 و 80 حرف' );
		}
		$err = self::check_slug( $slug );
		if ( $err ) {
			return new WP_Error( 'slug', $err );
		}
		$ok = $wpdb->insert(
			self::table( 'profiles' ),
			array(
				'owner_id'      => $user_id,
				'slug'          => $slug,
				'business_name' => $name,
				'category'      => $category,
				'created_at'    => self::now(),
				'updated_at'    => self::now(),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s' )
		);
		if ( ! $ok ) {
			return new WP_Error( 'db', 'تعذر إنشاء الصفحة، ربما الرابط مستخدم' );
		}
		return self::profile_by_owner( $user_id );
	}

	private static function clean_hex( $v, $fallback ) {
		return ( is_string( $v ) && preg_match( self::HEX_RE, $v ) ) ? strtolower( $v ) : $fallback;
	}

	/** رابط صورة مسموح فقط إذا كان من مكتبة وسائط الموقع نفسه */
	private static function clean_media_url( $v ) {
		$v = esc_url_raw( (string) $v, array( 'https', 'http' ) );
		if ( '' === $v ) {
			return null;
		}
		$uploads = wp_get_upload_dir();
		$base    = set_url_scheme( $uploads['baseurl'], 'https' );
		return 0 === strpos( set_url_scheme( $v, 'https' ), $base ) ? $v : null;
	}

	/**
	 * حفظ كامل لصفحة المستخدم الحالي (البيانات + الروابط) داخل Transaction
	 *
	 * @return array|WP_Error
	 */
	public static function save( $user_id, $data, $links ) {
		global $wpdb;
		$profile = self::profile_by_owner( $user_id );
		if ( ! $profile ) {
			return new WP_Error( 'no_profile', 'لا توجد صفحة لهذا الحساب' );
		}

		// ---------- بيانات الصفحة ----------
		$name = isset( $data['business_name'] ) ? sanitize_text_field( $data['business_name'] ) : $profile->business_name;
		if ( mb_strlen( $name ) < 1 || mb_strlen( $name ) > 80 ) {
			return new WP_Error( 'name', 'اسم النشاط مطلوب (حتى 80 حرف)' );
		}
		$slug = isset( $data['slug'] ) ? strtolower( sanitize_text_field( $data['slug'] ) ) : $profile->slug;
		if ( $slug !== $profile->slug ) {
			$err = self::check_slug( $slug, $profile->id );
			if ( $err ) {
				return new WP_Error( 'slug', $err );
			}
		}
		$bio = isset( $data['bio'] ) ? sanitize_textarea_field( $data['bio'] ) : (string) $profile->bio;
		if ( mb_strlen( $bio ) > 300 ) {
			return new WP_Error( 'bio', 'النبذة حتى 300 حرف' );
		}

		$fields = array(
			'business_name'    => $name,
			'slug'             => $slug,
			'bio'              => '' === $bio ? null : $bio,
			'category'         => ( isset( $data['category'] ) && array_key_exists( $data['category'], self::CATEGORIES ) ) ? $data['category'] : $profile->category,
			'logo_url'         => array_key_exists( 'logo_url', $data ) ? self::clean_media_url( $data['logo_url'] ) : $profile->logo_url,
			'cover_url'        => array_key_exists( 'cover_url', $data ) ? self::clean_media_url( $data['cover_url'] ) : $profile->cover_url,
			'logo_shape'       => ( isset( $data['logo_shape'] ) && in_array( $data['logo_shape'], array( 'circle', 'square' ), true ) ) ? $data['logo_shape'] : $profile->logo_shape,
			'theme_primary'    => self::clean_hex( isset( $data['theme_primary'] ) ? $data['theme_primary'] : '', $profile->theme_primary ),
			'theme_background' => self::clean_hex( isset( $data['theme_background'] ) ? $data['theme_background'] : '', $profile->theme_background ),
			'theme_text'       => self::clean_hex( isset( $data['theme_text'] ) ? $data['theme_text'] : '', $profile->theme_text ),
			'button_style'     => ( isset( $data['button_style'] ) && in_array( $data['button_style'], array( 'brand', 'filled', 'outline', 'soft' ), true ) ) ? $data['button_style'] : $profile->button_style,
			'layout'           => ( isset( $data['layout'] ) && in_array( $data['layout'], array( 'list', 'grid' ), true ) ) ? $data['layout'] : $profile->layout,
			'is_published'     => isset( $data['is_published'] ) ? ( $data['is_published'] ? 1 : 0 ) : (int) $profile->is_published,
			'updated_at'       => self::now(),
		);

		// ---------- الروابط ----------
		if ( ! is_array( $links ) ) {
			$links = array();
		}
		if ( count( $links ) > self::MAX_LINKS ) {
			return new WP_Error( 'limit', 'الحد الأقصى ' . self::MAX_LINKS . ' رابط' );
		}
		$clean = array();
		foreach ( array_values( $links ) as $i => $l ) {
			$type  = isset( $l['type'] ) ? (string) $l['type'] : '';
			$value = isset( $l['value'] ) ? sanitize_text_field( (string) $l['value'] ) : '';
			$err   = self::validate_link( $type, $value );
			if ( $err ) {
				return new WP_Error( 'link', sprintf( 'الرابط رقم %d: %s', $i + 1, $err ) );
			}
			$label   = isset( $l['label'] ) ? mb_substr( sanitize_text_field( (string) $l['label'] ), 0, 60 ) : '';
			$clean[] = array(
				'id'        => ( isset( $l['id'] ) && is_numeric( $l['id'] ) ) ? (int) $l['id'] : 0,
				'client_id' => isset( $l['id'] ) ? (string) $l['id'] : '',
				'type'      => $type,
				'label'     => '' === $label ? null : $label,
				'value'     => trim( $value ),
				'is_active' => empty( $l['is_active'] ) ? 0 : 1,
				'position'  => $i,
			);
		}

		$pt = self::table( 'profiles' );
		$lt = self::table( 'links' );

		$wpdb->query( 'START TRANSACTION' );

		$updated = $wpdb->update( $pt, $fields, array( 'id' => $profile->id, 'owner_id' => $user_id ) );
		if ( false === $updated ) {
			$wpdb->query( 'ROLLBACK' );
			return new WP_Error( 'db', 'تعذر حفظ بيانات الصفحة' );
		}

		// الروابط الموجودة فعلاً لهذه الصفحة فقط — أي id غريب يُعامل كرابط جديد
		$owned = array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $lt WHERE profile_id = %d", $profile->id ) ) );
		$keep  = array();
		$idmap = array();

		foreach ( $clean as $l ) {
			$row = array(
				'type'       => $l['type'],
				'label'      => $l['label'],
				'value'      => $l['value'],
				'is_active'  => $l['is_active'],
				'position'   => $l['position'],
				'updated_at' => self::now(),
			);
			if ( $l['id'] && in_array( $l['id'], $owned, true ) ) {
				$wpdb->update( $lt, $row, array( 'id' => $l['id'], 'profile_id' => $profile->id ) );
				$keep[]                    = $l['id'];
				$idmap[ $l['client_id'] ] = $l['id'];
			} else {
				$row['profile_id'] = $profile->id;
				$row['created_at'] = self::now();
				$wpdb->insert( $lt, $row );
				$new                       = (int) $wpdb->insert_id;
				$keep[]                    = $new;
				$idmap[ $l['client_id'] ] = $new;
			}
		}

		$remove = array_diff( $owned, $keep );
		if ( $remove ) {
			$in = implode( ',', array_map( 'intval', $remove ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM $lt WHERE profile_id = %d AND id IN ($in)", $profile->id ) ); // phpcs:ignore -- ints only
		}

		if ( $wpdb->last_error ) {
			$wpdb->query( 'ROLLBACK' );
			return new WP_Error( 'db', 'تعذر حفظ الروابط' );
		}
		$wpdb->query( 'COMMIT' );

		$fresh = self::profile_by_owner( $user_id );
		self::purge( $profile, $fresh );

		return array(
			'profile' => $fresh,
			'links'   => self::links( $profile->id ),
			'idmap'   => $idmap,
		);
	}

	/** مسح كاش الصفحات العامة في إضافات الكاش الشائعة بعد الحفظ */
	public static function purge( $old, $new = null ) {
		$urls = array_values( array_unique( array_merge( array_values( self::profile_urls( $old ) ), $new ? array_values( self::profile_urls( $new ) ) : array() ) ) );
		foreach ( $urls as $u ) {
			do_action( 'litespeed_purge_url', $u );
			if ( function_exists( 'w3tc_flush_url' ) ) {
				w3tc_flush_url( $u );
			}
		}
		if ( function_exists( 'rocket_clean_files' ) ) {
			rocket_clean_files( $urls );
		}
		do_action( 'alzahby_profile_updated', $new ? $new : $old, $urls );
	}

	/* =================================================================
	 *  Stands
	 * ================================================================= */
	public static function stands( $profile_id ) {
		global $wpdb;
		$t = self::table( 'stands' );
		return $wpdb->get_results( $wpdb->prepare( "SELECT code, label, claimed_at, scan_count FROM $t WHERE profile_id = %d ORDER BY claimed_at DESC", $profile_id ) );
	}

	/** يُستدعى عند فتح /s/CODE — يزيد عداد المسح ويرجع حالة الاستاند */
	public static function resolve_stand( $code ) {
		global $wpdb;
		if ( ! preg_match( self::CODE_RE, $code ) ) {
			return null;
		}
		$t     = self::table( 'stands' );
		$stand = $wpdb->get_row( $wpdb->prepare( "SELECT id, code, profile_id FROM $t WHERE code = %s", $code ) );
		if ( ! $stand ) {
			return null;
		}
		$wpdb->query( $wpdb->prepare( "UPDATE $t SET scan_count = scan_count + 1 WHERE id = %d", $stand->id ) );
		$stand->profile = $stand->profile_id ? self::public_profile( 'id', $stand->profile_id ) : null;
		return $stand;
	}

	/** ربط استاند غير مربوط بصفحة المستخدم — عملية ذرّية (UPDATE ... WHERE profile_id IS NULL) */
	public static function claim_stand( $user_id, $code ) {
		global $wpdb;
		if ( ! preg_match( self::CODE_RE, $code ) ) {
			return new WP_Error( 'code', 'كود الاستاند غير صحيح' );
		}
		// منع تخمين الأكواد
		if ( ! self::rate_limit( 'claim_' . $user_id, 15, HOUR_IN_SECONDS ) ) {
			return new WP_Error( 'rate', 'محاولات كثيرة، حاول بعد ساعة' );
		}
		$profile = self::profile_by_owner( $user_id );
		if ( ! $profile ) {
			return new WP_Error( 'profile_required', 'أنشئ صفحتك أولاً ثم اربط الاستاند' );
		}
		$t       = self::table( 'stands' );
		$changed = $wpdb->query( $wpdb->prepare( "UPDATE $t SET profile_id = %d, claimed_at = %s WHERE code = %s AND profile_id IS NULL", $profile->id, self::now(), $code ) );
		if ( $changed ) {
			return true;
		}
		$owner = $wpdb->get_var( $wpdb->prepare( "SELECT profile_id FROM $t WHERE code = %s", $code ) );
		if ( (int) $owner === $profile->id ) {
			return true;
		}
		return new WP_Error( 'unavailable', 'هذا الاستاند مربوط بنشاط آخر أو الكود غير صحيح' );
	}

	public static function release_stand( $user_id, $code ) {
		global $wpdb;
		$profile = self::profile_by_owner( $user_id );
		if ( ! $profile || ! preg_match( self::CODE_RE, $code ) ) {
			return new WP_Error( 'code', 'كود غير صحيح' );
		}
		$t = self::table( 'stands' );
		$wpdb->query( $wpdb->prepare( "UPDATE $t SET profile_id = NULL, claimed_at = NULL, label = NULL WHERE code = %s AND profile_id = %d", $code, $profile->id ) );
		return true;
	}

	public static function random_code( $len = 10 ) {
		$out = '';
		$max = strlen( self::ALPHABET ) - 1;
		for ( $i = 0; $i < $len; $i++ ) {
			$out .= self::ALPHABET[ random_int( 0, $max ) ];
		}
		return $out;
	}

	/** توليد دفعة أكواد جديدة (للأدمن فقط — يتم التحقق في شاشة الإدارة) */
	public static function generate_stands( $count, $batch ) {
		global $wpdb;
		$t     = self::table( 'stands' );
		$codes = array();
		$count = max( 1, min( 1000, (int) $count ) );
		while ( count( $codes ) < $count ) {
			$code = self::random_code();
			$ok   = $wpdb->insert(
				$t,
				array(
					'code'       => $code,
					'batch'      => $batch ? $batch : null,
					'created_at' => self::now(),
				)
			);
			if ( $ok ) {
				$codes[] = $code;
			}
		}
		return $codes;
	}

	/* =================================================================
	 *  Tracking
	 * ================================================================= */
	public static function track( $kind, $id ) {
		global $wpdb;
		$id = (int) $id;
		if ( $id <= 0 ) {
			return;
		}
		if ( 'view' === $kind ) {
			$t = self::table( 'profiles' );
			$wpdb->query( $wpdb->prepare( "UPDATE $t SET view_count = view_count + 1 WHERE id = %d AND is_published = 1", $id ) );
		} elseif ( 'click' === $kind ) {
			$t = self::table( 'links' );
			$wpdb->query( $wpdb->prepare( "UPDATE $t SET click_count = click_count + 1 WHERE id = %d AND is_active = 1", $id ) );
		}
	}

	/* =================================================================
	 *  Rendering — نفس الـ markup يعيد بناءه renderer.js في المعاينة والديمو
	 * ================================================================= */
	public static function button_style( $profile, $def ) {
		$bg = isset( $def['gradient'] ) ? $def['gradient'] : $def['color'];
		switch ( $profile->button_style ) {
			case 'filled':
				return 'background:' . $profile->theme_primary . ';color:#fff';
			case 'outline':
				return 'background:transparent;color:' . $profile->theme_primary . ';border:2px solid ' . $profile->theme_primary;
			case 'soft':
				return 'background:' . $profile->theme_primary . '14;color:' . $profile->theme_primary;
			default:
				return 'background:' . $bg . ';color:' . $def['on'];
		}
	}

	public static function render_profile( $profile, $links ) {
		$defs     = self::link_defs();
		$initials = mb_substr( trim( $profile->business_name ), 0, 2 );
		$solid    = in_array( $profile->button_style, array( 'brand', 'filled' ), true );

		ob_start();
		echo '<div class="lp" style="--p:' . esc_attr( $profile->theme_primary ) . ';--bg:' . esc_attr( $profile->theme_background ) . ';--tx:' . esc_attr( $profile->theme_text ) . '">';
		echo '<div class="lp__in">';

		echo '<div class="lp__cover">';
		if ( $profile->cover_url ) {
			echo '<img src="' . esc_url( $profile->cover_url ) . '" alt="" fetchpriority="high">';
		}
		echo '</div>';

		echo '<div class="lp__head">';
		echo '<div class="lp__logo lp__logo--' . esc_attr( $profile->logo_shape ) . '">';
		if ( $profile->logo_url ) {
			echo '<img src="' . esc_url( $profile->logo_url ) . '" alt="' . esc_attr( $profile->business_name ) . '" width="96" height="96">';
		} else {
			echo esc_html( $initials );
		}
		echo '</div>';
		echo '<h1 class="lp__name">' . esc_html( $profile->business_name ) . '</h1>';
		if ( $profile->bio ) {
			echo '<p class="lp__bio">' . nl2br( esc_html( $profile->bio ) ) . '</p>';
		}
		echo '</div>';

		echo '<div class="lp__links lp__links--' . esc_attr( $profile->layout ) . '">';
		$count = 0;
		foreach ( $links as $l ) {
			if ( ! $l->is_active || ! isset( $defs[ $l->type ] ) ) {
				continue;
			}
			$def    = $defs[ $l->type ];
			$action = self::link_action( $l->type, $l->value );
			$label  = self::display_label( $l->type, $l->label );
			$wrap   = $solid ? 'background:rgba(255,255,255,.18)' : 'background:' . ( isset( $def['gradient'] ) ? $def['gradient'] : $def['color'] ) . ';color:' . $def['on'];
			$inner  = '<span class="lb__ic" style="' . esc_attr( $wrap ) . '">' . self::icon( $l->type ) . '</span><span class="lb__t">' . esc_html( $label ) . '</span>';

			if ( 'wifi' === $action ) {
				$w = self::wifi_parse( $l->value );
				if ( ! $w ) {
					continue;
				}
				echo '<button type="button" class="lb" data-id="' . (int) $l->id . '" data-wifi-ssid="' . esc_attr( $w['ssid'] ) . '" data-wifi-pass="' . esc_attr( $w['pass'] ) . '" style="' . esc_attr( self::button_style( $profile, $def ) ) . '">' . $inner . '<span class="lb__copy" dir="ltr">' . esc_html( $w['ssid'] ) . '</span></button>'; // phpcs:ignore
			} elseif ( 'copy' === $action ) {
				$copy = self::copy_value( $l->type, $l->value );
				echo '<button type="button" class="lb" data-id="' . (int) $l->id . '" data-copy="' . esc_attr( $copy ) . '" data-label="' . esc_attr( $def['label'] ) . '" style="' . esc_attr( self::button_style( $profile, $def ) ) . '">' . $inner . '<span class="lb__copy" dir="ltr">' . esc_html( $copy ) . self::icon( 'copy' ) . '</span></button>'; // phpcs:ignore
			} else {
				$href = self::build_href( $l->type, $l->value );
				if ( '' === $href ) {
					continue;
				}
				$ext = 0 === strpos( $href, 'http' );
				$pdf = 'menu' === $l->type && self::is_local_pdf( $href ) ? ' data-pdf="1"' : '';
				echo '<a class="lb" href="' . esc_url( $href, array( 'https', 'http', 'tel', 'mailto' ) ) . '" data-id="' . (int) $l->id . '" data-type="' . esc_attr( $l->type ) . '"' . $pdf . ( $ext ? ' target="_blank" rel="noopener noreferrer"' : '' ) . ' style="' . esc_attr( self::button_style( $profile, $def ) ) . '">' . $inner . '</a>'; // phpcs:ignore
			}
			$count++;
		}
		if ( ! $count ) {
			echo '<p class="lp__empty">لا توجد روابط بعد</p>';
		}
		echo '</div>';

		echo '</div>';
		echo '<a class="lp__brand" href="' . esc_url( home_url( '/' ) ) . '">صُنع بواسطة <b>' . esc_html( self::opt( 'brand_name', 'QurTap' ) ) . '</b></a>';
		echo '</div>';
		return ob_get_clean();
	}

	/** شعار الموقع: صورة من الإعدادات أو الشعار الافتراضي (أيقونة + الاسم) */
	public static function logo_html( $light = false ) {
		$img   = self::opt( 'logo_url' );
		$name  = self::opt( 'brand_name', 'QurTap' );
		$en    = self::opt( 'brand_name_en', 'SMART NFC STAND' );
		$html  = '<a class="brand' . ( $light ? ' brand--light' : '' ) . '" href="' . esc_url( home_url( '/' ) ) . '">';
		if ( $img ) {
			$html .= '<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $name ) . '" height="40">';
		} else {
			$html .= '<span class="brand__mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M8.5 16.5a6 6 0 0 1 0-9"/><path d="M12 19a9.5 9.5 0 0 1 0-14" opacity=".55"/><circle cx="15.5" cy="12" r="2.4" fill="currentColor" stroke="none"/></svg></span>';
			$html .= '<span class="brand__txt">' . esc_html( $name ) . '<small dir="ltr">' . esc_html( $en ) . '</small></span>';
		}
		return $html . '</a>';
	}

	/** خط Montserrat Arabic المرفق مع القالب */
	public static function font_css() {
		$base = get_template_directory_uri() . '/components/styles/Font/';
		$map  = array( 400 => 'Regular', 500 => 'Medium', 700 => 'Bold', 800 => 'ExtraBold', 900 => 'Black' );
		$css  = '';
		foreach ( $map as $w => $f ) {
			$css .= '@font-face{font-family:"MontserratArabic";font-style:normal;font-weight:' . $w . ';font-display:swap;src:url("' . esc_url( $base . 'Montserrat-Arabic-' . $f . '.ttf' ) . '") format("truetype")}';
		}
		return $css;
	}

	/** محتوى ملف أصول لطباعته inline (أسرع لصفحات الـ NFC) */
	public static function inline_asset( $rel ) {
		$path = get_template_directory() . '/components/assets/' . $rel;
		return file_exists( $path ) ? file_get_contents( $path ) : ''; // phpcs:ignore
	}

	public static function asset_url( $rel ) {
		$path = get_template_directory() . '/components/assets/' . $rel;
		return get_template_directory_uri() . '/components/assets/' . $rel . '?v=' . ( file_exists( $path ) ? filemtime( $path ) : ALZ_VERSION );
	}

	/** رأس HTML مختصر لصفحات المنصة (بدون wp_head لسرعة قصوى) */
	public static function head( $title, $extra = '' ) {
		echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">';
		echo '<title>' . esc_html( $title ) . '</title>';
		$fav = get_site_icon_url( 64 );
		if ( $fav ) {
			echo '<link rel="icon" href="' . esc_url( $fav ) . '">';
		}
		echo $extra; // phpcs:ignore -- built by caller with escaping
		echo '<style>' . self::font_css() . 'html,body{margin:0;padding:0}body{font-family:"MontserratArabic",system-ui,-apple-system,"Segoe UI",Tahoma,sans-serif}</style>'; // phpcs:ignore
	}

	/** بيانات مشتركة للجافاسكربت (المحرر + الديمو) */
	public static function js_config() {
		$icons = self::icons();
		return array(
			'defs'   => self::link_defs(),
			'groups' => self::link_groups(),
			'icons'  => $icons,
			'brand'  => self::opt( 'brand_name', 'QurTap' ),
		);
	}
}
