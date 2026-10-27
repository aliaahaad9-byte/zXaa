<?echo '<!DOCTYPE html>';
echo '<html lang="'.(get_option('yc_lang') ==   '' ? 'ar-eg' : get_option('yc_lang')).'" dir="rtl">';
echo '<head>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
echo '<meta charset="utf-8">';
//
echo '<title>';
wp_title();
echo'</title>';
do_action('BeforeWPHead');
 wp_head(); 

do_action('AfterWPHead');
if(!empty(get_option('favicon')['url'])){
    echo '<link rel="shortcut icon" type="image/png" href="'.get_option('favicon')['url'].'">';
}
// Font Awesome: ملف واحد يجمع كل الأنماط (بدل 35 طلبًا) — وإن لم يوجد نعود للطريقة القديمة
$fontawesomeBundle = $this->StylesPath.'fontawesome/bundle/fa-all.css';
if( file_exists($fontawesomeBundle) ) {
    echo '<link rel="stylesheet" href="'.$this->StylesURL.'fontawesome/bundle/fa-all.css?v='.filemtime($fontawesomeBundle).'">';
}else {
    $fontawesomePath = $this->StylesPath."fontawesome/css/*.css";
    $fontawesomeCss = glob($fontawesomePath);
    $fontawesomeURL = $this->StylesURL.'fontawesome/css/';
    echo '<link rel="stylesheet" href="'.$fontawesomeURL.'fontawesome.css">';
    foreach ( $fontawesomeCss as $file ) {
        echo '<link rel="stylesheet" href="'.$fontawesomeURL.basename($file).'">';
    }
}
// تحميل مسبق للوزن الأكثر استخدامًا من خط القالب
$yc_font_dir = get_template_directory().'/components/styles/Font/YourColor/';
if( file_exists($yc_font_dir.'Montserrat-Arabic-Regular.woff2') ) {
    echo '<link rel="preload" href="'.$this->StylesURL.'/Font/YourColor/Montserrat-Arabic-Regular.woff2" as="font" type="font/woff2" crossorigin>';
}
echo '<meta name="apple-mobile-web-app-title" content="'.get_bloginfo("name").'">';
echo '<meta http-equiv="Cache-control" content="public">';
echo '<meta name="application-name" content="'.get_bloginfo("name").'">';
echo '<meta name="msapplication-TileColor" content="#a03576">';
//
//echo '<link rel="stylesheet" media="all" href="'.$this->StylesURL.'main.css?'.rand().'" />';
//echo '<link rel="stylesheet" media="all" href="'.$this->StylesURL.'responsive.css?'.rand().'" />';
 	echo '<style>';
 		// خط القالب: WOFF2 أولًا (أصغر بنحو 70%) ثم TTF احتياطًا — نفس الخط بالضبط
		$yc_fonts = array( 200 => 'ExtraLight', 300 => 'Light', 400 => 'Regular', 500 => 'Medium', 600 => 'Bold', 700 => 'ExtraBold' );
		foreach( $yc_fonts as $yc_w => $yc_n ) {
			$yc_src = array();
			if( file_exists($yc_font_dir.'Montserrat-Arabic-'.$yc_n.'.woff2') ) {
				$yc_src[] = 'url("'.$this->StylesURL.'/Font/YourColor/Montserrat-Arabic-'.$yc_n.'.woff2") format("woff2")';
			}
			$yc_src[] = 'url("'.$this->StylesURL.'/Font/YourColor/Montserrat-Arabic-'.$yc_n.'.ttf") format("truetype")';
			echo '@font-face {font-family: YourColor;font-style: normal;font-weight: '.$yc_w.';font-display: swap;src: '.implode(',', $yc_src).';}';
		}
		require get_template_directory().'/components/styles/main.css';
    	require get_template_directory().'/components/styles/responsive.css';
	echo '</style>';
	{
			$show_headCodes = wp_is_mobile() ? get_option('show_ads_mobile_header') : get_option('show_ads_header');
			$ads_code = wp_is_mobile() ? get_option('mobile_ads_header', '') : get_option('ads_header', '');

			if (wp_is_mobile() && empty($show_headCodes)) {
			    echo get_option('mobile_ads_header', '');
			} else if (!wp_is_mobile() && empty($show_headCodes)) {
			    echo get_option('ads_header', '');
			}

			
		}
echo '</head>';
echo '<body mode="light">';
echo '<root>';
echo '<rootinse>';
$logo = get_option('logo');
if( isset($logo['url']) ) {
	$logo = $logo['url'];
}

$Whatsapp = get_option('Whatsapp');
$Phone = get_option('Phone');
if( is_single() ) {
	wp_reset_query();
    global $post;
    $country = get_the_terms( $post->ID,'country',true );
	$country = ( is_array( $country ) ) ? $country : array();
	$call_number = array();
	$whatsapp_number = array();
    foreach ( $terms_category as $cats ) {

	    $category__ids[] = $cats->term_id;
        // Get the custom field value from the term object
        $call_number = get_term_meta($term_obj->term_id, 'call_number', true);
        $whatsapp_number = get_term_meta($term_obj->term_id, 'whatsapp_number', true);
    
    	if( !empty($call_number) ) {
    		$Phone = $call_number;
    	}
    	if( !empty($whatsapp_number) ) {
    		$Whatsapp = $whatsapp_number;
    	}

    }

}elseif( is_category() ) {
	$current_object = get_queried_object();
	$call_number = get_term_meta($current_object->term_id, 'call_number', true);
	$whatsapp_number = get_term_meta($current_object->term_id, 'whatsapp_number', true);
	if( !empty($call_number) ) {
		$Phone = $call_number;
	}
	if( !empty($whatsapp_number) ) {
		$Whatsapp = $whatsapp_number;
	}
}
elseif( is_tax('country') ) {
	$current_object = get_queried_object();
	$call_number = get_term_meta($current_object->term_id, 'call_number', true);
	$whatsapp_number = get_term_meta($current_object->term_id, 'whatsapp_number', true);
	if( !empty($call_number) ) {
		$Phone = $call_number;
	}
	if( !empty($whatsapp_number) ) {
		$Whatsapp = $whatsapp_number;
	}
}

echo'<header>';
	echo'<div class="container">';
			
		echo'<div class="logo">';
			echo'<a href="'.home_url().'"><img width="210" height="52" alt="'.get_bloginfo('name').'" data-loader-src="'.$logo.'"></a>';
		echo'</div>';
		echo'<div class="menu-nav">';
			echo'<form method="GET" action="'.home_url().'" style="display:none;">';

		        echo'<input type="seach" name="s" placeholder="أدخل كلمة البحث" />';

		        echo'<button  aria-label="seach" type="submit"><i class="fa fa-search"></i></button>';

		    echo'</form>'; 
			wp_nav_menu(
		       array(
	               'theme_location' => 'home-menu',
	               'menu'           => '',
	               'container'      => '',
	               'container_class' => '',
	               'container_id'   => '',
	               'menu_class'     => '',
	               'menu_id'        => '',
	               'echo'           => true,
	               'fallback_cb'    => 'wp_page_menu',
	               'before'         => '',
	               'after'          => '',
	               'link_before'    => '',
	               'link_after'     => '',
	               'items_wrap'     => '<ul class ="header-menu">%3$s</ul>',
	               'depth'          => 0,
	               'walker'         => '',
				)
			);
			
		echo'</div>';
		echo '<div class="menu-barbox">';
			echo '<div class ="menu_bar">';
	    		echo'<i class="fa-duotone fa-bars"></i>';
	    		echo '<i class="fa-thin fa-xmark"></i>';
	    	echo'</div>';
	    	echo '<div class ="search_header">';
	    		echo '<span id="openSEarch"><i class="far fa-search"></i><i class="fa-solid fa-xmark"></i></span>';
		    	echo '<form action="'.home_url().'" method="GET" >';
		            echo '<input type="text" name="s" placeholder="إبحث " >';
		            echo '<button aria-label="name" title="Search" type="submit">بحث</button>';
		        echo '</form>';
	        echo '</div>';
        echo '</div>';
	echo '</div>';
echo'</header>';
