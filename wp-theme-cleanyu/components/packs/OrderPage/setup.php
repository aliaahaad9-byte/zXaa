<?php
/**
 * صفحة «طلب خدمة» — إعدادات النموذج.
 *
 * العرض: نموذج الصفحة @models/order.php (خصائص ← النموذج ← طلب خدمة).
 * التحرير: لوحة التحكم ← الحاسبة والعروض ← صفحة طلب الخدمة.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function yc_order_defaults() {
    return array(
        'wa'        => '',
        'badge'     => 'نموذج آمن ومباشر',
        'title'     => 'أرسل طلبك في خطوات بسيطة',
        'subtitle'  => 'اختر الخدمة، أدخل بياناتك، وسنتواصل معك فوراً',
        'services'  => "عزل خزانات\nعزل مواسير\nعزل أسطح\nتنظيف خزانات\nتنظيف سجاد\nتنظيف كنب\nتنظيف مجالس\nتنظيف منازل\nتنظيف مكيفات\nتنظيف مكاتب\nمكافحة حشرات\nرش مبيدات\nكشف تسربات\nمبلط سيراميك\nترميم منازل\nإنشاء مسابح",
        'cities'    => "الرياض\nالدمام\nالخبر\nالقطيف\nالظهران\nالأحساء\nجدة\nمكة المكرمة\nالمدينة المنورة",
        'why_title' => 'لماذا تختار شركة التنفيذ؟',
        'why_text'  => 'منذ أكثر من 23 عاماً ونحن نقدّم خدمات الصيانة والتنظيف والعزل لآلاف العملاء في الرياض والمنطقة الشرقية. نلتزم بأعلى معايير الجودة، الأمان، والشفافية في الأسعار.',
        'features'  => "ضمان الجودة لمدة عام | نقدّم ضمان مكتوب على جميع أعمال العزل ومكافحة الحشرات يصل إلى 12 شهراً، مع زيارات متابعة مجانية.\n"
                     . "استجابة خلال 60 دقيقة | نتواصل معك فور استلام طلبك عبر واتساب ونرسل الفني المختص إلى موقعك في أسرع وقت ممكن.\n"
                     . "أسعار شفافة بدون مفاجآت | نتفق على السعر النهائي قبل بدء العمل، بدون تكاليف خفية أو رسوم إضافية مهما طال وقت التنفيذ.\n"
                     . "فنيون مدرّبون ومعتمدون | فريقنا من العمالة السعودية المدرّبة والفنيين المعتمدين على أحدث تقنيات العزل والتنظيف الآمنة بيئياً.\n"
                     . "مواد عالمية معتمدة | نستخدم مواد عزل ومبيدات مرخّصة من وزارة البيئة، صديقة للأسرة والأطفال وآمنة على المياه.\n"
                     . "خدمة 24/7 طوال أيام الأسبوع | متاحون يومياً بما في ذلك العطل الرسمية، لتلبية الحالات الطارئة ككشف التسربات وانفجار المواسير.",
    );
}

function yc_order_data() {
    $d = get_option( 'yc_order_data' );
    return is_array( $d ) ? wp_parse_args( $d, yc_order_defaults() ) : yc_order_defaults();
}

/** أسطر غير فارغة. */
function yc_order_lines( $raw ) {
    return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $raw ) ), 'strlen' ) );
}

/* ---------- لوحة التحكم ---------- */
add_action( 'admin_menu', function () {
    add_submenu_page( 'yc-calc', 'صفحة طلب الخدمة', 'صفحة طلب الخدمة', 'manage_options', 'yc-order', 'yc_order_admin_page' );
}, 20 );

add_action( 'admin_init', function () {
    if ( empty( $_POST['yc_order_save'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    check_admin_referer( 'yc_order_save' );
    $in = isset( $_POST['order'] ) && is_array( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : array();
    if ( ! empty( $_POST['yc_reset'] ) ) {
        delete_option( 'yc_order_data' );
    } else {
        $out = array();
        foreach ( array( 'wa', 'badge', 'title', 'subtitle', 'why_title' ) as $k ) {
            $out[ $k ] = isset( $in[ $k ] ) ? sanitize_text_field( $in[ $k ] ) : '';
        }
        foreach ( array( 'services', 'cities', 'why_text', 'features' ) as $k ) {
            $out[ $k ] = isset( $in[ $k ] ) ? sanitize_textarea_field( $in[ $k ] ) : '';
        }
        update_option( 'yc_order_data', $out, false );
    }
    wp_safe_redirect( add_query_arg( array( 'page' => 'yc-order', 'saved' => 1 ), admin_url( 'admin.php' ) ) );
    exit;
} );

function yc_order_admin_page() {
    $d = yc_order_data();
    $t = function ( $k, $label, $ph = '' ) use ( $d ) {
        return '<p><label><b>' . esc_html( $label ) . '</b><br><input type="text" class="large-text" name="order[' . $k . ']" value="' . esc_attr( $d[ $k ] ) . '" placeholder="' . esc_attr( $ph ) . '"></label></p>';
    };
    $a = function ( $k, $label, $rows, $help = '' ) use ( $d ) {
        return '<p><label><b>' . esc_html( $label ) . '</b>' . ( $help ? '<br><span class="description">' . esc_html( $help ) . '</span>' : '' ) . '<br><textarea class="large-text" rows="' . (int) $rows . '" name="order[' . $k . ']">' . esc_textarea( $d[ $k ] ) . '</textarea></label></p>';
    };
    echo '<div class="wrap"><h1>صفحة طلب الخدمة</h1>';
    if ( isset( $_GET['saved'] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>تم الحفظ.</p></div>';
    }
    echo '<p class="description">لعرض النموذج: أنشئ صفحة (مثل /order/)، ومن صندوق «خصائص» اختر <b>النموذج ← طلب خدمة</b>. عنوان الصفحة ومحتواها يظهران أعلى النموذج.</p>';
    echo '<form method="post" style="max-width:900px;background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:18px 24px">';
    wp_nonce_field( 'yc_order_save' );
    echo '<input type="hidden" name="yc_order_save" value="1">';
    echo $t( 'wa', 'رقم واتساب لاستقبال الطلبات (اتركه فارغًا لاستخدام رقم القالب)', '9665xxxxxxxx' ); // phpcs:ignore
    echo $t( 'badge', 'شارة أعلى النموذج' ); // phpcs:ignore
    echo $t( 'title', 'عنوان النموذج' ); // phpcs:ignore
    echo $t( 'subtitle', 'وصف النموذج' ); // phpcs:ignore
    echo $a( 'services', 'الخدمات', 8, 'خدمة في كل سطر — تظهر كأزرار اختيار.' ); // phpcs:ignore
    echo $a( 'cities', 'المدن', 6, 'مدينة في كل سطر. اتركه فارغًا لاستخدام «المدن» المضافة في الموقع.' ); // phpcs:ignore
    echo '<hr>';
    echo $t( 'why_title', 'عنوان قسم «لماذا تختارنا»' ); // phpcs:ignore
    echo $a( 'why_text', 'نص قسم «لماذا تختارنا»', 3 ); // phpcs:ignore
    echo $a( 'features', 'المميزات', 7, 'ميزة في كل سطر بصيغة: العنوان | الوصف' ); // phpcs:ignore
    echo '<p class="submit"><button class="button button-primary button-large">حفظ</button> ';
    echo '<button class="button" name="yc_reset" value="1" onclick="return confirm(\'استعادة النصوص الافتراضية؟\')">استعادة الافتراضي</button></p>';
    echo '</form></div>';
}
