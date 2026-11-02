<?php
/**
 * تسجيل الدخول المجاني للعملاء:
 *  - Google OAuth 2.0 (بدون إضافات — يحتاج Client ID / Secret من إعدادات القالب)
 *  - Magic Link: رابط دخول لمرة واحدة صالح 15 دقيقة (الحساب يُنشأ فقط بعد الضغط على الرابط)
 *  - Email + Password
 * العملاء لا يدخلون wp-admin نهائياً.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once dirname( __DIR__ ) . '/AlzCore/core.php';

final class AlzAuth {

	const MAGIC_TTL = 900;

	public static function boot() {
		add_filter( 'alz_route_data_login', array( __CLASS__, 'route' ) );
		add_action( 'admin_init', array( __CLASS__, 'block_admin' ) );
		add_filter( 'login_redirect', array( __CLASS__, 'login_redirect' ), 10, 3 );
	}

	/** منع العملاء من لوحة ووردبريس */
	public static function block_admin() {
		if ( wp_doing_ajax() || ! is_user_logged_in() ) {
			return;
		}
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_safe_redirect( Alz::dashboard_url() );
			exit;
		}
	}

	public static function login_redirect( $redirect, $requested, $user ) {
		if ( $user instanceof WP_User && ! user_can( $user, 'edit_posts' ) ) {
			return Alz::dashboard_url();
		}
		return $redirect;
	}

	public static function google_enabled() {
		return '' !== Alz::opt( 'google_client_id' ) && '' !== Alz::opt( 'google_client_secret' );
	}

	public static function google_redirect_uri() {
		return home_url( '/dashboard/login/google-callback/' );
	}

	/* -------------------------------------------------------------- */

	public static function route() {
		nocache_headers();
		$action = (string) get_query_var( 'alz_action' );
		$next   = isset( $_REQUEST['next'] ) ? Alz::safe_next( $_REQUEST['next'] ) : Alz::dashboard_url(); // phpcs:ignore

		switch ( $action ) {
			case 'logout':
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'alz_logout' ) ) {
					wp_logout();
				}
				wp_safe_redirect( Alz::login_url() );
				exit;
			case 'magic':
				return self::consume_magic();
			case 'google':
				self::google_start( $next );
				exit;
			case 'google-callback':
				return self::google_callback();
		}

		if ( is_user_logged_in() ) {
			wp_safe_redirect( $next );
			exit;
		}

		$state = array(
			'next'    => $next,
			'error'   => isset( $_GET['error'] ) ? sanitize_text_field( wp_unslash( $_GET['error'] ) ) : '',
			'notice'  => '',
			'mode'    => 'magic',
			'email'   => '',
			'google'  => self::google_enabled(),
			'signup'  => '1' === (string) Alz::opt( 'allow_signup', '1' ),
		);

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['alz_login'] ) ) { // phpcs:ignore
			$state = self::handle_post( $state );
		}
		return $state;
	}

	private static function handle_post( $state ) {
		if ( ! isset( $_POST['_alznonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_alznonce'] ) ), 'alz_login' ) ) {
			$state['error'] = 'انتهت صلاحية الصفحة، أعد المحاولة';
			return $state;
		}
		// Honeypot ضد البوتات
		if ( ! empty( $_POST['website'] ) ) {
			$state['error'] = 'تعذر المتابعة';
			return $state;
		}
		$mode           = isset( $_POST['mode'] ) ? sanitize_key( $_POST['mode'] ) : 'magic';
		$email          = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$state['mode']  = $mode;
		$state['email'] = $email;

		if ( ! is_email( $email ) ) {
			$state['error'] = 'أدخل بريداً إلكترونياً صحيحاً';
			return $state;
		}
		if ( ! Alz::rate_limit( 'login_ip_' . Alz::client_ip(), 20, 15 * MINUTE_IN_SECONDS ) ) {
			$state['error'] = 'محاولات كثيرة، حاول بعد قليل';
			return $state;
		}

		if ( 'magic' === $mode ) {
			$sent = self::send_magic( $email, $state['next'], $state['signup'] );
			if ( is_wp_error( $sent ) ) {
				$state['error'] = $sent->get_error_message();
			} else {
				$state['notice'] = 'sent';
			}
			return $state;
		}

		$password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore -- password is not sanitized

		if ( 'signup' === $mode ) {
			if ( ! $state['signup'] ) {
				$state['error'] = 'التسجيل الجديد متوقف حالياً';
				return $state;
			}
			if ( strlen( $password ) < 8 ) {
				$state['error'] = 'كلمة المرور 8 أحرف على الأقل';
				return $state;
			}
			if ( email_exists( $email ) ) {
				$state['error'] = 'هذا البريد مسجل بالفعل — سجّل الدخول';
				return $state;
			}
			$uid = self::create_user( $email, $password );
			if ( is_wp_error( $uid ) ) {
				$state['error'] = $uid->get_error_message();
				return $state;
			}
			self::login_user( $uid, $state['next'] );
		}

		// تسجيل دخول بكلمة المرور
		$user = get_user_by( 'email', $email );
		$res  = wp_signon(
			array(
				'user_login'    => $user ? $user->user_login : $email,
				'user_password' => $password,
				'remember'      => true,
			),
			is_ssl()
		);
		if ( is_wp_error( $res ) ) {
			$state['error'] = 'البريد أو كلمة المرور غير صحيحة';
			return $state;
		}
		wp_safe_redirect( $state['next'] );
		exit;
	}

	/* ---------------------- Users ---------------------- */

	private static function create_user( $email, $password = '', $name = '' ) {
		$base  = sanitize_user( strstr( $email, '@', true ), true );
		$base  = $base ? $base : 'client';
		$login = $base;
		$i     = 1;
		while ( username_exists( $login ) ) {
			$login = $base . ++$i;
		}
		$uid = wp_insert_user(
			array(
				'user_login'   => $login,
				'user_email'   => $email,
				'user_pass'    => $password ? $password : wp_generate_password( 32, true, true ),
				'display_name' => $name ? $name : $base,
				'role'         => Alz::ROLE,
			)
		);
		return $uid;
	}

	/** الدخول بدون كلمة مرور (Magic / Google) غير مسموح لحسابات الإدارة — يستخدمون wp-login فقط */
	private static function passwordless_login( $uid, $next ) {
		if ( user_can( $uid, 'edit_posts' ) ) {
			wp_safe_redirect( add_query_arg( 'error', rawurlencode( 'حسابات الإدارة تدخل من صفحة wp-login فقط' ), Alz::login_url() ) );
			exit;
		}
		self::login_user( $uid, $next );
	}

	private static function login_user( $uid, $next ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $uid );
		wp_set_auth_cookie( $uid, true, is_ssl() );
		do_action( 'wp_login', get_userdata( $uid )->user_login, get_userdata( $uid ) );
		wp_safe_redirect( $next );
		exit;
	}

	/* ---------------------- Magic link ---------------------- */

	private static function token_hash( $token ) {
		return hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
	}

	private static function send_magic( $email, $next, $allow_signup ) {
		if ( ! Alz::rate_limit( 'magic_' . strtolower( $email ), 3, 10 * MINUTE_IN_SECONDS ) ) {
			return new WP_Error( 'rate', 'تم إرسال رابط بالفعل، انتظر بضع دقائق' );
		}
		$user = get_user_by( 'email', $email );
		if ( ! $user && ! $allow_signup ) {
			// نفس الرد حتى لا نكشف هل البريد مسجل أم لا
			return true;
		}
		$token = bin2hex( random_bytes( 32 ) );
		set_transient(
			'alz_magic_' . self::token_hash( $token ),
			array(
				'email' => $email,
				'next'  => $next,
			),
			self::MAGIC_TTL
		);
		$link  = add_query_arg( 't', $token, home_url( '/dashboard/login/magic/' ) );
		$brand = Alz::opt( 'brand_name', 'QurTap' );
		$body  = '<div dir="rtl" style="font-family:Tahoma,Arial;font-size:15px;line-height:1.8">'
			. '<h2>رابط الدخول إلى ' . esc_html( $brand ) . '</h2>'
			. '<p>اضغط الزر لتسجيل الدخول إلى لوحة التحكم. الرابط صالح لمدة 15 دقيقة ولمرة واحدة فقط.</p>'
			. '<p><a href="' . esc_url( $link ) . '" style="display:inline-block;background:#0b1020;color:#fff;padding:12px 22px;border-radius:10px;text-decoration:none">تسجيل الدخول</a></p>'
			. '<p style="color:#888;font-size:12px">إذا لم تطلب هذا الرابط تجاهل الرسالة.</p></div>';
		$ok    = wp_mail( $email, 'رابط تسجيل الدخول — ' . $brand, $body, array( 'Content-Type: text/html; charset=UTF-8' ) );
		return $ok ? true : new WP_Error( 'mail', 'تعذر إرسال البريد — تأكد من إعدادات SMTP في الموقع' );
	}

	private static function consume_magic() {
		$token = isset( $_GET['t'] ) ? sanitize_text_field( wp_unslash( $_GET['t'] ) ) : '';
		if ( ! preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
			wp_safe_redirect( add_query_arg( 'error', rawurlencode( 'رابط الدخول غير صالح' ), Alz::login_url() ) );
			exit;
		}
		$key  = 'alz_magic_' . self::token_hash( $token );
		$data = get_transient( $key );
		delete_transient( $key ); // استخدام لمرة واحدة
		if ( ! $data || empty( $data['email'] ) ) {
			wp_safe_redirect( add_query_arg( 'error', rawurlencode( 'انتهت صلاحية رابط الدخول، اطلب رابطاً جديداً' ), Alz::login_url() ) );
			exit;
		}
		$user = get_user_by( 'email', $data['email'] );
		$uid  = $user ? $user->ID : self::create_user( $data['email'] );
		if ( is_wp_error( $uid ) ) {
			wp_safe_redirect( add_query_arg( 'error', rawurlencode( 'تعذر إنشاء الحساب' ), Alz::login_url() ) );
			exit;
		}
		self::passwordless_login( $uid, Alz::safe_next( $data['next'] ) );
	}

	/* ---------------------- Google OAuth ---------------------- */

	private static function google_start( $next ) {
		if ( ! self::google_enabled() ) {
			wp_safe_redirect( add_query_arg( 'error', rawurlencode( 'تسجيل الدخول بجوجل غير مفعّل' ), Alz::login_url() ) );
			exit;
		}
		$state = bin2hex( random_bytes( 16 ) );
		set_transient( 'alz_gstate_' . $state, array( 'next' => $next ), 10 * MINUTE_IN_SECONDS );
		// ربط الـ state بالمتصفح نفسه (منع Login CSRF)
		setcookie( 'alz_gstate', $state, time() + 600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );

		$url = add_query_arg(
			array(
				'client_id'     => rawurlencode( Alz::opt( 'google_client_id' ) ),
				'redirect_uri'  => rawurlencode( self::google_redirect_uri() ),
				'response_type' => 'code',
				'scope'         => rawurlencode( 'openid email profile' ),
				'state'         => $state,
				'prompt'        => 'select_account',
			),
			'https://accounts.google.com/o/oauth2/v2/auth'
		);
		wp_redirect( $url ); // phpcs:ignore -- external Google URL
		exit;
	}

	private static function google_fail( $msg ) {
		wp_safe_redirect( add_query_arg( 'error', rawurlencode( $msg ), Alz::login_url() ) );
		exit;
	}

	private static function google_callback() {
		$state  = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';
		$code   = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
		$cookie = isset( $_COOKIE['alz_gstate'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['alz_gstate'] ) ) : '';
		$saved  = $state ? get_transient( 'alz_gstate_' . $state ) : false;
		delete_transient( 'alz_gstate_' . $state );
		setcookie( 'alz_gstate', '', time() - 3600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );

		if ( ! $code || ! $saved || ! hash_equals( $state, $cookie ) ) {
			self::google_fail( 'تعذر التحقق من جلسة جوجل، حاول مرة أخرى' );
		}

		$res = wp_remote_post(
			'https://oauth2.googleapis.com/token',
			array(
				'timeout' => 15,
				'body'    => array(
					'code'          => $code,
					'client_id'     => Alz::opt( 'google_client_id' ),
					'client_secret' => Alz::opt( 'google_client_secret' ),
					'redirect_uri'  => self::google_redirect_uri(),
					'grant_type'    => 'authorization_code',
				),
			)
		);
		$tok = is_wp_error( $res ) ? null : json_decode( wp_remote_retrieve_body( $res ), true );
		if ( empty( $tok['access_token'] ) ) {
			self::google_fail( 'تعذر تسجيل الدخول بجوجل' );
		}

		$info = wp_remote_get(
			'https://openidconnect.googleapis.com/v1/userinfo',
			array(
				'timeout' => 15,
				'headers' => array( 'Authorization' => 'Bearer ' . $tok['access_token'] ),
			)
		);
		$me = is_wp_error( $info ) ? null : json_decode( wp_remote_retrieve_body( $info ), true );
		if ( empty( $me['email'] ) || empty( $me['email_verified'] ) || empty( $me['sub'] ) ) {
			self::google_fail( 'حساب جوجل بدون بريد مؤكد' );
		}

		$email = sanitize_email( $me['email'] );
		$user  = get_user_by( 'email', $email );
		if ( $user ) {
			$uid = $user->ID;
		} else {
			if ( '1' !== (string) Alz::opt( 'allow_signup', '1' ) ) {
				self::google_fail( 'التسجيل الجديد متوقف حالياً' );
			}
			$uid = self::create_user( $email, '', isset( $me['name'] ) ? sanitize_text_field( $me['name'] ) : '' );
			if ( is_wp_error( $uid ) ) {
				self::google_fail( 'تعذر إنشاء الحساب' );
			}
		}
		update_user_meta( $uid, 'alz_google_sub', sanitize_text_field( $me['sub'] ) );
		self::passwordless_login( $uid, Alz::safe_next( $saved['next'] ) );
	}
}

AlzAuth::boot();
