<?php
/**
 * نموذج الصفحة: سابقة الأعمال — بلاطات صور/فيديو مع تصفية بالخدمة والخدمة الفرعية.
 * كل بطاقة تفتح صفحة المشروع (الفيديو أعلاها ثم الوصف ثم أعمال مشابهة).
 * يدعم ?fam=رقم_القسم&sub=رقم_الفرعي في الرابط.
 */
if ( ! function_exists( 'yc_pr_card' ) ) {
    return;
}
global $post;
$page  = $post;
$works = get_posts( array( 'post_type' => 'works', 'posts_per_page' => -1, 'post_status' => 'publish', 'no_found_rows' => true ) );

// الأقسام الرئيسية والفرعية الموجودة فعلًا في المشاريع
$fams = array();
foreach ( $works as $w ) {
    $f = yc_pr_service_term( $w->ID );
    if ( ! $f ) {
        continue;
    }
    if ( ! isset( $fams[ $f->term_id ] ) ) {
        $fams[ $f->term_id ] = array( 'name' => $f->name, 'n' => 0, 'subs' => array() );
    }
    $fams[ $f->term_id ]['n']++;
    $cats = get_the_terms( $w->ID, 'category' );
    if ( is_array( $cats ) ) {
        foreach ( $cats as $c ) {
            if ( $c->parent ) {
                if ( ! isset( $fams[ $f->term_id ]['subs'][ $c->term_id ] ) ) {
                    $fams[ $f->term_id ]['subs'][ $c->term_id ] = array( 'name' => $c->name, 'n' => 0 );
                }
                $fams[ $f->term_id ]['subs'][ $c->term_id ]['n']++;
                break;
            }
        }
    }
}
$dots = array( '#2764c3', '#0f9d58', '#d93025', '#2764c3', '#e8a20c', '#7b3fd1', '#0b8a8a' );

$css = get_template_directory() . '/components/packs/Projects/projects.css';
if ( file_exists( $css ) ) {
    echo '<style>' . file_get_contents( $css ) . '</style>';
}

echo '<section class="yc-prs">';
  echo '<div class="yc-prs__hero"><div class="container">';
    echo '<div class="yc-prj__crumb"><breadcrumb>';
      Breadcrumb();
    echo '</breadcrumb></div>';
    echo '<h1>' . esc_html( get_the_title( $page ) ) . '</h1>';
    $content = trim( (string) $page->post_content );
    if ( '' !== $content ) {
      echo '<div class="yc-prs__lead">' . apply_filters( 'the_content', $content ) . '</div>';
    }
    if ( $works ) {
      $nw = count( $works );
      $lw = 1 === $nw ? 'عمل موثّق' : ( 2 === $nw ? 'عملان موثّقان' : ( $nw <= 10 ? 'أعمال موثّقة' : 'عملًا موثّقًا' ) );
      echo '<p class="yc-prs__count" aria-live="polite"><b>' . esc_html( $nw > 2 ? number_format( $nw ) . ' ' . $lw : $lw ) . '</b></p>';
    }

    if ( count( $fams ) > 1 ) {
      echo '<div class="yc-prs__filter" role="group" aria-label="تصفية الأعمال حسب الخدمة">';
        echo '<button type="button" aria-pressed="true" data-fam="all"><i style="background:#2764c3"></i>الكل</button>';
        $k = 0;
        foreach ( $fams as $id => $f ) {
          echo '<button type="button" aria-pressed="false" data-fam="' . (int) $id . '"><i style="background:' . esc_attr( $dots[ $k % count( $dots ) ] ) . '"></i>' . esc_html( $f['name'] ) . '</button>';
          $k++;
        }
      echo '</div>';
      foreach ( $fams as $id => $f ) {
        if ( count( $f['subs'] ) < 2 ) {
          continue;
        }
        echo '<div class="yc-prs__subs" data-for="' . (int) $id . '" role="group" aria-label="' . esc_attr( 'تصفية ' . $f['name'] ) . '" hidden>';
        foreach ( $f['subs'] as $sid => $sb ) {
          echo '<button type="button" aria-pressed="false" data-sub="' . (int) $sid . '">' . esc_html( $sb['name'] ) . '</button>';
        }
        echo '</div>';
      }
    }
  echo '</div></div>';

  echo '<div class="container">';
  if ( ! $works ) {
    echo '<p class="yc-prs__empty">لم تُنشر أعمال بعد.</p>';
  } else {
    echo '<div class="yc-prg yc-prg--tiles" id="yc-prs-grid">';
    foreach ( $works as $w ) {
      echo yc_pr_card( $w, 'h2' ); // phpcs:ignore
    }
    echo '</div>';
    echo '<p class="yc-prs__morewrap"><button type="button" class="yc-prs__more" hidden>عرض المزيد</button></p>';
  }
  echo '</div>';
echo '</section>';

if ( $works ) {
    ?>
<script>
(function(){
  var PAGE=20, shown=PAGE, fam='all', sub='';
  var grid=document.getElementById('yc-prs-grid'); if(!grid){return;}
  var cards=[].slice.call(grid.querySelectorAll('.yc-prc'));
  var famBtns=[].slice.call(document.querySelectorAll('.yc-prs__filter button'));
  var subBars=[].slice.call(document.querySelectorAll('.yc-prs__subs'));
  var more=document.querySelector('.yc-prs__more');
  var count=document.querySelector('.yc-prs__count b');
  function label(n){ return n===1?'عمل موثّق':n===2?'عملان موثّقان':n<=10?n+' أعمال موثّقة':n.toLocaleString('en-US')+' عملًا موثّقًا'; }
  function apply(push){
    var match=cards.filter(function(c){ return (fam==='all'||c.getAttribute('data-fam')===fam)&&(!sub||c.getAttribute('data-sub')===sub); });
    cards.forEach(function(c){ c.hidden=true; });
    match.forEach(function(c,i){ c.hidden=i>=shown; });
    if(more){ more.hidden=match.length<=shown; }
    if(count){ count.textContent=label(match.length); }
    famBtns.forEach(function(b){ b.setAttribute('aria-pressed', b.getAttribute('data-fam')===fam?'true':'false'); });
    subBars.forEach(function(bar){
      bar.hidden=bar.getAttribute('data-for')!==fam;
      [].forEach.call(bar.querySelectorAll('button'),function(b){ b.setAttribute('aria-pressed', b.getAttribute('data-sub')===sub?'true':'false'); });
    });
    if(push&&window.history&&history.replaceState){
      var u=new URL(location.href); u.searchParams.delete('fam'); u.searchParams.delete('sub');
      if(fam!=='all'){ u.searchParams.set('fam',fam); } if(sub){ u.searchParams.set('sub',sub); }
      history.replaceState(null,'',u.toString());
    }
  }
  famBtns.forEach(function(b){ b.addEventListener('click',function(){ fam=b.getAttribute('data-fam'); sub=''; shown=PAGE; apply(true); }); });
  subBars.forEach(function(bar){ bar.addEventListener('click',function(e){ var b=e.target.closest('button'); if(!b){return;} var v=b.getAttribute('data-sub'); sub=(sub===v)?'':v; shown=PAGE; apply(true); }); });
  if(more){ more.addEventListener('click',function(){ var first=cards.filter(function(c){return c.hidden;})[0]; shown+=PAGE; apply(false); var a=first&&first.querySelector('a'); if(a){a.focus();} }); }
  try{ var q=new URLSearchParams(location.search); if(q.get('fam')&&famBtns.some(function(b){return b.getAttribute('data-fam')===q.get('fam');})){ fam=q.get('fam'); } if(q.get('sub')){ sub=q.get('sub'); } }catch(e){}
  apply(false);
})();
</script>
    <?php
    $list = array();
    foreach ( array_values( $works ) as $i => $w ) {
        $list[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'url' => get_permalink( $w->ID ), 'name' => get_the_title( $w->ID ) );
    }
    echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => get_the_title( $page ), 'itemListElement' => $list ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>';
}
