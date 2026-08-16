<?php  
header("Content-Type: application/json");
ob_start();
$json = array();
if( isset( $_POST['Removed'] ) ){
	$post = get_post($_POST['Removed']);
	if( isset( $post->ID ) ){
		wp_delete_post($post->ID);
		
	}

}
echo json_encode($json);