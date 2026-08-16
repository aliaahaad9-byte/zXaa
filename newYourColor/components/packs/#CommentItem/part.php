<?php 
$class = ''; 
$status = 'زائر';
$user = ($comment->user_id > 0) ? get_userdata($comment->user_id) : null;

if ($user && $comment->user_id > 0) {
    if (in_array('administrator', $user->roles) || in_array('editor', $user->roles) || in_array('author', $user->roles)) {
        $status = 'الدعم';
        $class = 'featured';
    } else {
        $status = 'عضو';
    }
}

$AvatarAuthor = ($user) ? get_avatar_url($user) : get_avatar_url($comment->comment_author_email);

echo '<li id="comment-'.$comment->comment_ID.'">';
    echo '<div class="CommentContent">';
        echo '<div class="UserAvatar '.$class.'"><i class="fa-regular fa-user"></i></div>';
        echo '<div class="NameArea">'.$comment->comment_author.'</div>';
        echo '<div class="CommentDate">';
            $date = 'منذ '.human_time_diff( date('U', strtotime($comment->comment_date)), current_time('timestamp') );
            echo $date;
        echo '</div>';
        
        if (function_exists('IsSpeed') && IsSpeed() == false) {
            echo '<a href="javascript:void(0);" data-comment="'.$comment->comment_ID.'" data-id="'.$post->ID.'" class="activable" onClick="ReplyComment(this);">رد</a>';
        }
    echo '</div>';
    echo '<p class="contentcomment">'.$comment->comment_content.'</p>';
echo '</li>';

$arguments = array(
    'status' => 'approve',
    'number' => '10',
    'post_id' => $post->ID,
    'parent'  => $comment->comment_ID
);
$comments = get_comments($arguments);

if (!empty($comments)) {
    echo '<ul class="ChildComments">';
    foreach ($comments as $comment) {
        $this->Part("CommentItem", array("comment" => $comment, "post" => $post));
    }
    echo '</ul>';
}
