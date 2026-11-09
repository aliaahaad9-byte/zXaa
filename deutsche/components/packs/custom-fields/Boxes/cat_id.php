<?php 
if (isset($_GET['post'])) {
	$post_id = $_GET['post'];
	$categories = get_post_meta($post_id,'categories',1);
	$categories = explode(',',$categories);
	$categories = array_filter($categories);

	echo '<categories--order>';
		foreach (get_the_terms($post_id,'category',1) as $t) {
			echo '<categories '.( in_array($t->term_id, $categories) ? 'class="active"'  : '' ).' data-id="'.$t->term_id.'">';
				echo '<span><i class="fa-solid fa-check"></i></span>';
				echo '<strong>'.$t->name.'</strong>';
			echo '</categories>';
		}
	echo '</categories--order>';



}