<?php
/**
 * المدونة / الأقسام / الوسوم
 *
 * @var WP_Term|null $obj
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$title = isset( $obj ) && $obj ? $obj->name : ( is_home() ? 'المدونة' : get_the_archive_title() );
$desc  = isset( $obj ) && $obj ? term_description( $obj ) : '';
?>
<main>
	<header class="page-head">
		<div class="container">
			<h1><?php echo esc_html( wp_strip_all_tags( $title ) ); ?></h1>
			<?php if ( $desc ) : ?>
				<div class="muted"><?php echo wp_kses_post( $desc ); ?></div>
			<?php endif; ?>
		</div>
	</header>
	<div class="container">
		<?php $this->Part( 'Posts' ); ?>
		<?php $this->Pagination(); ?>
	</div>
</main>
