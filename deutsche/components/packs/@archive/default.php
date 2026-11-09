<?php
// Unified archive: categories, tags, brands, dates.
global $wp_query;
$obj   = get_queried_object();
$title = wp_strip_all_tags( get_the_archive_title() );
$desc  = wp_strip_all_tags( get_the_archive_description() );
if ( $obj && isset( $obj->name ) ) { $title = $obj->name; }
echo '<main id="main">';
	dg_page_hero( 'Auto Care Blog', $title, $desc ? $desc : 'مقالات ونصائح من مهندسي دويتش جراج للسيارات الألمانية.', '', '' );
	echo '<section class="section"><div class="container">';
	if ( have_posts() ) {
		echo '<ul class="posts-grid" role="list">';
		while ( have_posts() ) { the_post(); dg_post_card(); }
		echo '</ul>';
		echo '<nav class="pager" aria-label="الصفحات">' . paginate_links( array( 'total' => $wp_query->max_num_pages, 'prev_text' => 'السابق', 'next_text' => 'التالي' ) ) . '</nav>';
	} else {
		echo '<p class="empty">لا توجد مقالات هنا بعد.</p>';
	}
	echo '</div></section>';
	dg_cta_band();
echo '</main>';
