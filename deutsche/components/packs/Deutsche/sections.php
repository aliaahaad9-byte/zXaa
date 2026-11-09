<?php
/**
 * Deutsche Garage — reusable page sections.
 * Each function echoes one self-contained, accessible section.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function dg_section_head( $kicker, $title, $sub = '', $tag = 'h2', $align = 'center' ) {
	echo '<header class="sec-head sec-head--' . esc_attr( $align ) . '">';
	if ( $kicker ) { echo '<p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>' . esc_html( $kicker ) . '</p>'; }
	echo '<' . $tag . ' class="sec-title">' . dg_t( $title ) . '</' . $tag . '>';
	if ( $sub ) { echo '<p class="sec-sub">' . esc_html( $sub ) . '</p>'; }
	echo '</header>';
}

function dg_btn( $label, $url, $variant = 'primary', $icon = '', $attrs = '' ) {
	$ext = ( strpos( $url, 'http' ) === 0 && strpos( $url, home_url() ) !== 0 ) ? ' target="_blank" rel="noopener"' : '';
	return '<a class="btn btn--' . esc_attr( $variant ) . '" href="' . esc_url( $url ) . '"' . $ext . ' ' . $attrs . '>' . ( $icon ? dg_icon( $icon ) : '' ) . '<span>' . esc_html( $label ) . '</span></a>';
}

function dg_breadcrumbs() {
	if ( function_exists( 'Breadcrumb' ) ) {
		echo '<nav class="crumbs" aria-label="مسار التنقل">';
		Breadcrumb();
		echo '</nav>';
	}
}

/* ---------------------------------------------------------------- Hero */
function dg_hero_home() {
	$h1    = dg_opt( 'hero_title', 'دويتش جراج (Deutsche Garage).. الدقة الألمانية لصيانة وبرمجة سيارتك في قلب الإسكندرية' );
	$sub   = dg_opt( 'hero_sub', 'المركز المتخصص في صيانة مرسيدس، بي إم دبليو، فولكس فاجن، أودي، سكودا، وسيات في سيدي جابر – فحص كمبيوتر متقدم، صيانات دورية، وقطع غيار أصلية بضمان معتمد' );
	$video = dg_opt( 'hero_video' );
	$img   = dg_img( 'hero-workshop' );
	$imgm  = dg_img( 'hero-mobile' );
	echo '<section class="hero" aria-labelledby="hero-title">';
		echo '<div class="hero__media">';
			echo '<picture>';
				echo '<source media="(max-width: 700px)" srcset="' . esc_url( $imgm ) . '">';
				echo dg_image( $img, 'ورشة دويتش جراج لصيانة السيارات الألمانية في سيدي جابر', 1920, 1080, array( 'loading' => false, 'fetchpriority' => 'high', 'class' => 'hero__img' ) );
			echo '</picture>';
			if ( $video ) {
				echo '<video class="hero__video" autoplay muted loop playsinline preload="none" poster="' . esc_url( $img ) . '" data-dg-video><source src="' . esc_url( $video ) . '" type="video/webm"></video>';
			}
			echo '<div class="hero__shade"></div>';
		echo '</div>';
		echo '<div class="container hero__inner">';
			echo '<p class="hero__badge"><span class="dot" aria-hidden="true"></span>مركز متخصص في السيارات الألمانية — سيدي جابر</p>';
			echo '<h1 id="hero-title" class="hero__title">' . dg_t( $h1 ) . '</h1>';
			echo '<p class="hero__sub">' . dg_t( $sub ) . '</p>';
			echo '<div class="hero__ctas">';
				echo dg_btn( 'احجز موعد صيانتك الآن', dg_booking_url(), 'primary', 'calendar' );
				echo dg_btn( 'استشارة فنية على واتساب', dg_wa( 'مرحبًا دويتش جراج، محتاج استشارة فنية لعربيتي' ), 'wa', 'whatsapp' );
			echo '</div>';
			echo '<ul class="hero__facts" role="list">';
				echo '<li>' . dg_icon( 'cpu' ) . '<span><b>ODIS · Xentry · ISTA</b>أجهزة تشخيص الوكالة</span></li>';
				echo '<li>' . dg_icon( 'shield' ) . '<span><b>OEM</b>قطع أصلية بضمان</span></li>';
				echo '<li>' . dg_icon( 'receipt' ) . '<span><b>تسعيرة أولًا</b>قبل أي إصلاح</span></li>';
			echo '</ul>';
		echo '</div>';
		echo '<div class="hero__stripes" aria-hidden="true"><i></i><i></i><i></i></div>';
	echo '</section>';
}

/* --------------------------------------------------------- Inner hero */
function dg_page_hero( $kicker, $h1, $lead, $img = '', $ctas = '' ) {
	echo '<section class="phero">';
		if ( $img ) {
			echo '<div class="phero__media">' . dg_image( $img, $h1, 1600, 800, array( 'loading' => false, 'fetchpriority' => 'high' ) ) . '<div class="phero__shade"></div></div>';
		}
		echo '<div class="container phero__inner">';
			dg_breadcrumbs();
			if ( $kicker ) { echo '<p class="kicker kicker--light"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span><span class="latin">' . esc_html( $kicker ) . '</span></p>'; }
			echo '<h1 class="phero__title">' . dg_t( $h1 ) . '</h1>';
			if ( $lead ) { echo '<p class="phero__lead">' . dg_t( $lead ) . '</p>'; }
			if ( $ctas ) { echo '<div class="phero__ctas">' . $ctas . '</div>'; }
		echo '</div>';
	echo '</section>';
}

/* ----------------------------------------------------- Brands strip */
function dg_brands_strip() {
	$items = dg_brands();
	echo '<section class="brands" aria-label="الماركات التي نخدمها">';
		echo '<div class="brands__track">';
		for ( $loop = 0; $loop < 4; $loop++ ) {
			echo '<ul class="brands__list" role="list"' . ( $loop ? ' aria-hidden="true"' : '' ) . '>';
			foreach ( $items as $b ) {
				$url = dg_page_url( $b['hub'] );
				echo '<li><a href="' . esc_url( $url ) . '"' . ( $loop ? ' tabindex="-1"' : '' ) . '><span class="brands__name latin">' . esc_html( $b['name'] ) . '</span><span class="brands__ar">' . esc_html( $b['ar'] ) . '</span></a></li>';
			}
			echo '</ul>';
		}
		echo '</div>';
	echo '</section>';
}

/* ------------------------------------------------------------ Why us */
function dg_why_block() {
	echo '<section class="section why" aria-labelledby="why-t">';
		echo '<div class="container">';
			echo '<header class="sec-head sec-head--center"><p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Why Deutsche Garage</p><h2 id="why-t" class="sec-title">لماذا دويتش جراج؟</h2><p class="sec-sub">نفس الدقة اللي اتصنعت بيها عربيتك — من غير قلق التشخيص الغلط أو القطع المقلدة أو الفاتورة المفاجئة.</p></header>';
			echo '<ul class="why__grid" role="list">';
			foreach ( dg_why() as $i => $w ) {
				echo '<li class="why__card"><span class="why__num latin">0' . ( $i + 1 ) . '</span><span class="why__ico">' . dg_icon( $w['icon'] ) . '</span><h3>' . esc_html( $w['title'] ) . '</h3><p>' . esc_html( $w['text'] ) . '</p></li>';
			}
			echo '</ul>';
		echo '</div>';
	echo '</section>';
}

/* ---------------------------------------------------------- Services */
function dg_services_grid( $title = 'خدماتنا الأساسية' ) {
	echo '<section class="section services" id="services" aria-labelledby="svc-t">';
		echo '<div class="container">';
			echo '<header class="sec-head sec-head--split"><div><p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Services</p><h2 id="svc-t" class="sec-title">' . esc_html( $title ) . '</h2></div><p class="sec-sub">ست خدمات تغطي احتياج عربيتك الألمانية من التشخيص لحد التسليم — بتقرير مكتوب وضمان.</p></header>';
			echo '<ul class="svc__grid" role="list">';
			foreach ( dg_services() as $s ) {
				$url = dg_booking_url( array( 'need' => $s['need'] ) );
				echo '<li class="svc">';
					echo '<a class="svc__link" href="' . esc_url( $url ) . '">';
						echo '<span class="svc__media">' . dg_image( dg_img( $s['slot'] ), $s['title'], 800, 560 ) . '</span>';
						echo '<span class="svc__body">';
							echo '<span class="svc__en latin">' . esc_html( $s['en'] ) . '</span>';
							echo '<h3 class="svc__title">' . esc_html( $s['title'] ) . '</h3>';
							echo '<span class="svc__text">' . esc_html( $s['text'] ) . '</span>';
							echo '<span class="svc__more">اطلب الخدمة ' . dg_icon( 'arrow' ) . '</span>';
						echo '</span>';
					echo '</a>';
				echo '</li>';
			}
			echo '</ul>';
		echo '</div>';
	echo '</section>';
}

/* ------------------------------------------------ Smart estimator */
function dg_estimator( $heading = true ) {
	$need   = isset( $_GET['need'] ) ? sanitize_text_field( wp_unslash( $_GET['need'] ) ) : '';
	$issue  = isset( $_GET['issue'] ) ? sanitize_key( wp_unslash( $_GET['issue'] ) ) : '';
	$lights = dg_warning_lights();
	if ( $issue && isset( $lights[ $issue ] ) ) { $need = $lights[ $issue ]['title']; }
	$brand_q = isset( $_GET['brand'] ) ? sanitize_text_field( wp_unslash( $_GET['brand'] ) ) : '';
	$needs  = array( 'صيانة دورية', 'فحص كمبيوتر', 'لمبة عطل منورة', 'مشكلة في الفتيس', 'برمجة وتكويد', 'عفشة وفرامل', 'تبريد / محرك', 'فحص قبل الشراء', 'أخرى' );
	if ( $need && ! in_array( $need, $needs, true ) ) { array_unshift( $needs, $need ); }
	$year_now = (int) date( 'Y' ) + 1;

	echo '<section class="section est-wrap" id="estimator" aria-labelledby="est-t">';
	echo '<div class="container est-grid">';
		echo '<div class="est-intro">';
			if ( $heading ) {
				echo '<p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Smart Service Estimator</p>';
				echo '<h2 id="est-t" class="sec-title">اعرف تكلفة صيانتك في 4 خطوات</h2>';
			} else {
				echo '<h2 id="est-t" class="sec-title">طلب تسعيرة وحجز موعد</h2>';
			}
			echo '<p class="sec-sub">اختار عربيتك واحكيلنا المطلوب، وطلبك هيوصل منسّق لواتساب المركز مباشرة — نرد عليك بالتكلفة التقريبية وأقرب موعد متاح.</p>';
			echo '<ul class="ticks" role="list"><li>' . dg_icon( 'check' ) . 'بدون التزام — التسعيرة مجانية</li><li>' . dg_icon( 'check' ) . 'رد سريع خلال مواعيد العمل</li><li>' . dg_icon( 'check' ) . 'أو اتصل مباشرة: <a class="latin" dir="ltr" href="' . esc_attr( dg_tel() ) . '">' . esc_html( dg_phone() ) . '</a></li></ul>';
		echo '</div>';

		echo '<form class="est" data-dg-est novalidate data-ajax="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" data-wa="' . esc_attr( dg_intl( dg_wa_num() ) ) . '">';
			echo '<input type="hidden" name="action" value="dg_lead"><input type="hidden" name="dg_nonce" value="' . esc_attr( wp_create_nonce( 'dg_lead' ) ) . '"><input type="hidden" name="source" value="estimator">';
			echo '<div class="est__progress" aria-hidden="true"><span style="--p:25%"></span></div>';
			echo '<ol class="est__steps" aria-label="خطوات الطلب"><li class="is-on">السيارة</li><li>سنة الصنع</li><li>المطلوب</li><li>التواصل</li></ol>';

			// Step 1
			echo '<fieldset class="est__step is-on" data-step="1"><legend>1. ماركة السيارة وموديلها</legend>';
				echo '<div class="chips" role="radiogroup" aria-label="الماركة">';
				foreach ( array( 'Mercedes-Benz', 'BMW', 'MINI', 'Volkswagen', 'Audi', 'Skoda', 'Seat', 'Porsche', 'Opel' ) as $b ) {
					echo '<label class="chip"><input type="radio" name="brand" value="' . esc_attr( $b ) . '" required' . checked( strcasecmp( $brand_q, $b ) === 0, true, false ) . '><span class="latin">' . esc_html( $b ) . '</span></label>';
				}
				echo '</div>';
				echo '<div class="field"><label for="est-model">الموديل <small>(مثال: C200 – Golf 7 – X5)</small></label><input id="est-model" name="model" type="text" autocomplete="off" maxlength="40" required></div>';
			echo '</fieldset>';

			// Step 2
			echo '<fieldset class="est__step" data-step="2" hidden><legend>2. سنة الصنع</legend>';
				echo '<div class="field"><label for="est-year">سنة الصنع</label><select id="est-year" name="year" required><option value="">اختر السنة</option>';
				for ( $y = $year_now; $y >= 1998; $y-- ) { echo '<option>' . $y . '</option>'; }
				echo '</select></div>';
			echo '</fieldset>';

			// Step 3
			echo '<fieldset class="est__step" data-step="3" hidden><legend>3. المطلوب وقراءة العداد</legend>';
				echo '<div class="field"><label for="est-need">نوع الخدمة أو العطل</label><select id="est-need" name="need" required><option value="">اختر</option>';
				foreach ( $needs as $n ) { echo '<option' . selected( $need, $n, false ) . '>' . esc_html( $n ) . '</option>'; }
				echo '</select></div>';
				echo '<div class="field" data-light-field hidden><label for="est-light">أي لمبة منورة؟</label><select id="est-light" name="light"><option value="">اختر اللمبة</option>';
				foreach ( $lights as $k => $l ) { echo '<option value="' . esc_attr( $l['title'] ) . '"' . selected( $issue, $k, false ) . '>' . esc_html( $l['title'] ) . '</option>'; }
				echo '</select></div>';
				echo '<div class="field"><label for="est-km">قراءة العداد بالكيلومتر <small>(اختياري)</small></label><input id="est-km" name="km" type="text" inputmode="numeric" pattern="[0-9٠-٩,. ]*" maxlength="9" dir="ltr" placeholder="85000"></div>';
				echo '<div class="field"><label for="est-notes">تفاصيل إضافية <small>(اختياري)</small></label><textarea id="est-notes" name="notes" rows="2" maxlength="300" placeholder="مثال: رعشة عند التحويل من 1 إلى 2"></textarea></div>';
			echo '</fieldset>';

			// Step 4
			echo '<fieldset class="est__step" data-step="4" hidden><legend>4. بيانات التواصل</legend>';
				echo '<div class="field"><label for="est-name">الاسم</label><input id="est-name" name="name" type="text" autocomplete="name" maxlength="60" required></div>';
				echo '<div class="field"><label for="est-phone">رقم الموبايل</label><input id="est-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" dir="ltr" placeholder="01xxxxxxxxx" pattern="[0-9+٠-٩ ]{8,16}" required><p class="hint">هنستخدمه للرد عليك بالتسعيرة فقط.</p></div>';
			echo '</fieldset>';

			echo '<p class="est__error" role="alert" aria-live="assertive"></p>';
			echo '<div class="est__nav">';
				echo '<button type="button" class="btn btn--ghost" data-prev hidden>' . dg_icon( 'arrow', '', 'flip' ) . '<span>السابق</span></button>';
				echo '<button type="button" class="btn btn--primary" data-next><span>التالي</span>' . dg_icon( 'arrow' ) . '</button>';
				echo '<button type="submit" class="btn btn--wa" data-submit hidden>' . dg_icon( 'whatsapp' ) . '<span>إرسال الطلب على واتساب</span></button>';
			echo '</div>';
		echo '</form>';
	echo '</div>';
	echo '</section>';
}

/* --------------------------------------------- Warning-light guide */
function dg_warning_guide( $limit = 0, $with_head = true ) {
	$lights = dg_warning_lights();
	if ( $limit ) { $lights = array_slice( $lights, 0, $limit, true ); }
	echo '<section class="section wl" id="warning-lights" aria-labelledby="wl-t">';
		echo '<div class="container">';
			if ( $with_head ) {
				echo '<header class="sec-head sec-head--center"><p class="kicker kicker--light"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Interactive Warning Light Guide</p><h2 id="wl-t" class="sec-title">لمبة منورة في الطابلوه؟ اعرف معناها فورًا</h2><p class="sec-sub">اضغط على الرمز اللي ظاهر قدامك — هنقولك الأسباب المحتملة ولازم تقف ولا تكمّل.</p></header>';
			} else {
				echo '<h2 id="wl-t" class="sr-only">رموز لمبات الأعطال</h2>';
			}
			echo '<div class="wl__layout" data-dg-wl>';
				echo '<div class="wl__grid" role="tablist" aria-label="رموز لمبات التحذير">';
				$first = true;
				foreach ( $lights as $k => $l ) {
					echo '<button type="button" role="tab" id="wl-tab-' . esc_attr( $k ) . '" aria-controls="wl-p-' . esc_attr( $k ) . '" aria-selected="' . ( $first ? 'true' : 'false' ) . '" tabindex="' . ( $first ? '0' : '-1' ) . '" class="wl__btn wl__btn--' . esc_attr( $l['level'] ) . '">' . dg_icon( $l['icon'] ) . '<span>' . dg_t( $l['title'] ) . '</span></button>';
					$first = false;
				}
				echo '</div>';
				$first = true;
				foreach ( $lights as $k => $l ) {
					echo '<div class="wl__panel" role="tabpanel" id="wl-p-' . esc_attr( $k ) . '" aria-labelledby="wl-tab-' . esc_attr( $k ) . '"' . ( $first ? '' : ' hidden' ) . '>';
						echo '<div class="wl__panel-head"><span class="wl__big wl__btn--' . esc_attr( $l['level'] ) . '">' . dg_icon( $l['icon'] ) . '</span><div><h3>' . dg_t( $l['title'] ) . '</h3><p class="lvl lvl--' . esc_attr( $l['level'] ) . '">' . dg_icon( 'alert' ) . ( $l['level'] === 'red' ? 'خطورة عالية — توقف بأمان فورًا' : 'تحذير — احجز فحص في أقرب وقت' ) . '</p></div></div>';
						echo '<h4>الأسباب المحتملة</h4><ul class="ticks" role="list">';
						foreach ( $l['causes'] as $c ) { echo '<li>' . dg_icon( 'check' ) . dg_t( $c ) . '</li>'; }
						echo '</ul>';
						echo '<p class="wl__advice">' . dg_t( $l['advice'] ) . '</p>';
						echo '<div class="wl__ctas">';
							echo dg_btn( 'احجز فحص كمبيوتر فوري لهذا العطل', dg_booking_url( array( 'issue' => $k ) ) . '#estimator', 'primary', 'calendar' );
							echo dg_btn( 'اسأل المهندس على واتساب', dg_wa( 'مرحبًا، عندي ' . $l['title'] . ' منورة في الطابلوه' ), 'wa', 'whatsapp' );
						echo '</div>';
					echo '</div>';
					$first = false;
				}
			echo '</div>';
		echo '</div>';
	echo '</section>';
}

/* ------------------------------------------- Maintenance schedules */
function dg_schedules_block( $only = '' ) {
	$s      = dg_schedules();
	$common = $s['_common'];
	unset( $s['_common'] );
	if ( $only && isset( $s[ $only ] ) ) { $s = array( $only => $s[ $only ] ); }
	echo '<section class="section sched" aria-labelledby="sched-t">';
		echo '<div class="container">';
			echo '<header class="sec-head sec-head--center"><p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Digital Maintenance Schedules</p><h2 id="sched-t" class="sec-title">جدول الصيانة الدورية حسب العداد</h2><p class="sec-sub">البنود الوقائية المستحقة عند كل قراءة — عشان عربيتك تفضل بكفاءة المصنع في جو مصر.</p></header>';
			echo '<div data-dg-tabs class="sched__wrap">';
			if ( count( $s ) > 1 ) {
				echo '<div class="tabs" role="tablist" aria-label="اختر المجموعة">';
				$f = true;
				foreach ( $s as $k => $g ) {
					echo '<button type="button" role="tab" id="st-' . esc_attr( $k ) . '" aria-controls="sp-' . esc_attr( $k ) . '" aria-selected="' . ( $f ? 'true' : 'false' ) . '" tabindex="' . ( $f ? '0' : '-1' ) . '">' . esc_html( $g['label'] ) . '</button>';
					$f = false;
				}
				echo '</div>';
			}
			$f = true;
			foreach ( $s as $k => $g ) {
				echo '<div class="sched__panel" role="tabpanel" id="sp-' . esc_attr( $k ) . '"' . ( count( $s ) > 1 ? ' aria-labelledby="st-' . esc_attr( $k ) . '"' : '' ) . ( $f ? '' : ' hidden' ) . '>';
				echo '<ol class="sched__grid" role="list">';
				foreach ( $common as $km => $items ) {
					$all = array_merge( isset( $g['extra'][ $km ] ) ? $g['extra'][ $km ] : array(), $items );
					echo '<li class="sched__card"><p class="sched__km"><span class="latin">' . number_format( $km * 1000 ) . '</span> كم</p><ul role="list">';
					foreach ( $all as $j => $it ) {
						$hl = isset( $g['extra'][ $km ] ) && $j < count( $g['extra'][ $km ] );
						echo '<li' . ( $hl ? ' class="is-special"' : '' ) . '>' . dg_icon( 'check' ) . esc_html( $it ) . '</li>';
					}
					echo '</ul></li>';
				}
				echo '</ol></div>';
				$f = false;
			}
			echo '</div>';
			echo '<p class="note">' . dg_icon( 'alert' ) . 'الجداول إرشادية — الفترات الدقيقة تختلف حسب المحرك وكتيب الصيانة وظروف الاستخدام. نحدد المطلوب لعربيتك بعد الفحص.</p>';
			echo '<p class="center">' . dg_btn( 'احجز الصيانة المستحقة لعربيتك', dg_booking_url( array( 'need' => 'صيانة دورية' ) ), 'primary', 'calendar' ) . '</p>';
		echo '</div>';
	echo '</section>';
}

/* --------------------------------------------------- Reels gallery */
function dg_reels_block( $title = 'من داخل الورشة — فيديوهات حقيقية' ) {
	$reels = dg_reels();
	echo '<section class="section reels" aria-labelledby="reels-t">';
		echo '<div class="container">';
			echo '<header class="sec-head sec-head--split"><div><p class="kicker kicker--light"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Reels &amp; TikTok</p><h2 id="reels-t" class="sec-title">' . esc_html( $title ) . '</h2></div>';
			echo '<div class="reels__follow">' . dg_btn( '@deutsche_garage_', dg_opt( 'instagram', 'https://www.instagram.com/deutsche_garage_/' ), 'ghost-light', 'instagram' ) . dg_btn( '@deutsche_garage', dg_opt( 'tiktok', 'https://www.tiktok.com/@deutsche_garage' ), 'ghost-light', 'tiktok' ) . '</div></header>';
			echo '<ul class="reels__rail" role="list">';
			foreach ( $reels as $r ) {
				echo '<li class="reel">';
					echo '<a class="reel__link" href="' . esc_url( $r['url'] ) . '" target="_blank" rel="noopener" data-dg-reel>';
						echo dg_image( $r['thumb'], $r['title'], 540, 960 );
						echo '<span class="reel__play" aria-hidden="true">' . dg_icon( 'play' ) . '</span>';
						echo '<span class="reel__title">' . esc_html( $r['title'] ) . '<span class="sr-only"> (يفتح الفيديو)</span></span>';
					echo '</a>';
				echo '</li>';
			}
			echo '</ul>';
		echo '</div>';
	echo '</section>';
	dg_reel_modal();
}

function dg_reel_modal() {
	static $done = false;
	if ( $done ) { return; }
	$done = true;
	echo '<dialog class="reel-modal" data-dg-modal aria-label="مشغل الفيديو"><button type="button" class="reel-modal__close" data-close aria-label="إغلاق">' . dg_icon( 'close' ) . '</button><div class="reel-modal__frame"></div></dialog>';
}

/* --------------------------------------------- Case studies (CPT) */
function dg_cases_block( $limit = 3, $title = 'حالات إصلاح من الورشة', $brand = '' ) {
	$args = array( 'post_type' => 'dg_case', 'posts_per_page' => $limit, 'no_found_rows' => true );
	if ( $brand ) { $args['tax_query'] = array( array( 'taxonomy' => 'car_brand', 'field' => 'slug', 'terms' => (array) $brand ) ); }
	$q = new WP_Query( $args );
	if ( ! $q->have_posts() ) { return; }
	echo '<section class="section cases" aria-labelledby="cases-t"><div class="container">';
		echo '<header class="sec-head sec-head--center"><p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Case Studies</p><h2 id="cases-t" class="sec-title">' . esc_html( $title ) . '</h2><p class="sec-sub">المشكلة، التشخيص بالكمبيوتر، والحل الفني — موثّقة من داخل الورشة.</p></header>';
		echo '<ul class="cases__grid" role="list">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$id = get_the_ID();
			$rows = array(
				'المشكلة'  => get_post_meta( $id, 'case_problem', true ),
				'التشخيص' => get_post_meta( $id, 'case_diagnosis', true ),
				'الحل'     => get_post_meta( $id, 'case_solution', true ),
			);
			$video = get_post_meta( $id, 'case_video', true );
			$img = has_post_thumbnail() ? get_the_post_thumbnail_url( $id, 'dg-card' ) : dg_img( 'svc-diagnostics' );
			$terms = get_the_terms( $id, 'car_brand' );
			echo '<li class="case">';
				echo '<div class="case__media">' . dg_image( $img, get_the_title(), 720, 450 );
				if ( $video ) { echo '<a class="case__video" href="' . esc_url( $video ) . '" target="_blank" rel="noopener" data-dg-reel>' . dg_icon( 'play' ) . '<span>شاهد الفيديو</span></a>'; }
				echo '</div>';
				echo '<div class="case__body">';
					if ( $terms && ! is_wp_error( $terms ) ) { echo '<p class="tag">' . esc_html( $terms[0]->name ) . '</p>'; }
					echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
					echo '<dl class="case__dl">';
					foreach ( $rows as $k => $v ) { if ( $v ) { echo '<div><dt>' . esc_html( $k ) . '</dt><dd>' . esc_html( wp_trim_words( $v, 18 ) ) . '</dd></div>'; } }
					echo '</dl>';
				echo '</div>';
			echo '</li>';
		}
		wp_reset_postdata();
		echo '</ul>';
	echo '</div></section>';
	dg_reel_modal();
}

/* ------------------------------------------- Pre-purchase CTA band */
function dg_prepurchase_band() {
	echo '<section class="band band--yellow" aria-labelledby="pp-t"><div class="container band__inner">';
		echo '<div class="band__ico">' . dg_icon( 'search' ) . '</div>';
		echo '<div class="band__text"><h2 id="pp-t">لا تشتري سيارة ألمانية مستعملة قبل فحصها بالكامل في دويتش جراج</h2><p>قياس سمك الدهان، الشاسيه، كبس المحرك والفتيس، وكشف التلاعب بالعداد — بتقرير مكتوب.</p></div>';
		echo '<div class="band__cta">' . dg_btn( 'احجز فحص شراء فوري', dg_page_url( 'pre-purchase' ), 'dark', 'calendar' ) . '</div>';
	echo '</div></section>';
}

/* --------------------------------------------------- FAQ + schema */
function dg_faq_block( $items, $title = 'الأسئلة الشائعة', $id = 'faq' ) {
	if ( ! $items ) { return; }
	echo '<section class="section faq" aria-labelledby="' . esc_attr( $id ) . '-t"><div class="container container--narrow">';
		dg_section_head( 'FAQ', $title );
		echo '<div class="faq__list">';
		$schema = array();
		foreach ( $items as $i => $qa ) {
			echo '<details class="faq__item"' . ( $i === 0 ? ' open' : '' ) . '><summary><span>' . esc_html( $qa[0] ) . '</span>' . dg_icon( 'chevron' ) . '</summary><div class="faq__a"><p>' . esc_html( $qa[1] ) . '</p></div></details>';
			$schema[] = array( '@type' => 'Question', 'name' => $qa[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $qa[1] ) );
		}
		echo '</div>';
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $schema ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
	echo '</div></section>';
}

/** FAQ items from the "faq" post type, else defaults. */
function dg_faq_items() {
	$posts = get_posts( array( 'post_type' => 'faq', 'posts_per_page' => 20, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
	if ( ! $posts ) { return dg_default_faq(); }
	$out = array();
	foreach ( $posts as $p ) { $out[] = array( $p->post_title, wp_strip_all_tags( $p->post_content ) ); }
	return $out;
}

/* --------------------------------------------------- Latest posts */
function dg_post_card( $p = null ) {
	$p   = get_post( $p );
	$img = has_post_thumbnail( $p ) ? get_the_post_thumbnail_url( $p, 'dg-card' ) : dg_img( 'svc-maintenance' );
	$cat = get_the_category( $p->ID );
	echo '<li class="post-card">';
		echo '<a class="post-card__media" href="' . esc_url( get_permalink( $p ) ) . '" tabindex="-1" aria-hidden="true">' . dg_image( $img, '', 720, 450 ) . '</a>';
		echo '<div class="post-card__body">';
			if ( $cat ) { echo '<p class="tag">' . esc_html( $cat[0]->name ) . '</p>'; }
			echo '<h3><a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a></h3>';
			echo '<p>' . esc_html( wp_trim_words( get_the_excerpt( $p ), 22 ) ) . '</p>';
			echo '<p class="post-card__meta">' . dg_icon( 'calendar' ) . '<time datetime="' . esc_attr( get_the_date( 'c', $p ) ) . '">' . esc_html( get_the_date( '', $p ) ) . '</time></p>';
		echo '</div>';
	echo '</li>';
}

function dg_latest_posts( $n = 3 ) {
	$posts = get_posts( array( 'posts_per_page' => $n, 'post_type' => 'post' ) );
	if ( ! $posts ) { return; }
	echo '<section class="section blog-latest" aria-labelledby="bl-t"><div class="container">';
		echo '<header class="sec-head sec-head--split"><div><p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Auto Care Blog</p><h2 id="bl-t" class="sec-title">دليلك الهندسي للحفاظ على سيارتك الألمانية</h2></div>' . dg_btn( 'كل المقالات', dg_page_url( 'blog' ), 'ghost', 'arrow' ) . '</header>';
		echo '<ul class="posts-grid" role="list">';
		foreach ( $posts as $p ) { dg_post_card( $p ); }
		echo '</ul>';
	echo '</div></section>';
}

/* ------------------------------------------------- Map + contact */
function dg_map_block() {
	$embed = get_option( 'map' );
	echo '<section class="section visit" id="visit" aria-labelledby="visit-t"><div class="container visit__grid">';
		echo '<div class="visit__info">';
			echo '<p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Visit Us</p>';
			echo '<h2 id="visit-t" class="sec-title">زورنا في سيدي جابر</h2>';
			echo '<ul class="contact-list" role="list">';
				echo '<li>' . dg_icon( 'pin' ) . '<div><b>العنوان</b><a href="' . esc_url( dg_map_url() ) . '" target="_blank" rel="noopener">' . esc_html( dg_address() ) . '</a></div></li>';
				echo '<li>' . dg_icon( 'phone' ) . '<div><b>اتصال</b><a class="latin" dir="ltr" href="' . esc_attr( dg_tel() ) . '">' . esc_html( dg_phone() ) . '</a>' . ( dg_phone2() ? ' — <a class="latin" dir="ltr" href="' . esc_attr( dg_tel( dg_phone2() ) ) . '">' . esc_html( dg_phone2() ) . '</a>' : '' ) . '</div></li>';
				echo '<li>' . dg_icon( 'whatsapp' ) . '<div><b>واتساب</b><a class="latin" dir="ltr" href="' . esc_url( dg_wa() ) . '" target="_blank" rel="noopener">' . esc_html( dg_wa_num() ) . '</a></div></li>';
				$hours = dg_opt( 'work_hours' );
				if ( $hours ) {
					echo '<li>' . dg_icon( 'clock' ) . '<div><b>مواعيد العمل</b>';
					foreach ( preg_split( '/\r?\n/', $hours ) as $line ) {
						$parts = array_map( 'trim', explode( '=', $line, 2 ) );
						if ( $parts[0] === '' ) { continue; }
						echo '<span class="hours-row"><span>' . esc_html( $parts[0] ) . '</span><span>' . esc_html( isset( $parts[1] ) ? $parts[1] : '' ) . '</span></span>';
					}
					echo '</div></li>';
				}
			echo '</ul>';
			echo '<div class="visit__ctas">' . dg_btn( 'افتح الاتجاهات على الخريطة', dg_map_url(), 'primary', 'pin' ) . dg_btn( 'اتصل الآن', dg_tel(), 'ghost', 'phone' ) . '</div>';
		echo '</div>';
		echo '<div class="visit__map">';
		if ( $embed && strpos( $embed, '<iframe' ) !== false ) {
			// Lazy: iframe only loads when the user asks for it (keeps the page under 2s on mobile).
			echo '<div class="map-lazy" data-dg-map><template>' . wp_kses( $embed, array( 'iframe' => array( 'src' => true, 'width' => true, 'height' => true, 'style' => true, 'allowfullscreen' => true, 'loading' => true, 'referrerpolicy' => true, 'title' => true ) ) ) . '</template>';
			echo '<button type="button" class="map-lazy__btn">' . dg_icon( 'pin' ) . '<span>عرض الخريطة التفاعلية</span></button></div>';
		} else {
			echo '<a class="map-card" href="' . esc_url( dg_map_url() ) . '" target="_blank" rel="noopener"><span class="map-card__grid" aria-hidden="true"></span><span class="map-card__pin">' . dg_icon( 'pin' ) . '</span><span class="map-card__label"><b>دويتش جراج</b>16 خلف بورسعيد — سيدي جابر<span class="map-card__open">افتح في خرائط جوجل ' . dg_icon( 'arrow' ) . '</span></span></a>';
		}
		echo '</div>';
	echo '</div></section>';
}

/* ------------------------------------------------------ CTA band */
function dg_cta_band( $title = 'عربيتك تستاهل دقة ألمانية', $text = 'احجز موعدك دلوقتي أو كلّم المهندس مباشرة — هنرد عليك بالتشخيص المبدئي والتكلفة.' ) {
	echo '<section class="band band--dark" aria-label="احجز الآن"><div class="container band__inner">';
		echo '<div class="band__text"><h2>' . esc_html( $title ) . '</h2><p>' . esc_html( $text ) . '</p></div>';
		echo '<div class="band__cta">' . dg_btn( 'احجز موعد صيانتك الآن', dg_booking_url(), 'primary', 'calendar' ) . dg_btn( 'واتساب', dg_wa( 'مرحبًا دويتش جراج، عايز أحجز موعد' ), 'wa', 'whatsapp' ) . '</div>';
	echo '</div></section>';
}

/* ------------------------------------------------- Brand hub page */
function dg_render_hub( $key ) {
	$hubs = dg_hubs();
	if ( ! isset( $hubs[ $key ] ) ) { return; }
	$h = $hubs[ $key ];
	global $post;
	$title = $post && $post->post_title ? $post->post_title : $h['h1'];
	echo '<main id="main" class="hub">';
		dg_page_hero( $h['kicker'], $h['h1'], $h['lead'], dg_img( $h['slot'] ),
			dg_btn( $h['cta'], dg_booking_url( array( 'brand' => $h['brand'] ) ) . '#estimator', 'primary', 'calendar' ) .
			dg_btn( 'واتساب المهندس', dg_wa( 'مرحبًا، عندي ' . $h['kicker'] . ' ومحتاج استشارة' ), 'wa', 'whatsapp' ) );

		echo '<section class="section"><div class="container">';
			echo '<header class="sec-head sec-head--split"><div><p class="kicker"><span class="kicker__bars" aria-hidden="true"><i></i><i></i><i></i></span>Specialised Services</p><h2 class="sec-title">' . esc_html( $title ) . '</h2></div><p class="sec-sub">تشخيص بجهاز <b class="latin">' . esc_html( $h['tool'] ) . '</b> وقطع أصلية OEM، وتقرير مكتوب قبل وبعد الإصلاح.</p></header>';
			echo '<ul class="feat__grid" role="list">';
			foreach ( $h['items'] as $it ) {
				echo '<li class="feat"><span class="feat__ico">' . dg_icon( $it[0] ) . '</span><h3>' . esc_html( $it[1] ) . '</h3><p>' . esc_html( $it[2] ) . '</p></li>';
			}
			echo '</ul>';
			echo '<div class="models"><h3>موديلات نتعامل معها يوميًا</h3><ul role="list">';
			foreach ( $h['models'] as $m ) { echo '<li class="latin">' . esc_html( $m ) . '</li>'; }
			echo '</ul></div>';
			if ( $post && trim( $post->post_content ) ) {
				echo '<div class="prose">' . apply_filters( 'the_content', $post->post_content ) . '</div>';
			}
		echo '</div></section>';

		dg_schedules_block( $key );
		dg_cases_block( 3, 'حالات إصلاح ' . $h['kicker'] );
		dg_estimator();
		dg_faq_block( $h['faq'], 'أسئلة ملاك ' . $h['kicker'] );
		dg_cta_band();
	echo '</main>';
}
