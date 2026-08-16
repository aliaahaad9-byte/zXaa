<?php 

$comment_num = get_comments_number($post->ID);

echo '<div class ="comments_label">';
	echo '<div class="single-post-parent-container-comments">';
		echo '<span><i class="fa fa-comments"></i> تعليقات ('.$comment_num.')</span>';
		echo '<form action="'.home_url().'" data-parent="0" data-id="'.$post->ID.'" class="CommentsFormInner" method="POST" onsubmit="SubmitComment(this);return false;">';
			echo '<div class="alerts"></div>';
			echo '<div class ="Comment_lise">';
				if( is_user_logged_in() ) { global $current_user;
					echo '<input type="text" disabled name="yourname" value="'.$current_user->display_name.'" placeholder="إسمك الكريم *" />';
					echo '<input type="email" disabled name="email" value="'.$current_user->user_email.'" placeholder="بريدك الإلكتروني *" />';
				}else {
					echo '<input type="text" name="yourname" placeholder="إسمك الكريم *" />';
					echo '<input type="email" name="email" placeholder="بريدك الإلكتروني *" />';
				}
			echo '</div>';
			echo '<div class ="textarea_form">';
				echo '<textarea name="comment" placeholder="أكتب التعليق هنا .."></textarea>';
				echo '<button class="hoverable activable" type="submit">إرســال التعليق</button>';
			echo '</div>';
		echo '</form>';
		$arguments = array(

			'status' => 'approve',

			'number' => '15',

			'post_id' => $post->ID,

			'parent'  => 0

		);

		$comments = get_comments($arguments);

		$totalcomments = wp_count_comments($post->ID)->approved;

		if( count($comments) > 0 ) {

			echo '<div class="CommentsList" data-id="'.$post->ID.'">';

				echo '<div class="CommentsList__Title"><span class="comments_number">'.NumberReader($totalcomments).'</span><span>تعليقات  </span><i class="fas fa-arrow-down"></i></div>';

				echo '<ul class="CommentsListInner">';

					foreach ($comments as $comment) {

						$this->Part("CommentItem", array("comment"=>$comment, "post"=>$post));

					}

				echo '</ul>';

			echo '</div>';

		}else {

			echo '<div class="CommentsList" data-id="'.$post->ID.'">';

				echo '<ul class="CommentsListInner">';

					echo '<li class="NoComments">';

						echo '<i class="fal fa-info-circle"></i>';

						echo 'لم يتم إضافة تعليقات لهذا المقال.';

					echo '</li>';

				echo '</ul>';

			echo '</div>';

		}

	echo '</div>';

echo '</div>';