<?php
// FAQ page — items from the "الأسئله" post type (falls back to defaults).
global $post;
echo '<main id="main">';
	dg_page_hero( 'FAQ', get_the_title( $post ), $post ? wp_strip_all_tags( $post->post_content ) : '', '', '' );
	dg_faq_block( dg_faq_items(), 'إجابات سريعة' );
	dg_cta_band( 'سؤالك مش موجود؟', 'كلّم مهندس الصيانة مباشرة على واتساب وهيرد عليك.' );
echo '</main>';
