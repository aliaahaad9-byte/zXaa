<?
ob_start();
header("Content-Type: application/json");
if( is_user_logged_in() ) {
	global $current_user;
	if( $current_user->ID != $_POST['user'] ) {
		$json['error'] = 'إجراء منتهي الصلاحية';
	}else {
		require($AjaxCenterPath.'UserSubmissions/'.$Params.'.php');
		$html = ob_get_clean();
		$json['output'] = $html;
	}
}else {
	$json['error'] = 'سجّل دخولك اولاََ';
}
echo json_encode($json);