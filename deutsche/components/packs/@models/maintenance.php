<?php
// Digital maintenance schedules page.
global $post;
echo '<main id="main">';
	dg_page_hero( 'Maintenance Schedules', get_the_title( $post ), 'اعرف البنود الوقائية المستحقة لعربيتك عند 10 و30 و60 و100 ألف كيلومتر لكل ماركة ألمانية.', '', '' );
	dg_schedules_block();
	if ( $post && trim( $post->post_content ) ) { echo '<section class="section"><div class="container container--narrow prose">' . apply_filters( 'the_content', $post->post_content ) . '</div></section>'; }
	dg_estimator();
echo '</main>';
