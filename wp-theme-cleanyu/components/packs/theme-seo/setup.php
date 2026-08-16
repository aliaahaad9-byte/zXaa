<?

class ThemeSeo {
	function __construct() {
		$this->sep = '-';
		$this->MySiteName = ' '.get_option('sitename');
		$this->LastWord = ' '.$this->sep.$this->MySiteName;
	}
	public function Title(){
		global $post;
		$title = '';
		if( is_page() ){
			if( $post->post_parent > 0 ) {
				$title .= get_post($post->post_parent)->post_title.' '.$this->sep.' ';
			}
			$title .= $post->post_title;
		}else if( is_author() ){
			$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
			$title .= 'الملف الشخصي لـ '.$curauth->display_name;
		}else if( $actor = get_query_var('actor') ) {
			$ActorsDB = new TX_actor();
	    	$data = $ActorsDB->get(explode('/', $actor)[0], 'slug');
			$title .= $data->name;
		}else if( is_category() ){
			$obj = get_queried_object();
			if( $obj->parent > 0 ) {
				$title .= get_term($obj->parent, 'category')->name.' '.$this->sep.' ';
			}else if( get_term_meta($obj->term_id, 'parentc', true) > 0 ) {
				$parent = get_term_meta($obj->term_id, 'parentc', true);
				$title .= get_term($parent, 'category')->name.' '.$this->sep.' ';
			}
			$title .= $obj->name;
		}else if( is_tax() ){
			$obj = get_queried_object();
			if( $obj->parent > 0 ) {
				$title .= get_term($obj->parent, $obj->taxonomy)->name.' '.$this->sep.' ';
			}else if( get_term_meta($obj->term_id, 'parentc', true) > 0 ) {
				$parent = get_term_meta($obj->term_id, 'parentc', true);
				$title .= get_term($parent, $obj->taxonomy)->name.' '.$this->sep.' ';
			}
			$title .= $obj->name;
		}else if( is_single() ){
			$title .= $post->post_title;
			if( get_query_var('watch') ) {
				$title .= ' '.$this->sep.' مشاهدة و تحميل';
			}else if( get_query_var('similar') ) {
				$title .= ' '.$this->sep.' مواضيع مشابهة';
			}
		}else {
			$title .= get_bloginfo('name');
		}
		return $title.$this->LastWord;
	}
}