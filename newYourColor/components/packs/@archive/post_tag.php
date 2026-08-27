<?php
$obj = get_queried_object();
# Parents
$UniqId = uniqid();
	if( $obj->parent > 0 ) {
		$parents = get_terms(
			array(
				"taxonomy"		=> "category",
				"parent"		=> $obj->parent
			)
		);
		$parentitem = array(get_term($obj->parent, "category"));
		$parents = array_merge($parentitem, $parents);
	}else {
		$parents = get_terms(
			array(
				"taxonomy"		=> "category",
				"parent"		=> $obj->term_id
			)
		);
		$parentitem = array(get_term($obj->term_id, "category"));
		$parents = array_merge($parentitem, $parents);
	}
	$arguments = array(
		"post_type"		=> 'post',
		"posts_per_page"=> 20,
		"cat"			=> $obj->term_id
	);
	
	
# Headline
echo '<div class="-single-cat-box">';
	echo '<div class="container">';
		echo '<div class="-homepage--container">';
			echo '<div class="CategoryBox">';
				echo '<div class="-Breadcrumb-SingularPost">';
					Breadcrumb($obj);
					echo '<h1><i class="fa fa-earth-america"></i>'.$obj->name.'</h1>';
				echo '</div>';
				echo '<div class="ArticleDetails details">';
					if( !empty($obj->description) ) {
						echo '<p>'.the_archive_description().'</p>';
					}
				echo '</div>';
			echo '</div>';
			if( count($parents) > 1 ) {
				echo '<ul class="Shapes__Navigator__V1">';
					foreach( $parents as $parent ) {
						echo '<li'.(($obj->term_id == $parent->term_id) ? ' class="current-menu-item"' : '').'>';
							echo '<a href="'.get_term_link($parent).'">';
								echo '<span>'.$parent->name.'</span>';
								echo '<i class="fa-thin fa-arrow-left"></i>';
							echo '</a>';
						echo '</li>';
					}
				echo '</ul>';
			}		
			# Body
				
		
			
			$this->Part('Posts',array('AutoLoadmore'=>false,'post__not_in'=>array($obj->term_id),'UniqId'=>$UniqId,'AutoLoadmore'=>true,'term'=>array($obj)));
		echo '</div>';
		echo '<div class="-single-parent-post--sidebar">';
			
			dynamic_sidebar('posts-sidebar');
		echo '</div>';
	echo '</div>';
echo '</div>';