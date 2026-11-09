<?php
// Blog & knowledge center (lists posts with pagination).
global $post;
$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$q = new WP_Query( array( 'post_type' => 'post', 'paged' => $paged, 'posts_per_page' => 9 ) );
echo '<main id="main">';
	dg_page_hero( 'Auto Care Blog', get_the_title( $post ), 'دليلك الهندسي للحفاظ على كفاءة سيارتك الألمانية في أجواء مصر — مقالات وإجابات من مهندسي دويتش جراج.', '', '' );
	echo '<section class="section"><div class="container">';
	$cats = get_categories( array( 'hide_empty' => true ) );
	if ( $cats ) {
		echo '<nav class="cat-pills" aria-label="التصنيفات"><ul role="list">';
		foreach ( $cats as $c ) { echo '<li><a href="' . esc_url( get_category_link( $c ) ) . '">' . esc_html( $c->name ) . '</a></li>'; }
		echo '</ul></nav>';
	}
	if ( $q->have_posts() ) {
		echo '<ul class="posts-grid" role="list">';
		while ( $q->have_posts() ) { $q->the_post(); dg_post_card(); }
		echo '</ul>';
		echo '<nav class="pager" aria-label="الصفحات">' . paginate_links( array( 'total' => $q->max_num_pages, 'current' => $paged, 'prev_text' => 'السابق', 'next_text' => 'التالي' ) ) . '</nav>';
		wp_reset_postdata();
	} else {
		echo '<p class="empty">لم يتم نشر مقالات بعد.</p>';
	}
	echo '</div></section>';
	dg_cta_band();
echo '</main>';
