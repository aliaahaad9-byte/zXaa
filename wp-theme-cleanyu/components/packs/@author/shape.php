<?php
/**
 * صفحة الكاتب — ملف خبير مبني على معايير E-E-A-T.
 *
 * كل النصوص تأتي من «ملف الخبير» في صفحة الملف الشخصي
 * (حزمة AuthorBio)، ولا يوجد أي محتوى مكتوب داخل هذا الملف.
 */

$curauth = ( get_query_var( 'author_name' ) )
    ? get_user_by( 'slug', get_query_var( 'author_name' ) )
    : get_userdata( get_query_var( 'author' ) );

if ( ! $curauth || ! function_exists( 'yc_author_profile' ) ) {
    return;
}

global $wp, $wp_rewrite, $wp_query;
$paged  = $this->Paged();
$UniqId = uniqid();
$a      = yc_author_profile( $curauth->ID );

/** عنوان قسم بأيقونة مربعة. */
$yc_head = function ( $icon, $text, $tag = 'h2' ) {
    return '<' . $tag . ' class="yc-auth__h"><i>' . yc_author_svg( $icon ) . '</i>' . esc_html( $text ) . '</' . $tag . '>';
};

/** تمييز العدد في العربية: مقال / مقالان / مقالات / مقالًا. */
$yc_count_label = function ( $n, $one, $two, $few, $many ) {
    if ( 1 === $n ) { return $one; }
    if ( 2 === $n ) { return $two; }
    if ( $n >= 3 && $n <= 10 ) { return $few; }
    return $many;
};

# الأنماط — مضمّنة حتى لا نلمس main.css
$css_file = get_template_directory() . '/components/packs/AuthorBio/author-page.css';
if ( file_exists( $css_file ) ) {
    echo '<style>' . file_get_contents( $css_file ) . '</style>';
}

echo '<section class="yc-auth">';
echo '<div class="container">';

  echo '<div class="yc-auth__crumb"><breadcrumb>';
    Breadcrumb();
  echo '</breadcrumb></div>';

  /* ---------- 1) الهوية ---------- */
  echo '<div class="yc-auth__hero">';

    echo '<div class="yc-auth__photo">';
      echo '<img src="' . esc_url( $a['photo'] ) . '" width="190" height="190" alt="' . esc_attr( $a['name'] ) . '" />';
    echo '</div>';

    echo '<div class="yc-auth__id">';
      if ( $a['verified'] ) {
        echo '<span class="yc-auth__badge">' . yc_author_svg( 'check' ) . 'خبير موثّق</span>';
      }
      echo '<h1>' . esc_html( $a['name'] ) . '</h1>';
      if ( '' !== $a['title'] ) {
        echo '<p class="yc-auth__role">' . esc_html( $a['title'] ) . '</p>';
      }
      if ( '' !== $a['short'] ) {
        echo '<p class="yc-auth__bio">' . esc_html( $a['short'] ) . '</p>';
      }

      $chips = array();
      if ( '' !== $a['regions'] ) {
        $chips[] = '<li>' . yc_author_svg( 'pin' ) . esc_html( $a['regions'] ) . '</li>';
      }
      if ( ! empty( $a['exp'][0]['title'] ) ) {
        $chips[] = '<li>' . yc_author_svg( $a['exp'][0]['icon'] ) . esc_html( $a['exp'][0]['title'] ) . '</li>';
      }
      if ( $chips ) {
        echo '<ul class="yc-auth__chips">' . implode( '', $chips ) . '</ul>';
      }

      if ( $a['socials'] ) {
        $labels = yc_author_social_fields();
        echo '<div class="yc-auth__social">';
          foreach ( $a['socials'] as $k => $u ) {
            echo '<a href="' . esc_url( $u ) . '" target="_blank" rel="me noopener" aria-label="' . esc_attr( $labels[ $k ][1] ) . '" title="' . esc_attr( $labels[ $k ][1] ) . '">' . yc_author_svg( $k ) . '</a>';
          }
        echo '</div>';
      }
    echo '</div>';

  echo '</div>';

  /* ---------- 2) الأرقام ---------- */
  $stats = array();
  if ( '' !== $a['years'] ) {
    $stats[] = array( 'bag', yc_author_num( $a['years'] ), 'سنة خبرة ميدانية' );
  }
  if ( '' !== $a['projects'] ) {
    $stats[] = array( 'clip', yc_author_num( $a['projects'] ), 'معاينة ومشروع منفّذ' );
  }
  if ( $a['posts'] > 0 ) {
    $stats[] = array( 'news', number_format( $a['posts'] ), $yc_count_label( $a['posts'], 'مقال منشور', 'مقالان منشوران', 'مقالات منشورة', 'مقالًا منشورًا' ) );
  }
  if ( $a['certs'] ) {
    $c       = count( $a['certs'] );
    $stats[] = array( 'award', number_format( $c ), $yc_count_label( $c, 'شهادة معتمدة', 'شهادتان معتمدتان', 'شهادات معتمدة', 'شهادة معتمدة' ) );
  }
  if ( $stats ) {
    echo '<div class="yc-auth__stats yc-auth__stats--' . count( $stats ) . '">';
      foreach ( $stats as $s ) {
        echo '<div class="yc-auth__stat"><i>' . yc_author_svg( $s[0] ) . '</i><b>' . esc_html( $s[1] ) . '</b><span>' . esc_html( $s[2] ) . '</span></div>';
      }
    echo '</div>';
  }

  /* ---------- 3) النبذة التفصيلية ---------- */
  if ( '' !== $a['long'] && $a['long'] !== $a['short'] ) {
    echo '<div class="yc-auth__card">';
      echo $yc_head( 'user', 'نبذة عن الكاتب' );
      echo '<div class="yc-auth__text">';
        foreach ( preg_split( '/\n\s*\n|\r\n\s*\r\n/', $a['long'] ) as $para ) {
          $para = trim( $para );
          if ( '' !== $para ) {
            echo '<p>' . nl2br( esc_html( $para ) ) . '</p>';
          }
        }
      echo '</div>';
    echo '</div>';
  }

  /* ---------- 4) الخبرة حسب القسم ---------- */
  if ( $a['exp'] ) {
    echo '<div class="yc-auth__card">';
      echo $yc_head( 'bag', 'الخبرة الميدانية حسب القسم' );
      echo '<div class="yc-auth__exp">';
        foreach ( $a['exp'] as $e ) {
          $link = '';
          if ( ! empty( $e['cat'] ) ) {
            $l = get_category_link( (int) $e['cat'] );
            if ( $l && ! is_wp_error( $l ) ) {
              $link = $l;
            }
          }
          echo '<article class="yc-auth__field">';
            echo '<div class="yc-auth__field-top">';
              echo '<i>' . yc_author_svg( $e['icon'] ) . '</i>';
              echo '<h3>' . esc_html( $e['title'] ) . '</h3>';
              if ( '' !== $e['years'] ) {
                echo '<span class="yc-auth__yrs">' . esc_html( yc_author_num( $e['years'] ) ) . ' سنة</span>';
              }
            echo '</div>';
            if ( '' !== $e['desc'] ) {
              echo '<p>' . esc_html( $e['desc'] ) . '</p>';
            }
            if ( $link ) {
              echo '<a class="yc-auth__more" href="' . esc_url( $link ) . '">مقالاته في هذا القسم' . yc_author_svg( 'arrow' ) . '</a>';
            }
          echo '</article>';
        }
      echo '</div>';
    echo '</div>';
  }

  /* ---------- 5) الشهادات والعضويات ---------- */
  if ( $a['certs'] || $a['members'] ) {
    echo '<div class="yc-auth__two' . ( $a['certs'] && $a['members'] ? '' : ' yc-auth__two--one' ) . '">';

      if ( $a['certs'] ) {
        echo '<div class="yc-auth__card">';
          echo $yc_head( 'award', 'الشهادات والاعتمادات' );
          echo '<ul class="yc-auth__list">';
            foreach ( $a['certs'] as $c ) {
              $sub = array_filter( array( $c['issuer'], $c['year'] ) );
              echo '<li><i>' . yc_author_svg( 'seal' ) . '</i><div>';
                echo '<b>' . esc_html( $c['name'] ) . '</b>';
                if ( $sub ) {
                  echo '<span>' . esc_html( implode( ' · ', $sub ) ) . '</span>';
                }
                if ( ! empty( $c['url'] ) ) {
                  echo '<a href="' . esc_url( $c['url'] ) . '" target="_blank" rel="noopener nofollow">تحقّق من الشهادة' . yc_author_svg( 'link' ) . '</a>';
                }
              echo '</div></li>';
            }
          echo '</ul>';
        echo '</div>';
      }

      if ( $a['members'] ) {
        echo '<div class="yc-auth__card">';
          echo $yc_head( 'people', 'العضويات المهنية' );
          echo '<ul class="yc-auth__list">';
            foreach ( $a['members'] as $m ) {
              echo '<li><i>' . yc_author_svg( 'bank' ) . '</i><div>';
                echo '<b>' . esc_html( $m['name'] ) . '</b>';
                if ( '' !== $m['type'] ) {
                  echo '<span>' . esc_html( $m['type'] ) . '</span>';
                }
                if ( ! empty( $m['url'] ) ) {
                  echo '<a href="' . esc_url( $m['url'] ) . '" target="_blank" rel="noopener nofollow">صفحة العضوية' . yc_author_svg( 'link' ) . '</a>';
                }
              echo '</div></li>';
            }
          echo '</ul>';
        echo '</div>';
      }

    echo '</div>';
  }

  /* ---------- 6) الإنجازات ---------- */
  if ( $a['achievements'] ) {
    echo '<div class="yc-auth__card">';
      echo $yc_head( 'trophy', 'سجل الإنجازات' );
      echo '<ul class="yc-auth__done">';
        foreach ( $a['achievements'] as $line ) {
          echo '<li>' . yc_author_svg( 'check' ) . '<span>' . esc_html( $line ) . '</span></li>';
        }
      echo '</ul>';
    echo '</div>';
  }

  /* ---------- 7) المقالات ---------- */
  echo '<div class="yc-auth__posts">';
    echo $yc_head( 'news', 'مقالات ' . $a['name'] );
    $this->Part( 'Posts', array(
        'AutoLoadmore' => true,
        'UniqId'       => $UniqId,
        'author'       => $a['id'],
    ) );
  echo '</div>';

echo '</div>';
echo '</section>';

/* ---------- البيانات المنظّمة: Person ---------- */
$person = array(
    '@context' => 'https://schema.org',
    '@type'    => 'Person',
    '@id'      => $a['url'] . '#person',
    'name'     => $a['name'],
    'url'      => $a['url'],
    'image'    => $a['photo'],
);
if ( '' !== $a['title'] ) { $person['jobTitle']    = $a['title']; }
if ( '' !== $a['short'] ) { $person['description'] = $a['short']; }
if ( $a['socials'] )      { $person['sameAs']      = array_values( $a['socials'] ); }

if ( $a['exp'] ) {
    $person['knowsAbout'] = wp_list_pluck( $a['exp'], 'title' );
}
if ( '' !== $a['title'] ) {
    $occ = array( '@type' => 'Occupation', 'name' => $a['title'] );
    if ( '' !== $a['regions'] ) {
        $occ['occupationLocation'] = array( '@type' => 'AdministrativeArea', 'name' => $a['regions'] );
    }
    $person['hasOccupation'] = $occ;
}
if ( $a['certs'] ) {
    $creds = array();
    foreach ( $a['certs'] as $c ) {
        $cred = array(
            '@type'              => 'EducationalOccupationalCredential',
            'name'               => $c['name'],
            'credentialCategory' => 'certificate',
        );
        if ( '' !== $c['issuer'] ) { $cred['recognizedBy'] = array( '@type' => 'Organization', 'name' => $c['issuer'] ); }
        if ( '' !== $c['year'] )   { $cred['dateCreated']  = $c['year']; }
        if ( ! empty( $c['url'] ) ) { $cred['url']         = $c['url']; }
        $creds[] = $cred;
    }
    $person['hasCredential'] = $creds;
}
if ( $a['members'] ) {
    $orgs = array();
    foreach ( $a['members'] as $m ) {
        $org = array( '@type' => 'Organization', 'name' => $m['name'] );
        if ( ! empty( $m['url'] ) ) { $org['url'] = $m['url']; }
        $orgs[] = $org;
    }
    $person['memberOf'] = $orgs;
}
if ( $a['achievements'] ) {
    $person['award'] = $a['achievements'];
}
$person['worksFor'] = array(
    '@type' => 'Organization',
    'name'  => get_option( 'sitename' ) ? get_option( 'sitename' ) : get_bloginfo( 'name' ),
    'url'   => home_url( '/' ),
);

echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $person, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>' . "\n";
