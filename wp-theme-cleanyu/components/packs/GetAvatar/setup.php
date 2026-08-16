<?
function get_brightness($hex) { 
	$hex = str_replace('#', '', $hex); 
	$c_r = hexdec(substr($hex, 0, 2)); 
	$c_g = hexdec(substr($hex, 2, 2)); 
	$c_b = hexdec(substr($hex, 4, 2)); 

	return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}
function GetAvatar($user) {
	$color = get_user_meta($user, "color", true); 
	if( empty($color) ) {
		$color = '#005fa3';
		if( is_numeric($user) ) {
			update_user_meta($user, "color", $color);
		}
	}
	if (get_brightness($color) > 130) {
		$textcolor = 'black';
	} else {  
		$textcolor = 'white';
	}
	$user = get_userdata($user);
	$letter = mb_substr($user->display_name, 0, 1);
	return array("color"=>$color, "letter"=>$letter, "textcolor"=>$textcolor);
}