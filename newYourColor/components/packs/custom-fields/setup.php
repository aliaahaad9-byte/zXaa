<?php

define('APB_Path', trailingslashit( dirname( __FILE__ ) ));

$APBURL = explode(get_template_directory(), trailingslashit( dirname( __FILE__ ) ))[1];

$APBURL = get_template_directory_uri().$APBURL;

define('APB_URL', $APBURL);

/////////////////////////////////

class APB {

	private $args;

	private $boxes;



	function __construct($arguments=array()) {

	    $this->args = $arguments;

	    $layouts = array();

	    $metaboxes = array();

	    $Params = $_GET;

		require(APB_Path.'/layouts.php');

		require(APB_Path.'/fields.php');

		$this->layouts = $layouts;

		$this->boxes = $metaboxes;

		if (isset($Params['action']) and $Params['action'] == 'edit' and isset($_GET['post'])) {

			$this->type = 'edit';

			$this->post = $_GET['post'];

		}else {

			$this->type = 'add';

			$this->post = 0;

		}

	}

	private function Methods() {

		return $_POST;

	}

	public function CanSave() {

		$return = false;

		if( current_user_can('edit_posts') and current_user_can('edit_published_posts') ) {

			$return = true;

		}

		return $return;

	}

	public function AdminFooter() {

		// JS & JQuery

		echo '<script>var $ = jQuery;</script>';

		echo '<script type="text/javascript" src="'.APB_URL.'UI/js/bootstrap.bundle.min.js"></script>';

		echo '<script type="text/javascript" src="'.APB_URL.'UI/js/bootstrap-colorpicker.min.js"></script>';

		echo '<script type="text/javascript" src="'.APB_URL.'UI/js/codemirror.js"></script>';

		//echo '<script type="text/javascript" src="'.APB_URL.'UI/js/DatePicker.js"></script>';

		echo '<script type="text/javascript" src="'.APB_URL.'UI/js/jquery.richtext.min.js"></script>';		  

		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>';

		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js"></script>';


		(new APBFields)->PinnedJQuery();

		echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';

		if( !isset($_GET['tag_ID']) ) {

			echo '<script src="'.APB_URL.'UI/addswitch.js?'.rand().'" type="text/javascript"></script>';

		}

		echo '<script src="'.APB_URL.'UI/main.js?'.rand().'" type="text/javascript"></script>';

		if ( ! did_action( 'wp_enqueue_media' ) ) {

			wp_enqueue_media();

		}

	 	wp_register_script('mediaelement', plugins_url('wp-mediaelement.min.js', __FILE__), array('jquery'), '4.8.2', true);

		wp_enqueue_script('mediaelement');



	 	wp_enqueue_script( 'myuploadscript', APB_URL . 'UI/js/UploadAction.js?rand="'.rand().'"', array('jquery'), null, false );

	}

	public function SetupEnqueue() {

		// CSS

		echo '<link rel="stylesheet" type="text/css" media="all" href="'.APB_URL.'UI/style.css?'.rand().'" />';

		if( !is_rtl() ) {

			echo '<link rel="stylesheet" type="text/css" media="all" href="'.APB_URL.'UI/ltr.css?'.rand().'" />';

		}

		echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.min.css" />';

		echo '<link rel="stylesheet" type="text/css" media="all" href="'.APB_URL.'UI/css/codemirror.css" />';

		echo '<link rel="stylesheet" type="text/css" media="all" href="'.APB_URL.'UI/css/richtext.min.css" />';

		echo '<link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.12.0/css/pro.min.css">';

		//echo '<link href="'.APB_URL.'UI/css/datepicker.css" rel="stylesheet">';

		echo '<link href="'.APB_URL.'UI/css/colorpicker.css" rel="stylesheet">';

		//echo '<link href="http://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">';

		echo '<link rel="stylesheet" media="all" type="text/css" href="https://site-assets.fontawesome.com/releases/v6.0.0/css/all.css" />';

	}

	public function SetupAPB() {

		add_action( 'add_meta_boxes', array($this,'SetupMetaBox') );

		add_action( 'admin_enqueue_scripts', array($this, 'SetupEnqueue') );

		add_action('admin_footer', array($this, 'AdminFooter'));

		add_action( 'save_post', array( $this, 'SavePost' ), 10, 2 );

		add_action( 'edit_post', array( $this, 'SavePost' ), 10, 2 );

		add_action( 'after_setup_theme', array( $this, 'AfterThemeSetup' ) );

	}

	public function GetMetaBox($post, $extra) {

		echo '<input type="hidden" name="apbupdate" value="1" />';

		$args      = $extra['args'];

		if( isset($args['callback']) ) {

			require($args['callback']);

		}else {

			$MetaboxID = explode('APBMetaBox-', $extra['id'])[1];

			(new APBFields)->SetupFields($args, $MetaboxID, '0');

		}

	}

	public function SaveMetaBox() {

		$args      = $extra['args'];

		if( isset($args['callback']) ) {

			require($args['callback']);

		}else {

			$MetaboxID = explode('APBMetaBox-', $extra['id'])[1];

			(new APBFields)->SetupFields($args, $MetaboxID, '0');

		}

	}

	public function GetMetaTaxonomy($id) {

		$args = $this->boxes[$id];

		if ( ! did_action( 'wp_enqueue_media' ) ) {

			wp_enqueue_media();

		}

		$title = (is_rtl()) ? $args['name'] : $args['nameEN'];

		echo '</table>';

		echo '<div id="APBMetaBox-'.$id.'" class="postbox">';

		    echo '<h2 style="margin: 0; padding: 8px 12px; border-bottom: 1px solid #eee; font-size: 14px; line-height: 1.4;"><span>'.$title.'</span></h2>';

		    echo '<div class="inside" style="padding:15px;">';

				echo '<input type="hidden" name="apbupdate" value="1" />';

				if( isset($args['callback']) ) {

					require($args['callback']);

				}else {

					(new APBFields)->SetupFields($args, $id, '0', true);

				}

			echo '</div>';

		echo '</div>';

	}

	public function SaveMetaTaxonomy($tagID) {	
		#print_r($this->Methods());die;
		if( $this->CanSave() and isset($this->Methods()['apbupdate']) ) {

			foreach ($this->boxes as $k => $v) {

				if( isset($v['taxonomy']) ) {

					if( isset($v['type']) and $v['type'] == 'layouts' ) {

						if( isset($this->Methods()[$k]) ) {

							$this->UpdateTerm($tagID, $k, $this->Methods()[$k]);

						}else {

							$this->DeleteTerm($tagID, $k);

						}

					}else if( isset($v['type']) and $v['type'] == 'fields' && isset($v['fields'])) {

						foreach ($v['fields'] as $kf => $vf) {

							if( isset($this->Methods()[$vf['id']]) ) {

								$this->UpdateTerm($tagID, $vf['id'], $this->Methods()[$vf['id']]);
								if( $vf['type'] == 'file' && isset($this->Methods()[$vf['id'].'_id']) ){
									$this->UpdateTerm($tagID, $vf['id'].'_id', $this->Methods()[$vf['id'].'_id']);
								}

							}else {

								$this->DeleteTerm($tagID, $vf['id']);
								if( $vf['type'] == 'file'){
									$this->DeleteTerm($tagID, $vf['id'].'_id');
								}


							}

						}

					}

				}

			}



			if(isset($_POST['Certificate'])){

				$this->UpdateTerm($tagID,'Certificate',$_POST['Certificate']);

				//print_r($_POST['Certificate']);die;

			}else if(  isset($this->Methods()['Certificate'])){

				$this->UpdateTerm($tagID, $vf['id'], $this->Methods()['Certificate']);

				//print_r($this->Methods()['Certificate']);die;

			}else{

				$this->DeleteTerm($tagID,'Certificate');				

			}

		}



		if( empty(get_term_meta($tagID, 'datePublished', true)) ) {

			update_term_meta($tagID, 'datePublished', time());

		}else {

			update_term_meta($tagID, 'dateModified', time());

		}

	}

	public function UpdatePost($id, $key, $val) {

		if( $this->CanSave() ) {

			update_post_meta($id, $key, $val);

		}

	}

	public function RemovePost($id, $key) {

		if( $this->CanSave() ) {

			delete_post_meta($id, $key);

		}

	}

	public function UpdateTerm($id, $key, $val) {

		if( $this->CanSave() ) {

			update_term_meta($id, $key, $val);

		}

	}

	public function DeleteTerm($id, $key) {

		if( $this->CanSave() ) {

			delete_term_meta($id, $key);

		}

	}

	public function SavePost($postID) {

		global $post;

		if( $this->CanSave() and isset($this->Methods()['apbupdate']) ) {

			/*echo '<pre>';

			print_r($_POST);
			echo '</pre>';
			die;*/

			foreach ((is_array($this->boxes)) ? $this->boxes : array() as $k => $v) {

				if( isset($v['ptype']) and !isset($v['callback']) and in_array(get_post_type($postID), $v['ptype']) ) {

					if( $v['type'] == 'layouts' ) {

						if( isset($this->Methods()[$k]) ) {

							$this->UpdatePost($postID, $k, $this->Methods()[$k]);

						}else {

							$this->RemovePost($postID, $k);

						}

					}else if( $v['type'] == 'fields' ) {

						foreach ($v['fields'] as $kf => $vf) {

							if( isset($this->Methods()[$vf['id']]) ) {

								$this->UpdatePost($postID, $vf['id'], $this->Methods()[$vf['id']]);

							}else {

								$this->RemovePost($postID, $vf['id']);

							}

						}

					}

				}

			}

			$MyPost = get_post($postID);

			if($MyPost->post_type == 'contest_participants'){

				$Competition_id = get_post_meta($MyPost->ID,'Competition_id', true);

				if( empty($Competition_id) ) {

					$current_competition = ( is_array( get_option('current_competition') ) ) ? get_option('current_competition') : array();

					if( !empty( $current_competition ) ){

						update_post_meta($MyPost->ID,'Competition_id',$current_competition['id']);

					}
				}

				$updated_contest_v1 = get_post_meta($MyPost->ID,'updated_contest_v1', true);

				if( empty($updated_contest_v1) ) {

					update_post_meta($MyPost->ID,'updated_contest_v1', 'notupdated');

				}
			}
		}

		if( isset($_POST['parent_page']) ) {

			update_post_meta($postID, 'parent_page', $_POST['parent_page']);

		}

		if( isset($_POST['language']) ) {

			update_post_meta($postID, 'language', $_POST['language']);

		}

		if( empty(get_post_meta($postID, 'datePublished', true)) ) {

			update_post_meta($postID, 'datePublished', time());

		}else {

			update_post_meta($postID, 'dateModified', time());

		}

	}

	public function stripslashes_deep($value)	{

	    $value = is_array($value) ?

	                array_map('stripslashes_deep', $value) :

	                stripslashes($value);



	    return $value;

	}

	public function AdSizes() {

		$sizes = [];

		foreach (get_terms( array("taxonomy"=>'ads-sizes', "hide_empty"=>false) ) as $t) {

			$sizes[$t->term_id] = $t->name;

		}

		return $sizes;

	}

	public function AdCategories() {

		$categories = [];

		foreach (get_terms( array("taxonomy"=>'ads-categories', "hide_empty"=>false) ) as $t) {

			$categories[$t->term_id] = $t->name;

		}

		return $categories;

	}

	public function SetupMetaBox() {

		foreach ($this->boxes as $k => $box) {

			if( isset($box['ptype']) ) {

				add_meta_box(

				    'APBMetaBox-'.$k,

				    __( $box['name'], 'APB' ),

				    array($this, 'GetMetaBox'),

				    $box['ptype'],

				    $box['context'],

				    $box['priority'],

				    $box

				);

			}

		}

	}

	public function AfterThemeSetup() {

		foreach ($this->boxes as $k => $box) {

			if( isset($box['taxonomy']) ) {

				if( is_array($box['taxonomy']) ) {

					foreach ($box['taxonomy'] as $tax) {

						add_action ( 'edited_'.$tax.'', array( $this, 'SaveMetaTaxonomy' ));

						add_action ( $tax.'_edit_form_fields', function($a) use ($k) { (new APB)->GetMetaTaxonomy($k); } );

					}

				}else {

					add_action ( 'edited_'.$box['taxonomy'].'', array( $this, 'SaveMetaTaxonomy' ));

					add_action ( $box['taxonomy'].'_edit_form_fields', function($a,$b) use ($box,$k) { (new APB)->GetMetaTaxonomy($box); } );

				}

			}

		}

	}

}

require(APB_Path.'/APBLoader/core.php');

$APB = new APB();

$APB->SetupAPB();