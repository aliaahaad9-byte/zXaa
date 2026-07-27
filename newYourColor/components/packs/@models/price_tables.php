<?php
    $title = get_the_title($post->ID);
    $content = wp_trim_words(get_the_content($post->ID), 100, '...');
    $city = get_terms(array( 'taxonomy' => 'country', 'hide_empty' => false  ) );
echo '<sections-items>';
    echo'<section section-concept="true" class="price-section price-model">';
        echo'<div class="price container">';
            echo'<breadcrumb>';
                $this->Part('Breadcrumb');
            echo'</breadcrumb>';
            echo '<div class="titles-faq titles-serive-model">';
                echo'<h1>'.$title.'</h1>';
                echo'<p>'.the_content().'</p>';
            echo '</div>';
            echo'<div class="price-block">';
                $args = [ 'post_type'=>'price','posts_per_page'=> -1];
                
                foreach (get_posts($args) as $i => $post) {
                    $image = get_the_post_thumbnail_url( $post->ID );
                    $title = get_the_title($post->ID);
                    $feature = get_post_meta($post->ID,'feature',1);
                    $price_text = get_post_meta($post->ID,'price_text',1);
                    $btn_title = get_post_meta($post->ID,'btn_title',1);
                    $link = get_the_permalink($post->ID);
                    $content = wp_trim_words(get_post($post)->post_content, 7, '...');
                    $services_text = get_post_meta($post->ID,'services_text',1);
                    $offer = get_post_meta($post->ID,'offer',1);
                    $img_price = get_post_meta($post->ID,'img_price',1);
                    echo'<div class="box-price '.( ($feature == 'on') ? ' featuer' : '' ).'">';
                        echo'<p>'.$title.'</p>';
                        if (!empty($offer)) {
                            echo'<em>'.$offer.'</em>';
                        }
                        echo '<div class ="image_price">';
                            echo '<img width="100%" height="100%" alt="'.$title.'" src="'.$image.'"/>';
                        echo '</div>';
                        echo'<h3>'.$price_text.'</h3>';
                        echo '<div class ="list_services_price">';
                            echo'<ul>';
                                foreach ($services_text as $p ) {
                                    echo'<li>'.$p['service_info'].'</li>';
                                }
                            echo'</ul>';
                        echo '</div>';
                        echo '<div class ="links_price">';
                            echo'<a class="price_Alniks" href="'.$link.'">'.$btn_title.'</a>';
                        echo '</div>';
                    echo'</div>';
                }
            echo'</div>';
        echo'</div>';
    echo'</section>';
echo '</sections-items>';