<?php
// Booking / Smart Service Estimator page.
global $post;
echo '<main id="main" class="booking">';
	dg_page_hero( 'Book a Service', get_the_title( $post ), 'احجز موعد صيانة أو اطلب تسعيرة فورية في 4 خطوات — الطلب يوصل منسّق لواتساب المركز مباشرة.', '', '' );
	dg_estimator( false );
	if ( $post && trim( $post->post_content ) ) { echo '<section class="section"><div class="container container--narrow prose">' . apply_filters( 'the_content', $post->post_content ) . '</div></section>'; }
	dg_map_block();
echo '</main>';
