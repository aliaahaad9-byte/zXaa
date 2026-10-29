<?php
/**
 * إضافة تعليق (AJAX).
 * يستقبل: postID, user_name (أو yourname), email, comment, parent (اختياري), rate (اختياري), website (حقل مخفي ضد السبام).
 * يرجع JSON: {ok:true, status:'approved'|'pending', message, output(html)} أو {error:true, message}.
 */
@ini_set( 'display_errors', '0' );
header( 'Content-type: application/json; charset=utf-8' );

if ( ! function_exists( 'yc_cmt_json' ) ) {
    function yc_cmt_json( $data, $code = 200 ) {
        while ( ob_get_level() > 0 ) {
            ob_end_clean();
        }
        status_header( $code );
        echo wp_json_encode( $data, JSON_UNESCAPED_UNICODE );
        exit;
    }
}

$in = wp_unslash( $_POST );

// حقل فخ للسبام: الزائر الحقيقي لا يراه ولا يملؤه
if ( ! empty( $in['website'] ) ) {
    yc_cmt_json( array( 'ok' => true, 'status' => 'pending', 'message' => 'شكرًا لك! وصل تعليقك وسيظهر بعد مراجعته.', 'output' => '' ) );
}

$post_id = isset( $in['postID'] ) ? absint( $in['postID'] ) : 0;
$post    = $post_id ? get_post( $post_id ) : null;
if ( ! $post || 'publish' !== $post->post_status || ! comments_open( $post_id ) ) {
    yc_cmt_json( array( 'error' => true, 'message' => 'التعليقات غير متاحة على هذه الصفحة.' ), 400 );
}

$logged = is_user_logged_in();
$user   = $logged ? wp_get_current_user() : null;

$name  = $logged ? $user->display_name : sanitize_text_field( isset( $in['user_name'] ) ? $in['user_name'] : ( isset( $in['yourname'] ) ? $in['yourname'] : '' ) );
$email = $logged ? $user->user_email : sanitize_email( isset( $in['email'] ) ? $in['email'] : '' );
$text  = isset( $in['comment'] ) ? trim( wp_strip_all_tags( $in['comment'] ) ) : '';

if ( '' === $name || ! is_email( $email ) || '' === $text ) {
    yc_cmt_json( array( 'error' => true, 'message' => 'يرجى ملء الاسم والبريد الإلكتروني (بشكل صحيح) ونص التعليق.' ), 422 );
}
if ( mb_strlen( $text ) > 2000 || mb_strlen( $name ) > 80 ) {
    yc_cmt_json( array( 'error' => true, 'message' => 'النص أطول من المسموح.' ), 422 );
}

// التعليق الأب (إن كان ردًا) يجب أن يتبع نفس المقال
$parent = isset( $in['parent'] ) ? absint( $in['parent'] ) : 0;
if ( $parent ) {
    $pc = get_comment( $parent );
    if ( ! $pc || (int) $pc->comment_post_ID !== $post_id ) {
        $parent = 0;
    }
}

// تحديد معدل الإرسال: 5 تعليقات كل 10 دقائق لكل عنوان
$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
$key = 'yc_cmt_' . md5( $ip );
$cnt = (int) get_transient( $key );
if ( $cnt >= 5 && ! current_user_can( 'moderate_comments' ) ) {
    yc_cmt_json( array( 'error' => true, 'message' => 'أرسلت عددًا كبيرًا من التعليقات، حاول بعد قليل.' ), 429 );
}

// منع التكرار (نفس الكاتب والنص على نفس المقال) — يحمي من الإرسال المزدوج
$dupe = get_comments( array(
    'post_id'      => $post_id,
    'author_email' => $email,
    'number'       => 5,
    'status'       => 'all',
) );
foreach ( $dupe as $d ) {
    if ( trim( $d->comment_content ) === $text && (int) $d->comment_parent === $parent ) {
        yc_cmt_json( array(
            'ok'      => true,
            'status'  => ( '1' === $d->comment_approved ) ? 'approved' : 'pending',
            'message' => 'تم استلام تعليقك مسبقًا.',
            'output'  => '',
        ) );
    }
}

// المشرفون والمحررون تُنشر تعليقاتهم فورًا، والزوار تذهب للمراجعة
$approved = ( $logged && current_user_can( 'moderate_comments' ) ) ? 1 : 0;

$cid = wp_insert_comment( array(
    'comment_post_ID'      => $post_id,
    'comment_author'       => $name,
    'comment_author_email' => $email,
    'comment_author_IP'    => $ip,
    'comment_content'      => $text,
    'comment_parent'       => $parent,
    'comment_date'         => current_time( 'mysql' ),
    'comment_approved'     => $approved,
    'user_id'              => $logged ? $user->ID : 0,
) );
if ( ! $cid ) {
    yc_cmt_json( array( 'error' => true, 'message' => 'تعذّر حفظ التعليق، حاول مرة أخرى.' ), 500 );
}
set_transient( $key, $cnt + 1, 10 * MINUTE_IN_SECONDS );

// التقييم (اختياري) — رقم من 1 إلى 5 فقط
$rate = isset( $in['rate'] ) ? (int) $in['rate'] : 0;
if ( $rate >= 1 && $rate <= 5 ) {
    update_comment_meta( $cid, 'rating', $rate );
    $k = ( $rate >= 3 ) ? 'wp_review_comments_positive_count' : 'wp_review_comments_negative_count';
    update_post_meta( $post_id, $k, (int) get_post_meta( $post_id, $k, true ) + 1 );
}
if ( isset( $in['customtype'] ) ) {
    update_comment_meta( $cid, 'type', sanitize_text_field( $in['customtype'] ) );
}

// متوسط التقييم: على التعليقات المعتمدة التي لها تقييم فقط (بدون قسمة على صفر)
$sum = 0;
$num = 0;
foreach ( get_comments( array( 'status' => 'approve', 'post_id' => $post_id, 'parent' => 0 ) ) as $c ) {
    $r = (float) get_comment_meta( $c->comment_ID, 'rating', true );
    if ( $r > 0 ) {
        $sum += $r;
        $num++;
    }
}
if ( $num > 0 ) {
    update_post_meta( $post_id, '_wc_average_rating', mb_substr( (string) ( $sum / $num ), 0, 3 ) );
}

$html = '';
if ( $approved ) {
    ob_start();
    ( new ThemeStatic )->Part( 'CommentItem', array( 'comment' => get_comment( $cid ), 'post' => $post ) );
    $html = ob_get_clean();
}

yc_cmt_json( array(
    'ok'         => true,
    'comment_ID' => (int) $cid,
    'status'     => $approved ? 'approved' : 'pending',
    'message'    => $approved ? 'تم نشر تعليقك.' : 'شكرًا لك! وصل تعليقك وسيظهر بعد مراجعته.',
    'output'     => $html,
) );
