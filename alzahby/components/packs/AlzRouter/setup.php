<?php
/**
 * المسارات:
 *   /s/{code}                 الرابط المطبوع على الاستاند (QR + NFC) — ثابت للأبد
 *   /u/{id}                   الرابط الدائم لصفحة النشاط
 *   /{slug}                   الرابط المختصر لصفحة النشاط
 *   /dashboard                لوحة تحكم العميل
 *   /dashboard/login[/...]    تسجيل الدخول (Google / Magic link / Password)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once dirname( __DIR__ ) . '/AlzCore/core.php';

add_filter(
	'query_vars',
	function ( $vars ) {
		return array_merge( $vars, array( 'alz_route', 'alz_code', 'alz_pid', 'alz_slug', 'alz_action' ) );
	}
);

add_action(
	'init',
	function () {
		add_rewrite_rule( '^s/([A-Za-z0-9]{6,16})/?$', 'index.php?alz_route=stand&alz_code=$matches[1]', 'top' );
		add_rewrite_rule( '^u/([0-9]+)/?$', 'index.php?alz_route=profile&alz_pid=$matches[1]', 'top' );
		add_rewrite_rule( '^dashboard/login/?$', 'index.php?alz_route=login', 'top' );
		add_rewrite_rule( '^dashboard/login/(magic|google|google-callback)/?$', 'index.php?alz_route=login&alz_action=$matches[1]', 'top' );
		add_rewrite_rule( '^dashboard/logout/?$', 'index.php?alz_route=login&alz_action=logout', 'top' );
		add_rewrite_rule( '^dashboard/?$', 'index.php?alz_route=dashboard', 'top' );

		if ( get_option( 'alz_flush_rewrite' ) ) {
			delete_option( 'alz_flush_rewrite' );
			flush_rewrite_rules( false );
		}
	},
	20
);

/**
 * /{slug} — بعد أن يحلل ووردبريس الطلب: إذا كان المسار جزءاً واحداً يطابق صفحة نشاط منشورة
 * ولا توجد صفحة/مقال بنفس الاسم، نوجّهه لصفحة النشاط.
 */
add_action(
	'parse_request',
	function ( $wp ) {
		if ( ! empty( $wp->query_vars['alz_route'] ) ) {
			return;
		}
		$path = trim( (string) $wp->request, '/' );
		if ( '' === $path || false !== strpos( $path, '/' ) || ! preg_match( Alz::SLUG_RE, $path ) ) {
			return;
		}
		$profile = Alz::public_profile( 'slug', $path );
		if ( ! $profile || get_page_by_path( $path, OBJECT, array( 'page', 'post' ) ) ) {
			return;
		}
		$wp->query_vars = array(
			'alz_route' => 'profile',
			'alz_slug'  => $path,
		);
	}
);

// منع ووردبريس من "تخمين" وإعادة توجيه مساراتنا
add_filter(
	'redirect_canonical',
	function ( $redirect ) {
		return get_query_var( 'alz_route' ) ? false : $redirect;
	}
);

// مساراتنا ليست 404
add_filter(
	'pre_handle_404',
	function ( $handled ) {
		return get_query_var( 'alz_route' ) ? true : $handled;
	}
);

/* -------------------------------------------------------------------
 *  تجهيز بيانات كل مسار (تُمرر للـ Blade كمتغيرات)
 * ------------------------------------------------------------------- */

add_filter(
	'alz_route_data_profile',
	function () {
		$slug    = get_query_var( 'alz_slug' );
		$profile = $slug ? Alz::public_profile( 'slug', $slug ) : Alz::public_profile( 'id', (int) get_query_var( 'alz_pid' ) );
		if ( ! $profile ) {
			return false;
		}
		// كاش قصير على الـ CDN — الحفظ من الداشبورد يمسح الكاش فوراً
		header( 'Cache-Control: public, max-age=0, s-maxage=30' );
		return array(
			'profile' => $profile,
			'links'   => Alz::links( $profile->id, true ),
		);
	}
);

add_filter(
	'alz_route_data_stand',
	function () {
		nocache_headers();
		$code  = (string) get_query_var( 'alz_code' );
		$stand = Alz::resolve_stand( $code );
		if ( ! $stand ) {
			return false;
		}
		if ( $stand->profile ) {
			wp_safe_redirect( home_url( '/' . $stand->profile->slug . '/' ), 302 );
			exit;
		}

		$user_id = get_current_user_id();
		$error   = '';
		$profile = $user_id ? Alz::profile_by_owner( $user_id ) : null;

		// ربط الاستاند (POST + nonce)
		if ( $user_id && isset( $_POST['alz_claim'] ) ) {
			if ( ! isset( $_POST['_alznonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_alznonce'] ) ), 'alz_claim_' . $code ) ) {
				$error = 'انتهت صلاحية الصفحة، أعد المحاولة';
			} else {
				$res = Alz::claim_stand( $user_id, $code );
				if ( true === $res ) {
					wp_safe_redirect( add_query_arg( array( 'tab' => 'share', 'claimed' => 1 ), Alz::dashboard_url() ) );
					exit;
				}
				$error = $res->get_error_message();
			}
		}

		return array(
			'code'        => $code,
			'user_id'     => $user_id,
			'has_profile' => (bool) $profile,
			'error'       => $error,
		);
	}
);

add_filter(
	'alz_route_data_dashboard',
	function () {
		nocache_headers();
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_safe_redirect( Alz::login_url( '/dashboard/' ) );
			exit;
		}
		$profile = Alz::profile_by_owner( $user_id );
		$stand   = isset( $_GET['stand'] ) ? sanitize_text_field( wp_unslash( $_GET['stand'] ) ) : '';
		return array(
			'user'    => wp_get_current_user(),
			'profile' => $profile,
			'links'   => $profile ? Alz::links( $profile->id ) : array(),
			'stands'  => $profile ? Alz::stands( $profile->id ) : array(),
			'stand'   => preg_match( Alz::CODE_RE, $stand ) ? $stand : '',
		);
	}
);
