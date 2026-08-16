<?
ob_start();
the_content();
$Content = ob_get_clean();
$url = get_the_permalink($post->ID);
$img = get_the_post_thumbnail_url($post->ID, 'medium-large');
$category = get_the_terms($post->ID, 'category', '');
$time = 'منذ '.human_time_diff( date_i18n('U', strtotime($post->post_date)), current_time('timestamp') );
$PostAuthor = get_user_by('id',$post->post_author);
$AvatarAuthor = get_avatar_url($PostAuthor);
if(empty($AvatarAuthor)){
    $AvatarAuthor = '<i class="fa-solid fa-user-pen"></i>';
}else{
    $AvatarAuthor = '<img src="'.$AvatarAuthor.'" width="50" height="50">';
}   
$posts_per = (INT) get_option('posts_per');
//
$title = $post->post_title;
$color = false;
$termIDs = array();
$termTerm = array();
$CommentsNumber = get_comments_number($post->ID);
$CategoryTitle = '';
$CategoryURL = '';

if(!isset($icon) || $icon == '') $icon = '<ChartterElement>'.mb_substr($CategoryTitle, 0, 1,"UTF-8").'</ChartterElement>';
$mostly_searching_sorting = CommonKeywords($post->post_title, $post->post_content);

if( mb_strlen($title) > 60 ) $title = mb_substr($title, 0, 60).'..';
$classes = array();
if( mb_strlen(strip_tags($post->post_content)) > 1600 ) {
    $classes[] = '-reader';
    $excerpt = mb_substr(wp_trim_words($post->post_content, 200), 0, 190).'.. <span>اقرأ المزيد</span>';
    if( mb_strlen($post->post_title) > 64 ) $title = mb_substr($post->post_title, 0, 64).'..';
}
$isVideo = false;
if( strpos($post->post_content, '/embed') !== false ) {
    $isVideo = true;
}
$likes = (INT) get_post_meta($post->ID, 'likes', true);
##

$show_article_tag = get_term_meta($post->ID, 'show_article_tag',true);
$references = get_post_meta($post->ID, 'references', true);
if( !empty($references) ) {
    $references = explode(PHP_EOL, $references);
    $references = array_filter($references);
}

//
$post_tag = ((is_array(get_the_terms($post->ID,'post_tag',true)))) ? get_the_terms($post->ID,'post_tag',true) : array();
####
// # Related Articles
    
    $similars = get_posts(
        array(
            "post_type"         => 'post',
            "posts_per_page"    => 5,
            "post__not_in"      => array($post->ID),
            "cat"               => array_values($termIDs),
            "s"                 => $mostly_searching_sorting
        )
    );
    
    $IDsP = $post->ID;
    $termIDs = array_values($termIDs);
    $questionsMeta = get_post_meta($post->ID, 'faq', true);
    $questions = array();
    if(!empty($questionsMeta)){
        foreach( $questionsMeta as $q ) {
            if( !empty($q['question']) ) {
                $questions[] = $q;
            }
        }
    }
    if( !empty($questions)  && $questions[0]['question'] != '' ){
        echo '<script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": [ ';
                    foreach( $questions as $i => $faq ){
                        $i++;
                        echo '{
                        "@type": "Question",
                        "name": "'.$faq['question'].'",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "'.$faq['answer'].'"
                            }
                        }';
                        if( $i < count($questions) ){
                            echo ',';
                        }
                    }
                 
                echo ']
            }
        </script>';
        }
        
        $item_list_element = [];

        $item_list_element[] = [
          "@type"=> "ListItem",
          "position"=> 1,
          "name"=> get_bloginfo('name'),
          "item"=> home_url()
       ];  
        
       $item_list_element[] = [
          "@type"=> "ListItem",
          "position"=> 2,
          "name"=> get_the_title(),
          "item"=> get_the_permalink()
       ];

       $breadcrumb = [
          "@context" => "https://schema.org",
          "@type" => "BreadcrumbList",
          "itemListElement" => $item_list_element
       ];
       $Title = $post->post_title;
        $Permalink = get_the_permalink($post->ID);
        $termIDs = array();
        $termTerm = array();
        $CommentsNumber = get_comments_number($post->ID);
        $CategoryTitle = '';
        $CategoryURL = '';
        $videoID = '';
        foreach( $category as $c ) {
            $termTerm[] = $c;
            $termIDs[] = $c->term_id;
            if( $c->parent == 0  && $color == false) {
                $color = get_term_meta($c->term_id, 'color', true);
                $videoID = get_term_meta($c->term_id, 'videoID', true);
                $icon = get_term_meta($c->term_id, 'icon', true);
                $CategoryTitle = $c->name;
                $CategoryURL = get_term_link($c);

            }else if( $c->parent > 0 && $color == false) {
                $color = get_term_meta($c->parent, 'color', true);
                $videoID = get_term_meta($c->term_id, 'videoID', true);
                $icon = get_term_meta($c->parent, 'icon', true);
                $BObj = get_term_by('id',$c->parent,$c->taxonomy);
                $CategoryTitle = $BObj->name;
                $CategoryURL = get_term_link($BObj);
            }
        }
$author_id=$post->post_author; 
$author_name = get_the_author_meta( 'display_name' , $author_id );
    $author_desc = get_the_author_meta( 'description' , $author_id );
    $author_job=get_userdata($author_id)->roles[0];
    $author_link = get_author_posts_url($author_id);
echo '<div class="single-post">';
    echo '<div class="container">';
        echo '<div class="-single-parent-box">';
            echo '<div class="-single-parent">';
                echo '<div class="-single-parent-flexes--content data-id="'.$post->ID.'">';
                    echo '<div class="-single-parent-flexes--content-inner" data-post="'.$post->ID.'">';
                        do_action('yc_hook_ad_location_title_below');
                        //
                        echo '<div class="-single-parent-flexes--content-bar">';
                            echo '<h1 class="slice-title">'.$post->post_title.'</h1>';
                            echo '<Breadcrumb>';
                                Breadcrumb($post);
                            echo '</Breadcrumb>';
                        echo '</div>';
                        echo '<div class="-single-parent-flexes--content-inner-thumb">';
                            // Display the compressed image
                            if(!empty($img)){
                                echo '<img data-loader-src="'.$img.'" width="100%" height="100%" title="'.$post->post_title.'" alt="'.$post->post_title.'" >';
                            }
                            
                        echo '</div>';
                        
                        
                        echo '<div class="Contain--Content--Context">';
                            echo '<div class="-single-parent-flexes--content-inner-content">';
                                echo '<div class="ArticleDetails details">';
                                    ob_start();
                                    the_content();
                                    $content = ob_get_clean();
                                    $content = explode("<div class='-single-parent'", $content)[0];
                                    $pattern = '/<table[^>]*>(.*?)<\/table>/s'; // نمط يستهدف العناصر <table>
                                    $content = preg_replace($pattern, '<div class="tablecontainer">$0</div>', $content);
                                    if(strpos($_SERVER['HTTP_USER_AGENT'],'Lighthouse') !==false){
                                         $content = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '$1', $content);
                                    }
                                    echo $content;
                                echo '</div>';
                            echo '</div>';
                            
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="-single-social">';
                        echo '<span>شارك معانا</span>';
                        echo '<ul class="blogs-box-social-share">';
                            echo '<li class="whatsapp" data-sharer="whatsapp" data-hashtag="#'.str_replace(' ', '_', $Title).'" data-url="'.$Permalink.'"'.( ( !wp_is_mobile() ) ? ' data-web="true"' : '' ).' data-link="true" data-blank="true" data-title="'.$Title.'"b" rel="noopener" data-custom-class="socialTips">';
                                echo '<div data-navigate-off="true">';
                                  echo '<i class="fab fa-whatsapp"></i>';
                                  echo '<span>whatsapp</span>';
                                echo '</div>';
                            echo '</li>';
                            echo '<li class="facebook" data-sharer="facebook"  data-url="'.$Permalink.'" data-hashtag="#'.str_replace(' ', '_', $Title).'" rel="noopener" data-quote="'.$Title.'">';
                                echo '<div data-navigate-off="true">';
                                  echo '<i class="fab fa-facebook"></i>';
                                  echo '<span>facebook</span>';
                                echo '</div>';
                            echo '</li>';
                            echo '<li class="telegram" rel="noopener" data-sharer="telegram" data-url="'.$Permalink.'" data-title="'.$Title.'">';
                                echo '<div data-navigate-off="true">';
                                  echo '<i class="fab fa-telegram"></i>';
                                  echo '<span>telegram</span>';
                                echo '</div>';
                            echo '</li>';
                            echo '<li class="twitter" data-sharer="twitter" rel="noopener" data-url="'.$Permalink.'" data-hashtag="'.$Title.'" data-title="'.$Title.'">';
                                echo '<div data-navigate-off="true">';
                                  echo '<i class="fab fa-twitter"></i>';
                                  echo '<span>twitter</span>';
                                echo '</div>';
                            echo '</li>';
                            echo '<li class="linkedin" data-sharer="linkedin" rel="noopener" data-url="'.$Permalink.'" data-title="'.$Title.'">';
                                echo '<div data-navigate-off="true">';
                                    echo '<i class="fa-brands fa-linkedin"></i>';
                                    echo '<span>linkedin</span>';
                                echo '</div>';
                            echo '</li>';
                            echo '<li class="skype" data-sharer="skype" rel="noopener" data-url="'.$Permalink.'" data-title="'.$Title.'">';
                                echo '<div data-navigate-off="true">';
                                    echo '<i class="fa-brands fa-skype"></i>';
                                    echo '<span>skype</span>';
                                echo '</div>';
                            echo '</li>';
                        echo '</ul>';
                    echo '</div>';
                    
                echo '</div>';
                //
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</div>';


