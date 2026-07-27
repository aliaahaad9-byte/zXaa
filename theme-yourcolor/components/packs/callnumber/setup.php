<?php

function callnumber() {
    add_menu_page(
        'صفحة المكالمات',
        'صفحة المكالمات',
        'manage_options',
        'add_callnumber_fields',
        'add_callnumber_fields',
        'dashicons-edit',
        6
    );
}

add_action( 'admin_menu', 'callnumber' );

/**
 * تسميات و أيقونات أنواع الاتصال
 */
function callnumber_types() {
    return array(
        'phone' => array(
            'label' => 'اتصال هاتفي',
            'icon'  => '<i class="fas fa-phone-alt"></i>',
        ),
        'whatsapp' => array(
            'label' => 'واتساب',
            'icon'  => '<i class="fab fa-whatsapp"></i>',
        ),
        'unknown' => array(
            'label' => 'غير محدد',
            'icon'  => '<i class="fas fa-question"></i>',
        ),
    );
}

/**
 * فارق التوقيت المستخدم في عرض تواريخ المكالمات ( توقيت الرياض )
 */
function callnumber_offset_hours() {
    return 3;
}

/**
 * تحويل تاريخ المكالمة المخزن الى طابع زمني بتوقيت الرياض
 */
function callnumber_timestamp( $calldate ) {
    if( empty($calldate) ) {
        return false;
    }
    $Stamp = strtotime( $calldate );
    if( $Stamp === false ) {
        return false;
    }
    return $Stamp + ( callnumber_offset_hours() * HOUR_IN_SECONDS );
}

/**
 * تنسيق التاريخ و الوقت بنظام 12 ساعة مع صباحاً / مساءً
 */
function callnumber_format_time( $timestamp ) {
    $Period = ( date( 'A', $timestamp ) === 'AM' ) ? 'صباحاً' : 'مساءً';
    return array(
        'date'   => date( 'Y-m-d', $timestamp ),
        'time'   => date( 'h:i:s', $timestamp ),
        'period' => $Period,
        'full'   => date( 'Y-m-d', $timestamp ) . ' - ' . date( 'h:i:s', $timestamp ) . ' ' . $Period,
    );
}

/**
 * أسماء الشهور بالعربية
 */
function callnumber_month_label( $month ) {
    $Names = array(
        '01' => 'يناير',  '02' => 'فبراير', '03' => 'مارس',
        '04' => 'أبريل',  '05' => 'مايو',   '06' => 'يونيو',
        '07' => 'يوليو',  '08' => 'أغسطس',  '09' => 'سبتمبر',
        '10' => 'أكتوبر', '11' => 'نوفمبر', '12' => 'ديسمبر',
    );
    $Parts = explode( '-', $month );
    $Year  = isset($Parts[0]) ? $Parts[0] : '';
    $Num   = isset($Parts[1]) ? $Parts[1] : '';
    $Name  = isset($Names[$Num]) ? $Names[$Num] : $Num;
    return $Name . ' ' . $Year;
}

/**
 * التحقق من صيغة الشهر YYYY-MM و الرجوع للشهر الحالي عند الخطأ
 */
function callnumber_sanitize_month( $month ) {
    if( is_string($month) && preg_match( '/^\d{4}-(0[1-9]|1[0-2])$/', $month ) ) {
        return $month;
    }
    return date( 'Y-m', current_time('timestamp') );
}

/**
 * حدود الشهر بصيغة القيم المخزنة ( مع خصم فارق التوقيت حتى تطابق
 * حدود الشهر ما يظهر فعلا في الجدول )
 */
function callnumber_month_range( $month ) {
    $Offset = callnumber_offset_hours() * HOUR_IN_SECONDS;
    $Start  = strtotime( $month . '-01 00:00:00' ) - $Offset;
    $End    = strtotime( $month . '-01 00:00:00 +1 month' ) - 1 - $Offset;
    return array(
        'start' => date( 'Y-m-d H:i:s', $Start ),
        'end'   => date( 'Y-m-d H:i:s', $End ),
    );
}

/**
 * عدادات أنواع الاتصال ( لكل السجلات و ليس الصفحة الحالية فقط )
 */
function callnumber_counters() {
    global $wpdb;

    $Total = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts}
         WHERE post_type = 'callwebsite' AND post_status = 'publish'"
    );

    $Phone = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
         WHERE p.post_type = 'callwebsite' AND p.post_status = 'publish'
         AND m.meta_key = 'calltype' AND m.meta_value = 'phone'"
    );

    $Whatsapp = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
         WHERE p.post_type = 'callwebsite' AND p.post_status = 'publish'
         AND m.meta_key = 'calltype' AND m.meta_value = 'whatsapp'"
    );

    $Unknown = $Total - $Phone - $Whatsapp;
    if( $Unknown < 0 ) {
        $Unknown = 0;
    }

    return array(
        'total'    => $Total,
        'phone'    => $Phone,
        'whatsapp' => $Whatsapp,
        'unknown'  => $Unknown,
    );
}

/**
 * كل مكالمات شهر معين مرتبة من الأحدث للأقدم
 */
function callnumber_month_calls( $month ) {
    $Range = callnumber_month_range( $month );

    $Query = new WP_Query( array(
        'post_type'      => 'callwebsite',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_key'       => 'calldate',
        'orderby'        => 'meta_value',
        'order'          => 'DESC',
        'meta_query'     => array(
            array(
                'key'     => 'calldate',
                'value'   => array( $Range['start'], $Range['end'] ),
                'compare' => 'BETWEEN',
                'type'    => 'DATETIME',
            ),
        ),
    ) );

    return $Query->posts;
}

/**
 * رابط تقرير الـ PDF الشهري
 */
function callnumber_report_url( $month ) {
    return wp_nonce_url(
        admin_url( 'admin.php?page=add_callnumber_fields&callreport=1&month=' . $month ),
        'callnumber_report'
    );
}

/**
 * اعتراض الطلب و عرض التقرير الشهري في صفحة مستقلة جاهزة للحفظ كـ PDF
 */
function callnumber_maybe_render_report() {

    if( !isset($_GET['page']) || $_GET['page'] !== 'add_callnumber_fields' ) {
        return;
    }
    if( !isset($_GET['callreport']) ) {
        return;
    }
    if( !current_user_can('manage_options') ) {
        wp_die( 'غير مسموح لك بعرض هذا التقرير.' );
    }
    check_admin_referer( 'callnumber_report' );

    callnumber_render_report( callnumber_sanitize_month( isset($_GET['month']) ? $_GET['month'] : '' ) );
    exit;
}

add_action( 'admin_init', 'callnumber_maybe_render_report' );

/**
 * قالب التقرير الشهري
 */
function callnumber_render_report( $month ) {

    $Types = callnumber_types();
    $Posts = callnumber_month_calls( $month );

    $Stats = array( 'phone' => 0, 'whatsapp' => 0, 'unknown' => 0 );
    $Rows  = array();

    foreach( $Posts as $post ) {
        $Type = get_post_meta( $post->ID, 'calltype', true );
        if( !array_key_exists($Type, $Types) || $Type === 'unknown' ) {
            $Type = 'unknown';
        }
        $Stats[$Type]++;

        $Stamp = callnumber_timestamp( get_post_meta( $post->ID, 'calldate', true ) );

        $Rows[] = array(
            'page' => get_post_meta( $post->ID, 'page', true ),
            'type' => $Type,
            'time' => ( $Stamp === false ) ? false : callnumber_format_time( $Stamp ),
        );
    }

    $Total     = count( $Rows );
    $SiteName  = get_bloginfo( 'name' );
    $MonthName = callnumber_month_label( $month );
    $Generated = callnumber_format_time( current_time('timestamp') );

    nocache_headers();
    header( 'Content-Type: text/html; charset=utf-8' );

    ?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( 'تقرير المكالمات - ' . $MonthName ); ?></title>
<style>
    * { box-sizing: border-box; }
    body {
        margin: 0;
        padding: 30px 20px;
        background: #f3f6fb;
        color: #060b18;
        font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        direction: rtl;
    }
    .report-sheet {
        max-width: 1000px;
        margin: 0 auto;
        background: #fff;
        border-radius: 18px;
        padding: 34px;
        box-shadow: 0 4px 22px rgba(9,20,34,0.09);
    }
    .report-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        border-bottom: 2px solid #eef3fa;
        padding-bottom: 18px;
        margin-bottom: 24px;
    }
    .report-head h1 { margin: 0 0 6px; font-size: 23px; }
    .report-head p { margin: 0; font-size: 13px; color: #7b8aa1; }
    .report-actions { display: flex; gap: 10px; }
    .report-actions button {
        cursor: pointer;
        border: 0;
        border-radius: 10px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        color: #fff;
        background: #1269eb;
    }
    .report-actions button.ghost { background: #7b8aa1; }
    .report-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 26px;
    }
    .report-stat {
        border: 1px solid #e3ecf7;
        border-radius: 14px;
        padding: 16px;
        text-align: center;
    }
    .report-stat b { display: block; font-size: 26px; }
    .report-stat span { font-size: 13px; color: #7b8aa1; }
    .report-stat.is-total { background: #f5f9ff; border-color: #c3dbff; }
    .report-stat.is-total b { color: #1269eb; }
    .report-stat.is-phone b { color: #1269eb; }
    .report-stat.is-whatsapp b { color: #128c4b; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead th {
        background: #f1f5fb;
        text-align: right;
        padding: 12px 10px;
        font-size: 13px;
        border-bottom: 1px solid #e3ecf7;
    }
    tbody td { padding: 11px 10px; border-bottom: 1px solid #eef3fa; vertical-align: middle; }
    tbody tr:nth-child(even) { background: #fbfcfe; }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge.phone { color: #1269eb; background: #e8f1ff; }
    .badge.whatsapp { color: #128c4b; background: #e6f9ee; }
    .badge.unknown { color: #7b8aa1; background: #f1f4f9; }
    .empty { text-align: center; padding: 40px 10px; color: #7b8aa1; }
    .report-foot { margin-top: 22px; font-size: 12px; color: #7b8aa1; text-align: center; }
    @media print {
        body { background: #fff; padding: 0; }
        .report-sheet { box-shadow: none; border-radius: 0; padding: 0; max-width: none; }
        .report-actions { display: none; }
        thead { display: table-header-group; }
        tbody tr { page-break-inside: avoid; }
    }
    @page { size: A4; margin: 14mm; }
</style>
</head>
<body>
<div class="report-sheet">

    <div class="report-head">
        <div>
            <h1>تقرير المكالمات الشهري - <?php echo esc_html( $MonthName ); ?></h1>
            <p><?php echo esc_html( $SiteName ); ?> — تم إنشاء التقرير في <?php echo esc_html( $Generated['full'] ); ?></p>
        </div>
        <div class="report-actions">
            <button type="button" onclick="window.print()">تحميل PDF / طباعة</button>
            <button type="button" class="ghost" onclick="window.close()">إغلاق</button>
        </div>
    </div>

    <div class="report-stats">
        <div class="report-stat is-total">
            <b><?php echo esc_html( number_format_i18n($Total) ); ?></b>
            <span>إجمالي المكالمات</span>
        </div>
        <div class="report-stat is-phone">
            <b><?php echo esc_html( number_format_i18n($Stats['phone']) ); ?></b>
            <span>اتصال هاتفي</span>
        </div>
        <div class="report-stat is-whatsapp">
            <b><?php echo esc_html( number_format_i18n($Stats['whatsapp']) ); ?></b>
            <span>اتصال واتساب</span>
        </div>
    </div>

    <?php if( empty($Rows) ) : ?>

        <div class="empty">لا توجد مكالمات مسجلة خلال <?php echo esc_html( $MonthName ); ?>.</div>

    <?php else : ?>

        <table>
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>اسم الصفحة</th>
                    <th style="width:150px">نوع الاتصال</th>
                    <th style="width:130px">التاريخ</th>
                    <th style="width:160px">الوقت</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 0; foreach( $Rows as $Row ) : $i++; ?>
                <tr>
                    <td><?php echo esc_html( number_format_i18n($i) ); ?></td>
                    <td><?php echo esc_html( $Row['page'] ); ?></td>
                    <td><span class="badge <?php echo esc_attr( $Row['type'] ); ?>"><?php echo esc_html( $Types[$Row['type']]['label'] ); ?></span></td>
                    <td><?php echo ( $Row['time'] === false ) ? '—' : esc_html( $Row['time']['date'] ); ?></td>
                    <td><?php echo ( $Row['time'] === false ) ? '—' : esc_html( $Row['time']['time'] . ' ' . $Row['time']['period'] ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <div class="report-foot">
        جميع الأوقات بتوقيت الرياض (GMT+<?php echo esc_html( callnumber_offset_hours() ); ?>) و بنظام 12 ساعة.
    </div>

</div>
<script>
    window.addEventListener('load', function () {
        setTimeout(function () { window.print(); }, 400);
    });
</script>
</body>
</html><?php
}

function add_callnumber_fields(){

    $Whatsapp = get_option('Whatsapp');
$Phone = get_option('Phone');
$postID = array();
foreach( get_posts(['posts_per_page'=>-1,'post_type'=>'callwebsite']) as $posts ){
    $postID[] = $posts->ID;
}
$currentURL = (new ThemeStatic)->GetCurrentURL();
$per = 50;

// نوع الاتصال المستخدم في الفلترة
$Types = callnumber_types();
$ActiveType = isset($_GET['calltype']) ? sanitize_key($_GET['calltype']) : '';
if( !array_key_exists($ActiveType, $Types) ) {
    $ActiveType = '';
}

$args = array(
    'post_type' => 'callwebsite',
    'posts_per_page' => $per,
    'orderby' => 'date',  // ترتيب حسب التاريخ
    'order' => 'DESC',    // ترتيب تنازلي (الأحدث أولا)
);
if( $ActiveType === 'unknown' ) {
    // السجلات القديمة التي سُجلت قبل إضافة خاصية نوع الاتصال
    $args['meta_query'] = array(
        'relation' => 'OR',
        array( 'key' => 'calltype', 'compare' => 'NOT EXISTS' ),
        array( 'key' => 'calltype', 'value' => '', 'compare' => '=' ),
    );
} elseif( $ActiveType !== '' ) {
    $args['meta_query'] = array(
        array( 'key' => 'calltype', 'value' => $ActiveType, 'compare' => '=' ),
    );
}
if( isset( $_GET['paged'] ) ) {
    $args['paged'] = $_GET['paged'];
}
$Founder = new WP_Query($args);
$CountQuery = $Founder->found_posts;
$CounterAll = $CountQuery / $per;

if( strpos($CounterAll, '.') !== FALSE ){
    $CounterAll = explode('.',$CounterAll)[0];
    $CounterAll = $CounterAll + 1;
}
$UniqId = uniqid();
$Paged = ((!isset($_GET['paged']))) ? 1 : $_GET['paged'];
$BackPaged = $Paged - 1;
$NextPaged = $Paged + 1;
$PagenateURL = $currentURL;
$PagenateURL = explode('page=add_callnumber_fields', $PagenateURL)[0];
$PagenateURL = $PagenateURL.'page=add_callnumber_fields';

// الرابط الأساسي للفلترة ( بدون ترقيم الصفحات )
$FilterURL = $PagenateURL;
// الحفاظ على الفلتر الحالي داخل روابط ترقيم الصفحات
if( $ActiveType !== '' ) {
    $PagenateURL = $PagenateURL.'&calltype='.$ActiveType;
}

$Counters = callnumber_counters();

// الشهر المختار لتقرير الـ PDF ( الشهر الحالي افتراضيا )
$ReportMonth = callnumber_sanitize_month( isset($_GET['reportmonth']) ? $_GET['reportmonth'] : '' );

    echo '<div class="custom-page-mail-">';
    echo '<div class="container">';
    echo '<div class="page-boxed-">';
    echo '<div class="box-page">';
    echo '<div class="title_page">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464H398.7c-8.9-63.3-63.3-112-129-112H178.3c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3z"/></svg>';
    echo '<div class="title_boxed">';
    echo '<h2>صفحة المكالمات</h2>';
    echo '</div>';
    echo '</div>';
     echo '<ul class="-Navs-Actions">';
                        echo '<li data-navs-actions="SelectAll" data-uniqid="'.$UniqId.'"><i class="far fa-plus-octagon"></i><span>تحديد الكل </span></li>';
                        echo '<li data-navs-actions="RemoveSelectAll" data-uniqid="'.$UniqId.'" style="display:none;pointer-events: none; opacity: 0.5;"><i class="fal fa-times-hexagon"></i><span>ألغاء التحديد</span></li>';
                        echo '<li data-navs-actions="RemoveAllSelected" data-uniqid="'.$UniqId.'" style="pointer-events: none; opacity: 0.5;"><i class="fas fa-minus-hexagon"></i><span>حذف المحدد</span></li>';
                    echo '</ul>';
    echo '</div>';

    // زر تحميل تقرير PDF شهري + اختيار الشهر
    echo '<div class="calls-report-bar">';
        echo '<span class="calls-report-title"><i class="fas fa-file-pdf"></i> تقرير شهري</span>';
        echo '<select class="calls-report-month" id="calls-report-month">';
        for( $i = 0; $i < 12; $i++ ) {
            $Month = date( 'Y-m', strtotime( date('Y-m-01', current_time('timestamp')).' -'.$i.' month' ) );
            echo '<option value="'.esc_attr($Month).'" data-href="'.esc_url( callnumber_report_url($Month) ).'"'.( ($Month === $ReportMonth) ? ' selected' : '' ).'>'.esc_html( callnumber_month_label($Month) ).'</option>';
        }
        echo '</select>';
        echo '<a class="calls-report-btn" id="calls-report-btn" target="_blank" rel="noopener" href="'.esc_url( callnumber_report_url($ReportMonth) ).'"><i class="fas fa-download"></i><span>تحميل PDF</span></a>';
    echo '</div>';

    echo '<script>
    (function(){
        var Select = document.getElementById("calls-report-month");
        var Button = document.getElementById("calls-report-btn");
        if( !Select || !Button ) { return; }
        Select.addEventListener("change", function(){
            Button.setAttribute( "href", Select.options[ Select.selectedIndex ].getAttribute("data-href") );
        });
    })();
    </script>';

    // عدادات أنواع الاتصال - قابلة للضغط للفلترة
    echo '<div class="calls-stats-boxes">';

        echo '<a class="calls-stat-box is-total'.(($ActiveType === '') ? ' active' : '').'" href="'.esc_url($FilterURL).'">';
            echo '<span class="calls-stat-icon"><i class="fas fa-chart-pie"></i></span>';
            echo '<span class="calls-stat-body">';
                echo '<span class="calls-stat-number">'.number_format_i18n($Counters['total']).'</span>';
                echo '<span class="calls-stat-label">إجمالي المكالمات</span>';
            echo '</span>';
        echo '</a>';

        echo '<a class="calls-stat-box is-phone'.(($ActiveType === 'phone') ? ' active' : '').'" href="'.esc_url($FilterURL.'&calltype=phone').'">';
            echo '<span class="calls-stat-icon"><i class="fas fa-phone-alt"></i></span>';
            echo '<span class="calls-stat-body">';
                echo '<span class="calls-stat-number">'.number_format_i18n($Counters['phone']).'</span>';
                echo '<span class="calls-stat-label">اتصال هاتفي</span>';
            echo '</span>';
        echo '</a>';

        echo '<a class="calls-stat-box is-whatsapp'.(($ActiveType === 'whatsapp') ? ' active' : '').'" href="'.esc_url($FilterURL.'&calltype=whatsapp').'">';
            echo '<span class="calls-stat-icon"><i class="fab fa-whatsapp"></i></span>';
            echo '<span class="calls-stat-body">';
                echo '<span class="calls-stat-number">'.number_format_i18n($Counters['whatsapp']).'</span>';
                echo '<span class="calls-stat-label">اتصال واتساب</span>';
            echo '</span>';
        echo '</a>';

        if( $Counters['unknown'] > 0 ) {
            echo '<a class="calls-stat-box is-unknown'.(($ActiveType === 'unknown') ? ' active' : '').'" href="'.esc_url($FilterURL.'&calltype=unknown').'">';
                echo '<span class="calls-stat-icon"><i class="fas fa-question"></i></span>';
                echo '<span class="calls-stat-body">';
                    echo '<span class="calls-stat-number">'.number_format_i18n($Counters['unknown']).'</span>';
                    echo '<span class="calls-stat-label">غير محدد</span>';
                echo '</span>';
            echo '</a>';
        }

    echo '</div>';

    if( $ActiveType !== '' ) {
        echo '<div class="calls-active-filter">';
        echo '<span>يتم عرض: '.$Types[$ActiveType]['label'].' ('.number_format_i18n($CountQuery).')</span>';
        echo '<a href="'.esc_url($FilterURL).'">إلغاء الفلترة</a>';
        echo '</div>';
    }

    echo '<div class="custom-page-mail">';
    echo '<table>';
    echo '<thead class="mail-header">';
    echo '<tr>';
    echo '<th class="checkbox"><input type="checkbox" id="select-all" name="name" value="'.implode(',', $postID).'"></th>';
    echo '<th>اسم الصفحة</th>';
    echo '<th>نوع الاتصال</th>';
    echo '<th>التاريخ</th>';
    echo '<th>حذف</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody class="client_order -ScrollerCenter" data-uniqid="'.$UniqId.'">';
    foreach ( $Founder->posts as $post) {
        $page = get_post_meta($post->ID, 'page', true);
        $type = get_post_meta($post->ID, 'calltype', true);
        if( !array_key_exists($type, $Types) || $type === 'unknown' ) {
            $type = 'unknown';
        }
        echo '<tr data-client="'.$post->ID.'" class="-contain-MiniBox" data-post-id="'.$post->ID.'" data-calltype="'.$type.'">';
        echo '<td data-client="'.$post->ID.'">';
        echo '<div class="-checkBox-post-fixed" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
        echo '</td>';
        echo '<td class="page">';
        if(!empty($page)){
            echo '<p>'.esc_html($page).'</p>';
        }
        echo '</td>';
        echo '<td class="calltype">';
        echo '<span class="calltype-badge calltype-'.$type.'">'.$Types[$type]['icon'].'<b>'.$Types[$type]['label'].'</b></span>';
        echo '</td>';
        echo '<td class="time">';

        // التاريخ و الوقت بتوقيت الرياض و بنظام 12 ساعة ( صباحاً / مساءً )
        $Stamp = callnumber_timestamp( get_post_meta($post->ID, 'calldate', true) );
        if( $Stamp !== false ) {
            $Formatted = callnumber_format_time( $Stamp );
            echo '<p class="day-time">اليوم: '.$Formatted['date'].'</p> /';
            echo '<p class="time-click">الوقت: '.$Formatted['time'].' <span class="time-period">'.$Formatted['period'].'</span></p>';
        }

        echo '</td>';
        echo '<td>';
        echo '<div class="romoves-posts" data-remove="'.$post->ID.'"><span><svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24" width="512" height="512"><path d="M21,4H17.9A5.009,5.009,0,0,0,13,0H11A5.009,5.009,0,0,0,6.1,4H3A1,1,0,0,0,3,6H4V19a5.006,5.006,0,0,0,5,5h6a5.006,5.006,0,0,0,5-5V6h1a1,1,0,0,0,0-2ZM11,2h2a3.006,3.006,0,0,1,2.829,2H8.171A3.006,3.006,0,0,1,11,2Zm7,17a3,3,0,0,1-3,3H9a3,3,0,0,1-3-3V6H18Z"/><path d="M10,18a1,1,0,0,0,1-1V11a1,1,0,0,0-2,0v6A1,1,0,0,0,10,18Z"/><path d="M14,18a1,1,0,0,0,1-1V11a1,1,0,0,0-2,0v6A1,1,0,0,0,14,18Z"/></svg> حذف</span></div>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '<div class="PagnationsNavs" id="PagnationsNavs" data-counter="'.$CountQuery.'" data-pagedcounter="'.$CounterAll.'" data-permalink="'.$PagenateURL.'">';
    echo '<ul>';
    echo '<li class="perv" '.(($Paged > 1) ? '' : 'style="pointer-events:none;opacity:0.5"').'><a href="'.$PagenateURL.'&paged='.$BackPaged.'"><i class="fas fa-chevron-right"></i><span>السابق</span></a></li>';
    echo '<li class="next" '.(($Paged >= $CounterAll) ? 'style="pointer-events:none;opacity:0.5"' : '').'><a href="'.$PagenateURL.'&paged='.$NextPaged.'"><span>التالي</span><i class="fas fa-chevron-left"></i></a></li>';
    echo '<li '.(($Paged >= $CounterAll) ? 'style="pointer-events:none;opacity:0.5"' : '').'><a href="'.$PagenateURL.'&paged='.$CounterAll.'"><coun-tt>'.$CounterAll.'</coun-tt> <span>الصفحة الاخيرة</span><i class="fas fa-chevron-double-left"></i></a></li>';
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

?>
