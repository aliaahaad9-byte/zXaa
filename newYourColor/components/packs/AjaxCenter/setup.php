<?php 
class AjaxCenter {
	function __construct() {
		$this->ThemeStatic = new ThemeStatic;
	}
	public function QueryEndpoint() {
		add_rewrite_endpoint( 'AjaxCenter', EP_ROOT );
	}
	public function AjaxCenterPage() {
		if($AjaxCenter = get_query_var('AjaxCenter')){
			$Action = explode('/', $AjaxCenter)[0];
			if( strpos( $AjaxCenter , $Action.'/') !== FALSE ){
				$Params = explode($Action.'/', $AjaxCenter)[1];
			}else{
				$Params = '';
			}
			$Action = sanitize_file_name(basename($Action));
			$AjaxCenterPath = get_template_directory().'/components/packs/AjaxCenter/';
			$AjaxCenterURL = get_template_directory_uri().'/components/packs/AjaxCenter/';
			if( $Action != '' && file_exists($AjaxCenterPath.$Action.'.php') ) {
				require($AjaxCenterPath.$Action.'.php');
			}
	    	die();
	    }
	}
	public function Setup() {
		add_action( 'init', array( $this, 'QueryEndpoint' ) );
		add_action( 'BeforeHeader', array( $this, 'AjaxCenterPage' ) );
	}
}
(new AjaxCenter)->Setup();