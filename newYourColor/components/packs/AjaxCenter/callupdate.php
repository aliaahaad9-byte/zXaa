<?php
/**
 * نقطة تسجيل نقرات الاتصال (اتصال مباشر / واتساب) من واجهة الموقع.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

header( 'Content-Type: application/json; charset=utf-8' );

$page_name = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
$page_url  = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
$calltype  = isset( $_POST['calltype'] ) ? sanitize_key( wp_unslash( $_POST['calltype'] ) ) : '';

if ( ! in_array( $calltype, array( 'phone', 'whatsapp' ), true ) ) {
    $calltype = '';
}
$page_name = mb_substr( $page_name, 0, 200 );

// نقبل فقط روابط تنتمي لنفس الموقع
if ( $page_url ) {
    $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
    $url_host  = wp_parse_url( $page_url, PHP_URL_HOST );
    if ( ! $url_host || strtolower( (string) $url_host ) !== strtolower( (string) $site_host ) ) {
        $page_url = '';
    }
}

$post_id = wp_insert_post( array(
    'post_title'  => ( '' !== $page_name ) ? $page_name : '--',
    'post_type'   => 'callwebsite',
    'post_status' => 'publish',
) );

$json = array( 'output' => 'Error' );

if ( $post_id && ! is_wp_error( $post_id ) ) {
    update_post_meta( $post_id, 'calldate', current_time( 'mysql' ) );
    update_post_meta( $post_id, 'page', $page_name );
    update_post_meta( $post_id, 'pageurl', $page_url );
    update_post_meta( $post_id, 'calltype', $calltype );
    $json = array(
        'output'   => 'Success',
        'calltype' => $calltype,
        'page'     => $page_name,
    );
}

echo wp_json_encode( $json );
