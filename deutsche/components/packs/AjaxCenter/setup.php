<?php
class AjaxCenter {
     private $ThemeStatic;
	function __construct() {
		$this->ThemeStatic = new ThemeStatic;
	}
	public function QueryEndpoint() {
		add_rewrite_endpoint( 'AjaxCenter', EP_ROOT );
	}
	public function AjaxCenterPage() {
		if($AjaxCenter = get_query_var('AjaxCenter')){
			$Action = explode('/', $AjaxCenter)[0];
			$Before = explode('/', $AjaxCenter);
			if (isset($Before[1])){
				$Params = explode($Action.'/', $AjaxCenter)[1];
			}else{
				$Params = explode($Action.'/', $AjaxCenter)[0];
			}
			$AjaxCenterPath = get_template_directory().'/components/packs/AjaxCenter/';
			$AjaxCenterURL = get_template_directory_uri().'/components/packs/AjaxCenter/';
			$Action = preg_replace('/[^A-Za-z0-9_-]/', '', $Action);
			$file = get_template_directory().'/components/packs/AjaxCenter/'.$Action.'.php';
			if ( $Action === '' || $Action === 'setup' || ! file_exists( $file ) ) { status_header(404); die(); }
	    	require($file);
	    	die();
	    }
	}
	public function Setup() {
		add_action( 'init', array( $this, 'QueryEndpoint' ) );
		add_action( 'BeforeHeader', array( $this, 'AjaxCenterPage' ) );
	}
}
(new AjaxCenter)->Setup();