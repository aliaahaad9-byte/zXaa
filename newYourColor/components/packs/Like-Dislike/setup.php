<?php 
function ReviewArticle($type, $id) {
	global $current_user;
	$post = $id;
	if( $type == 'like' ) {
		$meta_total = 'likes';
		$other_type = 'dislike';
		$other_meta_total = 'dislikes';
	}else if( $type == 'dislike' ) {
		$meta_total = 'dislikes';
		$other_meta_total = 'likes';
		$other_type = 'like';
	}else {
		return false;
	}
	$counts = (INT) get_post_meta($post, $meta_total, true);
	$reviews_article = (is_array(get_post_meta($post, "reviews_article", true))) ? get_post_meta($post, "reviews_article", true) : array();
	if( isset($reviews_article[$current_user->ID]) and $reviews_article[$current_user->ID] == $other_type ) {
		$counts_other = (INT) get_post_meta($post, $other_meta_total, true);
		unset($reviews_article[$current_user->ID]);
		$counts_other--;
		update_post_meta($post, $other_meta_total, $counts_other);
	}
	if( isset($reviews_article[$current_user->ID]) and $reviews_article[$current_user->ID] == $type ) {
		unset($reviews_article[$current_user->ID]);
		$counts--;
	}else {
		$reviews_article[$current_user->ID] = $type;
		$counts++;
	}
	update_post_meta($post, "reviews_article", $reviews_article);
	update_post_meta($post, $meta_total, $counts);
	return true;
}