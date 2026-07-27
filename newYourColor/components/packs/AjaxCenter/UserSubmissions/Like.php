<?
header("Content-Type: application/json");
global $current_user;
$json = array();
//
$post = $_POST['post'];
$type = $_POST['type'];
#
ReviewArticle($type, $post);
#
ob_start();
$term = get_term($term, $taxonomy);
(new ThemeStatic)->Part("social-mini", array("post"=>get_post($post)));
$json['output'] = ob_get_clean();
echo json_encode($json);
die();