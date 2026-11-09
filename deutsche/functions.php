<?php
ob_start();
class ThemeTree {
  private $args;
    private $_GET;
    private $_POST;

    public $Method;
    public $TempPath;
    public $TempURL;
    public $StylesURL;
    public $StylesPath;
    public $folderpath;
    public $packsPath;
    public $Packages;
  function __construct($args=array()) {
    $this->args = $args;
    $this->Method = array(
      'GETs'=>$_GET,
      'POSTs'=>$_POST,
    );
    $this->TempPath = get_template_directory();
    $this->TempURL = get_template_directory();
    $this->StylesURL = get_template_directory_uri().'/components/styles/';
    $this->StylesPath = get_template_directory().'/components/styles/';
    $this->folderpath = $this->TempPath.'/components/packs/*/';
    $this->packsPath = $this->TempPath.'/components/packs/';
    $this->Packages = array_filter(glob($this->folderpath), 'is_dir');
    if( !class_exists('ThemeStatic') ) {
      require($this->TempPath.'/syntax.php');
    }
  }
  public function AddTaxonomy($id, $ptypes, $name, $rewrite, $hierarchical) {
    $labels = array(
      'name' => __($name, 'PtypeLocalize' , 'post type general name'),
      'all_items' => __('كل العناصر', 'PtypeLocalize' , 'all items'),
      'add_new_item' => __('اضافة عنصر جديد', 'PtypeLocalize' , 'adding a new item'),
      'new_item_name' => __('اسم عنصر جديد', 'PtypeLocalize' , 'adding a new item'),
    );
    register_taxonomy( $id, $ptypes, 
      array( 
        'hierarchical' => $hierarchical,
        'rewrite' => $rewrite,
        'labels' => $labels,
      )
    );
  }
  public function AddPType($name, $singlename, $plus, $id, $public, $rewrite, $supports, $position) {
    $labels = array(
      'name'               => __( $name, 'post type general name', 'MycimaLocalize' ),
      'singular_name'      => __( $name, 'post type singular name', 'MycimaLocalize' ),
      'menu_name'          => __( $name, 'admin menu', 'MycimaLocalize' ),
      'name_admin_bar'     => __( $name, 'add new on admin bar', 'MycimaLocalize' ),
      'add_new'            => __( 'اضف جديد', 'search', 'MycimaLocalize' ),
      'add_new_item'       => __( 'إضافة '.$singlename.' جديد'.$plus, 'MycimaLocalize' ),
      'new_item'           => __( $singlename.' جديد'.$plus, 'MycimaLocalize' ),
      'edit_item'          => __( 'تعديل '.$singlename, 'MycimaLocalize' ),
      'all_items'          => __( 'كل '.$name, 'MycimaLocalize' ),
      'search_items'       => __( 'بحث  في '.$name, 'MycimaLocalize' ),
      'parent_item_colon'  => __( $singlename.' الرئيس', 'MycimaLocalize' ),
      'not_found'          => __( 'لا يوجد عناصر.', 'MycimaLocalize' ),
      'not_found_in_trash' => __( 'لا يوجد عناصر فى سلة المهملات.', 'MycimaLocalize' )
    );
    $args = array(
      'labels'             => $labels,
      'public'             => $public,
      'rewrite'             => $rewrite,
      'supports'           => $supports,
    );
    if( is_numeric($position) ) {
      $args['menu_position'] = $position;
    }
    register_post_type( $id, $args );
  }
  public function Require($path, $vars=array()) {
    extract($vars);
    if( file_exists($path) ) {
      require($path);
    }else {
      echo '<p><strong>هذا المسار غير موجود :</strong>'.$path.'</p>';
    }
  }
  public function Initialize() {

        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"];

        do_action('Initialize');


  }
}
$ThemeTree = new ThemeTree();
add_action('init', array($ThemeTree, 'Initialize'));
$ThemeStatic = new ThemeStatic();
$packs = $ThemeTree->Packages;
foreach ($packs as $pack) {
  if( substr(basename($pack), 0, 1) != '@' and substr(basename($pack), 0, 1) != '#' ) {
    $path = $pack.'setup.php';
    $ThemeTree->Require($path, array('CurrentDir'=>$pack));
  }
}
wp_reset_query();


add_filter( 'wpseo_next_rel_link', '__return_false' );
add_filter( 'wpseo_prev_rel_link', '__return_false' );
 function get_attachment_id_from_url( $attachment_url ) {
    global $wpdb;
    $attachment_id = false;

    // يستخرج رابط الصورة من القاعدة
    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='%s'";
    $result = $wpdb->get_var( $wpdb->prepare( $query, $attachment_url ) );

    if ( ! empty( $result ) ) {
        $attachment_id = (int) $result;
    }

    return $attachment_id;
}  

function get_alt_text_from_image_url($image_url) {
    // الحصول على المعلومات المتعلقة بالصورة
    $image_info = getimagesize($image_url);

    // الحصول على النص البديل "alt"
    $image_alt = isset($image_info['APP13']) ? $image_info['APP13'] : '';

    return $image_alt;
}

function disable_classic_theme_styles() {
    wp_deregister_style('classic-theme-styles');
    wp_dequeue_style('classic-theme-styles');
}
if(!is_admin()){
    add_filter('wp_enqueue_scripts', 'disable_classic_theme_styles', 100);
}
function update_protocol_links($html) {
  $html = preg_replace_callback('/(src|href)=[\"\']\/\/(.*?)[\"\']/', function($match) {
    return $match[1] . '="https://' . $match[2] . '"';
  }, $html);
  $html = preg_replace_callback('/Permissions-Policy: (.*)\r\n/', function($match) {
    $policies = explode(',', $match[1]);
    $supported_policies = array('geolocation', 'midi', 'notifications', 'push', 'sync-xhr', 'microphone', 'camera', 'magnetometer', 'gyroscope', 'speaker', 'vibrate', 'fullscreen', 'payment', 'usb', 'accelerometer', 'vr', 'xr-spatial-tracking');
    $filtered_policies = array_filter($policies, function($policy) use ($supported_policies) {
      return in_array(trim($policy), $supported_policies);
    });
    $filtered_policies = implode(',', $filtered_policies);
    return 'Permissions-Policy: ' . $filtered_policies . "\r\n";
  }, $html);
  $html = str_replace(array('ch-ua-form-factor', 'ch-ua-mobile'), '', $html);
  return $html;
}
function orderHeader($test) {
   ob_start();
}
function orderFooter($test) {
    $html = ob_get_clean();
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xpath = new DOMXpath($dom);
    $html = str_replace('target="_blank"', 'target="_blank" rel="nofollow"', $html);
    $html = update_protocol_links($html);

    echo $html;
}

add_action( 'BeforeHeader', 'orderHeader' );
add_action( 'AfterFooter', 'orderFooter' ); 


function disable_classic_theme_styless() {
    wp_deregister_style('classic-theme-styles');
    wp_dequeue_style('classic-theme-styles');
}
if(!is_admin()){
    add_filter('wp_enqueue_scripts', 'disable_classic_theme_styless', 100);
}
add_filter( 'rank_math/frontend/disable_adjacent_rel_links', '__return_true' );


add_action( 'wp_enqueue_scripts', 'deregister_styles', 20 );
function deregister_styles() {
    wp_dequeue_style( 'classic-theme-styles' );
    wp_dequeue_style( 'ez-toc' );
    wp_dequeue_style( 'yasrcss' );
    wp_dequeue_style( 'ez-icomoon' );
    
    
}
function smartwp_remove_specific_css(){
    wp_dequeue_style( 'screen' ); // Remove the screen.min.css file
}

// Hook into the 'wp_enqueue_scripts' action
add_action( 'wp_enqueue_scripts', 'smartwp_remove_specific_css' );
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );
add_filter( 'rank_math/frontend/disable_adjacent_rel_links', '__return_true' );



function custom_comment_status($commentdata) {
    if (!is_user_logged_in() && isset($_POST['approve_comment']) && $_POST['approve_comment'] != '1') {
        $commentdata['comment_approved'] = 0;
    }
    return $commentdata;
}
add_filter('preprocess_comment', 'custom_comment_status');

function disable_unnecessary_scripts() {
    wp_dequeue_script( 'jquery' ); // إزالة ملف jQuery
    wp_dequeue_script( 'wp-embed' ); // إزالة ملف wp-embed
    wp_dequeue_script( 'wp-emoji' ); // إزالة ملف wp-emoji
    wp_dequeue_script( 'comment-reply' ); // إزالة ملف comment-reply
}
add_action( 'wp_enqueue_scripts', 'disable_unnecessary_scripts', 999 );

add_filter( 'wpseo_next_rel_link', '__return_false' );
add_filter( 'wpseo_prev_rel_link', '__return_false' );


// Apply to post content and widgets
// Disable Query Monitor CSS
add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_admin() ) {
        wp_dequeue_style( 'qm-admin-css' );
    }
}, 100 );
//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
    if(!is_page()){
        wp_deregister_script( 'contact-form-7' );
    }
    if(!is_admin()){
        add_filter('wp_enqueue_scripts', 'disable_classic_theme_styles', 100);
    }
    wp_dequeue_style( 'contact-form-7' ); // Remove WooCommerce block CSS
    wp_dequeue_style( 'contact-form-7-rtl' ); // Remove WooCommerce block CSs
    wp_dequeue_style( 'front.min' ); // Remove WooCommerce block CSS
    wp_dequeue_style( 'ez-toc' ); // Remove WooCommerce block CSS
    wp_dequeue_script( 'contact-form-7' );
     wp_dequeue_script( 'jquery' ); // إزالة ملف jQuery

} 

add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );
add_filter( 'rank_math/frontend/disable_adjacent_rel_links', '__return_true' );


add_action( 'wp_print_scripts', function() {
    wp_dequeue_script( 'contact-form-7' );
    wp_dequeue_script( 'contact-form-7-swfupload' );
}, 100 );

function add_taxonomy_to_pages() {
    register_taxonomy_for_object_type('category', 'page');
}
add_action('init', 'add_taxonomy_to_pages');


function remove_youtube_embed_scripts() {
    wp_deregister_style( 'wp-youtube-embed' );
    wp_deregister_script( 'wp-youtube-embed' );
    wp_deregister_script( 'base' );
}
add_action( 'wp_enqueue_scripts', 'remove_youtube_embed_scripts', 999 );

function disable_all_scripts() {
     global $wp_scripts;
    
    foreach ($wp_scripts->queue as $handle ) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }

    global $wp_styles;
    foreach ($wp_styles->queue as $handle) {
        wp_dequeue_style($handle);
    }
}

add_action('wp_enqueue_scripts', 'disable_all_scripts', 9999);
function disable_wordfence_css() {
    wp_dequeue_style('wordfenceBox');
    wp_dequeue_script('wordfenceAJAXcalls');
}
add_action('wp_enqueue_scripts', 'disable_wordfence_css', 9999);



// Images & iframes in content use native lazy-loading (WordPress core adds loading="lazy").
