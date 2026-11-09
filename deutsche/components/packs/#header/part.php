<?php
/**
 * Deutsche Garage — document head + site header.
 */
$lang = get_option( 'yc_lang' ) ? get_option( 'yc_lang' ) : 'ar-EG';
echo '<!DOCTYPE html>';
echo '<html lang="' . esc_attr( $lang ) . '" dir="rtl">';
echo '<head>';
echo '<meta charset="utf-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">';
if ( ! current_theme_supports( 'title-tag' ) ) { echo '<title>' . wp_get_document_title() . '</title>'; }
$font_url = get_template_directory_uri() . '/assets/fonts/';
echo '<link rel="preload" href="' . esc_url( $font_url . 'cairo-arabic.woff2' ) . '" as="font" type="font/woff2" crossorigin>';
do_action( 'BeforeWPHead' );
wp_head();
do_action( 'AfterWPHead' );

$fav = dg_opt( 'favicon' );
echo '<link rel="icon" href="' . esc_url( $fav ? $fav : dg_asset( 'img/favicon.png' ) ) . '">';
echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr( dg_name() ) . '">';
echo '<meta name="application-name" content="' . esc_attr( dg_name() ) . '">';
echo '<meta name="msapplication-TileColor" content="#111111">';

// Font Awesome is only needed by legacy blog widgets/comments — load it non-blocking there.
if ( is_singular( 'post' ) || is_author() ) {
	echo '<link rel="stylesheet" href="' . esc_url( $this->StylesURL . 'fontawesome/css/fontawesome.css' ) . '" media="print" onload="this.media=\'all\'">';
	foreach ( array( 'solid', 'regular', 'light', 'brands' ) as $fa ) {
		echo '<link rel="stylesheet" href="' . esc_url( $this->StylesURL . 'fontawesome/css/' . $fa . '.css' ) . '" media="print" onload="this.media=\'all\'">';
	}
}

echo '<style>';
	echo str_replace( '{{URL}}', $font_url, file_get_contents( get_template_directory() . '/assets/fonts/fonts.tpl' ) );
	require get_template_directory() . '/components/styles/main.css';
	require get_template_directory() . '/components/styles/responsive.css';
echo '</style>';

$head_code = wp_is_mobile() ? get_option( 'mobile_ads_header', '' ) : get_option( 'ads_header', '' );
$hide_code = wp_is_mobile() ? get_option( 'show_ads_mobile_header' ) : get_option( 'show_ads_header' );
if ( $head_code && empty( $hide_code ) ) { echo $head_code; } // Admin-provided tracking codes (GA, Pixel…).
echo '</head>';

echo '<body class="' . esc_attr( implode( ' ', get_body_class() ) ) . '">';
if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); }
echo '<a class="skip-link" href="#main">تخطَّ إلى المحتوى</a>';

/* Top bar (desktop) */
echo '<div class="topbar"><div class="container topbar__inner">';
	echo '<a href="' . esc_url( dg_map_url() ) . '" target="_blank" rel="noopener">' . dg_icon( 'pin' ) . '<span>' . esc_html( dg_address() ) . '</span></a>';
	$hours_line = trim( strtok( (string) dg_opt( 'work_hours' ), "\n" ) );
	if ( $hours_line ) { echo '<span>' . dg_icon( 'clock' ) . '<span>' . esc_html( str_replace( '=', '·', $hours_line ) ) . '</span></span>'; }
	echo '<span class="topbar__social">';
		foreach ( array( 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'facebook' => 'Facebook', 'youtube' => 'YouTube' ) as $k => $label ) {
			$u = dg_opt( $k, $k === 'instagram' ? 'https://www.instagram.com/deutsche_garage_/' : ( $k === 'tiktok' ? 'https://www.tiktok.com/@deutsche_garage' : '' ) );
			if ( $u ) { echo '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $label ) . '">' . dg_icon( $k ) . '</a>'; }
		}
	echo '</span>';
echo '</div></div>';

/* Header */
echo '<header class="site-header" data-dg-header>';
	echo '<div class="container site-header__inner">';
		echo '<a class="brand" href="' . esc_url( home_url( '/' ) ) . '" aria-label="' . esc_attr( dg_name() ) . ' — الرئيسية">';
			echo '<img src="' . esc_url( dg_logo( true ) ) . '" alt="Deutsche Garage" width="190" height="30" fetchpriority="high">';
		echo '</a>';

		echo '<nav class="nav" id="site-nav" aria-label="القائمة الرئيسية" data-dg-nav>';
			echo '<div class="nav__head"><img src="' . esc_url( dg_logo( true ) ) . '" alt="" width="150" height="24"><button type="button" class="icon-btn" data-nav-close aria-label="إغلاق القائمة">' . dg_icon( 'close' ) . '</button></div>';
			wp_nav_menu( array(
				'theme_location' => 'home-menu',
				'container'      => '',
				'items_wrap'     => '<ul class="nav__list">%3$s</ul>',
				'depth'          => 2,
				'fallback_cb'    => function () {
					echo '<ul class="nav__list"><li><a href="' . esc_url( home_url( '/' ) ) . '">الرئيسية</a></li>';
					foreach ( array( 'brand-vag' => 'فولكس / أودي / سكودا / سيات', 'brand-mercedes' => 'مرسيدس', 'brand-bmw' => 'BMW وميني', 'pre-purchase' => 'فحص قبل الشراء', 'warning-lights' => 'لمبات الأعطال', 'blog' => 'المدونة' ) as $m => $l ) {
						echo '<li><a href="' . esc_url( dg_page_url( $m ) ) . '">' . esc_html( $l ) . '</a></li>';
					}
					echo '</ul>';
				},
			) );
			echo '<div class="nav__foot">';
				echo dg_btn( 'احجز موعد', dg_booking_url(), 'primary', 'calendar' );
				echo dg_btn( 'اتصل ' . dg_phone(), dg_tel(), 'ghost-light', 'phone' );
			echo '</div>';
		echo '</nav>';

		echo '<div class="site-header__ctas">';
			echo '<a class="hdr-phone" href="' . esc_attr( dg_tel() ) . '">' . dg_icon( 'phone' ) . '<span class="latin" dir="ltr">' . esc_html( dg_phone() ) . '</span></a>';
			echo dg_btn( 'احجز موعد', dg_booking_url(), 'primary btn--sm hdr-book', 'calendar' );
			echo '<button type="button" class="icon-btn nav-toggle" aria-controls="site-nav" aria-expanded="false" data-nav-open aria-label="فتح القائمة">' . dg_icon( 'menu' ) . '</button>';
		echo '</div>';
	echo '</div>';
	echo '<div class="site-header__stripes" aria-hidden="true"><i></i><i></i><i></i></div>';
echo '</header>';
echo '<div class="nav-scrim" data-nav-close hidden></div>';
