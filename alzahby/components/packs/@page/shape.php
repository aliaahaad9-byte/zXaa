<?php
/**
 * @var WP_Post $post
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
setup_postdata( $post );
?>
<main>
	<header class="page-head"><div class="container"><h1><?php echo esc_html( get_the_title( $post ) ); ?></h1></div></header>
	<article class="content">
		<?php the_content(); ?>
	</article>
</main>
<?php
wp_reset_postdata();
