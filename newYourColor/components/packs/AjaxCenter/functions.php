<?phpheader("Content-type: application/json");
$json = array();
ob_start();


if($_POST['type'] =='servies'){
	$args = [ 'taxonomy'=>'category','number'=>$_POST['per'],'offset'=>$_POST['offset'],'fields'=>'ids','hide_empty' => 0];
	(new ThemeStatic)->Part("services", array("args"=>$args));
	$json['output'] = ob_get_clean();
	echo json_encode($json);
}else if($_POST['type'] =='posts'){
	$args = array(
			'post_type'		=> 'post',
			'posts_per_page'=> $_POST['per'],
			'offset'=> $_POST['offset'],
		);
		$wp_query = new WP_Query();
		$wp_query->query( $args );
		$i = $args['paged'];
		while ($wp_query->have_posts()) : $wp_query->the_post();
        	(new ThemeStatic)->Part("blog-item", array("post"=>$post->ID));
		endwhile;
}