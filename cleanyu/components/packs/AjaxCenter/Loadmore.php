<?
header("Content-Type: application/json");
ob_start();
$arguments = json_decode(base64_decode($Params), true);
if( !isset($arguments["paged"]) ) $arguments["paged"] = 1;
$arguments["paged"] = $arguments["paged"] + 1;
$query = new WP_Query($arguments);

$i = 0;
while( $query->have_posts() ) {
    $query->the_post();
    global $post;
    $i++;
    
    (new ThemeStatic)->Part("blog-three", array("post"=>$post,"hidecat"=>true,"strlen"=>50,"i"=>$i,"model"=>6));
    
}

$json['end'] = false;
if($i == 0 || $i < $arguments['posts_per_page'] ) $json['end'] = true;




$html = ob_get_clean();


$json['output'] = $html;
$json['arguments'] = base64_encode(json_encode($arguments));

echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);