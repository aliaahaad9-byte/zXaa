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
 * نوع الاتصال المسجل لمكالمة معينة ( مع معالجة السجلات القديمة )
 */
function callnumber_get_type( $post_id ) {
    $type = get_post_meta( $post_id, 'calltype', true );
    $type = is_string( $type ) ? strtolower( trim( $type ) ) : '';

    if( !in_array( $type, array( 'phone', 'whatsapp' ), true ) ) {
        $type = 'unknown';
    }

    return $type;
}

/**
 * عدادات أنواع الاتصال ( لكل السجلات و ليس الصفحة الحالية فقط )
 * $from / $to اختيارية بصيغة Y-m-d H:i:s لحصر العد في مدة معينة
 */
function callnumber_counters( $from = '', $to = '' ) {
    global $wpdb;

    $Range = '';
    if( $from !== '' && $to !== '' ) {
        $Range = $wpdb->prepare( ' AND p.post_date BETWEEN %s AND %s ', $from, $to );
    }

    $Total = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts} p
         WHERE p.post_type = 'callwebsite' AND p.post_status = 'publish' {$Range}"
    );

    $Phone = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
         WHERE p.post_type = 'callwebsite' AND p.post_status = 'publish'
         AND m.meta_key = 'calltype' AND m.meta_value = 'phone' {$Range}"
    );

    $Whatsapp = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
         WHERE p.post_type = 'callwebsite' AND p.post_status = 'publish'
         AND m.meta_key = 'calltype' AND m.meta_value = 'whatsapp' {$Range}"
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
 * تنسيق وقت المكالمة بنظام 12 ساعة ( صباحاً / مساءً )
 * يرجع مصفوفة : التاريخ , الوقت , الفترة
 */
function callnumber_format_time( $time ) {
    if( empty( $time ) ) {
        return false;
    }

    // تحديد المنطقة الزمنية إلى الرياض (GMT+3)
    date_default_timezone_set('Asia/Riyadh');

    $timestamp = strtotime( $time . ' +3 hours' ); // إضافة 3 ساعات للحصول على التوقيت في الرياض
    if( $timestamp === false ) {
        return false;
    }

    return array(
        'date'   => date( "Y-m-d", $timestamp ),
        'time'   => date( "h:i:s", $timestamp ), // نظام 12 ساعة
        'period' => ( date( "A", $timestamp ) === 'AM' ) ? 'صباحاً' : 'مساءً',
    );
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
$ReportURL = wp_nonce_url( admin_url('admin-post.php?action=callnumber_monthly_report'), 'callnumber_monthly_report' );

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
                        echo '<li class="calls-report-btn"><a href="'.esc_url($ReportURL).'" target="_blank" rel="noopener" title="تقرير بكل مكالمات آخر شهر"><i class="fas fa-file-pdf"></i><span>تقرير PDF شهري</span></a></li>';
                        echo '<li data-navs-actions="SelectAll" data-uniqid="'.$UniqId.'"><i class="far fa-plus-octagon"></i><span>تحديد الكل </span></li>';
                        echo '<li data-navs-actions="RemoveSelectAll" data-uniqid="'.$UniqId.'" style="display:none;pointer-events: none; opacity: 0.5;"><i class="fal fa-times-hexagon"></i><span>ألغاء التحديد</span></li>';
                        echo '<li data-navs-actions="RemoveAllSelected" data-uniqid="'.$UniqId.'" style="pointer-events: none; opacity: 0.5;"><i class="fas fa-minus-hexagon"></i><span>حذف المحدد</span></li>';
                    echo '</ul>';
    echo '</div>';

    // عدادات أنواع الاتصال - ثلاثة كروت جنب بعض و قابلة للضغط للفلترة
    echo '<div class="calls-stats-boxes">';

        echo '<a class="calls-stat-box is-whatsapp'.(($ActiveType === 'whatsapp') ? ' active' : '').'" href="'.esc_url($FilterURL.'&calltype=whatsapp').'">';
            echo '<span class="calls-stat-icon"><i class="fab fa-whatsapp"></i></span>';
            echo '<span class="calls-stat-body">';
                echo '<span class="calls-stat-number">'.number_format_i18n($Counters['whatsapp']).'</span>';
                echo '<span class="calls-stat-label">اتصال واتساب</span>';
            echo '</span>';
        echo '</a>';

        echo '<a class="calls-stat-box is-phone'.(($ActiveType === 'phone') ? ' active' : '').'" href="'.esc_url($FilterURL.'&calltype=phone').'">';
            echo '<span class="calls-stat-icon"><i class="fas fa-phone-alt"></i></span>';
            echo '<span class="calls-stat-body">';
                echo '<span class="calls-stat-number">'.number_format_i18n($Counters['phone']).'</span>';
                echo '<span class="calls-stat-label">اتصال هاتفي</span>';
            echo '</span>';
        echo '</a>';

        echo '<a class="calls-stat-box is-total'.(($ActiveType === '') ? ' active' : '').'" href="'.esc_url($FilterURL).'">';
            echo '<span class="calls-stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="22" height="22" fill="currentColor"><path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64V400c0 44.2 35.8 80 80 80H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H80c-8.8 0-16-7.2-16-16V64zM160 320c17.7 0 32-14.3 32-32V224c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7 14.3 32 32 32zm128-64V128c0-17.7-14.3-32-32-32s-32 14.3-32 32V288c0 17.7 14.3 32 32 32s32-14.3 32-32zm96 64c17.7 0 32-14.3 32-32V96c0-17.7-14.3-32-32-32s-32 14.3-32 32V288c0 17.7 14.3 32 32 32z"/></svg></span>';
            echo '<span class="calls-stat-body">';
                echo '<span class="calls-stat-number">'.number_format_i18n($Counters['total']).'</span>';
                echo '<span class="calls-stat-label">إجمالي المكالمات</span>';
                if( $Counters['unknown'] > 0 ) {
                    echo '<span class="calls-stat-note">منها '.number_format_i18n($Counters['unknown']).' غير محدد</span>';
                }
            echo '</span>';
        echo '</a>';

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
        $time = get_post_meta($post->ID, 'calldate', true);
        $page = get_post_meta($post->ID, 'page', true);
        $type = callnumber_get_type($post->ID);
        echo '<tr data-client="'.$post->ID.'" class="-contain-MiniBox" data-post-id="'.$post->ID.'" data-calltype="'.$type.'">';
        echo '<td data-client="'.$post->ID.'">';
        echo '<div class="-checkBox-post-fixed" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
        echo '</td>';
        echo '<td class="page">';
        if(!empty($page)){
            echo '<p>'.$page.'</p>';
        }
        echo '</td>';
        echo '<td class="calltype">';
        echo '<span class="calltype-badge calltype-'.$type.'">'.$Types[$type]['icon'].'<b>'.$Types[$type]['label'].'</b></span>';
        echo '</td>';
        echo '<td class="time">';
        $Formatted = callnumber_format_time($time);
        if( $Formatted !== false ) {
            // عرض التاريخ و الوقت بنظام 12 ساعة
            echo '<p class="day-time">اليوم: ' . $Formatted['date'] . '</p> /';
            echo '<p class="time-click">الوقت: ' . $Formatted['time'] . ' <span class="time-period">' . $Formatted['period'] . '</span></p>';
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

/**
 * تقرير شهري بكل المكالمات خلال آخر شهر
 * يفتح صفحة تقرير جاهزة للطباعة و يتم حفظها كملف PDF من نافذة الطباعة
 */
function callnumber_monthly_report() {

    if( !current_user_can('manage_options') ) {
        wp_die('غير مصرح لك بعرض هذا التقرير');
    }
    check_admin_referer('callnumber_monthly_report');

    date_default_timezone_set('Asia/Riyadh');

    $Types = callnumber_types();
    $To    = current_time('Y-m-d H:i:s');
    $From  = date('Y-m-d H:i:s', strtotime($To.' -1 month'));

    $Query = new WP_Query(array(
        'post_type'      => 'callwebsite',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'date_query'     => array(
            array(
                'after'     => $From,
                'before'    => $To,
                'inclusive' => true,
            ),
        ),
    ));

    $Counters  = callnumber_counters($From, $To);
    $SiteName  = get_bloginfo('name');
    $FromLabel = date('Y-m-d', strtotime($From));
    $ToLabel   = date('Y-m-d', strtotime($To));

    // إحصائية الصفحات الأكثر اتصالاً
    $Pages = array();
    foreach( $Query->posts as $post ) {
        $page = get_post_meta($post->ID, 'page', true);
        $page = ( $page === '' || $page === false ) ? 'غير معروفة' : $page;
        $type = callnumber_get_type($post->ID);
        if( !isset($Pages[$page]) ) {
            $Pages[$page] = array('total'=>0,'phone'=>0,'whatsapp'=>0,'unknown'=>0);
        }
        $Pages[$page]['total']++;
        $Pages[$page][$type]++;
    }
    uasort($Pages, function($a, $b){ return $b['total'] - $a['total']; });
    $TopPages = array_slice($Pages, 0, 15, true);

    nocache_headers();
    header('Content-Type: text/html; charset=utf-8');

    echo '<!DOCTYPE html>';
    echo '<html dir="rtl" lang="ar"><head><meta charset="utf-8">';
    echo '<title>تقرير المكالمات - '.esc_html($FromLabel).' إلى '.esc_html($ToLabel).'</title>';
    echo '<style>
        * { box-sizing: border-box; }
        body { font-family: Tahoma, "Segoe UI", Arial, sans-serif; background: #f4f7fb; color: #1a2233; margin: 0; padding: 28px; }
        .report-wrap { max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 14px; }
        .report-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; border-bottom: 2px solid #eef2f8; padding-bottom: 18px; margin-bottom: 22px; flex-wrap: wrap; }
        .report-head h1 { font-size: 22px; margin: 0 0 6px; }
        .report-head p { margin: 0; font-size: 13px; color: #6b7a90; }
        .report-print { background: #1269eb; color: #fff; border: 0; border-radius: 8px; padding: 10px 20px; font-size: 14px; font-family: inherit; cursor: pointer; }
        .report-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 26px; }
        .report-stat { border: 1px solid #e3ecf7; border-radius: 12px; padding: 14px 16px; }
        .report-stat b { display: block; font-size: 26px; margin-bottom: 4px; }
        .report-stat span { font-size: 13px; color: #6b7a90; }
        .report-stat.is-whatsapp b { color: #128c4b; }
        .report-stat.is-phone b { color: #1269eb; }
        .report-stat.is-total b { color: #1a2233; }
        h2 { font-size: 17px; margin: 26px 0 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { border: 1px solid #e3ecf7; padding: 8px 10px; text-align: right; }
        th { background: #f4f7fb; font-weight: 700; }
        tr:nth-child(even) td { background: #fafcff; }
        .tag { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .tag.phone { color: #1269eb; background: #e8f1ff; }
        .tag.whatsapp { color: #128c4b; background: #e6f9ee; }
        .tag.unknown { color: #7b8aa1; background: #f1f4f9; }
        .report-empty { padding: 30px; text-align: center; color: #6b7a90; }
        @media print {
            body { background: #fff; padding: 0; }
            .report-wrap { padding: 0; border-radius: 0; max-width: none; }
            .report-print { display: none; }
            tr { page-break-inside: avoid; }
        }
    </style>';
    echo '</head><body>';
    echo '<div class="report-wrap">';

    echo '<div class="report-head">';
        echo '<div>';
            echo '<h1>تقرير المكالمات الشهري</h1>';
            echo '<p>'.esc_html($SiteName).' — من '.esc_html($FromLabel).' إلى '.esc_html($ToLabel).'</p>';
        echo '</div>';
        echo '<button type="button" class="report-print" onclick="window.print()">حفظ كـ PDF / طباعة</button>';
    echo '</div>';

    echo '<div class="report-stats">';
        echo '<div class="report-stat is-whatsapp"><b>'.number_format_i18n($Counters['whatsapp']).'</b><span>اتصال واتساب</span></div>';
        echo '<div class="report-stat is-phone"><b>'.number_format_i18n($Counters['phone']).'</b><span>اتصال هاتفي</span></div>';
        echo '<div class="report-stat is-total"><b>'.number_format_i18n($Counters['total']).'</b><span>إجمالي المكالمات</span></div>';
    echo '</div>';

    if( !empty($TopPages) ) {
        echo '<h2>أكثر الصفحات اتصالاً</h2>';
        echo '<table><thead><tr><th>الصفحة</th><th>واتساب</th><th>هاتفي</th><th>الإجمالي</th></tr></thead><tbody>';
        foreach( $TopPages as $PageName => $Row ) {
            echo '<tr>';
            echo '<td>'.esc_html($PageName).'</td>';
            echo '<td>'.number_format_i18n($Row['whatsapp']).'</td>';
            echo '<td>'.number_format_i18n($Row['phone']).'</td>';
            echo '<td>'.number_format_i18n($Row['total']).'</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    echo '<h2>تفاصيل المكالمات ('.number_format_i18n($Counters['total']).')</h2>';

    if( empty($Query->posts) ) {
        echo '<div class="report-empty">لا توجد مكالمات مسجلة خلال آخر شهر</div>';
    } else {
        echo '<table><thead><tr><th>#</th><th>اسم الصفحة</th><th>نوع الاتصال</th><th>التاريخ</th><th>الوقت</th></tr></thead><tbody>';
        $i = 0;
        foreach( $Query->posts as $post ) {
            $i++;
            $page = get_post_meta($post->ID, 'page', true);
            $type = callnumber_get_type($post->ID);
            $Formatted = callnumber_format_time( get_post_meta($post->ID, 'calldate', true) );
            echo '<tr>';
            echo '<td>'.number_format_i18n($i).'</td>';
            echo '<td>'.esc_html($page).'</td>';
            echo '<td><span class="tag '.$type.'">'.esc_html($Types[$type]['label']).'</span></td>';
            echo '<td>'.( ($Formatted !== false) ? esc_html($Formatted['date']) : '—' ).'</td>';
            echo '<td>'.( ($Formatted !== false) ? esc_html($Formatted['time'].' '.$Formatted['period']) : '—' ).'</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    echo '</div>';
    echo '<script>window.addEventListener("load", function(){ setTimeout(function(){ window.print(); }, 400); });</script>';
    echo '</body></html>';

    exit;
}

add_action( 'admin_post_callnumber_monthly_report', 'callnumber_monthly_report' );

?>
