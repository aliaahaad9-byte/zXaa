<?php
add_action( 'wp_enqueue_scripts', function(){

	#
	
		//wp_enqueue_style( 'main--css', (new ThemeStatic)->StylesURL.'main.css?'.rand() );
		//wp_enqueue_style( 'responsive--css', (new ThemeStatic)->StylesURL.'responsive.css?'.rand() );
	
	
} );
add_action( 'wp_footer', function(){
	//wp_enqueue_script ( 'news-script', get_template_directory_uri() . '/components/packs/'.urlencode('#footer').'/js/jquery-3.4.1.min.js' );
	//wp_enqueue_script ( 'news-owlcarousel', get_template_directory_uri() . '/components/packs/'.urlencode('#footer').'/js/owl.carousel.min.js' );

	//wp_enqueue_script ( 'news-init', get_template_directory_uri() . '/components/packs/'.urlencode('#footer').'/js/setup.js?'.rand() );
});

