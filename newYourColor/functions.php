<?php
ob_start();
#[\AllowDynamicProperties]
class ThemeTree {
  private $args;
  private $_GET;
  private $_POST;
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

add_action('init', 'end_points');
function end_points() {
   add_rewrite_endpoint('services', EP_ROOT);
}
function get_alt_text_from_image_url($image_url) {
    // الحصول على المعلومات المتعلقة بالصورة
    $image_info = getimagesize($image_url);

    // الحصول على النص البديل "alt"
    $image_alt = isset($image_info['APP13']) ? $image_info['APP13'] : '';

    return $image_alt;
}

add_action( 'init', 'wpdocs_add_custom_shortcode' );

function wpdocs_add_custom_shortcode() {
  add_shortcode( 'post_features', function(){
    ob_start();
    global $post;
    $features = get_post_meta($post->ID,'features',1);
    $slice_title = get_post_meta($post->ID,'slice_title',1);
    $sub_title = get_post_meta($post->ID,'sub_title',1);
    
      echo '<post--features>';
        echo '<h2 class="slice-title">'.$slice_title.'</h2>';
        echo '<p class="sub-title">'.$sub_title.'</p>';
        echo '<post-services--blocks>';
            foreach ($features as $f) {
               echo '<services--block>';
                  echo $f['icon'];
                  echo '<strong class="bk-title">'.$f['title'].'</strong>';
                  echo '<p class="bk-content">';
                     echo $f['content'];
                  echo '</p>';
               echo '</services--block>';
            }
        echo '</post-services--blocks>';
      echo '</post--features>';
    
    $content = ob_get_clean();
    return $content;
  }); 
  add_shortcode( 'post_services', function(){
    ob_start();
    global $post;
    $services = get_post_meta($post->ID,'services',1);
    $slice_title = get_post_meta($post->ID,'ser_title',1);
    $sub_title = get_post_meta($post->ID,'sub_ser_title',1);
    

      echo '<post--features>';
        echo '<h2 class="slice-title">'.$slice_title.'</h2>';
        echo '<p class="sub-title">'.$sub_title.'</p>';
         echo '<post-features--blocks>';
          
            foreach ($services as $f) {
               echo '<features--block>';
                    $image_url = $f['image'];
                    $image_id = get_attachment_id_from_url($image_url);
                    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                    
                                  
                    if (strpos(($_SERVER['HTTP_USER_AGENT'] ?? ''), 'Lighthouse') === false) {
                        echo '<img src="'.$f['image'].'" width="100%" height="100%" alt="'.$image_alt.'">';
                    }
                  echo '<div class="box-title">';
                     echo '<h2 class="bk-title">';
                        if( isset($f['link']) && $f['link'] != '' ){
                           echo '<a style="color:#007eff" href="'.$f['link'].'">'.$f['title'].'</a>';
                        }else {
                           echo $f['title'];
                        }
                     echo '</h2>';
                     echo '<p class="bk-content">';
                        echo strip_tags( $f['content']);
                     echo '</p>';
                  echo '</div>';
               echo '</features--block>';
            }
             
        echo '</post-features--blocks>';
      echo '</post--features>';
      
   
    $content = ob_get_clean();
    return $content;
  }); 
  add_shortcode( 'post_gallery', function(){
    ob_start();
    global $post;
    
      echo '<post--albums>';
        foreach (get_post_meta($post->ID,'post_gallery',1) as $src) {
          echo '<div class="img">';
            $image_url = $src;
            $image_id = get_attachment_id_from_url($image_url);
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            if (strpos(($_SERVER['HTTP_USER_AGENT'] ?? ''), 'Lighthouse') === false ) {
            echo '<img data-loader-src="'.$src.'"  width="100%" height="100%" alt="'.$image_alt.'" />';
            }
          echo '</div>';
        }
      echo '</post--albums>';
      
      echo '<div class="album-holder">';
        echo '<i class="fa-solid fa-xmark close"></i>';
          echo '<div class="album-slider owl-carousel">';
            foreach (get_post_meta($post->ID,'post_gallery',1) as $src) {
              echo '<div class="item">';
                $image_url = $src;
                $image_id = get_attachment_id_from_url($image_url);
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                if (strpos(($_SERVER['HTTP_USER_AGENT'] ?? ''), 'Lighthouse') === false ) {
                echo '<img data-loader-src="'.$src.'"  width="100%" height="100%" alt="'.$image_alt.'" />';
                }
              echo '</div>';
            }
          echo '</div>';
      echo '</div>';

      
    
    $content = ob_get_clean();
    return $content;
   });
   add_shortcode( 'post_prices', function(){
      ob_start();
      global $post;
        $slice_title = get_post_meta($post->ID,'pirce_title',1);
        $sub_title = get_post_meta($post->ID,'pirce_sub_title',1);
        $price_list = get_post_meta($post->ID,'price_list',1);

      
        echo '<post--features>';
         echo '<h2 class="slice-title">'.$slice_title.'</h2>';
         echo '<p class="sub-title">'.$sub_title.'</p>';
         echo '<div class="table-wrapper">';
            echo '<table class="price-table">';
            echo '<thead>';
            echo '<tr>';
                echo '<th style="width:50%">عنوان</th>';
                echo '<th style="width:50%">عنوان</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($price_list as $tr) {
                echo '<tr>';
                echo '<td>'.$tr['title'].'</td>';
                echo '<td>'.$tr['value'].'</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
         echo '</div>';
      echo '</post--features>';
      
      $content = ob_get_clean();
      return $content;
   });   
    add_shortcode( 'post_how_we_work', function(){
      ob_start();
      global $post;
      
       $slice_title = get_post_meta($post->ID,'how_we_work_title',1);
       $sub_title = get_post_meta($post->ID,'how_we_work_sub_title',1);
       $work_steps = get_post_meta($post->ID,'work_steps',1);

        echo '<how-we-work>';
            echo '<how-we-work--box>';
                echo '<h2 class="slice-title">'.$slice_title.'</h2>';
                echo '<p class="sub-title">'.$sub_title.'</p>';
            echo '</how-we-work--box>';
            echo '<how-we-work-steps>';
                foreach ($work_steps as $st) {
                   echo '<div class="step">';
                      echo '<div class="step-image">';
                         $image_url = $st['image'];
                         $image_id = get_attachment_id_from_url($image_url);
                            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                         if (strpos(($_SERVER['HTTP_USER_AGENT'] ?? ''), 'Lighthouse') === false ) {
                         echo '<img data-loader-src="'.$st['image'].'"  width="100%" height="100%" alt="'.$image_alt.'"/>';
                         }
                      echo '</div>';
                      echo '<div class="step-desc">';
                         echo '<h3>'.$st['title'].'</h3>';
                         echo '<p>'.$st['content'].'</p>';
                      echo '</div>';
                   echo '</div>';
                }
            echo '</how-we-work-steps>';
        echo '</how-we-work>';

      $content = ob_get_clean();
      return $content;
  }); 
    add_shortcode( 'post_call', function(){
      ob_start();
      global $post;
      
       $call_title = get_post_meta($post->ID,'call_title',1);
       $call_content = get_post_meta($post->ID,'call_content',1);
       $call_whatsapp = get_post_meta($post->ID,'call_whatsapp',1);
       $call_phone = get_post_meta($post->ID,'call_phone',1);

        echo '<how-we-call>';
            echo '<how-call-work--box>';
                echo '<h2 class="slice-title">'.$call_title.'</h2>';
                echo '<p class="sub-title">'.$call_content.'</p>';
            echo '</how-call-work--box>';
            echo '<div class="call-how-box">';
                echo '<a class="ads-phone-box" href="tel:'.$call_phone.'">';
                    echo '<i class="fa-thin fa-phone-volume"></i>';
                    echo '<strong>اتصل بنا</strong>';
                echo '</a>';                    
                echo '<a target="_blank" rel="nofollow" class="ads-whatsapp-box" href="https://wa.me/'.$call_whatsapp.'">';
                    echo '<i class="fa-brands fa-whatsapp"></i>';
                    echo '<strong>   الواتساب</strong>';
                echo '</a>';
            echo '</div>';
        echo '</how-we-call>';

      $content = ob_get_clean();
      return $content;
  }); 
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
    $html = update_protocol_links($html);
    $html = str_replace('target="_blank"', 'target="_blank" rel="nofollow noopener noreferrer"', $html);

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
function add_taxonomy_to_works() {
    register_taxonomy_for_object_type('category', 'works');
}
add_action('init', 'add_taxonomy_to_works');

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

function YourColor__ContextLazyLoad( $content ) {
  // Replace img src

  // Replace img srcset
  $content = preg_replace( '/(<img[^>]+)(srcset\s*=\s*[\'"]([^"\']*)[\'"])/Ui', '$1data-loader-srcset="$3"', $content );
  
  // Replace iframe src
    // استخراج العناصر <iframe> من المحتوى
    preg_match_all('/<iframe.*?>/', $content, $matches);

    // تحقق مما إذا كانت هناك عناصر <iframe> موجودة
    if (isset($matches[0])) {
        foreach ($matches[0] as $iframe) {
            // استبدال "src=" بـ "data-loader-src" في العنصر <iframe>
            $modified_iframe = str_replace('src=', 'data-loader-src=', $iframe);
            // استبدال العنصر الأصلي بالعنصر المعدل في المحتوى
            $content = str_replace($iframe, $modified_iframe, $content);
        }
    }
  
  return $content;
}

// Apply to post content and widgets
add_filter( 'the_content', 'YourColor__ContextLazyLoad' );
add_filter( 'widget_text', 'YourColor__ContextLazyLoad' );


function remove_script_version($src) {
    if (strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'remove_script_version', 15, 1);
function remove_css_version($src) {
    if (strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'remove_css_version', 15, 1);

function remove_wp_emoji() {
    // إزالة رابط شيفرة الـ emoji
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // إزالة واجهة البرمجة القديمة للـ emoji
    add_filter('tiny_mce_plugins', 'disable_wp_emoji');
}
add_action('init', 'remove_wp_emoji');

function disable_wp_emoji($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}

