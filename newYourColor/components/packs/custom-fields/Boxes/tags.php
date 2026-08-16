<?php 
if (isset($_GET['post'])) {
	$post_id = $_GET['post'];
	if(!empty(get_the_terms($post_id,'post_tag',1))){
		$tags = get_post_meta($post_id,'tags_order',1);
		$tags = explode(',',$tags);
		$tags = array_filter($tags);

		echo '<tags--order>';
			foreach (get_the_terms($post_id,'post_tag',1) as $t) {
				echo '<tag '.( in_array($t->term_id, $tags) ? 'class="active"'  : '' ).' data-id="'.$t->term_id.'">';
					echo '<span><i class="fa-solid fa-check"></i></span>';
					echo '<strong>'.$t->name.'</strong>';
				echo '</tag>';
			}
		echo '</tags--order>';
	}



}