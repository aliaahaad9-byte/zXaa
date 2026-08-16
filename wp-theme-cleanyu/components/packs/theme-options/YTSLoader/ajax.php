<?
add_action( 'wp_ajax_YTSLayoutsBuilder', 'YTSLayoutsBuilder' );

function YTSLayoutsBuilder() {
	$YTS = new YTS();
	//
	(new YTSFields)->AjaxFields($_POST['layout'], $_POST['fields'], $_POST['numb']);
	wp_die();
}
add_action( 'wp_ajax_YTSAddLayoutBuilder', 'YTSAddLayoutBuilder' );

function YTSAddLayoutBuilder() {
	$YTS = new YTS();
	//
	(new YTSFields)->YTSAddLayoutBuilder($_POST['metabox'], $_POST['numb']);
	wp_die();
}
add_action( 'wp_ajax_YTSAddGroupFields', 'YTSAddGroupFields' );

function YTSAddGroupFields() {
	$YTS = new YTS();
	//
	(new YTSFields)->YTSAddGroupFields($_POST['metabox'], $_POST['group'], $_POST['numb']);
	wp_die();
}