<?php
// Case studies & reels hub.
global $post;
echo '<main id="main">';
	dg_page_hero( 'Case Studies & Reels', get_the_title( $post ), 'مشاكل حقيقية اتحلت داخل دويتش جراج: المشكلة، التشخيص بالكمبيوتر، الحل الفني، والفيديو الموثّق.', '', '' );
	dg_cases_block( 12, 'أحدث حالات الإصلاح' );
	dg_reels_block();
	if ( $post && trim( $post->post_content ) ) { echo '<section class="section"><div class="container container--narrow prose">' . apply_filters( 'the_content', $post->post_content ) . '</div></section>'; }
	dg_cta_band();
echo '</main>';
