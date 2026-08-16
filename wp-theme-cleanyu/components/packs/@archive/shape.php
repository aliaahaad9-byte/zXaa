<?
global $current_user;
$obj = get_queried_object();
$path = $CurrentDir.$obj->taxonomy.'.php';
update_term_meta($obj->term_id, 'views', (INT) get_term_meta($obj->term_id, 'views', true) + 1);
//
if( file_exists($path) ) {
	require($CurrentDir.$obj->taxonomy.'.php');
}else {
	require($CurrentDir.'default.php');
}