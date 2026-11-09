<?php
// Single repair case.
global $post;
$rows = array(
	array( 'alert', 'المشكلة', get_post_meta( $post->ID, 'case_problem', true ) ),
	array( 'scan', 'التشخيص بالكمبيوتر', get_post_meta( $post->ID, 'case_diagnosis', true ) ),
	array( 'check', 'الحل الفني', get_post_meta( $post->ID, 'case_solution', true ) ),
);
$video = get_post_meta( $post->ID, 'case_video', true );
$img   = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'full' ) : dg_img( 'svc-diagnostics' );
echo '<main id="main">';
	dg_page_hero( 'Case Study', get_the_title( $post ), has_excerpt( $post ) ? get_the_excerpt( $post ) : '', $img, $video ? dg_btn( 'شاهد الفيديو', $video, 'primary', 'play', 'data-dg-reel' ) : '' );
	echo '<section class="section"><div class="container container--narrow">';
		echo '<ol class="case-steps" role="list">';
		foreach ( $rows as $r ) {
			if ( ! $r[2] ) { continue; }
			echo '<li><span class="case-steps__ico">' . dg_icon( $r[0] ) . '</span><div><h2>' . esc_html( $r[1] ) . '</h2><p>' . nl2br( esc_html( $r[2] ) ) . '</p></div></li>';
		}
		echo '</ol>';
		echo '<div class="prose">' . apply_filters( 'the_content', $post->post_content ) . '</div>';
	echo '</div></section>';
	dg_reel_modal();
	dg_cta_band( 'عندك مشكلة مشابهة؟', 'ابعتلنا التفاصيل ونرد عليك بالتشخيص المبدئي والتكلفة.' );
echo '</main>';
