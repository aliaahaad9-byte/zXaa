<?php
wp_reset_query();
global $post;
# Body
echo '<div class="-single-services-box">';
    echo '<div class ="container">';
        echo '<div class="-homepage--container">';
            echo '<div class="-Posts-parent-flexes--content">';
                
                echo '<div class="CategoryBox">';
                                                
                    //
                    echo '<div class="category-headline">';
                        echo '<div class="-Breadcrumb-SingularPost">';
                            Breadcrumb();
                            echo '<h1><i class="fa fa-earth-america"></i>'.$post->post_title.'</h1>';
                              echo '<p>استكشف   أقسام موقع '.get_option('sitename').' </p>';
                        echo '</div>';
                        
                    echo '</div>';
                echo '</div>';
                echo '<div class="ArticleDetails">';
                    echo '<p>'.the_content().'</p>';
                echo '</div>';
                    $TermsArgums = array(
                        'taxonomy'=>'category',
                        'hide_empty'=>true,
                        'parent'=>0,
                        'order'=>'ASC',
                        
                    );
                   echo'<div class="category_panner-mobile">';
                    $imagepin = get_option('imagepin')['url'];
                    foreach( get_terms($TermsArgums) as $category ) {
                        $content = wp_trim_words($category->description, 15, '...');
                        $icon = get_term_meta($category->term_id, "icon", true);
                        if( empty($icon) ) {
                            $icon = '<i aria-hidden="true" class="fas fa-grip-horizontal"></i>';
                        }
                        $image = get_term_meta($category->term_id, "imgcat", true);
                        if( empty($image) ) {
                            $image = $imagepin;
                        }
                        $Service_price = get_term_meta($category->term_id, "Service_price", true);
                        echo '<div class= "-category-boxed">';
                            echo '<div class= "-category-image">';
                                echo'<a href="'.get_term_link($category).'">';
                                    echo '<img width="100%" height="100%" alt="'.$category->name.'" src="'.$image.'"/>';
                                   
                                echo '</a>';
                            echo '</div>';
                            echo '<div class= "cat_title_boxed">';
                                echo'<a href="'.get_term_link($category).'">';
                                   echo'<h3>'.$category->name.'</h3>';
                                echo '</a>';
                            echo '</div>';
                        echo '</div>';
                    }        
                
                echo '</div>';
                
            echo '</div>';
        echo '</div>';
        echo '<div class="-single-parent-post--sidebar">';
            dynamic_sidebar('posts-sidebar');
        echo '</div>';
    echo '</div>';
echo '</div>';
                