<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$order = AlzOptions::order_link();
?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0b1020">
<style><?php echo Alz::font_css(); // phpcs:ignore ?></style>
<link rel="stylesheet" href="<?php echo esc_url( $this->StylesURL . 'main.css?v=' . filemtime( $this->StylesPath . 'main.css' ) ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( Alz::asset_url( 'css/profile.css' ) ); ?>">
<?php wp_head(); ?>
<?php echo AlzOptions::get( 'site_head_code' ); // phpcs:ignore -- admin-only code ?>
</head>
<body <?php body_class( is_front_page() ? 'is-landing' : 'is-inner' ); ?>>
<?php wp_body_open(); ?>
<header class="sh<?php echo is_front_page() ? ' sh--over' : ''; ?>">
	<div class="container sh__in">
		<?php echo Alz::logo_html( is_front_page() ); // phpcs:ignore ?>
		<nav class="sh__nav" id="siteNav">
			<?php
			if ( has_nav_menu( 'home-menu' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'home-menu',
						'container'      => false,
						'items_wrap'     => '<ul>%3$s</ul>',
						'depth'          => 1,
					)
				);
			} else {
				$home = is_front_page() ? '' : home_url( '/' );
				echo '<ul><li><a href="' . esc_url( $home . '#features' ) . '">المميزات</a></li><li><a href="' . esc_url( $home . '#demo' ) . '">جرّب بنفسك</a></li><li><a href="' . esc_url( $home . '#how' ) . '">طريقة العمل</a></li><li><a href="' . esc_url( $home . '#offer' ) . '">العروض</a></li></ul>';
			}
			?>
		</nav>
		<div class="sh__cta">
			<a class="btn btn--ghost-light btn--sm" href="<?php echo esc_url( is_user_logged_in() ? Alz::dashboard_url() : Alz::login_url() ); ?>"><?php echo is_user_logged_in() ? 'لوحة التحكم' : 'دخول العملاء'; ?></a>
			<a class="btn btn--gold btn--sm hide-sm" href="<?php echo esc_url( $order ); ?>" target="_blank" rel="noopener">اطلب الآن</a>
			<button class="sh__burger" aria-label="القائمة" aria-controls="siteNav" onclick="document.body.classList.toggle('nav-open')"><?php echo Alz::icon( 'hamburger' ); // phpcs:ignore ?></button>
		</div>
	</div>
</header>
