<?php
/**
 * منطق تسجيل نقرات الاتصال — مشترك بين مسارين:
 *   1) admin-ajax.php   (يعمل دائمًا على أي تركيب ووردبريس)
 *   2) /AjaxCenter/callupdate  (المسار القديم، يبقى للتوافق)
 *
 * لا يُكتب سجل إلا لنقرة حقيقية من صفحات الموقع نفسه.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** توحيد اسم النطاق: تجاهل www. وحالة الأحرف. */
function yc_track_host( $url ) {
    $host = $url ? wp_parse_url( $url, PHP_URL_HOST ) : '';
    $host = strtolower( (string) $host );
    return preg_replace( '/^www\./', '', $host );
}

/** هل وكيل المستخدم زاحف أو أداة فحص؟ */
function yc_track_is_bot( $ua ) {
    $ua = strtolower( trim( (string) $ua ) );
    if ( '' === $ua ) {
        return true;
    }
    $bots = array( 'bot/', 'bot ', 'googlebot', 'bingbot', 'yandex', 'baiduspider',
                   'crawler', 'spider', 'slurp', 'curl/', 'wget', 'python-requests',
                   'headlesschrome', 'phantomjs', 'uptimerobot', 'pingdom',
                   'facebookexternalhit', 'lighthouse', 'ahrefsbot', 'semrushbot',
                   'dataprovider', 'petalbot', 'gptbot', 'ccbot' );
    foreach ( $bots as $bot ) {
        if ( false !== strpos( $ua, $bot ) ) {
            return true;
        }
    }
    return false;
}

/**
 * يسجّل النقرة. يعيد مصفوفة: saved + reason.
 *
 * @param bool $throttle تفعيل كبح التكرار (يُعطَّل في الفحص التشخيصي).
 */
function yc_track_record( $throttle = true ) {

    // 1) الطريقة
    $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : '';
    if ( 'POST' !== $method ) {
        return array( 'saved' => false, 'reason' => 'method' );
    }

    // 2) نوع النقرة
    $call_type = isset( $_POST['call_type'] ) ? sanitize_key( wp_unslash( $_POST['call_type'] ) ) : '';
    if ( ! in_array( $call_type, array( 'call', 'whatsapp' ), true ) ) {
        return array( 'saved' => false, 'reason' => 'type' );
    }

    // 3) الزواحف
    $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
    if ( yc_track_is_bot( $ua ) ) {
        return array( 'saved' => false, 'reason' => 'bot' );
    }

    // 4) المصدر: يكفي أن يطابق الرابط المرسل أو الـ referer نطاق الموقع
    $site_host = yc_track_host( home_url() );
    $referer   = isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '';
    $page_url  = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';

    $url_ok = ( $page_url && yc_track_host( $page_url ) === $site_host );
    $ref_ok = ( $referer  && yc_track_host( $referer )  === $site_host );

    if ( ! $url_ok && ! $ref_ok ) {
        return array( 'saved' => false, 'reason' => 'origin' );
    }
    if ( ! $url_ok ) {
        $page_url = esc_url_raw( $referer );
    }

    // 5) اسم الصفحة: العنوان، وإلا يُشتق من الرابط
    $page_name = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
    $page_name = trim( $page_name );
    if ( '' === $page_name && $page_url ) {
        $path = trim( (string) wp_parse_url( $page_url, PHP_URL_PATH ), '/' );
        $page_name = ( '' === $path )
            ? get_bloginfo( 'name' )
            : urldecode( str_replace( array( '-', '/' ), array( ' ', ' — ' ), $path ) );
    }
    if ( '' === $page_name ) {
        return array( 'saved' => false, 'reason' => 'page' );
    }

    // 6) كبح التكرار
    if ( $throttle ) {
        $ip  = isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] )
             : ( isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '' );
        $key = 'yct_' . md5( $ip . '|' . $call_type . '|' . $page_url );
        if ( get_transient( $key ) ) {
            return array( 'saved' => false, 'reason' => 'throttled' );
        }
        set_transient( $key, 1, 8 );
    }

    // 7) الكتابة
    $calldate = gmdate( 'Y-m-d H:i:s' ); // UTC — اللوحة تحوّله لتوقيت الرياض

    $post_id = wp_insert_post( array(
        'post_title'  => '--',
        'post_type'   => 'callwebsite',
        'post_status' => 'publish',
    ) );

    if ( ! $post_id || is_wp_error( $post_id ) ) {
        return array( 'saved' => false, 'reason' => 'insert' );
    }

    update_post_meta( $post_id, 'calldate', $calldate );
    update_post_meta( $post_id, 'page', $page_name );
    update_post_meta( $post_id, 'page_url', $page_url );
    update_post_meta( $post_id, 'call_type', $call_type );

    return array(
        'saved'     => true,
        'id'        => (int) $post_id,
        'calldate'  => $calldate,
        'page'      => $page_name,
        'page_url'  => $page_url,
        'call_type' => $call_type,
    );
}

/* -------------------------------------------------------------------------
 * المسار المضمون: admin-ajax.php — لا يعتمد على الروابط الدائمة إطلاقًا
 * ---------------------------------------------------------------------- */
function yc_track_ajax() {
    $result = yc_track_record( true );
    wp_send_json( $result );
}
add_action( 'wp_ajax_nopriv_yc_call_track', 'yc_track_ajax' );
add_action( 'wp_ajax_yc_call_track', 'yc_track_ajax' );

/* -------------------------------------------------------------------------
 * تعريف عنوان المسار للواجهة
 * ---------------------------------------------------------------------- */
add_action( 'wp_footer', function () {
    echo '<script>var YCTrackURL=' . wp_json_encode( admin_url( 'admin-ajax.php' ) ) . ';</script>' . "\n";
}, 5 );

/* -------------------------------------------------------------------------
 * تفريغ قواعد الروابط مرة واحدة بعد التحديث حتى يعمل المسار القديم أيضًا
 * ---------------------------------------------------------------------- */
add_action( 'wp_loaded', function () {
    if ( 'v2' !== get_option( 'yc_track_flushed' ) ) {
        flush_rewrite_rules( false );
        update_option( 'yc_track_flushed', 'v2' );
    }
}, 99 );

/* -------------------------------------------------------------------------
 * فحص تشخيصي: يختبر مسار التتبع فعليًا ويقول أين الخلل بالضبط
 * ---------------------------------------------------------------------- */
add_action( 'wp_ajax_yc_track_diag', function () {
    check_ajax_referer( 'callnumber_admin', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'غير مصرح.' ), 403 );
    }

    $checks    = array();
    $ajax_url  = admin_url( 'admin-ajax.php' );
    $site_host = yc_track_host( home_url() );

    // 1) هل يستجيب admin-ajax ويُسجّل فعلًا؟
    $before = (int) wp_count_posts( 'callwebsite' )->publish;
    $res = wp_remote_post( $ajax_url, array(
        'timeout'   => 20,
        'sslverify' => false,
        'headers'   => array( 'Referer' => home_url( '/' ) ),
        'body'      => array(
            'action'    => 'yc_call_track',
            'page'      => 'فحص تشخيصي — لوحة المكالمات',
            'page_url'  => home_url( '/' ),
            'call_type' => 'call',
        ),
    ) );

    if ( is_wp_error( $res ) ) {
        $checks[] = array( 'ok' => false, 'label' => 'مسار التسجيل (admin-ajax)',
            'note' => 'تعذر الاتصال: ' . $res->get_error_message() );
    } else {
        $code = wp_remote_retrieve_response_code( $res );
        $body = json_decode( wp_remote_retrieve_body( $res ), true );
        $ok   = ( 200 === $code && ! empty( $body['saved'] ) );
        $note = ( 200 !== $code )
            ? 'استجابة HTTP ' . $code . ' — قد يكون هناك جدار حماية أو إضافة تحجب admin-ajax.'
            : ( $ok ? 'تم تسجيل نقرة تجريبية بنجاح.'
                    : 'رُفض الطلب، السبب: ' . ( isset( $body['reason'] ) ? $body['reason'] : 'غير معروف' ) );
        $checks[] = array( 'ok' => $ok, 'label' => 'مسار التسجيل (admin-ajax)', 'note' => $note );

        // نحذف السجل التجريبي فورًا
        if ( $ok && ! empty( $body['id'] ) ) {
            wp_delete_post( (int) $body['id'], true );
        }
    }

    // 2) المسار القديم (الروابط الدائمة)
    $legacy = wp_remote_post( home_url( '/AjaxCenter/callupdate' ), array(
        'timeout' => 15, 'sslverify' => false,
        'headers' => array( 'Referer' => home_url( '/' ) ),
        'body'    => array( 'call_type' => 'x' ), // نوع غير صالح: لن يكتب شيئًا
    ) );
    if ( is_wp_error( $legacy ) ) {
        $checks[] = array( 'ok' => false, 'label' => 'المسار القديم /AjaxCenter/',
            'note' => $legacy->get_error_message() );
    } else {
        $lcode = wp_remote_retrieve_response_code( $legacy );
        $checks[] = array(
            'ok'    => ( 200 === $lcode ),
            'label' => 'المسار القديم /AjaxCenter/',
            'note'  => ( 200 === $lcode )
                ? 'يعمل.'
                : 'يعطي ' . $lcode . ' — احفظ الروابط الدائمة (الإعدادات ← روابط دائمة ← حفظ). لا يؤثر على التتبع لأنه يستخدم admin-ajax.',
        );
    }

    // 3) نطاق الموقع
    $current_host = isset( $_SERVER['HTTP_HOST'] ) ? yc_track_host( 'http://' . wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
    $checks[] = array(
        'ok'    => ( $current_host === $site_host ),
        'label' => 'تطابق نطاق الموقع',
        'note'  => 'إعدادات ووردبريس: ' . $site_host . ' · النطاق الحالي: ' . $current_host
                 . ( $current_host === $site_host ? '' : ' — اختلاف النطاق يمنع تسجيل النقرات.' ),
    );

    // 4) نوع المحتوى
    $checks[] = array(
        'ok'    => post_type_exists( 'callwebsite' ),
        'label' => 'نوع المحتوى callwebsite',
        'note'  => post_type_exists( 'callwebsite' ) ? 'مسجَّل.' : 'غير مسجَّل — القالب غير مفعَّل بالكامل.',
    );

    // 5) إجمالي السجلات
    $after = (int) wp_count_posts( 'callwebsite' )->publish;
    $checks[] = array(
        'ok'    => true,
        'label' => 'إجمالي السجلات المخزَّنة',
        'note'  => number_format_i18n( $after ) . ' سجل.',
    );

    wp_send_json_success( array( 'checks' => $checks ) );
} );
