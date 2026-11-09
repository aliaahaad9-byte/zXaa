<?php
global $wp_query;
$s = get_search_query();
echo '<main id="main">';
	dg_page_hero( 'Search', 'نتائج البحث عن: ' . $s, sprintf( 'وجدنا %d نتيجة', (int) $wp_query->found_posts ), '', '' );
	echo '<section class="section"><div class="container">';
	echo '<form class="search-form" role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '"><label for="s2" class="sr-only">ابحث</label><input id="s2" type="search" name="s" value="' . esc_attr( $s ) . '" placeholder="ابحث عن عطل، ماركة، أو خدمة"><button class="btn btn--primary" type="submit">' . dg_icon( 'search' ) . '<span>بحث</span></button></form>';
	if ( have_posts() ) {
		echo '<ul class="posts-grid" role="list">';
		while ( have_posts() ) { the_post(); dg_post_card(); }
		echo '</ul>';
		echo '<nav class="pager" aria-label="الصفحات">' . paginate_links( array( 'total' => $wp_query->max_num_pages, 'prev_text' => 'السابق', 'next_text' => 'التالي' ) ) . '</nav>';
	} else {
		echo '<p class="empty">لا توجد نتائج. جرّب كلمة أخرى أو تواصل معنا مباشرة.</p>';
	}
	echo '</div></section>';
echo '</main>';
