<?php
/**
 * نموذج الصفحة: حاسبة الأسعار.
 * البيانات من «الحاسبة والعروض ← حاسبة الأسعار» في لوحة التحكم.
 */
if ( ! function_exists( 'yc_calc_data' ) ) {
    return;
}
global $post;
$d      = yc_calc_data();
$cur    = $d['currency'];
$wa     = yc_pt_wa_number( $d['wa'] );
$phone  = yc_pt_phone();
$cities = get_terms( array( 'taxonomy' => 'country', 'hide_empty' => false ) );
$cities = is_wp_error( $cities ) ? array() : $cities;
$uid    = 'ycc' . substr( md5( uniqid( '', true ) ), 0, 6 );

$css = get_template_directory() . '/components/packs/PricingTools/tools.css';
if ( file_exists( $css ) ) {
    echo '<style>' . file_get_contents( $css ) . '</style>';
}

echo '<section class="yc-pt-page yc-calc" id="' . esc_attr( $uid ) . '"'
    . ' data-cur="' . esc_attr( $cur ) . '"'
    . ' data-vat="' . esc_attr( $d['vat_on'] ? (float) $d['vat'] : 0 ) . '"'
    . ' data-min="' . esc_attr( (float) $d['min_order'] ) . '"'
    . ' data-wa="' . esc_attr( $wa ) . '"'
    . ' data-url="' . esc_attr( get_permalink( $post ) ) . '">';
echo '<div class="container">';

    echo '<div class="yc-pt-crumb"><breadcrumb>';
        Breadcrumb();
    echo '</breadcrumb></div>';

    echo '<div class="yc-pt-head">';
        echo '<h1>' . esc_html( get_the_title( $post ) ) . '</h1>';
        $content = trim( (string) $post->post_content );
        if ( '' !== $content ) {
            echo '<div class="yc-pt-intro">' . apply_filters( 'the_content', $content ) . '</div>';
        }
    echo '</div>';

    if ( empty( $d['cats'] ) ) {
        echo '<p class="yc-pt-empty">لم تُضف خدمات للحاسبة بعد.</p>';
    } else {

    /* التبويبات */
    echo '<div class="yc-calc__tabs" role="tablist" aria-label="أقسام الخدمات">';
    foreach ( array_values( $d['cats'] ) as $c => $cat ) {
        echo '<button type="button" role="tab" id="' . $uid . '-t' . $c . '" aria-controls="' . $uid . '-p' . $c . '" aria-selected="' . ( 0 === $c ? 'true' : 'false' ) . '"' . ( 0 === $c ? '' : ' tabindex="-1"' ) . '>'
            . '<i>' . yc_pt_icon( $cat['icon'] ) . '</i><span>' . esc_html( $cat['name'] ) . '</span><b class="yc-calc__tabcount" hidden>0</b></button>';
    }
    echo '</div>';

    echo '<a class="yc-calc__mini" href="#' . esc_attr( $uid ) . '-sum" hidden><span>الإجمالي التقديري: <b>0</b></span><em>عرض الطلب ↓</em></a>';

    echo '<div class="yc-calc__body">';

        /* الخدمات */
        echo '<div class="yc-calc__panels">';
        foreach ( array_values( $d['cats'] ) as $c => $cat ) {
            echo '<div class="yc-calc__panel" role="tabpanel" id="' . $uid . '-p' . $c . '" aria-labelledby="' . $uid . '-t' . $c . '"' . ( 0 === $c ? '' : ' hidden' ) . '>';
            if ( empty( $cat['items'] ) ) {
                echo '<p class="yc-pt-empty">لا توجد خدمات في هذا القسم.</p>';
            }
            foreach ( $cat['items'] as $i => $it ) {
                $key = $c . '-' . $i;
                echo '<div class="yc-calc__item" data-key="' . esc_attr( $key ) . '" data-cat="' . $c . '" data-name="' . esc_attr( $it['name'] ) . '" data-unit="' . esc_attr( $it['unit'] ) . '" data-price="' . esc_attr( (float) $it['price'] ) . '" data-min="' . esc_attr( max( 1, (int) $it['min'] ) ) . '">';
                    echo '<div class="yc-calc__info">';
                        echo '<h3>' . esc_html( $it['name'] ) . '</h3>';
                        if ( '' !== $it['desc'] ) {
                            echo '<p>' . esc_html( $it['desc'] ) . '</p>';
                        }
                        echo '<span class="yc-calc__price"><b>' . esc_html( yc_pt_num( $it['price'] ) ) . '</b> ' . esc_html( $cur ) . ( '' !== $it['unit'] ? ' / ' . esc_html( $it['unit'] ) : '' ) . '</span>';
                        if ( (int) $it['min'] > 1 ) {
                            echo '<span class="yc-calc__minq">أقل كمية: ' . esc_html( yc_pt_num( $it['min'] ) ) . ' ' . esc_html( $it['unit'] ) . '</span>';
                        }
                    echo '</div>';
                    echo '<div class="yc-calc__qty">';
                        echo '<button type="button" class="yc-calc__minus" aria-label="إنقاص ' . esc_attr( $it['name'] ) . '">−</button>';
                        echo '<input type="number" inputmode="numeric" min="0" step="1" value="0" aria-label="كمية ' . esc_attr( $it['name'] ) . ( '' !== $it['unit'] ? ' (' . esc_attr( $it['unit'] ) . ')' : '' ) . '">';
                        echo '<button type="button" class="yc-calc__plus" aria-label="زيادة ' . esc_attr( $it['name'] ) . '">+</button>';
                    echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';

        /* الملخص */
        echo '<aside class="yc-calc__sum" id="' . esc_attr( $uid ) . '-sum" aria-live="polite">';
            echo '<h2>ملخص الطلب</h2>';
            echo '<p class="yc-calc__none">اختر الخدمات والكميات لحساب التكلفة التقديرية.</p>';
            echo '<ul class="yc-calc__lines"></ul>';
            echo '<dl class="yc-calc__totals" hidden>';
                echo '<div><dt>المجموع</dt><dd class="yc-calc__sub">0</dd></div>';
                if ( $d['vat_on'] && (float) $d['vat'] > 0 ) {
                    echo '<div><dt>ضريبة القيمة المضافة (' . esc_html( yc_pt_num( $d['vat'] ) ) . '%)</dt><dd class="yc-calc__vat">0</dd></div>';
                }
                echo '<div class="yc-calc__grand"><dt>الإجمالي التقديري</dt><dd class="yc-calc__total">0</dd></div>';
            echo '</dl>';
            echo '<p class="yc-calc__minmsg" hidden>الحد الأدنى للطلب ' . esc_html( yc_pt_num( $d['min_order'] ) . ' ' . $cur ) . '.</p>';
            if ( $cities ) {
                echo '<label class="yc-calc__city"><span>المدينة</span><select>';
                echo '<option value="">اختر مدينتك</option>';
                foreach ( $cities as $ct ) {
                    echo '<option>' . esc_html( $ct->name ) . '</option>';
                }
                echo '</select></label>';
            }
            if ( $wa ) {
                echo '<a class="yc-pt-btn yc-pt-btn--wa yc-calc__wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener" aria-disabled="true">' . yc_pt_wa_svg() . 'احجز عبر واتساب</a>';
            }
            if ( $phone ) {
                echo '<a class="yc-pt-btn yc-pt-btn--call" href="tel:' . esc_attr( $phone ) . '">' . yc_pt_call_svg() . 'اتصل للاستفسار</a>';
            }
            echo '<button type="button" class="yc-calc__reset" hidden>مسح الاختيارات</button>';
            if ( '' !== trim( $d['note'] ) ) {
                echo '<p class="yc-calc__note">' . esc_html( $d['note'] ) . '</p>';
            }
        echo '</aside>';

    echo '</div>';
    }

echo '</div>';
echo '</section>';

$js = get_template_directory() . '/components/packs/PricingTools/calc.js';
if ( file_exists( $js ) && ! empty( $d['cats'] ) ) {
    echo '<script>' . file_get_contents( $js ) . '</script>';
}
