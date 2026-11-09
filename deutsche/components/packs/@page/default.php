<?php
// Generic page (no model selected).
global $post;
echo '<main id="main">';
	dg_page_hero( '', get_the_title( $post ), has_excerpt( $post ) ? get_the_excerpt( $post ) : '', has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'full' ) : '', '' );
	echo '<section class="section"><div class="container container--narrow"><div class="prose">';
		echo apply_filters( 'the_content', $post->post_content );
	echo '</div></div></section>';
	dg_cta_band();
echo '</main>';
