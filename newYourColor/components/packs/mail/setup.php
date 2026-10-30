<?php

    function custom_page_mail() {
        add_menu_page(
            'طلبات العملاء',
            'طلبات العملاء',
            'manage_options',
            'add_mail_fields',
            'add_mail_fields',
            'dashicons-edit',
            6
        );
    }
    
    add_action( 'admin_menu', 'custom_page_mail' );
    
    add_action('admin_enqueue_scripts',function(){
        wp_enqueue_script('mail-page-js',get_template_directory_uri().'/components/packs/mail/page_order.js?v'.rand(),['jquery']);
    });
    function add_mail_fields(){
        $postID = array();
        foreach( get_posts(['posts_per_page'=>-1,'post_type'=>'clint']) as $posts ){
            $postID[] = $posts->ID;
        }
        $currentURL = (new ThemeStatic)->GetCurrentURL();
        $per = 10;
        $args = array(
            'post_type' => 'clint',
            'posts_per_page' => $per, 
            
        );

        if( isset( $_GET['paged'] ) ) {
            $args['paged']    = $_GET['paged'];
        }
        #
        $UniqId = uniqid();
        $Founder = new WP_Query($args);
        $CountQuery = $Founder->found_posts;
        $CounterAll = $CountQuery / $per;

        if( strpos($CounterAll, '.') !== FALSE ){
            $CounterAll = explode('.',$CounterAll)[0];
            $CounterAll = $CounterAll + 1;

        }
        $Paged = ((!isset($_GET['paged']))) ? 1 : $_GET['paged'];
        $BackPaged = $Paged - 1;
        $NextPaged = $Paged + 1;
        $PagenateURL = $currentURL;
        $PagenateURL = explode('page=add_mail_fields', $PagenateURL)[0];
        $PagenateURL = $PagenateURL.'page=add_mail_fields';
        echo '<div class="custom-page-mail-">';
            echo '<div class="container">';
                echo '<div class="page-boxed-">';
                    echo '<div class="box-page">';
                        echo '<div class="title_page">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464H398.7c-8.9-63.3-63.3-112-129-112H178.3c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3z"/></svg>';
                            echo '<div class="title_boxed">'; 
                                echo '<h2>طلبات العملاء</h2>';
                                echo '<p>طلبات العملاء التي يتم ارسالها</p>';
                            echo '</div>'; 
                            
                        echo '</div>';
                        echo '<ul class="-Navs-Actions">';
                            echo '<li data-navs-actions="SelectAll" data-uniqid="'.$UniqId.'"><i class="far fa-plus-octagon"></i><span>تحديد الكل </span></li>';
                            echo '<li data-navs-actions="RemoveSelectAll" data-uniqid="'.$UniqId.'" style="display:none;pointer-events: none; opacity: 0.5;"><i class="fal fa-times-hexagon"></i><span>ألغاء التحديد</span></li>';
                            echo '<li data-navs-actions="RemoveAllSelected" data-uniqid="'.$UniqId.'" style="pointer-events: none; opacity: 0.5;"><i class="fas fa-minus-hexagon"></i><span>حذف المحدد</span></li>';
                        echo '</ul>';
                    echo '</div>';
                    echo '<div class="custom-page-mail">';
                        echo '<table>';
                            echo '<thead  class ="mail-header">';
                                echo '<tr>';
                                    echo '<th class="checkbox"><input type="checkbox" id="select-all" name="name" value="'.array_values($postID).'"></th>';
                                    echo '<th>الاسم </th>';
                                    echo '<th>البريد الاكلتروني</th>';
                                    echo '<th>رقم الهاتف</th>';
                                    echo '<th>الرسالة</th>';
                                    echo '<th>حذف </th>';
                                echo '</tr>';
                                
                            echo '</thead>';
                                $argrment = '';
                                $argrment = get_posts($args);
                                if (!empty($argrment)){
                                    echo '<tbody class="client_order -ScrollerCenter" data-uniqid="'.$UniqId.'">';
                                        foreach ( $argrment as $post) {
                                            $name = $post->post_title;
                                            $phone = get_post_meta($post->ID,'phone',true);
                                            $email = get_post_meta($post->ID,'email',true);
                                            $message = get_post_meta($post->ID,'message',true);
                                            $address = get_post_meta($post->ID,'address',true);
                                            
                                            echo '<tr data-client="'.$post->ID.'" class="-contain-MiniBox" data-post-id="'.$post->ID.'">';
                                                echo '<td data-client="'.$post->ID.'">';
                                                    echo '<div class="-checkBox-post-fixed" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
                                                echo '</td>';
                                                echo '<td><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464H398.7c-8.9-63.3-63.3-112-129-112H178.3c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3z"/></svg> '.$name.'</td>';
                                                echo '<td class="email">';
                                                        if(!empty($email)){
                                                            echo '<a href="mailto:?subject='.$email.'">'.$email.'</a>';
                                                        }
                                                echo '</td>';
                                                echo '<td class="phone">';
                                                    if(!empty($phone)){
                                                        echo '<a href="tel:'.$phone.'">'.$phone.'</a>';
                                                    }else {
                                                        echo '<p>لا يوجد رقم</p>';
                                                    }
                                                echo '</td>';
                                                echo '<td class="box-message">';
                                                    if(!empty($message)){
                                                        echo '<span>عرض الرسالة </span>';
                                                        echo '<div class="message">';
                                                            echo '<h2>تفاصيل الرسالة</h2>';
                                                            echo '<p>' .$message.'</p>';
                                                        echo '</div>';
                                                    }else {
                                                        echo '<p>لا توجد رسالة </p>';
                                                    }
                                                echo '</td>';                                    
                                                echo '<td>';
                                                    echo '<div class="romoves-posts" data-remove="'.$post->ID.'"><span><svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24" width="512" height="512"><path d="M21,4H17.9A5.009,5.009,0,0,0,13,0H11A5.009,5.009,0,0,0,6.1,4H3A1,1,0,0,0,3,6H4V19a5.006,5.006,0,0,0,5,5h6a5.006,5.006,0,0,0,5-5V6h1a1,1,0,0,0,0-2ZM11,2h2a3.006,3.006,0,0,1,2.829,2H8.171A3.006,3.006,0,0,1,11,2Zm7,17a3,3,0,0,1-3,3H9a3,3,0,0,1-3-3V6H18Z"/><path d="M10,18a1,1,0,0,0,1-1V11a1,1,0,0,0-2,0v6A1,1,0,0,0,10,18Z"/><path d="M14,18a1,1,0,0,0,1-1V11a1,1,0,0,0-2,0v6A1,1,0,0,0,14,18Z"/></svg> حذف</span></div>';
                                                echo '</td>';
                                            echo '</tr>';
                                        } 
                                        echo '<PagnationsNavs id="PagnationsNavs" data-counter="'.$CountQuery.'" data-pagedcounter="'.$CounterAll.'" data-permalink="'.$PagenateURL.'">';
                                            echo '<ul>';        
                                                echo '<li class="perv" '.(($Paged > 1) ? '' : 'style="pointer-events:none;opacity:0.5"').'><a href="'.$PagenateURL.'&paged='.$BackPaged.'"><i class="fas fa-chevron-right"></i><span>السابق</span></a></li>';
                                                echo '<li  class="next"'.(($Paged >= $CounterAll) ? 'style="pointer-events:none;opacity:0.5"' : '').'><a href="'.$PagenateURL.'&paged='.$NextPaged.'"><span>التالي</span><i class="fas fa-chevron-left"></i></a></li>';
                                                echo '<li  '.(($Paged >= $CounterAll) ? 'style="pointer-events:none;opacity:0.5"' : '').'><a href="'.$PagenateURL.'&paged='.$CounterAll.'"><coun-tt>'.$CounterAll.'</coun-tt> <span>الصفحة الاخيرة</span><i class="fas fa-chevron-double-left"></i></a></li>';
                                            echo '</ul>';
                                        echo '</PagnationsNavs>';    
                                    echo '</tbody>';
                                }else {
                                    echo '<div class="YT-emptyform">';
                                        echo '<div class="NothingFoundFilter">';
                                            echo '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g> <g> <path d="M174.829,187.317c-13.772,0-24.976,11.204-24.976,24.976s11.204,24.976,24.976,24.976 c13.767,0,24.971-11.202,24.976-24.976C199.805,198.521,188.603,187.317,174.829,187.317z"></path> </g> </g> <g> <g> <path d="M337.171,187.317c-13.772,0-24.976,11.204-24.976,24.976s11.204,24.976,24.976,24.976 c13.767,0,24.971-11.202,24.976-24.976C362.146,198.521,350.945,187.317,337.171,187.317z"></path> </g> </g> <g> <g> <path d="M256,0C114.84,0,0,114.842,0,256s114.84,256,256,256s256-114.842,256-256S397.16,0,256,0z M256,474.537 c-120.501,0-218.537-98.036-218.537-218.537S135.499,37.463,256,37.463S474.537,135.499,474.537,256S376.501,474.537,256,474.537z "></path> </g> </g> <g> <g> <path d="M239.464,314.134l-73.929,12.203l6.102,36.964l74.046-12.223c35.402-6.082,68.507,7.402,90.821,36.983l29.908-22.56 C335.818,324.943,288.313,305.755,239.464,314.134z"></path> </g> </g></svg>';
                                            echo '<p>لا يوجد طلبات</p>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                                
                           
                                
                        echo '</table>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
   
        
    }

