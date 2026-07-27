<?
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
		'post_type'		=> 'post',
		'posts_per_page'=> 20,
		'tax_query'		=> array(
			array(
				'taxonomy'	=> $obj->taxonomy,
				'terms'		=> $obj->term_id,
				'field'		=> 'term_id',
			)
		),
	);	
# Headline
echo '<div class="-single-city-box">';
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
				$args = array(
					'taxonomy' => 'category',
					"meta_query"=>array(
						array(
							"key"=>'country',
							"compare"=>"LIKE",
							"value"=>$obj->term_id
						)
					),
					'hide_empty' => false
				);
				if (!empty(get_categories($args))){
					echo'<div class="services nonebg lowpadding" id="services1">';
					    echo'<section class="services">';
					        echo '<div class="titles_concept">';
					            echo'<h2>خدمات مدينة '.$obj->name.'</h2>';
					        echo '</div>';
					       	echo'<div class="category-shap">';
				                foreach (get_categories($args) as $category) {
									$content = wp_trim_words($category->description, 15, '...');
		                            $icon = get_term_meta($category->term_id, "icon", true);
		                            if( empty($icon) ) {
		                                $icon = '<i aria-hidden="true" class="fas fa-grip-horizontal"></i>';
		                            }
		                            $image = get_term_meta($category->term_id, "imgcat", true);
		                            
		                            $Service_price = get_term_meta($category->term_id, "Service_price", true);
		                            echo '<div class= "-category-boxed">';
		                                echo '<div class= "-category-image">';
		                                    echo'<a href="'.get_term_link($category).'">';
		                                        echo '<img width="100%" height="100%" alt="'.$category->name.'" src="'.$image.'"/>';
		                                    echo '</a>';
		                                    echo '<span>'.$icon.'</span>';
		                                    if (!empty($Service_price)){
		                                        echo '<p class="Service_price">'.$Service_price.'</p>';
		                                    }
		                                echo '</div>';
		                                echo '<div class= "cat_title_boxed">';
		                                    echo'<a href="'.get_term_link($category).'">';
		                                       echo'<h3>'.$category->name.'</h3>';
		                                       echo'<p>'.$content.'</p>';
		                                    echo '</a>';
		                                       
		                                    
		                                echo '</div>';
		                            echo '</div>';
								}
				            echo'</div>';
					    echo'</section>';
					echo'</div>';
				}

			
			# Body
			$argumen = get_posts( $arguments );
			if (!empty($argumen)){
				echo '<div class="titles_concept">';
		            echo'<h2>مقالات مدينة '.$obj->name.'</h2>';
		        echo '</div>';
			}
			
			$this->Part('Posts',array('AutoLoadmore'=>false,'UniqId'=>$UniqId,'AutoLoadmore'=>true,'arguments'=>$arguments));
		echo '</div>';
		echo '<div class="-single-parent-post--sidebar">';
			
			dynamic_sidebar('posts-sidebar');
		echo '</div>';
	echo '</div>';
echo '</div>';