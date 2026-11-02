<?php
/**
 * /s/{code} — استاند جديد غير مربوط (المربوط يتحول تلقائياً قبل الوصول هنا)
 *
 * @var string $code
 * @var int    $user_id
 * @var bool   $has_profile
 * @var string $error
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
Alz::head( 'تفعيل الاستاند', '<meta name="robots" content="noindex"><link rel="stylesheet" href="' . esc_url( Alz::asset_url( 'css/app.css' ) ) . '">' );
echo '</head><body class="app app--dark">';
?>
<main class="center-screen">
	<div class="stand-card">
		<?php echo Alz::logo_html( true ); // phpcs:ignore ?>
		<div class="stand-card__icon"><?php echo Alz::icon( 'nfc' ); // phpcs:ignore ?></div>
		<h1>استاند جديد جاهز للتفعيل</h1>
		<p class="muted">هذا الاستاند لم يُربط بأي نشاط بعد. إذا كنت صاحب الاستاند، فعّله الآن ليفتح صفحتك تلقائياً عند كل لمسة أو مسح.</p>
		<p class="code-chip" dir="ltr"><?php echo esc_html( $code ); ?></p>

		<?php if ( $error ) : ?>
			<p class="alert alert--error"><?php echo esc_html( $error ); ?></p>
		<?php endif; ?>

		<?php if ( ! $user_id ) : ?>
			<a class="btn btn--gold btn--lg btn--block" href="<?php echo esc_url( Alz::login_url( '/s/' . $code . '/' ) ); ?>">سجّل الدخول لتفعيل الاستاند</a>
		<?php elseif ( ! $has_profile ) : ?>
			<a class="btn btn--gold btn--lg btn--block" href="<?php echo esc_url( add_query_arg( 'stand', $code, Alz::dashboard_url() ) ); ?>">أنشئ صفحتك واربط الاستاند</a>
		<?php else : ?>
			<form method="post">
				<?php wp_nonce_field( 'alz_claim_' . $code, '_alznonce' ); ?>
				<input type="hidden" name="alz_claim" value="1">
				<button type="submit" class="btn btn--gold btn--lg btn--block">اربط الاستاند بصفحتي</button>
			</form>
		<?php endif; ?>
	</div>
</main>
</body></html>
