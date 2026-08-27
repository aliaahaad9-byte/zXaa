<?php
$obj = get_queried_object();

// صفحة هبوط الخدمة — تعمل فقط على التصنيفات المفعّل فيها الخيار، وما عداها يكمل على الأرشيف كما هو
if ( class_exists( 'ServiceLanding' ) && ServiceLanding::is_enabled( $obj ) ) {
	ServiceLanding::render( $obj, $this );
	return;
}

$paged = $this->Paged();
$user_id = wp_get_current_user()->ID;
$taxonomy = $obj->taxonomy;
$taxonomy_query_search = $obj->taxonomy == 'category' ? 'cat' : ( $obj->taxonomy == 'post_tag' ? 'tag__in' : $obj->taxonomy ) ;
$taxonomy_name = $obj->name;
$description = $obj->description;
$taxonomy_id = $obj->term_id;
$taxonomy_parent = $obj->parent;
$country = get_term_meta($obj->term_id, 'country', true);
$taxonomy_key_search = $obj->taxonomy == 'category' ? $taxonomy_id : ( $obj->taxonomy == 'post_tag' ? $taxonomy_id : $obj->slug );
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
	
		$args = array(
        "post_type"         => 'page',
        "posts_per_page"    => 1,
        "cat"               => $taxonomy_id,
    );
    $videoID = get_term_meta($obj->term_id,'videoID', true);
    $page = get_posts($args);
# Headline
echo '<div class="-single-cat-box">';
	echo '<div class="container">';
		echo '<div class="-homepage--container">';
			echo '<div class="CategoryBox">';
				echo '<div class="-Breadcrumb-SingularPost">';
					Breadcrumb($obj);
					echo '<h1><i class="fa fa-earth-america"></i>'.$taxonomy_name.'</h1>';
				echo '</div>';
				echo '<div class="catArticleDetails">'; 
					echo '<div class="ArticleDetails details">';
						if( !empty(the_archive_description()) ) {
							the_archive_description();
						}
					echo '</div>';
					echo '<div class="bottun">';
						echo '<span class="more">المزيد</span>';
						echo '<span class="lese">اقل</span>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
					
			# Body
		/*	if (!empty($country)){
		    	echo'<div class="flex-city">';
					echo '<div class="titles_concept">';
			            echo'<h2>المدن التي تتواجد بها خدماتنا</h2>';
			        echo '</div>';
			    	echo'<div class="d-flex">';
	                    $i = 0;
	                    
	                   
	                    foreach ($country as $c) {
                                if (is_object(get_term($c, 'country', OBJECT))) {
                                    $c = get_term($c, 'country', OBJECT);
                                    $icon_country = get_term_meta($c->term_id, "icon", true);
	                        if( empty($icon_country) ) {
	                            $icon_country = '<i class="far fa-washer"></i>';
	                        }
                                    if(empty($c)){
                                        $c = '';
                                    }else {
                                       echo'<div class="city-block">';  
                                         echo '<a href="'.(new CityServices)->ServiceQVar($obj, $c).'">';
                                         	echo '<div class ="icon_country">';
			                                    echo '<span>'.$icon_country.'</span>';
			                                echo '</div>';    
                                            echo '<h3>'.$c->name.'</h3>';
                                         echo '</a>';
                                    echo'</div>';

                                    }
                                }
                                    

                            } 
	                echo'</div>';
	            echo '</div>';
			}*/
            
			echo '<div class="titles_concept">';
	            echo'<h2>مقالات  خدمة '.$obj->name.'</h2>';
	        echo '</div>';
			
			$this->Part('Posts',array('AutoLoadmore'=>false,'post__not_in'=>array($obj->term_id),'UniqId'=>$UniqId,'AutoLoadmore'=>true,'term'=>array($obj)));
		echo '</div>';
		echo '<div class="-single-parent-post--sidebar">';
			if(!empty($page)){    
	            echo '<div class="pagesingle">';
	                foreach( $page as $widget__post ) {
	                    $number++;
	                    $url = get_the_permalink($widget__post->ID);
	                    $title = $widget__post->post_title;
	                    
	                    echo '<a class="hoverable" href="'.$url.'">';
	                        
	                        echo '<h3>'.$title.'</h3>';
	                    echo '</a>';
	                }
	            echo '</div>';
	        }
	        if(!empty($videoID)){
	            
	       
	            echo '<div class="pagevideo">';
	                echo'<iframe class="iframe" src="https://www.youtube.com/embed/'.$videoID.'?autoplay=1" frameborder="0"></iframe>';
	            echo '</div>';
	        }
	        $similar = array(
                "post_type"         => 'works',
                "posts_per_page"    => 5,
               "cat"			=> $obj->term_id
            );

            
            $similar = get_posts(

                $similar

            );

            $UniqId = uniqid();

            $i = 0;
            if (!empty($similar)){
                echo '<div class="-Singlebar-most-view">';
                    echo '<div class ="sidebar-title">';
                        echo '<h2>سابقة اعمالنا</h2>';
                    echo '</div>';
                    echo '<div class="Singlemostview">';
                        foreach( $similar as $post ) {$i++;
                             $url = get_the_permalink($post->ID);
                             $img = get_the_post_thumbnail_url($post->ID, 'default');
                             $time = 'منذ '.human_time_diff( date('U', strtotime($post->post_date)), current_time('timestamp') );
                             $title = $post->post_title;
                             $price = get_post_meta($post->ID, 'price', true);
                             $space = get_post_meta($post->ID, 'space', true);
                             $date = get_post_meta($post->ID, 'date', true);
                             $site = get_post_meta($post->ID, 'site', true);
                             $service = get_post_meta($post->ID, 'service', true);
                             $Client = get_post_meta($post->ID, 'Client', true);
                             $imageservice = get_post_meta($post->ID, 'imageservice', true);
                             echo '<div data-id="'.$post->ID.'" class="-works">';
                                 echo '<div class="-works-ThumbBox">';
                                     echo '<div class="works-single-img- defimg">';
                                         if(!empty($imageservice)){
                                             foreach ($imageservice as $src){
                                                 echo '<img width="100%" height="100%" alt="'.$title.'" src="'.$src.'"/>';
                                             }

                                         }
                                     echo '</div>';
                                    echo '<span>'.$price.'</span>';
                                 echo '</div>';
                            
                                echo '<div class="-works-info-box">';
                                   echo '<div class="works-title-model">';
                                        echo '<span class="service-">'.$service.'</span>';
                                      echo '<h3>'.$title.'</h3>';
                                   echo '</div>';  
                                    echo '<ul class="box-widght-data">';
                                      if(!empty($Client)){
                                      echo '<li class="Client">';
                                          echo '<p>اسم العميل  :</p>';
                                          echo '<span>'.$Client.'</span>';
                                      echo '</li>';
                                      }
                                      if(!empty($site)){
                                      echo '<li class="site">';
                                          echo '<p>الموقع :</p>';
                                          echo '<span>'.$site.'</span>';
                                      echo '</li>';
                                      }
                                      if(!empty($date)){
                                      echo '<li class="date">';
                                          echo '<p>التاريخ :</p>';
                                          echo '<span>'.$date.'</span>';
                                      echo '</li>';
                                      }
                                      if(!empty($service)){
                                      echo '<li class="service">';
                                          echo '<p>نوع الخدمة :</p>';
                                          echo '<span>'.$service.'</span>';
                                      echo '</li>';
                                      }
                                      if(!empty($price)){
                                      echo '<li class="price">';
                                          echo '<p>السعر :</p>';
                                          echo '<span>'.$price.'</span>';
                                      echo '</li>';
                                      }
                                    echo '</ul>';
                                echo '</div>';
                             echo '</div>';

                        }
                        
                        
                    echo '</div>';
                echo '</div>';
            }
			dynamic_sidebar('posts-sidebar');
		echo '</div>';
	echo '</div>';
echo '</div>';