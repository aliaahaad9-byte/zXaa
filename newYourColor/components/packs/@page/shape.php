<?php 
global $post;
$model = get_post_meta($post->ID, 'template', true);
$model = $this->packsPath.'@models/'.$model.'.php';
if( file_exists($model) ) {
	require($model);
}else {
	$path = $CurrentDir.'default.php';
	require($path);
}