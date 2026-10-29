<?php
/**
 * نموذج صفحة «طلب الخدمة».
 * اختره من: تحرير الصفحة ← خصائص ← النموذج ← Service request.
 *
 * النموذج يرسل إلى نظام الطلبات الموجود (AjaxCenter/sendinfo → «طلبات العملاء»)
 * عبر نفس معالج .form-contact في setup.js — بلا أي كود إرسال جديد.
 * رابط مسبق الاختيار: ‎/طلب-الخدمة/?service=اسم-القسم&city=اسم-المدينة
 */

$title    = get_the_title( $post->ID );
$content  = apply_filters( 'the_content', $post->post_content );
$Phone    = trim( (string) get_option( 'Phone' ) );
$Whatsapp = preg_replace( '/[^0-9]/', '', (string) get_option( 'Whatsapp' ) );
$email    = trim( (string) ( get_option( 'Email' ) ? get_option( 'Email' ) : get_option( 'email' ) ) );
$address  = trim( (string) get_option( 'Adress' ) );
$hours    = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) get_option( 'working_hours' ) ) ) );

// الخدمات = الأقسام الرئيسية (بدون القسم الافتراضي «غير مصنف»)
$services = get_terms( array(
	'taxonomy'   => 'category',
	'hide_empty' => false,
	'parent'     => 0,
	'exclude'    => array( (int) get_option( 'default_category' ) ),
) );
$cities = get_terms( array( 'taxonomy' => 'country', 'hide_empty' => false ) );

// اختيار مسبق من الرابط
$want_service = isset( $_GET['service'] ) ? sanitize_text_field( wp_unslash( $_GET['service'] ) ) : '';
$want_city    = isset( $_GET['city'] ) ? sanitize_text_field( wp_unslash( $_GET['city'] ) ) : '';
$matches = function ( $term, $want ) {
	if ( '' === $want ) {
		return false;
	}
	$want = str_replace( '-', ' ', urldecode( $want ) );
	return $term->name === $want || urldecode( $term->slug ) === str_replace( ' ', '-', $want ) || (string) $term->term_id === $want;
};

$icon_phone = '<svg viewBox="0 0 512 512" aria-hidden="true"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>';
$icon_wa    = '<svg viewBox="0 0 448 512" aria-hidden="true"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zM223.9 438.7c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6z"/></svg>';

echo '<div class="sr-page" dir="rtl">';

	/* الرأس */
	echo '<section class="sr-hero">';
		echo '<div class="container">';
			echo '<breadcrumb class="sr-breadcrumb">';
				Breadcrumb();
			echo '</breadcrumb>';
			echo '<h1>' . esc_html( $title ) . '</h1>';
			echo '<ol class="sr-steps" aria-label="خطوات الطلب">';
				echo '<li><span class="sr-num" dir="ltr">1</span>املأ النموذج</li>';
				echo '<li><span class="sr-num" dir="ltr">2</span>نتواصل معك لتأكيد الموعد والسعر</li>';
				echo '<li><span class="sr-num" dir="ltr">3</span>يصلك الفريق وينفّذ الخدمة</li>';
			echo '</ol>';
		echo '</div>';
	echo '</section>';

	echo '<div class="container">';
		echo '<div class="sr-grid">';

			/* النموذج */
			echo '<div class="sr-card sr-form">';
				echo '<h2>بيانات الطلب</h2>';
				echo '<p class="sr-hint">الحقول المعلَّمة بـ <span aria-hidden="true">*</span> مطلوبة. نتواصل معك على رقم الجوال.</p>';
				echo '<div class="form-contact">';
					echo '<form method="post">';
						echo '<div class="sr-row">';
							echo '<label class="sr-field"><span>الاسم <em>*</em></span><input type="text" name="names" required autocomplete="name"></label>';
							echo '<label class="sr-field"><span>رقم الجوال <em>*</em></span><input type="tel" name="phone" required inputmode="tel" autocomplete="tel" pattern="[0-9+\s]{8,15}" dir="ltr" placeholder="05xxxxxxxx"></label>';
						echo '</div>';
						echo '<div class="sr-row">';
							echo '<label class="sr-field"><span>الخدمة المطلوبة</span><select name="service">';
								echo '<option value="">اختر الخدمة</option>';
								foreach ( is_array( $services ) ? $services : array() as $s ) {
									echo '<option value="' . esc_attr( $s->name ) . '"' . ( $matches( $s, $want_service ) ? ' selected' : '' ) . '>' . esc_html( $s->name ) . '</option>';
								}
							echo '</select></label>';
							echo '<label class="sr-field"><span>المدينة</span><select name="city">';
								echo '<option value="">اختر المدينة</option>';
								foreach ( is_array( $cities ) ? $cities : array() as $c ) {
									echo '<option value="' . esc_attr( $c->name ) . '"' . ( $matches( $c, $want_city ) ? ' selected' : '' ) . '>' . esc_html( $c->name ) . '</option>';
								}
							echo '</select></label>';
						echo '</div>';
						echo '<label class="sr-field"><span>الحي أو العنوان</span><input type="text" name="address" autocomplete="street-address"></label>';
						echo '<label class="sr-field"><span>تفاصيل الطلب</span><textarea name="message" rows="4" placeholder="مثال: صراصير في المطبخ، شقة 3 غرف"></textarea></label>';
						echo '<button type="submit" class="sr-btn sr-btn--primary">إرسال الطلب</button>';
					echo '</form>';
				echo '</div>';
				if ( '' !== $Whatsapp ) {
					echo '<a class="sr-btn sr-btn--wa" href="https://wa.me/' . esc_attr( $Whatsapp ) . '" target="_blank" rel="nofollow noopener" data-call="whatsapp">' . $icon_wa . '<span>أو اطلب مباشرة عبر واتساب</span></a>';
				}
			echo '</div>';

			/* التواصل المباشر */
			echo '<aside class="sr-side">';
				echo '<div class="sr-card sr-contact">';
					echo '<h2>تواصل مباشرة</h2>';
					if ( '' !== $Phone ) {
						echo '<a class="sr-line" href="tel:' . esc_attr( $Phone ) . '" data-call="Phone"><span class="sr-ico">' . $icon_phone . '</span><span><small>اتصل بنا</small><b dir="ltr">' . esc_html( $Phone ) . '</b></span></a>';
					}
					if ( '' !== $Whatsapp ) {
						echo '<a class="sr-line" href="https://wa.me/' . esc_attr( $Whatsapp ) . '" target="_blank" rel="nofollow noopener" data-call="whatsapp"><span class="sr-ico sr-ico--wa">' . $icon_wa . '</span><span><small>واتساب</small><b dir="ltr">' . esc_html( get_option( 'Whatsapp' ) ) . '</b></span></a>';
					}
					if ( '' !== $email ) {
						echo '<a class="sr-line" href="mailto:' . esc_attr( $email ) . '"><span class="sr-ico"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span><span><small>البريد الإلكتروني</small><b>' . esc_html( $email ) . '</b></span></a>';
					}
					if ( '' !== $address ) {
						echo '<div class="sr-line"><span class="sr-ico"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span><span><small>العنوان</small><b>' . esc_html( $address ) . '</b></span></div>';
					}
				echo '</div>';
				if ( $hours ) {
					echo '<div class="sr-card sr-hours">';
						echo '<h2><i class="fa-regular fa-clock" aria-hidden="true"></i>مواعيد العمل</h2>';
						echo '<ul>';
						foreach ( $hours as $h ) {
							echo '<li>' . esc_html( $h ) . '</li>';
						}
						echo '</ul>';
					echo '</div>';
				}
			echo '</aside>';

		echo '</div>';

		if ( '' !== trim( wp_strip_all_tags( $content ) ) ) {
			echo '<div class="sr-card sr-content ArticleDetails">' . $content . '</div>';
		}
	echo '</div>';

echo '</div>';
