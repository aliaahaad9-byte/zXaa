<?
$CurrentURL = explode(get_template_directory(), trailingslashit( dirname( __FILE__ ) ))[1];
$CurrentURL = get_template_directory_uri().$CurrentURL;
define("WidgetURL", $CurrentURL);
define("WidgetDir", $CurrentDir);
require(WidgetDir.'/FieldsBuilder/setup.php');
// Widgets
	function Theme__Widgets() {

	   
		//# Home

			register_sidebar( array(
		        'name'          => 'الصفحة  الرئيسية',
		        'id'            => 'home',
		        'before_widget' => '<div id="%1$s" class="%2$s">',
		        'after_widget'  => '</div>',
		        'before_title'  => '<div class="widget--title">',
		        'after_title'   => '</div>',
		    ) );
		    register_sidebar( array(
		        'name'          => 'posts-sidebar',
		        'id'            => 'posts-sidebar',
		        'before_widget' => '<div id="%1$s" class="%2$s">',
		        'after_widget'  => '</div>',
		        'before_title'  => '<div class="widget--title">',
		        'after_title'   => '</div>',
		    ) );
			

		
	}
	add_action( 'widgets_init', 'Theme__Widgets' );

// Widget Models
	setlocale(LC_MONETARY,"en_US");
	foreach( glob($CurrentDir.'/widgets/*.php') as $file ) {
		require($file);
	}

// Widget Styles
	function AdvancedStyling($advanceds) {
		$attrs = array();
		$style = '';
		if( isset($advanceds['bg']) and !empty($advanceds['bg']) ) {
			$style .= 'background-color:'.$advanceds['bg'].';';
		}
		if( isset($advanceds['uicolor']) and !empty($advanceds['uicolor']) ) {
			$style .= '--uicolor:'.$advanceds['uicolor'].';';
		}
		if( isset($advanceds['text']) and !empty($advanceds['text']) ) {
			$style .= '--textcolor:'.$advanceds['text'].';';
		}
		$attrs[] = 'style="'.$style.'"';
		return implode(' ', $attrs);
	}
	function Widget__Header() {
		echo '<link href="'.WidgetURL.'assets/css/widgets-admin.css?'.rand().'" rel="stylesheet">';
		echo '<script type="text/javascript" src="'.YTS_URL.'UI/js/UploadAction.js"></script>';
		echo '<script type="text/javascript" src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>';
		echo '<script src="'.WidgetURL.'assets/js/widgets-admin.js?'.rand().'" type="text/javascript"></script>';
		echo '<link href="https://kit-pro.fontawesome.com/releases/v5.13.0/css/pro.min.css" rel="stylesheet">';
		echo '<link href="'.(new ThemeStatic)->StylesURL.'Font.css?'.rand().'" rel="stylesheet">';
	}
	add_action('admin_footer', 'Widget__Header');