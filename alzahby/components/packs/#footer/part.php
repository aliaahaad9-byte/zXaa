<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$text = AlzOptions::get( 'footer_text' );
?>
<footer class="sf">
	<div class="container sf__in">
		<?php echo Alz::logo_html(); // phpcs:ignore ?>
		<?php
		if ( has_nav_menu( 'footer-menu' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'footer-menu',
					'container'      => 'nav',
					'container_class'=> 'sf__nav',
					'depth'          => 1,
				)
			);
		}
		?>
		<p class="muted">
			<?php echo $text ? esc_html( $text ) : '© ' . esc_html( gmdate( 'Y' ) . ' ' . Alz::opt( 'brand_name', 'QurTap' ) ) . '. جميع الحقوق محفوظة.'; ?>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
