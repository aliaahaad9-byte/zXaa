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

  /* ---------- الوسائط: الفيديو أولًا، وإلا الصورة ---------- */
  $media  = yc_pr_media_summary( $pid );
  $videos = $media['videos'];
  if ( $videos ) {
    echo '<div class="yc-prj__stage">' . yc_pr_player( $videos[0], true ) . '</div>';
  } elseif ( $hero ) {
    echo '<figure class="yc-prj__stage yc-prj__stage--img"><img src="' . esc_url( $hero ) . '" alt="' . esc_attr( $title ) . '" fetchpriority="high"></figure>';
  }

  /* ---------- العنوان ---------- */
  echo '<div class="yc-prj__head">';
    echo '<h1>' . esc_html( $title ) . '</h1>';
    $meta = array();
    if ( $svc ) {
      $meta[] = '<span class="yc-prj__mk">الخدمة</span> <a href="' . esc_url( $svc_url ) . '">' . esc_html( $svc->name ) . '</a>';
    }
    $nimg = count( $media['images'] );
    $kind = $videos && $nimg ? 'صور وفيديو' : ( $videos ? 'فيديو' : ( $nimg ? 'صور' : '' ) );
    if ( $kind ) {
      $meta[] = '<span class="yc-prj__mk">' . esc_html( $kind ) . '</span>';
    }
    if ( $videos && $videos[0]['label'] ) {
      $meta[] = '<b>فيديو ' . esc_html( $videos[0]['label'] ) . '</b>';
    }
    $place = $m( 'yc_pr_city' ) . ( $m( 'yc_pr_district' ) ? ' — ' . $m( 'yc_pr_district' ) : '' );
    if ( '' !== trim( $place ) ) {
      $meta[] = esc_html( $place );
    }
    if ( $meta ) {
      echo '<p class="yc-prj__sub">' . implode( '<span aria-hidden="true" class="yc-prj__dot">·</span>', $meta ) . '</p>'; // phpcs:ignore -- مُهرَّب أعلاه
    }
  echo '</div>';

  /* باقي فيديوهات المشروع */
  if ( count( $videos ) > 1 ) {
    echo '<section class="yc-prj__more-vids"><h2>فيديوهات أخرى من المشروع</h2><div class="yc-prj__vgrid">';
    foreach ( array_slice( $videos, 1 ) as $v ) {
      echo '<figure>' . yc_pr_player( $v ) . '<figcaption>' . esc_html( $v['caption'] ) . '</figcaption></figure>'; // phpcs:ignore
    }
    echo '</div></section>';
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

  /* ---------- أعمال مشابهة: نفس الخدمة الفرعية أولًا ثم نفس القسم ---------- */
  $related = array();
  $exclude = array( $pid );
  $subcat  = 0;
  $pcats   = get_the_terms( $pid, 'category' );
  if ( is_array( $pcats ) ) {
    foreach ( $pcats as $c ) {
      if ( $c->parent ) { $subcat = $c->term_id; break; }
    }
  }
  foreach ( array_filter( array( $subcat, $svc ? $svc->term_id : 0 ) ) as $cat_id ) {
    if ( count( $related ) >= 4 ) { break; }
    $more = get_posts( array( 'post_type' => 'works', 'posts_per_page' => 4 - count( $related ), 'post__not_in' => $exclude, 'cat' => $cat_id, 'no_found_rows' => true ) );
    foreach ( $more as $r ) { $related[] = $r; $exclude[] = $r->ID; }
  }
  if ( ! $related ) {
    $related = get_posts( array( 'post_type' => 'works', 'posts_per_page' => 4, 'post__not_in' => $exclude, 'no_found_rows' => true ) );
  }
  if ( $related ) {
    echo '<section class="yc-prj__related"><h2>أعمال مشابهة</h2><div class="yc-prg yc-prg--4">';
    foreach ( $related as $r ) {
      echo yc_pr_card( $r ); // phpcs:ignore
    }
    echo '</div></section>';
  }

echo '</div>';
echo '</article>';

/* ---------- البيانات المنظّمة: Article + VideoObject (أو ImageObject إن كانت صورًا فقط) ---------- */
$page_url = get_permalink( $pid );
$article  = array(
    '@type'            => 'Article',
    '@id'              => $page_url . '#article',
    'headline'         => $title,
    'datePublished'    => get_post_time( 'c', true, $post ),
    'dateModified'     => get_post_modified_time( 'c', true, $post ),
    'mainEntityOfPage' => $page_url,
    'inLanguage'       => 'ar',
    'publisher'        => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ),
    'author'           => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ),
);
$first = yc_pr_paras( get_post_meta( $pid, 'yc_pr_challenge', true ) );
if ( $first ) {
    $article['description'] = wp_trim_words( $first[0], 40, '…' );
}
if ( $svc ) {
    $article['about'] = array( '@type' => 'Service', 'name' => $svc->name, 'url' => $svc_url );
}
if ( $m( 'yc_pr_city' ) ) {
    $article['contentLocation'] = array( '@type' => 'Place', 'name' => $m( 'yc_pr_city' ) . ( $m( 'yc_pr_district' ) ? '، ' . $m( 'yc_pr_district' ) : '' ) );
}
$graph = array();
if ( $videos ) {
    $refs = array();
    foreach ( $videos as $i => $v ) {
        $vo = yc_pr_video_schema( $v, $post );
        if ( $vo ) {
            $vo['@id']  = $page_url . '#video-' . ( $i + 1 );
            $graph[]    = $vo;
            $refs[]     = array( '@id' => $vo['@id'] );
        }
    }
    if ( $refs ) {
        $article['video'] = $refs;
    }
    if ( $media['thumb'] ) {
        $article['image'] = $media['thumb'];
    }
} elseif ( $media['images'] ) {
    $refs = array();
    foreach ( $media['images'] as $i => $img ) {
        $io        = yc_pr_image_schema( $img, $post );
        $io['@id'] = $page_url . '#image-' . ( $i + 1 );
        $graph[]   = $io;
        $refs[]    = array( '@id' => $io['@id'] );
    }
    $article['image'] = $refs;
}
array_unshift( $graph, $article );
echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>';
echo yc_pr_player_js(); // phpcs:ignore
