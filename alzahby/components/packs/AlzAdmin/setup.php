<?php
/**
 * شاشات الإدارة: توليد أكواد الاستاندات للطباعة + متابعة الاستاندات والعملاء
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once dirname( __DIR__ ) . '/AlzCore/core.php';

final class AlzAdmin {

	public static function boot() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'admin_post_alz_export_stands', array( __CLASS__, 'export' ) );
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	/** نقاط REST لمولّد تصاميم الاستاندات — للأدمن فقط */
	public static function routes() {
		$admin = function () {
			return current_user_can( 'manage_options' );
		};
		register_rest_route( 'alzahby/v1', '/admin/stands/generate', array(
			'methods'             => 'POST',
			'permission_callback' => $admin,
			'callback'            => function ( WP_REST_Request $req ) {
				$count = max( 1, min( 500, (int) $req->get_param( 'count' ) ) );
				$batch = sanitize_text_field( (string) $req->get_param( 'batch' ) );
				$batch = '' !== $batch ? mb_substr( $batch, 0, 40 ) : 'batch-' . gmdate( 'Y-m-d-His' );
				return array( 'batch' => $batch, 'codes' => Alz::generate_stands( $count, $batch ) );
			},
		) );
		register_rest_route( 'alzahby/v1', '/admin/stands/batch', array(
			'methods'             => 'GET',
			'permission_callback' => $admin,
			'callback'            => function ( WP_REST_Request $req ) {
				global $wpdb;
				$t     = Alz::table( 'stands' );
				$batch = sanitize_text_field( (string) $req->get_param( 'batch' ) );
				$only  = 'free' === $req->get_param( 'only' ) ? ' AND profile_id IS NULL' : '';
				$codes = $wpdb->get_col( $wpdb->prepare( "SELECT code FROM $t WHERE batch = %s$only ORDER BY id", $batch ) ); // phpcs:ignore
				return array( 'batch' => $batch, 'codes' => $codes );
			},
		) );
	}

	public static function batches() {
		global $wpdb;
		$t = Alz::table( 'stands' );
		return $wpdb->get_results( "SELECT batch, COUNT(*) total, SUM(profile_id IS NOT NULL) linked, MAX(created_at) created FROM $t WHERE batch IS NOT NULL GROUP BY batch ORDER BY created DESC LIMIT 100" ); // phpcs:ignore
	}

	public static function menu() {
		add_submenu_page( 'alzahby', 'تصميم الاستاندات', 'تصميم الاستاندات (QR)', 'manage_options', 'alzahby-designer', array( __CLASS__, 'designer_page' ) );
		add_submenu_page( 'alzahby', 'الاستاندات', 'الاستاندات', 'manage_options', 'alzahby-stands', array( __CLASS__, 'stands_page' ) );
		add_submenu_page( 'alzahby', 'العملاء', 'العملاء', 'manage_options', 'alzahby-clients', array( __CLASS__, 'clients_page' ) );
	}

	/** تصدير CSV بأكواد الاستاندات وروابط الطباعة */
	public static function export() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'غير مسموح' );
		}
		check_admin_referer( 'alz_export_stands' );
		global $wpdb;
		$t     = Alz::table( 'stands' );
		$batch = isset( $_GET['batch'] ) ? sanitize_text_field( wp_unslash( $_GET['batch'] ) ) : '';
		$rows  = $batch
			? $wpdb->get_results( $wpdb->prepare( "SELECT code, batch, profile_id, scan_count FROM $t WHERE batch = %s ORDER BY id", $batch ) )
			: $wpdb->get_results( "SELECT code, batch, profile_id, scan_count FROM $t ORDER BY id" ); // phpcs:ignore

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=stands-' . sanitize_file_name( $batch ? $batch : 'all' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" );
		fputcsv( $out, array( 'code', 'print_url', 'batch', 'linked', 'scans' ) );
		foreach ( $rows as $r ) {
			fputcsv( $out, array( $r->code, home_url( '/s/' . $r->code ), $r->batch, $r->profile_id ? 'yes' : 'no', $r->scan_count ) );
		}
		fclose( $out ); // phpcs:ignore
		exit;
	}

	/** إعدادات التصميم الافتراضية: مكان مربع الـ QR كنسبة مئوية من صورة القالب */
	public static function design_defaults() {
		return array(
			'template' => get_template_directory_uri() . '/components/assets/img/stand-template.webp',
			'x'        => 22.0,
			'y'        => 22.9,
			'w'        => 56.1,
			'h'        => 38.7,
			'qr'       => 86,
			'code'     => 1,
			'color'    => '#0b1020',
		);
	}

	public static function design() {
		$saved = get_option( 'alz_stand_design', array() );
		return array_merge( self::design_defaults(), is_array( $saved ) ? $saved : array() );
	}

	/** صفحة مولّد تصاميم الاستاندات: يركّب QR لكل كود على صورة القالب ويحمّلها ZIP أو PDF */
	public static function designer_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$notice = '';
		if ( isset( $_POST['alz_design_save'] ) ) {
			check_admin_referer( 'alz_design' );
			$d = self::design_defaults();
			$n = array( 'template' => isset( $_POST['template'] ) ? esc_url_raw( wp_unslash( $_POST['template'] ) ) : $d['template'] );
			foreach ( array( 'x', 'y', 'w', 'h' ) as $k ) {
				$n[ $k ] = isset( $_POST[ $k ] ) ? max( 0, min( 100, round( (float) $_POST[ $k ], 2 ) ) ) : $d[ $k ];
			}
			$n['qr']    = isset( $_POST['qr'] ) ? max( 40, min( 100, (int) $_POST['qr'] ) ) : $d['qr'];
			$n['code']  = empty( $_POST['code'] ) ? 0 : 1;
			$n['color'] = isset( $_POST['color'] ) && preg_match( Alz::HEX_RE, wp_unslash( $_POST['color'] ) ) ? sanitize_text_field( wp_unslash( $_POST['color'] ) ) : $d['color'];
			if ( '' === $n['template'] ) {
				$n['template'] = $d['template'];
			}
			update_option( 'alz_stand_design', $n );
			$notice = 'تم حفظ إعدادات التصميم.';
		}
		if ( isset( $_POST['alz_design_reset'] ) ) {
			check_admin_referer( 'alz_design' );
			delete_option( 'alz_stand_design' );
			$notice = 'تمت استعادة التصميم الافتراضي.';
		}
		wp_enqueue_media();
		$design  = self::design();
		$batches = self::batches();
		?>
		<div class="wrap alzd" dir="rtl">
			<h1>تصميم الاستاندات وتوليد أكواد QR للطباعة</h1>
			<?php if ( $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
			<?php endif; ?>
			<p>كل تصميم يحمل QR فريد برابط ثابت <code dir="ltr"><?php echo esc_html( home_url( '/s/CODE' ) ); ?></code> وكود مكتوب تحته. العميل يمسح الـ QR أو يكتب الكود في لوحته ليربط الاستاند بصفحته.</p>

			<div class="alzd__grid">
				<div class="alzd__side">
					<div class="card alzd__card">
						<h2>1. توليد دفعة جديدة</h2>
						<p><label>عدد الاستاندات<br>
							<select id="alzCount">
								<option value="10">10</option><option value="25">25</option><option value="50" selected>50</option><option value="100">100</option><option value="200">200</option><option value="custom">عدد آخر…</option>
							</select>
							<input type="number" id="alzCountCustom" min="1" max="500" value="75" class="small-text" hidden>
						</label></p>
						<p><label>اسم الدفعة<br><input type="text" id="alzBatch" class="regular-text" value="<?php echo esc_attr( 'batch-' . gmdate( 'Y-m-d' ) ); ?>"></label></p>
						<p><label>صيغة التحميل<br>
							<select id="alzFormat">
								<option value="pdf">PDF للطباعة (صفحة لكل استاند)</option>
								<option value="zip">ZIP صور PNG</option>
							</select>
						</label></p>
						<p><label>دقة الإخراج<br>
							<select id="alzScale">
								<option value="1">عادية (حجم القالب)</option>
								<option value="2" selected>عالية ×2 (أفضل للطباعة)</option>
								<option value="3">عالية جداً ×3</option>
							</select>
						</label></p>
						<p><button type="button" class="button button-primary button-hero" id="alzGenerate">توليد وتحميل</button></p>
						<div class="alzd__progress" id="alzProgress" hidden><span></span><b></b></div>
					</div>

					<div class="card alzd__card">
						<h2>2. إعادة تحميل دفعة سابقة</h2>
						<?php if ( $batches ) : ?>
							<p><select id="alzOldBatch" class="regular-text">
								<?php foreach ( $batches as $b ) : ?>
									<option value="<?php echo esc_attr( $b->batch ); ?>"><?php echo esc_html( $b->batch . ' — ' . (int) $b->total . ' استاند (' . (int) $b->linked . ' مربوط)' ); ?></option>
								<?php endforeach; ?>
							</select></p>
							<p><label><input type="checkbox" id="alzOnlyFree"> غير المربوطة فقط</label></p>
							<p><button type="button" class="button" id="alzRedownload">تحميل الدفعة بنفس التصميم</button></p>
						<?php else : ?>
							<p class="description">لا توجد دفعات بعد.</p>
						<?php endif; ?>
					</div>

					<form method="post" class="card alzd__card" id="alzDesignForm">
						<?php wp_nonce_field( 'alz_design' ); ?>
						<h2>3. إعدادات التصميم</h2>
						<p><label>صورة القالب (التصميم الفاضي)<br>
							<input type="url" name="template" id="alzTemplate" class="regular-text" dir="ltr" value="<?php echo esc_attr( $design['template'] ); ?>">
						</label><br><button type="button" class="button" id="alzPickTemplate">اختر من مكتبة الوسائط</button></p>
						<p class="description">ارفع التصميم بأعلى دقة متاحة (مثلاً 2480×3508 لـ A4). المربع الأحمر في المعاينة هو مكان الـ QR — حرّكه بالسحب أو بالأرقام.</p>
						<table class="form-table alzd__nums" role="presentation"><tbody>
							<?php
							foreach ( array( 'x' => 'من اليسار %', 'y' => 'من الأعلى %', 'w' => 'العرض %', 'h' => 'الارتفاع %', 'qr' => 'حجم الـ QR داخل المربع %' ) as $k => $label ) :
								?>
								<tr><th><?php echo esc_html( $label ); ?></th><td><input type="number" step="0.1" name="<?php echo esc_attr( $k ); ?>" data-k="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $design[ $k ] ); ?>" class="small-text"></td></tr>
							<?php endforeach; ?>
							<tr><th>لون الـ QR</th><td><input type="color" name="color" data-k="color" value="<?php echo esc_attr( $design['color'] ); ?>"></td></tr>
							<tr><th>كتابة الكود تحت الـ QR</th><td><input type="checkbox" name="code" data-k="code" value="1" <?php checked( 1, (int) $design['code'] ); ?>></td></tr>
						</tbody></table>
						<p><button class="button button-primary" name="alz_design_save" value="1">حفظ التصميم</button> <button class="button" name="alz_design_reset" value="1" onclick="return confirm('استعادة التصميم الافتراضي؟')">استعادة الافتراضي</button></p>
					</form>
				</div>

				<div class="alzd__preview">
					<h2>معاينة</h2>
					<div class="alzd__stage" id="alzStage">
						<canvas id="alzCanvas"></canvas>
						<div class="alzd__box" id="alzBox"><i class="alzd__handle"></i></div>
					</div>
					<p class="description">المعاينة بكود تجريبي — الأكواد الحقيقية تُولَّد عند الضغط على «توليد وتحميل».</p>
				</div>
			</div>
		</div>
		<style>
			.alzd__grid{display:grid;gap:20px;grid-template-columns:minmax(300px,420px) 1fr;align-items:start;margin-top:16px}
			@media(max-width:960px){.alzd__grid{grid-template-columns:1fr}}
			.alzd__card{max-width:none;padding:8px 18px 14px;margin:0 0 16px}
			.alzd__nums th{padding:6px 0;width:55%}.alzd__nums td{padding:6px 0}
			.alzd__preview{position:sticky;top:46px}
			.alzd__stage{position:relative;max-width:520px;box-shadow:0 10px 30px rgba(0,0,0,.15);background:#fff;line-height:0;user-select:none}
			.alzd__stage canvas{width:100%;height:auto}
			.alzd__box{position:absolute;border:2px dashed #d63638;background:rgba(214,54,56,.06);cursor:move;box-sizing:border-box}
			.alzd__handle{position:absolute;right:-7px;bottom:-7px;width:14px;height:14px;background:#d63638;border-radius:3px;cursor:nwse-resize}
			.alzd__progress{height:26px;border-radius:6px;background:#f0f0f1;position:relative;overflow:hidden}
			.alzd__progress span{position:absolute;inset:0 auto 0 0;background:#2271b1;width:0;transition:width .2s}
			.alzd__progress b{position:relative;display:block;text-align:center;line-height:26px;font-size:12px}
		</style>
		<script>
		window.ALZD = <?php
		echo wp_json_encode(
			array(
				'design' => $design,
				'api'    => rest_url( 'alzahby/v1/admin/stands/' ),
				'nonce'  => wp_create_nonce( 'wp_rest' ),
				'base'   => home_url( '/s/' ),
			)
		);
		?>;
		</script>
		<script src="<?php echo esc_url( Alz::asset_url( 'js/qrcode.js' ) ); ?>"></script>
		<script src="<?php echo esc_url( Alz::asset_url( 'js/stand-designer.js' ) ); ?>"></script>
		<?php
	}

	public static function stands_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		global $wpdb;
		$t   = Alz::table( 'stands' );
		$pt  = Alz::table( 'profiles' );
		$new = array();

		if ( isset( $_POST['alz_generate'] ) ) {
			check_admin_referer( 'alz_generate' );
			$count = isset( $_POST['count'] ) ? absint( $_POST['count'] ) : 0;
			$batch = isset( $_POST['batch'] ) ? sanitize_text_field( wp_unslash( $_POST['batch'] ) ) : '';
			$new   = Alz::generate_stands( $count, $batch );
		}
		if ( isset( $_POST['alz_unlink'] ) ) {
			check_admin_referer( 'alz_unlink' );
			$code = sanitize_text_field( wp_unslash( $_POST['alz_unlink'] ) );
			$wpdb->query( $wpdb->prepare( "UPDATE $t SET profile_id = NULL, claimed_at = NULL WHERE code = %s", $code ) );
		}

		$filter = isset( $_GET['f'] ) ? sanitize_key( $_GET['f'] ) : 'all';
		$where  = 'linked' === $filter ? 'WHERE s.profile_id IS NOT NULL' : ( 'free' === $filter ? 'WHERE s.profile_id IS NULL' : '' );
		$paged  = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
		$per    = 50;
		$total  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $t s $where" ); // phpcs:ignore
		$rows   = $wpdb->get_results( $wpdb->prepare( "SELECT s.*, p.slug, p.business_name FROM $t s LEFT JOIN $pt p ON p.id = s.profile_id $where ORDER BY s.id DESC LIMIT %d OFFSET %d", $per, ( $paged - 1 ) * $per ) ); // phpcs:ignore
		$base   = admin_url( 'admin.php?page=alzahby-stands' );
		?>
		<div class="wrap" dir="rtl">
			<h1>الاستاندات</h1>
			<p>كل استاند له كود ثابت يُطبع كـ QR ويُكتب على شريحة NFC بالرابط: <code dir="ltr"><?php echo esc_html( home_url( '/s/CODE' ) ); ?></code></p>

			<div class="card" style="max-width:640px;padding:16px">
				<h2>توليد دفعة أكواد جديدة</h2>
				<form method="post">
					<?php wp_nonce_field( 'alz_generate' ); ?>
					<input type="hidden" name="alz_generate" value="1">
					<p>
						<label>العدد <input type="number" name="count" min="1" max="1000" value="50" class="small-text"></label>
						&nbsp; <label>اسم الدفعة <input type="text" name="batch" value="<?php echo esc_attr( 'batch-' . gmdate( 'Y-m-d' ) ); ?>"></label>
						&nbsp; <button class="button button-primary">توليد</button>
					</p>
				</form>
				<?php if ( $new ) : ?>
					<p><b>تم توليد <?php echo count( $new ); ?> كود.</b> انسخ الروابط للطباعة أو صدّرها CSV:</p>
					<textarea class="large-text code" dir="ltr" rows="8" readonly><?php echo esc_textarea( implode( "\n", array_map( function ( $c ) { return home_url( '/s/' . $c ); }, $new ) ) ); ?></textarea>
					<p><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alz_export_stands&batch=' . rawurlencode( sanitize_text_field( wp_unslash( $_POST['batch'] ) ) ) ), 'alz_export_stands' ) ); // phpcs:ignore ?>">تصدير الدفعة CSV</a></p>
				<?php endif; ?>
			</div>

			<ul class="subsubsub">
				<?php foreach ( array( 'all' => 'الكل', 'linked' => 'مربوطة', 'free' => 'غير مربوطة' ) as $k => $v ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( 'f', $k, $base ) ); ?>" class="<?php echo $filter === $k ? 'current' : ''; ?>"><?php echo esc_html( $v ); ?></a> |</li>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alz_export_stands' ), 'alz_export_stands' ) ); ?>">تصدير الكل CSV</a></li>
			</ul>

			<table class="widefat striped" style="margin-top:12px">
				<thead><tr><th>الكود</th><th>الدفعة</th><th>النشاط المربوط</th><th>عدد المسح</th><th>تاريخ الربط</th><th></th></tr></thead>
				<tbody>
				<?php foreach ( $rows as $r ) : ?>
					<tr>
						<td><code dir="ltr"><?php echo esc_html( $r->code ); ?></code></td>
						<td><?php echo esc_html( $r->batch ); ?></td>
						<td><?php echo $r->slug ? '<a target="_blank" href="' . esc_url( home_url( '/' . $r->slug . '/' ) ) . '">' . esc_html( $r->business_name ) . '</a>' : '—'; ?></td>
						<td><?php echo (int) $r->scan_count; ?></td>
						<td><?php echo esc_html( $r->claimed_at ? $r->claimed_at : '—' ); ?></td>
						<td>
							<?php if ( $r->profile_id ) : ?>
								<form method="post" onsubmit="return confirm('فك ربط هذا الاستاند؟')">
									<?php wp_nonce_field( 'alz_unlink' ); ?>
									<button class="button-link-delete" name="alz_unlink" value="<?php echo esc_attr( $r->code ); ?>">فك الربط</button>
								</form>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php if ( ! $rows ) : ?>
					<tr><td colspan="6">لا توجد استاندات.</td></tr>
				<?php endif; ?>
				</tbody>
			</table>
			<?php
			echo '<div class="tablenav"><div class="tablenav-pages">' . paginate_links( // phpcs:ignore
				array(
					'base'    => add_query_arg( 'paged', '%#%' ),
					'format'  => '',
					'current' => $paged,
					'total'   => max( 1, ceil( $total / $per ) ),
				)
			) . '</div></div>';
			?>
		</div>
		<?php
	}

	public static function clients_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		global $wpdb;
		$pt    = Alz::table( 'profiles' );
		$lt    = Alz::table( 'links' );
		$st    = Alz::table( 'stands' );
		$paged = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
		$per   = 50;
		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $pt" ); // phpcs:ignore
		$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT p.*, (SELECT COUNT(*) FROM $lt l WHERE l.profile_id = p.id) links_count, (SELECT COUNT(*) FROM $st s WHERE s.profile_id = p.id) stands_count FROM $pt p ORDER BY p.id DESC LIMIT %d OFFSET %d", $per, ( $paged - 1 ) * $per ) ); // phpcs:ignore
		?>
		<div class="wrap" dir="rtl">
			<h1>العملاء (<?php echo (int) $total; ?>)</h1>
			<table class="widefat striped" style="margin-top:12px">
				<thead><tr><th>النشاط</th><th>البريد</th><th>الرابط</th><th>الروابط</th><th>الاستاندات</th><th>الزيارات</th><th>الحالة</th></tr></thead>
				<tbody>
				<?php
				foreach ( $rows as $r ) :
					$u = get_userdata( $r->owner_id );
					?>
					<tr>
						<td><b><?php echo esc_html( $r->business_name ); ?></b><br><small><?php echo esc_html( isset( Alz::CATEGORIES[ $r->category ] ) ? Alz::CATEGORIES[ $r->category ] : '' ); ?></small></td>
						<td dir="ltr"><?php echo esc_html( $u ? $u->user_email : '—' ); ?></td>
						<td><a target="_blank" dir="ltr" href="<?php echo esc_url( home_url( '/' . $r->slug . '/' ) ); ?>">/<?php echo esc_html( $r->slug ); ?></a></td>
						<td><?php echo (int) $r->links_count; ?></td>
						<td><?php echo (int) $r->stands_count; ?></td>
						<td><?php echo (int) $r->view_count; ?></td>
						<td><?php echo $r->is_published ? 'منشورة' : 'موقوفة'; ?></td>
					</tr>
				<?php endforeach; ?>
				<?php if ( ! $rows ) : ?>
					<tr><td colspan="7">لا يوجد عملاء بعد.</td></tr>
				<?php endif; ?>
				</tbody>
			</table>
			<?php
			echo '<div class="tablenav"><div class="tablenav-pages">' . paginate_links( // phpcs:ignore
				array(
					'base'    => add_query_arg( 'paged', '%#%' ),
					'format'  => '',
					'current' => $paged,
					'total'   => max( 1, ceil( $total / $per ) ),
				)
			) . '</div></div>';
			?>
		</div>
		<?php
	}
}

AlzAdmin::boot();

/**
 * إزالة بقايا قسم الخدمات (Services) من القالب القديم:
 * أنواع المحتوى والقوائم الخاصة بالخدمات لم تعد مستخدمة في QurTap.
 * البيانات نفسها لا تُحذف من قاعدة البيانات — فقط تختفي من لوحة التحكم والموقع.
 */
add_action(
	'init',
	function () {
		foreach ( array( 'service', 'services', 'works', 'city-services' ) as $pt ) {
			if ( post_type_exists( $pt ) ) {
				unregister_post_type( $pt );
			}
		}
		foreach ( array( 'country', 'questions', 'service_category', 'services_category' ) as $tax ) {
			if ( taxonomy_exists( $tax ) ) {
				unregister_taxonomy( $tax );
			}
		}
		remove_rewrite_endpoint_services();
	},
	999
);

function remove_rewrite_endpoint_services() {
	global $wp_rewrite;
	if ( ! empty( $wp_rewrite->endpoints ) ) {
		$wp_rewrite->endpoints = array_values(
			array_filter(
				$wp_rewrite->endpoints,
				function ( $e ) {
					return 'services' !== $e[1];
				}
			)
		);
	}
}

add_action(
	'admin_menu',
	function () {
		global $menu;
		foreach ( (array) $menu as $item ) {
			$title = isset( $item[0] ) ? wp_strip_all_tags( $item[0] ) : '';
			$slug  = isset( $item[2] ) ? $item[2] : '';
			if ( preg_match( '/^(services?|الخدمات|خدمات)(\s|$)/iu', trim( $title ) ) || preg_match( '/post_type=(services?|works)(&|$)/', $slug ) ) {
				remove_menu_page( $slug );
			}
		}
	},
	9999
);

// حذف صفحة العميل وروابطه عند حذف حسابه
add_action(
	'delete_user',
	function ( $user_id ) {
		global $wpdb;
		$p = Alz::profile_by_owner( $user_id );
		if ( ! $p ) {
			return;
		}
		$wpdb->delete( Alz::table( 'links' ), array( 'profile_id' => $p->id ) );
		$wpdb->query( $wpdb->prepare( 'UPDATE ' . Alz::table( 'stands' ) . ' SET profile_id = NULL, claimed_at = NULL WHERE profile_id = %d', $p->id ) ); // phpcs:ignore
		$wpdb->delete( Alz::table( 'profiles' ), array( 'id' => $p->id ) );
	}
);
