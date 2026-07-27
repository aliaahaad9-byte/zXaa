<?php $posts_per = (INT) get_option('posts_per');
if(empty($posts_per) || $posts_per == 0) $posts_per = 25;
if(!isset($orderby)) $orderby = '';
if(!isset($AutoLoadmore)) $AutoLoadmore = true;
if(!isset($btn)) $btn = true;
if(!isset($ScrollLoader)) $ScrollLoader = true;
if(!isset($per)) $per = $posts_per;
if(!isset($post_type)) $post_type = 'post';
if(!isset($UniqId)) $UniqId = uniqid();
if(!isset($ajax)) $ajax  = false;


$currentURL = $this->GetCurrentURL();
$currentURL = str_replace('www.', '',$currentURL);

$paged = $this->Paged();
if(!isset($arguments)){
	$arguments = array(
		"post_type"	=> $post_type,
		"posts_per_page"=>$posts_per,
	);

	// 
	if( $orderby == 'trending' ) {
	    $arguments['meta_key'] = 'trending';
	    $arguments['orderby'] = 'meta_value_num';
	}else if( $orderby == 'rand' ) {
	    $arguments['orderby'] = 'rand';
	}else if( $orderby == 'old' ) {
	    $arguments['order'] = 'ASC';
	}
	//
	if(isset($term)){
		$TotalTerms = array();
		if(!isset($data['tax_relation'])) $data['tax_relation'] = 'AND';
		$arguments['tax_query'] = array(
			'relation'=>  $data['tax_relation']
		);
			//
		foreach ($term as $s => $mm) {
			$arguments['tax_query'][] = array(
			    'taxonomy'  => $mm->taxonomy,
			    'field'   => ($mm->taxonomy == 'category') ? 'term_id' : 'slug',
			    'terms'   => ($mm->taxonomy == 'category') ? $mm->term_id : $mm->slug,
			    'operator'  => 'IN'
			);
		}
	}
}

if(isset($author)){
	$arguments['author'] = $author;
}



if($paged > 1){
	$arguments['paged'] = $paged;
}

//print_r($arguments);

$Founder = new WP_Query($arguments);
$CountQuery = $Founder->found_posts;
//

$ScrollLoader = (($ScrollLoader == true && $CountQuery < $per || $per == -1)) ? false : true;
$LoaMoreAttr = (($ScrollLoader != false)) ? 'data-loadmore="'.base64_encode(json_encode($arguments)).'" data-finish="false"' : ' data-finish="true"';

echo (($ajax != true)) ? '<div class="-Posts-grid -ScrollerCenter" '.$LoaMoreAttr.' data-autoloaded="'.$AutoLoadmore.'" data-uniqid="'.$UniqId.'">': '';
	$i=0;
	foreach( get_posts($arguments) as $post ) {
		$i++;
		$this->Part("Griditem", array("post"=>$post,"i"=>$i,"model"=>2));
		
		
		
	}
echo (($ajax != true)) ? '</div>': '';

	if($ScrollLoader == true){
		echo (($ajax != true)) ? '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'.$UniqId.'" class="PostsScrollLoader hoverable LoadMorePostsBTN" '.(($AutoLoadmore != false) ? 'style="display:none"' : '').'><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>' : '';
	}


if($ajax == true){
	$json = array(
		'arguments'=>$arguments,
		'ScrollLoader'=>$ScrollLoader,
	);
	echo '<CutAjax>'.json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE).'</CutAjax>';
}