<?php
/**
 * تسجيل نقرة اتصال / واتساب قادمة من واجهة الموقع.
 * يستقبل: page (عنوان الصفحة) , call_type (call|whatsapp) , page_url (رابط الصفحة)
 */
header("Content-Type: application/json; charset=utf-8");

$json = array();

$page_name = isset($_POST['page']) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
$call_type = isset($_POST['call_type']) ? sanitize_key( wp_unslash( $_POST['call_type'] ) ) : '';
$page_url  = isset($_POST['page_url']) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';

if ( ! in_array( $call_type, array( 'call', 'whatsapp' ), true ) ) {
    $call_type = '';
}
// نقبل فقط الروابط التابعة لنفس الموقع
if ( $page_url !== '' && strpos( $page_url, untrailingslashit( home_url() ) ) !== 0 ) {
    $page_url = '';
}

$calldate = current_time( 'mysql' );

$new_post = array(
    'post_title'   => '--',
    'post_type'    => 'callwebsite',
    'post_status'  => 'publish',
);

$post_id = wp_insert_post( $new_post );

if ( $post_id && ! is_wp_error( $post_id ) ) {
    update_post_meta( $post_id, 'calldate', $calldate );
    update_post_meta( $post_id, 'page', $page_name );
    if ( $call_type !== '' ) {
        update_post_meta( $post_id, 'call_type', $call_type );
    }
    if ( $page_url !== '' ) {
        update_post_meta( $post_id, 'page_url', $page_url );
    }
    $json['output']    = 'Success';
    $json['calldate']  = $calldate;
    $json['page']      = $page_name;
    $json['call_type'] = $call_type;
} else {
    $json['output'] = 'Error';
}

echo wp_json_encode( $json );
