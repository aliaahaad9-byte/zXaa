<?php
/**
 * ServiceLanding — تحويل تصنيف الخدمة من أرشيف عادي إلى صفحة هبوط.
 *
 * لا يوجد أي نص تسويقي داخل هذا الملف: كل عنوان ورقم وميزة وخطوة
 * يأتي من حقول تُضاف إلى شاشة تحرير التصنيف.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =========================================================================
 * أدوات مساعدة
 * ====================================================================== */

/** هل هذا التصنيف مفعّل عليه وضع صفحة الهبوط؟ */
function svc_is_landing( $term_id ) {
    return 'on' === get_term_meta( $term_id, 'svc_enable', true );
}

/** قراءة حقل نصي من التصنيف بعد تنظيفه. */
function svc_field( $term_id, $key ) {
    return trim( (string) get_term_meta( $term_id, $key, true ) );
}

/**
 * تحويل حقل متعدد الأسطر إلى مصفوفة صفوف.
 * كل سطر: «الجزء الأول | الجزء الثاني» — الفاصل | أو =
 */
function svc_rows( $raw, $limit = 0 ) {
    $rows = array();
    if ( '' === trim( (string) $raw ) ) {
        return $rows;
    }
    foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
        $line = trim( $line );
        if ( '' === $line ) {
            continue;
        }
        $parts = preg_split( '/\s*[|=]\s*/u', $line, 2 );
        $rows[] = array(
            'a' => trim( $parts[0] ),
            'b' => isset( $parts[1] ) ? trim( $parts[1] ) : '',
        );
        if ( $limit && count( $rows ) >= $limit ) {
            break;
        }
    }
    return $rows;
}

/**
 * تنسيق الأرقام بشكل موحّد عبر الصفحة كلها.
 * latin  => 8,400   |   arabic => ٨٬٤٠٠
 * لا خلط: الأرقام والفاصل من نفس النظام دائمًا.
 */
function svc_num( $value, $style = 'latin' ) {
    $raw = trim( (string) $value );
    if ( '' === $raw ) {
        return '';
    }

    // نمرّر النص كما هو إن لم يكن رقمًا خالصًا (مثل «٢٤/٧»).
    $digits = str_replace( array( ',', '٬', '،', ' ', '٫', '.' ), '', $raw );
    $digits = strtr( $digits, array(
        '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4',
        '٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
    ) );
    if ( ! preg_match( '/^\d+$/', $digits ) ) {
        return $raw;
    }

    $formatted = number_format( (float) $digits, 0, '.', ',' );

    if ( 'arabic' === $style ) {
        $formatted = strtr( $formatted, array(
            '0'=>'٠','1'=>'١','2'=>'٢','3'=>'٣','4'=>'٤',
            '5'=>'٥','6'=>'٦','7'=>'٧','8'=>'٨','9'=>'٩',
            ','=>'٬',
        ) );
    }
    return $formatted;
}

/** نظام الأرقام المختار للتصنيف (لاتيني افتراضيًا). */
function svc_num_style( $term_id ) {
    return ( 'arabic' === svc_field( $term_id, 'svc_num_style' ) ) ? 'arabic' : 'latin';
}

/** أيقونات SVG مضمّنة — بلا رموز تعبيرية وبلا ملفات خارجية. */
function svc_icon( $name ) {
    $p = array(
        'phone' => '<path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/>',
        'wa'    => '<path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>',
        'check' => '<path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/>',
        'star'  => '<path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/>',
        'shield'=> '<path d="M256 0c4.6 0 9.2 1 13.4 2.9L457.7 82.8c22 9.3 38.4 31 38.3 57.2c-.5 99.2-41.3 280.7-213.6 363.2c-16.7 8-36.1 8-52.8 0C57.3 420.7 16.5 239.2 16 140c-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.8 1 251.4 0 256 0z"/>',
        'clock' => '<path d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/>',
        'users' => '<path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/>',
        'pin'   => '<path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>',
        'award' => '<path d="M173.8 5.5c11-7.3 25.4-7.3 36.4 0L228 17.2c6 3.9 13 5.8 20.1 5.4l21.3-1.3c13.2-.8 25.6 6.4 31.5 18.2l9.6 19.1c3.2 6.4 8.4 11.5 14.7 14.7L344.5 83c11.8 5.9 19 18.3 18.2 31.5l-1.3 21.3c-.4 7.1 1.5 14.2 5.4 20.1l11.8 17.8c7.3 11 7.3 25.4 0 36.4L366.8 228c-3.9 6-5.8 13-5.4 20.1l1.3 21.3c.8 13.2-6.4 25.6-18.2 31.5l-19.1 9.6c-6.4 3.2-11.5 8.4-14.7 14.7L301 344.5c-5.9 11.8-18.3 19-31.5 18.2l-21.3-1.3c-7.1-.4-14.2 1.5-20.1 5.4l-17.8 11.8c-11 7.3-25.4 7.3-36.4 0L156 366.8c-6-3.9-13-5.8-20.1-5.4l-21.3 1.3c-13.2 .8-25.6-6.4-31.5-18.2l-9.6-19.1c-3.2-6.4-8.4-11.5-14.7-14.7L39.5 301c-11.8-5.9-19-18.3-18.2-31.5l1.3-21.3c.4-7.1-1.5-14.2-5.4-20.1L5.5 210.2c-7.3-11-7.3-25.4 0-36.4L17.2 156c3.9-6 5.8-13 5.4-20.1l-1.3-21.3c-.8-13.2 6.4-25.6 18.2-31.5l19.1-9.6C65 70.2 70.2 65 73.4 58.6L83 39.5c5.9-11.8 18.3-19 31.5-18.2l21.3 1.3c7.1 .4 14.2-1.5 20.1-5.4L173.8 5.5zM272 192a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM1.3 441.8L44.4 339.3c.2 .1 .3 .2 .4 .4l9.6 19.1c11.7 23.2 36 37.3 62 35.8l21.3-1.3c.2 0 .5 0 .7 .2l17.8 11.8c5.1 3.3 10.5 5.9 16.1 7.7l-37.4 89.1c-2.9 6.9-9.5 11.5-17 11.7s-14.3-3.9-17.6-10.6L74.7 454.2l-49.4 6.6c-7.3 1-14.5-2.2-18.7-8.2s-4.7-13.9-1.3-20.8zm248 60.4L212 413c5.6-1.8 11-4.3 16.1-7.7l17.8-11.8c.2-.1 .4-.2 .7-.2l21.3 1.3c26 1.5 50.3-12.6 62-35.8l9.6-19.1c.1-.2 .2-.3 .4-.4l43.2 102.5c2.9 6.9 1.9 14.9-2.7 20.8s-12 9.2-19.3 8.2l-49.4-6.6-25.2 42.6c-3.7 6.3-10.6 10.2-17.9 10.2s-14.1-3.9-17.9-10.2z"/>',
        'gear'  => '<path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>',
    );
    if ( ! isset( $p[ $name ] ) ) {
        return '';
    }
    $vb = ( 'users' === $name ) ? '0 0 640 512' : ( in_array( $name, array( 'wa', 'pin', 'award' ), true ) ? '0 0 448 512' : '0 0 512 512' );
    if ( 'pin' === $name )  { $vb = '0 0 384 512'; }
    if ( 'shield' === $name ) { $vb = '0 0 512 512'; }
    return '<svg viewBox="' . $vb . '" role="img" aria-hidden="true" focusable="false">' . $p[ $name ] . '</svg>';
}

/** ترتيب أيقونات بطاقات المميزات (ست بطاقات). */
function svc_feature_icons() {
    return array( 'clock', 'shield', 'award', 'users', 'gear', 'star' );
}

/* =========================================================================
 * العرض
 * ====================================================================== */

/**
 * يرسم صفحة الهبوط كاملة لتصنيف الخدمة.
 *
 * @param WP_Term      $obj      التصنيف الحالي.
 * @param ThemeStatic  $tpl      كائن القالب (لإعادة استخدام Part).
 */
function svc_render_landing( $obj, $tpl ) {

    $tid    = $obj->term_id;
    $nstyle = svc_num_style( $tid );
    $phone  = get_term_meta( $tid, 'call_number', true );
    if ( empty( $phone ) ) {
        $phone = get_option( 'Phone' );
    }
    $wa = get_term_meta( $tid, 'whatsapp_number', true );
    if ( empty( $wa ) ) {
        $wa = get_option( 'Whatsapp' );
    }

    $call_label = svc_field( $tid, 'svc_call_label' );
    $wa_label   = svc_field( $tid, 'svc_wa_label' );

    // كتلة أزرار الاتصال — تتكرر عند كل نقطة اقتناع
    $actions = '';
    if ( $call_label && $phone ) {
        $actions .= '<a class="svc-btn svc-btn--call" href="tel:' . esc_attr( $phone ) . '">'
                 . svc_icon( 'phone' ) . '<span>' . esc_html( $call_label ) . '</span></a>';
    }
    if ( $wa_label && $wa ) {
        $actions .= '<a class="svc-btn svc-btn--wa" target="_blank" rel="nofollow noopener" href="https://wa.me/'
                 . esc_attr( trim( $wa ) ) . '">' . svc_icon( 'wa' ) . '<span>' . esc_html( $wa_label ) . '</span></a>';
    }

    // أنماط الحزمة — مضمّنة لأن القالب يلغي كل الملفات المسجَّلة في الواجهة
    $css = get_template_directory() . '/components/packs/ServiceLanding/landing.css';
    if ( file_exists( $css ) ) {
        echo '<style>' . file_get_contents( $css ) . '</style>';
    }

    echo '<div class="svc-landing">';

    /* ---------- ١ المقدمة ---------- */
    $h1      = svc_field( $tid, 'svc_title' );
    $promise = svc_field( $tid, 'svc_promise' );
    $badges  = svc_rows( svc_field( $tid, 'svc_badges' ) );
    $icon    = get_term_meta( $tid, 'icon', true );

    echo '<section class="svc-hero"><div class="container"><div class="svc-hero__grid">';
    echo '<div class="svc-hero__intro">';
    if ( ! empty( $icon ) ) {
        echo '<div class="svc-hero__icon">' . $icon . '</div>';
    }
    echo '<h1>' . esc_html( $h1 ? $h1 : $obj->name ) . '</h1>';
    if ( $promise ) {
        echo '<p class="svc-hero__promise">' . esc_html( $promise ) . '</p>';
    }
    if ( $badges ) {
        echo '<ul class="svc-badges">';
        foreach ( $badges as $b ) {
            echo '<li>' . svc_icon( 'check' ) . '<span>' . esc_html( $b['a'] ) . '</span></li>';
        }
        echo '</ul>';
    }
    if ( $actions ) {
        echo '<div class="svc-actions">' . $actions . '</div>';
    }
    echo '</div>';

    // نموذج المقدمة — ثلاثة حقول
    $qf_title = svc_field( $tid, 'svc_quick_title' );
    if ( $qf_title ) {
        $qf_sub  = svc_field( $tid, 'svc_quick_sub' );
        $qf_btn  = svc_field( $tid, 'svc_quick_btn' );
        $qf_note = svc_field( $tid, 'svc_quick_note' );
        echo '<div class="svc-quickform form-contact svc-form" data-svc-form="quick">';
        echo '<h3>' . esc_html( $qf_title ) . '</h3>';
        if ( $qf_sub ) {
            echo '<p>' . esc_html( $qf_sub ) . '</p>';
        }
        echo '<form method="post">';
        echo '<label class="svc-lbl" for="svcq-name">' . esc_html( svc_field( $tid, 'svc_lbl_name' ) ) . '</label>';
        echo '<input class="svc-field" id="svcq-name" type="text" name="names" required>';
        echo '<label class="svc-lbl" for="svcq-phone">' . esc_html( svc_field( $tid, 'svc_lbl_phone' ) ) . '</label>';
        echo '<input class="svc-field" id="svcq-phone" type="tel" name="phone" required>';
        echo '<label class="svc-lbl" for="svcq-city">' . esc_html( svc_field( $tid, 'svc_lbl_city' ) ) . '</label>';
        echo '<input class="svc-field" id="svcq-city" type="text" name="address">';
        echo '<input type="hidden" name="message" value="' . esc_attr( $obj->name ) . '">';
        echo '<input type="hidden" name="email" value="">';
        if ( $qf_btn ) {
            echo '<button type="submit" class="svc-btn svc-btn--brand">' . esc_html( $qf_btn ) . '</button>';
        }
        echo '</form>';
        if ( $qf_note ) {
            echo '<p class="svc-hint">' . esc_html( $qf_note ) . '</p>';
        }
        echo '</div>';
    }
    echo '</div></div></section>';

    /* ---------- ٢ شريط الأرقام ---------- */
    $stats = svc_rows( svc_field( $tid, 'svc_stats' ), 4 );
    if ( $stats ) {
        echo '<section class="svc-stats"><div class="container"><div class="svc-stats__grid">';
        foreach ( $stats as $s ) {
            // لاحقة اختيارية بعد الرقم: «12+ | سنة خبرة»
            $num = $s['a'];
            $suf = '';
            if ( preg_match( '/^(.*?)([+٪%])$/u', $num, $m ) ) {
                $num = trim( $m[1] );
                $suf = $m[2];
            }
            echo '<div class="svc-stat">';
            echo '<span class="svc-stat__num">' . esc_html( svc_num( $num, $nstyle ) );
            if ( $suf ) {
                echo '<i>' . esc_html( $suf ) . '</i>';
            }
            echo '</span>';
            echo '<span class="svc-stat__label">' . esc_html( $s['b'] ) . '</span>';
            echo '</div>';
        }
        echo '</div></div></section>';
    }

    /* ---------- ٣ المميزات ---------- */
    $feats = svc_rows( svc_field( $tid, 'svc_features' ), 6 );
    if ( $feats ) {
        echo '<section class="svc-section svc-section--soft"><div class="container">';
        svc_head( svc_field( $tid, 'svc_features_title' ), svc_field( $tid, 'svc_features_sub' ) );
        echo '<div class="svc-cards">';
        $icons = svc_feature_icons();
        foreach ( $feats as $i => $f ) {
            echo '<article class="svc-card">';
            echo '<div class="svc-card__icon">' . svc_icon( $icons[ $i % count( $icons ) ] ) . '</div>';
            echo '<h3>' . esc_html( $f['a'] ) . '</h3>';
            if ( $f['b'] ) {
                echo '<p>' . esc_html( $f['b'] ) . '</p>';
            }
            echo '</article>';
        }
        echo '</div>';
        if ( $actions ) {
            echo '<div class="svc-actions svc-actions--center">' . $actions . '</div>';
        }
        echo '</div></section>';
    }

    /* ---------- ٤ خطوات العمل ---------- */
    $steps = svc_rows( svc_field( $tid, 'svc_steps' ) );
    if ( $steps ) {
        echo '<section class="svc-section"><div class="container">';
        svc_head( svc_field( $tid, 'svc_steps_title' ), svc_field( $tid, 'svc_steps_sub' ) );
        echo '<ol class="svc-flow">';
        $last = count( $steps ) - 1;
        foreach ( $steps as $i => $st ) {
            $done = ( $i === $last ) ? ' svc-stp--done' : '';
            echo '<li class="svc-stp' . $done . '">';
            echo '<span class="svc-stp__n">' . esc_html( svc_num( $i + 1, $nstyle ) ) . '</span>';
            echo '<div class="svc-stp__box">';
            echo '<h3>' . esc_html( $st['a'] ) . '</h3>';
            if ( $st['b'] ) {
                echo '<p>' . esc_html( $st['b'] ) . '</p>';
            }
            echo '</div></li>';
        }
        echo '</ol>';
        echo '</div></section>';
    }

    /* ---------- ٥ المدن ---------- */
    $cities = get_term_meta( $tid, 'country', true );
    if ( is_array( $cities ) && $cities ) {
        $as_service = ( 'on' === svc_field( $tid, 'svc_city_service_links' ) );
        echo '<section class="svc-section svc-section--soft"><div class="container">';
        svc_head( svc_field( $tid, 'svc_cities_title' ), svc_field( $tid, 'svc_cities_sub' ) );
        echo '<div class="svc-cities">';
        foreach ( $cities as $cid ) {
            $city = get_term( $cid, 'country' );
            if ( ! $city || is_wp_error( $city ) ) {
                continue;
            }
            // الافتراضي: صفحة المدينة في أرشيف المدن
            $url = get_term_link( $city );
            if ( $as_service && class_exists( 'CityServices' ) ) {
                $url = ( new CityServices )->ServiceQVar( $obj, $city );
            }
            if ( is_wp_error( $url ) ) {
                continue;
            }
            echo '<a class="svc-city" href="' . esc_url( $url ) . '">' . esc_html( $city->name ) . '</a>';
        }
        echo '</div>';
        if ( $actions ) {
            echo '<div class="svc-actions svc-actions--center">' . $actions . '</div>';
        }
        echo '</div></section>';
    }

    /* ---------- ٦ الأسئلة الشائعة ---------- */
    $faq_posts = svc_faq_posts( $tid );
    if ( $faq_posts ) {
        echo '<section class="svc-section"><div class="container">';
        svc_head( svc_field( $tid, 'svc_faq_title' ), svc_field( $tid, 'svc_faq_sub' ) );
        echo '<div class="svc-faqwrap"><div class="faq_section"><div class="faq-info">';
        // نفس مكوّن الأسئلة الموجود في القالب
        $tpl->Part( 'faq', array( 'args' => svc_faq_args( $tid ) ) );
        echo '</div></div></div>';
        echo '</div></section>';
    }

    /* ---------- ٧ نموذج الطلب الكامل ---------- */
    $ord_title = svc_field( $tid, 'svc_order_title' );
    if ( $ord_title ) {
        echo '<section class="svc-section svc-section--soft"><div class="container">';
        svc_head( $ord_title, svc_field( $tid, 'svc_order_sub' ) );
        echo '<div class="svc-order form-contact svc-form" data-svc-form="full"><form method="post">';
        echo '<div class="svc-order__rows">';
        echo '<div><label class="svc-lbl" for="svco-name">' . esc_html( svc_field( $tid, 'svc_lbl_name' ) ) . '</label>'
           . '<input class="svc-field" id="svco-name" type="text" name="names" required></div>';
        echo '<div><label class="svc-lbl" for="svco-phone">' . esc_html( svc_field( $tid, 'svc_lbl_phone' ) ) . '</label>'
           . '<input class="svc-field" id="svco-phone" type="tel" name="phone" required></div>';
        echo '<div><label class="svc-lbl" for="svco-email">' . esc_html( svc_field( $tid, 'svc_lbl_email' ) ) . '</label>'
           . '<input class="svc-field" id="svco-email" type="email" name="email"></div>';
        echo '<div><label class="svc-lbl" for="svco-city">' . esc_html( svc_field( $tid, 'svc_lbl_city' ) ) . '</label>'
           . '<input class="svc-field" id="svco-city" type="text" name="address"></div>';
        echo '<div class="svc-full"><label class="svc-lbl" for="svco-msg">' . esc_html( svc_field( $tid, 'svc_lbl_details' ) ) . '</label>'
           . '<textarea class="svc-field" id="svco-msg" name="message"></textarea></div>';
        $ord_btn = svc_field( $tid, 'svc_order_btn' );
        if ( $ord_btn ) {
            echo '<div class="svc-full"><button type="submit" class="svc-btn svc-btn--brand">' . esc_html( $ord_btn ) . '</button></div>';
        }
        echo '</div></form></div>';
        echo '</div></section>';
    }

    /* ---------- ٨ المقالات — نفس الاستعلام والبطاقة بلا تغيير ---------- */
    echo '<section class="svc-section svc-posts"><div class="container">';
    $posts_title = svc_field( $tid, 'svc_posts_title' );
    if ( $posts_title ) {
        echo '<div class="titles_concept"><div class="titles_concept_1"><h2>' . esc_html( $posts_title ) . '</h2></div></div>';
    }
    $tpl->Part( 'Posts', array(
        'AutoLoadmore'  => true,
        'post__not_in'  => array( $obj->term_id ),
        'UniqId'        => uniqid(),
        'term'          => array( $obj ),
    ) );
    echo '</div></section>';

    echo '</div>'; // .svc-landing

    svc_schema( $obj, $tid, $nstyle );
}

/** عنوان قسم موحّد. */
function svc_head( $title, $sub = '' ) {
    if ( ! $title ) {
        return;
    }
    echo '<div class="svc-head"><h2>' . esc_html( $title ) . '</h2>';
    if ( $sub ) {
        echo '<p>' . esc_html( $sub ) . '</p>';
    }
    echo '</div>';
}

/** استعلام الأسئلة الخاص بالخدمة. */
function svc_faq_args( $tid ) {
    $count = (int) svc_field( $tid, 'svc_faq_count' );
    if ( $count < 1 ) {
        $count = 6;
    }
    $args = array( 'post_type' => 'faq', 'posts_per_page' => $count );
    // أسئلة مرتبطة بالخدمة إن وُجدت، وإلا أحدث الأسئلة
    if ( has_term( '', 'category', null ) || term_exists( (int) $tid, 'category' ) ) {
        $tagged = get_posts( array_merge( $args, array( 'cat' => $tid, 'fields' => 'ids' ) ) );
        if ( $tagged ) {
            $args['cat'] = $tid;
        }
    }
    return $args;
}

/** هل توجد أسئلة أصلًا؟ */
function svc_faq_posts( $tid ) {
    return get_posts( array_merge( svc_faq_args( $tid ), array( 'fields' => 'ids' ) ) );
}

/* =========================================================================
 * البيانات المنظّمة: Service + FAQPage
 * ====================================================================== */
function svc_schema( $obj, $tid, $nstyle ) {

    $name = svc_field( $tid, 'svc_title' );
    if ( ! $name ) {
        $name = $obj->name;
    }

    $service = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Service',
        'name'     => $name,
        'serviceType' => $obj->name,
        'url'      => get_term_link( $obj ),
        'provider' => array(
            '@type' => 'LocalBusiness',
            'name'  => get_option( 'sitename' ) ? get_option( 'sitename' ) : get_bloginfo( 'name' ),
            'url'   => home_url( '/' ),
        ),
    );

    $desc = svc_field( $tid, 'svc_promise' );
    if ( $desc ) {
        $service['description'] = $desc;
    }
    $phone = get_term_meta( $tid, 'call_number', true );
    if ( empty( $phone ) ) {
        $phone = get_option( 'Phone' );
    }
    if ( $phone ) {
        $service['provider']['telephone'] = $phone;
    }

    // مناطق الخدمة من تصنيف المدن
    $cities = get_term_meta( $tid, 'country', true );
    if ( is_array( $cities ) && $cities ) {
        $areas = array();
        foreach ( $cities as $cid ) {
            $city = get_term( $cid, 'country' );
            if ( $city && ! is_wp_error( $city ) ) {
                $areas[] = array( '@type' => 'City', 'name' => $city->name );
            }
        }
        if ( $areas ) {
            $service['areaServed'] = $areas;
        }
    }

    echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $service, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

    // FAQPage
    $faqs = get_posts( svc_faq_args( $tid ) );
    if ( $faqs ) {
        $items = array();
        foreach ( $faqs as $f ) {
            $answer = trim( wp_strip_all_tags( $f->post_content ) );
            if ( '' === $answer ) {
                continue;
            }
            $items[] = array(
                '@type' => 'Question',
                'name'  => get_the_title( $f->ID ),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => $answer,
                ),
            );
        }
        if ( $items ) {
            $faqpage = array(
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $items,
            );
            echo '<script type="application/ld+json">' . wp_json_encode( $faqpage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        }
    }
}

/* =========================================================================
 * ربط الأسئلة بالخدمات — نفس نمط القالب مع أنواع المحتوى الأخرى
 * ====================================================================== */
add_action( 'init', function () {
    register_taxonomy_for_object_type( 'category', 'faq' );
} );

/* =========================================================================
 * معالج النموذج — مستقل حتى لا يتداخل نموذجا الصفحة
 * المعالج الأصلي يضيف رسالة النجاح إلى كل .form-contact في الصفحة.
 * ====================================================================== */
add_action( 'wp_footer', function () {
    if ( ! is_category() ) {
        return;
    }
    $obj = get_queried_object();
    if ( ! $obj || ! isset( $obj->term_id ) || ! svc_is_landing( $obj->term_id ) ) {
        return;
    }
    ?>
<script>
(function(){
  var forms = document.querySelectorAll('.svc-form form');
  Array.prototype.forEach.call(forms, function(f){
    f.addEventListener('submit', function(e){
      e.preventDefault();
      e.stopPropagation();
      var box = f.closest('.svc-form');
      var btn = f.querySelector('button[type="submit"]');
      if (btn) { btn.disabled = true; btn.style.opacity = '.6'; }
      var xhr = new XMLHttpRequest();
      xhr.open('POST', HomeURL + '/AjaxCenter/sendinfo', true);
      xhr.onreadystatechange = function(){
        if (xhr.readyState !== 4) { return; }
        if (btn) { btn.disabled = false; btn.style.opacity = ''; }
        var msg = '';
        try { msg = JSON.parse(xhr.responseText).output || ''; } catch (err) { msg = ''; }
        if (xhr.status >= 200 && xhr.status < 300 && msg) {
          box.insertAdjacentHTML('beforeend', msg);
          f.reset();
        }
      };
      xhr.send(new FormData(f));
    }, true);
  });
})();
</script>
    <?php
}, 30 );

/* =========================================================================
 * صفحة الأدوات — تعبئة الحقول الفارغة فقط
 * ====================================================================== */

/** المحتوى المبدئي. {name} تُستبدل باسم التصنيف. */
function svc_seed_values() {
    return array(
        'svc_title'        => 'خدمة {name}',
        'svc_promise'      => 'نقدّم {name} على يد فريق مرخّص وبمواد معتمدة، مع ضمان مكتوب ومعاينة مجانية قبل التسعير.',
        'svc_badges'       => "فريق مرخّص\nضمان مكتوب\nمعاينة مجانية",
        'svc_call_label'   => 'اتصل الآن',
        'svc_wa_label'     => 'واتساب',
        'svc_quick_title'  => 'اطلب معاينة مجانية',
        'svc_quick_sub'    => 'نرد عليك خلال دقائق في أوقات العمل.',
        'svc_quick_btn'    => 'أرسل الطلب',
        'svc_quick_note'   => 'لن نشارك بياناتك مع أي جهة',
        'svc_lbl_name'     => 'الاسم بالكامل',
        'svc_lbl_phone'    => 'رقم الجوال',
        'svc_lbl_city'     => 'المدينة / الحي',
        'svc_lbl_email'    => 'البريد الإلكتروني',
        'svc_lbl_details'  => 'تفاصيل الخدمة المطلوبة',
        'svc_stats'        => "12+ | سنة خبرة\n8400+ | عميل خدمناه\n14 | مدينة نغطيها\n6 | أشهر ضمان",
        'svc_features_title' => 'لماذا يختارنا العملاء',
        'svc_features_sub'   => 'كل ميزة هنا وعد نلتزم به ويمكنك قياسه.',
        'svc_features'     => "نصل خلال 90 دقيقة | فريقنا موزّع على كل المدن التي نغطيها، ومتوسط زمن الوصول 90 دقيقة من تأكيد الطلب.\n"
                            . "سعر نهائي قبل البدء | نعاين ونكتب السعر قبل بدء العمل، ولا نضيف أي رسوم بعد الانتهاء.\n"
                            . "ضمان مكتوب 6 أشهر | إن تكررت المشكلة خلال 6 أشهر نعيد الخدمة كاملة دون تكلفة إضافية.\n"
                            . "فنيون بشهادات معتمدة | كل فني اجتاز تدريبًا معتمدًا ومعه بطاقة تعريف يمكنك التحقق منها.\n"
                            . "مواد مرخّصة ومعتمدة | نستخدم مواد مسجّلة لدى الجهات المختصة وآمنة على الأطفال بعد ساعتين.\n"
                            . "متابعة بعد 7 أيام | نتصل بك بعد أسبوع للتأكد من النتيجة، ونعيد الزيارة مجانًا إن لزم الأمر.",
        'svc_steps_title'  => 'كيف نعمل',
        'svc_steps_sub'    => 'أربع خطوات واضحة من طلبك حتى تسليم الضمان.',
        'svc_steps'        => "اطلب الخدمة | اتصال أو واتساب أو نموذج — نرد خلال دقائق في أوقات العمل.\n"
                            . "معاينة وتسعير | نعاين الموقع ونعطيك سعرًا نهائيًا مكتوبًا قبل البدء.\n"
                            . "تنفيذ العمل | فريق مرخّص ينفّذ في الموعد المتفق عليه بمواد معتمدة.\n"
                            . "ضمان ومتابعة | تسلّم الضمان مكتوبًا، ونتابع معك بعد 7 أيام.",
        'svc_cities_title' => 'المدن التي نغطيها',
        'svc_cities_sub'   => 'اختر مدينتك لعرض تفاصيل الخدمة فيها.',
        'svc_faq_title'    => 'الأسئلة الشائعة',
        'svc_faq_sub'      => 'أكثر ما يسأل عنه العملاء قبل الطلب.',
        'svc_faq_count'    => '6',
        'svc_order_title'  => 'اطلب الخدمة الآن',
        'svc_order_sub'    => 'املأ البيانات ويصلك اتصال خلال دقائق.',
        'svc_order_btn'    => 'إرسال الطلب',
        'svc_posts_title'  => 'مقالات {name}',
    );
}

add_action( 'admin_menu', function () {
    add_submenu_page(
        'tools.php',
        'أدوات صفحات الخدمات',
        'أدوات صفحات الخدمات',
        'manage_options',
        'service-landing-tools',
        'svc_tools_page'
    );
} );

function svc_tools_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'غير مصرح.' );
    }

    $report = null;
    if ( isset( $_POST['svc_seed'] ) ) {
        check_admin_referer( 'svc_seed_action' );
        $target = isset( $_POST['svc_term'] ) ? absint( $_POST['svc_term'] ) : 0;
        $report = svc_seed_terms( $target );
    }

    $terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) );

    echo '<div class="wrap" dir="rtl">';
    echo '<h1>أدوات صفحات الخدمات</h1>';
    echo '<p>يملأ الحقول <strong>الفارغة فقط</strong> بمحتوى مبدئي جاهز للتحرير. أي حقل مكتوب لن يُمس إطلاقًا.</p>';

    if ( is_array( $report ) ) {
        echo '<div class="notice notice-success"><p><strong>تم.</strong></p><ul style="margin:0 0 10px 18px;list-style:disc">';
        foreach ( $report as $line ) {
            echo '<li>' . esc_html( $line ) . '</li>';
        }
        echo '</ul></div>';
    }

    echo '<form method="post" style="margin-top:18px">';
    wp_nonce_field( 'svc_seed_action' );
    echo '<table class="form-table"><tr>';
    echo '<th scope="row"><label for="svc_term">التصنيف</label></th><td>';
    echo '<select name="svc_term" id="svc_term">';
    echo '<option value="0">كل التصنيفات المفعَّل فيها وضع صفحة الهبوط</option>';
    foreach ( $terms as $t ) {
        $on = svc_is_landing( $t->term_id ) ? ' — مفعَّل' : '';
        echo '<option value="' . (int) $t->term_id . '">' . esc_html( $t->name . $on ) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">التصنيفات غير المفعَّلة تبقى على قالب الأرشيف الحالي بلا أي تغيير.</p>';
    echo '</td></tr></table>';
    submit_button( 'املأ الحقول الفارغة', 'primary', 'svc_seed' );
    echo '</form>';

    echo '<hr><h2>الحقول التي تُملأ</h2>';
    echo '<p>' . esc_html( implode( ' · ', array_keys( svc_seed_values() ) ) ) . '</p>';
    echo '</div>';
}

/**
 * يملأ الحقول الفارغة. يعيد تقريرًا بما مُلئ وما تُرك.
 * لا يكتب فوق أي قيمة موجودة.
 */
function svc_seed_terms( $term_id = 0 ) {
    $targets = array();

    if ( $term_id ) {
        $targets[] = $term_id;
    } else {
        foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) ) as $t ) {
            if ( svc_is_landing( $t->term_id ) ) {
                $targets[] = $t->term_id;
            }
        }
    }

    if ( ! $targets ) {
        return array( 'لا يوجد تصنيف مفعَّل عليه وضع صفحة الهبوط — فعّله من شاشة تحرير التصنيف أولًا.' );
    }

    $report = array();
    foreach ( $targets as $tid ) {
        $term = get_term( $tid, 'category' );
        if ( ! $term || is_wp_error( $term ) ) {
            continue;
        }
        $filled = 0;
        $kept   = 0;
        foreach ( svc_seed_values() as $key => $val ) {
            $current = get_term_meta( $tid, $key, true );
            if ( '' !== trim( (string) $current ) ) {
                $kept++;
                continue; // مكتوب — لا نمسه
            }
            update_term_meta( $tid, $key, str_replace( '{name}', $term->name, $val ) );
            $filled++;
        }
        $report[] = sprintf( '%s: مُلئ %d حقلًا، وتُرك %d حقلًا مكتوبًا كما هو.', $term->name, $filled, $kept );
    }
    return $report;
}
