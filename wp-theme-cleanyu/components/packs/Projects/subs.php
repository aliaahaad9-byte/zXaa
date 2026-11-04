<?php
/**
 * الخدمات الفرعية لسابقة الأعمال (مثل: تنظيف خزانات، تنظيف كنب) داخل تبويب رئيسي (قسم من التصنيفات).
 *
 * - تُدار من: سابقة الأعمال ← الخدمات الفرعية (الاسم، التبويب، المقالات المرتبطة).
 * - تُختار لكل مشروع من صندوق «تفاصيل المشروع» (أو تُنشأ جديدة من هناك مباشرة).
 * - تظهر أزرارًا تحت التبويب في صفحة سابقة الأعمال، ورابطها: ?fam=رقم_التبويب&sub=s رقم_الخدمة.
 * - المقال المرتبط بخدمة فرعية يُظهر زر «سابقة أعمالنا» العائم برابط تبويبها مباشرة.
 * المشاريع التي ليس لها خدمة فرعية تبقى على التصنيف الفرعي (السلوك القديم).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** كل الخدمات الفرعية: [ id => [ 'name', 'fam' (term_id), 'posts' (معرّفات المقالات) ] ] بالترتيب. */
function yc_pr_subs() {
    $s = get_option( 'yc_pr_subs' );
    return is_array( $s ) ? $s : array();
}

/** التبويبات المتاحة: الأقسام الرئيسية في التصنيفات. */
function yc_pr_fam_terms() {
    $t = get_terms( array( 'taxonomy' => 'category', 'parent' => 0, 'hide_empty' => false ) );
    return is_wp_error( $t ) ? array() : $t;
}

/** إضافة خدمة فرعية (أو إرجاع الموجودة بنفس الاسم في نفس التبويب). */
function yc_pr_sub_add( $name, $fam ) {
    $name = trim( sanitize_text_field( $name ) );
    $fam  = absint( $fam );
    if ( '' === $name || ! $fam ) {
        return 0;
    }
    $subs = yc_pr_subs();
    foreach ( $subs as $id => $s ) {
        if ( (int) $s['fam'] === $fam && $s['name'] === $name ) {
            return (int) $id;
        }
    }
    $id          = $subs ? max( array_map( 'intval', array_keys( $subs ) ) ) + 1 : 1;
    $subs[ $id ] = array( 'name' => $name, 'fam' => $fam, 'posts' => array() );
    update_option( 'yc_pr_subs', $subs, false );
    return $id;
}

/**
 * تصنيف المشروع: [ التبويب (WP_Term|null), مفتاح الخدمة الفرعية ('s3' أو 'c45' أو ''), اسمها ].
 * الخدمة الفرعية المختارة يدويًا تحدد التبويب أيضًا، وإلا يُستخدم التصنيف الفرعي.
 */
function yc_pr_classify( $post_id ) {
    static $cache = array();
    if ( isset( $cache[ $post_id ] ) ) {
        return $cache[ $post_id ];
    }
    $subs = yc_pr_subs();
    $fam  = yc_pr_service_term( $post_id );
    $key  = '';
    $name = '';
    $sid  = (int) get_post_meta( $post_id, 'yc_pr_sub', true );
    if ( $sid && isset( $subs[ $sid ] ) ) {
        $t = get_term( (int) $subs[ $sid ]['fam'], 'category' );
        if ( $t && ! is_wp_error( $t ) ) {
            $fam = $t;
        }
        $key  = 's' . $sid;
        $name = $subs[ $sid ]['name'];
    } else {
        $cats = get_the_terms( $post_id, 'category' );
        if ( is_array( $cats ) ) {
            foreach ( $cats as $c ) {
                if ( $c->parent ) {
                    $key  = 'c' . $c->term_id;
                    $name = $c->name;
                    // نفس الاسم موجود كخدمة فرعية في نفس التبويب ← زر واحد لا زرّان
                    foreach ( $subs as $id => $s ) {
                        if ( $s['name'] === $c->name && $fam && (int) $s['fam'] === (int) $fam->term_id ) {
                            $key = 's' . $id;
                            break;
                        }
                    }
                    break;
                }
            }
        }
    }
    return $cache[ $post_id ] = array( $fam, $key, $name );
}

/** الخدمة الفرعية المرتبطة بمقال/صفحة (0 إن لم توجد). */
function yc_pr_sub_for_post( $post_id ) {
    $post_id = (int) $post_id;
    if ( ! $post_id ) {
        return 0;
    }
    foreach ( yc_pr_subs() as $id => $s ) {
        if ( in_array( $post_id, array_map( 'intval', (array) $s['posts'] ), true ) ) {
            return (int) $id;
        }
    }
    return 0;
}

/** رابط تبويب الخدمة الفرعية على صفحة سابقة الأعمال. */
function yc_pr_sub_url( $sid, $base ) {
    $subs = yc_pr_subs();
    if ( ! isset( $subs[ $sid ] ) ) {
        return $base;
    }
    return add_query_arg( array( 'fam' => (int) $subs[ $sid ]['fam'], 'sub' => 's' . (int) $sid ), $base );
}

/* ---------- حقل «الخدمة الفرعية» في محرر المشروع ---------- */
add_action( 'yc_pr_box_top', function ( $post ) {
    $subs = yc_pr_subs();
    $cur  = (int) get_post_meta( $post->ID, 'yc_pr_sub', true );
    $fams = yc_pr_fam_terms();
    $def  = yc_pr_service_term( $post->ID );
    echo '<div style="background:#f6f9ff;border:1px solid #c9dafc;border-radius:10px;padding:12px 16px;margin:10px 0 4px">';
    echo '<h3 style="margin-top:4px">الخدمة الفرعية (تظهر زرًّا تحت التبويب في صفحة سابقة الأعمال)</h3>';
    echo '<p><select name="yc_pr_sub" style="min-width:320px"><option value="0">— بدون (حسب التصنيف) —</option>';
    $by = array();
    foreach ( $subs as $id => $s ) {
        $by[ (int) $s['fam'] ][ $id ] = $s['name'];
    }
    foreach ( $fams as $f ) {
        if ( empty( $by[ $f->term_id ] ) ) {
            continue;
        }
        echo '<optgroup label="' . esc_attr( $f->name ) . '">';
        foreach ( $by[ $f->term_id ] as $id => $n ) {
            echo '<option value="' . (int) $id . '"' . selected( $cur, $id, false ) . '>' . esc_html( $n ) . '</option>';
        }
        echo '</optgroup>';
    }
    echo '</select></p>';
    echo '<p style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">أو أضف جديدة: <input type="text" name="yc_pr_sub_new" placeholder="مثال: تنظيف خزانات" style="min-width:220px"> في تبويب <select name="yc_pr_sub_new_fam">';
    foreach ( $fams as $f ) {
        echo '<option value="' . (int) $f->term_id . '"' . selected( $def ? $def->term_id : 0, $f->term_id, false ) . '>' . esc_html( $f->name ) . '</option>';
    }
    echo '</select></p>';
    echo '<p class="description">لربط الخدمة الفرعية بمقالها (ليظهر عليه زر «سابقة أعمالنا» برابط هذا التبويب): <a href="' . esc_url( admin_url( 'edit.php?post_type=works&page=yc-pr-subs' ) ) . '" target="_blank">سابقة الأعمال ← الخدمات الفرعية</a>.</p>';
    echo '</div>';
} );

add_action( 'save_post_works', function ( $post_id ) {
    if ( ! isset( $_POST['yc_pr_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['yc_pr_nonce'] ) ), 'yc_pr_save' ) ) {
        return;
    }
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    $in  = wp_unslash( $_POST );
    $sid = isset( $in['yc_pr_sub'] ) ? absint( $in['yc_pr_sub'] ) : 0;
    if ( ! empty( $in['yc_pr_sub_new'] ) && current_user_can( 'edit_posts' ) ) {
        $sid = yc_pr_sub_add( $in['yc_pr_sub_new'], isset( $in['yc_pr_sub_new_fam'] ) ? $in['yc_pr_sub_new_fam'] : 0 );
    }
    $subs = yc_pr_subs();
    update_post_meta( $post_id, 'yc_pr_sub', isset( $subs[ $sid ] ) ? $sid : 0 );
}, 20 );

/* ---------- صفحة «الخدمات الفرعية» ---------- */
add_action( 'admin_menu', function () {
    add_submenu_page( 'edit.php?post_type=works', 'الخدمات الفرعية', 'الخدمات الفرعية', 'edit_posts', 'yc-pr-subs', 'yc_pr_subs_page' );
}, 25 );

/** تحويل سطور (روابط أو أرقام) إلى معرّفات مقالات منشورة. */
function yc_pr_posts_from_lines( $raw ) {
    $ids = array();
    foreach ( preg_split( '/[\r\n,]+/', (string) $raw ) as $l ) {
        $l = trim( $l );
        if ( '' === $l ) {
            continue;
        }
        $id = ctype_digit( $l ) ? (int) $l : url_to_postid( $l );
        if ( ! $id && preg_match( '/[?&]p(?:age_id)?=(\d+)/', $l, $m ) ) {
            $id = (int) $m[1];
        }
        if ( $id && get_post( $id ) ) {
            $ids[] = $id;
        }
    }
    return array_values( array_unique( $ids ) );
}

add_action( 'admin_init', function () {
    if ( empty( $_POST['yc_pr_subs_save'] ) || ! current_user_can( 'edit_posts' ) ) {
        return;
    }
    check_admin_referer( 'yc_pr_subs_save' );
    $rows = isset( $_POST['subs'] ) && is_array( $_POST['subs'] ) ? wp_unslash( $_POST['subs'] ) : array();
    $old  = yc_pr_subs();
    $new  = array();
    $next = $old ? max( array_map( 'intval', array_keys( $old ) ) ) + 1 : 1;
    $miss = 0;
    foreach ( $rows as $key => $r ) {
        $name = isset( $r['name'] ) ? trim( sanitize_text_field( $r['name'] ) ) : '';
        $fam  = isset( $r['fam'] ) ? absint( $r['fam'] ) : 0;
        if ( '' === $name || ! $fam || ! empty( $r['del'] ) ) {
            continue;
        }
        $lines = isset( $r['posts'] ) ? (string) $r['posts'] : '';
        $posts = yc_pr_posts_from_lines( $lines );
        $miss += count( array_filter( array_map( 'trim', preg_split( '/[\r\n,]+/', $lines ) ), 'strlen' ) ) - count( $posts );
        $id    = ( 'new' !== $key && isset( $old[ (int) $key ] ) ) ? (int) $key : $next++;
        $new[ $id ] = array( 'name' => $name, 'fam' => $fam, 'posts' => $posts );
    }
    update_option( 'yc_pr_subs', $new, false );
    // المشاريع المرتبطة بخدمة حُذفت تعود للتصنيف
    foreach ( array_diff( array_keys( $old ), array_keys( $new ) ) as $gone ) {
        yc_pr_sub_unlink( (int) $gone );
    }
    wp_safe_redirect( add_query_arg( array( 'post_type' => 'works', 'page' => 'yc-pr-subs', 'saved' => 1, 'miss' => max( 0, $miss ) ), admin_url( 'edit.php' ) ) );
    exit;
} );

/** حذف قيمة yc_pr_sub من المشاريع عند حذف الخدمة. */
function yc_pr_sub_unlink( $sid ) {
    $ids = get_posts( array( 'post_type' => 'works', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => 'yc_pr_sub', 'meta_value' => (int) $sid ) );
    foreach ( $ids as $id ) {
        update_post_meta( $id, 'yc_pr_sub', 0 );
    }
}

function yc_pr_subs_page() {
    $subs = yc_pr_subs();
    $fams = yc_pr_fam_terms();
    $base = function_exists( 'yc_fw_url' ) && function_exists( 'yc_fw_options' ) ? yc_fw_url( yc_fw_options() ) : home_url( '/works/' );
    $used = array();
    foreach ( get_posts( array( 'post_type' => 'works', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => 'yc_pr_sub' ) ) as $w ) {
        $s = (int) get_post_meta( $w, 'yc_pr_sub', true );
        $used[ $s ] = isset( $used[ $s ] ) ? $used[ $s ] + 1 : 1;
    }
    echo '<div class="wrap" style="max-width:1100px"><h1>الخدمات الفرعية لسابقة الأعمال</h1>';
    if ( isset( $_GET['saved'] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>تم الحفظ.</p></div>';
        if ( ! empty( $_GET['miss'] ) ) {
            echo '<div class="notice notice-warning"><p>' . (int) $_GET['miss'] . ' رابط لم يُتعرَّف على مقاله وتم تجاهله. تأكد أنه رابط مقال منشور على الموقع.</p></div>';
        }
    }
    echo '<p class="description">كل خدمة فرعية تظهر زرًّا تحت تبويبها في صفحة سابقة الأعمال (مثل «تنظيف خزانات» تحت «التنظيف»). اخترها لكل مشروع من صفحة تحرير المشروع.<br>';
    echo '<b>المقالات المرتبطة:</b> الصق رابط المقال (سطر لكل مقال). على هذه المقالات يظهر زر «سابقة أعمالنا» العائم ويفتح الصفحة على هذا التبويب مباشرة.</p>';
    echo '<form method="post">';
    wp_nonce_field( 'yc_pr_subs_save' );
    echo '<input type="hidden" name="yc_pr_subs_save" value="1">';
    echo '<table class="widefat striped"><thead><tr><th style="width:22%">اسم الخدمة الفرعية</th><th style="width:16%">التبويب</th><th>المقالات المرتبطة (رابط في كل سطر)</th><th style="width:9%">مشاريع</th><th style="width:7%">حذف</th></tr></thead><tbody>';
    $row = function ( $key, $s ) use ( $fams, $used, $base ) {
        echo '<tr><td><input type="text" class="widefat" name="subs[' . esc_attr( $key ) . '][name]" value="' . esc_attr( $s['name'] ) . '" placeholder="مثال: تنظيف خزانات">';
        if ( 'new' !== $key ) {
            echo '<br><a href="' . esc_url( yc_pr_sub_url( (int) $key, $base ) ) . '" target="_blank" style="font-size:12px">رابط التبويب ↗</a>';
        }
        echo '</td><td><select name="subs[' . esc_attr( $key ) . '][fam]" class="widefat"><option value="0">—</option>';
        foreach ( $fams as $f ) {
            echo '<option value="' . (int) $f->term_id . '"' . selected( (int) $s['fam'], (int) $f->term_id, false ) . '>' . esc_html( $f->name ) . '</option>';
        }
        echo '</select></td><td>';
        $lines = array();
        foreach ( (array) $s['posts'] as $pid ) {
            $u = get_permalink( $pid );
            if ( $u ) {
                $lines[] = rawurldecode( $u );
            }
        }
        echo '<textarea class="widefat" dir="ltr" rows="' . max( 2, count( $lines ) + 1 ) . '" name="subs[' . esc_attr( $key ) . '][posts]" placeholder="https://altnfez.com/شركة-تنظيف-خزانات-بالرياض/">' . esc_textarea( implode( "\n", $lines ) ) . '</textarea>';
        foreach ( (array) $s['posts'] as $pid ) {
            echo '<div style="font-size:12px;color:#1d6b2f">✓ ' . esc_html( get_the_title( $pid ) ) . '</div>';
        }
        echo '</td><td>' . ( 'new' === $key ? '' : (int) ( isset( $used[ (int) $key ] ) ? $used[ (int) $key ] : 0 ) ) . '</td>';
        echo '<td>' . ( 'new' === $key ? '' : '<input type="checkbox" name="subs[' . esc_attr( $key ) . '][del]" value="1">' ) . '</td></tr>';
    };
    foreach ( $subs as $id => $s ) {
        $row( (int) $id, $s );
    }
    echo '<tr><td colspan="5" style="background:#f0f6ff;font-weight:600">إضافة خدمة فرعية جديدة</td></tr>';
    $row( 'new', array( 'name' => '', 'fam' => 0, 'posts' => array() ) );
    echo '</tbody></table>';
    echo '<p class="submit"><button class="button button-primary button-large">حفظ</button></p></form></div>';
}
