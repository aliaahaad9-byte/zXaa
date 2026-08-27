<?php
/**
 * لوحة تتبع المكالمات — YourColor Calls Dashboard
 * تتبع نقرات الاتصال المباشر والواتساب مع إحصائيات وفلاتر زمنية وتقارير.
 * توقيت العرض: Asia/Riyadh (GMT+3)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class YC_Calls_Dashboard {

	const PAGE_SLUG = 'add_callnumber_fields';
	const POST_TYPE = 'callwebsite';
	const PER_PAGE  = 50;
	const NONCE_KEY = 'yc_calls_nonce';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_ajax_yc_calls_data', array( __CLASS__, 'ajax_data' ) );
		add_action( 'wp_ajax_yc_calls_delete', array( __CLASS__, 'ajax_delete' ) );
		add_action( 'wp_ajax_yc_calls_delete_month', array( __CLASS__, 'ajax_delete_month' ) );
		add_action( 'wp_ajax_yc_calls_report', array( __CLASS__, 'ajax_report' ) );
	}

	/* ---------------------------------- Menu ---------------------------------- */

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

	/**
	 * تحميل ملفات CSS/JS داخل صفحة اللوحة فقط دون التأثير على باقي لوحة التحكم.
	 */
	public static function enqueue_assets( $hook ) {
		if ( $hook !== 'toplevel_page_' . self::PAGE_SLUG ) {
			return;
		}
		$base = get_template_directory_uri() . '/components/packs/callnumber/assets/';
		$ver  = '3.0.0';
		wp_enqueue_style( 'yc-calls-admin', $base . 'admin.css', array(), $ver );
		wp_enqueue_script( 'yc-calls-admin', $base . 'admin.js', array( 'jquery' ), $ver, true );
		wp_localize_script( 'yc-calls-admin', 'YCCalls', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( self::NONCE_KEY ),
		) );
	}

	/* ------------------------------ Time helpers ------------------------------ */

	private static function tz() {
		return new DateTimeZone( 'Asia/Riyadh' );
	}

	/**
	 * حدود الفترة الزمنية المختارة (بتوقيت الرياض) محولة إلى GMT للمقارنة مع post_date_gmt.
	 * @return array [start_gmt|null, end_gmt|null]
	 */
	private static function period_range( $period ) {
		$tz  = self::tz();
		$utc = new DateTimeZone( 'UTC' );
		$now = new DateTime( 'now', $tz );
		$start = null;
		$end   = null;

		switch ( $period ) {
			case 'today':
				$start = new DateTime( $now->format( 'Y-m-d' ) . ' 00:00:00', $tz );
				break;
			case 'week':
				// الأسبوع يبدأ من الأحد بتوقيت السعودية
				$dow   = (int) $now->format( 'w' ); // 0 = الأحد
				$start = new DateTime( $now->format( 'Y-m-d' ) . ' 00:00:00', $tz );
				$start->modify( '-' . $dow . ' days' );
				break;
			case 'month':
				$start = new DateTime( $now->format( 'Y-m' ) . '-01 00:00:00', $tz );
				break;
			case 'all':
			default:
				break;
		}

		$start_gmt = $start ? $start->setTimezone( $utc )->format( 'Y-m-d H:i:s' ) : null;
		return array( $start_gmt, $end );
	}

	/**
	 * حدود شهر معين YYYY-MM بتوقيت الرياض محولة إلى GMT.
	 */
	private static function month_range_gmt( $month ) {
		if ( ! preg_match( '/^\d{4}-\d{2}$/', $month ) ) {
			return false;
		}
		$tz  = self::tz();
		$utc = new DateTimeZone( 'UTC' );
		$start = DateTime::createFromFormat( 'Y-m-d H:i:s', $month . '-01 00:00:00', $tz );
		if ( ! $start ) return false;
		$end = clone $start;
		$end->modify( 'first day of next month' );
		return array(
			$start->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
			$end->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * تنسيق تاريخ السجل بتوقيت الرياض بنظام 12 ساعة (صباحًا / مساءً).
	 * @return array [date, time]
	 */
	private static function format_datetime( $gmt_datetime ) {
		try {
			$d = new DateTime( $gmt_datetime, new DateTimeZone( 'UTC' ) );
			$d->setTimezone( self::tz() );
		} catch ( Exception $e ) {
			return array( '—', '' );
		}
		$suffix = ( $d->format( 'A' ) === 'PM' ) ? 'مساءً' : 'صباحًا';
		return array( $d->format( 'Y-m-d' ), $d->format( 'g:i' ) . ' ' . $suffix );
	}

	/* ------------------------------ Data helpers ------------------------------ */

	private static function where_period( $period ) {
		global $wpdb;
		list( $start_gmt, ) = self::period_range( $period );
		$where = $wpdb->prepare( "p.post_type = %s AND p.post_status = 'publish'", self::POST_TYPE );
		if ( $start_gmt ) {
			$where .= $wpdb->prepare( " AND p.post_date_gmt >= %s", $start_gmt );
		}
		return $where;
	}

	/**
	 * أعداد النقرات حسب النوع خلال الفترة.
	 */
	private static function get_kpis( $period ) {
		global $wpdb;
		$where = self::where_period( $period );
		$rows  = $wpdb->get_results(
			"SELECT COALESCE(mt.meta_value, '') AS ctype, COUNT(*) AS total
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$wpdb->postmeta} mt ON mt.post_id = p.ID AND mt.meta_key = 'call_type'
			 WHERE {$where}
			 GROUP BY ctype"
		);
		$kpis = array( 'whatsapp' => 0, 'call' => 0, 'other' => 0, 'total' => 0 );
		foreach ( (array) $rows as $r ) {
			$n = (int) $r->total;
			if ( $r->ctype === 'whatsapp' )     $kpis['whatsapp'] += $n;
			elseif ( $r->ctype === 'call' )     $kpis['call']     += $n;
			else                                $kpis['other']    += $n;
			$kpis['total'] += $n;
		}
		return $kpis;
	}

	/**
	 * أفضل الصفحات تحويلًا خلال الفترة.
	 */
	private static function get_top_pages( $period, $limit = 5 ) {
		global $wpdb;
		$where = self::where_period( $period );
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT COALESCE(NULLIF(TRIM(mp.meta_value), ''), 'غير معروف') AS page_name,
			        MAX(mu.meta_value) AS page_url,
			        COUNT(*) AS clicks
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$wpdb->postmeta} mp ON mp.post_id = p.ID AND mp.meta_key = 'page'
			 LEFT JOIN {$wpdb->postmeta} mu ON mu.post_id = p.ID AND mu.meta_key = 'page_url'
			 WHERE {$where}
			 GROUP BY page_name
			 ORDER BY clicks DESC
			 LIMIT %d", $limit
		) );
	}

	/**
	 * قائمة الأشهر المتوفرة في السجلات (بتوقيت الرياض).
	 */
	private static function get_months() {
		global $wpdb;
		$months = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT DATE_FORMAT(DATE_ADD(p.post_date_gmt, INTERVAL 3 HOUR), '%%Y-%%m') AS m
			 FROM {$wpdb->posts} p
			 WHERE p.post_type = %s AND p.post_status = 'publish'
			 ORDER BY m DESC", self::POST_TYPE
		) );
		$current = ( new DateTime( 'now', self::tz() ) )->format( 'Y-m' );
		if ( ! in_array( $current, (array) $months, true ) ) {
			array_unshift( $months, $current );
		}
		return $months;
	}

	private static function query_logs( $period, $paged ) {
		global $wpdb;
		$where  = self::where_period( $period );
		$paged  = max( 1, (int) $paged );
		$offset = ( $paged - 1 ) * self::PER_PAGE;
		$total  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} p WHERE {$where}" );
		$posts  = $wpdb->get_results( $wpdb->prepare(
			"SELECT p.ID, p.post_date_gmt FROM {$wpdb->posts} p WHERE {$where}
			 ORDER BY p.post_date_gmt DESC LIMIT %d OFFSET %d",
			self::PER_PAGE, $offset
		) );
		return array( $posts, $total, $paged );
	}

	/* ------------------------------ HTML renderers ---------------------------- */

	private static function render_badge( $type ) {
		if ( $type === 'whatsapp' ) {
			return '<span class="yc-badge yc-badge--whatsapp"><span class="dashicons dashicons-whatsapp"></span> واتساب</span>';
		}
		if ( $type === 'call' ) {
			return '<span class="yc-badge yc-badge--call"><span class="dashicons dashicons-phone"></span> اتصال</span>';
		}
		return '<span class="yc-badge yc-badge--unknown">غير محدد</span>';
	}

	private static function render_rows( $posts ) {
		ob_start();
		if ( empty( $posts ) ) {
			echo '<tr class="yc-empty-row"><td colspan="5">لا توجد سجلات في هذه الفترة.</td></tr>';
			return ob_get_clean();
		}
		foreach ( $posts as $p ) {
			$id    = (int) $p->ID;
			$page  = get_post_meta( $id, 'page', true );
			$url   = get_post_meta( $id, 'page_url', true );
			$type  = get_post_meta( $id, 'call_type', true );
			list( $date, $time ) = self::format_datetime( $p->post_date_gmt );
			echo '<tr data-id="' . $id . '">';
			echo '<td class="yc-td-check"><input type="checkbox" class="yc-row-check" value="' . $id . '"></td>';
			echo '<td class="yc-td-page"><span class="yc-page-name">' . esc_html( $page !== '' ? $page : '—' ) . '</span>';
			if ( ! empty( $url ) ) {
				echo ' <a class="yc-page-link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" title="فتح الصفحة"><span class="dashicons dashicons-external"></span></a>';
			}
			echo '</td>';
			echo '<td class="yc-td-type">' . self::render_badge( $type ) . '</td>';
			echo '<td class="yc-td-time"><strong>' . esc_html( $date ) . '</strong><span class="yc-time">' . esc_html( $time ) . '</span></td>';
			echo '<td class="yc-td-del"><button type="button" class="yc-del-row" data-id="' . $id . '" title="حذف السجل"><span class="dashicons dashicons-trash"></span></button></td>';
			echo '</tr>';
		}
		return ob_get_clean();
	}

	private static function render_top_pages( $period ) {
		$rows  = self::get_top_pages( $period );
		$kpis  = self::get_kpis( $period );
		$total = max( 1, $kpis['total'] );
		ob_start();
		if ( empty( $rows ) ) {
			echo '<p class="yc-top-empty">لا توجد بيانات بعد.</p>';
			return ob_get_clean();
		}
		$i = 0;
		foreach ( $rows as $r ) {
			$i++;
			$clicks  = (int) $r->clicks;
			$percent = round( $clicks / $total * 100, 1 );
			echo '<div class="yc-top-item">';
			echo '<div class="yc-top-line">';
			echo '<span class="yc-top-rank yc-top-rank--' . $i . '">' . $i . '</span>';
			echo '<span class="yc-top-name" title="' . esc_attr( $r->page_name ) . '">' . esc_html( ( function_exists("mb_strimwidth") ? mb_strimwidth( $r->page_name, 0, 60, "..." ) : $r->page_name ) ) . '</span>';
			if ( ! empty( $r->page_url ) ) {
				echo '<a class="yc-page-link" href="' . esc_url( $r->page_url ) . '" target="_blank" rel="noopener noreferrer" title="فتح الصفحة"><span class="dashicons dashicons-external"></span></a>';
			}
			echo '</div>';
			echo '<div class="yc-top-meta"><span class="yc-top-count">' . $clicks . ' نقرة</span><span class="yc-top-percent">' . $percent . '%</span></div>';
			echo '<div class="yc-top-bar"><span style="width:' . min( 100, $percent ) . '%"></span></div>';
			echo '</div>';
		}
		return ob_get_clean();
	}

	private static function render_pagination( $total, $paged ) {
		$pages = max( 1, (int) ceil( $total / self::PER_PAGE ) );
		if ( $pages <= 1 ) return '';
		ob_start();
		echo '<div class="yc-pagination">';
		echo '<button type="button" class="yc-page-btn" data-paged="' . ( $paged - 1 ) . '" ' . ( $paged <= 1 ? 'disabled' : '' ) . '>السابق</button>';
		echo '<span class="yc-page-info">صفحة ' . $paged . ' من ' . $pages . '</span>';
		echo '<button type="button" class="yc-page-btn" data-paged="' . ( $paged + 1 ) . '" ' . ( $paged >= $pages ? 'disabled' : '' ) . '>التالي</button>';
		echo '</div>';
		return ob_get_clean();
	}

	/* ------------------------------ Page renderer ----------------------------- */

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'غير مصرح لك بالوصول لهذه الصفحة.' );
		}
		$period = 'today';
		$kpis   = self::get_kpis( $period );
		list( $posts, $total, $paged ) = self::query_logs( $period, 1 );
		$months  = self::get_months();
		$current = ( new DateTime( 'now', self::tz() ) )->format( 'Y-m' );
		?>
		<div class="yc-calls-wrap" dir="rtl">

			<div class="yc-header">
				<div class="yc-header-title">
					<span class="yc-header-icon"><span class="dashicons dashicons-phone"></span></span>
					<div>
						<h1>لوحة تتبع المكالمات</h1>
						<p>إحصائيات نقرات الاتصال المباشر والواتساب — توقيت الرياض (GMT+3)</p>
					</div>
				</div>
				<div class="yc-header-actions">
					<select id="yc-month" class="yc-month-select">
						<?php foreach ( $months as $m ) : ?>
							<option value="<?php echo esc_attr( $m ); ?>" <?php selected( $m, $current ); ?>><?php echo esc_html( $m ); ?></option>
						<?php endforeach; ?>
					</select>
					<button type="button" id="yc-export-pdf" class="yc-btn yc-btn--export"><span class="dashicons dashicons-download"></span> تصدير تقرير PDF</button>
					<button type="button" id="yc-delete-month" class="yc-btn yc-btn--danger"><span class="dashicons dashicons-trash"></span> حذف سجلات الشهر</button>
				</div>
			</div>

			<div class="yc-filters">
				<button type="button" class="yc-filter is-active" data-period="today">اليوم</button>
				<button type="button" class="yc-filter" data-period="week">هذا الأسبوع</button>
				<button type="button" class="yc-filter" data-period="month">هذا الشهر</button>
				<button type="button" class="yc-filter" data-period="all">كل الفترات</button>
			</div>

			<div class="yc-kpis">
				<div class="yc-kpi yc-kpi--whatsapp">
					<div class="yc-kpi-icon"><span class="dashicons dashicons-whatsapp"></span></div>
					<div class="yc-kpi-body">
						<span class="yc-kpi-label">إجمالي اتصالات الواتساب</span>
						<span class="yc-kpi-value" id="yc-kpi-whatsapp"><?php echo (int) $kpis['whatsapp']; ?></span>
					</div>
				</div>
				<div class="yc-kpi yc-kpi--call">
					<div class="yc-kpi-icon"><span class="dashicons dashicons-phone"></span></div>
					<div class="yc-kpi-body">
						<span class="yc-kpi-label">إجمالي الاتصالات الهاتفية</span>
						<span class="yc-kpi-value" id="yc-kpi-call"><?php echo (int) $kpis['call']; ?></span>
					</div>
				</div>
				<div class="yc-kpi yc-kpi--total">
					<div class="yc-kpi-icon"><span class="dashicons dashicons-id-alt"></span></div>
					<div class="yc-kpi-body">
						<span class="yc-kpi-label">الإجمالي الكلي للتحويلات</span>
						<span class="yc-kpi-value" id="yc-kpi-total"><?php echo (int) $kpis['total']; ?></span>
					</div>
				</div>
			</div>

			<div class="yc-main">
				<div class="yc-panel yc-panel--top5">
					<h3>أفضل 5 صفحات <mark>تحويلًا</mark></h3>
					<div id="yc-top-pages"><?php echo self::render_top_pages( $period ); ?></div>
				</div>

				<div class="yc-panel yc-panel--logs">
					<div class="yc-logs-head">
						<h3>سجل المكالمات</h3>
						<button type="button" id="yc-delete-selected" class="yc-btn yc-btn--danger-soft" disabled><span class="dashicons dashicons-trash"></span> حذف المحدد</button>
					</div>
					<div class="yc-table-scroll">
						<table class="yc-table">
							<thead>
								<tr>
									<th class="yc-td-check"><input type="checkbox" id="yc-check-all"></th>
									<th>اسم الصفحة</th>
									<th>نوع الاتصال</th>
									<th>التاريخ والوقت</th>
									<th>حذف</th>
								</tr>
							</thead>
							<tbody id="yc-logs-body"><?php echo self::render_rows( $posts ); ?></tbody>
						</table>
					</div>
					<div id="yc-pagination-holder"><?php echo self::render_pagination( $total, $paged ); ?></div>
				</div>
			</div>

			<!-- نافذة تأكيد الحذف -->
			<div class="yc-modal" id="yc-modal" aria-hidden="true">
				<div class="yc-modal-box">
					<span class="yc-modal-icon dashicons dashicons-warning"></span>
					<h3 id="yc-modal-title">تأكيد الحذف</h3>
					<p id="yc-modal-text"></p>
					<div class="yc-modal-actions">
						<button type="button" class="yc-btn yc-btn--danger" id="yc-modal-confirm">نعم، احذف</button>
						<button type="button" class="yc-btn yc-btn--ghost" id="yc-modal-cancel">إلغاء</button>
					</div>
				</div>
			</div>

			<div class="yc-loader" id="yc-loader" aria-hidden="true"><span></span></div>
		</div>
		<?php
	}

	/* -------------------------------- AJAX ------------------------------------ */

	private static function verify_ajax() {
		check_ajax_referer( self::NONCE_KEY, 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'غير مصرح.' ), 403 );
		}
	}

	public static function ajax_data() {
		self::verify_ajax();
		$period = isset( $_POST['period'] ) ? sanitize_key( $_POST['period'] ) : 'today';
		if ( ! in_array( $period, array( 'today', 'week', 'month', 'all' ), true ) ) {
			$period = 'today';
		}
		$paged = isset( $_POST['paged'] ) ? max( 1, (int) $_POST['paged'] ) : 1;
		$kpis  = self::get_kpis( $period );
		list( $posts, $total, $paged ) = self::query_logs( $period, $paged );
		wp_send_json_success( array(
			'kpis'       => $kpis,
			'top5'       => self::render_top_pages( $period ),
			'rows'       => self::render_rows( $posts ),
			'pagination' => self::render_pagination( $total, $paged ),
		) );
	}

	public static function ajax_delete() {
		self::verify_ajax();
		$ids = isset( $_POST['ids'] ) ? array_map( 'intval', (array) $_POST['ids'] ) : array();
		$deleted = 0;
		foreach ( $ids as $id ) {
			if ( $id > 0 && get_post_type( $id ) === self::POST_TYPE ) {
				if ( wp_delete_post( $id, true ) ) {
					$deleted++;
				}
			}
		}
		wp_send_json_success( array( 'deleted' => $deleted ) );
	}

	public static function ajax_delete_month() {
		self::verify_ajax();
		global $wpdb;
		$month = isset( $_POST['month'] ) ? sanitize_text_field( wp_unslash( $_POST['month'] ) ) : '';
		$range = self::month_range_gmt( $month );
		if ( ! $range ) {
			wp_send_json_error( array( 'message' => 'شهر غير صالح.' ) );
		}
		$ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_type = %s AND post_status = 'publish'
			 AND post_date_gmt >= %s AND post_date_gmt < %s",
			self::POST_TYPE, $range[0], $range[1]
		) );
		$deleted = 0;
		foreach ( (array) $ids as $id ) {
			if ( wp_delete_post( (int) $id, true ) ) {
				$deleted++;
			}
		}
		wp_send_json_success( array( 'deleted' => $deleted ) );
	}

	/**
	 * تقرير شهري قابل للطباعة / الحفظ كملف PDF (بدون روابط الصفحات).
	 */
	public static function ajax_report() {
		check_ajax_referer( self::NONCE_KEY, 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'غير مصرح.' );
		}
		global $wpdb;
		$month = isset( $_GET['month'] ) ? sanitize_text_field( wp_unslash( $_GET['month'] ) ) : '';
		$range = self::month_range_gmt( $month );
		if ( ! $range ) {
			wp_die( 'شهر غير صالح.' );
		}

		$where = $wpdb->prepare(
			"p.post_type = %s AND p.post_status = 'publish' AND p.post_date_gmt >= %s AND p.post_date_gmt < %s",
			self::POST_TYPE, $range[0], $range[1]
		);

		$counts = $wpdb->get_results(
			"SELECT COALESCE(mt.meta_value, '') AS ctype, COUNT(*) AS total
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$wpdb->postmeta} mt ON mt.post_id = p.ID AND mt.meta_key = 'call_type'
			 WHERE {$where} GROUP BY ctype"
		);
		$whatsapp = 0; $call = 0; $other = 0;
		foreach ( (array) $counts as $c ) {
			if ( $c->ctype === 'whatsapp' )  $whatsapp = (int) $c->total;
			elseif ( $c->ctype === 'call' )  $call     = (int) $c->total;
			else                             $other   += (int) $c->total;
		}
		$total = $whatsapp + $call + $other;

		$top = $wpdb->get_results(
			"SELECT COALESCE(NULLIF(TRIM(mp.meta_value), ''), 'غير معروف') AS page_name, COUNT(*) AS clicks
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$wpdb->postmeta} mp ON mp.post_id = p.ID AND mp.meta_key = 'page'
			 WHERE {$where} GROUP BY page_name ORDER BY clicks DESC LIMIT 5"
		);

		$logs = $wpdb->get_results(
			"SELECT p.ID, p.post_date_gmt FROM {$wpdb->posts} p
			 WHERE {$where} ORDER BY p.post_date_gmt DESC"
		);

		$site  = get_bloginfo( 'name' );
		$title = 'تقرير المكالمات — ' . $month;

		header( 'Content-Type: text/html; charset=utf-8' );
		?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<title><?php echo esc_html( $title ); ?></title>
<style>
	* { box-sizing: border-box; margin: 0; padding: 0; }
	body { font-family: Tahoma, "Segoe UI", Arial, sans-serif; color: #1e293b; background: #fff; padding: 32px; }
	.rp-head { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #0ea5a4; padding-bottom: 16px; margin-bottom: 24px; }
	.rp-head h1 { font-size: 22px; color: #0f766e; }
	.rp-head p { color: #64748b; font-size: 13px; margin-top: 4px; }
	.rp-meta { text-align: left; font-size: 13px; color: #64748b; }
	.rp-cards { display: flex; gap: 12px; margin-bottom: 24px; }
	.rp-card { flex: 1; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; text-align: center; }
	.rp-card strong { display: block; font-size: 28px; margin-top: 6px; }
	.rp-card.wa strong { color: #16a34a; } .rp-card.ph strong { color: #2563eb; } .rp-card.tt strong { color: #7c3aed; }
	h2 { font-size: 16px; margin: 20px 0 10px; color: #0f172a; }
	table { width: 100%; border-collapse: collapse; font-size: 13px; }
	th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: right; }
	th { background: #f1f5f9; }
	.badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; }
	.badge.wa { background: #dcfce7; color: #166534; } .badge.ph { background: #dbeafe; color: #1e40af; } .badge.un { background: #f1f5f9; color: #64748b; }
	.rp-footer { margin-top: 28px; font-size: 12px; color: #94a3b8; text-align: center; }
	@media print { body { padding: 0; } .no-print { display: none; } }
	.no-print { text-align: center; margin-bottom: 20px; }
	.no-print button { background: #0ea5a4; color: #fff; border: 0; border-radius: 8px; padding: 10px 24px; font-size: 14px; cursor: pointer; }
</style>
</head>
<body>
	<div class="no-print"><button onclick="window.print()">طباعة / حفظ PDF</button></div>
	<div class="rp-head">
		<div>
			<h1><?php echo esc_html( $title ); ?></h1>
			<p><?php echo esc_html( $site ); ?> — إحصائيات نقرات الاتصال والواتساب (توقيت الرياض GMT+3)</p>
		</div>
		<div class="rp-meta">تاريخ الإصدار: <?php echo esc_html( ( new DateTime( 'now', self::tz() ) )->format( 'Y-m-d' ) ); ?></div>
	</div>

	<div class="rp-cards">
		<div class="rp-card wa">اتصالات الواتساب<strong><?php echo $whatsapp; ?></strong></div>
		<div class="rp-card ph">الاتصالات الهاتفية<strong><?php echo $call; ?></strong></div>
		<div class="rp-card tt">الإجمالي الكلي<strong><?php echo $total; ?></strong></div>
	</div>

	<?php if ( ! empty( $top ) ) : ?>
	<h2>أفضل الصفحات تحويلًا</h2>
	<table>
		<thead><tr><th>#</th><th>اسم الصفحة</th><th>عدد النقرات</th><th>نسبة التحويل</th></tr></thead>
		<tbody>
		<?php $i = 0; foreach ( $top as $t ) : $i++; ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo esc_html( $t->page_name ); ?></td>
				<td><?php echo (int) $t->clicks; ?></td>
				<td><?php echo $total > 0 ? round( (int) $t->clicks / $total * 100, 1 ) : 0; ?>%</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>

	<h2>سجل العمليات (<?php echo count( (array) $logs ); ?> عملية)</h2>
	<table>
		<thead><tr><th>#</th><th>اسم الصفحة</th><th>نوع الاتصال</th><th>التاريخ</th><th>الوقت</th></tr></thead>
		<tbody>
		<?php
		$i = 0;
		foreach ( (array) $logs as $log ) :
			$i++;
			$page = get_post_meta( $log->ID, 'page', true );
			$type = get_post_meta( $log->ID, 'call_type', true );
			list( $date, $time ) = self::format_datetime( $log->post_date_gmt );
			if ( $type === 'whatsapp' )   { $badge = '<span class="badge wa">واتساب</span>'; }
			elseif ( $type === 'call' )   { $badge = '<span class="badge ph">اتصال</span>'; }
			else                          { $badge = '<span class="badge un">غير محدد</span>'; }
		?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo esc_html( $page !== '' ? $page : '—' ); ?></td>
				<td><?php echo $badge; ?></td>
				<td><?php echo esc_html( $date ); ?></td>
				<td><?php echo esc_html( $time ); ?></td>
			</tr>
		<?php endforeach; ?>
		<?php if ( $i === 0 ) : ?>
			<tr><td colspan="5">لا توجد سجلات لهذا الشهر.</td></tr>
		<?php endif; ?>
		</tbody>
	</table>

	<div class="rp-footer">تم إنشاء هذا التقرير تلقائيًا من لوحة تتبع المكالمات — <?php echo esc_html( $site ); ?></div>
</body>
</html>
		<?php
		exit;
	}
}

YC_Calls_Dashboard::init();

/* توافق خلفي مع الاستدعاء القديم لدالة صفحة القائمة */
if ( ! function_exists( 'add_callnumber_fields' ) ) {
	function add_callnumber_fields() {
		YC_Calls_Dashboard::render_page();
	}
}
