<?
$logo_footer = get_option('logo_footer');
if( isset($logo_footer['url']) ) {
	$logo_footer = $logo_footer['url'];
}
$Whatsapp = get_option('Whatsapp');
$Phone = get_option('Phone');
$Adress = get_option('Adress');
$map = get_option('map');
$email = get_option('email');
$hide_phone = 'off';
if( is_single() ) {
	wp_reset_query();
	global $post;
	$city = get_the_terms($post->ID, 'country', '');
	$call_number = get_post_meta($post->ID, 'call_number', true);
	if (empty($call_number)) {
	    $call_number = get_term_meta($city[0]->term_id, 'call_number', 1);
	}
	$whatsapp_number = get_post_meta($post->ID, 'whatsapp_number', true);
	if (empty($whatsapp_number)) {
	    $whatsapp_number = get_term_meta($city[0]->term_id, 'whatsapp_number', 1);
	}
	$hide_phone = get_post_meta($post->ID, 'hide_phone', true);
	if( !empty($call_number) ) {
		$Phone = $call_number ;
	}else{
		$phone = get_option('Phone');
	}
	if( !empty($call_number) ) {
		$Whatsapp = $whatsapp_number ;
	}else{
		$Whatsapp = get_option('Whatsapp');
	}

}elseif( is_category() ) {
	$current_object = get_queried_object();
	$call_number = get_term_meta($current_object->term_id, 'call_number', 1);
	$whatsapp_number = get_term_meta($current_object->term_id, 'whatsapp_number', 1);
	if( !empty($call_number) ) {
		$Phone = $call_number ;
	}else{
		$phone = get_option('Phone');
	}
	if( !empty($call_number) ) {
		$Whatsapp = $whatsapp_number ;
	}else{
		$Whatsapp = get_option('Whatsapp');
	}
}
elseif( is_tax('country') ) {
	$current_object = get_queried_object();
	$call_number = get_term_meta($current_object->term_id, 'call_number', true);
	$whatsapp_number = get_term_meta($current_object->term_id, 'whatsapp_number', true);
	if( !empty($call_number) ) {
		$Phone = $call_number ;
	}else{
		$phone = get_option('Phone');
	}
	if( !empty($call_number) ) {
		$Whatsapp = $whatsapp_number ;
	}else{
		$Whatsapp = get_option('Whatsapp');
	}
}elseif( is_page('country') ) {
	$current_object = get_queried_object();
	$call_number = get_term_meta($current_object->term_id, 'call_number', true);
	$whatsapp_number = get_term_meta($current_object->term_id, 'whatsapp_number', true);
	if( !empty($call_number) ) {
		$Phone = $call_number ;
	}else{
		$phone = get_option('Phone');
	}
	if( !empty($call_number) ) {
		$Whatsapp = $whatsapp_number ;
	}else{
		$Whatsapp = get_option('Whatsapp');
	}
}
	echo '</rootinse>';
	echo '<footer>';
		echo'<div class="contact-info-box">';
			echo '<div class="container">';
	            echo'<div class="contact-info">';
				    echo'<a href="tel:'.$Phone.'" aria-label="Phone">';
	                    echo'<svg-phone><svg class="mobile" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#fff" role="img" focusable="false" aria-hidden="true" viewBox="0 0 512 512"> <title id="icon-mobile">mobile</title><path d="m353 5c15 0 27 5 37 15c10 10 14 22 14 36c0 0 0 400 0 400c0 13-4 25-14 36c-10 10-22 15-37 15c0 0-194 0-194 0c-14 0-26-5-36-15c-10-11-15-23-15-36c0 0 0-400 0-400c0-14 5-26 15-36c10-10 22-15 36-15c0 0 194 0 194 0m-97 481c10 0 19-2 26-7c6-5 10-11 10-18c0-8-4-14-10-19c-7-4-16-7-26-7c-10 0-18 3-25 8c-7 5-11 11-11 18c0 7 4 13 11 18c7 5 15 7 25 7m108-76c0 0 0-338 0-338c0 0-216 0-216 0c0 0 0 338 0 338c0 0 216 0 216 0"></path></svg></svg-phone>';
	                    echo '<div class="info-footer">';
	                    	echo '<span>رقم الهاتف:</span>';
	                    	echo'<span>'.$Phone.'</span>';
	                    echo'</div>';
	                echo'</a>';
	                echo '<div class="boder"></div>';
	                echo '<a href=" mailto:?subject='.urlencode($email).'" aria-label="Email">';
	                    echo'<svg-phone><svg class="mail" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#fff" role="img" focusable="false" aria-hidden="true" viewBox="0 0 512 512"> <title id="icon-mail">mail</title><path d="m485 97l-5-1l-448 0l-3 1l227 202z m25 20l-161 143l159 139c2-5 4-9 4-15l0-256c0-4-1-7-2-11m-508-1c-1 3-2 8-2 12l0 256c0 5 1 9 3 13l161-138z m254 225l-68-60l-158 135l2 0l447 0l-155-135z"></path></svg></svg-phone>';
	                    echo '<div class="info-footer">';
	                    	echo '<span>راسلنا عبر البريد :</span>';
	                   	 	echo'<span>'.$email.'</span>';
	                   	echo'</div>';
	                echo'</a>';
	                echo '<div class="boder"></div>';
	                echo '<div class="Address">';
	                    echo'<svg-phone><svg class="address" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#fff" role="img" focusable="false" aria-hidden="true" viewBox="0 0 512 512"> <title id="icon-address">address</title><path d="m256 53c-80 0-144 65-144 145c0 32 10 62 28 86l93 161c0 1 1 2 2 3l0 0l0 0c5 6 12 11 21 11c8 0 15-4 20-10l0 1l1-2c1-2 2-4 3-6l91-157c18-24 29-55 29-87c0-80-64-145-144-145z m-1 218c-40 0-72-32-72-71c0-40 32-71 72-71c39 0 71 31 71 71c0 39-32 71-71 71z"></path></svg></svg-phone>';
	                    echo '<div class="info-footer">';
	                    	echo '<span>العنوان :</span>';
	                    	echo'<span>'.$Adress.'</span>';
	                	echo'</div>';
	                echo'</div>';
	            echo'</div>';
            
        	echo'</div>';
        echo'</div>';
		echo '<div class="container">';

			echo'<div class="blocks-footer">';
				echo '<div class ="footer_menu">';
					echo '<span class ="text-footer-menu">روابط هامة</span>';
					wp_nav_menu(
				       array(
			               'theme_location' => 'footer-menu',
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
			               'items_wrap'     => '<ul class ="footer-menu">%3$s</ul>',
			               'depth'          => 0,
			               'walker'         => '',
						)
					);
				echo'</div>';
				echo'<div class="blocks-yc-">';				
					echo'<div class="contact-info-right">';
		                echo '<span class ="text-footer-menu">التواصل الإجتماعي</span>';
		                echo'<div class="contact-box">';
							(new ThemeStatic)->Part("social");
		                echo'</div>';
		            echo'</div>';
				echo'</div>';
			echo'</div>';
			
		echo '</div>';
		$sitename__copyrights = get_option('sitename');
		echo'<div class="foot">';
			echo '<div class="container">';
				echo'<div class="foot-footer">';
					echo '<allrights-reserved>';
						echo 'جميع الحقوق محفوظة &copy; ' .date('Y');
						if( !empty($sitename__copyrights) ) {
						    echo ' لموقع  <a href="'.home_url().'">'.$sitename__copyrights.'</a>';
						}
					echo '</allrights-reserved>';
					echo'<div class="company">';
						echo '<allrights-SEO>';
							echo '<span>ارشفه <a target="_blank" rel="nofollow" href="https://www.facebook.com/alsyd.hossam"> Teko</a></span>';
						echo '</allrights-SEO>';
						echo '<p>برمجه <a target="_blank" rel="nofollow" href="https://yourcolor.net"><img width="96" height="19" data-loader-src="'.$CurrentURL.'yourcolor.png" alt="Yourcolor Workshop"/></a></p>';
					echo'</div>';
				echo'</div>';
			echo'</div>';
		echo'</div>';
	echo '</footer>';
	


	// Back to top button 
	echo '<div id="button" aria-label="go to up"><i aria-hidden="true" class="far fa-sort-up"></i></div>';
	
	// btn contact
	echo '<div class ="btn-fixed-bh">';
		echo '<div class="btn-phone">';
			echo'<a href="tel:'.$Phone.'" aria-label="Phone" data-call="Phone">';
				echo '<span>اتصل بنا</span>';
				echo '<div class="footer-header">';
					echo '<svg width="36" height="48" fill="#fff" role="img" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 512 512">
		                <path d="M352 320c-32 32-32 64-64 64s-64-32-96-64-64-64-64-96 32-32 64-64-64-128-96-128-96 96-96 96c0 64 65.75 193.75 128 256s192 128 256 128c0 0 96-64 96-96s-96-128-128-96z">
		                </path>
		            </svg>';
					echo '</div>';
				echo'</a>';
		echo'</div>';
		echo'<div class="btn-whatsapp">';
			echo '<a href="https://wa.me/'.trim($Whatsapp).'" target="_blank" aria-label="whatsapp" data-call="whatsapp">';
				echo '<span>  الواتساب</span>';
				echo '<div class="footer-header">';
					if(IsSpeed() == false){
					echo '<svg width="55" height="56" fill="#4caf50" role="img" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="fill:#4caf50;">
		                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z">
		                </path>
		            </svg>';
				
					}
				echo '</div>';
			echo '</a>';
		echo'</div>';
	
	echo'</div>';
echo '</root>';

$TempDIR = $this->TempURL;
	echo '<script type="text/javascript">';
		
		//echo "var WPAdminAjax = '".admin_url('admin-ajax.php')."';";
		echo "var LoginURL = '".home_url('/sign-in/')."';";
		echo "var AdminAjax = '".home_url('/AjaxCenter/')."';";
		echo "var HomeURL = '".home_url()."';";
		echo "var TmpDIR = '".get_template_directory_uri()."';";
		echo "var href__login = '".home_url('/sign-in/')."';";
		echo "var ISMobile = ".((wp_is_mobile()) ? 'true' : 'false').";";
		echo "var IsSpeed = ".( ( IsSpeed() != false ) ? 'true' : 'false').";";
		if( is_user_logged_in() ) {
			global $current_user;
			echo "var Currentuser_ID = '".$current_user->ID."';";
			echo "var Currentuser_first_name = '".$current_user->first_name."';";
			echo "var Currentuser_last_name = '".$current_user->last_name."';";
			echo "var Currentuser_email = '".$current_user->user_email."';";
			echo "var Currentuser_display_name = '".$current_user->display_name."';";
			echo "var Currentuser_Logged = true;";
		}else {
			echo "var Currentuser_ID = 0;";
			echo "var Currentuser_first_name = false;";
			echo "var Currentuser_email = false;";
			echo "var Currentuser_last_name = false;";
			echo "var Currentuser_display_name = 'أنت';";
			echo "var Currentuser_Logged = false;";
		}
		echo 'function onTouchStart() {}document.addEventListener(\'touchstart\', onTouchStart, {passive: true});';

	echo '</script>';
	echo '<script>';
	require($CurrentDir.'js/jquery-3.4.1.min.js');
	echo '</script>';
	echo '<script>';
	require($CurrentDir.'js/owl.carousel.min.js');
	echo '</script>';
	
	echo '<script>';
	require($CurrentDir.'js/setup.js');
	echo '</script>';
	
wp_footer();
echo '</body>';
echo '</html>';