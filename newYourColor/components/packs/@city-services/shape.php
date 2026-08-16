<?php 
$this->Part('header');
global $wp, $wp_rewrite, $wp_query, $post;
$paged = $this->Paged();
$country = get_term_meta($term['category']->term_id, 'country', true);
$UniqId = uniqid();
$tax_query = array(
	array(
			'taxonomy'	=> $term['city']->taxonomy,
			'terms'		=> array($term['city']->slug),
			'field'		=> 'slug',
			'operator'	=> 'LIKE'
	)
);
echo'<section class="cities-list">';
	echo'<div class="container">';
		echo'<div class="cities-box">';
			echo'<div class="title-page">';
				echo'<breadcrumb>';
			      Breadcrumb();
			   echo'</breadcrumb>';
			   echo '<h1 class="news-title">'.get_term($term['category']->term_id)->name.' في مدينة   '.get_term($term['city']->term_id)->name.'</h1>';
		   echo '</div>';
			if (!empty($args)){
				echo'<div class="city-model">';
					echo '<h1 class="news-title"> '.get_term($term['category']->term_id)->name.'</h1>';
		  			$tax_query['relation']='AND';
					$tax_query[]= array(
						'taxonomy'	=>'category',
						'terms'		=> array($term['category']->term_id),
						'field'		=> 'id',
					);
					$tax_query[] = array(
						'taxonomy'	=> $term['city']->taxonomy,
						'terms'		=> array($term['city']->slug),
						'field'		=> 'slug',
					);
					$args = array(
						'post_type'		=> 'post',
						'posts_per_page'=> 20,
						'paged'=>$paged,
						'tax_query'		=> $tax_query,
						
					);
					$this->Part('Posts',array('AutoLoadmore'=>false,'UniqId'=>$UniqId,'AutoLoadmore'=>true,'arguments'=>$args));
				echo '</div>';
			}
			if (!empty($country)){
				echo'<div class="cities-inner">';
				echo'<h1>خدماتنا في مدينة'.get_term($term['city']->term_id)->name.'</h1>';
					echo'<div class="cities-list__inner">';
				     
				    	foreach( $country as $c ) {
				    		if (is_object(get_term($c, 'country', OBJECT))) {
				    			$c = get_term($c, 'country', true);
					    		if(empty($c)){
				            	$c = '';
					    	}else {
						    		$tax_query[] = array(
										'taxonomy'	=> $c->taxonomy,
										'terms'		=> array($c->slug),
										'field'		=> 'slug',
										'operator'	=> 'LIKE'
									);
							    		 
			                  $icon_country = get_term_meta($c->term_id, "icon", true);
		                        if( empty($icon_country) ) {
		                            $icon_country = '<i class="far fa-washer"></i>';
		                        }
		                       
		                        $btn = get_term_meta($c->term_id , 'btn' ,1);
		                        echo'<div class="city-block">';         
		                            echo '<a class="links" href="'.get_term_link($c).'">';  
		                                echo '<div class ="icon_country">';
		                                    echo '<span>'.$icon_country.'</span>';
		                                echo '</div>';           
		                                echo'<div class="head-block-city">';
		                                    echo '<h2>'.$c->name.'</h2>';                                    
		                                echo'</div>';    
		                            echo '</a>';                                    
		                        echo'</div>';
				                    
				                    

					    		}
				    		}
			        	}
					  
					echo '</div>';
				echo '</div>';
			}
		echo '</div>';

	echo '</div>';
echo '</section>';

$this->Part('footer');