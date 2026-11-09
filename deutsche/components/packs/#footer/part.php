<?php
/**
 * Deutsche Garage — site footer, sticky mobile action bar and scripts.
 */
$year = date_i18n( 'Y' );
echo '<footer class="site-footer">';
	echo '<div class="site-footer__stripes" aria-hidden="true"><i></i><i></i><i></i></div>';
	echo '<div class="container footer__grid">';

		echo '<div class="footer__brand">';
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="brand"><img src="' . esc_url( dg_opt( 'logoFooter', dg_logo( true ) ) ) . '" alt="Deutsche Garage" width="200" height="32" loading="lazy"></a>';
			echo '<p>' . esc_html( dg_opt( 'footer_about', 'دويتش جراج — مركز متخصص في صيانة وبرمجة السيارات الألمانية في سيدي جابر بالإسكندرية. فحص كمبيوتر بأجهزة الوكالة، قطع أصلية، وضمان معتمد.' ) ) . '</p>';
			echo '<ul class="social" role="list">';
			foreach ( array( 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'facebook' => 'Facebook', 'youtube' => 'YouTube' ) as $k => $label ) {
				$u = dg_opt( $k, $k === 'instagram' ? 'https://www.instagram.com/deutsche_garage_/' : ( $k === 'tiktok' ? 'https://www.tiktok.com/@deutsche_garage' : '' ) );
				if ( $u ) { echo '<li><a href="' . esc_url( $u ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $label ) . '">' . dg_icon( $k ) . '</a></li>'; }
			}
			echo '</ul>';
		echo '</div>';

		echo '<nav class="footer__col" aria-label="الماركات"><h2>الماركات</h2><ul role="list">';
		foreach ( array( 'brand-mercedes' => 'مرسيدس بنز', 'brand-bmw' => 'بي إم دبليو وميني', 'brand-vag' => 'فولكس فاجن · أودي · سكودا · سيات', 'brand-porsche-opel' => 'بورش وأوبل' ) as $m => $l ) {
			echo '<li><a href="' . esc_url( dg_page_url( $m ) ) . '">' . esc_html( $l ) . '</a></li>';
		}
		echo '</ul></nav>';

		echo '<nav class="footer__col" aria-label="روابط مهمة"><h2>روابط مهمة</h2>';
		if ( has_nav_menu( 'footer-menu' ) ) {
			wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'depth' => 1, 'items_wrap' => '<ul role="list">%3$s</ul>' ) );
		} else {
			echo '<ul role="list">';
			foreach ( array( 'pre-purchase' => 'فحص قبل الشراء', 'warning-lights' => 'دليل لمبات الأعطال', 'maintenance' => 'جداول الصيانة', 'booking' => 'احجز موعد', 'blog' => 'المدونة' ) as $m => $l ) {
				echo '<li><a href="' . esc_url( dg_page_url( $m ) ) . '">' . esc_html( $l ) . '</a></li>';
			}
			echo '</ul>';
		}
		echo '</nav>';

		echo '<div class="footer__col footer__contact"><h2>تواصل معنا</h2><ul role="list">';
			echo '<li>' . dg_icon( 'pin' ) . '<a href="' . esc_url( dg_map_url() ) . '" target="_blank" rel="noopener">' . esc_html( dg_address() ) . '</a></li>';
			echo '<li>' . dg_icon( 'phone' ) . '<span><a class="latin" dir="ltr" href="' . esc_attr( dg_tel() ) . '">' . esc_html( dg_phone() ) . '</a>' . ( dg_phone2() ? '<br><a class="latin" dir="ltr" href="' . esc_attr( dg_tel( dg_phone2() ) ) . '">' . esc_html( dg_phone2() ) . '</a>' : '' ) . '</span></li>';
			echo '<li>' . dg_icon( 'whatsapp' ) . '<a class="latin" dir="ltr" href="' . esc_url( dg_wa() ) . '" target="_blank" rel="noopener">' . esc_html( dg_wa_num() ) . '</a></li>';
			$hours = dg_opt( 'work_hours' );
			if ( $hours ) {
				echo '<li>' . dg_icon( 'clock' ) . '<span>';
				foreach ( preg_split( '/\r?\n/', $hours ) as $line ) {
					if ( trim( $line ) === '' ) { continue; }
					echo esc_html( str_replace( '=', '·', $line ) ) . '<br>';
				}
				echo '</span></li>';
			}
		echo '</ul></div>';

	echo '</div>';

	$copy = dg_opt( 'copyright_text', '© {year} {sitename}. جميع الحقوق محفوظة.' );
	$copy = str_replace( array( '{year}', '{sitename}' ), array( $year, dg_name() ), $copy );
	echo '<div class="footer__bottom"><div class="container footer__bottom-inner">';
		echo '<p>' . esc_html( $copy ) . '</p>';
		$dev = dg_opt( 'dev_name' );
		if ( $dev && ! get_option( 'hide_dev_credit' ) ) {
			$dev_url = dg_opt( 'dev_url' );
			echo '<p>' . esc_html( dg_opt( 'dev_label', 'تصميم وتطوير' ) ) . ' ' . ( $dev_url ? '<a href="' . esc_url( $dev_url ) . '" target="_blank" rel="noopener">' . esc_html( $dev ) . '</a>' : esc_html( $dev ) ) . '</p>';
		}
	echo '</div></div>';
echo '</footer>';

/* Floating WhatsApp (desktop) + sticky emergency bar (mobile) */
echo '<a class="fab-wa" href="' . esc_url( dg_wa( 'مرحبًا دويتش جراج' ) ) . '" target="_blank" rel="noopener" aria-label="تواصل على واتساب">' . dg_icon( 'whatsapp' ) . '</a>';
echo '<nav class="mbar" aria-label="تواصل سريع">';
	echo '<a class="mbar__btn mbar__btn--call" href="' . esc_attr( dg_tel() ) . '">' . dg_icon( 'phone' ) . '<span>اتصال</span></a>';
	echo '<a class="mbar__btn mbar__btn--wa" href="' . esc_url( dg_wa( 'مرحبًا دويتش جراج، محتاج مساعدة' ) ) . '" target="_blank" rel="noopener">' . dg_icon( 'whatsapp' ) . '<span>واتساب</span></a>';
	echo '<a class="mbar__btn mbar__btn--map" href="' . esc_url( dg_map_url() ) . '" target="_blank" rel="noopener">' . dg_icon( 'pin' ) . '<span>الموقع</span></a>';
echo '</nav>';

/* Scripts */
echo '<script>';
	echo 'window.DG=' . wp_json_encode( array(
		'ajax'  => admin_url( 'admin-ajax.php' ),
		'home'  => home_url(),
		'track' => function_exists( 'yc_track_record' ),
	) ) . ';';
	echo "var HomeURL='" . esc_js( home_url() ) . "',AdminAjax='" . esc_js( home_url( '/AjaxCenter/' ) ) . "',TmpDIR='" . esc_js( get_template_directory_uri() ) . "',IsSpeed=false,ISMobile=" . ( wp_is_mobile() ? 'true' : 'false' ) . ";";
	require get_template_directory() . '/assets/js/deutsche.js';
echo '</script>';

// Legacy jQuery bundle is only needed for blog comments/likes.
if ( is_singular( 'post' ) && comments_open() ) {
	echo '<script>';
	require $CurrentDir . 'js/jquery-3.4.1.min.js';
	echo '</script><script>';
	require $CurrentDir . 'js/owl.carousel.min.js';
	echo '</script><script>';
	require $CurrentDir . 'js/setup.js';
	echo '</script>';
}

wp_footer();
echo '</body>';
echo '</html>';
