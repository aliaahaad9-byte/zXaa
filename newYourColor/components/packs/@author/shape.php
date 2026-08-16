<?php
$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));

global $wp, $wp_rewrite, $wp_query;
$paged = $this->Paged();
$AvatarAuthor = get_avatar_url($curauth->ID);
echo'<section class="project project-archive">';
	echo'<div class="container">';
		echo'<div class="Yc-author-auto-container">';
			echo'<div class="Yc-author-box-right">';
			
				echo '<div class="page-image">';
					echo '<img width="100%" height="100%" src="'.$AvatarAuthor.'" alt="'.$current_user->display_name.'" />';
					echo '<svg role="img">
	                    <use xlink:href="/icons.svg?ver=138#star"></use>
	                </svg>';
				echo '</div>';
					
				echo '<div class="page-title">';
					echo '<h1>'.$curauth->display_name.'</h1>';
					echo '<div class ="CountPosts">';
						
						echo '<em>عدد المقالات المنشورة<span>('.count_user_posts( $curauth->ID , ['post','book']).')</span></em>';
					
					
					echo '</div>';
				echo '</div>';
					
			echo '</div>';
			echo'<div class="Yc-author-box-left">';
				echo'<breadcrumb>';
		            Breadcrumb();
		        echo'</breadcrumb>';
		        echo '<div class="page-content">';
		        	echo '<h2 class="news-title-content">نظرة عامة</h2>';
		        	
					echo'<p>'.$curauth->description.'</p>';
		        echo '</div>';
		        $args = array(
					'post_type'		=> 'post',
					'posts_per_page'=> 9,
					'paged'=>$paged,
					'tax_query'		=> $tax_query

				);

				$this->Part('Posts',array('AutoLoadmore'=>false,'UniqId'=>$UniqId,'AutoLoadmore'=>true,'author'=>$curauth->ID));
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</section>';



