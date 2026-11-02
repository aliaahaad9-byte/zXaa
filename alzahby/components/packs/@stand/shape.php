<?php
/**
 * /s/{code}
 *  - claim   : استاند غير مربوط ⟵ تفعيل (المربوط يتحول تلقائياً قبل الوصول هنا)
 *  - pay     : استاند إنستاباي / فودافون كاش برقم أو عنوان (ليس رابطاً) ⟵ عرض القيمة مع زر نسخ
 *  - pending : استاند مربوط لكن صاحبه لم يضف رابط المنصة بعد
 *
 * @var string      $mode
 * @var string      $kind
 * @var array       $def
 * @var string      $code
 * @var int         $user_id
 * @var bool        $has_profile
 * @var string      $error
 * @var string      $value
 * @var object|null $profile
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$mode = isset( $mode ) ? $mode : 'claim';
$kind = isset( $kind ) ? $kind : 'profile';
$def  = isset( $def ) ? $def : array( 'label' => 'استاند', 'short' => '', 'icon' => 'nfc', 'color' => '#0b1020' );

/* ---------------- صفحة الدفع: رقم المحفظة / عنوان إنستاباي ---------------- */
if ( 'pay' === $mode ) :
	$color = $def['color'];
	Alz::head(
		$profile->business_name . ' — ' . $def['short'],
		'<meta name="robots" content="noindex"><meta name="theme-color" content="' . esc_attr( $color ) . '"><link rel="stylesheet" href="' . esc_url( Alz::asset_url( 'css/app.css' ) ) . '">'
	);
	echo '</head><body class="app">';
	?>
	<main class="center-screen pay" style="--pc:<?php echo esc_attr( $color ); ?>">
		<div class="pay__card">
			<div class="pay__head">
				<?php if ( $profile->logo_url ) : ?>
					<img class="pay__logo" src="<?php echo esc_url( $profile->logo_url ); ?>" alt="">
				<?php else : ?>
					<span class="pay__logo pay__logo--ic"><?php echo Alz::icon( $def['icon'] ); // phpcs:ignore ?></span>
				<?php endif; ?>
				<h1><?php echo esc_html( $profile->business_name ); ?></h1>
				<p class="pay__badge"><?php echo Alz::icon( $def['icon'] ); // phpcs:ignore ?> <?php echo esc_html( $def['short'] ); ?></p>
			</div>
			<p class="muted"><?php echo 'vcash' === $kind ? 'حوّل على رقم المحفظة:' : 'حوّل عبر إنستاباي على:'; ?></p>
			<p class="pay__value" dir="ltr" id="payValue"><?php echo esc_html( $value ); ?></p>
			<button type="button" class="btn btn--lg btn--block pay__btn" id="payCopy"><?php echo Alz::icon( 'copy' ); // phpcs:ignore ?> <span>نسخ</span></button>
			<p class="muted pay__note">انسخ <?php echo 'vcash' === $kind ? 'الرقم' : 'العنوان'; ?> ثم افتح تطبيق <?php echo esc_html( $def['short'] ); ?> وأكمل التحويل.</p>
			<?php if ( $profile->is_published ) : ?>
				<a class="link pay__more" href="<?php echo esc_url( home_url( '/' . $profile->slug . '/' ) ); ?>">صفحة <?php echo esc_html( $profile->business_name ); ?></a>
			<?php endif; ?>
		</div>
	</main>
	<script>
	(function () {
		var b = document.getElementById("payCopy"), v = document.getElementById("payValue").textContent.trim();
		b.addEventListener("click", function () {
			var done = function () { b.querySelector("span").textContent = "تم النسخ ✓"; setTimeout(function () { b.querySelector("span").textContent = "نسخ"; }, 1800); };
			if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(v).then(done, fallback); } else { fallback(); }
			function fallback() { var t = document.createElement("textarea"); t.value = v; document.body.appendChild(t); t.select(); try { document.execCommand("copy"); done(); } catch (e) {} t.remove(); }
		});
	})();
	</script>
	</body></html>
	<?php
	return;
endif;

Alz::head( 'pending' === $mode ? $def['label'] : 'تفعيل الاستاند', '<meta name="robots" content="noindex"><link rel="stylesheet" href="' . esc_url( Alz::asset_url( 'css/app.css' ) ) . '">' );
echo '</head><body class="app app--dark">';

/* ---------------- مربوط لكن بدون رابط ---------------- */
if ( 'pending' === $mode ) :
	?>
	<main class="center-screen">
		<div class="stand-card">
			<?php echo Alz::logo_html( true ); // phpcs:ignore ?>
			<div class="stand-card__icon"><?php echo Alz::icon( $def['icon'] ); // phpcs:ignore ?></div>
			<h1><?php echo esc_html( $def['label'] ); ?></h1>
			<p class="muted">هذا الاستاند مُفعّل، لكن صاحب النشاط لم يضف رابط <?php echo esc_html( $def['short'] ); ?> بعد. حاول مرة أخرى لاحقاً.</p>
			<p class="code-chip" dir="ltr"><?php echo esc_html( $code ); ?></p>
		</div>
	</main>
	</body></html>
	<?php
	return;
endif;

/* ---------------- تفعيل استاند جديد ---------------- */
$is_profile = 'profile' === $kind;
?>
<main class="center-screen">
	<div class="stand-card">
		<?php echo Alz::logo_html( true ); // phpcs:ignore ?>
		<div class="stand-card__icon"><?php echo Alz::icon( $def['icon'] ); // phpcs:ignore ?></div>
		<h1><?php echo $is_profile ? 'استاند جديد جاهز للتفعيل' : esc_html( $def['label'] ) . ' جاهز للتفعيل'; ?></h1>
		<?php if ( $is_profile ) : ?>
			<p class="muted">هذا الاستاند لم يُربط بأي نشاط بعد. إذا كنت صاحب الاستاند، فعّله الآن ليفتح صفحتك تلقائياً عند كل لمسة أو مسح.</p>
		<?php else : ?>
			<p class="muted">هذا الاستاند لم يُربط بأي نشاط بعد. فعّله الآن ثم أضف رابط <?php echo esc_html( $def['short'] ); ?> الخاص بك — وسيفتح مباشرة لعملائك عند كل مسح.</p>
		<?php endif; ?>
		<p class="code-chip" dir="ltr"><?php echo esc_html( $code ); ?></p>

		<?php if ( $error ) : ?>
			<p class="alert alert--error"><?php echo esc_html( $error ); ?></p>
		<?php endif; ?>

		<?php if ( ! $user_id ) : ?>
			<a class="btn btn--gold btn--lg btn--block" href="<?php echo esc_url( Alz::login_url( '/s/' . $code . '/' ) ); ?>">سجّل الدخول لتفعيل الاستاند</a>
		<?php elseif ( ! $has_profile ) : ?>
			<a class="btn btn--gold btn--lg btn--block" href="<?php echo esc_url( add_query_arg( 'stand', $code, Alz::dashboard_url() ) ); ?>">أنشئ حسابك واربط الاستاند</a>
		<?php else : ?>
			<form method="post">
				<?php wp_nonce_field( 'alz_claim_' . $code, '_alznonce' ); ?>
				<input type="hidden" name="alz_claim" value="1">
				<button type="submit" class="btn btn--gold btn--lg btn--block"><?php echo $is_profile ? 'اربط الاستاند بصفحتي' : 'اربط الاستاند بحسابي'; ?></button>
			</form>
		<?php endif; ?>
	</div>
</main>
</body></html>
