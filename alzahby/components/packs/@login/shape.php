<?php
/**
 * /dashboard/login — Google OAuth / Magic Link / Email & Password
 *
 * @var string $next
 * @var string $error
 * @var string $notice
 * @var string $mode
 * @var string $email
 * @var bool   $google
 * @var bool   $signup
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
Alz::head( 'تسجيل الدخول — ' . Alz::opt( 'brand_name', 'QurTap' ), '<meta name="robots" content="noindex"><link rel="stylesheet" href="' . esc_url( Alz::asset_url( 'css/app.css' ) ) . '">' );
echo '</head><body class="app">';
$mode = in_array( $mode, array( 'magic', 'password', 'signup' ), true ) ? $mode : 'magic';
?>
<main class="auth">
	<section class="auth__form">
		<div class="auth__inner">
			<?php echo Alz::logo_html(); // phpcs:ignore ?>

			<?php if ( 'sent' === $notice ) : ?>
				<div class="sent-box">
					<?php echo Alz::icon( 'mail' ); // phpcs:ignore ?>
					<h2>افحص بريدك الإلكتروني</h2>
					<p class="muted">أرسلنا رابط الدخول إلى <b dir="ltr"><?php echo esc_html( $email ); ?></b> — صالح لمدة 15 دقيقة.</p>
					<a class="btn btn--ghost" href="<?php echo esc_url( Alz::login_url() ); ?>">تغيير البريد</a>
				</div>
			<?php else : ?>
				<h1>أهلاً بك 👋</h1>
				<p class="muted">سجّل الدخول لإدارة صفحة نشاطك وروابط الاستاند.</p>

				<?php if ( $error ) : ?>
					<p class="alert alert--error"><?php echo esc_html( $error ); ?></p>
				<?php endif; ?>

				<?php if ( $google ) : ?>
					<a class="btn btn--outline btn--lg btn--block" href="<?php echo esc_url( add_query_arg( 'next', rawurlencode( $next ), home_url( '/dashboard/login/google/' ) ) ); ?>">
						<span class="g-ic"><?php echo Alz::icon( 'google' ); // phpcs:ignore ?></span> المتابعة بحساب جوجل
					</a>
					<div class="divider"><span>أو بالبريد الإلكتروني</span></div>
				<?php endif; ?>

				<div class="seg" role="tablist">
					<button type="button" data-mode="magic" class="<?php echo 'magic' === $mode ? 'on' : ''; ?>"><?php echo Alz::icon( 'mail' ); // phpcs:ignore ?> رابط سحري</button>
					<button type="button" data-mode="password" class="<?php echo 'magic' !== $mode ? 'on' : ''; ?>"><?php echo Alz::icon( 'key' ); // phpcs:ignore ?> كلمة مرور</button>
				</div>

				<form method="post" class="stack" id="loginForm" data-mode="<?php echo esc_attr( $mode ); ?>">
					<?php wp_nonce_field( 'alz_login', '_alznonce' ); ?>
					<input type="hidden" name="alz_login" value="1">
					<input type="hidden" name="mode" value="<?php echo esc_attr( $mode ); ?>">
					<input type="hidden" name="next" value="<?php echo esc_attr( $next ); ?>">
					<input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hp" aria-hidden="true">

					<label class="field">
						<span>البريد الإلكتروني</span>
						<input type="email" name="email" dir="ltr" required autocomplete="email" value="<?php echo esc_attr( $email ); ?>">
					</label>
					<label class="field only-pass">
						<span>كلمة المرور</span>
						<input type="password" name="password" dir="ltr" minlength="8" autocomplete="current-password">
					</label>
					<button type="submit" class="btn btn--dark btn--lg btn--block">
						<span class="t-magic">أرسل رابط الدخول</span>
						<span class="t-password">تسجيل الدخول</span>
						<span class="t-signup">إنشاء حساب</span>
					</button>
					<?php if ( $signup ) : ?>
						<p class="muted center only-pass">
							<span class="t-password">ليس لديك حساب؟ <button type="button" class="link" data-mode="signup">أنشئ حساباً مجاناً</button></span>
							<span class="t-signup">لديك حساب؟ <button type="button" class="link" data-mode="password">سجّل الدخول</button></span>
						</p>
					<?php endif; ?>
				</form>
			<?php endif; ?>
		</div>
	</section>
	<aside class="auth__side">
		<div class="glow"></div>
		<div class="auth__side-txt">
			<p class="big">عدّل روابطك في ثواني..<br><span class="gold-text">والاستاند يفضل زي ما هو.</span></p>
			<p class="muted-light">تسجيل الدخول مجاني بالكامل عبر حساب جوجل أو رابط سحري على بريدك.</p>
		</div>
	</aside>
</main>
<script>
(function () {
  var f = document.getElementById("loginForm");
  if (!f) return;
  function set(m) {
    f.setAttribute("data-mode", m);
    f.mode.value = m;
    f.password.required = m !== "magic";
    f.password.autocomplete = m === "signup" ? "new-password" : "current-password";
    document.querySelectorAll(".seg button").forEach(function (b) {
      b.classList.toggle("on", (m === "magic") === (b.getAttribute("data-mode") === "magic"));
    });
  }
  document.querySelectorAll("[data-mode]").forEach(function (b) {
    if (b.tagName === "BUTTON") b.addEventListener("click", function () { set(b.getAttribute("data-mode")); });
  });
  set(f.getAttribute("data-mode"));
})();
</script>
</body></html>
