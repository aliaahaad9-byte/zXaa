<?php
/**
 * Calls Tracking Dashboard (admin.php?page=add_callnumber_fields)
 *
 * - KPI cards (WhatsApp / Phone / Total) for the selected period.
 * - Top 5 converting pages widget.
 * - Logs table with call-type badge, page link icon, Asia/Riyadh 12h time.
 * - AJAX quick filters (today / week / month / all).
 * - Secure monthly delete (nonce + confirmation modal) and printable PDF report.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// منطق تسجيل النقرات (مشترك بين admin-ajax والمسار القديم)
require_once __DIR__ . '/tracker.php';

/* -------------------------------------------------------------------------
 * Post type (hidden, storage only)
 * ---------------------------------------------------------------------- */
add_action( 'init', function () {
    register_post_type(
        'callwebsite',
        array(
            'label'               => 'سجل المكالمات',
            'public'              => false,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'show_in_rest'        => false,
            'exclude_from_search' => true,
            'supports'            => array( 'title' ),
        )
    );
} );

/* -------------------------------------------------------------------------
 * Admin menu
 * ---------------------------------------------------------------------- */
function callnumber() {
    add_menu_page(
        'صفحة المكالمات',
        'صفحة المكالمات',
        'manage_options',
        'add_callnumber_fields',
        'add_callnumber_fields',
        'dashicons-phone',
        6
    );
}
add_action( 'admin_menu', 'callnumber' );

/* -------------------------------------------------------------------------
 * Assets — loaded ONLY on this dashboard page
 * ---------------------------------------------------------------------- */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( 'toplevel_page_add_callnumber_fields' !== $hook ) {
        return;
    }

    $base = get_template_directory_uri() . '/components/packs/callnumber/';
    $ver  = '2.0.0';

    wp_enqueue_style( 'callnumber-admin', $base . 'admin.css', array(), $ver );
    wp_enqueue_script( 'callnumber-admin', $base . 'admin.js', array( 'jquery' ), $ver, true );

    wp_localize_script(
        'callnumber-admin',
        'CallnumberData',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'callnumber_admin' ),
        )
    );
} );

/* -------------------------------------------------------------------------
 * Helpers
 * ---------------------------------------------------------------------- */

/** Riyadh timezone object (GMT+3, no DST). */
function callnumber_tz() {
    return new DateTimeZone( 'Asia/Riyadh' );
}

/**
 * UTC lower bound ('Y-m-d H:i:s') for a quick-filter range, or '' for all time.
 * Ranges are computed on Riyadh local time (week starts Sunday).
 */
function callnumber_range_start_utc( $range ) {
    $tz = callnumber_tz();

    switch ( $range ) {
        case 'today':
            $start = new DateTime( 'today', $tz );
            break;
        case 'week':
            $start = new DateTime( 'today', $tz );
            $start->modify( '-' . (int) $start->format( 'w' ) . ' days' );
            break;
        case 'month':
            $start = new DateTime( ( new DateTime( 'now', $tz ) )->format( 'Y-m-01' ) . ' 00:00:00', $tz );
            break;
        default:
            return '';
    }

    $start->setTimezone( new DateTimeZone( 'UTC' ) );
    return $start->format( 'Y-m-d H:i:s' );
}

/** UTC bounds [start, end) for a Riyadh month given as 'YYYY-MM'. */
function callnumber_month_bounds_utc( $month ) {
    if ( ! preg_match( '/^\d{4}-(0[1-9]|1[0-2])$/', $month ) ) {
        return false;
    }
    $tz    = callnumber_tz();
    $start = new DateTime( $month . '-01 00:00:00', $tz );
    $end   = clone $start;
    $end->modify( '+1 month' );

    $utc = new DateTimeZone( 'UTC' );
    $start->setTimezone( $utc );
    $end->setTimezone( $utc );

    return array( $start->format( 'Y-m-d H:i:s' ), $end->format( 'Y-m-d H:i:s' ) );
}

/** Shared WHERE clause for the callwebsite log rows. */
function callnumber_where_sql( $start_utc, $end_utc = '' ) {
    global $wpdb;
    $where = $wpdb->prepare( "p.post_type = %s AND p.post_status = 'publish'", 'callwebsite' );
    if ( $start_utc ) {
        $where .= $wpdb->prepare( ' AND p.post_date_gmt >= %s', $start_utc );
    }
    if ( $end_utc ) {
        $where .= $wpdb->prepare( ' AND p.post_date_gmt < %s', $end_utc );
    }
    return $where;
}

/** Totals per click type for a range: [whatsapp, call, unknown, total]. */
function callnumber_get_stats( $start_utc, $end_utc = '' ) {
    global $wpdb;
    $where = callnumber_where_sql( $start_utc, $end_utc );

    $rows = $wpdb->get_results(
        "SELECT COALESCE(pm.meta_value, '') AS call_type, COUNT(*) AS total
         FROM {$wpdb->posts} p
         LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'call_type'
         WHERE {$where}
         GROUP BY COALESCE(pm.meta_value, '')"
    );

    $stats = array( 'whatsapp' => 0, 'call' => 0, 'unknown' => 0, 'total' => 0 );
    foreach ( (array) $rows as $row ) {
        $count = (int) $row->total;
        if ( 'whatsapp' === $row->call_type ) {
            $stats['whatsapp'] += $count;
        } elseif ( 'call' === $row->call_type ) {
            $stats['call'] += $count;
        } else {
            $stats['unknown'] += $count;
        }
        $stats['total'] += $count;
    }
    return $stats;
}

/** Top converting pages for a range: [{page, page_url, clicks}]. */
function callnumber_get_top_pages( $start_utc, $limit = 5, $end_utc = '' ) {
    global $wpdb;
    $where = callnumber_where_sql( $start_utc, $end_utc );

    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT pm.meta_value AS page, MAX(pmu.meta_value) AS page_url, COUNT(*) AS clicks
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm  ON pm.post_id  = p.ID AND pm.meta_key  = 'page'
             LEFT JOIN  {$wpdb->postmeta} pmu ON pmu.post_id = p.ID AND pmu.meta_key = 'page_url' AND pmu.meta_value <> ''
             WHERE {$where} AND pm.meta_value <> ''
             GROUP BY pm.meta_value
             ORDER BY clicks DESC
             LIMIT %d",
            $limit
        )
    );
}

/** Distinct months (Riyadh time, 'YYYY-MM', newest first) that contain records. */
function callnumber_get_months() {
    global $wpdb;
    $where = callnumber_where_sql( '' );
    return $wpdb->get_col(
        "SELECT DISTINCT DATE_FORMAT(DATE_ADD(p.post_date_gmt, INTERVAL 3 HOUR), '%Y-%m')
         FROM {$wpdb->posts} p
         WHERE {$where}
         ORDER BY 1 DESC"
    );
}


/** عدد السجلات المهملة: بلا اسم صفحة وبلا نوع اتصال (طلبات آلية أو مباشرة). */
function callnumber_junk_count() {
    global $wpdb;
    $where = callnumber_where_sql( '' );
    return (int) $wpdb->get_var(
        "SELECT COUNT(*)
         FROM {$wpdb->posts} p
         LEFT JOIN {$wpdb->postmeta} pg ON pg.post_id = p.ID AND pg.meta_key = 'page'
         LEFT JOIN {$wpdb->postmeta} pt ON pt.post_id = p.ID AND pt.meta_key = 'call_type'
         WHERE {$where}
           AND COALESCE(NULLIF(TRIM(pg.meta_value), ''), '') = ''
           AND COALESCE(NULLIF(TRIM(pt.meta_value), ''), '') = ''"
    );
}

/** معرّفات السجلات المهملة. */
function callnumber_junk_ids( $limit = 2000 ) {
    global $wpdb;
    $where = callnumber_where_sql( '' );
    return $wpdb->get_col(
        $wpdb->prepare(
            "SELECT p.ID
             FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pg ON pg.post_id = p.ID AND pg.meta_key = 'page'
             LEFT JOIN {$wpdb->postmeta} pt ON pt.post_id = p.ID AND pt.meta_key = 'call_type'
             WHERE {$where}
               AND COALESCE(NULLIF(TRIM(pg.meta_value), ''), '') = ''
               AND COALESCE(NULLIF(TRIM(pt.meta_value), ''), '') = ''
             LIMIT %d",
            $limit
        )
    );
}


/**
 * حذف سريع بالجملة: استعلامان لكل دفعة بدل wp_delete_post لكل سجل.
 * آمن هنا لأن callwebsite نوع تخزين فقط: بلا مرفقات ولا تصنيفات ولا مراجعات.
 */
function callnumber_delete_ids( $ids ) {
    global $wpdb;
    $ids = array_values( array_filter( array_map( 'absint', (array) $ids ) ) );
    if ( ! $ids ) {
        return 0;
    }

    $deleted = 0;
    foreach ( array_chunk( $ids, 500 ) as $chunk ) {
        $ph = implode( ',', array_fill( 0, count( $chunk ), '%d' ) );

        // نتأكد أن المعرّفات من هذا النوع فقط قبل أي حذف
        $safe = $wpdb->get_col( $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'callwebsite' AND ID IN ($ph)",
            $chunk
        ) );
        if ( ! $safe ) {
            continue;
        }

        $ph2 = implode( ',', array_fill( 0, count( $safe ), '%d' ) );
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE post_id IN ($ph2)", $safe ) );
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE ID IN ($ph2)", $safe ) );
        $deleted += count( $safe );
    }

    if ( $deleted ) {
        // wp_count_posts يخزّن العدد مؤقتًا — نُبطله حتى تظهر الأرقام الصحيحة فورًا
        wp_cache_delete( 'posts-callwebsite', 'counts' );
    }
    return $deleted;
}

/** عدد سجلات شهر معيّن. */
function callnumber_month_count( $bounds ) {
    global $wpdb;
    $where = callnumber_where_sql( $bounds[0], $bounds[1] );
    return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} p WHERE {$where}" );
}

/** One page of log records, newest first. */
function callnumber_get_records( $start_utc, $paged, $per_page, &$found = 0, $end_utc = '' ) {
    global $wpdb;
    $where  = callnumber_where_sql( $start_utc, $end_utc );
    $offset = max( 0, ( $paged - 1 ) * $per_page );

    $found = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} p WHERE {$where}" );

    $ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p WHERE {$where} ORDER BY p.post_date_gmt DESC, p.ID DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        )
    );

    $records = array();
    foreach ( $ids as $id ) {
        $records[] = array(
            'id'        => (int) $id,
            'page'      => get_post_meta( $id, 'page', true ),
            'page_url'  => get_post_meta( $id, 'page_url', true ),
            'call_type' => get_post_meta( $id, 'call_type', true ),
            'calldate'  => get_post_meta( $id, 'calldate', true ),
            'date_gmt'  => get_post( $id )->post_date_gmt,
        );
    }
    return $records;
}

/** Formats a UTC datetime string in Riyadh time: ['date' => Y-m-d, 'time' => h:i صباحًا/مساءً]. */
function callnumber_format_datetime( $utc_string ) {
    if ( empty( $utc_string ) || '0000-00-00 00:00:00' === $utc_string ) {
        return array( 'date' => '—', 'time' => '—' );
    }
    try {
        $dt = new DateTime( $utc_string, new DateTimeZone( 'UTC' ) );
    } catch ( Exception $e ) {
        return array( 'date' => '—', 'time' => '—' );
    }
    $dt->setTimezone( callnumber_tz() );

    return array(
        'date' => $dt->format( 'Y-m-d' ),
        'time' => $dt->format( 'h:i' ) . ' ' . ( 'AM' === $dt->format( 'A' ) ? 'صباحًا' : 'مساءً' ),
    );
}

/** Arabic label + badge class for a call type. */
function callnumber_type_label( $type ) {
    if ( 'whatsapp' === $type ) {
        return array( 'واتساب', 'cn-badge--whatsapp' );
    }
    if ( 'call' === $type ) {
        return array( 'اتصال', 'cn-badge--call' );
    }
    return array( 'غير محدد', 'cn-badge--unknown' );
}

/* -------------------------------------------------------------------------
 * HTML partials (used by the initial render AND the AJAX responses)
 * ---------------------------------------------------------------------- */

function callnumber_svg( $name ) {
    $icons = array(
        'whatsapp' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M.057 24l1.687-6.163A11.867 11.867 0 0 1 .157 11.9C.16 5.336 5.495 0 12.05 0a11.82 11.82 0 0 1 8.413 3.488 11.824 11.824 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>',
        'phone'    => '<svg viewBox="0 0 512 512" fill="currentColor" aria-hidden="true"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>',
        'total'    => '<svg viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"><path d="M64 64C28.7 64 0 92.7 0 128V384c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H64zM96 192a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm32 128a64 64 0 1 1 0-128 64 64 0 1 1 0 128zm144-160h192c8.8 0 16 7.2 16 16s-7.2 16-16 16H272c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 96h192c8.8 0 16 7.2 16 16s-7.2 16-16 16H272c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 96H400c8.8 0 16 7.2 16 16s-7.2 16-16 16H272c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>',
        'link'     => '<svg viewBox="0 0 512 512" fill="currentColor" aria-hidden="true"><path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z"/></svg>',
        'trash'    => '<svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>',
        'pdf'      => '<svg viewBox="0 0 512 512" fill="currentColor" aria-hidden="true"><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64z"/></svg>',
    );
    return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/** The three KPI cards. */
function callnumber_render_cards( $stats ) {
    ob_start();
    $cards = array(
        array( 'whatsapp', 'إجمالي اتصالات الواتساب', $stats['whatsapp'], 'cn-card--whatsapp' ),
        array( 'phone', 'إجمالي الاتصالات الهاتفية', $stats['call'], 'cn-card--call' ),
        array( 'total', 'الإجمالي الكلي للتحويلات', $stats['total'], 'cn-card--total' ),
    );
    echo '<div class="cn-cards">';
    foreach ( $cards as $card ) {
        echo '<div class="cn-card ' . esc_attr( $card[3] ) . '">';
        echo '<div class="cn-card__icon">' . callnumber_svg( $card[0] ) . '</div>';
        echo '<div class="cn-card__body">';
        echo '<span class="cn-card__label">' . esc_html( $card[1] ) . '</span>';
        echo '<span class="cn-card__value" data-count="' . (int) $card[2] . '">' . number_format_i18n( (int) $card[2] ) . '</span>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    return ob_get_clean();
}

/** Top 5 converting pages widget. */
function callnumber_render_top_pages( $top_pages, $total ) {
    ob_start();
    echo '<div class="cn-panel cn-top-pages">';
    echo '<h3 class="cn-panel__title">أفضل 5 صفحات تحويلًا</h3>';

    if ( empty( $top_pages ) ) {
        echo '<p class="cn-empty">لا توجد بيانات في هذه الفترة.</p>';
    } else {
        echo '<ol class="cn-top-list">';
        foreach ( $top_pages as $row ) {
            $clicks  = (int) $row->clicks;
            $percent = $total > 0 ? round( ( $clicks / $total ) * 100, 1 ) : 0;
            echo '<li class="cn-top-item">';
            echo '<div class="cn-top-item__info">';
            echo '<span class="cn-top-item__name" title="' . esc_attr( $row->page ) . '">' . esc_html( $row->page ) . '</span>';
            if ( ! empty( $row->page_url ) ) {
                echo '<a class="cn-link-icon" href="' . esc_url( $row->page_url ) . '" target="_blank" rel="noopener noreferrer" title="فتح الصفحة">' . callnumber_svg( 'link' ) . '</a>';
            }
            echo '</div>';
            echo '<div class="cn-top-item__meta">';
            echo '<span class="cn-top-item__clicks">' . number_format_i18n( $clicks ) . ' نقرة</span>';
            echo '<span class="cn-top-item__percent">' . esc_html( $percent ) . '%</span>';
            echo '</div>';
            echo '<div class="cn-top-item__bar"><span style="width:' . esc_attr( min( 100, $percent ) ) . '%"></span></div>';
            echo '</li>';
        }
        echo '</ol>';
    }
    echo '</div>';
    return ob_get_clean();
}

/** Logs table body rows. */
function callnumber_render_rows( $records ) {
    ob_start();
    if ( empty( $records ) ) {
        echo '<tr class="cn-row-empty"><td colspan="5">لا توجد سجلات في هذه الفترة.</td></tr>';
        return ob_get_clean();
    }

    foreach ( $records as $rec ) {
        $when = callnumber_format_datetime( $rec['calldate'] ? $rec['calldate'] : $rec['date_gmt'] );
        list( $type_label, $type_class ) = callnumber_type_label( $rec['call_type'] );

        echo '<tr data-record-id="' . (int) $rec['id'] . '">';
        echo '<td class="cn-col-check"><input type="checkbox" class="cn-row-check" value="' . (int) $rec['id'] . '"></td>';

        echo '<td class="cn-col-page">';
        echo '<span class="cn-page-name" title="' . esc_attr( $rec['page'] ) . '">' . esc_html( $rec['page'] ? $rec['page'] : '—' ) . '</span>';
        if ( ! empty( $rec['page_url'] ) ) {
            echo '<a class="cn-link-icon" href="' . esc_url( $rec['page_url'] ) . '" target="_blank" rel="noopener noreferrer" title="فتح الصفحة">' . callnumber_svg( 'link' ) . '</a>';
        }
        echo '</td>';

        echo '<td class="cn-col-type"><span class="cn-badge ' . esc_attr( $type_class ) . '">';
        if ( 'whatsapp' === $rec['call_type'] ) {
            echo callnumber_svg( 'whatsapp' );
        } elseif ( 'call' === $rec['call_type'] ) {
            echo callnumber_svg( 'phone' );
        }
        echo '<span>' . esc_html( $type_label ) . '</span></span></td>';

        echo '<td class="cn-col-date"><span class="cn-date">' . esc_html( $when['date'] ) . '</span><span class="cn-time">' . esc_html( $when['time'] ) . '</span></td>';

        echo '<td class="cn-col-actions"><button type="button" class="cn-btn-icon cn-delete-row" title="حذف السجل">' . callnumber_svg( 'trash' ) . '</button></td>';
        echo '</tr>';
    }
    return ob_get_clean();
}

/** Pagination bar. */
function callnumber_render_pagination( $paged, $found, $per_page ) {
    $pages = max( 1, (int) ceil( $found / $per_page ) );
    ob_start();
    echo '<div class="cn-pagination" data-pages="' . (int) $pages . '">';
    echo '<span class="cn-pagination__info">' . number_format_i18n( $found ) . ' سجل — صفحة ' . number_format_i18n( $paged ) . ' من ' . number_format_i18n( $pages ) . '</span>';
    echo '<div class="cn-pagination__nav">';
    echo '<button type="button" class="cn-btn cn-page-btn" data-page="' . max( 1, $paged - 1 ) . '"' . ( $paged <= 1 ? ' disabled' : '' ) . '>السابق</button>';
    echo '<button type="button" class="cn-btn cn-page-btn" data-page="' . min( $pages, $paged + 1 ) . '"' . ( $paged >= $pages ? ' disabled' : '' ) . '>التالي</button>';
    echo '</div>';
    echo '</div>';
    return ob_get_clean();
}

/* -------------------------------------------------------------------------
 * Page render
 * ---------------------------------------------------------------------- */
function add_callnumber_fields() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'غير مصرح لك بالوصول إلى هذه الصفحة.' );
    }

    $per_page  = 20;
    $range     = 'all';
    $start_utc = callnumber_range_start_utc( $range );

    $stats     = callnumber_get_stats( $start_utc );
    $top_pages = callnumber_get_top_pages( $start_utc );
    $found     = 0;
    $records   = callnumber_get_records( $start_utc, 1, $per_page, $found );
    $months    = callnumber_get_months();

    $filters = array(
        'today' => 'اليوم',
        'week'  => 'هذا الأسبوع',
        'month' => 'هذا الشهر',
        'all'   => 'كل الفترات',
    );

    echo '<div class="wrap cn-dash" dir="rtl">';

    /* Header */
    echo '<div class="cn-header">';
    echo '<div class="cn-header__title">';
    echo '<span class="cn-header__icon">' . callnumber_svg( 'phone' ) . '</span>';
    echo '<div><h1>لوحة تتبع المكالمات</h1><p>إحصائيات نقرات الاتصال المباشر والواتساب — توقيت الرياض (GMT+3)</p></div>';
    echo '</div>';

    echo '<div class="cn-header__tools">';
    echo '<select id="cn-month-select" class="cn-select" aria-label="اختيار الشهر">';
    if ( empty( $months ) ) {
        echo '<option value="">لا توجد أشهر</option>';
    } else {
        foreach ( $months as $month ) {
            echo '<option value="' . esc_attr( $month ) . '">' . esc_html( $month ) . '</option>';
        }
    }
    echo '</select>';

    $export_base = wp_nonce_url( admin_url( 'admin-post.php?action=callnumber_export_pdf' ), 'callnumber_export' );
    echo '<a id="cn-export-pdf" class="cn-btn cn-btn--primary" href="#" data-base="' . esc_url( $export_base ) . '" target="_blank" rel="noopener">' . callnumber_svg( 'pdf' ) . '<span>تصدير تقرير PDF</span></a>';
    echo '<button type="button" id="cn-delete-month" class="cn-btn cn-btn--danger">' . callnumber_svg( 'trash' ) . '<span>حذف سجلات الشهر</span></button>';
    echo '<button type="button" id="cn-diag" class="cn-btn">فحص التتبع</button>';
    echo '</div>';
    echo '</div>';
    echo '<div id="cn-diag-box" class="cn-diag" hidden></div>';

    // تنبيه السجلات المهملة
    $junk = callnumber_junk_count();
    if ( $junk > 0 ) {
        echo '<div class="cn-junk" id="cn-junk-note">';
        echo '<div class="cn-junk__txt"><strong>' . number_format_i18n( $junk ) . ' سجل مهمل</strong>';
        echo '<span>سجلات بلا اسم صفحة وبلا نوع اتصال — نتجت عن طلبات آلية وصلت لنقطة التتبع، وهي تضخّم الإجمالي بلا فائدة.</span></div>';
        echo '<button type="button" id="cn-clean-junk" class="cn-btn cn-btn--danger">' . callnumber_svg( 'trash' ) . '<span>تنظيف السجلات المهملة</span></button>';
        echo '</div>';
    }

    /* Quick filters */
    echo '<div class="cn-filters" role="tablist" aria-label="فلترة الفترة الزمنية">';
    foreach ( $filters as $key => $label ) {
        echo '<button type="button" class="cn-filter' . ( $key === $range ? ' is-active' : '' ) . '" data-range="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</button>';
    }
    echo '</div>';

    /* KPI cards */
    echo '<div id="cn-cards-wrap">' . callnumber_render_cards( $stats ) . '</div>';

    echo '<div class="cn-grid">';

    /* Top pages */
    echo '<div id="cn-top-pages-wrap">' . callnumber_render_top_pages( $top_pages, $stats['total'] ) . '</div>';

    /* Logs table */
    echo '<div class="cn-panel cn-table-panel">';
    echo '<div class="cn-table-panel__head">';
    echo '<h3 class="cn-panel__title">سجل المكالمات</h3>';
    echo '<button type="button" id="cn-delete-selected" class="cn-btn cn-btn--danger-soft" disabled>' . callnumber_svg( 'trash' ) . '<span>حذف المحدد</span></button>';
    echo '</div>';
    echo '<div class="cn-table-scroll">';
    echo '<table class="cn-table">';
    echo '<thead><tr>';
    echo '<th class="cn-col-check"><input type="checkbox" id="cn-check-all" aria-label="تحديد الكل"></th>';
    echo '<th>اسم الصفحة</th>';
    echo '<th>نوع الاتصال</th>';
    echo '<th>التاريخ والوقت</th>';
    echo '<th>حذف</th>';
    echo '</tr></thead>';
    echo '<tbody id="cn-table-body">' . callnumber_render_rows( $records ) . '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '<div id="cn-pagination-wrap">' . callnumber_render_pagination( 1, $found, $per_page ) . '</div>';
    echo '</div>';

    echo '</div>'; // .cn-grid

    /* Confirmation modal */
    echo '<div id="cn-modal" class="cn-modal" hidden>';
    echo '<div class="cn-modal__backdrop"></div>';
    echo '<div class="cn-modal__box" role="dialog" aria-modal="true" aria-labelledby="cn-modal-title">';
    echo '<h3 id="cn-modal-title">تأكيد الحذف</h3>';
    echo '<p id="cn-modal-text">هل أنت متأكد؟ لا يمكن التراجع عن هذا الإجراء.</p>';
    echo '<div class="cn-modal__actions">';
    echo '<button type="button" id="cn-modal-confirm" class="cn-btn cn-btn--danger">نعم، احذف</button>';
    echo '<button type="button" id="cn-modal-cancel" class="cn-btn">إلغاء</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div id="cn-toast" class="cn-toast" hidden></div>';

    echo '</div>'; // .cn-dash
}

/* -------------------------------------------------------------------------
 * AJAX: filters + pagination
 * ---------------------------------------------------------------------- */
add_action( 'wp_ajax_callnumber_filter', function () {
    check_ajax_referer( 'callnumber_admin', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'غير مصرح.' ), 403 );
    }

    $allowed = array( 'today', 'week', 'month', 'all' );
    $range   = isset( $_POST['range'] ) ? sanitize_key( $_POST['range'] ) : 'all';
    if ( ! in_array( $range, $allowed, true ) ) {
        $range = 'all';
    }
    $paged    = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;
    $per_page = 20;

    $start_utc = callnumber_range_start_utc( $range );
    $stats     = callnumber_get_stats( $start_utc );
    $top_pages = callnumber_get_top_pages( $start_utc );
    $found     = 0;
    $records   = callnumber_get_records( $start_utc, $paged, $per_page, $found );

    wp_send_json_success(
        array(
            'cards'      => callnumber_render_cards( $stats ),
            'top_pages'  => callnumber_render_top_pages( $top_pages, $stats['total'] ),
            'rows'       => callnumber_render_rows( $records ),
            'pagination' => callnumber_render_pagination( $paged, $found, $per_page ),
        )
    );
} );

/* -------------------------------------------------------------------------
 * AJAX: delete selected records
 * ---------------------------------------------------------------------- */
add_action( 'wp_ajax_callnumber_delete_records', function () {
    check_ajax_referer( 'callnumber_admin', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'غير مصرح.' ), 403 );
    }

    $ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) $_POST['ids'] ) : array();
    $ids = array_filter( array_unique( $ids ) );

    wp_send_json_success( array( 'deleted' => callnumber_delete_ids( $ids ) ) );
} );

/* -------------------------------------------------------------------------
 * AJAX: delete a whole month
 * ---------------------------------------------------------------------- */
add_action( 'wp_ajax_callnumber_delete_month', function () {
    check_ajax_referer( 'callnumber_admin', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'غير مصرح.' ), 403 );
    }

    $month  = isset( $_POST['month'] ) ? sanitize_text_field( wp_unslash( $_POST['month'] ) ) : '';
    $bounds = callnumber_month_bounds_utc( $month );
    if ( ! $bounds ) {
        wp_send_json_error( array( 'message' => 'صيغة الشهر غير صحيحة.' ), 400 );
    }

    global $wpdb;
    $where = callnumber_where_sql( $bounds[0], $bounds[1] );

    // دفعة واحدة لكل طلب حتى لا تتجاوز مهلة التنفيذ مهما كان عدد السجلات
    $ids = $wpdb->get_col( "SELECT p.ID FROM {$wpdb->posts} p WHERE {$where} LIMIT 1000" );

    $deleted   = callnumber_delete_ids( $ids );
    $remaining = callnumber_month_count( $bounds );

    wp_send_json_success( array( 'deleted' => $deleted, 'remaining' => $remaining ) );
} );


/* -------------------------------------------------------------------------
 * AJAX: تنظيف السجلات المهملة
 * ---------------------------------------------------------------------- */
add_action( 'wp_ajax_callnumber_clean_junk', function () {
    check_ajax_referer( 'callnumber_admin', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'غير مصرح.' ), 403 );
    }

    $deleted = callnumber_delete_ids( callnumber_junk_ids( 1000 ) );

    wp_send_json_success( array(
        'deleted'   => $deleted,
        'remaining' => callnumber_junk_count(),
    ) );
} );

/* -------------------------------------------------------------------------
 * Monthly PDF report (printable — opens the browser's save-as-PDF dialog)
 * ---------------------------------------------------------------------- */
add_action( 'admin_post_callnumber_export_pdf', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'غير مصرح.' );
    }
    check_admin_referer( 'callnumber_export' );

    $month  = isset( $_GET['month'] ) ? sanitize_text_field( wp_unslash( $_GET['month'] ) ) : '';
    $bounds = callnumber_month_bounds_utc( $month );
    if ( ! $bounds ) {
        wp_die( 'صيغة الشهر غير صحيحة.' );
    }

    $stats     = callnumber_get_stats( $bounds[0], $bounds[1] );
    $top_pages = callnumber_get_top_pages( $bounds[0], 5, $bounds[1] );
    $found     = 0;
    $records   = callnumber_get_records( $bounds[0], 1, 5000, $found, $bounds[1] );
    $site_name = get_bloginfo( 'name' );

    header( 'Content-Type: text/html; charset=utf-8' );
    ?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<title>تقرير المكالمات — <?php echo esc_html( $month ); ?></title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: -apple-system, "Segoe UI", Tahoma, Arial, sans-serif; color: #1e293b; background: #fff; padding: 32px; direction: rtl; }
    .rep-head { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #0f766e; padding-bottom: 16px; margin-bottom: 24px; }
    .rep-head h1 { font-size: 22px; color: #0f766e; }
    .rep-head p { color: #64748b; font-size: 13px; margin-top: 4px; }
    .rep-cards { display: flex; gap: 12px; margin-bottom: 24px; }
    .rep-card { flex: 1; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; text-align: center; }
    .rep-card strong { display: block; font-size: 26px; margin-top: 6px; }
    .rep-card--wa strong { color: #16a34a; }
    .rep-card--call strong { color: #2563eb; }
    .rep-card--total strong { color: #7c3aed; }
    h2 { font-size: 16px; margin: 22px 0 10px; color: #0f172a; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: right; }
    th { background: #f1f5f9; }
    tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 2px 10px; border-radius: 99px; font-size: 12px; }
    .badge-wa { background: #dcfce7; color: #15803d; }
    .badge-call { background: #dbeafe; color: #1d4ed8; }
    .badge-un { background: #e2e8f0; color: #475569; }
    .rep-footer { margin-top: 28px; color: #94a3b8; font-size: 12px; text-align: center; }
    @media print { body { padding: 0; } .no-print { display: none; } }
    .no-print { margin-bottom: 20px; }
    .no-print button { background: #0f766e; color: #fff; border: 0; padding: 10px 22px; border-radius: 8px; font-size: 14px; cursor: pointer; }
</style>
</head>
<body>
<div class="no-print"><button onclick="window.print()">حفظ / طباعة PDF</button></div>
<div class="rep-head">
    <div>
        <h1>تقرير المكالمات الشهري</h1>
        <p><?php echo esc_html( $site_name ); ?> — شهر <?php echo esc_html( $month ); ?> — توقيت الرياض (GMT+3)</p>
    </div>
</div>

<div class="rep-cards">
    <div class="rep-card rep-card--wa">اتصالات الواتساب<strong><?php echo (int) $stats['whatsapp']; ?></strong></div>
    <div class="rep-card rep-card--call">الاتصالات الهاتفية<strong><?php echo (int) $stats['call']; ?></strong></div>
    <div class="rep-card rep-card--total">الإجمالي الكلي<strong><?php echo (int) $stats['total']; ?></strong></div>
</div>

<h2>أفضل الصفحات تحويلًا</h2>
<table>
    <thead><tr><th>الصفحة</th><th>عدد النقرات</th><th>نسبة التحويل</th></tr></thead>
    <tbody>
    <?php if ( empty( $top_pages ) ) : ?>
        <tr><td colspan="3">لا توجد بيانات.</td></tr>
    <?php else : foreach ( $top_pages as $row ) :
        $percent = $stats['total'] > 0 ? round( ( (int) $row->clicks / $stats['total'] ) * 100, 1 ) : 0; ?>
        <tr>
            <td><?php echo esc_html( $row->page ); ?></td>
            <td><?php echo (int) $row->clicks; ?></td>
            <td><?php echo esc_html( $percent ); ?>%</td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<h2>سجل العمليات (<?php echo (int) $found; ?> سجل)</h2>
<table>
    <thead><tr><th>#</th><th>الصفحة</th><th>نوع الاتصال</th><th>التاريخ</th><th>الوقت</th></tr></thead>
    <tbody>
    <?php if ( empty( $records ) ) : ?>
        <tr><td colspan="5">لا توجد سجلات لهذا الشهر.</td></tr>
    <?php else :
        $i = 0;
        foreach ( $records as $rec ) :
            $i++;
            $when = callnumber_format_datetime( $rec['calldate'] ? $rec['calldate'] : $rec['date_gmt'] );
            list( $type_label ) = callnumber_type_label( $rec['call_type'] );
            $badge = 'whatsapp' === $rec['call_type'] ? 'badge-wa' : ( 'call' === $rec['call_type'] ? 'badge-call' : 'badge-un' ); ?>
        <tr>
            <td><?php echo (int) $i; ?></td>
            <td><?php echo esc_html( $rec['page'] ? $rec['page'] : '—' ); ?></td>
            <td><span class="badge <?php echo esc_attr( $badge ); ?>"><?php echo esc_html( $type_label ); ?></span></td>
            <td><?php echo esc_html( $when['date'] ); ?></td>
            <td><?php echo esc_html( $when['time'] ); ?></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<p class="rep-footer">تم إنشاء هذا التقرير تلقائيًا من لوحة تتبع المكالمات — <?php echo esc_html( wp_date( 'Y-m-d' ) ); ?></p>
</body>
</html>
    <?php
    exit;
} );
