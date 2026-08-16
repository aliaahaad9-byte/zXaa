<?php
/**
 * Frontend endpoint: records a click on the phone / WhatsApp buttons.
 * POST: page (document title), page_url (current URL), call_type (call|whatsapp)
 */
ob_start();
header( 'Content-Type: application/json; charset=utf-8' );

$json = array();

$page_name = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
$page_url  = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';
$call_type = ( isset( $_POST['call_type'] ) && 'whatsapp' === $_POST['call_type'] ) ? 'whatsapp' : 'call';

// Only keep URLs that belong to this website.
$home_host = wp_parse_url( home_url(), PHP_URL_HOST );
$url_host  = $page_url ? wp_parse_url( $page_url, PHP_URL_HOST ) : '';
if ( $page_url && $url_host !== $home_host ) {
    $page_url = '';
}

if ( '' === $page_name ) {
    $page_name = '--';
}

// Stored in UTC; the dashboard converts to Asia/Riyadh (GMT+3) for display.
$calldate = gmdate( 'Y-m-d H:i:s' );

$post_id = wp_insert_post(
    array(
        'post_title'  => '--',
        'post_type'   => 'callwebsite',
        'post_status' => 'publish',
    )
);

if ( $post_id && ! is_wp_error( $post_id ) ) {
    update_post_meta( $post_id, 'calldate', $calldate );
    update_post_meta( $post_id, 'page', $page_name );
    update_post_meta( $post_id, 'page_url', $page_url );
    update_post_meta( $post_id, 'call_type', $call_type );

    $json['calldate']  = $calldate;
    $json['page']      = $page_name;
    $json['page_url']  = $page_url;
    $json['call_type'] = $call_type;
}

$json['output'] = ob_get_clean();

echo wp_json_encode( $json );
