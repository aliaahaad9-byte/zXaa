<?php 
$logo_footer = get_option('logo_footer');

$Whatsapp = get_option('Whatsapp');
$Phone = get_option('Phone');
$Adress = get_option('Adress');
$map = get_option('map');
$email = get_option('email');
$title_mune_1 = get_option('title_mune_1');
$title_mune_2 = get_option('title_mune_2');
$title_mune_3 = get_option('title_mune_3');
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
    $logo = get_option('logoFooter');
    echo '</rootinse>';

    echo '<footer >';

        echo '<div class="container">';
                echo'<div class="blocks-footer">';
                    echo'<div class="blocks-yc-">';             
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
                                //echo '<span>'.get_option('titleFooter').'</span>';
                                echo '<p>'.get_option('contentFooter').'</p>';
                            echo'</div>';
                            echo'<div class="logo_footer">';
                                (new ThemeStatic)->Part("social");
                            echo'</div>';
                        echo'</div>';
                    echo'</div>';
                     
                    echo'<div class="slider-menu-box">';
                        echo '<div class ="footer_menu">';
                            echo '<div class="text-footer-menu">' . (!empty($title_mune_1) ? $title_mune_1 : 'روابط مفيدة') . '</div>';
                               /* wp_nav_menu(
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
                            );*/
                            echo'<div class="contact-map">';
                        
                        		echo '<div>'.$map.'</div>';
                
                        	echo'</div>';
                        echo'</div>';
                        echo '<div class ="footer_menu">';
                            echo '<div class ="text-footer-menu">' . (!empty($title_mune_2) ? $title_mune_2 : 'اعمالنا'  ). '</div>';
                            wp_nav_menu(
                               array(
                                   'theme_location' => 'footer-menu-2',
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
                        echo'<div class="contact-info-right">';
                        echo '<div class ="text-footer-menu">' . (!empty($title_mune_3) ? $title_mune_3 : 'معلومات الاتصال'  ). '</div>';
                        echo'<div class="contact-info">';
                            echo '<div class="Address">';

                                echo'<i class="fa-light fa-location-crosshairs"></i>';

                                echo '<div class="info-footer">';

                                    echo '<span class="first_info">العنوان :</span>';

                                    echo'<span>'.$Adress.'</span>';

                                echo'</div>';

                            echo'</div>';
                            echo'<a href="tel:'.$Phone.'" aria-label="رقم الهاتف: '.$Phone.'" data-call="Phone">';

                                echo'<i class="fa-solid fa-phone"></i>';

                                echo '<div class="info-footer">';

                                    echo '<span class="first_info">رقم الهاتف:</span>';

                                    echo'<span>'.$Phone.'</span>';

                                echo'</div>';

                            echo'</a>';

                            echo '<div class="Address">';

                                echo'<i class="fa-solid fa-envelope"></i>';

                                echo '<div class="info-footer">';

                                    echo '<span class="first_info">راسلنا عبر البريد :</span>';

                                    echo'<span>'.$email.'</span>';

                                echo'</div>';

                            echo'</div>';
                            

                        echo'</div>';
                    echo'</div>';

                    echo'</div>';

                        

                      

                    

                echo'</div>';

            
                        

            

        echo '</div>';
    echo '</footer>';
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
                    /*echo '<allrights-SEO>';
                        echo '<span>ارشفه <a target="_blank" rel="nofollow" href="https://www.facebook.com/alsyd.hossam"> Teko</a></span>';
                    echo '</allrights-SEO>';*/
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

    $calling_buttons_direction =  get_option('calling_buttons_direction','rtl');
    $Phone_2 =  get_option('Phone_2');

    $calling_button_color =  get_option('calling_button_color');

    $whatsapp_button_color =  get_option('whatsapp_button_color');

    $calls_button_width =  get_option('calls_button_width');

    $calls_button_height =  get_option('calls_button_height');

    $size_button =  get_option('size_button','20');

    // Back to top button 


    

    // btn contact

    // Back to top button 
    echo '<div id="button" aria-label="go to up"><i aria-hidden="true" class="far fa-sort-up"></i></div>';
    
    // btn contact
    echo '<div class ="btn-fixed-bh">';
        echo '<div class="btn-photo">';
            echo'<a href="'.home_url('photo-gallery').'">';
            echo '<span>معرض الصور</span>';
            echo '<div class="footer-header">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>';
                echo '</div>';
            echo'</a>';
        echo'</div>';
        echo '<div class="btn-phone">';
            echo'<a href="tel:'.$Phone.'" aria-label="Phone" data-call="Phone">';
                echo '<span> <strong>المكتب</strong> '.$Phone.'</span>';
                echo '<div class="footer-header">';
                    echo '<svg width="36" height="48" fill="#fff" role="img" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 512 512">
                        <path d="M352 320c-32 32-32 64-64 64s-64-32-96-64-64-64-64-96 32-32 64-64-64-128-96-128-96 96-96 96c0 64 65.75 193.75 128 256s192 128 256 128c0 0 96-64 96-96s-96-128-128-96z">
                        </path>
                    </svg>';
                    echo '</div>';
                echo'</a>';
        echo'</div>';
        if(!empty($Phone_2)){
            
        
        echo '<div class="btn-phone">';
            echo'<a href="tel:'.$Phone_2.'" aria-label="Phone" data-call="Phone">';
                echo '<span><strong>الجوال</strong> '.$Phone_2.'</span>';
                echo '<div class="footer-header">';
                    echo '<svg width="36" height="48" fill="#fff" role="img" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 512 512">
                        <path d="M352 320c-32 32-32 64-64 64s-64-32-96-64-64-64-64-96 32-32 64-64-64-128-96-128-96 96-96 96c0 64 65.75 193.75 128 256s192 128 256 128c0 0 96-64 96-96s-96-128-128-96z">
                        </path>
                    </svg>';
                    echo '</div>';
                echo'</a>';
        echo'</div>';
        
        }
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



    echo '</script>';

    if( IsSpeed() == false && ( is_single() || is_page() || is_home() ) ) {

        echo "<script id='rendered-js' type='module'>function loadCSSOnce(href){if(!document.querySelector('link[href=\"'+href+'\"]')){const link=document.createElement('link');link.rel='stylesheet';link.href=href;document.head.appendChild(link);}}function loadJSOnce(src){if(!document.querySelector('script[src=\"'+src+'\"]')){const script=document.createElement('script');script.src=src;script.type='module';document.head.appendChild(script);}}function initPhotoSwipeLightbox(gallerySelector){import('https://unpkg.com/photoswipe/dist/photoswipe-lightbox.esm.js').then(({default:PhotoSwipeLightbox})=>{const lightbox=new PhotoSwipeLightbox({gallery:gallerySelector,children:'a',initialZoomLevel:'fit',secondaryZoomLevel:1,maxZoomLevel:5,pswpModule:()=>import('https://unpkg.com/photoswipe')});lightbox.init();});}function lazyLoadPhotoSwipe(selector,cssHref,jsSrc){const observer=new IntersectionObserver((entries,observer)=>{entries.forEach(entry=>{if(entry.isIntersecting){observer.unobserve(entry.target);loadCSSOnce(cssHref);loadJSOnce(jsSrc);initPhotoSwipeLightbox(selector);}});},{threshold:0.1});document.querySelectorAll(selector).forEach(element=>{observer.observe(element);});}if(document.querySelectorAll('[pswp]').length){lazyLoadPhotoSwipe('[pswp]','https://unpkg.com/photoswipe/dist/photoswipe.css','https://unpkg.com/photoswipe/dist/photoswipe-lightbox.esm.js');}</script>";

    }

    
    

        if(IsSpeed() == false){

       wp_footer();

        }



do_action('AfterWPFooter');



echo '</body>';

echo '</html>';