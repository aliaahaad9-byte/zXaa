<?php
/**
 * شبكة المقالات للـ Loop الحالي
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! have_posts() ) {
	echo '<p class="center muted" style="padding:60px 0">لا توجد مقالات بعد.</p>';
	return;
}
echo '<div class="cards">';
while ( have_posts() ) {
	the_post();
	$img = get_the_post_thumbnail_url( null, 'medium_large' );
	echo '<a class="card-post" href="' . esc_url( get_permalink() ) . '">';
	echo '<span class="card-post__img"' . ( $img ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '' ) . '></span>';
	echo '<span class="card-post__body"><h2>' . esc_html( get_the_title() ) . '</h2><p>' . esc_html( wp_trim_words( get_the_excerpt(), 22 ) ) . '</p></span>';
	echo '</a>';
}
echo '</div>';
wp_reset_postdata();
