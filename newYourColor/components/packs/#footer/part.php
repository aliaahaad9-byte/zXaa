<?php
$logo_footer = get_option('logo_footer');

$Whatsapp = get_option('Whatsapp');
$Phone = get_option('Phone');
$Adress = get_option('Adress');
$map = get_option('map');
$email = get_option('email');
$hide_phone = 'off';
if( is_single() ) {
	wp_reset_query();
	global $post;
	$call_number = get_post_meta($post->ID, 'call_number', 1);
	$whatsapp_number = get_post_meta($post->ID, 'whatsapp_number', 1);
	$hide_phone = get_post_meta($post->ID, 'hide_phone', 1);
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
	            echo'<div class="blocks-footer">';
				echo'<div class="blocks-yc-">';
				$logo = get_option('logoFooter');
			
					echo'<div class="blocks-content-left">';
						echo'<div class="footer-logo">';
						if(IsSpeed() == false){
						    
						
						if( !empty( $logo ) && isset( $logo['url'] ) && !empty( $logo['url'] ) ){
            				$wp_get_attachment_metadata = wp_get_attachment_metadata( $logo['id'] );		
            				echo'<a href="'.home_url().'"><img width=100% height=100%  alt="'.get_bloginfo('name').'" data-loader-src="'.$logo['url'].'"></a>';
            			}
						}
						echo'</div>';
						echo'<div class="blocks-content">';
							echo '<span>'.get_option('titleFooter').'</span>';
							echo '<p>'.get_option('contentFooter').'</p>';
						echo'</div>';
						echo'<div class="logo_footer">';
							(new ThemeStatic)->Part("social");
						echo'</div>';
					echo'</div>';
					echo'<div class="contact-info-right">';
		                echo '<h3 class ="text-footer-menu">معلومات الاتصال</h3>';
		                echo'<div class="contact-box">';
			                echo'<div class="contact-info">';
			                	if(!empty($Phone)){
		                        echo'<a class="contact" href="tel:'.$Phone.'">';
		                            echo'<h3>رقم الهاتف :</h3>';
		                            echo'<p>'.$Phone.'</p>';
		                        echo'</a>';
		                    	}
		                        if(!empty($email)){
		                        echo'<a class="contact" href="mailto:'.$email.'">';
		                            echo'<h3>الاميل :</h3>';
		                            echo'<p>'.$email.'</p>';
		                        echo'</a>';
		                    	}
			                echo'</div>';
			                if(!empty($map)){
			                echo'<div class="contact-map">';
			                	echo '<div>'.$map.'</div>';
			                echo'</div>';
			                }
		                echo'</div>';
		            echo'</div>';
				echo'</div>';
				echo '<div class ="footer_menu">';
					echo '<h3 class ="text-footer-menu">روابط هامة</h3>';
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
			echo'</div>';
            
        	echo'</div>';
        echo'</div>';
		
		$sitename__copyrights = get_option('sitename');
		echo'<div class="foot">';
			echo '<div class="container">';
				echo'<div class="foot-footer">';
					echo '<allrights-reserved>';
					    if(IsSpeed() == false){
						echo 'جميع الحقوق محفوظة &copy; ' .date('Y');
						if( !empty($sitename__copyrights) ) {
						    echo ' لموقع  <a href="'.home_url().'">'.$sitename__copyrights.'</a>';
						}
					    }
					echo '</allrights-reserved>';
					echo'<div class="company">';
						echo '<allrights-SEO>';
							echo '<span>ارشفه <a target="_blank" rel="nofollow" href="https://www.facebook.com/alsyd.hossam"> Teko</a></span>';
						echo '</allrights-SEO>';
						if(IsSpeed() == false){
						    
					
						echo '<p>برمجه <a target="_blank" rel="nofollow" href="https://yourcolor.net">
							<strong>Y</strong>

							<strong>O</strong>
							<strong>U</strong>
							<strong>R</strong>
							<strong>C</strong>
							<strong>O</strong>
							<strong>L</strong>
							<strong>O</strong>
							<strong>R</strong>

						</a></p>';
						}
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
					echo '<svg width="55" height="56" fill="#4caf50" role="img" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="fill:#4caf50;">
		                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z">
		                </path>
		            </svg>';
				
				echo '</div>';
			echo '</a>';
		echo'</div>';
	
	echo'</div>';
echo '</root>';

$TempDIR = $this->TempURL;
	echo '<script type="text/javascript">';
		
		echo "var WPAdminAjax = '".admin_url('admin-ajax.php')."';";

		echo "var LoginURL = '".home_url('/sign-in/')."';";

		echo "var AdminAjax = '".home_url('/ajaxcenter/')."';";

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
		//require($CurrentDir.'js/jquery-3.4.1.min.js');
		
	echo '</script>';
	
	/*echo '<script>';
	require($CurrentDir.'js/owl.carousel.min.js');
	echo '</script>';*/
	
wp_footer();
echo '</body>';
echo '</html>';