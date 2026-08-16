<?
global $wpdb;
$search_term = $_GET['s'];
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT ID FROM $wpdb->posts WHERE post_title LIKE '%%%s%%' AND post_type = 'post' AND post_status = 'publish'",
        $search_term
    )
);

$UniqId = uniqid();
$search_query = get_search_query();
    echo '<div class="-single-search-box">';
        echo '<div class="container">';
            echo '<div class="-search-page--content">';
                echo '<div class="CategoryBox">';
                    //
                    echo '<div class="category-headline">';
                        echo '<div class="-Breadcrumb-SingularPost">';
                            Breadcrumb();
                        echo '</div>';
                        echo '<div class="category-title">';
                            echo '<h1>تائج البحث عن  : '.$search_query.'</h1>';
                            
                        echo '</div>';
                        
                    echo '</div>';
                echo '</div>';
                echo '<div class="-Posts-grid">';
                    foreach ($results as $post ) {
                        $PostID  = $post->ID;
                        $url = get_the_permalink($post->ID);
                        $category = get_the_terms($post->ID, 'category', '');
                        $img = get_the_post_thumbnail_url($post->ID, 'default');
                        

                        echo '<div data-id="'.$post->ID.'" class="-GridItem-1 hover-custom">';
        
                            echo '<div class="-GridItem-1-ThumbBox">';
                                echo '<a href="'.get_the_permalink($post->ID).'">';
                                if(!empty($img)){
                                    echo '<img width="100%" height="100%" alt="'.get_the_title($PostID).'" title="'.get_the_title($PostID).'" src="'.$img.'"/>';
                                }
                                echo '</a>';
                                echo '<div class="-GridItem-1-category-Item">';
                                    foreach( array_slice($category, 0, 1) as $tag ) {
                                        if($tag->parent > 0){
                                            $icon = get_term_meta($tag->parent,'icon',true);
                                        }else{
                                            $icon = get_term_meta($tag->term_id,'icon',true);
                                        }
                                        echo '<span>';
                                            echo $tag->name;
                                        echo '</span>';
                                    }   
                                echo '</div>';
                            echo '</div>';
                            
                            
                            echo '<div class="-GridItem-1-info">';
                                
                                echo '<a href="'.get_the_permalink($post->ID).'">';
                                    echo '<h3>'.get_the_title($PostID).'</h3>';
                                echo '</a>';
                                
                            echo '</div>';
                            
                            //
                        echo '</div>';
                        
                    }
                echo '</div>';
                
            echo '</div>';
        echo '</div>';

        

    echo '</div>';
