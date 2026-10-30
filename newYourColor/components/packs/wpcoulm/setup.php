<?php
add_filter('manage_post_posts_columns', function($columns) {
	return array_merge($columns, ['pin' => __('مثبت ', 'textdomain')]);
});
 
add_action('manage_post_posts_custom_column', function($column_key, $post_id) {
	if ($column_key == 'pin') {
		$pin = get_post_meta($post_id, 'pin', true);
		if ($pin == 'on') {
			echo '<button type="button" class="pinyhis btn btn-sm btn-toggle active" data-toggle="button" data-pin="true" aria-pressed="true" data-id="'.$post_id.'"  autocomplete="off">
		        <div class="handle"></div>
		      </button>';
		} else {
			echo '<button type="button" class="pinyhis btn btn-sm btn-toggle" data-toggle="button" aria-pressed="false" data-pin="false" data-id="'.$post_id.'" autocomplete="off">
		        <div class="handle"></div>
		      </button>';
		}
	}
}, 10, 2);