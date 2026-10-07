<?php
/**
 * نموذج الصفحة: طلب خدمة — يُرسل الطلب عبر واتساب.
 * الإعدادات من «الحاسبة والعروض ← صفحة طلب الخدمة».
 */
if ( ! function_exists( 'yc_order_data' ) ) {
    return;
}
global $post;
$d        = yc_order_data();
$wa       = function_exists( 'yc_pt_wa_number' ) ? yc_pt_wa_number( $d['wa'] ) : preg_replace( '/\D/', '', (string) $d['wa'] );
$services = yc_order_lines( $d['services'] );
$cities   = yc_order_lines( $d['cities'] );
if ( ! $cities ) {
    $terms = get_terms( array( 'taxonomy' => 'country', 'hide_empty' => false ) );
    if ( ! is_wp_error( $terms ) ) {
        $cities = wp_list_pluck( $terms, 'name' );
    }
}
$uid = 'yco' . substr( md5( uniqid( '', true ) ), 0, 6 );

$ic = array(
    'user'   => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'phone'  => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.37 1.9.72 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.35 1.85.59 2.81.72A2 2 0 0 1 22 16.92z"/>',
    'star'   => '<path d="M12 2l2.39 7.36H22l-6.2 4.51L18.18 22 12 17.27 5.82 22l2.38-8.13L2 9.36h7.61z"/>',
    'pin'    => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
    'target' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/>',
    'clip'   => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>',
    'cal'    => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
    'clock'  => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    'note'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>',
    'check'  => '<path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>',
);
$svg = function ( $k ) use ( $ic ) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $ic[ $k ] . '</svg>';
};
$feat_icons = array(
    '<path d="M12 2L4 6v6c0 5 3.5 9.5 8 10 4.5-.5 8-5 8-10V6l-8-4z"/><path d="M9 12l2 2 4-4"/>',
    '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    '<path d="M20 7L9 18l-5-5"/>',
    '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>',
);

$css = get_template_directory() . '/components/packs/OrderPage/order.css';
if ( file_exists( $css ) ) {
    echo '<style>' . file_get_contents( $css ) . '</style>';
}

echo '<section class="yc-order" id="' . esc_attr( $uid ) . '" data-wa="' . esc_attr( $wa ) . '" data-url="' . esc_attr( get_permalink( $post ) ) . '">';

  /* ---------- الرأس ---------- */
  echo '<div class="yc-order__hero"><div class="container">';
    echo '<div class="yc-order__crumb"><breadcrumb>';
      Breadcrumb();
    echo '</breadcrumb></div>';
    echo '<h1>' . esc_html( get_the_title( $post ) ) . '</h1>';
    $content = trim( (string) $post->post_content );
    if ( '' !== $content ) {
      echo '<div class="yc-order__lead">' . apply_filters( 'the_content', $content ) . '</div>';
    }
  echo '</div></div>';

  /* ---------- النموذج ---------- */
  echo '<div class="container"><div class="yc-order__card">';

    echo '<div class="yc-order__head">';
      if ( '' !== $d['badge'] ) {
        echo '<span class="yc-order__badge">' . $svg( 'check' ) . esc_html( $d['badge'] ) . '</span>';
      }
      if ( '' !== $d['title'] ) {
        echo '<h2>' . esc_html( $d['title'] ) . '</h2>';
      }
      if ( '' !== $d['subtitle'] ) {
        echo '<p>' . esc_html( $d['subtitle'] ) . '</p>';
      }
    echo '</div>';

    echo '<form class="yc-order__form" novalidate>';

      echo '<div class="yc-order__row">';
        echo '<div class="yc-order__field">';
          echo '<label for="' . $uid . '-name">' . $svg( 'user' ) . 'الاسم الكامل</label>';
          echo '<input type="text" id="' . $uid . '-name" name="name" placeholder="مثال: محمد أحمد" autocomplete="name" aria-describedby="' . $uid . '-e-name">';
          echo '<span class="yc-order__err" id="' . $uid . '-e-name" role="alert" hidden></span>';
        echo '</div>';
        echo '<div class="yc-order__field">';
          echo '<label for="' . $uid . '-phone">' . $svg( 'phone' ) . 'رقم الجوال</label>';
          echo '<input type="tel" id="' . $uid . '-phone" name="phone" placeholder="05xxxxxxxx" autocomplete="tel" inputmode="tel" dir="ltr" aria-describedby="' . $uid . '-e-phone">';
          echo '<span class="yc-order__err" id="' . $uid . '-e-phone" role="alert" hidden></span>';
        echo '</div>';
      echo '</div>';

      if ( $services ) {
        echo '<fieldset class="yc-order__field" data-group="service">';
          echo '<legend>' . $svg( 'star' ) . 'الخدمة المطلوبة</legend>';
          echo '<div class="yc-order__chips" role="radiogroup" aria-describedby="' . $uid . '-e-service">';
          foreach ( $services as $s ) {
            echo '<button type="button" class="yc-order__chip" role="radio" aria-checked="false" data-value="' . esc_attr( $s ) . '">' . esc_html( $s ) . '</button>';
          }
          echo '</div>';
          echo '<span class="yc-order__err" id="' . $uid . '-e-service" role="alert" hidden></span>';
        echo '</fieldset>';
      }

      if ( $cities ) {
        echo '<fieldset class="yc-order__field" data-group="city">';
          echo '<legend>' . $svg( 'pin' ) . 'المدينة</legend>';
          echo '<div class="yc-order__chips" role="radiogroup" aria-describedby="' . $uid . '-e-city">';
          foreach ( $cities as $c ) {
            echo '<button type="button" class="yc-order__chip" role="radio" aria-checked="false" data-value="' . esc_attr( $c ) . '">' . esc_html( $c ) . '</button>';
          }
          echo '</div>';
          echo '<span class="yc-order__err" id="' . $uid . '-e-city" role="alert" hidden></span>';
        echo '</fieldset>';
      }

      echo '<div class="yc-order__field">';
        echo '<label for="' . $uid . '-loc">' . $svg( 'target' ) . 'الموقع التفصيلي <small>(اختياري — يساعد الفني للوصول بسرعة)</small></label>';
        echo '<div class="yc-order__locbar">';
          echo '<input type="text" id="' . $uid . '-loc" name="location" placeholder="رابط Google Maps أو وصف العنوان">';
          echo '<button type="button" class="yc-order__mini yc-order__locate">' . $svg( 'target' ) . 'حدّد موقعي</button>';
          echo '<button type="button" class="yc-order__mini yc-order__paste">' . $svg( 'clip' ) . 'لصق</button>';
        echo '</div>';
        echo '<p class="yc-order__help" role="status" hidden></p>';
      echo '</div>';

      echo '<div class="yc-order__row">';
        echo '<div class="yc-order__field">';
          echo '<label for="' . $uid . '-date">' . $svg( 'cal' ) . 'تاريخ التنفيذ المفضل <small>(اختياري)</small></label>';
          echo '<input type="date" id="' . $uid . '-date" name="date" min="' . esc_attr( current_time( 'Y-m-d' ) ) . '">';
        echo '</div>';
        echo '<div class="yc-order__field">';
          echo '<label for="' . $uid . '-time">' . $svg( 'clock' ) . 'الوقت المفضل <small>(اختياري)</small></label>';
          echo '<select id="' . $uid . '-time" name="time"><option value="">اختر الوقت</option><option>صباحاً</option><option>ظهراً</option><option>مساءً</option></select>';
        echo '</div>';
      echo '</div>';

      echo '<div class="yc-order__field">';
        echo '<label for="' . $uid . '-notes">' . $svg( 'note' ) . 'ملاحظات إضافية <small>(اختياري)</small></label>';
        echo '<textarea id="' . $uid . '-notes" name="notes" rows="4" placeholder="مثال: عدد الخزانات، مساحة المنزل، تفاصيل أخرى..."></textarea>';
      echo '</div>';

      // رابط واتساب حقيقي (لا زر submit) حتى يُحتسب في تتبع المكالمات تلقائيًا
      if ( $wa ) {
        echo '<a class="yc-order__submit" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener">'
          . ( function_exists( 'yc_pt_wa_svg' ) ? yc_pt_wa_svg() : '' ) . 'إرسال الطلب عبر واتساب</a>';
      }
      echo '<p class="yc-order__ok" role="status" hidden>' . $svg( 'check' ) . 'تم تجهيز طلبك! يتم تحويلك إلى واتساب الآن...</p>';

    echo '</form>';
  echo '</div></div>';

  /* ---------- لماذا تختارنا ---------- */
  $features = array();
  foreach ( yc_order_lines( $d['features'] ) as $line ) {
    $p          = preg_split( '/\s*\|\s*/u', $line, 2 );
    $features[] = array( trim( $p[0] ), isset( $p[1] ) ? trim( $p[1] ) : '' );
  }
  if ( $features || '' !== $d['why_title'] ) {
    echo '<div class="yc-order__why"><div class="container">';
      if ( '' !== $d['why_title'] ) {
        echo '<h2>' . esc_html( $d['why_title'] ) . '</h2>';
      }
      if ( '' !== $d['why_text'] ) {
        echo '<p class="yc-order__whytext">' . esc_html( $d['why_text'] ) . '</p>';
      }
      if ( $features ) {
        echo '<div class="yc-order__feats">';
        foreach ( $features as $i => $f ) {
          echo '<article class="yc-order__feat">';
            echo '<span class="yc-order__featic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $feat_icons[ $i % count( $feat_icons ) ] . '</svg></span>';
            echo '<h3>' . esc_html( $f[0] ) . '</h3>';
            if ( '' !== $f[1] ) {
              echo '<p>' . esc_html( $f[1] ) . '</p>';
            }
          echo '</article>';
        }
        echo '</div>';
      }
    echo '</div></div>';
  }

echo '</section>';

/* ---------- البيانات المنظّمة ---------- */
if ( $wa ) {
    echo '<script type="application/ld+json">' . wp_json_encode( array(
        '@context'        => 'https://schema.org',
        '@type'           => 'ContactPage',
        'url'             => get_permalink( $post ),
        'name'            => get_the_title( $post ),
        'inLanguage'      => 'ar',
        'potentialAction' => array(
            '@type'  => 'CommunicateAction',
            'name'   => 'إرسال طلب عبر واتساب',
            'target' => 'https://wa.me/' . $wa,
        ),
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>';
}

$js = get_template_directory() . '/components/packs/OrderPage/order.js';
if ( file_exists( $js ) ) {
    echo '<script>' . file_get_contents( $js ) . '</script>';
}
