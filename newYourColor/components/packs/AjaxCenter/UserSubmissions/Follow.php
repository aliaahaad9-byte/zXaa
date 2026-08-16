<?php 
header("Content-Type: application/json");
global $current_user;
$json = array();
//
$taxonomy = $_POST['tax'];
$term = $_POST['t'];
#
$following = (is_array($current_user->following)) ? $current_user->following : array();
if( !isset($following[$taxonomy]) ) $following[$taxonomy] = array();
#
if( isset($following[$taxonomy][$term]) ) {
	unset($following[$taxonomy][$term]);
}else {
	$following[$taxonomy][$term] = time();
}
#
update_user_meta($current_user->ID, "following", $following);
#
ob_start();
$term = get_term($term, $taxonomy);
(new ThemeStatic)->Part("Follow", array("term"=>$term));
$json['button'] = ob_get_clean();
echo json_encode($json);
die();