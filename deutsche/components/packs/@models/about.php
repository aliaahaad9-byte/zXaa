<?php
// About & workshop tour.
global $post;
echo '<main id="main" class="about">';
	dg_page_hero( 'About & Workshop', get_the_title( $post ), 'شفافية كاملة، قطع غيار أصلية، وضمان معتمد على كافة الإصلاحات — من قلب سيدي جابر.', dg_img( 'workshop-about' ), dg_btn( 'احجز زيارة للورشة', dg_booking_url(), 'primary', 'calendar' ) );
	echo '<section class="section"><div class="container about__grid">';
		echo '<div class="prose">';
		if ( $post && trim( $post->post_content ) ) {
			echo apply_filters( 'the_content', $post->post_content );
		} else {
			echo '<h2>قصة دويتش جراج</h2>';
			echo '<p>بدأنا دويتش جراج عشان نحل أكبر مشكلة بتقابل ملاك السيارات الألمانية في الإسكندرية: التشخيص بالتخمين، والقطع المقلدة، والفاتورة اللي بتتفاجئ بيها في الآخر.</p>';
			echo '<p>اخترنا مكاننا في 16 خلف شارع بورسعيد بسيدي جابر — وسط الإسكندرية — عشان توصل لنا بسهولة من سموحة وكليوباترا ورشدي من غير مشوار المناطق الصناعية.</p>';
			echo '<p>كل عربية بتدخل الورشة بتاخد تقرير فحص مكتوب قبل الإصلاح، وتسعيرة واضحة توافق عليها، وتقرير بعد التسليم بكل اللي اتعمل.</p>';
		}
		echo '</div>';
		echo '<ul class="stats" role="list">';
		foreach ( array( array( 'cpu', 'ODIS · Xentry · ISTA', 'أجهزة تشخيص أصلية' ), array( 'shield', 'OEM', 'قطع أصلية بضمان مكتوب' ), array( 'file', 'قبل / بعد', 'تقارير فنية شفافة' ), array( 'pin', 'سيدي جابر', 'موقع حضري مركزي' ) ) as $s ) {
			echo '<li>' . dg_icon( $s[0] ) . '<b class="latin">' . esc_html( $s[1] ) . '</b><span>' . esc_html( $s[2] ) . '</span></li>';
		}
		echo '</ul>';
	echo '</div></section>';
	dg_why_block();
	dg_reels_block( 'جولة داخل الورشة' );
	dg_map_block();
echo '</main>';
