<?php 
/*
function AppendToContent($content) {
	global $post;
	$PostCategory = '';
	$author = get_userdata($post->post_author);
	$CategoryTerms = get_the_terms($post->ID, 'category', '');
	foreach( $CategoryTerms as $t ) {
		$PostCategory = $t->term_id;
	}
	$post_tag = get_the_terms($post->ID, 'post_tag', '');
	$category = get_the_terms($post->ID, 'category', '');
	$catTerms = array();
	foreach( $category as $term ) {
		$catTerms[] = $term->term_id;
	}
	$time = 'منذ '.human_time_diff( date('U', strtotime($post->post_date)), current_time('timestamp') );
	ob_start();
	echo '<div class ="fast_s">';
	$references = get_post_meta($post->ID, 'references', true);
	$questionsMeta = get_post_meta($post->ID, 'faq_post', true);
	$questions = array();
	foreach( $questionsMeta as $q ) {
		if( !empty($q['question']) ) {
            $questions[] = $q;
        }
	}
	if( !empty($references) ) {
		$references = explode(PHP_EOL, $references);
		$references = array_filter($references);
		echo '<div class="references">';
			echo '<h2>';
				echo '<i class="fa fa-plus"></i>';
				echo '<span>المراجع</span>';
			echo '</h2>';
			echo '<ul class="referencesbh">';
				foreach( $references as $link ) {
					$string = $link;
					preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $string, $match);
					foreach( $match[0] as $url ) {
						$string = str_replace($url, '<a href="'.$url.'" rel="nofollow" target="_blank" class="unline">'.mb_substr($url, 0, 60).'</a>', $string);
					}
					echo '<li>';
						echo '<i class="fal fa-external-link-alt"></i>';
						echo '<p>'.$string.'</p>';
					echo '</li>';
				}
			echo '</ul>';
		echo '</div>';
	}
	do_action('yc_hook_ad_location_content_below');
	echo '<div class="--similar-posts-container">';
		echo '<h2>مقالات ذات صلة</h2>';
		$posts = get_posts(
			array(
				"post_type"		=> 'post',
				"posts_per_page"=> 8,
				"post__not_in"	=> array($post->ID),
				"cat"			=> $PostCategory
			)
		);
		$i = 0;
		echo'<div class="blog_shap ">';
            (new ThemeStatic)->Part("post_sngile", array("args"=>$args));   
        echo'</div>';
	echo '</div>';
	$execute = $content.ob_get_clean();
	$execute = str_replace('<table', '<div class="table__container"><table', $execute);
	$execute = str_replace('</table>', '</table></div>', $execute);
	##
	preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $execute, $match);
	foreach( $match[0] as $url ) {
		if( strpos($url, 'wp-content/uploads') !== false ) {
			$execute = str_replace('<a href="'.$url.'"', '<a ', $execute);
		}
	}
	##
	return $execute;
}

add_filter('the_content', 'AppendToContent', 40);
*/