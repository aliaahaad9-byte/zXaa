<?php
/**
 * نقطة تسجيل نقرات الاتصال (هاتف / واتساب).
 *
 * محصّنة: لا تُنشئ سجلًا إلا لنقرة حقيقية قادمة من صفحات الموقع نفسه.
 * أي طلب مباشر أو من زاحف أو بلا بيانات يُرفض بلا كتابة أي شيء في قاعدة البيانات.
 *
 * POST: page (عنوان الصفحة) · page_url (رابطها) · call_type (call | whatsapp)
 */

header( 'Content-Type: application/json; charset=utf-8' );
nocache_headers();

$json = array( 'saved' => false );

/** رفض مهذّب بلا أي كتابة. */
function callupdate_reject( $reason ) {
    echo wp_json_encode( array( 'saved' => false, 'reason' => $reason ) );
    die();
}

/* ---- 1) الطريقة: POST فقط ---- */
if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
    callupdate_reject( 'method' );
}

/* ---- 2) نوع النقرة: إلزامي وصالح ---- */
$call_type = isset( $_POST['call_type'] ) ? sanitize_key( wp_unslash( $_POST['call_type'] ) ) : '';
if ( ! in_array( $call_type, array( 'call', 'whatsapp' ), true ) ) {
    callupdate_reject( 'type' );
}

/* ---- 3) المصدر: من صفحات هذا الموقع فقط ---- */
$home_host = wp_parse_url( home_url(), PHP_URL_HOST );
$referer   = isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '';
$ref_host  = $referer ? wp_parse_url( $referer, PHP_URL_HOST ) : '';
if ( ! $ref_host || strcasecmp( $ref_host, $home_host ) !== 0 ) {
    callupdate_reject( 'referer' );
}

/* ---- 4) تجاهل الزواحف وبرامج الفحص ---- */
$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
if ( '' === trim( $ua ) ) {
    callupdate_reject( 'ua' );
}
$bots = array( 'bot', 'crawl', 'spider', 'slurp', 'curl', 'wget', 'python', 'headless',
               'monitor', 'uptime', 'preview', 'facebookexternalhit', 'lighthouse',
               'pingdom', 'ahrefs', 'semrush', 'dataprovider', 'scan' );
$ua_l = strtolower( $ua );
foreach ( $bots as $bot ) {
    if ( false !== strpos( $ua_l, $bot ) ) {
        callupdate_reject( 'bot' );
    }
}

/* ---- 5) الرابط: من هذا الموقع، ومنه نشتق اسم الصفحة عند الحاجة ---- */
$page_url = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';
if ( $page_url && strcasecmp( (string) wp_parse_url( $page_url, PHP_URL_HOST ), $home_host ) !== 0 ) {
    $page_url = '';
}
if ( ! $page_url ) {
    $page_url = esc_url_raw( $referer );
}

/* ---- 6) اسم الصفحة: من العنوان، وإلا من الرابط ---- */
$page_name = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
$page_name = trim( $page_name );
if ( '' === $page_name && $page_url ) {
    $path = trim( (string) wp_parse_url( $page_url, PHP_URL_PATH ), '/' );
    if ( '' !== $path ) {
        $page_name = urldecode( str_replace( array( '-', '/' ), array( ' ', ' — ' ), $path ) );
    }
}
if ( '' === $page_name ) {
    callupdate_reject( 'page' );
}

/* ---- 7) كبح التكرار: نقرة واحدة من نفس الزائر لنفس النوع كل 10 ثوانٍ ---- */
$ip  = isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] )
     : ( isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '' );
$key = 'cu_' . md5( $ip . '|' . $call_type . '|' . $page_url );
if ( get_transient( $key ) ) {
    callupdate_reject( 'throttled' );
}
set_transient( $key, 1, 10 );

/* ---- 8) التسجيل ---- */
$calldate = gmdate( 'Y-m-d H:i:s' ); // بتوقيت UTC — اللوحة تحوّله إلى توقيت الرياض

$post_id = wp_insert_post( array(
    'post_title'  => '--',
    'post_type'   => 'callwebsite',
    'post_status' => 'publish',
) );

if ( ! $post_id || is_wp_error( $post_id ) ) {
    callupdate_reject( 'insert' );
}

update_post_meta( $post_id, 'calldate', $calldate );
update_post_meta( $post_id, 'page', $page_name );
update_post_meta( $post_id, 'page_url', $page_url );
update_post_meta( $post_id, 'call_type', $call_type );

echo wp_json_encode( array(
    'saved'     => true,
    'calldate'  => $calldate,
    'page'      => $page_name,
    'page_url'  => $page_url,
    'call_type' => $call_type,
) );
die();
