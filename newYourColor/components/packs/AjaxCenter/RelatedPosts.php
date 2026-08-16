<?php 
header("Content-Type: application/json");
$json = array();
ob_start();
$tabs = $_POST['tabs'];
$cats = $_POST['cats'];
$number = $_POST['number'];
$type = $_POST['type'];
$category = get_term_by('term_id', $cats, 'category');

$arguments = array(
    "post_type"     => 'post',
    "posts_per_page"=> $number,
    "cat"   =>$cats,
);
if( $tabs == 'trending' ) {
    $arguments['meta_key'] = 'trending';
    $arguments['orderby'] = 'meta_value_num';
}else if( $tabs == 'last_update' ) {
    $arguments['order'] = 'DESC';
}else if( $tabs == 'rand' ) {
    $arguments['orderby'] = 'rand';
}

if( $type == 1 ) { 
    echo  '<div class="postmodel model-'.$type.'">';
        foreach( get_posts($arguments) as $posts ) {$i++;
            $this->ThemeStatic->Part("Griditem", array("post"=>$posts,'model'=>2));
        }
    echo  '</div>';
}else if( $type == 2 ) { 
    $arguments['posts_per_page'] = 8;
    echo  '<div class="postmodel-model-'.$type.'">';
        foreach( get_posts($arguments) as $post ) { 
            $i++;
            if($i <= 2){
                if($i == 1 ){
                    echo'<div class="Griditem-posts-left">';
                }
                $this->ThemeStatic->Part("Griditem", array("post"=>$post,'model'=>1));
                if($i == 2){
                    echo'</div>';
                }
            }else if($i <= 6){
                if($i == 3 ){
                    echo'<div class="Griditem-posts-midden">';
                }
                    $this->ThemeStatic->Part("Griditem", array("post"=>$post,'model'=>4));
                if($i == 6){
                    echo'</div>';
                }
            }else if ($i <= 8){
                if ($i == 7){
                    echo'<div class="Griditem-posts-right">';
                }
                $this->ThemeStatic->Part("Griditem", array("post"=>$post,'model'=>1));
                if ($i == 8){
                    echo'</div>';
                }
            }
        }
    echo  '</div>';
}else if( $type == 3 ) {
    echo '<div class="postmodel model-'.$type.'">';
        foreach( get_posts($args) as $posts ) {$i++;
            $this->ThemeStatic->Part("Griditem", array("post"=>$posts,'model'=>2));
        }
    echo'</div>';
        
}
$html = ob_get_clean();


$json['output'] = $html;
echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);