<?php
$title = get_the_title($post->ID);
$content = wp_trim_words(get_the_content($post->post_content), 20, '...');
$image = get_the_post_thumbnail_url( $post->ID );

echo'<div class="faq-section_faq-model">';
   
    echo'<div class="faq-section2 faq-model">';
        echo'<section class="faq1 container">';
            echo '<div class ="breadcrumb1">';
                echo'<breadcrumb>';
                   Breadcrumb();
                echo'</breadcrumb>';
            echo '</div>';
            echo '<div class="titles-faq">';
                echo'<h1>'.$title.'</h1>';
                echo'<p>'.the_content().'</p>';
            echo '</div>';
            echo '<div class ="faq_questions">';
                $args = [ 'post_type'=>'faq','posts_per_page'=>-1];
                echo '<div class="faq_section">';
                    echo'<div class="faq-info">';
                        $args = [ 'post_type'=>'faq','posts_per_page'=>-1];
                        (new ThemeStatic)->Part("faq", array("args"=>$args));
                    echo'</div>';
                echo'</div>';
                
            echo'</div>';
        echo'</section>';
    echo'</div>';
echo'</div>';
