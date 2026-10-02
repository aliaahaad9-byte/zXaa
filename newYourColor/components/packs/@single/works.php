<?php
$url = get_the_permalink($post->ID);
$img = get_the_post_thumbnail_url($post->ID, 'medium-large');
$category = get_the_terms($post->ID, 'category', '');
$time = 'منذ '.human_time_diff( date_i18n('U', strtotime($post->post_date)), current_time('timestamp') );
$PostAuthor = get_user_by('id',$post->post_author);
$AvatarAuthor = get_avatar_url($PostAuthor);
$posts_per = (INT) get_option('posts_per');
//
$title = $post->post_title;
$price = get_post_meta($post->ID, 'price', true);
$space = get_post_meta($post->ID, 'space', true);
$date = get_post_meta($post->ID, 'date', true);
$site = get_post_meta($post->ID, 'site', true);
$service = get_post_meta($post->ID, 'service', true);
$Client = get_post_meta($post->ID, 'Client', true);
$imageservice = get_post_meta($post->ID, 'imageservice', true);
$termIDs = array();

$termTerm = array();
$CategoryTitle = '';
$CategoryURL = '';
foreach( $category as $c ) {
    $termTerm[] = $c;
    $termIDs[] = $c->term_id;
    if( $c->parent == 0  && $color == false) {
        $icon = get_term_meta($c->term_id, 'icon', true);
        $CategoryTitle = $c->name;
        $CategoryURL = get_term_link($c);
    }else if( $c->parent > 0 && $color == false) {
        $icon = get_term_meta($c->parent, 'icon', true);
        $BObj = get_term_by('id',$c->parent,$c->taxonomy);
        $CategoryTitle = $BObj->name;
        $CategoryURL = get_term_link($BObj);
    }

}
echo '<div class="works-single">';
    echo '<div class="container">';
    	echo '<div class="works-single-parent-">';
	        echo '<Breadcrumb>';
	            Breadcrumb($post);
	        echo '</Breadcrumb>';
	        echo '<div class="works-single-title-">';
	        	echo '<h1>'.$post->post_title.'</h1>';
	      	echo '</div>';
	        echo '<div class="works-single-img-">';
	        	if(!empty(get_post_meta($post->ID,'video_Post',1))){
		        	foreach (get_post_meta($post->ID,'video_Post',1) as $video) {
		                echo '<div class="video_Post">'.$video['videocompany'].'</div>';
		            }
	            }
	        	echo '<img src="'.get_the_post_thumbnail_url($post->ID).'" width="100%" height="100%" title="'.$post->post_title.'" alt="'.$post->post_title.'" >';
	        	if(!empty(get_post_meta($post->ID,'imageservice',1))){
		        	foreach (get_post_meta($post->ID,'imageservice',1) as $src) {
		                echo '<img src="'.$src.'" width="100%" height="100%" title="'.$post->post_title.'" alt="'.$post->post_title.'" />';
		            }
	            }
	        echo '</div>';
	      	echo '<div class="ArticleDetails details">';
	            the_content();
	        echo '</div>';
	        echo '<div class="box-user">';
	        	echo '<div class="user-data-title-">';
		        	echo '<h3>نظرة عامة</h3>';
		      	echo '</div>';
	             echo '<ul class="box-widght-data">';
                    foreach( $works_steps as $post ) {
                      echo '<li class="Client">';
                          echo '<p>'.$post['title_right'].':</p>';
                          echo '<span>'.$post['title_left'].'</span>';
                      echo '</li>';
                      
                      
                }
                echo '</ul>';
	        echo '</div>';
	    echo '</div>';
	    echo '<div class="-single-parent-post--sidebar">';
            $similars = get_posts(
		        array(
		            "post_type"         => 'post',
		            "posts_per_page"    => 5,
		            "post__not_in"      => array($post->ID),
		            "cat"               => array_values($termIDs),
		        )
		    );
		    $similar = get_posts(
		        array(
		            "post_type"         => 'works',
		            "posts_per_page"    => 5,
		            "post__not_in"      => array($post->ID),
		            "cat"               => array_values($termIDs),
		        )
		    );
		    if(!empty($similar)){
		    echo '<div class="LoaderPostsRelaterIndex-box">';
                echo '<div class ="sidebar-title">';
                    echo '<h2><span><i class="fa fa-earth-america"></i></span>اعمال متشابة</h2>';
                    echo '<p>اهم المقالات </p>';
                echo '</div>';
                echo '<div class="LoaderPostsRelaterIndex-1">';
                    foreach( $similar as $postes ) {
                            $this->Part("Griditem", array("post"=>$postes,'model'=>3));
                    }
                echo '</div>';

            echo '</div>';
		    }
		    if(!empty($similars)){
			    echo '<div class="LoaderPostsRelaterIndex-box">';
	                echo '<div class ="sidebar-title">';
	                    echo '<h2><span><i class="fa fa-earth-america"></i></span>اعمال  ذات صلة</h2>';
	                    echo '<p>اهم المقالات </p>';
	                echo '</div>';
	                echo '<div class="LoaderPostsRelaterIndex-1">';
	                    foreach( $similars as $postes ) {
	                            $this->Part("Griditem", array("post"=>$postes,'model'=>3));
	                    }
	                echo '</div>';

	            echo '</div>';
        	}
        echo '</div>';
    echo '</div>';
echo '</div>';
