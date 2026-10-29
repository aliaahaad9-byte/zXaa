<?php
/**
 * زر «سابقة أعمالنا» العائم — على الجوال فقط، بنفس مقاس زرّي الاتصال والواتساب.
 * الإعدادات: سابقة الأعمال ← زر سابقة أعمالنا العائم.
 * يُرسم من #footer/part.php عبر yc_fw_button( 'stack' | 'right' ).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function yc_fw_defaults() {
    return array(
        'enabled'     => 1,
        'text'        => 'سابقة أعمالنا',
        'url'         => '',
        'mode'        => 'all',      // all | cities
        'cities'      => array(),
        'show_home'   => 1,
        'show_nocity' => 1,
        'color'       => 'orange',
        'custom'      => '#c2570c',
        'position'    => 'stack',    // stack = مع زرّي الاتصال والواتساب · right = أقصى اليمين
    );
}

function yc_fw_colors() {
    return array(
        'orange' => array( 'برتقالي محروق', '#c2570c' ),
        'purple' => array( 'بنفسجي', '#6d3fc4' ),
        'olive'  => array( 'أخضر زيتي', '#0b6e57' ),
        'navy'   => array( 'كحلي', '#0b1a3a' ),
    );
}

function yc_fw_options() {
    $o = get_option( 'yc_fw' );
    return wp_parse_args( is_array( $o ) ? $o : array(), yc_fw_defaults() );
}

function yc_fw_color( $o ) {
    $c = yc_fw_colors();
    if ( 'custom' === $o['color'] && preg_match( '/^#[0-9a-fA-F]{6}$/', $o['custom'] ) ) {
        return $o['custom'];
    }
    return isset( $c[ $o['color'] ] ) ? $c[ $o['color'] ][1] : $c['orange'][1];
}

function yc_fw_url( $o ) {
    if ( '' !== trim( $o['url'] ) ) {
        return $o['url'];
    }
    if ( function_exists( 'yc_ard_page_by_template' ) ) {
        $id = yc_ard_page_by_template( 'projects' );
        if ( $id ) {
            return get_permalink( $id );
        }
    }
    return home_url( '/works/' );
}

/** هل يظهر الزر في الصفحة الحالية؟ */
function yc_fw_visible( $o ) {
    if ( empty( $o['enabled'] ) ) {
        return false;
    }
    // لا حاجة للزر في الصفحة التي يشير إليها
    $here = untrailingslashit( strtok( (string) ( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '' ), '?' ) );
    $to   = untrailingslashit( (string) wp_parse_url( yc_fw_url( $o ), PHP_URL_PATH ) );
    if ( '' !== $to && rawurldecode( $here ) === rawurldecode( $to ) ) {
        return false;
    }
    if ( 'cities' !== $o['mode'] ) {
        return true;
    }
    $city = function_exists( 'yc_city_context' ) ? yc_city_context() : null;
    if ( $city ) {
        return in_array( (int) $city->term_id, array_map( 'intval', (array) $o['cities'] ), true );
    }
    if ( is_front_page() || is_home() ) {
        return ! empty( $o['show_home'] );
    }
    return ! empty( $o['show_nocity'] );
}

/** يعيد HTML الزر إن كان موضعه $slot وكان ظاهرًا. */
function yc_fw_button( $slot ) {
    $o = yc_fw_options();
    if ( $o['position'] !== $slot || ! yc_fw_visible( $o ) ) {
        return '';
    }
    static $css = false;
    $color = yc_fw_color( $o );
    $out   = '';
    if ( ! $css ) {
        $css  = true;
        $out .= '<style>'
            . '.btn-works{display:none}'
            . '.btn-works a{width:100%;display:flex;text-decoration:none}'
            . '.btn-works span{padding:0 15px 0 20px;font-size:15px;font-weight:600;display:inline-block;background:var(--yc-fw,#c2570c);margin-left:-13px;line-height:28px;border-radius:10px;height:35px;margin-top:9px;border:3px solid #fff;color:#fff;box-shadow:0 8px 16px 0 rgba(0,0,0,.2),0 6px 20px 0 rgba(0,0,0,.19);white-space:nowrap}'
            . '.btn-works .footer-header{background:var(--yc-fw,#c2570c);animation:none}'
            . '.btn-works .footer-header svg{width:100%;height:30px}'
            . '.btn-works a:focus-visible{outline:3px solid #2764c380;outline-offset:3px;border-radius:14px}'
            . '.btn-works--stack{position:absolute;left:0;bottom:100%;margin-bottom:17px}'
            . '.btn-works--right{position:fixed;right:20px;bottom:5vh;z-index:2}'
            . '.btn-works--right a{flex-direction:row-reverse}'
            . '.btn-works--right span{padding:0 20px 0 15px;margin-left:0;margin-right:-13px}'
            . '@media only screen and (max-width:780px){.btn-works{display:block}}'
            . ( 'right' === $slot ? '@media only screen and (max-width:780px){body #button{bottom:calc(5vh + 70px)}}' : '' )
            . '</style>';
    }
    $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M3 15l5-4.5 4 3.5 3-2.5 6 5"/><circle cx="16.5" cy="8.8" r="1.4" fill="#fff" stroke="none"/></svg>';
    $out .= '<div class="btn-works btn-works--' . esc_attr( $slot ) . '" style="--yc-fw:' . esc_attr( $color ) . '">'
        . '<a href="' . esc_url( yc_fw_url( $o ) ) . '" aria-label="' . esc_attr( $o['text'] ) . '">'
        . '<span>' . esc_html( $o['text'] ) . '</span>'
        . '<div class="footer-header">' . $icon . '</div>'
        . '</a></div>';
    return $out;
}

/* ---------- لوحة التحكم ---------- */
add_action( 'admin_menu', function () {
    add_submenu_page( 'edit.php?post_type=works', 'زر سابقة أعمالنا العائم', 'زر سابقة أعمالنا العائم', 'manage_options', 'yc-fw', 'yc_fw_page' );
}, 30 );

add_action( 'admin_init', function () {
    if ( empty( $_POST['yc_fw_save'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    check_admin_referer( 'yc_fw_save' );
    $in = isset( $_POST['fw'] ) && is_array( $_POST['fw'] ) ? wp_unslash( $_POST['fw'] ) : array();
    $d  = yc_fw_defaults();
    $o  = array(
        'enabled'     => empty( $in['enabled'] ) ? 0 : 1,
        'text'        => isset( $in['text'] ) && '' !== trim( $in['text'] ) ? sanitize_text_field( $in['text'] ) : $d['text'],
        'url'         => isset( $in['url'] ) ? esc_url_raw( trim( $in['url'] ) ) : '',
        'mode'        => isset( $in['mode'] ) && 'cities' === $in['mode'] ? 'cities' : 'all',
        'cities'      => isset( $in['cities'] ) && is_array( $in['cities'] ) ? array_values( array_unique( array_map( 'absint', $in['cities'] ) ) ) : array(),
        'show_home'   => empty( $in['show_home'] ) ? 0 : 1,
        'show_nocity' => empty( $in['show_nocity'] ) ? 0 : 1,
        'color'       => isset( $in['color'] ) && ( isset( yc_fw_colors()[ $in['color'] ] ) || 'custom' === $in['color'] ) ? $in['color'] : 'orange',
        'custom'      => isset( $in['custom'] ) && preg_match( '/^#[0-9a-fA-F]{6}$/', $in['custom'] ) ? $in['custom'] : $d['custom'],
        'position'    => isset( $in['position'] ) && 'right' === $in['position'] ? 'right' : 'stack',
    );
    update_option( 'yc_fw', $o, false );
    wp_safe_redirect( add_query_arg( array( 'post_type' => 'works', 'page' => 'yc-fw', 'saved' => 1 ), admin_url( 'edit.php' ) ) );
    exit;
} );

function yc_fw_page() {
    $o      = yc_fw_options();
    $cities = taxonomy_exists( 'country' ) ? get_terms( array( 'taxonomy' => 'country', 'hide_empty' => false ) ) : array();
    $cities = is_wp_error( $cities ) ? array() : $cities;
    $colors = yc_fw_colors();
    echo '<div class="wrap" style="max-width:900px"><h1>زر «سابقة أعمالنا» العائم</h1>';
    if ( isset( $_GET['saved'] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>تم الحفظ.</p></div>';
    }
    echo '<p class="description">يظهر الزر <b>على الجوال فقط</b> (عرض الشاشة 780 بكسل فأقل)، بنفس مقاس زرّي الاتصال والواتساب.</p>';
    echo '<form method="post">';
    wp_nonce_field( 'yc_fw_save' );
    echo '<input type="hidden" name="yc_fw_save" value="1">';

    $box = 'background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:16px 22px;margin:14px 0';
    echo '<div style="' . $box . '"><h2 style="margin-top:0">الإظهار</h2>';
    echo '<p><label><input type="checkbox" name="fw[enabled]" value="1" ' . checked( $o['enabled'], 1, false ) . '> <b>إظهار الزر في الموقع</b> (أطفئه لإخفائه من كل الصفحات)</label></p>';
    echo '<p><label><b>نص الزر</b><br><input type="text" class="regular-text" name="fw[text]" value="' . esc_attr( $o['text'] ) . '"></label></p>';
    echo '<p><label><b>رابط الزر</b> <span class="description">(اتركه فارغًا لصفحة سابقة الأعمال)</span><br><input type="url" dir="ltr" class="regular-text" style="min-width:380px" name="fw[url]" value="' . esc_attr( $o['url'] ) . '" placeholder="' . esc_attr( yc_fw_url( array_merge( $o, array( 'url' => '' ) ) ) ) . '"></label></p></div>';

    echo '<div style="' . $box . '"><h2 style="margin-top:0">الموضع</h2>';
    echo '<p><label><input type="radio" name="fw[position]" value="stack" ' . checked( $o['position'], 'stack', false ) . '> مع زرّي الاتصال والواتساب (يسار الشاشة، فوقهما)</label></p>';
    echo '<p><label><input type="radio" name="fw[position]" value="right" ' . checked( $o['position'], 'right', false ) . '> أقصى اليمين (فوق زر العودة للأعلى) وتنتقل الأيقونة لليمين</label></p></div>';

    echo '<div style="' . $box . '"><h2 style="margin-top:0">أين يظهر؟</h2>';
    echo '<p><label><input type="radio" name="fw[mode]" value="all" ' . checked( $o['mode'], 'all', false ) . '> كل الصفحات</label><br>';
    echo '<label><input type="radio" name="fw[mode]" value="cities" ' . checked( $o['mode'], 'cities', false ) . '> مدن محددة فقط <span class="description">(تُعرَف المدينة من أرشيف المدينة، أو من مقال مرتبط بمدينة، أو من اسم المدينة في عنوان الصفحة)</span></label></p>';
    if ( $cities ) {
        echo '<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px 14px;margin:8px 0 12px">';
        foreach ( $cities as $c ) {
            echo '<label><input type="checkbox" name="fw[cities][]" value="' . (int) $c->term_id . '" ' . checked( in_array( (int) $c->term_id, array_map( 'intval', (array) $o['cities'] ), true ), true, false ) . '> ' . esc_html( $c->name ) . '</label>';
        }
        echo '</div>';
    } else {
        echo '<p class="description">لا توجد مدن مضافة بعد (المقالات ← المدن).</p>';
    }
    echo '<p><label><input type="checkbox" name="fw[show_home]" value="1" ' . checked( $o['show_home'], 1, false ) . '> إظهاره أيضًا في الصفحة الرئيسية</label><br>';
    echo '<label><input type="checkbox" name="fw[show_nocity]" value="1" ' . checked( $o['show_nocity'], 1, false ) . '> إظهاره في الصفحات التي ليس لها مدينة (من نحن، الأسعار …)</label></p>';
    echo '<p class="description">هذان الخياران يُطبَّقان عند اختيار «مدن محددة فقط».</p></div>';

    echo '<div style="' . $box . '"><h2 style="margin-top:0">اللون</h2><p style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">';
    foreach ( $colors as $k => $c ) {
        echo '<label style="border:2px solid #dcdcde;border-radius:10px;padding:6px 12px"><input type="radio" name="fw[color]" value="' . esc_attr( $k ) . '" ' . checked( $o['color'], $k, false ) . '> <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:' . esc_attr( $c[1] ) . ';vertical-align:-2px"></span> ' . esc_html( $c[0] ) . '</label>';
    }
    echo '<label style="border:2px solid #dcdcde;border-radius:10px;padding:6px 12px"><input type="radio" name="fw[color]" value="custom" ' . checked( $o['color'], 'custom', false ) . '> لون مخصص <input type="color" name="fw[custom]" value="' . esc_attr( $o['custom'] ) . '" style="vertical-align:middle"></label>';
    echo '</p><p class="description">اختر لونًا غامقًا بما يكفي ليظهر النص الأبيض بوضوح.</p></div>';

    echo '<p class="submit"><button class="button button-primary button-large">حفظ التغييرات</button></p></form></div>';
}
