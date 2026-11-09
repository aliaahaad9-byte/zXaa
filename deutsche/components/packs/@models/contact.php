<?php
// Contact page.
global $post;
echo '<main id="main">';
	dg_page_hero( 'Contact', get_the_title( $post ), 'اتصل بنا، راسلنا على واتساب، أو تعالى الورشة في سيدي جابر.', '', dg_btn( 'اتصل الآن', dg_tel(), 'primary', 'phone' ) . dg_btn( 'واتساب', dg_wa(), 'wa', 'whatsapp' ) );
	dg_map_block();
	if ( $post && trim( $post->post_content ) ) { echo '<section class="section"><div class="container container--narrow prose">' . apply_filters( 'the_content', $post->post_content ) . '</div></section>'; }
	dg_estimator();
echo '</main>';
