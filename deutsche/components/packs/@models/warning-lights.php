<?php
// Interactive warning-light guide page.
global $post;
echo '<main id="main" class="wl-page">';
	dg_page_hero( 'Warning Light Guide', get_the_title( $post ), 'اضغط على الرمز اللي منور في طابلوه عربيتك واعرف الأسباب المحتملة، وهل لازم توقف فورًا ولا تقدر توصل للمركز.', '', dg_btn( 'طوارئ؟ اتصل الآن', dg_tel(), 'primary', 'phone' ) );
	dg_warning_guide( 0, false );
	if ( $post && trim( $post->post_content ) ) { echo '<section class="section"><div class="container container--narrow prose">' . apply_filters( 'the_content', $post->post_content ) . '</div></section>'; }
	dg_cta_band( 'اللمبة لسه منورة؟', 'فحص كمبيوتر بأجهزة الوكالة يحدد السبب بالظبط بدل التخمين وتغيير قطع مالهاش لازمة.' );
echo '</main>';
