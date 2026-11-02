<?php
/**
 * REST API: /wp-json/alzahby/v1/...
 * كل نقاط الكتابة تتطلب: مستخدم مسجّل + X-WP-Nonce (wp_rest) — والملف المعدَّل دائماً هو ملف المستخدم الحالي.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once dirname( __DIR__ ) . '/AlzCore/core.php';

final class AlzApi {
	const NS = 'alzahby/v1';

	public static function boot() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function logged_in() {
		return is_user_logged_in();
	}

	public static function routes() {
		$auth = array( __CLASS__, 'logged_in' );

		register_rest_route( self::NS, '/profile', array(
			array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'create' ), 'permission_callback' => $auth ),
			array( 'methods' => 'PUT', 'callback' => array( __CLASS__, 'save' ), 'permission_callback' => $auth ),
		) );
		// روابط استاندات إنستاباي / فودافون كاش / تقييم جوجل
		register_rest_route( self::NS, '/targets', array(
			'methods'             => 'PUT',
			'callback'            => array( __CLASS__, 'targets' ),
			'permission_callback' => $auth,
		) );
		register_rest_route( self::NS, '/slug', array(
			'methods'             => 'GET',
			'callback'            => array( __CLASS__, 'slug' ),
			'permission_callback' => $auth,
		) );
		register_rest_route( self::NS, '/upload', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'upload' ),
			'permission_callback' => $auth,
		) );
		register_rest_route( self::NS, '/stands/(?P<action>claim|release)', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'stand' ),
			'permission_callback' => $auth,
		) );
		// عدادات عامة — بدون تسجيل دخول
		register_rest_route( self::NS, '/track', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'track' ),
			'permission_callback' => '__return_true',
		) );
	}

	private static function error( $msg, $status = 400 ) {
		return new WP_REST_Response( array( 'error' => $msg ), $status );
	}

	public static function export_profile( $p ) {
		if ( ! $p ) {
			return null;
		}
		return array(
			'id'               => $p->id,
			'slug'             => $p->slug,
			'business_name'    => $p->business_name,
			'category'         => $p->category,
			'bio'              => (string) $p->bio,
			'logo_url'         => $p->logo_url,
			'cover_url'        => $p->cover_url,
			'logo_shape'       => $p->logo_shape,
			'theme_primary'    => $p->theme_primary,
			'theme_background' => $p->theme_background,
			'theme_text'       => $p->theme_text,
			'button_style'     => $p->button_style,
			'layout'           => $p->layout,
			'is_published'     => (bool) $p->is_published,
			'view_count'       => (int) $p->view_count,
		);
	}

	public static function export_links( $links ) {
		return array_map(
			function ( $l ) {
				return array(
					'id'          => (string) $l->id,
					'type'        => $l->type,
					'label'       => $l->label,
					'value'       => $l->value,
					'is_active'   => (bool) $l->is_active,
					'click_count' => (int) $l->click_count,
				);
			},
			$links
		);
	}

	public static function create( WP_REST_Request $req ) {
		$uid = get_current_user_id();
		$p   = Alz::create_profile( $uid, (string) $req->get_param( 'business_name' ), (string) $req->get_param( 'slug' ), (string) $req->get_param( 'category' ) );
		if ( is_wp_error( $p ) ) {
			return self::error( $p->get_error_message() );
		}
		$out   = array( 'profile' => self::export_profile( $p ) );
		$stand = (string) $req->get_param( 'stand' );
		if ( $stand ) {
			$res              = Alz::claim_stand( $uid, $stand );
			$out['stand']     = true === $res ? 'claimed' : $res->get_error_message();
			$out['stand_kind'] = true === $res ? Alz::stand_kind_of( $stand ) : '';
		}
		return new WP_REST_Response( $out, 201 );
	}

	public static function save( WP_REST_Request $req ) {
		$body = $req->get_json_params();
		$data = isset( $body['profile'] ) && is_array( $body['profile'] ) ? $body['profile'] : array();
		$res  = Alz::save( get_current_user_id(), $data, isset( $body['links'] ) ? $body['links'] : array() );
		if ( is_wp_error( $res ) ) {
			return self::error( $res->get_error_message() );
		}
		return array(
			'profile' => self::export_profile( $res['profile'] ),
			'links'   => self::export_links( $res['links'] ),
			'idmap'   => $res['idmap'],
		);
	}

	public static function targets( WP_REST_Request $req ) {
		$uid = get_current_user_id();
		if ( ! Alz::rate_limit( 'targets_' . $uid, 30, HOUR_IN_SECONDS ) ) {
			return self::error( 'محاولات كثيرة، حاول لاحقاً', 429 );
		}
		$res = Alz::save_targets( $uid, $req->get_json_params() );
		if ( is_wp_error( $res ) ) {
			return self::error( $res->get_error_message() );
		}
		return array(
			'targets' => Alz::targets( $res['profile'] ),
			'notice'  => $res['notice'],
		);
	}

	public static function slug( WP_REST_Request $req ) {
		$p   = Alz::profile_by_owner( get_current_user_id() );
		$err = Alz::check_slug( strtolower( (string) $req->get_param( 'slug' ) ), $p ? $p->id : 0 );
		return array( 'available' => '' === $err, 'message' => $err );
	}

	/** رفع اللوجو / الغلاف: صور فقط، حتى 2MB، تُحفظ كمرفق باسم المستخدم */
	public static function upload( WP_REST_Request $req ) {
		$uid = get_current_user_id();
		if ( ! Alz::profile_by_owner( $uid ) ) {
			return self::error( 'أنشئ صفحتك أولاً', 403 );
		}
		if ( ! Alz::rate_limit( 'upload_' . $uid, 30, HOUR_IN_SECONDS ) ) {
			return self::error( 'عدد كبير من الصور، حاول لاحقاً', 429 );
		}
		$files = $req->get_file_params();
		if ( empty( $files['file'] ) || ! empty( $files['file']['error'] ) ) {
			return self::error( 'لم يتم استلام الصورة' );
		}
		$file = $files['file'];

		// منيو PDF: حتى 10MB، ويجب أن يبدأ الملف بتوقيع %PDF-
		if ( 'pdf' === $req->get_param( 'kind' ) ) {
			if ( $file['size'] > 10 * MB_IN_BYTES ) {
				return self::error( 'الحد الأقصى لملف المنيو 10 ميجا' );
			}
			$pdf   = array( 'pdf' => 'application/pdf' );
			$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $pdf );
			$fh    = fopen( $file['tmp_name'], 'rb' ); // phpcs:ignore
			$magic = $fh ? fread( $fh, 5 ) : ''; // phpcs:ignore
			if ( $fh ) {
				fclose( $fh ); // phpcs:ignore
			}
			if ( 'application/pdf' !== $check['type'] || '%PDF-' !== $magic ) {
				return self::error( 'الملف ليس PDF صالح' );
			}
			$file['name'] = 'menu-' . $uid . '-' . time() . '.pdf';
			return self::store_upload( $file, $uid, $pdf, false );
		}

		$allowed = array(
			'jpg|jpeg' => 'image/jpeg',
			'png'      => 'image/png',
			'webp'     => 'image/webp',
		);
		if ( $file['size'] > 2 * MB_IN_BYTES ) {
			return self::error( 'الحد الأقصى لحجم الصورة 2 ميجا' );
		}
		$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed );
		if ( empty( $check['type'] ) || ! in_array( $check['type'], $allowed, true ) || ! @getimagesize( $file['tmp_name'] ) ) { // phpcs:ignore
			return self::error( 'الصيغ المسموحة: JPG / PNG / WEBP' );
		}
		$kind         = 'cover' === $req->get_param( 'kind' ) ? 'cover' : 'logo';
		$file['name'] = $kind . '-' . $uid . '-' . time() . '.' . $check['ext'];
		return self::store_upload( $file, $uid, $allowed, 'logo' === $kind ? 'alz_logo' : 'large' );
	}

	/** حفظ الملف كمرفق في مكتبة الوسائط باسم المستخدم */
	private static function store_upload( $file, $uid, $mimes, $size ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$_FILES['alz_file'] = $file;
		$id                 = media_handle_upload( 'alz_file', 0, array( 'post_author' => $uid ), array( 'test_form' => false, 'mimes' => $mimes ) );
		if ( is_wp_error( $id ) ) {
			return self::error( 'تعذر رفع الملف' );
		}
		update_post_meta( $id, '_alz_owner', $uid );
		$url = $size ? wp_get_attachment_image_url( $id, $size ) : '';
		return array( 'url' => $url ? $url : wp_get_attachment_url( $id ) );
	}

	public static function stand( WP_REST_Request $req ) {
		$uid  = get_current_user_id();
		$code = trim( (string) $req->get_param( 'code' ) );
		$kind = (string) $req->get_param( 'kind' );
		$kind = ( '' !== $kind && Alz::is_stand_kind( $kind ) ) ? $kind : '';
		$res  = 'claim' === $req['action'] ? Alz::claim_stand( $uid, $code, $kind ) : Alz::release_stand( $uid, $code );
		if ( is_wp_error( $res ) ) {
			return self::error( $res->get_error_message() );
		}
		$p = Alz::profile_by_owner( $uid );
		return array(
			'stands' => $p ? Alz::stands( $p->id ) : array(),
			'kind'   => 'claim' === $req['action'] ? Alz::stand_kind_of( $code ) : '',
		);
	}

	public static function track( WP_REST_Request $req ) {
		$kind = (string) $req->get_param( 'kind' );
		$id   = (int) $req->get_param( 'id' );
		if ( in_array( $kind, array( 'view', 'click' ), true ) && $id > 0
			&& Alz::rate_limit( 'tr_' . $kind . '_' . $id . '_' . Alz::client_ip(), 'view' === $kind ? 1 : 5, 30 * MINUTE_IN_SECONDS ) ) {
			Alz::track( $kind, $id );
		}
		return new WP_REST_Response( null, 204 );
	}
}

AlzApi::boot();

// حجم صورة خاص باللوجو
add_action(
	'after_setup_theme',
	function () {
		add_image_size( 'alz_logo', 512, 512, true );
	}
);
