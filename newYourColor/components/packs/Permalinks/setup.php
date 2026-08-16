<?php 

add_action( 'load-options-permalink.php', 'wpse30021_load_permalinks' );

function wpse30021_load_permalinks()

{

	if( isset( $_POST['cities_url'] ) ) {

		$value = trim($_POST['cities_url']);

		update_option( 'cities_url', sanitize_title($_POST['cities_url']) );

	}

	

	add_settings_field( 'cities_url', __( 'بادئة المدينة' ), 'cities_url', 'permalink', 'optional' );



}



function cities_url() {

	$value = get_option( 'cities_url' );	

	echo '<input type="text" value="' . esc_attr( $value ) . '" name="cities_url" id="cities_url" class="regular-text" />';

}





function check__permalinks( $old_theme_name, $old_theme = false ) {

    if( empty(get_option('cities_url')) ) {

    	update_option( 'cities_url', 'city' );

    }
    if( empty(get_option('reviews_title')) ) {

		update_option( 'reviews_title', 'ساهم بتقييمك إلى خدماتنا' );

    }

    if( empty(get_option('blocks__model')) ) {

		update_option( 'blocks__model', '1' );

    }

}

add_action( 'after_switch_theme', 'check__permalinks', 10, 2 );

add_action( 'wp_loaded', 'check__permalinks', 10, 2 );