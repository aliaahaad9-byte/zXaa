<?php
/**
 * سابقة الأعمال — كل مشروع صفحة مستقلة (نوع المحتوى works).
 *
 * - صندوق «تفاصيل المشروع» في محرر المشروع: البيانات الأساسية،
 *   التحدي قبل التنفيذ، خطة العمل أثناء التنفيذ، النتيجة بعد التنفيذ،
 *   وصور كل مرحلة.
 * - ربط المشاريع بأقسام الخدمات (category) والمدن (country).
 * - أداة استيراد المشاريع الحالية من صفحة «مشاريعنا».
 * العرض: @single/works.php (صفحة المشروع) و @models/projects.php (صفحة كل المشاريع).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/media.php';

/* ---------- ربط المشاريع بالأقسام والمدن ---------- */
add_action( 'init', function () {
    if ( post_type_exists( 'works' ) ) {
        register_taxonomy_for_object_type( 'category', 'works' );
        if ( taxonomy_exists( 'country' ) ) {
            register_taxonomy_for_object_type( 'country', 'works' );
        }
    }
}, 99 );

/* ---------- الحقول ---------- */
function yc_pr_text_fields() {
    return array(
        'yc_pr_facility' => array( 'طبيعة المنشأة', 'مثل: فيلا سكنية من 3 طوابق، 6 غرف نوم، 4 حمامات' ),
        'yc_pr_city'     => array( 'المدينة', 'مثل: الرياض' ),
        'yc_pr_district' => array( 'الحي / المنطقة', 'مثل: حي الملقا' ),
        'yc_pr_duration' => array( 'مدة التنفيذ', 'مثل: يوم عمل كامل (9 ساعات)' ),
        'yc_pr_team'     => array( 'فريق العمل (اختياري)', 'مثل: 6 فنيين' ),
        'yc_pr_area'     => array( 'المساحة / الحجم (اختياري)', 'مثل: 320 م² أو 4 خزانات' ),
        'yc_pr_quote_by' => array( 'صاحب الرأي (اختياري)', 'مثل: السيد أحمد — صاحب الفيلا' ),
    );
}

function yc_pr_sections() {
    return array(
        'before' => array( 'yc_pr_challenge', 'التحدي والوضع قبل التنفيذ', 'yc_pr_imgs_before', 'صور قبل التنفيذ' ),
        'during' => array( 'yc_pr_plan', 'خطة العمل والحل أثناء التنفيذ', 'yc_pr_imgs_during', 'صور أثناء التنفيذ' ),
        'after'  => array( 'yc_pr_result', 'النتيجة والتحول بعد التنفيذ', 'yc_pr_imgs_after', 'صور بعد التنفيذ' ),
    );
}

/** أرقام صور من نص «1,2,3». */
function yc_pr_ids( $raw ) {
    return array_values( array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) ) );
}

/** روابط داخلية: سطر لكل رابط «النص | الرابط». */
function yc_pr_links( $raw ) {
    $out = array();
    foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
        $p = array_map( 'trim', explode( '|', $line, 2 ) );
        if ( 2 === count( $p ) && '' !== $p[0] && preg_match( '#^https?://#', $p[1] ) ) {
            $out[] = array( 'text' => $p[0], 'url' => $p[1] );
        }
    }
    return $out;
}

/* ---------- صندوق التحرير ---------- */
add_action( 'add_meta_boxes', function () {
    add_meta_box( 'yc_pr_box', 'تفاصيل المشروع', 'yc_pr_box', 'works', 'normal', 'high' );
} );

function yc_pr_gallery_field( $key, $label, $post_id ) {
    $ids = yc_pr_ids( get_post_meta( $post_id, $key, true ) );
    echo '<div class="yc-pr-gal" data-key="' . esc_attr( $key ) . '">';
    echo '<b>' . esc_html( $label ) . '</b>';
    echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '">';
    echo '<ul class="yc-pr-thumbs">';
    foreach ( $ids as $id ) {
        $src = wp_get_attachment_image_url( $id, 'thumbnail' );
        if ( $src ) {
            echo '<li data-id="' . (int) $id . '"><img src="' . esc_url( $src ) . '" alt=""><button type="button" class="yc-pr-x" aria-label="إزالة">×</button></li>';
        }
    }
    echo '</ul>';
    echo '<button type="button" class="button yc-pr-add">+ إضافة صور</button>';
    echo '</div>';
}

function yc_pr_box( $post ) {
    wp_nonce_field( 'yc_pr_save', 'yc_pr_nonce' );
    echo '<div class="yc-pr-admin">';
    echo '<p class="description">اختر <b>قسم الخدمة</b> من صندوق «التصنيفات» والمدينة من «المدن»، وضع صورة المشروع الرئيسية في «الصورة البارزة». ما تكتبه هنا يظهر في صفحة المشروع بأربعة أقسام مرقّمة.</p>';

    do_action( 'yc_pr_box_top', $post );

    echo '<h3>1. تفاصيل المشروع الأساسية</h3><div class="yc-pr-grid">';
    foreach ( yc_pr_text_fields() as $k => $f ) {
        if ( 'yc_pr_quote_by' === $k ) {
            continue;
        }
        echo '<p><label>' . esc_html( $f[0] ) . '<input type="text" name="' . esc_attr( $k ) . '" value="' . esc_attr( get_post_meta( $post->ID, $k, true ) ) . '" placeholder="' . esc_attr( $f[1] ) . '"></label></p>';
    }
    echo '</div>';

    $n = 2;
    foreach ( yc_pr_sections() as $s ) {
        echo '<h3>' . $n . '. ' . esc_html( $s[1] ) . '</h3>';
        echo '<p><textarea name="' . esc_attr( $s[0] ) . '" rows="6" placeholder="فقرة أو أكثر — اترك سطرًا فارغًا بين الفقرات.">' . esc_textarea( get_post_meta( $post->ID, $s[0], true ) ) . '</textarea></p>';
        yc_pr_gallery_field( $s[2], $s[3], $post->ID );
        $n++;
    }

    echo '<h3>رأي العميل (اختياري)</h3>';
    echo '<p><textarea name="yc_pr_quote" rows="2" placeholder="«شعرت كأنها فيلا جديدة»">' . esc_textarea( get_post_meta( $post->ID, 'yc_pr_quote', true ) ) . '</textarea></p>';
    $qb = yc_pr_text_fields()['yc_pr_quote_by'];
    echo '<p><label>' . esc_html( $qb[0] ) . '<input type="text" name="yc_pr_quote_by" value="' . esc_attr( get_post_meta( $post->ID, 'yc_pr_quote_by', true ) ) . '" placeholder="' . esc_attr( $qb[1] ) . '"></label></p>';

    echo '<h3>روابط داخلية لصفحات الخدمة</h3>';
    echo '<p><textarea name="yc_pr_links" rows="3" dir="auto" placeholder="شركة تنظيف بالرياض | https://altnfez.com/...">' . esc_textarea( get_post_meta( $post->ID, 'yc_pr_links', true ) ) . '</textarea>';
    echo '<span class="description">سطر لكل رابط بصيغة: النص | الرابط</span></p>';
    echo '</div>';
}

add_action( 'save_post_works', function ( $post_id ) {
    if ( ! isset( $_POST['yc_pr_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['yc_pr_nonce'] ) ), 'yc_pr_save' ) ) {
        return;
    }
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    $in = wp_unslash( $_POST );
    foreach ( array_keys( yc_pr_text_fields() ) as $k ) {
        update_post_meta( $post_id, $k, isset( $in[ $k ] ) ? sanitize_text_field( $in[ $k ] ) : '' );
    }
    foreach ( yc_pr_sections() as $s ) {
        update_post_meta( $post_id, $s[0], isset( $in[ $s[0] ] ) ? sanitize_textarea_field( $in[ $s[0] ] ) : '' );
        update_post_meta( $post_id, $s[2], isset( $in[ $s[2] ] ) ? implode( ',', yc_pr_ids( $in[ $s[2] ] ) ) : '' );
    }
    update_post_meta( $post_id, 'yc_pr_quote', isset( $in['yc_pr_quote'] ) ? sanitize_textarea_field( $in['yc_pr_quote'] ) : '' );
    $links = array();
    foreach ( yc_pr_links( isset( $in['yc_pr_links'] ) ? $in['yc_pr_links'] : '' ) as $l ) {
        $links[] = sanitize_text_field( $l['text'] ) . ' | ' . esc_url_raw( $l['url'] );
    }
    update_post_meta( $post_id, 'yc_pr_links', implode( "\n", $links ) );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'works' !== $screen->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }
    wp_enqueue_media();
    add_action( 'admin_footer', 'yc_pr_admin_assets' );
} );

function yc_pr_admin_assets() {
    ?>
<style>
.yc-pr-admin h3{margin:22px 0 10px;font-size:15px;border-inline-start:4px solid #2764c3;padding-inline-start:8px}
.yc-pr-admin label{display:block;font-weight:600}
.yc-pr-admin input[type=text],.yc-pr-admin textarea{display:block;width:100%;margin-top:4px;font-weight:400}
.yc-pr-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0 14px}
.yc-pr-gal{background:#f6f7f7;border:1px solid #e0e0e0;border-radius:10px;padding:10px 12px;margin-bottom:6px}
.yc-pr-thumbs{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0;list-style:none;padding:0}
.yc-pr-thumbs li{position:relative;width:84px;height:84px;margin:0;cursor:move}
.yc-pr-thumbs img{width:100%;height:100%;object-fit:cover;border-radius:8px;display:block}
.yc-pr-x{position:absolute;top:-6px;inset-inline-end:-6px;width:22px;height:22px;border-radius:50%;border:0;background:#b32d2e;color:#fff;cursor:pointer;line-height:1}
@media(max-width:900px){.yc-pr-grid{grid-template-columns:1fr}}
</style>
<script>
jQuery(function($){
  function sync($g){ $g.find('input[type=hidden]').val($g.find('li').map(function(){return $(this).data('id');}).get().join(',')); }
  $('.yc-pr-thumbs').each(function(){ if($.fn.sortable){ $(this).sortable({update:function(){ sync($(this).closest('.yc-pr-gal')); }}); } });
  $(document).on('click','.yc-pr-x',function(){ var $g=$(this).closest('.yc-pr-gal'); $(this).closest('li').remove(); sync($g); });
  $(document).on('click','.yc-pr-add',function(e){
    e.preventDefault(); if(!window.wp||!wp.media){return;}
    var $g=$(this).closest('.yc-pr-gal');
    var f=wp.media({title:'اختر صور المرحلة',multiple:true,library:{type:'image'}});
    f.on('select',function(){
      f.state().get('selection').each(function(a){
        a=a.toJSON(); var src=(a.sizes&&a.sizes.thumbnail)?a.sizes.thumbnail.url:a.url;
        $g.find('ul').append($('<li>').attr('data-id',a.id).append($('<img alt="">').attr('src',src)).append('<button type="button" class="yc-pr-x" aria-label="إزالة">×</button>'));
      });
      sync($g);
    });
    f.open();
  });
});
</script>
    <?php
}

/* ---------- القراءة ---------- */
function yc_pr_paras( $text ) {
    return array_values( array_filter( array_map( 'trim', preg_split( '/\n\s*\n/', (string) $text ) ), 'strlen' ) );
}

/** القسم الرئيسي (الخدمة) للمشروع. */
function yc_pr_service_term( $post_id ) {
    $terms = get_the_terms( $post_id, 'category' );
    if ( ! is_array( $terms ) || ! $terms ) {
        return null;
    }
    $t = $terms[0];
    if ( $t->parent ) {
        $p = get_term( $t->parent, 'category' );
        if ( $p && ! is_wp_error( $p ) ) {
            return $p;
        }
    }
    return $t;
}

/** الصورة الرئيسية للمشروع. */
function yc_pr_hero( $post_id, $size = 'large' ) {
    $src = get_the_post_thumbnail_url( $post_id, $size );
    if ( ! $src ) {
        $src = (string) get_post_meta( $post_id, 'yc_pr_hero_url', true );
    }
    if ( ! $src ) {
        foreach ( array( 'yc_pr_imgs_after', 'yc_pr_imgs_during', 'yc_pr_imgs_before' ) as $k ) {
            $ids = yc_pr_ids( get_post_meta( $post_id, $k, true ) );
            if ( $ids ) {
                $src = (string) wp_get_attachment_image_url( $ids[0], $size );
                break;
            }
        }
    }
    return $src;
}

/* =========================================================================
 * استيراد المشاريع الحالية (مرة واحدة — كمسودات للمراجعة)
 * ====================================================================== */
add_action( 'admin_menu', function () {
    add_submenu_page( 'edit.php?post_type=works', 'استيراد المشاريع الحالية', 'استيراد المشاريع الحالية', 'edit_posts', 'yc-pr-import', 'yc_pr_import_page' );
} );

function yc_pr_seed() {
    $file = __DIR__ . '/seed.json';
    $data = file_exists( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array();
    return is_array( $data ) ? $data : array();
}

function yc_pr_find_cat( $hint ) {
    $terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false, 'parent' => 0 ) );
    if ( is_wp_error( $terms ) ) {
        return 0;
    }
    foreach ( $terms as $t ) {
        if ( false !== mb_strpos( $t->name, $hint ) ) {
            return (int) $t->term_id;
        }
    }
    return 0;
}

function yc_pr_import_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }
    $seed   = yc_pr_seed();
    $report = array();

    if ( isset( $_POST['yc_pr_do_import'] ) ) {
        check_admin_referer( 'yc_pr_import' );
        $status = ( isset( $_POST['yc_pr_status'] ) && 'publish' === $_POST['yc_pr_status'] && current_user_can( 'publish_posts' ) ) ? 'publish' : 'draft';

        // إيقاف خطافات الحفظ مؤقتًا: إضافة على الموقع كانت تُسقط الطلب عند save_post،
        // فيتوقف الاستيراد بعد أول مشروع. تُعاد كما كانت بعد الانتهاء.
        global $wp_filter;
        $paused = array();
        foreach ( array( 'save_post', 'save_post_works', 'wp_insert_post', 'transition_post_status', 'draft_works', 'publish_works', 'new_to_publish', 'draft_to_publish', 'wp_after_insert_post' ) as $hook ) {
            if ( isset( $wp_filter[ $hook ] ) ) {
                $paused[ $hook ] = $wp_filter[ $hook ];
                unset( $wp_filter[ $hook ] );
            }
        }

        foreach ( $seed as $p ) {
            $exists = get_posts( array( 'post_type' => 'works', 'title' => $p['title'], 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
            if ( $exists ) {
                $report[] = array( $p['title'], 'موجود مسبقًا — تم تخطيه', (int) $exists[0] );
                continue;
            }
            $id = wp_insert_post( array(
                'post_type'    => 'works',
                'post_status'  => $status,
                'post_title'   => wp_strip_all_tags( $p['title'] ),
                'post_content' => '',
            ), true );
            if ( is_wp_error( $id ) || ! $id ) {
                $report[] = array( $p['title'], 'تعذّر الإنشاء', 0 );
                continue;
            }
            update_post_meta( $id, 'yc_pr_facility', sanitize_text_field( $p['facility'] ) );
            update_post_meta( $id, 'yc_pr_city', sanitize_text_field( $p['city'] ) );
            update_post_meta( $id, 'yc_pr_district', sanitize_text_field( $p['district'] ) );
            update_post_meta( $id, 'yc_pr_duration', sanitize_text_field( $p['duration'] ) );
            update_post_meta( $id, 'yc_pr_challenge', sanitize_textarea_field( $p['challenge'] ) );
            update_post_meta( $id, 'yc_pr_plan', sanitize_textarea_field( $p['plan'] ) );
            update_post_meta( $id, 'yc_pr_result', sanitize_textarea_field( $p['result'] ) );
            $links = array();
            foreach ( (array) $p['links'] as $l ) {
                $links[] = sanitize_text_field( $l['text'] ) . ' | ' . esc_url_raw( $l['url'] );
            }
            update_post_meta( $id, 'yc_pr_links', implode( "\n", $links ) );

            $cat = yc_pr_find_cat( $p['cat_hint'] );
            if ( $cat ) {
                wp_set_post_terms( $id, array( $cat ), 'category' );
            }
            if ( taxonomy_exists( 'country' ) && '' !== $p['city'] ) {
                $city = get_term_by( 'name', $p['city'], 'country' );
                if ( $city ) {
                    wp_set_post_terms( $id, array( (int) $city->term_id ), 'country' );
                }
            }
            $note = '';
            if ( ! empty( $p['image'] ) ) {
                $att = attachment_url_to_postid( $p['image'] );
                if ( ! $att ) {
                    $att = attachment_url_to_postid( esc_url_raw( $p['image'] ) );
                }
                if ( $att ) {
                    set_post_thumbnail( $id, $att );
                    update_post_meta( $id, 'yc_pr_imgs_after', (string) $att );
                } else {
                    update_post_meta( $id, 'yc_pr_hero_url', esc_url_raw( $p['image'] ) );
                    $note = ' (الصورة مربوطة برابطها)';
                }
            } else {
                $note = ' (بدون صورة — أضف صورها)';
            }
            $report[] = array( $p['title'], ( 'publish' === $status ? 'تم النشر' : 'تم الإنشاء كمسودة' ) . ( $cat ? '' : ' — لم يُعثر على القسم' ) . $note, (int) $id );
        }

        foreach ( $paused as $hook => $obj ) {
            $wp_filter[ $hook ] = $obj; // phpcs:ignore
        }
    }

    echo '<div class="wrap"><h1>استيراد المشاريع الحالية</h1>';
    echo '<p>تُنشئ هذه الأداة <b>' . count( $seed ) . ' مشروعًا</b> من صفحة «مشاريعنا» الحالية — كل مشروع صفحة مستقلة بأقسامه الأربعة، ويُربط بقسم الخدمة والمدينة وصورته. المشاريع الموجودة بنفس العنوان لا تتكرر.</p>';
    if ( $report ) {
        echo '<table class="widefat striped" style="max-width:1000px"><thead><tr><th>المشروع</th><th>النتيجة</th><th></th></tr></thead><tbody>';
        foreach ( $report as $r ) {
            echo '<tr><td>' . esc_html( $r[0] ) . '</td><td>' . esc_html( $r[1] ) . '</td><td>' . ( $r[2] ? '<a href="' . esc_url( get_edit_post_link( $r[2] ) ) . '">تحرير</a>' : '' ) . '</td></tr>';
        }
        echo '</tbody></table><p><a class="button button-primary" href="' . esc_url( admin_url( 'edit.php?post_type=works' ) ) . '">عرض كل المشاريع</a></p>';
    } else {
        echo '<form method="post">';
        wp_nonce_field( 'yc_pr_import' );
        echo '<p><label><input type="radio" name="yc_pr_status" value="draft" checked> كمسودات (أراجعها وأضيف الصور ثم أنشرها)</label><br>';
        echo '<label><input type="radio" name="yc_pr_status" value="publish"> نشر مباشرة</label></p>';
        echo '<p><button class="button button-primary button-large" name="yc_pr_do_import" value="1">استيراد ' . count( $seed ) . ' مشروعًا</button></p>';
        echo '<ol>';
        foreach ( $seed as $p ) {
            echo '<li>' . esc_html( $p['title'] ) . ' <span class="description">— ' . esc_html( $p['city'] ) . '</span></li>';
        }
        echo '</ol></form>';
    }
    echo '</div>';
}

/** بطاقة مشروع بشكل «بلاطة وسائط»: صورة/فيديو عمودي + شارة + عنوان + الخدمة. */
function yc_pr_card( $p, $heading = 'h3' ) {
    $id   = $p->ID;
    $m    = yc_pr_media_summary( $id );
    $svc  = yc_pr_service_term( $id );
    $sub  = null;
    $cats = get_the_terms( $id, 'category' );
    if ( is_array( $cats ) ) {
        foreach ( $cats as $c ) {
            if ( $c->parent ) {
                $sub = $c;
                break;
            }
        }
    }
    $pill = $sub ? $sub : $svc;
    $link = get_permalink( $id );
    $ttl  = get_the_title( $id );

    $h  = '<article class="yc-prc' . ( $m['is_video'] ? ' is-video' : '' ) . '" data-fam="' . esc_attr( $svc ? $svc->term_id : 0 ) . '" data-sub="' . esc_attr( $sub ? $sub->term_id : 0 ) . '">';
    $h .= '<a class="yc-prc__media" href="' . esc_url( $link ) . '" aria-label="' . esc_attr( ( $m['is_video'] ? 'شاهد فيديو: ' : '' ) . $ttl ) . '">';
    if ( $m['thumb'] ) {
        $h .= '<img src="' . esc_url( $m['thumb'] ) . '" alt="" loading="lazy" width="400" height="530">';
    } elseif ( $m['frame'] ) {
        $h .= '<video src="' . esc_url( $m['frame'] ) . '#t=0.5" preload="metadata" muted playsinline tabindex="-1" aria-hidden="true"></video>';
    } else {
        $h .= '<span class="yc-prc__ph"></span>';
    }
    if ( $m['badge'] ) {
        $h .= '<span class="yc-prc__badge">' . esc_html( $m['badge'] ) . '</span>';
    }
    if ( $m['is_video'] ) {
        $h .= '<span class="yc-prc__play" aria-hidden="true"></span>';
    }
    $h .= '</a><div class="yc-prc__body">';
    $h .= '<' . $heading . ' class="yc-prc__title"><a href="' . esc_url( $link ) . '">' . esc_html( $ttl ) . '</a></' . $heading . '>';
    if ( $pill ) {
        $h .= '<span class="yc-prc__svc">' . esc_html( $pill->name ) . '</span>';
    }
    $h .= '</div></article>';
    return $h;
}
