<?
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
	    	require(get_template_directory().'/components/packs/AjaxCenter/'.$Action.'.php');
	    	die();
	    }
	}
	public function Setup() {
		add_action( 'init', array( $this, 'QueryEndpoint' ) );
		add_action( 'BeforeHeader', array( $this, 'AjaxCenterPage' ) );
	}
}
(new AjaxCenter)->Setup();