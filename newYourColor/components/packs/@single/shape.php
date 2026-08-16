<?php 
wp_reset_query();
global $post, $current_user;
$path = $CurrentDir.'/'.$post->post_type.'.php';
if( file_exists($path) ) {
	require($path);
}