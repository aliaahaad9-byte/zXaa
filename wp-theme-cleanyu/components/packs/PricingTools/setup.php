<?php
/**
 * حاسبة الأسعار + صفحة العروض.
 *
 * - البيانات تُحرَّر من لوحة التحكم: «الحاسبة والعروض».
 * - العرض يتم عبر نموذجي الصفحة: @models/calculator.php و @models/offers.php
 *   (يُختاران من «خصائص ← النموذج» في محرر الصفحة).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =========================================================================
 * البيانات الافتراضية (أمثلة — تُعدَّل من لوحة التحكم)
 * ====================================================================== */
function yc_calc_defaults() {
    $i = function ( $name, $unit, $price, $desc = '', $min = 1 ) {
        return array( 'name' => $name, 'unit' => $unit, 'price' => $price, 'desc' => $desc, 'min' => $min );
    };
    return array(
        'vat'       => 15,
        'vat_on'    => 1,
        'min_order' => 0,
        'currency'  => 'ر.س',
        'wa'        => '',
        'note'      => 'الأسعار تقديرية وقد تختلف بعد المعاينة حسب حالة الموقع ومساحته. المعاينة مجانية.',
        'cats'      => array(
            array( 'name' => 'خدمات التنظيف', 'icon' => 'cleaning', 'items' => array(
                $i( 'تنظيف فلل', 'م²', 4, 'تنظيف شامل للأرضيات والجدران والنوافذ والحمامات والمطابخ.', 100 ),
                $i( 'تنظيف شقق', 'غرفة', 150, 'تنظيف عميق للغرف مع الحمامات والمطبخ.' ),
                $i( 'تنظيف كنب', 'مقعد', 35, 'غسيل بالبخار وشفط وتعقيم.' ),
                $i( 'تنظيف سجاد وموكيت', 'م²', 12, 'غسيل وتجفيف سريع.' ),
                $i( 'تنظيف خزانات', 'خزان', 350, 'تنظيف وتعقيم الخزانات العلوية والأرضية.' ),
                $i( 'تنظيف مكيفات', 'مكيف', 80, 'غسيل الفلاتر والوحدة الداخلية.' ),
            ) ),
            array( 'name' => 'مكافحة الحشرات', 'icon' => 'pest', 'items' => array(
                $i( 'رش مبيدات شقة', 'شقة', 250, 'مكافحة الصراصير والنمل والحشرات الزاحفة.' ),
                $i( 'رش مبيدات فيلا', 'فيلا', 450, 'رش داخلي وخارجي مع الحديقة.' ),
                $i( 'مكافحة النمل الأبيض', 'م²', 8, 'حقن التربة ومعالجة الأخشاب.', 50 ),
                $i( 'مكافحة بق الفراش', 'غرفة', 200, 'معالجة المراتب والأثاث بزيارتين.' ),
                $i( 'مكافحة القوارض', 'زيارة', 300, 'طعوم آمنة ومصائد ومتابعة.' ),
            ) ),
            array( 'name' => 'كشف التسربات', 'icon' => 'leak', 'items' => array(
                $i( 'كشف تسربات بالأجهزة الإلكترونية', 'زيارة', 350, 'تحديد مكان التسرب بدقة بدون تكسير.' ),
                $i( 'كشف تسربات الخزانات', 'خزان', 300, 'فحص الخزان والتوصيلات.' ),
                $i( 'إصلاح نقطة تسرب', 'نقطة', 250, 'إصلاح التسرب بعد تحديده.' ),
            ) ),
            array( 'name' => 'العزل', 'icon' => 'insulation', 'items' => array(
                $i( 'عزل أسطح مائي', 'م²', 35, 'عزل بالرولات أو المواد السائلة مع ضمان.', 50 ),
                $i( 'عزل حراري فوم', 'م²', 45, 'رش فوم بولي يوريثان.', 50 ),
                $i( 'عزل خزانات', 'خزان', 1200, 'عزل داخلي بمواد آمنة لمياه الشرب.' ),
                $i( 'عزل حمامات', 'حمام', 900, 'عزل الأرضيات قبل التبليط.' ),
            ) ),
        ),
    );
}

function yc_offers_defaults() {
    $expire = gmdate( 'Y-m-d', time() + 30 * DAY_IN_SECONDS );
    return array(
        'wa'           => '',
        'currency'     => 'ر.س',
        'hide_expired' => 1,
        'offers'       => array(
            array( 'title' => 'باقة تنظيف الشقة الشاملة', 'cat' => 'خدمات التنظيف', 'icon' => 'cleaning', 'image' => '', 'old' => 900, 'new' => 649, 'badge' => 'الأكثر طلبًا', 'expires' => $expire, 'featured' => 1,
                'features' => "تنظيف حتى 4 غرف\nتنظيف المطبخ والحمامات\nتنظيف الكنب والسجاد\nتعقيم شامل" ),
            array( 'title' => 'رش مبيدات + ضمان 6 أشهر', 'cat' => 'مكافحة الحشرات', 'icon' => 'pest', 'image' => '', 'old' => 450, 'new' => 299, 'badge' => '', 'expires' => $expire, 'featured' => 0,
                'features' => "رش داخلي وخارجي\nمبيدات آمنة ومعتمدة\nزيارة متابعة مجانية" ),
            array( 'title' => 'كشف تسربات + تقرير فني', 'cat' => 'كشف التسربات', 'icon' => 'leak', 'image' => '', 'old' => 500, 'new' => 350, 'badge' => '', 'expires' => $expire, 'featured' => 0,
                'features' => "كشف بالأجهزة الإلكترونية\nبدون تكسير\nتقرير فني مكتوب" ),
            array( 'title' => 'عزل سطح حتى 100 م²', 'cat' => 'العزل', 'icon' => 'insulation', 'image' => '', 'old' => 4500, 'new' => 3200, 'badge' => 'ضمان 10 سنوات', 'expires' => $expire, 'featured' => 0,
                'features' => "عزل مائي وحراري\nمعاينة مجانية\nضمان مكتوب" ),
        ),
    );
}

/* =========================================================================
 * القراءة
 * ====================================================================== */
function yc_calc_data() {
    $d = get_option( 'yc_calc_data' );
    return is_array( $d ) ? wp_parse_args( $d, yc_calc_defaults() ) : yc_calc_defaults();
}

function yc_offers_data() {
    $d = get_option( 'yc_offers_data' );
    return is_array( $d ) ? wp_parse_args( $d, yc_offers_defaults() ) : yc_offers_defaults();
}

/** رقم واتساب دولي من رقم محلي أو دولي (05xxxxxxxx ← 9665xxxxxxxx). */
function yc_pt_wa_number( $custom = '' ) {
    $n = preg_replace( '/\D/', '', (string) ( '' !== trim( (string) $custom ) ? $custom : get_option( 'Whatsapp' ) ) );
    if ( 0 === strpos( $n, '00' ) ) {
        $n = substr( $n, 2 );
    }
    if ( 0 === strpos( $n, '05' ) && 10 === strlen( $n ) ) {
        $n = '966' . substr( $n, 1 );
    }
    return $n;
}

/** رقم الاتصال العام. */
function yc_pt_phone() {
    return preg_replace( '/[^\d+]/', '', (string) get_option( 'Phone' ) );
}

/** أيقونة (من حزمة AuthorBio إن وُجدت). */
function yc_pt_icon( $key ) {
    return function_exists( 'yc_author_svg' ) ? yc_author_svg( $key ? $key : 'home' ) : '';
}

function yc_pt_icon_choices() {
    return array(
        'cleaning'   => 'تنظيف',
        'pest'       => 'مكافحة حشرات',
        'leak'       => 'كشف تسربات',
        'insulation' => 'عزل',
        'home'       => 'منازل',
        'tools'      => 'صيانة عامة',
    );
}

/** رقم بأرقام لاتينية وفواصل (8,400) مع كسور عند الحاجة. */
function yc_pt_num( $n ) {
    $n = (float) $n;
    return number_format( $n, ( floor( $n ) == $n ) ? 0 : 2 );
}

/* =========================================================================
 * لوحة التحكم
 * ====================================================================== */
add_action( 'admin_menu', function () {
    add_menu_page( 'الحاسبة والعروض', 'الحاسبة والعروض', 'manage_options', 'yc-calc', 'yc_pt_calc_page', 'dashicons-calculator', 59 );
    add_submenu_page( 'yc-calc', 'حاسبة الأسعار', 'حاسبة الأسعار', 'manage_options', 'yc-calc', 'yc_pt_calc_page' );
    add_submenu_page( 'yc-calc', 'العروض', 'العروض', 'manage_options', 'yc-offers', 'yc_pt_offers_page' );
} );

/* ---------- الحفظ ---------- */
add_action( 'admin_init', function () {
    if ( empty( $_POST['yc_pt_action'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $action = sanitize_key( wp_unslash( $_POST['yc_pt_action'] ) );
    check_admin_referer( 'yc_pt_' . $action );
    $post = wp_unslash( $_POST );

    if ( 'calc' === $action ) {
        if ( ! empty( $post['yc_reset'] ) ) {
            delete_option( 'yc_calc_data' );
        } else {
            $in    = isset( $post['calc'] ) && is_array( $post['calc'] ) ? $post['calc'] : array();
            $icons = yc_pt_icon_choices();
            $cats  = array();
            foreach ( ( isset( $in['cats'] ) && is_array( $in['cats'] ) ? $in['cats'] : array() ) as $c ) {
                $cname = isset( $c['name'] ) ? sanitize_text_field( $c['name'] ) : '';
                if ( '' === $cname ) {
                    continue;
                }
                $items = array();
                foreach ( ( isset( $c['items'] ) && is_array( $c['items'] ) ? $c['items'] : array() ) as $it ) {
                    $iname = isset( $it['name'] ) ? sanitize_text_field( $it['name'] ) : '';
                    if ( '' === $iname ) {
                        continue;
                    }
                    $items[] = array(
                        'name'  => $iname,
                        'unit'  => isset( $it['unit'] ) ? sanitize_text_field( $it['unit'] ) : '',
                        'price' => isset( $it['price'] ) ? max( 0, (float) $it['price'] ) : 0,
                        'min'   => isset( $it['min'] ) ? max( 1, (int) $it['min'] ) : 1,
                        'desc'  => isset( $it['desc'] ) ? sanitize_text_field( $it['desc'] ) : '',
                    );
                }
                $icon   = isset( $c['icon'] ) ? sanitize_key( $c['icon'] ) : 'home';
                $cats[] = array( 'name' => $cname, 'icon' => isset( $icons[ $icon ] ) ? $icon : 'home', 'items' => $items );
            }
            update_option( 'yc_calc_data', array(
                'vat'       => isset( $in['vat'] ) ? max( 0, (float) $in['vat'] ) : 15,
                'vat_on'    => empty( $in['vat_on'] ) ? 0 : 1,
                'min_order' => isset( $in['min_order'] ) ? max( 0, (float) $in['min_order'] ) : 0,
                'currency'  => isset( $in['currency'] ) && '' !== trim( $in['currency'] ) ? sanitize_text_field( $in['currency'] ) : 'ر.س',
                'wa'        => isset( $in['wa'] ) ? sanitize_text_field( $in['wa'] ) : '',
                'note'      => isset( $in['note'] ) ? sanitize_textarea_field( $in['note'] ) : '',
                'cats'      => $cats,
            ), false );
        }
        wp_safe_redirect( add_query_arg( array( 'page' => 'yc-calc', 'saved' => 1 ), admin_url( 'admin.php' ) ) );
        exit;
    }

    if ( 'offers' === $action ) {
        if ( ! empty( $post['yc_reset'] ) ) {
            delete_option( 'yc_offers_data' );
        } else {
            $in     = isset( $post['offers'] ) && is_array( $post['offers'] ) ? $post['offers'] : array();
            $icons  = yc_pt_icon_choices();
            $offers = array();
            foreach ( ( isset( $in['list'] ) && is_array( $in['list'] ) ? $in['list'] : array() ) as $o ) {
                $title = isset( $o['title'] ) ? sanitize_text_field( $o['title'] ) : '';
                if ( '' === $title ) {
                    continue;
                }
                $icon    = isset( $o['icon'] ) ? sanitize_key( $o['icon'] ) : 'home';
                $expires = isset( $o['expires'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $o['expires'] ) ? $o['expires'] : '';
                $offers[] = array(
                    'title'    => $title,
                    'cat'      => isset( $o['cat'] ) ? sanitize_text_field( $o['cat'] ) : '',
                    'icon'     => isset( $icons[ $icon ] ) ? $icon : 'home',
                    'image'    => isset( $o['image'] ) ? esc_url_raw( $o['image'] ) : '',
                    'old'      => isset( $o['old'] ) && '' !== $o['old'] ? max( 0, (float) $o['old'] ) : '',
                    'new'      => isset( $o['new'] ) ? max( 0, (float) $o['new'] ) : 0,
                    'badge'    => isset( $o['badge'] ) ? sanitize_text_field( $o['badge'] ) : '',
                    'expires'  => $expires,
                    'featured' => empty( $o['featured'] ) ? 0 : 1,
                    'features' => isset( $o['features'] ) ? sanitize_textarea_field( $o['features'] ) : '',
                );
            }
            update_option( 'yc_offers_data', array(
                'wa'           => isset( $in['wa'] ) ? sanitize_text_field( $in['wa'] ) : '',
                'currency'     => isset( $in['currency'] ) && '' !== trim( $in['currency'] ) ? sanitize_text_field( $in['currency'] ) : 'ر.س',
                'hide_expired' => empty( $in['hide_expired'] ) ? 0 : 1,
                'offers'       => $offers,
            ), false );
        }
        wp_safe_redirect( add_query_arg( array( 'page' => 'yc-offers', 'saved' => 1 ), admin_url( 'admin.php' ) ) );
        exit;
    }
} );

function yc_pt_icon_select( $name, $selected ) {
    $h = '<select name="' . esc_attr( $name ) . '">';
    foreach ( yc_pt_icon_choices() as $k => $l ) {
        $h .= '<option value="' . esc_attr( $k ) . '"' . selected( $selected, $k, false ) . '>' . esc_html( $l ) . '</option>';
    }
    return $h . '</select>';
}

/* ---------- صفوف الحاسبة ---------- */
function yc_pt_item_row( $c, $i, $it ) {
    $it = wp_parse_args( $it, array( 'name' => '', 'unit' => '', 'price' => '', 'min' => 1, 'desc' => '' ) );
    $n  = 'calc[cats][' . $c . '][items][' . $i . ']';
    return '<tr class="yc-pt-item">'
        . '<td><input type="text" name="' . $n . '[name]" value="' . esc_attr( $it['name'] ) . '" placeholder="اسم الخدمة"></td>'
        . '<td><input type="text" name="' . $n . '[desc]" value="' . esc_attr( $it['desc'] ) . '" placeholder="وصف مختصر (اختياري)"></td>'
        . '<td class="yc-pt-sm"><input type="text" name="' . $n . '[unit]" value="' . esc_attr( $it['unit'] ) . '" placeholder="م² / غرفة"></td>'
        . '<td class="yc-pt-sm"><input type="number" step="0.01" min="0" name="' . $n . '[price]" value="' . esc_attr( $it['price'] ) . '"></td>'
        . '<td class="yc-pt-sm"><input type="number" min="1" name="' . $n . '[min]" value="' . esc_attr( $it['min'] ) . '"></td>'
        . '<td class="yc-pt-x"><a href="#" class="yc-pt-del" title="حذف">✕</a></td>'
        . '</tr>';
}

function yc_pt_cat_block( $c, $cat ) {
    $cat = wp_parse_args( $cat, array( 'name' => '', 'icon' => 'home', 'items' => array() ) );
    $h   = '<div class="yc-pt-cat" data-c="' . esc_attr( $c ) . '" data-next="' . count( $cat['items'] ) . '">';
    $h  .= '<div class="yc-pt-cat__head">';
    $h  .= '<label>اسم التبويب<input type="text" name="calc[cats][' . $c . '][name]" value="' . esc_attr( $cat['name'] ) . '" placeholder="مثل: خدمات التنظيف"></label>';
    $h  .= '<label>الأيقونة' . yc_pt_icon_select( 'calc[cats][' . $c . '][icon]', $cat['icon'] ) . '</label>';
    $h  .= '<a href="#" class="yc-pt-del-cat">حذف التبويب</a>';
    $h  .= '</div>';
    $h  .= '<table class="yc-pt-table"><thead><tr><th>الخدمة</th><th>الوصف</th><th>الوحدة</th><th>السعر</th><th>أقل كمية</th><th></th></tr></thead><tbody>';
    foreach ( $cat['items'] as $i => $it ) {
        $h .= yc_pt_item_row( $c, $i, $it );
    }
    $h .= '</tbody></table>';
    $h .= '<p><button type="button" class="button yc-pt-add-item">+ إضافة خدمة</button></p>';
    return $h . '</div>';
}

function yc_pt_calc_page() {
    $d = yc_calc_data();
    echo '<div class="wrap yc-pt"><h1>حاسبة الأسعار</h1>';
    if ( isset( $_GET['saved'] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>تم الحفظ.</p></div>';
    }
    echo '<p class="description">لعرض الحاسبة: أنشئ صفحة، ومن صندوق «خصائص» اختر <b>النموذج ← حاسبة الأسعار</b>. التبويبات هنا تظهر كأقسام في أعلى الحاسبة.</p>';
    echo '<form method="post">';
    wp_nonce_field( 'yc_pt_calc' );
    echo '<input type="hidden" name="yc_pt_action" value="calc">';

    echo '<div class="yc-pt-box"><h2>الإعدادات العامة</h2><div class="yc-pt-grid">';
    echo '<label>العملة<input type="text" name="calc[currency]" value="' . esc_attr( $d['currency'] ) . '"></label>';
    echo '<label>نسبة الضريبة %<input type="number" step="0.01" min="0" name="calc[vat]" value="' . esc_attr( $d['vat'] ) . '"></label>';
    echo '<label class="yc-pt-check"><input type="checkbox" name="calc[vat_on]" value="1" ' . checked( $d['vat_on'], 1, false ) . '> إضافة الضريبة على الإجمالي</label>';
    echo '<label>الحد الأدنى للطلب (0 = بدون)<input type="number" step="1" min="0" name="calc[min_order]" value="' . esc_attr( $d['min_order'] ) . '"></label>';
    echo '<label>رقم واتساب للحجز (اتركه فارغًا لاستخدام رقم القالب)<input type="text" dir="ltr" name="calc[wa]" value="' . esc_attr( $d['wa'] ) . '" placeholder="9665xxxxxxxx"></label>';
    echo '</div><label>ملاحظة أسفل الملخص<textarea name="calc[note]" rows="2">' . esc_textarea( $d['note'] ) . '</textarea></label></div>';

    echo '<div class="yc-pt-box"><h2>التبويبات والخدمات والأسعار</h2>';
    echo '<div id="yc-pt-cats" data-next="' . count( $d['cats'] ) . '">';
    foreach ( array_values( $d['cats'] ) as $c => $cat ) {
        echo yc_pt_cat_block( $c, $cat ); // phpcs:ignore
    }
    echo '</div><p><button type="button" class="button button-secondary" id="yc-pt-add-cat">+ إضافة تبويب</button></p></div>';

    echo '<script type="text/template" id="yc-pt-tpl-cat">' . yc_pt_cat_block( '__c__', array() ) . '</script>'; // phpcs:ignore
    echo '<script type="text/template" id="yc-pt-tpl-item">' . yc_pt_item_row( '__c__', '__i__', array() ) . '</script>'; // phpcs:ignore

    echo '<p class="submit"><button class="button button-primary button-large">حفظ الحاسبة</button> ';
    echo '<button class="button yc-pt-reset" name="yc_reset" value="1">استعادة الأسعار الافتراضية</button></p>';
    echo '</form></div>';
    yc_pt_admin_assets();
}

/* ---------- صفوف العروض ---------- */
function yc_pt_offer_block( $i, $o ) {
    $o = wp_parse_args( $o, array( 'title' => '', 'cat' => '', 'icon' => 'home', 'image' => '', 'old' => '', 'new' => '', 'badge' => '', 'expires' => '', 'featured' => 0, 'features' => '' ) );
    $n = 'offers[list][' . $i . ']';
    $h  = '<div class="yc-pt-cat yc-pt-offer">';
    $h .= '<div class="yc-pt-grid">';
    $h .= '<label>عنوان العرض<input type="text" name="' . $n . '[title]" value="' . esc_attr( $o['title'] ) . '"></label>';
    $h .= '<label>القسم<input type="text" name="' . $n . '[cat]" value="' . esc_attr( $o['cat'] ) . '" placeholder="مثل: خدمات التنظيف"></label>';
    $h .= '<label>الأيقونة' . yc_pt_icon_select( $n . '[icon]', $o['icon'] ) . '</label>';
    $h .= '<label>السعر قبل الخصم<input type="number" step="0.01" min="0" name="' . $n . '[old]" value="' . esc_attr( $o['old'] ) . '"></label>';
    $h .= '<label>سعر العرض<input type="number" step="0.01" min="0" name="' . $n . '[new]" value="' . esc_attr( $o['new'] ) . '"></label>';
    $h .= '<label>شارة (اختياري)<input type="text" name="' . $n . '[badge]" value="' . esc_attr( $o['badge'] ) . '" placeholder="الأكثر طلبًا"></label>';
    $h .= '<label>ينتهي في<input type="date" name="' . $n . '[expires]" value="' . esc_attr( $o['expires'] ) . '"></label>';
    $h .= '<label>صورة (اختياري)<span class="yc-pt-img"><input type="url" dir="ltr" name="' . $n . '[image]" value="' . esc_attr( $o['image'] ) . '"><button type="button" class="button yc-pt-pick">اختيار</button></span></label>';
    $h .= '<label class="yc-pt-check"><input type="checkbox" name="' . $n . '[featured]" value="1" ' . checked( $o['featured'], 1, false ) . '> عرض مميّز</label>';
    $h .= '</div>';
    $h .= '<label>مميزات العرض (ميزة في كل سطر)<textarea name="' . $n . '[features]" rows="3">' . esc_textarea( $o['features'] ) . '</textarea></label>';
    $h .= '<a href="#" class="yc-pt-del-cat">حذف العرض</a>';
    return $h . '</div>';
}

function yc_pt_offers_page() {
    $d = yc_offers_data();
    echo '<div class="wrap yc-pt"><h1>العروض</h1>';
    if ( isset( $_GET['saved'] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>تم الحفظ.</p></div>';
    }
    echo '<p class="description">لعرض العروض: أنشئ صفحة، ومن صندوق «خصائص» اختر <b>النموذج ← صفحة العروض</b>.</p>';
    echo '<form method="post">';
    wp_nonce_field( 'yc_pt_offers' );
    echo '<input type="hidden" name="yc_pt_action" value="offers">';
    echo '<div class="yc-pt-box"><h2>الإعدادات العامة</h2><div class="yc-pt-grid">';
    echo '<label>العملة<input type="text" name="offers[currency]" value="' . esc_attr( $d['currency'] ) . '"></label>';
    echo '<label>رقم واتساب للحجز (اتركه فارغًا لاستخدام رقم القالب)<input type="text" dir="ltr" name="offers[wa]" value="' . esc_attr( $d['wa'] ) . '" placeholder="9665xxxxxxxx"></label>';
    echo '<label class="yc-pt-check"><input type="checkbox" name="offers[hide_expired]" value="1" ' . checked( $d['hide_expired'], 1, false ) . '> إخفاء العروض المنتهية تلقائيًا</label>';
    echo '</div></div>';
    echo '<div class="yc-pt-box"><h2>قائمة العروض</h2><div id="yc-pt-offers" data-next="' . count( $d['offers'] ) . '">';
    foreach ( array_values( $d['offers'] ) as $i => $o ) {
        echo yc_pt_offer_block( $i, $o ); // phpcs:ignore
    }
    echo '</div><p><button type="button" class="button button-secondary" id="yc-pt-add-offer">+ إضافة عرض</button></p></div>';
    echo '<script type="text/template" id="yc-pt-tpl-offer">' . yc_pt_offer_block( '__i__', array() ) . '</script>'; // phpcs:ignore
    echo '<p class="submit"><button class="button button-primary button-large">حفظ العروض</button> ';
    echo '<button class="button yc-pt-reset" name="yc_reset" value="1">استعادة العروض الافتراضية</button></p>';
    echo '</form></div>';
    wp_enqueue_media();
    yc_pt_admin_assets();
}

function yc_pt_admin_assets() {
    ?>
<style>
.yc-pt .yc-pt-box{background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:18px 22px;margin:16px 0;max-width:1200px}
.yc-pt .yc-pt-box h2{margin:0 0 14px;font-size:16px}
.yc-pt label{display:block;font-weight:600;margin-bottom:10px}
.yc-pt label input[type=text],.yc-pt label input[type=number],.yc-pt label input[type=url],.yc-pt label input[type=date],.yc-pt label select,.yc-pt label textarea{display:block;width:100%;margin-top:5px;font-weight:400}
.yc-pt-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0 16px;align-items:end}
.yc-pt-check{display:flex!important;align-items:center;gap:6px;padding-bottom:8px}
.yc-pt-cat{background:#f6f7f7;border:1px solid #e0e0e0;border-radius:10px;padding:14px 16px;margin-bottom:14px}
.yc-pt-cat__head{display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap}
.yc-pt-cat__head label{flex:1;min-width:200px}
.yc-pt-table{width:100%;border-collapse:collapse;margin-top:6px}
.yc-pt-table th{text-align:start;font-size:12px;color:#50575e;padding:4px}
.yc-pt-table td{padding:4px}
.yc-pt-table input{width:100%}
.yc-pt-sm{width:110px}.yc-pt-x{width:28px;text-align:center}
.yc-pt-del,.yc-pt-del-cat{color:#b32d2e;text-decoration:none}
.yc-pt-del-cat{display:inline-block;margin-bottom:10px;font-size:12.5px}
.yc-pt-img{display:flex;gap:6px;margin-top:5px}.yc-pt-img input{margin:0!important}
@media (max-width:900px){.yc-pt-grid{grid-template-columns:1fr}.yc-pt-table thead{display:none}.yc-pt-table tr{display:grid;grid-template-columns:1fr 1fr;gap:4px;border-bottom:1px solid #ddd;padding:6px 0}.yc-pt-sm,.yc-pt-x{width:auto}}
</style>
<script>
(function(){
  function tpl(id){ return document.getElementById(id).innerHTML; }
  function add(container, html){ var w=document.createElement('div'); w.innerHTML=html.trim(); var el=w.firstChild; container.appendChild(el); return el; }
  document.addEventListener('click', function(e){
    var t=e.target;
    if(t.id==='yc-pt-add-cat'){ e.preventDefault(); var box=document.getElementById('yc-pt-cats'); var c=+box.dataset.next; box.dataset.next=c+1; add(box, tpl('yc-pt-tpl-cat').replace(/__c__/g,c)).querySelector('input').focus(); }
    if(t.classList.contains('yc-pt-add-item')){ e.preventDefault(); var cat=t.closest('.yc-pt-cat'); var i=+cat.dataset.next; cat.dataset.next=i+1;
      var tb=cat.querySelector('tbody'); var tmp=document.createElement('tbody'); tmp.innerHTML=tpl('yc-pt-tpl-item').replace(/__c__/g,cat.dataset.c).replace(/__i__/g,i).trim(); var row=tmp.firstChild; tb.appendChild(row); row.querySelector('input').focus(); }
    if(t.id==='yc-pt-add-offer'){ e.preventDefault(); var ob=document.getElementById('yc-pt-offers'); var n=+ob.dataset.next; ob.dataset.next=n+1; add(ob, tpl('yc-pt-tpl-offer').replace(/__i__/g,n)).querySelector('input').focus(); }
    if(t.classList.contains('yc-pt-del')){ e.preventDefault(); t.closest('tr').remove(); }
    if(t.classList.contains('yc-pt-del-cat')){ e.preventDefault(); if(confirm('حذف؟')) t.closest('.yc-pt-cat').remove(); }
    if(t.classList.contains('yc-pt-reset') && !confirm('سيتم حذف كل تعديلاتك واستعادة البيانات الافتراضية. متابعة؟')){ e.preventDefault(); }
    if(t.classList.contains('yc-pt-pick')){ e.preventDefault(); if(!window.wp||!wp.media) return; var inp=t.previousElementSibling;
      var f=wp.media({title:'اختر صورة العرض',multiple:false,library:{type:'image'}}); f.on('select',function(){ inp.value=f.state().get('selection').first().toJSON().url; }); f.open(); }
  });
})();
</script>
    <?php
}

/** أيقونتا الأزرار. */
function yc_pt_wa_svg() {
    return '<svg viewBox="0 0 448 512" aria-hidden="true" focusable="false"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>';
}
function yc_pt_call_svg() {
    return '<svg viewBox="0 0 512 512" aria-hidden="true" focusable="false"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>';
}
