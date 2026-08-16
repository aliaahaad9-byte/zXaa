<?php
/**
 * لوحة تتبع المكالمات — Calls Analytics Dashboard
 * يتتبع نقرات الاتصال المباشر والواتساب مع إحصائيات وفلاتر زمنية
 * (توقيت الرياض GMT+3) وتقارير PDF وحذف دوري للسجلات.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YC_Calls_Dashboard {

    const PAGE_SLUG = 'add_callnumber_fields';
    const NONCE_KEY = 'yc_calls_nonce';
    const PER_PAGE  = 20;

    public static function init() {
        add_action( 'init',                  array( __CLASS__, 'register_post_type' ) );
        add_action( 'admin_menu',            array( __CLASS__, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

        add_action( 'wp_ajax_yc_calls_data',         array( __CLASS__, 'ajax_data' ) );
        add_action( 'wp_ajax_yc_calls_delete',       array( __CLASS__, 'ajax_delete' ) );
        add_action( 'wp_ajax_yc_calls_delete_month', array( __CLASS__, 'ajax_delete_month' ) );
        add_action( 'wp_ajax_yc_calls_report',       array( __CLASS__, 'ajax_report' ) );
    }

    public static function register_post_type() {
        register_post_type( 'callwebsite', array(
            'labels'   => array( 'name' => 'سجل المكالمات' ),
            'public'   => false,
            'show_ui'  => false,
            'supports' => array( 'title' ),
        ) );
    }

    public static function register_menu() {
        add_menu_page(
            'صفحة المكالمات',
            'صفحة المكالمات',
            'manage_options',
            self::PAGE_SLUG,
            array( __CLASS__, 'render_page' ),
            'dashicons-phone',
            6
        );
    }

    public static function enqueue_assets( $hook ) {
        if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook ) {
            return;
        }
        $base = get_template_directory_uri() . '/components/packs/callnumber/';
        $ver  = '2.0.0';

        wp_enqueue_style( 'yc-calls-admin', $base . 'admin.css', array(), $ver );
        wp_enqueue_script( 'yc-calls-admin', $base . 'admin.js', array( 'jquery' ), $ver, true );
        wp_localize_script( 'yc-calls-admin', 'YCCalls', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( self::NONCE_KEY ),
            'perPage' => self::PER_PAGE,
        ) );
    }

    /* ---------------------------------------------------------------- */
    /* Helpers                                                          */
    /* ---------------------------------------------------------------- */

    private static function tz() {
        return new DateTimeZone( 'Asia/Riyadh' );
    }

    /**
     * حدود الفترة الزمنية بتوقيت الرياض محولة إلى GMT (نطاق لعمود post_date_gmt).
     *
     * @return array|null [start_gmt, end_gmt] أو null لكل الفترات.
     */
    private static function range_for_filter( $filter ) {
        $now = new DateTimeImmutable( 'now', self::tz() );

        switch ( $filter ) {
            case 'today':
                $start = $now->setTime( 0, 0, 0 );
                $end   = $start->modify( '+1 day' );
                break;
            case 'week':
                // الأسبوع في السعودية يبدأ يوم الأحد
                $start = $now->setTime( 0, 0, 0 );
                $dow   = (int) $start->format( 'w' ); // 0 = الأحد
                $start = $start->modify( '-' . $dow . ' days' );
                $end   = $start->modify( '+7 days' );
                break;
            case 'month':
                $start = $now->modify( 'first day of this month' )->setTime( 0, 0, 0 );
                $end   = $start->modify( '+1 month' );
                break;
            default:
                return null;
        }

        $utc = new DateTimeZone( 'UTC' );
        return array(
            $start->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
            $end->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
        );
    }

    /** نطاق شهر محدد YYYY-MM بتوقيت الرياض محولاً إلى GMT. */
    private static function range_for_month( $month ) {
        if ( ! preg_match( '/^\d{4}-(0[1-9]|1[0-2])$/', $month ) ) {
            return null;
        }
        $start = new DateTimeImmutable( $month . '-01 00:00:00', self::tz() );
        $end   = $start->modify( '+1 month' );
        $utc   = new DateTimeZone( 'UTC' );
        return array(
            $start->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
            $end->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
        );
    }

    /** جلب كل السجلات داخل نطاق زمني (أو الكل) مع بيانات الميتا دفعة واحدة. */
    private static function fetch_rows( $range ) {
        global $wpdb;

        $sql = "SELECT p.ID, p.post_date_gmt,
                    MAX(CASE WHEN m.meta_key = 'calltype' THEN m.meta_value END) AS calltype,
                    MAX(CASE WHEN m.meta_key = 'page'     THEN m.meta_value END) AS page,
                    MAX(CASE WHEN m.meta_key = 'pageurl'  THEN m.meta_value END) AS pageurl
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
                WHERE p.post_type = 'callwebsite' AND p.post_status = 'publish'";
        $params = array();

        if ( $range ) {
            $sql     .= ' AND p.post_date_gmt >= %s AND p.post_date_gmt < %s';
            $params[] = $range[0];
            $params[] = $range[1];
        }
        $sql .= ' GROUP BY p.ID ORDER BY p.post_date_gmt DESC';

        if ( $params ) {
            $sql = $wpdb->prepare( $sql, $params );
        }
        return $wpdb->get_results( $sql );
    }

    /** تنسيق التاريخ والوقت بتوقيت الرياض بنظام 12 ساعة (صباحًا/مساءً). */
    private static function format_datetime( $gmt ) {
        try {
            $dt = new DateTimeImmutable( $gmt, new DateTimeZone( 'UTC' ) );
        } catch ( Exception $e ) {
            return array( 'date' => '', 'time' => '' );
        }
        $dt = $dt->setTimezone( self::tz() );
        return array(
            'date' => $dt->format( 'Y-m-d' ),
            'time' => $dt->format( 'h:i' ) . ' ' . ( 'am' === $dt->format( 'a' ) ? 'صباحًا' : 'مساءً' ),
        );
    }

    /** تجميع الإحصائيات وأفضل الصفحات من مجموعة سجلات. */
    private static function build_stats( $rows ) {
        $whatsapp = 0;
        $phone    = 0;
        $pages    = array();

        foreach ( $rows as $r ) {
            if ( 'whatsapp' === $r->calltype ) {
                $whatsapp++;
            } elseif ( 'phone' === $r->calltype ) {
                $phone++;
            }
            $title = trim( (string) $r->page );
            if ( '' === $title || '--' === $title ) {
                $title = 'غير معروف';
            }
            if ( ! isset( $pages[ $title ] ) ) {
                $pages[ $title ] = array( 'title' => $title, 'count' => 0, 'url' => '' );
            }
            $pages[ $title ]['count']++;
            if ( '' === $pages[ $title ]['url'] && ! empty( $r->pageurl ) ) {
                $pages[ $title ]['url'] = $r->pageurl;
            }
        }

        $total = count( $rows );
        usort( $pages, function ( $a, $b ) {
            return $b['count'] - $a['count'];
        } );
        $top = array_slice( array_values( $pages ), 0, 5 );
        foreach ( $top as &$p ) {
            $p['percent'] = $total ? round( $p['count'] / $total * 100, 1 ) : 0;
        }
        unset( $p );

        return array(
            'whatsapp'  => $whatsapp,
            'phone'     => $phone,
            'total'     => $total,
            'top_pages' => $top,
        );
    }

    /** الأشهر المتوفرة في السجلات لقائمة اختيار الشهر. */
    private static function available_months() {
        global $wpdb;
        $months = $wpdb->get_col(
            "SELECT DISTINCT DATE_FORMAT(DATE_ADD(post_date_gmt, INTERVAL 3 HOUR), '%Y-%m')
             FROM {$wpdb->posts}
             WHERE post_type = 'callwebsite' AND post_status = 'publish'
             ORDER BY 1 DESC"
        );
        $current = ( new DateTimeImmutable( 'now', self::tz() ) )->format( 'Y-m' );
        if ( ! in_array( $current, $months, true ) ) {
            array_unshift( $months, $current );
        }
        return $months;
    }

    private static function verify_request() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'غير مصرح لك بهذا الإجراء' ), 403 );
        }
        check_ajax_referer( self::NONCE_KEY, 'nonce' );
    }

    /* ---------------------------------------------------------------- */
    /* AJAX                                                             */
    /* ---------------------------------------------------------------- */

    public static function ajax_data() {
        self::verify_request();

        $filter = isset( $_POST['filter'] ) ? sanitize_key( $_POST['filter'] ) : 'today';
        if ( ! in_array( $filter, array( 'today', 'week', 'month', 'all' ), true ) ) {
            $filter = 'today';
        }
        $paged = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;

        $rows  = self::fetch_rows( self::range_for_filter( $filter ) );
        $stats = self::build_stats( $rows );

        $total_pages = max( 1, (int) ceil( count( $rows ) / self::PER_PAGE ) );
        $paged       = min( $paged, $total_pages );
        $slice       = array_slice( $rows, ( $paged - 1 ) * self::PER_PAGE, self::PER_PAGE );

        $items = array();
        foreach ( $slice as $r ) {
            $dt      = self::format_datetime( $r->post_date_gmt );
            $items[] = array(
                'id'   => (int) $r->ID,
                'page' => ( '' !== trim( (string) $r->page ) && '--' !== trim( (string) $r->page ) ) ? $r->page : 'غير معروف',
                'url'  => $r->pageurl ? esc_url( $r->pageurl ) : '',
                'type' => in_array( $r->calltype, array( 'phone', 'whatsapp' ), true ) ? $r->calltype : '',
                'date' => $dt['date'],
                'time' => $dt['time'],
            );
        }

        wp_send_json_success( array(
            'stats'       => $stats,
            'rows'        => $items,
            'paged'       => $paged,
            'total_pages' => $total_pages,
            'total_rows'  => count( $rows ),
        ) );
    }

    public static function ajax_delete() {
        self::verify_request();

        $ids     = isset( $_POST['ids'] ) ? array_map( 'absint', (array) $_POST['ids'] ) : array();
        $deleted = 0;
        foreach ( $ids as $id ) {
            $post = get_post( $id );
            if ( $post && 'callwebsite' === $post->post_type ) {
                wp_delete_post( $id, true );
                $deleted++;
            }
        }
        wp_send_json_success( array( 'deleted' => $deleted ) );
    }

    public static function ajax_delete_month() {
        self::verify_request();

        $month = isset( $_POST['month'] ) ? sanitize_text_field( wp_unslash( $_POST['month'] ) ) : '';
        $range = self::range_for_month( $month );
        if ( ! $range ) {
            wp_send_json_error( array( 'message' => 'صيغة الشهر غير صحيحة' ), 400 );
        }

        $rows    = self::fetch_rows( $range );
        $deleted = 0;
        foreach ( $rows as $r ) {
            wp_delete_post( (int) $r->ID, true );
            $deleted++;
        }
        wp_send_json_success( array( 'deleted' => $deleted ) );
    }

    public static function ajax_report() {
        self::verify_request();

        $month = isset( $_POST['month'] ) ? sanitize_text_field( wp_unslash( $_POST['month'] ) ) : '';
        $range = self::range_for_month( $month );
        if ( ! $range ) {
            wp_send_json_error( array( 'message' => 'صيغة الشهر غير صحيحة' ), 400 );
        }

        $rows  = self::fetch_rows( $range );
        $stats = self::build_stats( $rows );

        // التقرير لا يتضمن روابط الصفحات — الاسم والنوع والتوقيت فقط
        $items = array();
        foreach ( $rows as $r ) {
            $dt      = self::format_datetime( $r->post_date_gmt );
            $items[] = array(
                'page' => ( '' !== trim( (string) $r->page ) && '--' !== trim( (string) $r->page ) ) ? $r->page : 'غير معروف',
                'type' => in_array( $r->calltype, array( 'phone', 'whatsapp' ), true ) ? $r->calltype : '',
                'date' => $dt['date'],
                'time' => $dt['time'],
            );
        }

        wp_send_json_success( array(
            'month' => $month,
            'site'  => get_bloginfo( 'name' ),
            'stats' => $stats,
            'rows'  => $items,
        ) );
    }

    /* ---------------------------------------------------------------- */
    /* Page                                                             */
    /* ---------------------------------------------------------------- */

    public static function render_page() {
        $months = self::available_months();
        ?>
        <div class="yc-calls-wrap" dir="rtl">

            <div class="yc-head">
                <div class="yc-head-title">
                    <span class="yc-head-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="22" height="22" fill="currentColor"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>
                    </span>
                    <div>
                        <h1>لوحة تتبع المكالمات</h1>
                        <p>إحصائيات نقرات الاتصال المباشر والواتساب &mdash; توقيت الرياض (GMT+3)</p>
                    </div>
                </div>
                <div class="yc-head-actions">
                    <select id="yc-month" class="yc-month-select">
                        <?php foreach ( $months as $m ) : ?>
                            <option value="<?php echo esc_attr( $m ); ?>"><?php echo esc_html( $m ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" id="yc-export" class="yc-btn yc-btn-green">
                        <span class="dashicons dashicons-download"></span> تصدير تقرير PDF
                    </button>
                    <button type="button" id="yc-delete-month" class="yc-btn yc-btn-red">
                        <span class="dashicons dashicons-trash"></span> حذف سجلات الشهر
                    </button>
                </div>
            </div>

            <div class="yc-filters" role="tablist">
                <button type="button" class="yc-filter is-active" data-filter="today">اليوم</button>
                <button type="button" class="yc-filter" data-filter="week">هذا الأسبوع</button>
                <button type="button" class="yc-filter" data-filter="month">هذا الشهر</button>
                <button type="button" class="yc-filter" data-filter="all">كل الفترات</button>
            </div>

            <div class="yc-cards">
                <div class="yc-card yc-card-whatsapp">
                    <div class="yc-card-info">
                        <span class="yc-card-label">إجمالي اتصالات الواتساب</span>
                        <span class="yc-card-value" id="yc-stat-whatsapp">0</span>
                    </div>
                    <span class="yc-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="26" height="26" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                    </span>
                </div>
                <div class="yc-card yc-card-phone">
                    <div class="yc-card-info">
                        <span class="yc-card-label">إجمالي الاتصالات الهاتفية</span>
                        <span class="yc-card-value" id="yc-stat-phone">0</span>
                    </div>
                    <span class="yc-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="24" height="24" fill="currentColor"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>
                    </span>
                </div>
                <div class="yc-card yc-card-total">
                    <div class="yc-card-info">
                        <span class="yc-card-label">الإجمالي الكلي للتحويلات</span>
                        <span class="yc-card-value" id="yc-stat-total">0</span>
                    </div>
                    <span class="yc-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="26" height="26" fill="currentColor"><path d="M64 64C28.7 64 0 92.7 0 128L0 384c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-256c0-35.3-28.7-64-64-64L64 64zm64 320l-64 0 0-64c35.3 0 64 28.7 64 64zM64 192l0-64 64 0c0 35.3-28.7 64-64 64zM448 384c0-35.3 28.7-64 64-64l0 64-64 0zm64-192c-35.3 0-64-28.7-64-64l64 0 0 64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg>
                    </span>
                </div>
            </div>

            <div class="yc-body">
                <aside class="yc-top-pages">
                    <h2>أفضل 5 صفحات <mark>تحويلًا</mark></h2>
                    <ol id="yc-top-list"><li class="yc-empty">لا توجد بيانات بعد</li></ol>
                </aside>

                <div class="yc-table-card">
                    <div class="yc-table-head">
                        <h2>سجل المكالمات</h2>
                        <button type="button" id="yc-delete-selected" class="yc-btn yc-btn-red-soft" disabled>
                            <span class="dashicons dashicons-trash"></span> حذف المحدد
                        </button>
                    </div>
                    <div class="yc-table-scroll">
                        <table class="yc-table">
                            <thead>
                                <tr>
                                    <th class="yc-col-check"><input type="checkbox" id="yc-check-all" aria-label="تحديد الكل"></th>
                                    <th>اسم الصفحة</th>
                                    <th>نوع الاتصال</th>
                                    <th>التاريخ والوقت</th>
                                    <th class="yc-col-del">حذف</th>
                                </tr>
                            </thead>
                            <tbody id="yc-rows">
                                <tr><td colspan="5" class="yc-empty">جارٍ التحميل&hellip;</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="yc-pagination" id="yc-pagination"></div>
                </div>
            </div>

            <!-- نافذة تأكيد الحذف -->
            <div class="yc-modal-overlay" id="yc-modal" hidden>
                <div class="yc-modal" role="dialog" aria-modal="true" aria-labelledby="yc-modal-title">
                    <h3 id="yc-modal-title">تأكيد الحذف</h3>
                    <p id="yc-modal-text"></p>
                    <div class="yc-modal-actions">
                        <button type="button" class="yc-btn yc-btn-red" id="yc-modal-confirm">نعم، احذف</button>
                        <button type="button" class="yc-btn yc-btn-ghost" id="yc-modal-cancel">إلغاء</button>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }
}

YC_Calls_Dashboard::init();

// توافق خلفي: بعض الإصدارات كانت تستدعي هذه الدالة مباشرة كصفحة القائمة
if ( ! function_exists( 'add_callnumber_fields' ) ) {
    function add_callnumber_fields() {
        YC_Calls_Dashboard::render_page();
    }
}
