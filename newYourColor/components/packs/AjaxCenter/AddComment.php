<?php
/**
 * استقبال التعليقات من نموذج المقال (SubmitComment في setup.js).
 *
 * كان الكود القديم ينتظر حقولًا باسم user_name و comment بينما النموذج يرسل
 * yourname و message، فكان يرفض كل تعليق بصمت؛ وكان يقسم على عدد التعليقات
 * المعتمدة فيتوقف بخطأ فادح عند أول تعليق (القسمة على صفر في PHP 8).
 */
header( 'Content-Type: application/json; charset=utf-8' );

$json = array();

try {
	$field = function ( $keys ) {
		foreach ( (array) $keys as $k ) {
			if ( isset( $_POST[ $k ] ) && '' !== trim( (string) $_POST[ $k ] ) ) {
				return wp_unslash( $_POST[ $k ] );
			}
		}
		return '';
	};

	$post_id = absint( $field( 'postID' ) );
	$parent  = absint( $field( 'parent' ) );
	$content = sanitize_textarea_field( $field( array( 'message', 'comment' ) ) );
	$rate    = absint( $field( 'rate' ) );
	$rate    = ( $rate >= 1 && $rate <= 5 ) ? $rate : 0;
	$type    = sanitize_text_field( $field( array( 'type', 'customtype' ) ) );

	if ( is_user_logged_in() ) {
		$user  = wp_get_current_user();
		$name  = $user->display_name;
		$email = $user->user_email;
	} else {
		$user  = null;
		$name  = sanitize_text_field( $field( array( 'yourname', 'user_name' ) ) );
		$email = sanitize_email( $field( 'email' ) );
	}

	$post = $post_id ? get_post( $post_id ) : null;

	if ( ! $post || 'publish' !== $post->post_status ) {
		$json['error']   = true;
		$json['message'] = 'تعذّر العثور على المقال.';
	} elseif ( '' === $name || '' === $content ) {
		$json['error']   = true;
		$json['message'] = 'يرجى كتابة الاسم والتعليق.';
	} elseif ( ! is_email( $email ) ) {
		$json['error']   = true;
		$json['message'] = 'يرجى كتابة بريد إلكتروني صحيح.';
	} elseif ( mb_strlen( $content ) > 3000 ) {
		$json['error']   = true;
		$json['message'] = 'التعليق طويل جدًا.';
	} else {
		// منع الإغراق: تعليق واحد كل 30 ثانية من نفس العنوان
		$ip        = isset( $_SERVER['REMOTE_ADDR'] ) ? preg_replace( '/[^0-9a-fA-F:.]/', '', $_SERVER['REMOTE_ADDR'] ) : '';
		$flood_key = 'yc_comment_' . md5( $ip );
		if ( $ip && get_transient( $flood_key ) && ! current_user_can( 'moderate_comments' ) ) {
			$json['error']   = true;
			$json['message'] = 'انتظر قليلًا قبل إرسال تعليق آخر.';
		} else {
			// تعليقات الزوار تنتظر المراجعة؛ تعليقات المشرفين تُنشر مباشرة
			$approved = current_user_can( 'moderate_comments' ) ? 1 : 0;

			$data = array(
				'comment_post_ID'      => $post_id,
				'comment_author'       => $name,
				'comment_author_email' => $email,
				'comment_author_IP'    => $ip,
				'comment_agent'        => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 254 ) : '',
				'comment_content'      => $content,
				'comment_date'         => current_time( 'mysql' ),
				'comment_date_gmt'     => current_time( 'mysql', 1 ),
				'comment_approved'     => $approved,
				'comment_parent'       => $parent,
				'user_id'              => $user ? $user->ID : 0,
			);

			$cid = wp_insert_comment( $data );

			if ( ! $cid ) {
				$json['error']   = true;
				$json['message'] = 'تعذّر حفظ التعليق، حاول مرة أخرى.';
			} else {
				if ( $ip ) {
					set_transient( $flood_key, 1, 30 );
				}
				if ( $rate ) {
					update_comment_meta( $cid, 'rating', $rate );
					$counter = ( $rate >= 3 ) ? 'wp_review_comments_positive_count' : 'wp_review_comments_negative_count';
					update_post_meta( $post_id, $counter, (int) get_post_meta( $post_id, $counter, true ) + 1 );
				}
				if ( '' !== $type ) {
					update_comment_meta( $cid, 'type', $type );
				}

				// متوسط التقييم من التعليقات المعتمدة التي فيها تقييم فقط (بدون قسمة على صفر)
				$sum   = 0;
				$rated = 0;
				foreach ( get_comments( array( 'status' => 'approve', 'post_id' => $post_id ) ) as $c ) {
					$r = (int) get_comment_meta( $c->comment_ID, 'rating', true );
					if ( $r > 0 ) {
						$sum += $r;
						$rated++;
					}
				}
				if ( $rated > 0 ) {
					update_post_meta( $post_id, '_wc_average_rating', round( $sum / $rated, 1 ) );
				}

				if ( ! $approved ) {
					wp_notify_moderator( $cid );
				}

				$json['comment_ID'] = $cid;
				$json['approved']   = $approved;
				$json['output']     = '<div class="alert alert-success comment-pending-notice" style="padding:14px 16px;margin-bottom:12px;border-radius:14px;background:#e9f6ee;color:#1f6f32;font-weight:600">'
					. ( $approved ? 'تم نشر تعليقك.' : 'شكرًا لك، تم إرسال تعليقك وسيظهر بعد المراجعة.' )
					. '</div>';
			}
		}
	}
} catch ( \Throwable $e ) {
	$json = array(
		'error'   => true,
		'message' => 'حدث خطأ غير متوقع، حاول مرة أخرى.',
	);
}

echo wp_json_encode( $json );
