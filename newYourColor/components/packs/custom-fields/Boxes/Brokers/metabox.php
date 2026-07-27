<?
if( isset($_GET['action']) and $_GET['action'] == 'edit' ) {
	$post = get_post($_GET['post']);
	echo 'editing';
}else {
	echo 'adding';
}