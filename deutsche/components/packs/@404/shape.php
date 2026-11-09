<?php
echo '<main id="main" class="nf">';
	echo '<section class="section"><div class="container nf__inner">';
		echo '<p class="nf__code latin" aria-hidden="true">404</p>';
		echo '<h1 class="sec-title">الصفحة دي مش موجودة</h1>';
		echo '<p class="sec-sub">يمكن الرابط اتغيّر. جرّب الرئيسية أو احجز موعدك مباشرة.</p>';
		echo '<div class="nf__ctas">' . dg_btn( 'الرئيسية', home_url( '/' ), 'primary', 'arrow' ) . dg_btn( 'احجز موعد', dg_booking_url(), 'ghost', 'calendar' ) . '</div>';
	echo '</div></section>';
echo '</main>';
