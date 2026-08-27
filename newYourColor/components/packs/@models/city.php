<?php
$title = get_the_title($post->ID);
$content = wp_trim_words(get_the_content($post->ID), 100, '...');
$city = get_terms(array( 'taxonomy' => 'country', 'hide_empty' => false, ) );
$categories = get_terms(
    array(
        "taxonomy"      => "category",
        "hide_empty"    => false,
        "parent"        => 0,
        'meta_query'=>
         array(
            'relation' =>'or',
                array('key'=>'exclude_homepage','compare' =>'NOT EXISTS'),
                array('key'=>'exclude_homepage','value'=>'on','compare' =>'!='),
        )
    )
);
echo '<sections-items>';
    echo'<div  class="city-single-model" >';
        echo'<section class="services container">';
            echo '<div class="category-headline">';
                echo '<div class="-Breadcrumb-SingularPost">';
                    Breadcrumb();
                    echo '<h1><i class="fa fa-earth-america"></i>'.$post->post_title.'</h1>';
                    echo '<p>استكشف   مدن موقع '.get_option('sitename').' </p>';
                echo '</div>';
                
            echo '</div>';
            echo'<div class="services-info">';
                echo'<div class="d-flex">';
                    $i = 0;
                    foreach ($city as $c) {$i++;
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
                echo'</div>';
            echo'</div>';
            echo '<div class="ArticleDetails">';
                echo '<p>'.the_content().'</p>';
            echo '</div>';
        echo'</section>';
    echo'</div>';
echo '</sections-items>';