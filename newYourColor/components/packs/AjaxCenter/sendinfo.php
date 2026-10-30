<?php
/**
 * استقبال طلبات الخدمة من أي نموذج .form-contact في الموقع.
 * يحفظها كنوع المحتوى clint (لوحة «طلبات العملاء») بنفس الحقول السابقة،
 * ويقبل اختياريًا: الخدمة والمدينة من صفحة «طلب الخدمة».
 */
header( 'Content-Type: application/json; charset=utf-8' );
ob_start();

$json = array();

$val = function ( $key ) {
	return isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
};

$name    = sanitize_text_field( $val( 'names' ) );
$email   = sanitize_email( $val( 'email' ) );
$phone   = preg_replace( '/[^0-9+]/', '', sanitize_text_field( $val( 'phone' ) ) );
$address = sanitize_text_field( $val( 'address' ) );
$message = sanitize_textarea_field( $val( 'message' ) );
$service = sanitize_text_field( $val( 'service' ) );
$city    = sanitize_text_field( $val( 'city' ) );

// الاسم مع وسيلة تواصل واحدة على الأقل (نموذج «اتصل بنا» الجوال فيه اختياري)
$ok = ( '' !== $name && ( strlen( $phone ) >= 8 || is_email( $email ) ) );

if ( $ok ) {
	// الخدمة والمدينة والعنوان في أول الرسالة حتى تظهر في لوحة «طلبات العملاء» كما هي
	$lines = array();
	if ( '' !== $service ) {
		$lines[] = 'الخدمة: ' . $service;
	}
	if ( '' !== $city ) {
		$lines[] = 'المدينة: ' . $city;
	}
	if ( '' !== $address ) {
		$lines[] = 'العنوان: ' . $address;
	}
	if ( $lines ) {
		$message = implode( "\n", $lines ) . ( '' !== $message ? "\n\n" . $message : '' );
	}

	$post_id = wp_insert_post( array(
		'post_title'  => $name,
		'post_content' => '',
		'post_status' => 'publish',
		'post_type'   => 'clint',
	) );

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, 'email', $email );
		update_post_meta( $post_id, 'phone', $phone );
		update_post_meta( $post_id, 'message', $message );
		update_post_meta( $post_id, 'address', $address );
		if ( '' !== $service ) {
			update_post_meta( $post_id, 'service', $service );
		}
		if ( '' !== $city ) {
			update_post_meta( $post_id, 'city', $city );
		}
	} else {
		$ok = false;
	}
}

echo '<div class="success' . ( $ok ? '' : ' success--error' ) . '" data-close="true">';
	echo '<span data-closeclick="true" id="Close" class="hoverable activable"><i class="fal fa-times"></i></span>';
	echo '<p>' . ( $ok ? 'تم اضافة طلبك بنجاح ' : 'يرجى كتابة الاسم ورقم جوال صحيح ثم المحاولة مرة أخرى.' ) . '</p>';
echo '</div>';

$json['output'] = ob_get_clean();
$json['ok']     = $ok;
echo wp_json_encode( $json );
