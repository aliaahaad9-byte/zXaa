<?php
class SearchCenter {
	public function QueryEndpoint() {
		add_rewrite_endpoint( 'searchcenter', EP_ROOT );
	}
	public function SearchCenterPage() {
		if($SearchqQuery = get_query_var('searchcenter')){
	    	global $wp_query, $wp_rewrite, $post, $wpdb, $ThemeStatic;
			$SearchQuery = urldecode($SearchqQuery);
			//#############
			// Movies
			//#############
			$SearchMoviesIDs = $ThemeStatic->get_posts(
				array(
					'post_type'		=> 'post',
					's'				=> $SearchQuery,
					'posts_per_page'=> 15
				)
			);
			$ThemeStatic->Part('trending-box');
			if( empty($SearchMoviesIDs) ) {
				echo '<div class="NothingFoundFilter">';
					echo '<i class="ion ion-md-sad"></i>';
					echo '<p>تعذّر الوصول لأي نتيجة مطابقة للبحث الخاص بك</p>';
					echo '<em>جرّب البحث بطريقة أخري .. مشاهدة سعيدة :)</em>';
				echo '</div>';
			}else {
				echo '<h2>نتائج البحث عن <strong>"'.$SearchQuery.'"</strong></h2>';
				echo '<ul class="SearchingBlocks">';
					foreach ($SearchMoviesIDs as $m) {
						$ThemeStatic->Part('box', array('post'=>$post));
					}
				echo '</ul>';
				echo '<a href="'.home_url('/search/'.$SearchQuery.'/').'" class="MoreResults Hoverable">مزيد من النتائج <i class="ion ion-md-arrow-back"></i></a>';
			}
			die();
	    }
	}
	public function Setup() {
		add_action( 'init', array( $this, 'QueryEndpoint' ) );
		add_action( 'BeforeHeader', array( $this, 'SearchCenterPage' ) );
	}
}
(new SearchCenter)->Setup();