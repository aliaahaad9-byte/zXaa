<?php
/**
 * سابقة الأعمال — الفيديو والوسائط.
 *
 * طرق إضافة الفيديو لكل مشروع:
 *  - رفع ملف فيديو إلى مكتبة الوسائط (mp4/webm/mov).
 *  - رابط YouTube (عادي أو Shorts) / TikTok / Instagram (Reel/Post) / Vimeo.
 *  - رابط مباشر لملف فيديو (‎.mp4‎ …).
 * الحقل: yc_pr_videos (مصفوفة صفوف).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =========================================================================
 * قراءة الروابط
 * ====================================================================== */

/** يحلل رابط فيديو ويعيد نوعه ومعرّفه ورابط التضمين. */
function yc_pr_parse_video_url( $url ) {
    $url = trim( (string) $url );
    if ( '' === $url ) {
        return null;
    }
    // YouTube
    if ( preg_match( '#(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})#', $url, $m ) ) {
        return array(
            'type'     => 'youtube',
            'id'       => $m[1],
            'embed'    => 'https://www.youtube-nocookie.com/embed/' . $m[1],
            'thumb'    => 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg',
            'portrait' => false !== strpos( $url, '/shorts/' ),
            'url'      => 'https://www.youtube.com/watch?v=' . $m[1],
        );
    }
    // TikTok
    if ( preg_match( '#tiktok\.com/(?:@[^/]+/video|embed(?:/v2)?|v)/(\d{8,25})#', $url, $m ) ) {
        return array(
            'type'     => 'tiktok',
            'id'       => $m[1],
            'embed'    => 'https://www.tiktok.com/embed/v2/' . $m[1],
            'thumb'    => '',
            'portrait' => true,
            'url'      => $url,
        );
    }
    // Instagram
    if ( preg_match( '#instagram\.com/(?:[^/]+/)?(p|reel|reels|tv)/([A-Za-z0-9_-]+)#', $url, $m ) ) {
        $kind = ( 'reels' === $m[1] ) ? 'reel' : $m[1];
        return array(
            'type'     => 'instagram',
            'id'       => $m[2],
            'embed'    => 'https://www.instagram.com/' . $kind . '/' . $m[2] . '/embed/',
            'thumb'    => '',
            'portrait' => 'p' !== $kind,
            'url'      => 'https://www.instagram.com/' . $kind . '/' . $m[2] . '/',
        );
    }
    // Vimeo
    if ( preg_match( '#vimeo\.com/(?:video/)?(\d{5,12})#', $url, $m ) ) {
        return array(
            'type'     => 'vimeo',
            'id'       => $m[1],
            'embed'    => 'https://player.vimeo.com/video/' . $m[1],
            'thumb'    => '',
            'portrait' => false,
            'url'      => 'https://vimeo.com/' . $m[1],
        );
    }
    // ملف مباشر
    if ( preg_match( '#^https?://[^\s]+\.(mp4|webm|mov|m4v|ogv)(\?.*)?$#i', $url ) ) {
        return array(
            'type'     => 'file',
            'id'       => '',
            'embed'    => '',
            'thumb'    => '',
            'portrait' => false,
            'url'      => $url,
        );
    }
    return null;
}

/** «0:24» أو «1:02:05» ← ثوانٍ. */
function yc_pr_duration_to_seconds( $raw ) {
    $raw = trim( (string) $raw );
    if ( '' === $raw ) {
        return 0;
    }
    if ( ctype_digit( $raw ) ) {
        return (int) $raw;
    }
    $parts = array_map( 'intval', explode( ':', $raw ) );
    $s     = 0;
    foreach ( $parts as $p ) {
        $s = $s * 60 + $p;
    }
    return $s;
}

function yc_pr_seconds_label( $s ) {
    $s = (int) $s;
    if ( $s <= 0 ) {
        return '';
    }
    $h = intdiv( $s, 3600 );
    $m = intdiv( $s % 3600, 60 );
    $r = $s % 60;
    return $h ? sprintf( '%d:%02d:%02d', $h, $m, $r ) : sprintf( '%d:%02d', $m, $r );
}

/** مدة بصيغة ISO 8601 (PT1M24S) لبيانات VideoObject. */
function yc_pr_seconds_iso( $s ) {
    $s = (int) $s;
    if ( $s <= 0 ) {
        return '';
    }
    $h = intdiv( $s, 3600 );
    $m = intdiv( $s % 3600, 60 );
    $r = $s % 60;
    return 'PT' . ( $h ? $h . 'H' : '' ) . ( $m ? $m . 'M' : '' ) . ( $r || ( ! $h && ! $m ) ? $r . 'S' : '' );
}

/* =========================================================================
 * قراءة فيديوهات المشروع (جاهزة للعرض والـ schema)
 * ====================================================================== */
function yc_pr_videos( $post_id ) {
    $rows = get_post_meta( $post_id, 'yc_pr_videos', true );
    if ( ! is_array( $rows ) ) {
        return array();
    }
    $title    = get_the_title( $post_id );
    $fallback = (string) get_the_post_thumbnail_url( $post_id, 'large' );
    $out      = array();

    foreach ( array_values( $rows ) as $i => $r ) {
        $r = wp_parse_args( (array) $r, array( 'att' => 0, 'url' => '', 'poster' => 0, 'duration' => '', 'caption' => '', 'thumb_url' => '' ) );
        $v = array(
            'type'     => '',
            'src'      => '',
            'mime'     => 'video/mp4',
            'embed'    => '',
            'page_url' => '',
            'thumb'    => '',
            'portrait' => false,
            'seconds'  => yc_pr_duration_to_seconds( $r['duration'] ),
            'caption'  => '' !== trim( $r['caption'] ) ? trim( $r['caption'] ) : $title . ( $i ? ' — ' . ( $i + 1 ) : '' ),
            'width'    => 0,
            'height'   => 0,
        );

        if ( $r['att'] && wp_get_attachment_url( (int) $r['att'] ) ) {
            $v['type'] = 'upload';
            $v['src']  = (string) wp_get_attachment_url( (int) $r['att'] );
            $v['mime'] = (string) get_post_mime_type( (int) $r['att'] );
            $meta      = wp_get_attachment_metadata( (int) $r['att'] );
            if ( is_array( $meta ) ) {
                if ( ! $v['seconds'] && ! empty( $meta['length'] ) ) {
                    $v['seconds'] = (int) $meta['length'];
                }
                $v['width']    = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
                $v['height']   = isset( $meta['height'] ) ? (int) $meta['height'] : 0;
                $v['portrait'] = $v['height'] > $v['width'] && $v['width'] > 0;
            }
        } else {
            $p = yc_pr_parse_video_url( $r['url'] );
            if ( ! $p ) {
                continue;
            }
            $v['type']     = $p['type'];
            $v['embed']    = $p['embed'];
            $v['page_url'] = $p['url'];
            $v['portrait'] = $p['portrait'];
            $v['thumb']    = $p['thumb'];
            if ( 'file' === $p['type'] ) {
                $v['src']  = $p['url'];
                $v['mime'] = 'video/' . strtolower( pathinfo( wp_parse_url( $p['url'], PHP_URL_PATH ), PATHINFO_EXTENSION ) );
                if ( 'video/mov' === $v['mime'] ) {
                    $v['mime'] = 'video/quicktime';
                }
                if ( 'video/m4v' === $v['mime'] ) {
                    $v['mime'] = 'video/mp4';
                }
            }
        }

        // الصورة المصغّرة: صورة مختارة ← صورة محفوظة من المنصة ← صورة المنصة ← الصورة البارزة
        if ( $r['poster'] && wp_get_attachment_image_url( (int) $r['poster'], 'large' ) ) {
            $v['thumb'] = (string) wp_get_attachment_image_url( (int) $r['poster'], 'large' );
        } elseif ( '' !== $r['thumb_url'] ) {
            $v['thumb'] = $r['thumb_url'];
        }
        if ( '' === $v['thumb'] ) {
            $v['thumb'] = $fallback;
        }
        $v['label'] = yc_pr_seconds_label( $v['seconds'] );
        $out[]      = $v;
    }
    return $out;
}

/** كل صور المشروع (البارزة + المراحل) بدون تكرار: [[url, alt], ...]. */
function yc_pr_images( $post_id ) {
    $title = get_the_title( $post_id );
    $seen  = array();
    $out   = array();
    $add   = function ( $id, $alt ) use ( &$seen, &$out ) {
        $id = (int) $id;
        if ( ! $id || isset( $seen[ $id ] ) ) {
            return;
        }
        $url = wp_get_attachment_image_url( $id, 'full' );
        if ( ! $url ) {
            return;
        }
        $seen[ $id ] = true;
        $a           = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
        $out[]       = array( 'url' => $url, 'alt' => '' !== $a ? $a : $alt, 'id' => $id );
    };
    $add( get_post_thumbnail_id( $post_id ), $title );
    foreach ( yc_pr_sections() as $s ) {
        foreach ( yc_pr_ids( get_post_meta( $post_id, $s[2], true ) ) as $id ) {
            $add( $id, $title . ' — ' . $s[3] );
        }
    }
    if ( ! $out ) {
        $u = (string) get_post_meta( $post_id, 'yc_pr_hero_url', true );
        if ( $u ) {
            $out[] = array( 'url' => $u, 'alt' => $title, 'id' => 0 );
        }
    }
    return $out;
}

/** ملخص الوسائط للبطاقة: الصورة، الشارة، وهل فيه فيديو. */
function yc_pr_media_summary( $post_id ) {
    $videos = yc_pr_videos( $post_id );
    $images = yc_pr_images( $post_id );
    $nv     = count( $videos );
    $ni     = count( $images );

    if ( $nv ) {
        $badge = 1 === $nv ? 'فيديو' . ( $videos[0]['label'] ? ' ' . $videos[0]['label'] : '' ) : ( 2 === $nv ? 'فيديوهان' : $nv . ' فيديوهات' );
    } elseif ( $ni ) {
        $badge = 1 === $ni ? 'صورة' : ( 2 === $ni ? 'صورتان' : ( $ni <= 10 ? $ni . ' صور' : $ni . ' صورة' ) );
    } else {
        $badge = '';
    }
    $thumb = '';
    if ( $nv && $videos[0]['thumb'] ) {
        $thumb = $videos[0]['thumb'];
    } elseif ( $ni ) {
        $thumb = $images[0]['url'];
    }
    return array(
        'videos'   => $videos,
        'images'   => $images,
        'is_video' => $nv > 0,
        'badge'    => $badge,
        'thumb'    => $thumb,
        // فيديو مرفوع بلا أي صورة: نعرض أول إطار منه
        'frame'    => ( $nv && ! $thumb && $videos[0]['src'] ) ? $videos[0]['src'] : '',
    );
}

/* =========================================================================
 * العرض
 * ====================================================================== */

/** مشغّل فيديو واحد. */
function yc_pr_player( $v, $eager = false ) {
    $cls = 'yc-vid yc-vid--' . $v['type'] . ( $v['portrait'] ? ' is-portrait' : ' is-landscape' );
    $h   = '<div class="' . esc_attr( $cls ) . '">';
    switch ( $v['type'] ) {
        case 'upload':
        case 'file':
            $h .= '<video controls playsinline preload="' . ( $eager ? 'metadata' : 'none' ) . '"'
                . ( $v['thumb'] ? ' poster="' . esc_url( $v['thumb'] ) . '"' : '' )
                . ( $v['width'] && $v['height'] ? ' width="' . (int) $v['width'] . '" height="' . (int) $v['height'] . '"' : '' )
                . ' aria-label="' . esc_attr( $v['caption'] ) . '">'
                . '<source src="' . esc_url( $v['src'] ) . '" type="' . esc_attr( $v['mime'] ) . '">'
                . '</video>';
            break;
        case 'youtube':
        case 'vimeo':
            // واجهة خفيفة: صورة + زر تشغيل، ويُحمَّل المشغّل عند الضغط فقط
            $src = $v['embed'] . ( false === strpos( $v['embed'], '?' ) ? '?' : '&' ) . 'autoplay=1&rel=0&playsinline=1';
            $h  .= '<button type="button" class="yc-vid__lite" data-src="' . esc_url( $src ) . '" aria-label="تشغيل الفيديو: ' . esc_attr( $v['caption'] ) . '">';
            if ( $v['thumb'] ) {
                $h .= '<img src="' . esc_url( $v['thumb'] ) . '" alt="" loading="' . ( $eager ? 'eager' : 'lazy' ) . '">';
            }
            $h .= '<span class="yc-vid__play" aria-hidden="true"></span></button>';
            break;
        case 'tiktok':
        case 'instagram':
            $h .= '<iframe src="' . esc_url( $v['embed'] ) . '" title="' . esc_attr( $v['caption'] ) . '" loading="lazy" allow="encrypted-media; fullscreen; picture-in-picture" allowfullscreen scrolling="no"></iframe>';
            break;
    }
    return $h . '</div>';
}

/** سكربت تشغيل واجهة YouTube/Vimeo الخفيفة (مرة واحدة في الصفحة). */
function yc_pr_player_js() {
    static $done = false;
    if ( $done ) {
        return '';
    }
    $done = true;
    return "<script>document.addEventListener('click',function(e){var b=e.target.closest&&e.target.closest('.yc-vid__lite');if(!b){return;}var f=document.createElement('iframe');f.src=b.getAttribute('data-src');f.title=b.getAttribute('aria-label')||'';f.allow='autoplay; encrypted-media; picture-in-picture; fullscreen';f.allowFullscreen=true;b.parentNode.replaceChild(f,b);});</script>";
}

/* =========================================================================
 * البيانات المنظّمة
 * ====================================================================== */
function yc_pr_video_schema( $v, $post ) {
    if ( '' === $v['thumb'] ) {
        return null; // thumbnailUrl مطلوب عند جوجل
    }
    $desc = yc_pr_paras( get_post_meta( $post->ID, 'yc_pr_challenge', true ) );
    $o    = array(
        '@type'        => 'VideoObject',
        'name'         => $v['caption'],
        'description'  => $desc ? wp_trim_words( $desc[0], 45, '…' ) : get_the_title( $post ),
        'thumbnailUrl' => array( $v['thumb'] ),
        'uploadDate'   => get_post_time( 'c', true, $post ),
        'inLanguage'   => 'ar',
    );
    if ( $v['src'] ) {
        $o['contentUrl'] = $v['src'];
    }
    if ( $v['embed'] ) {
        $o['embedUrl'] = $v['embed'];
    }
    if ( $v['page_url'] && 'file' !== $v['type'] ) {
        $o['url'] = $v['page_url'];
    }
    if ( $v['seconds'] ) {
        $o['duration'] = yc_pr_seconds_iso( $v['seconds'] );
    }
    if ( $v['width'] && $v['height'] ) {
        $o['width']  = $v['width'];
        $o['height'] = $v['height'];
    }
    $o['publisher'] = array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) );
    return $o;
}

function yc_pr_image_schema( $img, $post ) {
    $site = get_bloginfo( 'name' );
    return array(
        '@type'           => 'ImageObject',
        'contentUrl'      => $img['url'],
        'url'             => $img['url'],
        'caption'         => $img['alt'],
        'name'            => $img['alt'],
        'creator'         => array( '@type' => 'Organization', 'name' => $site, 'url' => home_url( '/' ) ),
        'creditText'      => $site,
        'copyrightNotice' => '© ' . $site,
        'acquireLicensePage' => get_permalink( $post ),
        'uploadDate'      => get_post_time( 'c', true, $post ),
    );
}

/* =========================================================================
 * لوحة التحكم: حقول الفيديو داخل «تفاصيل المشروع»
 * ====================================================================== */
function yc_pr_video_row( $i, $r ) {
    $r   = wp_parse_args( (array) $r, array( 'att' => 0, 'url' => '', 'poster' => 0, 'duration' => '', 'caption' => '', 'thumb_url' => '' ) );
    $n   = 'yc_pr_videos[' . $i . ']';
    $att = (int) $r['att'];
    $af  = $att ? basename( (string) get_attached_file( $att ) ) : '';
    $pst = (int) $r['poster'] ? wp_get_attachment_image_url( (int) $r['poster'], 'thumbnail' ) : ( $r['thumb_url'] ? $r['thumb_url'] : '' );
    ob_start();
    ?>
    <div class="yc-pr-vrow">
        <div class="yc-pr-vgrid">
            <p class="yc-pr-vsrc">
                <label>فيديو مرفوع على الموقع</label>
                <input type="hidden" class="yc-pr-vatt" name="<?php echo esc_attr( $n ); ?>[att]" value="<?php echo $att ? (int) $att : ''; ?>">
                <span class="yc-pr-vfile"><?php echo $af ? esc_html( $af ) : 'لم يُختر ملف'; ?></span>
                <button type="button" class="button yc-pr-vpick">رفع / اختيار فيديو</button>
                <button type="button" class="button-link yc-pr-vclear"<?php echo $att ? '' : ' hidden'; ?>>إزالة الملف</button>
            </p>
            <p>
                <label>أو رابط الفيديو</label>
                <input type="url" dir="ltr" class="yc-pr-vurl" name="<?php echo esc_attr( $n ); ?>[url]" value="<?php echo esc_attr( $r['url'] ); ?>" placeholder="YouTube / TikTok / Instagram / Vimeo / رابط ‎.mp4‎">
                <span class="description yc-pr-vtype"></span>
            </p>
            <p>
                <label>صورة الغلاف (اختياري)</label>
                <input type="hidden" class="yc-pr-vposter" name="<?php echo esc_attr( $n ); ?>[poster]" value="<?php echo (int) $r['poster'] ? (int) $r['poster'] : ''; ?>">
                <input type="hidden" name="<?php echo esc_attr( $n ); ?>[thumb_url]" value="<?php echo esc_attr( $r['thumb_url'] ); ?>">
                <span class="yc-pr-vthumb"><?php echo $pst ? '<img src="' . esc_url( $pst ) . '" alt="">' : ''; ?></span>
                <button type="button" class="button yc-pr-vpposter">اختيار صورة</button>
            </p>
            <p>
                <label>المدة (اختياري)</label>
                <input type="text" dir="ltr" name="<?php echo esc_attr( $n ); ?>[duration]" value="<?php echo esc_attr( $r['duration'] ); ?>" placeholder="0:24">
            </p>
        </div>
        <p><label>عنوان / وصف الفيديو (اختياري)</label><input type="text" name="<?php echo esc_attr( $n ); ?>[caption]" value="<?php echo esc_attr( $r['caption'] ); ?>" placeholder="يُستخدم عنوان المشروع إن تُرك فارغًا"></p>
        <a href="#" class="yc-pr-vdel">حذف هذا الفيديو</a>
    </div>
    <?php
    return ob_get_clean();
}

add_action( 'yc_pr_box_top', function ( $post ) {
    $rows = get_post_meta( $post->ID, 'yc_pr_videos', true );
    $rows = is_array( $rows ) ? array_values( $rows ) : array();
    echo '<h3>الفيديوهات</h3>';
    echo '<p class="description">أول فيديو يظهر أعلى صفحة المشروع مكان الصورة، وتظهر عليه علامة التشغيل في صفحة الأعمال. ارفع ملفًا من جهازك أو الصق رابطًا من YouTube أو TikTok أو Instagram أو Vimeo. صورة الغلاف تُجلب تلقائيًا من YouTube وTikTok، وفي Instagram اختر صورة الغلاف بنفسك.</p>';
    echo '<div id="yc-pr-videos" data-next="' . count( $rows ) . '">';
    foreach ( $rows as $i => $r ) {
        echo yc_pr_video_row( $i, $r ); // phpcs:ignore
    }
    echo '</div>';
    echo '<p><button type="button" class="button button-secondary" id="yc-pr-vadd">+ إضافة فيديو</button></p>';
    echo '<script type="text/template" id="yc-pr-vtpl">' . yc_pr_video_row( '__i__', array() ) . '</script>'; // phpcs:ignore
} );

/* حفظ الفيديوهات + جلب صورة الغلاف من TikTok تلقائيًا */
add_action( 'save_post_works', function ( $post_id ) {
    if ( ! isset( $_POST['yc_pr_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['yc_pr_nonce'] ) ), 'yc_pr_save' ) ) {
        return;
    }
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    $in   = isset( $_POST['yc_pr_videos'] ) && is_array( $_POST['yc_pr_videos'] ) ? wp_unslash( $_POST['yc_pr_videos'] ) : array();
    $rows = array();
    foreach ( $in as $r ) {
        $att = isset( $r['att'] ) ? absint( $r['att'] ) : 0;
        if ( $att && 0 !== strpos( (string) get_post_mime_type( $att ), 'video/' ) ) {
            $att = 0;
        }
        $url = isset( $r['url'] ) ? esc_url_raw( trim( $r['url'] ) ) : '';
        // روابط TikTok المختصرة (vm.tiktok.com) تُحوَّل إلى الرابط الكامل
        if ( $url && preg_match( '#^https?://(vm|vt)\.tiktok\.com/#', $url ) ) {
            $res = wp_remote_head( $url, array( 'redirection' => 0, 'timeout' => 8 ) );
            if ( ! is_wp_error( $res ) ) {
                $final = wp_remote_retrieve_header( $res, 'location' );
                if ( $final ) {
                    $url = esc_url_raw( $final );
                }
            }
        }
        $parsed = $url ? yc_pr_parse_video_url( $url ) : null;
        if ( ! $att && ! $parsed ) {
            continue;
        }
        $row = array(
            'att'       => $att,
            'url'       => $att ? '' : $url,
            'poster'    => isset( $r['poster'] ) ? absint( $r['poster'] ) : 0,
            'duration'  => isset( $r['duration'] ) ? preg_replace( '/[^\d:]/', '', $r['duration'] ) : '',
            'caption'   => isset( $r['caption'] ) ? sanitize_text_field( $r['caption'] ) : '',
            'thumb_url' => isset( $r['thumb_url'] ) ? esc_url_raw( $r['thumb_url'] ) : '',
        );
        // رابط تغيّر ← نُسقط صورة المنصة القديمة
        if ( $parsed && $row['thumb_url'] && false === strpos( $row['thumb_url'], (string) $parsed['id'] ) && 'tiktok' !== $parsed['type'] ) {
            $row['thumb_url'] = '';
        }
        if ( ! $row['poster'] && $parsed && 'tiktok' === $parsed['type'] && ! $row['thumb_url'] ) {
            $row['poster'] = yc_pr_tiktok_thumb( $url, $post_id );
        }
        $rows[] = $row;
    }
    update_post_meta( $post_id, 'yc_pr_videos', $rows );
}, 20 );

/**
 * صورة غلاف TikTok: روابط صور TikTok تنتهي صلاحيتها، فنحفظ الصورة في مكتبة الوسائط.
 * تعيد رقم المرفق أو 0.
 */
function yc_pr_tiktok_thumb( $url, $post_id ) {
    $res = wp_remote_get( 'https://www.tiktok.com/oembed?url=' . rawurlencode( $url ), array( 'timeout' => 8 ) );
    if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
        return 0;
    }
    $data = json_decode( wp_remote_retrieve_body( $res ), true );
    if ( empty( $data['thumbnail_url'] ) ) {
        return 0;
    }
    if ( ! function_exists( 'media_sideload_image' ) ) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
    $id = media_sideload_image( $data['thumbnail_url'], $post_id, get_the_title( $post_id ), 'id' );
    return is_wp_error( $id ) ? 0 : (int) $id;
}

/* سكربت لوحة التحكم للفيديو */
add_action( 'admin_footer', function () {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'works' !== $screen->post_type || 'post' !== $screen->base ) {
        return;
    }
    ?>
<style>
.yc-pr-vrow{background:#f6f7f7;border:1px solid #e0e0e0;border-radius:10px;padding:12px 14px;margin-bottom:10px}
.yc-pr-vgrid{display:grid;grid-template-columns:1.2fr 1.5fr 1fr .6fr;gap:0 14px;align-items:start}
.yc-pr-vfile{display:block;margin:4px 0 6px;color:#50575e;font-size:12.5px;word-break:break-all}
.yc-pr-vthumb img{display:block;width:72px;height:72px;object-fit:cover;border-radius:8px;margin:4px 0 6px}
.yc-pr-vtype{display:block;margin-top:4px;color:#0f7a3b;font-weight:600}
.yc-pr-vdel{color:#b32d2e;text-decoration:none;font-size:12.5px}
@media(max-width:1100px){.yc-pr-vgrid{grid-template-columns:1fr 1fr}}
</style>
<script>
jQuery(function($){
  var $box=$('#yc-pr-videos'); if(!$box.length){return;}
  var re=[[/youtu\.?be/i,'YouTube ✓'],[/tiktok\.com/i,'TikTok ✓'],[/instagram\.com/i,'Instagram ✓'],[/vimeo\.com/i,'Vimeo ✓'],[/\.(mp4|webm|mov|m4v|ogv)(\?|$)/i,'ملف فيديو ✓']];
  function detect($i){ var v=$.trim($i.val()), t=''; for(var k=0;k<re.length;k++){ if(re[k][0].test(v)){ t=re[k][1]; break; } } if(v&&!t){ t='رابط غير مدعوم'; } $i.siblings('.yc-pr-vtype').text(t).css('color',t==='رابط غير مدعوم'?'#b32d2e':'#0f7a3b'); }
  $box.find('.yc-pr-vurl').each(function(){ detect($(this)); });
  $box.on('input','.yc-pr-vurl',function(){ detect($(this)); });
  $('#yc-pr-vadd').on('click',function(e){ e.preventDefault(); var i=+$box.attr('data-next'); $box.attr('data-next',i+1); $box.append($($('#yc-pr-vtpl').html().replace(/__i__/g,i))); });
  $box.on('click','.yc-pr-vdel',function(e){ e.preventDefault(); if(confirm('حذف هذا الفيديو؟')){ $(this).closest('.yc-pr-vrow').remove(); } });
  $box.on('click','.yc-pr-vclear',function(e){ e.preventDefault(); var $r=$(this).closest('.yc-pr-vrow'); $r.find('.yc-pr-vatt').val(''); $r.find('.yc-pr-vfile').text('لم يُختر ملف'); $(this).prop('hidden',true); });
  $box.on('click','.yc-pr-vpick',function(e){
    e.preventDefault(); if(!window.wp||!wp.media){return;}
    var $r=$(this).closest('.yc-pr-vrow'), f=wp.media({title:'اختر فيديو المشروع',multiple:false,library:{type:'video'}});
    f.on('select',function(){ var a=f.state().get('selection').first().toJSON(); $r.find('.yc-pr-vatt').val(a.id); $r.find('.yc-pr-vfile').text(a.filename||a.title); $r.find('.yc-pr-vclear').prop('hidden',false); });
    f.open();
  });
  $box.on('click','.yc-pr-vpposter',function(e){
    e.preventDefault(); if(!window.wp||!wp.media){return;}
    var $r=$(this).closest('.yc-pr-vrow'), f=wp.media({title:'صورة غلاف الفيديو',multiple:false,library:{type:'image'}});
    f.on('select',function(){ var a=f.state().get('selection').first().toJSON(), s=(a.sizes&&a.sizes.thumbnail)?a.sizes.thumbnail.url:a.url; $r.find('.yc-pr-vposter').val(a.id); $r.find('.yc-pr-vthumb').html($('<img alt="">').attr('src',s)); });
    f.open();
  });
});
</script>
    <?php
} );
