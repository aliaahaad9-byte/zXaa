<?
header("Content-Type: application/json");
$json = array();
$term = $_POST['term'];
$hometabsid = $_POST['hometabsid'];
$hometab = $_POST['hometab'];
$not_in = $_POST['not_in'];
$UniqId = uniqid();
ob_start();
$arguments = array(
	"post_type"		=> 'post',
	"posts_per_page"=> 20,
	"post__not_in"	=> array($not_in),
	"cat"	=> $term
);

if( $hometab == 'trendingcat' ) {
	$arguments['meta_key'] = 'trending';
	$arguments['orderby'] = 'meta_value_num';
}else if( $hometab == 'datecat' ) {
	$arguments['orderby'] = 'date';
}if( $hometab == 'trending' ) {
    $arguments['meta_key'] = 'trending';
    $arguments['orderby'] = 'meta_value_num';
}else if( $hometab == 'last_update' ) {
    $arguments['order'] = 'DESC';
}else if( $hometab == 'rand' ) {
    $arguments['orderby'] = 'rand';
}else if( $hometab == 'old' ) {
    $arguments['order'] = 'ASC';
    $arguments['orderby'] = 'meta_value';
}else if( $hometab == 'randcat' ) {
     $arguments['orderby'] = 'rand';
    
}

(new ThemeStatic)->Part('Posts',array('AutoLoadmore'=>false,'UniqId'=>$UniqId,'arguments'=>$arguments,'term'=>$term,'ajax'=>true));
$html = ob_get_clean();

$JsonArguments = json_decode($JsonArguments,true);


$html = explode('<CutAjax>',$html)[0];
if(empty($html)){
    $html = '<div class="NothingFoundFilter">';
        $html .= '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g> <g> <path d="M174.829,187.317c-13.772,0-24.976,11.204-24.976,24.976s11.204,24.976,24.976,24.976 c13.767,0,24.971-11.202,24.976-24.976C199.805,198.521,188.603,187.317,174.829,187.317z"></path> </g> </g> <g> <g> <path d="M337.171,187.317c-13.772,0-24.976,11.204-24.976,24.976s11.204,24.976,24.976,24.976 c13.767,0,24.971-11.202,24.976-24.976C362.146,198.521,350.945,187.317,337.171,187.317z"></path> </g> </g> <g> <g> <path d="M256,0C114.84,0,0,114.842,0,256s114.84,256,256,256s256-114.842,256-256S397.16,0,256,0z M256,474.537 c-120.501,0-218.537-98.036-218.537-218.537S135.499,37.463,256,37.463S474.537,135.499,474.537,256S376.501,474.537,256,474.537z "></path> </g> </g> <g> <g> <path d="M239.464,314.134l-73.929,12.203l6.102,36.964l74.046-12.223c35.402-6.082,68.507,7.402,90.821,36.983l29.908-22.56 C335.818,324.943,288.313,305.755,239.464,314.134z"></path> </g> </g></svg>';
        $html .= '<p>لم يتم العثور على نتائج اخري </p>';
        $html .= '<p>لقد شاهدت كل العناصر التي تُعرض فى هذه الصفحة</p>';
    $html .= '</div>';
}
$json['output'] = $html;
$json['arguments'] = base64_encode(json_encode($JsonArguments['arguments']));
$json['ScrollLoader'] = $JsonArguments['ScrollLoader'];
echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);