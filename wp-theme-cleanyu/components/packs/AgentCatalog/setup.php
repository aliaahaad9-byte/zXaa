<?php
/**
 * كتالوج وكلاء الذكاء الاصطناعي (Agentic Resource Discovery — ARD).
 *
 * يخدم /.well-known/ai-catalog.json (وكذلك /.well-known/ard.json في الإصدار الأحدث من المواصفة)
 * كملف JSON صالح بدل صفحة 404، وهو ما يفحصه Lighthouse / PageSpeed في قسم «Agentic Browsing».
 * يُبنى تلقائيًا من بيانات الموقع — لا يحتاج رفع ملفات ولا تعديل الخادم.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** مقطع صالح لمعرّف URN: حروف لاتينية وأرقام و . _ - فقط. */
function yc_ard_slug( $s, $fallback ) {
    $s = strtolower( preg_replace( '/[^a-zA-Z0-9._-]+/', '-', (string) $s ) );
    $s = trim( $s, '-.' );
    return '' !== $s ? $s : $fallback;
}

/** رابط صفحة منشورة تستخدم نموذجًا معينًا (calculator / offers). */
function yc_ard_page_by_template( $tpl ) {
    $ids = get_posts( array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_key'       => 'template',
        'meta_value'     => $tpl,
        'fields'         => 'ids',
    ) );
    return $ids ? $ids[0] : 0;
}

/** الكتالوج: مدخل واحد من النوع القياسي agent-skills يشير إلى دليل Markdown للموقع. */
function yc_ard_catalog() {
    $host      = (string) wp_parse_url( home_url(), PHP_URL_HOST );
    $publisher = yc_ard_slug( preg_replace( '/^www\./', '', $host ), 'site' );
    $name      = get_bloginfo( 'name' );
    $desc      = trim( wp_strip_all_tags( get_bloginfo( 'description' ) ) );
    $modified  = get_lastpostmodified( 'gmt' );
    $updated   = $modified ? gmdate( 'Y-m-d\TH:i:s\Z', strtotime( $modified ) ) : gmdate( 'Y-m-d\TH:i:s\Z' );
    $label     = $name ? $name : $host;

    $entry = array(
        'identifier'            => 'urn:air:' . $publisher . ':skills:site-guide',
        'displayName'           => $label . ' — دليل الخدمات والحجز',
        'type'                  => 'text/markdown; profile="urn:air:agent-skills"',
        'url'                   => home_url( '/.well-known/agent-skills/site-guide.md' ),
        'description'           => '' !== $desc ? $desc : 'خدمات منزلية: التنظيف، مكافحة الحشرات، كشف التسربات، والعزل — مع الأسعار وطرق الحجز.',
        'tags'                  => array( 'home-services', 'cleaning', 'pest-control', 'leak-detection', 'insulation', 'saudi-arabia' ),
        'representativeQueries' => array(
            'كم سعر رش المبيدات للشقة؟',
            'أريد حجز خدمة تنظيف منزل',
            'شركة كشف تسربات المياه بالأجهزة',
            'ما هي عروض العزل الحالية؟',
        ),
        'updatedAt'             => $updated,
    );

    $catalog = array(
        'specVersion' => '1.0',
        'host'        => array(
            'displayName'      => $label,
            'documentationUrl' => home_url( '/' ),
        ),
        'entries'     => array( $entry ),
    );
    $logo = get_option( 'logo' );
    $logo = is_array( $logo ) && isset( $logo['url'] ) ? $logo['url'] : $logo;
    if ( is_string( $logo ) && preg_match( '#^https?://#', $logo ) ) {
        $catalog['host']['logoUrl'] = $logo;
    }
    return apply_filters( 'yc_ard_catalog', $catalog );
}

/** دليل Markdown للوكلاء — يُبنى من بيانات الموقع الحالية. */
function yc_ard_guide_md() {
    $name  = get_bloginfo( 'name' );
    $desc  = trim( wp_strip_all_tags( get_bloginfo( 'description' ) ) );
    $phone = trim( (string) get_option( 'Phone' ) );
    $wa    = trim( (string) get_option( 'Whatsapp' ) );
    $mail  = trim( (string) get_option( 'email' ) );
    $addr  = trim( wp_strip_all_tags( (string) get_option( 'Adress' ) ) );

    $md  = '# ' . $name . "\n\n";
    if ( '' !== $desc ) {
        $md .= $desc . "\n\n";
    }
    $md .= "## Contact & booking\n\n";
    if ( '' !== $phone ) { $md .= '- Phone: ' . $phone . "\n"; }
    if ( '' !== $wa )    { $md .= '- WhatsApp: ' . $wa . ( function_exists( 'yc_pt_wa_number' ) && yc_pt_wa_number() ? ' (https://wa.me/' . yc_pt_wa_number() . ')' : '' ) . "\n"; }
    if ( '' !== $mail )  { $md .= '- Email: ' . $mail . "\n"; }
    if ( '' !== $addr )  { $md .= '- Address: ' . $addr . "\n"; }
    $md .= '- Website: ' . home_url( '/' ) . "\n\n";

    // الخدمات والأسعار من الحاسبة — فقط بعد أن يحفظ صاحب الموقع أسعاره الحقيقية
    // (الأسعار الافتراضية أمثلة لا تُنشر للوكلاء)
    if ( function_exists( 'yc_calc_data' ) && is_array( get_option( 'yc_calc_data' ) ) ) {
        $d = yc_calc_data();
        if ( ! empty( $d['cats'] ) ) {
            $md .= "## Services & indicative prices (" . $d['currency'] . ")\n\n";
            foreach ( $d['cats'] as $cat ) {
                $md .= '### ' . $cat['name'] . "\n\n";
                foreach ( $cat['items'] as $it ) {
                    $md .= '- ' . $it['name'] . ': ' . yc_pt_num( $it['price'] ) . ' ' . $d['currency'] . ( '' !== $it['unit'] ? ' / ' . $it['unit'] : '' );
                    if ( '' !== $it['desc'] ) { $md .= ' — ' . $it['desc']; }
                    $md .= "\n";
                }
                $md .= "\n";
            }
            if ( ! empty( $d['vat_on'] ) && (float) $d['vat'] > 0 ) {
                $md .= 'Prices exclude VAT (' . yc_pt_num( $d['vat'] ) . "%). Final price is confirmed after inspection.\n\n";
            }
        }
    }

    // الأقسام الرئيسية
    $cats = get_terms( array( 'taxonomy' => 'category', 'parent' => 0, 'hide_empty' => true ) );
    if ( ! is_wp_error( $cats ) && $cats ) {
        $md .= "## Service categories\n\n";
        foreach ( $cats as $c ) {
            $md .= '- [' . $c->name . '](' . get_term_link( $c ) . ")\n";
        }
        $md .= "\n";
    }

    // المدن
    $cities = get_terms( array( 'taxonomy' => 'country', 'hide_empty' => false ) );
    if ( ! is_wp_error( $cities ) && $cities ) {
        $md .= "## Cities served\n\n";
        foreach ( $cities as $c ) {
            $link = get_term_link( $c );
            $md  .= '- ' . ( is_wp_error( $link ) ? $c->name : '[' . $c->name . '](' . $link . ')' ) . "\n";
        }
        $md .= "\n";
    }

    // صفحات مفيدة
    $md .= "## Useful pages\n\n";
    foreach ( array( 'calculator' => 'Price calculator', 'offers' => 'Current offers' ) as $tpl => $lbl ) {
        $pid = yc_ard_page_by_template( $tpl );
        if ( $pid ) { $md .= '- ' . $lbl . ': ' . get_permalink( $pid ) . "\n"; }
    }
    $md .= '- Articles feed (RSS): ' . get_feed_link() . "\n";
    return apply_filters( 'yc_ard_guide_md', $md );
}

/* يُخدم مبكرًا قبل أن يحوّل ووردبريس الطلب إلى صفحة 404 */
add_action( 'init', function () {
    $path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
    $base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
    $rel  = '/' . ltrim( substr( $path, strlen( rtrim( $base, '/' ) ) ), '/' );
    $is_catalog = in_array( $rel, array( '/.well-known/ai-catalog.json', '/.well-known/ard.json' ), true );
    $is_guide   = ( '/.well-known/agent-skills/site-guide.md' === $rel );
    if ( ! $is_catalog && ! $is_guide ) {
        return;
    }
    while ( ob_get_level() > 0 ) {
        ob_end_clean();
    }
    status_header( 200 );
    if ( $is_guide ) {
        header( 'Content-Type: text/markdown; charset=utf-8' );
        header( 'Access-Control-Allow-Origin: *' );
        header( 'X-Robots-Tag: noindex' );
        echo yc_ard_guide_md(); // phpcs:ignore -- نص Markdown عادي
        exit;
    }
    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Access-Control-Allow-Origin: *' );
    header( 'Cache-Control: public, max-age=3600' );
    header( 'X-Robots-Tag: noindex' );
    echo wp_json_encode( yc_ard_catalog(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    exit;
}, 0 );

/* رابط الاكتشاف في <head> */
add_action( 'wp_head', function () {
    echo '<link rel="ai-catalog" type="application/json" href="' . esc_url( home_url( '/.well-known/ai-catalog.json' ) ) . '" />' . "\n";
}, 2 );
