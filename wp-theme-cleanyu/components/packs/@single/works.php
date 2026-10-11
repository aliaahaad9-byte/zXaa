<?php
/**
 * صفحة مشروع واحد (سابقة أعمال) — أربعة أقسام:
 * 1) تفاصيل المشروع الأساسية  2) التحدي قبل التنفيذ
 * 3) خطة العمل أثناء التنفيذ  4) النتيجة بعد التنفيذ
 * البيانات من صندوق «تفاصيل المشروع» (حزمة Projects).
 */
global $post;
if ( ! function_exists( 'yc_pr_sections' ) ) {
    echo '<div class="container"><h1>' . esc_html( get_the_title( $post ) ) . '</h1>';
    the_content();
    echo '</div>';
    return;
}

$pid   = $post->ID;
$m     = function ( $k ) use ( $pid ) {
    return trim( (string) get_post_meta( $pid, $k, true ) );
};
$svc   = yc_pr_service_term( $pid );
$svc_url = $svc ? get_term_link( $svc ) : '';
if ( is_wp_error( $svc_url ) ) {
    $svc_url = '';
}
$hero  = yc_pr_hero( $pid, 'full' );
$title = get_the_title( $pid );

$css = get_template_directory() . '/components/packs/Projects/projects.css';
if ( file_exists( $css ) ) {
    echo '<style>' . file_get_contents( $css ) . '</style>';
}

/** معرض صور مرحلة. */
$gallery = function ( $key, $label ) use ( $pid, $title ) {
    $ids = yc_pr_ids( get_post_meta( $pid, $key, true ) );
    if ( ! $ids ) {
        return '';
    }
    $h = '<div class="yc-prj__gal yc-prj__gal--' . min( count( $ids ), 3 ) . '">';
    foreach ( $ids as $i => $id ) {
        $full = wp_get_attachment_image_url( $id, 'full' );
        $src  = wp_get_attachment_image_url( $id, 'large' );
        if ( ! $src ) {
            continue;
        }
        $alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
        $alt = '' !== $alt ? $alt : $title . ' — ' . $label . ( count( $ids ) > 1 ? ' ' . ( $i + 1 ) : '' );
        $h  .= '<figure><a href="' . esc_url( $full ) . '" target="_blank" rel="noopener"><img src="' . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy"></a></figure>';
    }
    return $h . '</div>';
};

echo '<article class="yc-prj">';
echo '<div class="container">';

  echo '<div class="yc-prj__crumb"><breadcrumb>';
    Breadcrumb( $post );
  echo '</breadcrumb></div>';

  /* ---------- العنوان ---------- */
  echo '<div class="yc-prj__head">';
    if ( $svc ) {
      echo '<a class="yc-prj__svc" href="' . esc_url( $svc_url ) . '">' . esc_html( $svc->name ) . '</a>';
    }
    echo '<h1>' . esc_html( $title ) . '</h1>';
    $sub = array_filter( array( $m( 'yc_pr_city' ) . ( $m( 'yc_pr_district' ) ? ' — ' . $m( 'yc_pr_district' ) : '' ), $m( 'yc_pr_duration' ) ) );
    if ( $sub ) {
      echo '<p class="yc-prj__sub">' . esc_html( implode( ' · ', $sub ) ) . '</p>';
    }
  echo '</div>';

  if ( $hero ) {
    echo '<figure class="yc-prj__hero"><img src="' . esc_url( $hero ) . '" alt="' . esc_attr( $title ) . '" fetchpriority="high"></figure>';
  }

  /* ---------- 1) التفاصيل الأساسية ---------- */
  $facts = array(
    array( 'الخدمة', $svc ? $svc->name : '' ),
    array( 'طبيعة المنشأة', $m( 'yc_pr_facility' ) ),
    array( 'المدينة', $m( 'yc_pr_city' ) . ( $m( 'yc_pr_district' ) ? ' — ' . $m( 'yc_pr_district' ) : '' ) ),
    array( 'مدة التنفيذ', $m( 'yc_pr_duration' ) ),
    array( 'فريق العمل', $m( 'yc_pr_team' ) ),
    array( 'المساحة / الحجم', $m( 'yc_pr_area' ) ),
  );
  $facts = array_filter( $facts, function ( $f ) { return '' !== trim( $f[1] ); } );
  if ( $facts ) {
    echo '<section class="yc-prj__sec yc-prj__sec--info">';
      echo '<h2><span>1</span>تفاصيل المشروع الأساسية</h2>';
      echo '<dl class="yc-prj__facts">';
      foreach ( $facts as $f ) {
        echo '<div><dt>' . esc_html( $f[0] ) . '</dt><dd>' . esc_html( $f[1] ) . '</dd></div>';
      }
      echo '</dl>';
    echo '</section>';
  }

  /* ---------- 2–4) قبل / أثناء / بعد ---------- */
  $n   = 2;
  $any = false;
  foreach ( yc_pr_sections() as $key => $s ) {
    $paras = yc_pr_paras( get_post_meta( $pid, $s[0], true ) );
    $gal   = $gallery( $s[2], $s[3] );
    if ( ! $paras && '' === $gal ) {
      $n++;
      continue;
    }
    $any = true;
    echo '<section class="yc-prj__sec yc-prj__sec--' . esc_attr( $key ) . '">';
      echo '<h2><span>' . $n . '</span>' . esc_html( $s[1] ) . '</h2>';
      foreach ( $paras as $p ) {
        echo '<p>' . nl2br( esc_html( $p ) ) . '</p>';
      }
      echo $gal; // phpcs:ignore -- مُهرَّب داخل الدالة
    echo '</section>';
    $n++;
  }

  /* محتوى المحرر — للمشاريع القديمة أو أي إضافة */
  $content = trim( (string) $post->post_content );
  if ( '' !== $content ) {
    echo '<div class="yc-prj__content ArticleDetails details">';
      the_content();
    echo '</div>';
  }
  /* جدول «نظرة عامة» القديم */
  $steps = get_post_meta( $pid, 'works_steps', true );
  if ( ! $any && is_array( $steps ) && $steps ) {
    echo '<section class="yc-prj__sec yc-prj__sec--info"><h2><span>1</span>نظرة عامة</h2><dl class="yc-prj__facts">';
    foreach ( $steps as $st ) {
      if ( ! empty( $st['title_right'] ) ) {
        echo '<div><dt>' . esc_html( $st['title_right'] ) . '</dt><dd>' . esc_html( isset( $st['title_left'] ) ? $st['title_left'] : '' ) . '</dd></div>';
      }
    }
    echo '</dl></section>';
  }

  /* ---------- رأي العميل ---------- */
  if ( '' !== $m( 'yc_pr_quote' ) ) {
    echo '<blockquote class="yc-prj__quote"><p>' . esc_html( $m( 'yc_pr_quote' ) ) . '</p>';
    if ( '' !== $m( 'yc_pr_quote_by' ) ) {
      echo '<cite>' . esc_html( $m( 'yc_pr_quote_by' ) ) . '</cite>';
    }
    echo '</blockquote>';
  }

  /* ---------- روابط الخدمة ---------- */
  $links = yc_pr_links( get_post_meta( $pid, 'yc_pr_links', true ) );
  if ( $svc || $links ) {
    echo '<div class="yc-prj__links"><b>خدمات ذات صلة:</b> ';
    $out = array();
    if ( $svc ) {
      $out[] = '<a href="' . esc_url( $svc_url ) . '">' . esc_html( $svc->name ) . '</a>';
    }
    foreach ( $links as $l ) {
      $out[] = '<a href="' . esc_url( $l['url'] ) . '">' . esc_html( $l['text'] ) . '</a>';
    }
    echo implode( '<span aria-hidden="true"> · </span>', $out ); // phpcs:ignore
    echo '</div>';
  }

  /* ---------- دعوة للحجز ---------- */
  $wa    = function_exists( 'yc_pt_wa_number' ) ? yc_pt_wa_number() : preg_replace( '/\D/', '', (string) get_option( 'Whatsapp' ) );
  $phone = preg_replace( '/[^\d+]/', '', (string) get_option( 'Phone' ) );
  if ( $wa || $phone ) {
    echo '<div class="yc-prj__cta"><div><h2>تحتاج خدمة مشابهة؟</h2><p>أرسل لنا تفاصيل موقعك وسنعطيك تقييمًا مجانيًا وخطة عمل واضحة.</p></div><div class="yc-prj__btns">';
    if ( $wa ) {
      $msg = 'مرحبًا، اطّلعت على مشروع «' . $title . '» وأرغب في خدمة مشابهة.' . "\n" . get_permalink( $pid );
      echo '<a class="yc-prj__btn yc-prj__btn--wa" href="https://wa.me/' . esc_attr( $wa ) . '?text=' . rawurlencode( $msg ) . '" target="_blank" rel="noopener">' . ( function_exists( 'yc_pt_wa_svg' ) ? yc_pt_wa_svg() : '' ) . 'اطلب عبر واتساب</a>';
    }
    if ( $phone ) {
      echo '<a class="yc-prj__btn yc-prj__btn--call" href="tel:' . esc_attr( $phone ) . '">' . ( function_exists( 'yc_pt_call_svg' ) ? yc_pt_call_svg() : '' ) . 'اتصل الآن</a>';
    }
    echo '</div></div>';
  }

  /* ---------- مشاريع مشابهة ---------- */
  $args = array( 'post_type' => 'works', 'posts_per_page' => 3, 'post__not_in' => array( $pid ), 'no_found_rows' => true );
  if ( $svc ) {
    $args['cat'] = $svc->term_id;
  }
  $related = get_posts( $args );
  if ( $related ) {
    echo '<section class="yc-prj__related"><h2>مشاريع مشابهة</h2><div class="yc-prg">';
    foreach ( $related as $r ) {
      echo yc_pr_card( $r ); // phpcs:ignore
    }
    echo '</div></section>';
  }

echo '</div>';
echo '</article>';

/* ---------- البيانات المنظّمة ---------- */
$schema = array(
    '@context'      => 'https://schema.org',
    '@type'         => 'Article',
    'headline'      => $title,
    'datePublished' => get_post_time( 'c', true, $post ),
    'dateModified'  => get_post_modified_time( 'c', true, $post ),
    'mainEntityOfPage' => get_permalink( $pid ),
    'publisher'     => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ),
    'author'        => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ),
);
if ( $hero ) {
    $schema['image'] = $hero;
}
$first = yc_pr_paras( get_post_meta( $pid, 'yc_pr_challenge', true ) );
if ( $first ) {
    $schema['description'] = wp_trim_words( $first[0], 40, '…' );
}
if ( $svc ) {
    $schema['about'] = array( '@type' => 'Service', 'name' => $svc->name, 'url' => $svc_url );
}
if ( $m( 'yc_pr_city' ) ) {
    $schema['contentLocation'] = array( '@type' => 'Place', 'name' => $m( 'yc_pr_city' ) . ( $m( 'yc_pr_district' ) ? '، ' . $m( 'yc_pr_district' ) : '' ) );
}
echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>';
