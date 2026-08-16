<?php// ## SETUPCRON ## //
add_filter( 'cron_schedules', 'ps_upd' );
function ps_upd( $schedules ) {
	$schedules['supdateposydate'] = array(
		'interval' => 3,
		'display'  => __( 'Updata post date' ),
	); 
	return $schedules;
}
// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'Cronps_upd' ) ) {
	wp_schedule_event( time(), 'supdateposydate', 'Cronps_upd' );
}
add_action( 'Cronps_upd', 'supdateposydate', 10, 0 );
function supdateposydate() {
	$PostArguments = array(
		'post_type'=>'post',
		'posts_per_page'=>1,
		'meta_query'=>array(
			'relation'=>'AND',
			array(
				'relation'=>'OR',
				array(
					'key'=>'last_update_post',
					'value'=>'',
					'compare'=>'!=',
				),
			),
		),
	);
	foreach (get_posts($PostArguments) as $post) {
		$last_update_post = get_post_meta($post->ID,'last_update_post',true);
		if(!empty($last_update_post)){
			wp_update_post(
				array(
					'ID' => $post->ID,
						'post_date' => date('Y-m-d H:i:s',$last_update_post),
					)
			);
			delete_post_meta($post->ID, 'last_update_post');
		}
	}
}