<?php 
wp_reset_query();
global $post;
# Body

$photo_gallery_list = get_post_meta($post->ID,'photo_gallery_list',true);
echo '<div class="-single-services-box">';
    echo '<div class ="container">';
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
                

                echo '<div class="categories-container">';
                foreach ($photo_gallery_list as $index => $category) {
                    $category_class = ($index === 0) ? 'category-title active' : 'category-title'; // جعل الأول نشطًا

                    echo '<div class="category-section">';
                    echo '<div class="' . esc_attr($category_class) . '">' . esc_html($category['title']) . '</div>';

                    $photo_gallery = $category['photo_gallery'];
                    if (!empty($photo_gallery)) {
                        echo '<div class="photo-gallery" pswp>';
                        foreach ($photo_gallery as $image__id => $image__url) {
                            $image_alt = get_post_meta($image__id, '_wp_attachment_image_alt', true);
                            $total_attch_data = wp_get_attachment_metadata($image__id);

                            if (!isSpeed()) {
                                if (isset($total_attch_data['width'])) {
                                    echo '<a href="' . esc_url($image__url) . '" aria-label="' . esc_attr($image_alt) . '" data-pswp-width="' . esc_attr($total_attch_data['width']) . '" data-pswp-height="' . esc_attr($total_attch_data['height']) . '">';
                                    echo YC_get_attachment(array('id' => $image__id, 'size' => 'gallery__thumbs', 'alt' => $image_alt));
                                    echo '</a>';
                                }
                            }
                        }
                        echo '</div>'; // نهاية photo-gallery
                    }

                    echo '</div>'; // نهاية category-section
                }
                echo '</div>'; // نهاية categories-container

               

            
        echo '</div>';
        
    echo '</div>';
echo '</div>';
                