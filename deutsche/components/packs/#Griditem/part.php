<?php
$url = get_the_permalink($post->ID);
$category = get_the_terms($post->ID, 'category', '');
$img = get_the_post_thumbnail_url($post->ID, 'default');
$time = 'منذ '.human_time_diff( date('U', strtotime($post->post_date)), current_time('timestamp') );
$title = $post->post_title;
$Views = (INT) get_post_meta($post->ID,'views',true);
$likes = (INT) get_post_meta($post->ID, 'likes', true);
$color = false;
$icon = '';
foreach( $category as $c ) {
	if( $c->parent == 0 && $color == false) {
		$icon = get_term_meta($c->parent,'icon',true);
	}else if( $c->parent > 0 && $color == false) {
		$icon = get_term_meta($c->parent,'icon',true);
	}
}
$comment_num = get_comments_number($post->ID);
$classes = array();
$excerpt = mb_substr(wp_trim_words($post->post_content, 200), 0, 100).'..';

$author = get_user_by ('ID',$post->post_author);
$author = get_userdata($post->post_author);
$AvatarAuthor = get_avatar_url($author);
if(empty($AvatarAuthor)){
	$AvatarAuthor = '<i class="fa-solid fa-user-pen"></i>';
}else{
	$AvatarAuthor = '<img width="35px" height="35px" alt="'.$author->user_login.'" src="'.$AvatarAuthor.'">';
}


if(!isset($model)) $model = 1;
if(!isset($bottom)) $bottom = true;
if(!isset($top)) $top = true;
if(!isset($hidecat)) $hidecat = false;
$ExpDate = DisplayDate( strtotime($post->post_date) );
$ExpDate = explode(' ', $ExpDate);
$ExpDate[0] = str_replace(',','',$ExpDate[0]);
#
$isVideo = false;
if( strpos($post->post_content,'/watch') !== false ) {
	$isVideo = true;
}
if( $model == 1 ) {
	echo '<div data-id="'.$post->ID.'" class="-GridItem-'.$model.' hover-custom">';
		
		echo '<div class="-GridItem-'.$model.'-ThumbBox">';
		    echo '<a href="'.get_the_permalink($post->ID).'">';
				if(!empty($img)){
                    // Display the compressed image
                    echo '<img data-loader-src="'.$img.'" width="100%" height="100%" title="'.$post->post_title.'" alt="'.$post->post_title.'" >';
				}
			echo '</a>';	
		echo '</div>';
		
		
		echo '<div class="-GridItem-'.$model.'-info">';
			echo '<div class="-GridItem-'.$model.'-category-Item">';
				foreach( array_slice($category, 0, 1) as $tag ) {
					if($tag->parent > 0){
						$icon = get_term_meta($tag->parent,'icon',true);
					}else{
						$icon = get_term_meta($tag->term_id,'icon',true);
					}
					echo '<a class="category-items" href="'.get_term_link($tag).'" aria-label="'.$title.'">';
						echo $tag->name;
					echo '</a>';
				}
				echo '</div>';	
			echo '<a href="'.get_the_permalink($post->ID).'" aria-label="'.$title.'">';
				echo '<h3>'.$title.'</h3>';
			echo '</a>';
			
		echo '</div>';
		
		//
	echo '</div>';
}
else if( $model == 2 ) {
	echo '<div data-id="'.$post->ID.'" class="-GridItem-2 hover-custom">';
		echo '<div class="-GridItem-2--ThumbBox">';
			echo '<a href="'.get_the_permalink($post->ID).'" aria-label="'.$title.'">';
				if(!empty($img)){
                    // Display the compressed image
                    echo '<img data-loader-src="'.$img.'" width="100%" height="100%" title="'.$post->post_title.'" aria-label="'.$post->post_title.'" >';
				}
			echo '</a>';
		echo '</div>';
		//
		echo '<div class="-GridItem-'.$model.'-info">';
			echo '<div class="-GridItem-'.$model.'-category-Item">';
				foreach( array_slice($category, 0, 1) as $tag ) {
					if($tag->parent > 0){
						$icon = get_term_meta($tag->parent,'icon',true);
					}else{
						$icon = get_term_meta($tag->term_id,'icon',true);
					}
					echo '<a class="category-items" href="'.get_term_link($tag).'">';
						echo $tag->name;
					echo '</a>';
				}	
			echo '</div>';
			echo '<div class="GridItem-2-title-model">';
				echo '<a href="'.get_the_permalink($post->ID).'" aria-label="'.$title.'">';
					echo '<h3>'.$title.'</h3>';
						echo '<p>'.$excerpt.'</p>';
					
				echo '</a>';
			echo '</div>';
			//
			
		echo '</div>';
	echo '</div>';
}
// # list post
else if( $model == 3 ) {

	echo '<div data-id="'.$post->ID.'" class="-GridItem-'.$model.' hover-custom">';
			echo '<div class="-GridItem-'.$model.'-ThumbBox">';
    				if(!empty($img)){
                        // Display the compressed image
                        echo '<img data-loader-src="'.$img.'" width="100%" height="100%" title="'.$post->post_title.'" alt="'.$post->post_title.'" >';
    				}
			echo '</div>';
			//
			echo '<div class="-GridItem-'.$model.'-info">';
				echo '<div class="GridItem-'.$model.'-title-model">';
					echo '<a href="'.get_the_permalink($post->ID).'">';
						echo '<h3>'.$title.'</h3>';
					echo '</a>';
				echo '</div>';
				//
			echo '</div>';
	echo '</div>';
}
else if( $model == 4 ) {
	echo '<div data-id="'.$post->ID.'" class="-GridItem-2 hover-custom">';
		//
		echo '<div class="-GridItem-'.$model.'-ThumbBox">';
			echo '<a href="'.get_the_permalink($post->ID).'">';
				if(!empty($img)){
		            // Display the compressed image
		            echo '<img src="'.$img.'" width="100%" height="100%" title="'.$post->post_title.'" alt="'.$post->post_title.'" >';
				}
			echo '</a>';
			echo '<div class="-GridItem-'.$model.'-category-Item">';
				foreach( array_slice($category, 0, 1) as $tag ) {
					if($tag->parent > 0){
						$icon = get_term_meta($tag->parent,'icon',true);
					}else{
						$icon = get_term_meta($tag->term_id,'icon',true);
					}
					echo '<a class="category-items" href="'.get_term_link($tag).'">';
						echo $tag->name;
					echo '</a>';
				}	
			echo '</div>';
		echo '</div>';
		echo '<div class="-GridItem-'.$model.'-box">';

			echo '<div class="GridItem-title-">';
				echo '<a href="'.get_the_permalink($post->ID).'">';
					echo '<h3>'.$title.'</h3>';
				echo '</a>';
			echo '</div>';
			//
		echo '</div>';
	echo '</div>';
}
