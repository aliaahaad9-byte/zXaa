<?php
$class = ''; $status = 'زائر';
$user = get_userdata((($comment->user_id > 0) ? $comment->user_id : $comment->comment_author));
if( $comment->user_id > 0 ) {
	$user = get_userdata($comment->user_id);
	if( in_array('administrator', $user->roles) or in_array('editor', $user->roles) or in_array('author', $user->roles) ) {
		$status = 'الدعم';
		$class = 'featured';
	}else {
		$status = 'عضو';
	}
}
$AvatarAuthor = get_avatar_url($user);
echo '<li id="comment-'.$comment->comment_ID.'">';
	echo '<div class="CommentContent">';
		echo '<div class="UserAvatar '.$class.'"><img data-loader-src="'.esc_url($AvatarAuthor).'" width="50" height="50"></div>';
		echo '<div class="NameArea">'.esc_html($comment->comment_author).'</div>';
		echo '<div class="CommentDate">';
			$date = 'منذ '.human_time_diff( date('U', strtotime($comment->comment_date)), current_time('timestamp') );
			echo $date;
		echo '</div>';
		echo '<a href="javascript:void(0);" data-comment="'.(int)$comment->comment_ID.'" data-id="'.(int)$post->ID.'" data-name="'.esc_attr($comment->comment_author).'" class="activable" onClick="YCReplyComment(this);">رد</a>';
	echo '</div>';
	echo '<p class="contentcomment">'.nl2br(esc_html($comment->comment_content)).'</p>';
echo '</li>';
$arguments = array(
	'status' => 'approve',
	'number' => '10',
	'post_id' => $post->ID,
	'parent'  => $comment->comment_ID
);
$comments = get_comments($arguments);
if( !empty($comments) ) {
	echo '<ul class="ChildComments">';
		foreach ($comments as $comment) {
			$this->Part("CommentItem", array("comment"=>$comment, "post"=>$post));
		}
	echo '</ul>';
}