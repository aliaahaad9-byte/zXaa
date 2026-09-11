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
 * يقرأ الحقل من جسم الطلب أو من الرابط.
 *
 * سبب القراءة من الرابط: أي تحويل 301/302 — مثل الذي تفعله إضافات
 * السيو مع الروابط غير المعروفة — يُسقط محتوى POST بالكامل ويحوّل
 * الطلب إلى GET. أما معاملات الرابط فتبقى كما هي، فنرسل البيانات
 * في الاثنين معًا حتى لا تضيع النقرة.
 */
function yc_track_param( $key ) {
    if ( isset( $_POST[ $key ] ) && '' !== trim( (string) $_POST[ $key ] ) ) {
        return wp_unslash( $_POST[ $key ] );
    }
    if ( isset( $_GET[ $key ] ) && '' !== trim( (string) $_GET[ $key ] ) ) {
        return wp_unslash( $_GET[ $key ] );
    }
    return '';
}

/**
 * يسجّل النقرة. يعيد مصفوفة: saved + reason.
 *
 * @param bool $throttle تفعيل كبح التكرار (يُعطَّل في الفحص التشخيصي).
 */
function yc_track_record( $throttle = true ) {

    // 1) الطريقة: POST أو GET — لأن التحويل يحوّل POST إلى GET،
    //    والحماية الحقيقية تأتي من فحص النوع والمصدر والوكيل أدناه.
    $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : '';
    if ( ! in_array( $method, array( 'POST', 'GET' ), true ) ) {
        return array( 'saved' => false, 'reason' => 'method' );
    }

    // 2) نوع النقرة
    $call_type = sanitize_key( yc_track_param( 'call_type' ) );

    // توافق مع السكربت القديم المخزَّن في الكاش: كان يرسل نوعًا مختلفًا أو لا يرسل شيئًا.
    // نستنتج النوع من الحقول التي كان يرسلها بدل رفض نقرة حقيقية.
    if ( ! in_array( $call_type, array( 'call', 'whatsapp' ), true ) ) {
        $guess = '';
        foreach ( array( 'call_type', 'type', 'kind', 'action_type' ) as $k ) {
            $v = strtolower( trim( (string) yc_track_param( $k ) ) );
            if ( '' === $v ) {
                continue;
            }
            if ( false !== strpos( $v, 'whats' ) || false !== strpos( $v, 'wa' ) ) {
                $guess = 'whatsapp';
                break;
            }
            if ( false !== strpos( $v, 'call' ) || false !== strpos( $v, 'phone' ) || false !== strpos( $v, 'tel' ) ) {
                $guess = 'call';
                break;
            }
        }
        $call_type = $guess;
    }

    // ما زال بلا نوع: نقبله فقط إن كان طلبًا بشريًا صريحًا من صفحة حقيقية،
    // ونعلّمه legacy حتى يظهر في اللوحة بدل أن يضيع أو يلوّث الإحصائيات.
    $legacy = false;
    if ( ! in_array( $call_type, array( 'call', 'whatsapp' ), true ) ) {
        $has_page = '' !== trim( (string) yc_track_param( 'page' ) );
        if ( ! $has_page ) {
            return array( 'saved' => false, 'reason' => 'type' );
        }
        $call_type = 'call';
        $legacy    = true;
    }

    // 3) الزواحف
    $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
    if ( yc_track_is_bot( $ua ) ) {
        return array( 'saved' => false, 'reason' => 'bot' );
    }

    // 4) المصدر: يكفي أن يطابق الرابط المرسل أو الـ referer نطاق الموقع
    $site_host = yc_track_host( home_url() );
    $referer   = isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '';
    $page_url  = esc_url_raw( yc_track_param( 'page_url' ) );

    $url_ok = ( $page_url && yc_track_host( $page_url ) === $site_host );
    $ref_ok = ( $referer  && yc_track_host( $referer )  === $site_host );

    // مصدر خارجي صريح يُرفض دائمًا، حتى لو كان الرابط المُرسل داخليًا
    if ( $referer && ! $ref_ok ) {
        return array( 'saved' => false, 'reason' => 'origin' );
    }
    // بلا مصدر (سياسة خصوصية): يكفي أن يكون الرابط المُرسل من الموقع
    if ( ! $url_ok && ! $ref_ok ) {
        return array( 'saved' => false, 'reason' => 'origin' );
    }
    if ( ! $url_ok ) {
        $page_url = esc_url_raw( $referer );
    }

    // 5) اسم الصفحة: العنوان، وإلا يُشتق من الرابط
    $page_name = sanitize_text_field( yc_track_param( 'page' ) );
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
    // بصمة تُثبت أن هذا السجل من متتبّع القالب — أي سجل بلا هذه البصمة
    // أنشأه كود آخر (إضافة أو سكربت مكرّر)
    update_post_meta( $post_id, 'src', $legacy ? 'yc-legacy' : 'yc' );

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
 * البحث عن أي كود آخر يُنشئ سجلات مكالمات (المصدر الحقيقي للسجلات المعطوبة)
 * ---------------------------------------------------------------------- */
function yc_track_find_rogue() {
    global $wpdb;
    $hits = array();

    $needles = array( 'callwebsite', 'AjaxCenter/callupdate' );
    $theme   = wp_normalize_path( get_template_directory() );

    // 1) ملفات داخل wp-content خارج القالب المفعَّل
    $roots = array( WP_CONTENT_DIR . '/plugins', WP_CONTENT_DIR . '/mu-plugins', WP_CONTENT_DIR . '/themes' );
    $scanned = 0;
    foreach ( $roots as $root ) {
        if ( ! is_dir( $root ) ) {
            continue;
        }
        try {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::SELF_FIRST
            );
        } catch ( Exception $e ) {
            continue;
        }
        foreach ( $it as $f ) {
            if ( $scanned > 12000 ) {
                break 2;
            }
            if ( ! $f->isFile() || 'php' !== strtolower( $f->getExtension() ) ) {
                continue;
            }
            $path = wp_normalize_path( $f->getPathname() );
            if ( 0 === strpos( $path, $theme ) ) {
                continue; // القالب المفعَّل نفسه
            }
            if ( $f->getSize() > 2097152 ) {
                continue;
            }
            $scanned++;
            $code = @file_get_contents( $path );
            if ( ! $code ) {
                continue;
            }
            foreach ( $needles as $n ) {
                if ( false !== strpos( $code, $n ) ) {
                    $hits[] = str_replace( wp_normalize_path( WP_CONTENT_DIR ), 'wp-content', $path );
                    break;
                }
            }
        }
    }

    // 2) مقتطفات إضافة WPCode (نوع محتوى wpcode)
    $wpcode = $wpdb->get_col( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('wpcode','wpcode_snippet')
         AND (post_content LIKE %s OR post_content LIKE %s)",
        '%callwebsite%', '%callupdate%'
    ) );
    foreach ( (array) $wpcode as $id ) {
        $hits[] = 'مقتطف WPCode رقم ' . (int) $id . ' — ' . get_the_title( $id );
    }

    // 3) إضافة Code Snippets (جدول مستقل)
    $tbl = $wpdb->prefix . 'snippets';
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $tbl ) ) === $tbl ) {
        $snips = $wpdb->get_results( "SELECT id, name FROM {$tbl} WHERE code LIKE '%callwebsite%' OR code LIKE '%callupdate%'" );
        foreach ( (array) $snips as $sn ) {
            $hits[] = 'مقتطف Code Snippets رقم ' . (int) $sn->id . ' — ' . $sn->name;
        }
    }

    return $hits;
}

/** آخر السجلات مع بصمة المصدر. */
function yc_track_recent( $limit = 8 ) {
    global $wpdb;
    $ids = $wpdb->get_col( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'callwebsite' AND post_status = 'publish'
         ORDER BY post_date_gmt DESC, ID DESC LIMIT %d", $limit
    ) );
    $out = array();
    foreach ( $ids as $id ) {
        $out[] = array(
            'id'   => (int) $id,
            'page' => get_post_meta( $id, 'page', true ),
            'type' => get_post_meta( $id, 'call_type', true ),
            'src'  => get_post_meta( $id, 'src', true ),
        );
    }
    return $out;
}

/* -------------------------------------------------------------------------
 * فحص تشخيصي: يختبر مسار التتبع فعليًا ويقول أين الخلل بالضبط
 * ---------------------------------------------------------------------- */
add_action( 'wp_ajax_yc_track_diag', function () {
    check_ajax_referer( 'callnumber_admin', 'nonce' );
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
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

    // 4.5) هل يُحوَّل مسار التتبع؟ (إضافات السيو تفعل ذلك وتُسقط بيانات POST)
    $probe = wp_remote_post( $ajax_url . '?action=yc_call_track', array(
        'timeout'     => 15,
        'sslverify'   => false,
        'redirection' => 0, // لا نتبع التحويل حتى نكشفه
        'headers'     => array( 'Referer' => home_url( '/' ) ),
        'body'        => array( 'action' => 'yc_call_track', 'call_type' => 'x' ),
    ) );
    if ( ! is_wp_error( $probe ) ) {
        $pcode = (int) wp_remote_retrieve_response_code( $probe );
        $loc   = wp_remote_retrieve_header( $probe, 'location' );
        $redir = ( $pcode >= 300 && $pcode < 400 );
        $checks[] = array(
            'ok'    => ! $redir,
            'label' => 'تحويل على مسار التتبع',
            'note'  => $redir
                ? 'الطلب يُحوَّل (' . $pcode . ') إلى: ' . ( $loc ? $loc : 'غير معروف' )
                  . ' — التحويل يُسقط بيانات POST. استثنِ admin-ajax.php من قواعد التحويل في إضافة السيو.'
                : 'لا يوجد تحويل — المسار مباشر.',
        );
    }

    // 5) القالب المفعَّل ومكان المتتبّع
    $tpl_dir = wp_normalize_path( get_template_directory() );
    $has_trk = file_exists( $tpl_dir . '/components/packs/callnumber/tracker.php' );
    $checks[] = array(
        'ok'    => $has_trk,
        'label' => 'مكان ملف التتبع',
        'note'  => 'القالب المفعَّل: ' . basename( $tpl_dir )
                 . ( $has_trk ? ' — الملف موجود في مكانه.' : ' — ملف tracker.php غير موجود هنا! رفعت التصحيح لمجلد قالب غير مفعَّل.' ),
    );

    // 6) كود آخر يُنشئ سجلات
    $rogue = yc_track_find_rogue();
    $checks[] = array(
        'ok'    => empty( $rogue ),
        'label' => 'كود تتبع مكرّر خارج القالب',
        'note'  => empty( $rogue )
            ? 'لا يوجد — القالب هو المصدر الوحيد.'
            : 'وُجد ' . count( $rogue ) . ' مصدرًا آخر ينشئ سجلات مكالمات، وهو سبب السجلات بلا نوع: '
              . implode( ' · ', array_slice( $rogue, 0, 6 ) )
              . ' — عطّله ثم أعد الفحص.',
    );

    // 7) آخر السجلات ومصدرها
    $recent = yc_track_recent( 8 );
    $tagged = 0;
    $lines  = array();
    foreach ( $recent as $r ) {
        if ( 'yc' === $r['src'] ) {
            $tagged++;
        }
        $lines[] = '#' . $r['id'] . ' [' . ( $r['type'] ? $r['type'] : 'بلا نوع' ) . '] '
                 . ( $r['page'] ? mb_substr( $r['page'], 0, 28 ) : 'بلا صفحة' )
                 . ( 'yc' === $r['src'] ? ' ✓القالب' : ' ✕مصدر آخر' );
    }
    $checks[] = array(
        'ok'    => ( $recent && $tagged === count( $recent ) ),
        'label' => 'مصدر آخر ' . count( $recent ) . ' سجلات',
        'note'  => $recent
            ? ( $tagged . ' من ' . count( $recent ) . ' من القالب. ' . implode( ' | ', $lines ) )
            : 'لا توجد سجلات بعد.',
    );

    // 8) ذاكرة OPcache
    if ( function_exists( 'opcache_get_status' ) ) {
        $st = @opcache_get_status( false );
        $on = ( is_array( $st ) && ! empty( $st['opcache_enabled'] ) );
        $checks[] = array(
            'ok'    => true,
            'label' => 'ذاكرة OPcache',
            'note'  => $on
                ? 'مفعّلة — إن بقي سلوك قديم بعد رفع الملفات، أعد تشغيل PHP من cPanel أو غيّر إصدار PHP ثم أعده.'
                : 'غير مفعّلة.',
        );
    }

    // 9) إضافات التخزين المؤقت
    $cache_plugins = array();
    foreach ( array( 'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache',
                     'wp-rocket/wp-rocket.php'             => 'WP Rocket',
                     'w3-total-cache/w3-total-cache.php'   => 'W3 Total Cache',
                     'wp-super-cache/wp-cache.php'         => 'WP Super Cache',
                     'autoptimize/autoptimize.php'         => 'Autoptimize' ) as $file => $name ) {
        if ( is_plugin_active( $file ) ) {
            $cache_plugins[] = $name;
        }
    }
    $checks[] = array(
        'ok'    => empty( $cache_plugins ),
        'label' => 'إضافات التخزين المؤقت',
        'note'  => $cache_plugins
            ? implode( ' · ', $cache_plugins ) . ' — سكربت التتبع مضمَّن داخل صفحات الموقع، فامسح الكاش بالكامل بعد التحديث.'
            : 'لا توجد إضافة كاش مفعّلة.',
    );

    // 10) إجمالي السجلات
    $after = (int) wp_count_posts( 'callwebsite' )->publish;
    $checks[] = array(
        'ok'    => true,
        'label' => 'إجمالي السجلات المخزَّنة',
        'note'  => number_format_i18n( $after ) . ' سجل.',
    );

    wp_send_json_success( array( 'checks' => $checks ) );
} );
