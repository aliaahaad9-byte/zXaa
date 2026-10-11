<?php
/**
 * نموذج الصفحة: سابقة الأعمال — كل المشاريع مع تصفية حسب الخدمة.
 * كل بطاقة تفتح صفحة المشروع المستقلة.
 */
if ( ! function_exists( 'yc_pr_card' ) ) {
    return;
}
global $post;
$page  = $post;
$works = get_posts( array( 'post_type' => 'works', 'posts_per_page' => -1, 'post_status' => 'publish', 'no_found_rows' => true ) );

// الخدمات الموجودة فعلًا في المشاريع + عدد المدن
$svcs   = array();
$cities = array();
foreach ( $works as $w ) {
    $t = yc_pr_service_term( $w->ID );
    if ( $t ) {
        if ( ! isset( $svcs[ $t->term_id ] ) ) {
            $svcs[ $t->term_id ] = array( 'name' => $t->name, 'n' => 0 );
        }
        $svcs[ $t->term_id ]['n']++;
    }
    $c = trim( (string) get_post_meta( $w->ID, 'yc_pr_city', true ) );
    if ( '' !== $c ) {
        $cities[ $c ] = true;
    }
}

$css = get_template_directory() . '/components/packs/Projects/projects.css';
if ( file_exists( $css ) ) {
    echo '<style>' . file_get_contents( $css ) . '</style>';
}

echo '<section class="yc-prs">';
echo '<div class="container">';

  echo '<div class="yc-prj__crumb"><breadcrumb>';
    Breadcrumb();
  echo '</breadcrumb></div>';

  echo '<div class="yc-prs__hero">';
    echo '<h1>' . esc_html( get_the_title( $page ) ) . '</h1>';
    $content = trim( (string) $page->post_content );
    if ( '' !== $content ) {
      echo '<div class="yc-prs__lead">' . apply_filters( 'the_content', $content ) . '</div>';
    }
    if ( $works ) {
      echo '<ul class="yc-prs__tags">';
        $nw = count( $works );
        $lw = 1 === $nw ? 'مشروع موثّق' : ( 2 === $nw ? 'مشروعان موثّقان' : ( $nw <= 10 ? 'مشاريع موثّقة' : 'مشروعًا موثّقًا' ) );
        echo '<li>' . esc_html( ( $nw > 2 ? number_format( $nw ) . ' ' : '' ) . $lw ) . '</li>';
        if ( $cities ) {
          $nc = count( $cities );
          echo '<li>' . esc_html( 1 === $nc ? 'مدينة واحدة' : ( 2 === $nc ? 'مدينتان' : number_format( $nc ) . ( $nc <= 10 ? ' مدن' : ' مدينة' ) ) ) . '</li>';
        }
        echo '<li>نتائج قابلة للقياس</li>';
      echo '</ul>';
    }
  echo '</div>';

  if ( ! $works ) {
    echo '<p class="yc-prs__empty">لم تُنشر مشاريع بعد.</p>';
  } else {
    if ( count( $svcs ) > 1 ) {
      echo '<div class="yc-prs__filter" role="group" aria-label="تصفية المشاريع حسب الخدمة">';
        echo '<button type="button" aria-pressed="true" data-svc="all">الكل <b>' . count( $works ) . '</b></button>';
        foreach ( $svcs as $id => $s ) {
          echo '<button type="button" aria-pressed="false" data-svc="' . (int) $id . '">' . esc_html( $s['name'] ) . ' <b>' . (int) $s['n'] . '</b></button>';
        }
      echo '</div>';
    }
    echo '<div class="yc-prg" aria-live="polite">';
    foreach ( $works as $w ) {
      echo yc_pr_card( $w, 'h2' ); // phpcs:ignore
    }
    echo '</div>';
  }

echo '</div>';
echo '</section>';

if ( count( $svcs ) > 1 ) {
    ?>
<script>
(function(){
  var box=document.querySelector('.yc-prs__filter'); if(!box){return;}
  var btns=box.querySelectorAll('button'), cards=document.querySelectorAll('.yc-prs .yc-prc');
  box.addEventListener('click',function(e){
    var b=e.target.closest('button'); if(!b){return;}
    var v=b.getAttribute('data-svc');
    Array.prototype.forEach.call(btns,function(x){x.setAttribute('aria-pressed',x===b?'true':'false');});
    Array.prototype.forEach.call(cards,function(c){c.hidden=!(v==='all'||c.getAttribute('data-svc')===v);});
  });
})();
</script>
    <?php
}

/* البيانات المنظّمة: قائمة المشاريع */
if ( $works ) {
    $list = array();
    foreach ( array_values( $works ) as $i => $w ) {
        $list[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'url' => get_permalink( $w->ID ), 'name' => get_the_title( $w->ID ) );
    }
    echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => get_the_title( $page ), 'itemListElement' => $list ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>';
}
