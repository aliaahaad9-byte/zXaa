<?php
// Pre-purchase inspection landing page.
global $post;
echo '<main id="main" class="pp">';
	dg_page_hero( 'Pre-Purchase Check', 'لا تشتري سيارة ألمانية مستعملة قبل فحصها بالكامل في دويتش جراج', 'فحص شامل قبل الشراء في سيدي جابر بالإسكندرية: سمك الدهان، الشاسيه، كبس المحرك والفتيس، وكشف التلاعب بالعداد — وتقرير مكتوب تتفاوض بيه على السعر.', dg_img( 'pre-purchase' ),
		dg_btn( 'احجز فحص شراء فوري', dg_booking_url( array( 'need' => 'فحص قبل الشراء' ) ) . '#estimator', 'primary', 'calendar' ) .
		dg_btn( 'اسأل عن السعر على واتساب', dg_wa( 'مرحبًا، عايز أحجز فحص سيارة قبل الشراء' ), 'wa', 'whatsapp' ) );

	echo '<section class="section"><div class="container">';
		dg_section_head( 'Inspection Checklist', 'قائمة الفحص الشامل', 'سبعة محاور فحص بتكشف حالة العربية الحقيقية قبل ما تدفع.' );
		echo '<ol class="checklist" role="list">';
		foreach ( dg_prepurchase_checks() as $i => $c ) {
			echo '<li class="checklist__item"><span class="checklist__n latin">' . sprintf( '%02d', $i + 1 ) . '</span><span class="checklist__ico">' . dg_icon( $c[0] ) . '</span><div><h3>' . esc_html( $c[1] ) . '</h3><p>' . esc_html( $c[2] ) . '</p></div></li>';
		}
		echo '</ol>';
		if ( $post && trim( $post->post_content ) ) { echo '<div class="prose">' . apply_filters( 'the_content', $post->post_content ) . '</div>'; }
	echo '</div></section>';

	echo '<section class="section section--alt"><div class="container steps3">';
		dg_section_head( 'How it works', 'إزاي بيتم الفحص؟' );
		echo '<ol class="steps" role="list">';
		foreach ( array( array( 'calendar', 'احجز موعد', 'ابعتلنا بيانات العربية والبائع، ونحدد ميعاد مناسب.' ), array( 'scan', 'فحص 360°', 'فحص ميكانيكي وإلكتروني وهيكلي لمدة تقريبية ساعة ونص.' ), array( 'file', 'تقرير مكتوب', 'تقرير بالحالة والعيوب وتكلفة إصلاحها التقريبية قبل ما تشتري.' ) ) as $i => $s ) {
			echo '<li class="step"><span class="step__n latin">' . ( $i + 1 ) . '</span>' . dg_icon( $s[0] ) . '<h3>' . esc_html( $s[1] ) . '</h3><p>' . esc_html( $s[2] ) . '</p></li>';
		}
		echo '</ol>';
	echo '</div></section>';

	dg_estimator();
	dg_faq_block( array(
		array( 'الفحص بياخد وقت قد إيه؟', 'حوالي ساعة إلى ساعة ونص حسب الموديل، وبتستلم التقرير في نفس اليوم.' ),
		array( 'ينفع البائع يجيب العربية من غيري؟', 'أيوه، ونبعتلك التقرير والصور على واتساب مباشرة.' ),
		array( 'بتكشفوا التلاعب في العداد إزاي؟', 'بنقارن الكيلومترات المسجلة في أكثر من كنترول (المحرك، الفتيس، الطابلوه) وسجل الصيانة.' ),
	), 'أسئلة عن فحص ما قبل الشراء' );
	dg_cta_band( 'اشتري وانت مطمّن', 'تقرير فحص مكتوب من مهندس متخصص في السيارات الألمانية قبل ما تدفع.' );
echo '</main>';
