<?php
/**
 * مقال المدونة
 *
 * @var WP_Post $post
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
setup_postdata( $post );
?>
<main>
	<header class="page-head">
		<div class="container">
			<h1><?php echo esc_html( get_the_title( $post ) ); ?></h1>
			<div class="meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>"><?php echo esc_html( get_the_date( '', $post ) ); ?></time>
				<?php
				$cats = get_the_category( $post->ID );
				if ( $cats ) {
					echo '<a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
				}
				?>
			</div>
		</div>
	</header>
	<article class="content">
		<?php if ( has_post_thumbnail( $post ) ) : ?>
			<figure class="post-thumb"><?php echo get_the_post_thumbnail( $post, 'large' ); ?></figure>
		<?php endif; ?>
		<?php the_content(); ?>
	</article>
</main>
<?php
wp_reset_postdata();
