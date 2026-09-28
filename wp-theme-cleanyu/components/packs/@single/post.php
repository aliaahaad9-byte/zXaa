<?php
$url = get_the_permalink($post->ID);
$img = get_the_post_thumbnail_url($post->ID, 'medium-large');
$category = get_the_terms($post->ID, 'category', '');
$city = get_the_terms($post->ID, 'country', '');
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
$videoID = '';
foreach ($category as $c) {
    $termTerm[] = $c;
    $termIDs[] = $c->term_id;
    $CategoryTitle = $c->name;
    

    // Check if the category has a videoID meta value and is not a child category
    if (get_term_meta($c->term_id, 'videoID', true) && $c->parent == 0) {
        $color = get_term_meta($c->term_id, 'color', true);
        $imgcat = get_term_meta($c->term_id, 'imgcat', true);
        $icon = get_term_meta($c->term_id, 'icon', true);
        $videoID = get_term_meta($c->term_id, 'videoID', true);
        $CategoryTitle = $c->name;
        $CategoryURL = get_term_link($c);
        $height = get_term_meta($c->term_id, 'height', true);
        break; // Stop looping through categories
    }
    
    // If no category has a videoID meta value, use the category with the highest priority
    if (!$videoID && get_term_meta($c->term_id, 'priority', true) == 'high') {
        $color = get_term_meta($c->term_id, 'color', true);
        $icon = get_term_meta($c->term_id, 'icon', true);
        $videoID = get_term_meta($c->term_id, 'videoID', true);
        $CategoryTitle = $c->name;
        $CategoryURL = get_term_link($c);
        $height = get_term_meta($c->term_id, 'height', true);
    }
}
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
$post_ads_subtitle =  get_post_meta($post->ID, 'post_ads_subtitle', true);
$post_ads_content =  get_post_meta($post->ID, 'post_ads_content', true);
$orderservices =  get_post_meta($post->ID, 'orderservices', true);
$categorys =  get_post_meta($post->ID, 'categorys', true);
##
$works_steps =  get_post_meta($post->ID, 'works_steps', true);
$show_article_tag = get_term_meta($post->ID, 'show_article_tag',true);
$references = get_post_meta($post->ID, 'references', true);
if( !empty($references) ) {
    $references = explode(PHP_EOL, $references);
    $references = array_filter($references);
}
$time = 'منذ '.human_time_diff( date_i18n('U', strtotime($post->post_date)), current_time('timestamp') );
//
$post_tag = ((is_array(get_the_terms($post->ID,'post_tag',true)))) ? get_the_terms($post->ID,'post_tag',true) : array();
####
// # Related Articles
    
    
    
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
   echo '<script type="application/ld+json">'.json_encode($breadcrumb).'</script>';
   $Title = $post->post_title;
    $Permalink = get_the_permalink($post->ID);
    $call_number = get_post_meta($post->ID, 'call_number', true);
    $whatsapp_number = get_post_meta($post->ID, 'whatsapp_number', true);
    $hide_phone = get_post_meta($post->ID, 'hide_phone', true);
    if( !empty($call_number) ) {
        $Phone = $call_number ;
    }else{
        $Phone = get_option('Phone');
    }
    if( !empty($call_number) ) {
        $Whatsapp = $whatsapp_number ;
    }else{
        $Whatsapp = get_option('Whatsapp');
    }
    $cityIDs = array();
    $cityTerm = array();
    $cityTitle = '';
    if(!empty($city)){
    foreach( $city as $c ) {
        $cityTerm[] = $c;
        $cityIDs[] = $c->term_id;
        $cityTitle = $c->name;
    }
    }
$show_coverimage = get_option('show_coverimage');
 $postcovers = get_post_meta($post->ID, 'imagescovers', true);
if(IsSpeed() == false){
    if(wp_is_mobile()){
        if($show_coverimage == 'on'){
        echo '<div class="imagecover-post">';
            echo'<div class="imagecover-">';
                
                if(!empty($postcovers)){
                    
                    if (is_array($postcovers)) {
                        foreach ($postcovers as $src) {
                            echo '<div class="item-imagecover">';
                            $image_url = $src;
                            $image_id = attachment_url_to_postid($image_url);
                            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false) {
                                echo '<img data-loader-src="'.$src.'"  width="100%" height="100%" alt="'.$image_alt.'" />';
                            }
                            echo '</div>';
                        }
                    }
                }else {
                    $imagecover = get_option('imagecover');
                    if (is_array($imagecover)) {
                        foreach ($imagecover as $src) {
                            echo '<div class="item-imagecover">';
                                echo '<div class="item-imagecover-">';
                                $image_url = $src;
                                $image_id = attachment_url_to_postid($image_url);
                                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false) {
                                    echo '<img data-loader-src="'.$src.'"  width="100%" height="100%" alt="'.$image_alt.'" />';
                                }
                                echo '</div>';
                            echo '</div>';
                        }
                    }
                }
            echo'</div>';
        echo'</div>';
    }
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
                        $hidethumb = get_option('hidethumb');
                        if(!empty($hidethumb) && $hidethumb != 'on' || empty($hidethumb)){
                            echo '<div class="-single-parent-flexes--content-inner-thumb">';
                                // Display the compressed image
                                echo '<img data-loader-src="'.$img.'" width="100%" height="100%" title="'.$post->post_title.'" alt="'.$post->post_title.'" >';
                                /*echo '<div class="user-boxed">';
                                    echo '<div class="-user-avatar">'.$AvatarAuthor.'</div>';
                                    echo '<div class="-user-context"><p>بواسطة</p><span>'.$PostAuthor->display_name.'</span></div>';
                                echo '</div>';*/
                            echo '</div>';
                        }
                        
                        echo '<div class="Contain--Content--Context">';
                            echo '<div class="-single-parent-flexes--content-inner-content">';
                                echo '<div class="ArticleDetails details">';
                                    ob_start();
                                    the_content();
                                    $content = ob_get_clean();
                                    $content = explode("<div class='-single-parent'", $content)[0];
                                    $pattern = '/<table[^>]*>(.*?)<\/table>/s'; // نمط يستهدف العناصر <table>
                                    $content = preg_replace($pattern, '<div class="tablecontainer">$0</div>', $content);
                                    if(IsSpeed() == true){
                                         $content = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '$1', $content);
                                    }
                                    echo $content;
                                echo '</div>';
                            echo '</div>';
                            // أنماط التواريخ وأسئلة H3 — مضمّنة حتى لا نلمس main.css
                            $extras_css = get_template_directory() . '/components/packs/AuthorBio/single-extras.css';
                            if( file_exists($extras_css) ) {
                                echo '<style>'.file_get_contents($extras_css).'</style>';
                            }
                            echo '<ul class="single-bar">';
                                echo '<li>';
                                    echo  '<span>بواسطة</span>';
                                    echo '<a href="'.$author_link.'" class="unline">'.$author_name.'</a></span>';
                                echo '</li>';
                                
                                # تاريخ النشر وآخر تحديث — «نُشر: 25 ديسمبر 2022 · آخر تحديث: 20 سبتمبر 2026»
                                $published_ts = get_post_time( 'U', false, $post );
                                $modified_ts  = get_post_modified_time( 'U', false, $post );
                                echo '<li class="single-dates">';
                                    echo '<span>نُشر:</span>';
                                    echo '<time datetime="'.esc_attr( get_post_time( 'c', true, $post ) ).'">'.esc_html( date_i18n( 'j F Y', $published_ts ) ).'</time>';
                                    // يظهر آخر تحديث فقط إن عُدّل المقال في يوم لاحق ليوم نشره
                                    if( $modified_ts && date( 'Y-m-d', $modified_ts ) > date( 'Y-m-d', $published_ts ) ) {
                                        echo '<em aria-hidden="true">·</em>';
                                        echo '<span>آخر تحديث:</span>';
                                        echo '<time datetime="'.esc_attr( get_post_modified_time( 'c', true, $post ) ).'">'.esc_html( date_i18n( 'j F Y', $modified_ts ) ).'</time>';
                                    }
                                echo '</li>';
                                if(!empty($category)){
                                    foreach( $category as $cat ) {
                                        echo '<li>';
                                            echo  '<span>في  </span>';
                                            echo '<a href="'.get_term_link($cat).'" class="unline">'.$cat->name.'</a>';
                                        echo '</li>';
                                    }
                                }
                                if(!empty($cityTerm)){
                                    // رابط داخلي لأرشيف كل مدينة
                                    $city_links = array();
                                    foreach( $cityTerm as $ct ) {
                                        $ct_link = get_term_link( $ct );
                                        $city_links[] = is_wp_error( $ct_link )
                                            ? esc_html( $ct->name )
                                            : '<a href="'.esc_url( $ct_link ).'" class="unline">'.esc_html( $ct->name ).'</a>';
                                    }
                                    echo '<li>';
                                        echo '<span> في مدينة :</span>';
                                        echo implode( '<span>،</span>', $city_links );
                                    echo '</li>';
                                }
                                echo '<li><i class="fa-regular fa-comment"></i> '. $CommentsNumber.'</li>';
                            echo '</ul>';
                        echo '</div>';
                    echo '</div>';
                    if(IsSpeed() == false){
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
                    }
                    
                    echo '<div class="Contain-post-">';
                        if(!empty(get_post_meta($post->ID,'widefat',1)[0]['name'])){
                            echo'<div class="referance">';
                                echo'<div class="referance-title"><i class="fa-solid fa-arrow-up-right-from-square"></i><h4>المراجع  </h4><i class="fa-solid fa-plus"></i></div>';
                                    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                                    if(strpos($actual_link,'#ref') !== false){                      
                                        echo'<div class="referance-Content active">';
                                    }else{
                                        echo'<div class="referance-Content">';
                                    }
                                    $i= 1;
                                    foreach (get_post_meta($post->ID,'widefat',1) as $key => $widefat) {
                                        echo'<li id="ref'.$i.'"><sitename>'.$widefat['name'].'</sitename><a href="'.$widefat['url'].'">'.$widefat['select'].'</a><date>'.$widefat['date'].'</date></li>';
                                        $i++;
                                    }
                                echo'</div>';
                            echo'</div>';
                        }
                        if(!empty($questions)){
                            $faq_about = rtrim( trim( wp_strip_all_tags( get_the_title( $post ) ) ), '؟?' );
                            echo '<div class="-faqs-singlebox">';
                                echo '<h2 class="-TitleContent-section"><i class="fa-solid fa-question"></i>الأسئلة الشائعة حول '.esc_html( $faq_about ).'؟</h2>';
                                echo '<ul>';
                                    $q=0;
                                    foreach ($questions as $v) {$q++;
                                        echo '<li '.(($q == 1) ? 'class="active"' : '').'>';
                                            echo '<h3 class="FaqQuestion"><span>'.$v['question'].'</span><i class="fa-solid fa-plus"></i></h3>';
                                            echo '<div class="FaqsAnswers"><div class="AnswerContext">'.$v['answer'].'</div></div>';
                                        echo '</li>';
                                    }
                                echo '</ul>';
                        echo '</div>';
                        }
                        if(!empty($post_tag)){
                            echo '<div class="-keywords-box">';
                                echo '<span class="-Title-keywords"><i class="fa-regular fa-tags"></i> الوسوم</span>';
                                echo '<p class="-common-keywords">';
                                    
                                        foreach ($post_tag as $tag) {
                                            echo '<a href="'.get_term_link($tag).'">';
                                                echo $tag->name;
                                            echo '</a>';
                                        }
                                    
                                echo '</p>';
                            echo '</div>';
                        }

                        # نبذة عن كاتب المقال
                        if( !empty($author_id) ) {
                            // الأنماط مضمَّنة هنا حتى لا نلمس main.css إطلاقًا،
                            // ولأن القالب يلغي كل الملفات المسجَّلة في الواجهة.
                            $bio_css = get_template_directory() . '/components/packs/AuthorBio/author-bio.css';
                            if( file_exists($bio_css) ) {
                                echo '<style>'.file_get_contents($bio_css).'</style>';
                            }
                            $yc_a          = function_exists('yc_author_profile') ? yc_author_profile( $author_id ) : array();
                            $author_posts  = (int) count_user_posts( $author_id, 'post' );
                            $author_avatar = !empty($yc_a['photo']) ? $yc_a['photo'] : get_avatar_url( $author_id, array('size'=>160) );
                            $author_title  = !empty($yc_a['title']) ? $yc_a['title'] : '';
                            if( !empty($yc_a['name']) )  { $author_name = $yc_a['name']; }
                            if( !empty($yc_a['short']) ) { $author_desc = $yc_a['short']; }

                            echo '<div class="author-bio">';

                                echo '<div class="author-bio__avatar">';
                                    if( !empty($author_avatar) ) {
                                        echo '<img src="'.esc_url($author_avatar).'" width="86" height="86" loading="lazy" alt="'.esc_attr($author_name).'" />';
                                    }else {
                                        $fallback = GetAvatar( $author_id );
                                        echo '<span class="author-bio__letter" style="background:'.esc_attr($fallback['color']).';color:'.esc_attr($fallback['textcolor']).'">'.esc_html($fallback['letter']).'</span>';
                                    }
                                echo '</div>';

                                echo '<div class="author-bio__body">';

                                    echo '<span class="author-bio__label">كاتب المقال</span>';
                                    if( !empty($yc_a['verified']) ) {
                                        echo '<span class="author-bio__verified">'.yc_author_svg('check').'خبير موثّق</span>';
                                    }
                                    echo '<h2 class="author-bio__name"><a href="'.esc_url($author_link).'">'.esc_html($author_name).'</a></h2>';

                                    if( $author_title !== '' ) {
                                        echo '<span class="author-bio__role">'.esc_html($author_title).'</span>';
                                    }

                                    if( !empty($author_desc) ) {
                                        echo '<p class="author-bio__text">'.esc_html( wp_strip_all_tags($author_desc) ).'</p>';
                                    }

                                    echo '<div class="author-bio__foot">';
                                        if( $author_posts > 0 ) {
                                            echo '<span class="author-bio__count">'.number_format_i18n($author_posts).' مقالًا منشورًا</span>';
                                        }
                                        echo '<a class="author-bio__more" href="'.esc_url($author_link).'">كل مقالات الكاتب</a>';
                                    echo '</div>';

                                echo '</div>';

                            echo '</div>';
                        }
                    echo '</div>';
                echo '</div>';
                //
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</div>';

echo '<div class="ratingpost">';
    echo'<div class="container">';
    
    $this->Part('comments',array('post'=>$post));
    echo'</div>';
echo'</div>';
$arguments = array(
    "post_type"         => 'post',
    "posts_per_page"    => 5,
    "post__not_in"      => array($post->ID),
    "cat"               => array_values($termIDs),
    "orderby"           => 'last_update'
);

$UniqId = uniqid();
$i = 0;
echo '<div class="gridpostsingle">';
    echo '<div class="container">';
        echo '<div class="Singlemosttabs">';
            
            echo '<div class="postgrid-title">';
                echo '<span><i class="fa-solid fa-newspaper"></i>مقالات قد تهمك</span>';
                echo '<p>مقالات موقع '.get_option('sitename').'</p>';
            echo '</div>';
            echo '<ul class="-Tabs--Posts--List">';
                echo '<li class="-Tabs--Posts--Items hoverable" data-tabs="true" data-uniq="'.$UniqId.'" data-hometab="date"><span> اخر المقالات</span></li>';
                echo '<li class="-Tabs--Posts--Items hoverable" data-tabs="true" data-uniq="'.$UniqId.'" data-hometab="trending"><span>الاكثر شيوعا</span></li>';
                
            echo '</ul>';
        echo '</div>';
        //echo '<div class="Singlemostview">';
            $this->Part('Posts',array('AutoLoadmore'=>false,'UniqId'=>$UniqId,'arguments'=>$arguments,'AutoLoadmore'=>true));
        //echo '</div>';
    echo '</div>';
echo '</div>';

$call_titles = get_post_meta($post->ID,'call_titles',true);
$call_contents = get_post_meta($post->ID,'call_contents',true);
if(empty($call_titles) ) {
    $titlepopup = get_option('titlepopup');
    if(empty($titlepopup)){
        $titlepopup = 'اطلب الخدمة';
    }
    $call_titles = $titlepopup;
}
if(empty($call_contents)){
     $contentpopup = get_option('contentpopup');
    if(empty($contentpopup)){
        $contentpopup = 'خدمة علي مدار 24 ساعه';
    }
    $call_contents = $contentpopup;
}
/*echo '<div class="popup-call">';
    
    echo'<div class="box-block">';
        echo '<span class="closepopup"><i class="fal fa-times"></i></span>';
        echo '<div class="popup-boxed">';
            echo '<svg-box><svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24" width="512" height="512"><path d="M21,12.424V11A9,9,0,0,0,3,11v1.424A5,5,0,0,0,5,22a2,2,0,0,0,2-2V14a2,2,0,0,0-2-2V11a7,7,0,0,1,14,0v1a2,2,0,0,0-2,2v6H14a1,1,0,0,0,0,2h5a5,5,0,0,0,2-9.576ZM5,20H5a3,3,0,0,1,0-6Zm14,0V14a3,3,0,0,1,0,6Z"/></svg></svg-box>';
            echo '<popup-title--box>';
                echo '<h2>'.$call_titles.'</h2>';
                echo '<p>'.$call_contents.'</p>';
            echo '</popup-title--box>';
        echo '</div>';
        echo '<div class="popup-boxnumber">';
            echo '<a class="popup-phone" href="tel:'.$Phone.'">';
                echo '<i class="fa-thin fa-phone-volume"></i>';
                echo '<strong>اتصل بنا</strong>';
            echo '</a>';                    
            echo '<a target="_blank" rel="nofollow" class="popup-whatsapp" href="https://wa.me/'.$Whatsapp.'">';
                echo '<i class="fa-brands fa-whatsapp"></i>';
                echo '<strong>   الواتساب</strong>';
            echo '</a>';
        echo '</div>';
    echo '</div>';
echo '</div>';
*/