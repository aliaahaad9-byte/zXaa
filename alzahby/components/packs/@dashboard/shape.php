<?php
/**
 * /dashboard — لوحة تحكم العميل
 *
 * @var WP_User     $user
 * @var object|null $profile
 * @var array       $links
 * @var array       $stands
 * @var string      $stand   كود استاند قادم من صفحة المسح (اختياري)
 * @var string      $setup   نوع استاند يحتاج إضافة رابطه (instapay / vcash / greview)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$brand = Alz::opt( 'brand_name', 'QurTap' );
Alz::head(
	'لوحة التحكم — ' . $brand,
	'<meta name="robots" content="noindex"><link rel="stylesheet" href="' . esc_url( Alz::asset_url( 'css/app.css' ) ) . '"><link rel="stylesheet" href="' . esc_url( Alz::asset_url( 'css/profile.css' ) ) . '">'
);
echo '</head><body class="app app--dash">';

$logout = wp_nonce_url( home_url( '/dashboard/logout/' ), 'alz_logout' );
$tab    = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'links'; // phpcs:ignore
$tab    = in_array( $tab, array( 'links', 'identity', 'design', 'share' ), true ) ? $tab : 'links';
$i      = function ( $n ) {
	echo Alz::icon( $n ); // phpcs:ignore
};
?>
<header class="dh">
	<div class="container dh__in">
		<?php echo Alz::logo_html(); // phpcs:ignore ?>
		<div class="dh__user">
			<span class="muted hide-sm" dir="ltr"><?php echo esc_html( $user->user_email ); ?></span>
			<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( $logout ); ?>"><?php $i( 'logout' ); ?> خروج</a>
		</div>
	</div>
</header>

<?php if ( ! $profile ) : ?>
<main class="container center-screen center-screen--dash">
	<form class="card onboard" id="onboard">
		<span class="tile-ic"><?php $i( 'sparkles' ); ?></span>
		<h1>لنُنشئ صفحة نشاطك</h1>
		<p class="muted">دقيقة واحدة وتكون صفحتك جاهزة — تقدر تغيّر كل شيء لاحقاً.</p>
		<?php if ( $stand ) : ?>
			<p class="alert alert--ok"><?php $i( 'nfc' ); ?> سيتم ربط الاستاند <b dir="ltr"><?php echo esc_html( $stand ); ?></b> بصفحتك تلقائياً</p>
		<?php endif; ?>
		<?php if ( $stand ) : ?>
			<input type="hidden" name="stand" value="<?php echo esc_attr( $stand ); ?>">
		<?php endif; ?>
		<label class="field"><span>اسم النشاط</span><input name="business_name" required maxlength="80" placeholder="مثال: عيادة د. أحمد"></label>
		<label class="field">
			<span>رابط صفحتك</span>
			<span class="prefix-input" dir="ltr"><em><?php echo esc_html( preg_replace( '#^https?://#', '', home_url( '/' ) ) ); ?></em><input name="slug" required pattern="[a-z0-9][a-z0-9\-]{1,38}[a-z0-9]" placeholder="my-business"></span>
			<small class="muted">حروف إنجليزية صغيرة وأرقام و (-) فقط، من 3 إلى 40 حرف.</small>
		</label>
		<label class="field"><span>نوع النشاط</span>
			<select name="category">
				<?php foreach ( Alz::CATEGORIES as $k => $v ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>" <?php selected( 'other', $k ); ?>><?php echo esc_html( $v ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<?php if ( ! $stand ) : ?>
			<label class="field">
				<span>كود الاستاند (اختياري)</span>
				<input name="stand" dir="ltr" maxlength="16" pattern="[A-Za-z0-9]{6,16}" placeholder="الكود المكتوب تحت الـ QR">
				<small class="muted">لو معاك استاند اكتب الكود المطبوع تحت الـ QR ليتربط بصفحتك، أو امسح الـ QR بكاميرا الموبايل.</small>
			</label>
		<?php endif; ?>
		<p class="alert alert--error" hidden></p>
		<button class="btn btn--dark btn--lg btn--block" type="submit">إنشاء صفحتي</button>
	</form>
</main>
<?php else : ?>
<main class="container dash" id="app">
	<div class="dash__top">
		<div class="dash__id">
			<h1 data-bind="saved_name"><?php echo esc_html( $profile->business_name ); ?></h1>
			<div class="mylink">
				<span class="mylink__ic"><?php $i( 'link' ); ?></span>
				<div class="mylink__txt">
					<small>رابط صفحتك</small>
					<a target="_blank" rel="noopener" dir="ltr" data-bind="pretty_link" href="<?php echo esc_url( home_url( '/' . $profile->slug . '/' ) ); ?>"><?php echo esc_html( preg_replace( '#^https?://#', '', home_url( '/' . $profile->slug ) ) ); ?></a>
				</div>
				<button type="button" class="mylink__btn" data-copy-from="pretty" aria-label="نسخ الرابط" title="نسخ الرابط"><?php $i( 'copy' ); ?></button>
				<button type="button" class="mylink__btn" id="shareBtn" aria-label="مشاركة" title="مشاركة"><?php $i( 'share' ); ?></button>
				<a class="mylink__btn" target="_blank" rel="noopener" data-bind="pretty_open" href="<?php echo esc_url( home_url( '/' . $profile->slug . '/' ) ); ?>" aria-label="فتح الصفحة" title="فتح الصفحة"><?php $i( 'external' ); ?></a>
			</div>
		</div>
		<div class="stats">
			<div class="stat"><?php $i( 'eye' ); ?><div><b><?php echo esc_html( number_format_i18n( $profile->view_count ) ); ?></b><small>زيارة</small></div></div>
			<div class="stat"><?php $i( 'click' ); ?><div><b data-bind="clicks">0</b><small>ضغطة</small></div></div>
		</div>
	</div>

	<nav class="tabs">
		<button data-tab="links"><?php $i( 'link' ); ?> الروابط</button>
		<button data-tab="identity"><?php $i( 'store' ); ?> الهوية</button>
		<button data-tab="design"><?php $i( 'palette' ); ?> التصميم</button>
		<button data-tab="share"><?php $i( 'nfc' ); ?> QR والاستاندات</button>
	</nav>

	<div class="dash__grid">
		<div class="dash__main">

			<!-- الروابط -->
			<section data-panel="links">
				<div class="row-between">
					<div><h2>الروابط</h2><p class="muted">اسحب لإعادة الترتيب • يمكنك تكرار أي منصة أكثر من مرة</p></div>
					<button class="btn btn--dark" id="addLinkBtn"><?php $i( 'plus' ); ?> إضافة رابط</button>
				</div>
				<div class="card picker" id="picker" hidden></div>
				<ul class="links" id="links"></ul>
				<button class="empty-add" id="emptyAdd" hidden><?php $i( 'plus' ); ?> أضف أول رابط لصفحتك</button>
			</section>

			<!-- الهوية -->
			<section data-panel="identity">
				<div class="card">
					<h2>الصور</h2>
					<div class="images">
						<div class="img-field" data-image="logo_url" data-kind="logo">
							<span class="lbl">اللوجو</span>
							<button type="button" class="img-drop img-drop--logo"><?php $i( 'image_plus' ); ?></button>
							<button type="button" class="link danger img-remove">إزالة</button>
						</div>
						<div class="img-field img-field--wide" data-image="cover_url" data-kind="cover">
							<span class="lbl">صورة الغلاف (اختياري)</span>
							<button type="button" class="img-drop img-drop--cover"><?php $i( 'image_plus' ); ?></button>
							<button type="button" class="link danger img-remove">إزالة</button>
						</div>
					</div>
					<span class="lbl">شكل اللوجو</span>
					<div class="chips" data-choice="logo_shape">
						<button data-value="circle"><span class="shape shape--circle"></span> دائري</button>
						<button data-value="square"><span class="shape"></span> مربع</button>
					</div>
				</div>
				<div class="card">
					<h2>بيانات النشاط</h2>
					<label class="field"><span>اسم النشاط</span><input data-field="business_name" maxlength="80"></label>
					<label class="field"><span>نبذة قصيرة</span><textarea data-field="bio" maxlength="300" rows="3"></textarea><small class="muted" data-bind="bio_count">0/300</small></label>
					<div class="grid-2">
						<label class="field"><span>نوع النشاط</span>
							<select data-field="category">
								<?php foreach ( Alz::CATEGORIES as $k => $v ) : ?>
									<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<label class="field"><span>رابط الصفحة</span><input data-field="slug" dir="ltr" maxlength="40"></label>
					</div>
					<p class="alert alert--info">تغيير رابط الصفحة لا يؤثر على الاستاند — الاستاند والرابط الدائم يفتحان صفحتك دائماً.</p>
					<label class="toggle-row">
						<span><b>الصفحة منشورة</b><small class="muted">أوقفها مؤقتاً لإخفائها عن الزوار</small></span>
						<input type="checkbox" class="switch" data-field="is_published">
					</label>
				</div>
			</section>

			<!-- التصميم -->
			<section data-panel="design">
				<div class="card"><h2>ثيمات جاهزة</h2><div class="themes" id="themes"></div></div>
				<div class="card">
					<h2>ألوان مخصصة</h2>
					<div class="grid-3">
						<?php foreach ( array( 'theme_primary' => 'اللون الأساسي', 'theme_background' => 'الخلفية', 'theme_text' => 'النص' ) as $k => $v ) : ?>
							<label class="field"><span><?php echo esc_html( $v ); ?></span>
								<span class="color-input"><input type="color" data-color="<?php echo esc_attr( $k ); ?>"><input dir="ltr" maxlength="7" data-field="<?php echo esc_attr( $k ); ?>"></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="card">
					<h2>شكل الأزرار</h2>
					<div class="chips chips--grid" data-choice="button_style">
						<button data-value="brand">ألوان المنصات</button>
						<button data-value="filled">لون الهوية</button>
						<button data-value="soft">ناعم</button>
						<button data-value="outline">إطار</button>
					</div>
					<span class="lbl">طريقة العرض</span>
					<div class="chips" data-choice="layout">
						<button data-value="list"><?php $i( 'list' ); ?> قائمة</button>
						<button data-value="grid"><?php $i( 'grid' ); ?> شبكة</button>
					</div>
				</div>
			</section>

			<!-- QR والاستاند -->
			<section data-panel="share">
				<div class="card">
					<h2>روابط صفحتك</h2>
					<div class="copy-row">
						<span class="lbl">الرابط الدائم (للطباعة)</span>
						<div class="copy-row__in"><input readonly dir="ltr" data-bind="permanent"><button class="btn btn--outline btn--icon" data-copy-from="permanent" aria-label="نسخ"><?php $i( 'copy' ); ?></button></div>
						<small class="muted">لا يتغير أبداً حتى لو غيّرت اسم الرابط</small>
					</div>
					<div class="copy-row">
						<span class="lbl">الرابط المختصر (للمشاركة)</span>
						<div class="copy-row__in"><input readonly dir="ltr" data-bind="pretty"><button class="btn btn--outline btn--icon" data-copy-from="pretty" aria-label="نسخ"><?php $i( 'copy' ); ?></button></div>
					</div>
				</div>
				<div class="card">
					<h2><?php $i( 'qr' ); ?> رمز QR عالي الجودة</h2>
					<div class="qr">
						<div class="qr__box"><canvas id="qrCanvas" width="480" height="480"></canvas></div>
						<div class="qr__opts">
							<div class="seg" data-qr-target>
								<button data-value="permanent" class="on">الرابط الدائم</button>
								<button data-value="pretty">الرابط المختصر</button>
							</div>
							<label class="check"><input type="checkbox" id="qrColor"> بلون هوية النشاط</label>
							<div class="row-gap">
								<button class="btn btn--dark" data-qr-download="png"><?php $i( 'download' ); ?> PNG (2048px)</button>
								<button class="btn btn--outline" data-qr-download="svg"><?php $i( 'download' ); ?> SVG للطباعة</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card" id="targets">
					<h2><?php $i( 'qr' ); ?> روابط استاندات الدفع والتقييم</h2>
					<p class="muted" style="margin:-6px 0 14px;font-size:13px">لكل منصة استاند QR مستقل يفتح رابطك مباشرة. أضف رابطك مرة واحدة ويشتغل على كل استاندات المنصة — وتقدر تغيّره في أي وقت والكود المطبوع يفضل شغال.</p>
					<form id="targetsForm" novalidate>
						<?php
						foreach ( Alz::stand_kinds() as $k => $d ) :
							if ( 'profile' === $k ) {
								continue;
							}
							?>
							<div class="target<?php echo $setup === $k ? ' focus' : ''; ?>" data-target="<?php echo esc_attr( $k ); ?>" style="--kc:<?php echo esc_attr( $d['color'] ); ?>">
								<div class="target__head"><span class="chip-ic"><?php $i( $d['icon'] ); ?></span> <?php echo esc_html( $d['short'] ); ?> <small class="muted" data-count></small></div>
								<label class="field">
									<input name="<?php echo esc_attr( $k ); ?>" dir="ltr" maxlength="500" placeholder="<?php echo esc_attr( $d['placeholder'] ); ?>" autocomplete="off">
									<small class="muted"><?php echo esc_html( $d['hint'] ); ?></small>
								</label>
								<?php if ( 'greview' === $k ) : ?>
									<div class="target__ok" data-review hidden></div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
						<p style="margin:14px 0 0"><button class="btn btn--dark" type="submit"><?php $i( 'save' ); ?> حفظ الروابط</button></p>
					</form>
				</div>
				<div class="card">
					<h2><?php $i( 'nfc' ); ?> الاستاندات المربوطة</h2>
					<p class="alert alert--warn"><?php $i( 'scan' ); ?> أسهل طريقة: افتح كاميرا موبايلك وامسح الـ QR المطبوع على الاستاند وأنت مسجّل دخول — سيُربط بصفحتك تلقائياً. أو اكتب الكود المطبوع أسفل الـ QR هنا.</p>
					<form class="row-gap" id="claimForm">
						<input class="input" name="code" dir="ltr" maxlength="16" minlength="6" placeholder="كود الاستاند مثل: Ab3dEf9h" required>
						<button class="btn btn--dark" type="submit">ربط</button>
					</form>
					<ul class="stands" id="stands"></ul>
				</div>
			</section>
		</div>

		<aside class="dash__preview">
			<p class="muted center">معاينة حية</p>
			<div class="phone"><div class="phone__notch"></div><div class="phone__screen" id="preview"></div></div>
		</aside>
	</div>
</main>

<div class="preview-modal" id="previewModal" hidden><div class="phone"><div class="phone__notch"></div><div class="phone__screen" id="previewMobile"></div></div></div>

<div class="savebar">
	<div class="container savebar__in">
		<p class="muted" data-bind="status">كل التعديلات محفوظة</p>
		<div class="row-gap">
			<button class="btn btn--outline show-sm" id="previewBtn"><?php $i( 'eye' ); ?> معاينة</button>
			<button class="btn btn--ghost" id="undoBtn" hidden><?php $i( 'undo' ); ?> تراجع</button>
			<button class="btn btn--gold" id="saveBtn" disabled><?php $i( 'save' ); ?> حفظ</button>
		</div>
	</div>
</div>
<?php endif; ?>

<div class="toast" id="toast" role="status"></div>

<script>
window.ALZ = <?php
echo wp_json_encode(
	array_merge(
		Alz::js_config(),
		array(
			'api'     => rest_url( 'alzahby/v1/' ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'home'    => home_url( '/' ),
			'tab'     => $tab,
			'notice'  => isset( $_GET['claimed'] ) ? ( $setup ? 'تم ربط الاستاند 🎉 أضف رابطك الآن ليفتح مباشرة' : 'تم ربط الاستاند بصفحتك بنجاح 🎉' ) : '', // phpcs:ignore
			'profile' => $profile ? AlzApi::export_profile( $profile ) : null,
			'links'   => $profile ? AlzApi::export_links( $links ) : array(),
			'stands'  => $stands,
			'kinds'   => Alz::stand_kinds(),
			'targets' => $profile ? Alz::targets( $profile ) : null,
			'setup'   => $setup,
			'urls'    => $profile ? Alz::profile_urls( $profile ) : null,
		)
	)
);
?>;
</script>
<script src="<?php echo esc_url( Alz::asset_url( 'js/renderer.js' ) ); ?>"></script>
<script src="<?php echo esc_url( Alz::asset_url( 'js/qrcode.js' ) ); ?>"></script>
<script src="<?php echo esc_url( Alz::asset_url( 'js/dashboard.js' ) ); ?>"></script>
</body></html>
