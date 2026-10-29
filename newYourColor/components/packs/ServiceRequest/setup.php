<?php
/**
 * تنسيق صفحة «طلب الخدمة» — يُحمَّل فقط في الصفحات التي اختير لها نموذج service-request.
 */
add_action( 'wp_head', function () {
	if ( ! is_page() ) {
		return;
	}
	if ( 'service-request' !== get_post_meta( get_queried_object_id(), 'template', true ) ) {
		return;
	}
	echo '<link rel="stylesheet" href="' . esc_url( get_template_directory_uri() . '/components/packs/ServiceRequest/service-request.css?ver=1.0.0' ) . '">';
}, 30 );
