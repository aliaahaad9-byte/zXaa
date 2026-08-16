<?php

class Theme__WidgetModel__intro extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__intro',
        );
        parent::__construct( 'intro', '[Widget] إنترو', $widget_ops );
    }
    public function widget( $args, $instance ) {
        $title = '';
        if( isset($instance['title']) ) {
            $title = $instance['title'];
        }
        
        $url = '';
        if( isset($instance['url']) ) {
            $url = $instance['url'];
        }
        $button_name = '';
        if( isset($instance['button_name']) ) {
            $button_name = $instance['button_name'];
        }
        $button_number = '';
        if( isset($instance['button_number']) ) {
            $button_number = $instance['button_number'];
        }
        $description = '';
        if( isset($instance['description']) ) {
            $description = $instance['description'];
        }
        $images = '';
        if( isset($instance['images']) ) {
            $images = $instance['images'];
        }
       
        $text = $title;
        $words = explode(" ", $text); // تقسيم الجملة إلى كلمات
        $result = "";
        
        foreach ($words as $key => $word) {
            if ($key == 1 || $key == 2) { // التحقق إذا كانت الكلمة الحالية هي الثانية أو الثالثة
                $result .= "<strong>" . $word . "</strong> ";
            } else {
                $result .= $word . " ";
            }
        }
         $intro = '';
        if( isset($instance['intro']) ) {
            $intro = $instance['intro'];
        }
        $Whatsapp = get_option('Whatsapp');
        $Phone = get_option('Phone');
        echo '<div  class="page-concept">';
            echo '<div class="container-page-intro">';
                foreach($intro as $intropost ){
                    echo'<div class="YC-intro-yu">';
                        $text = $intropost['title'];
                        $words = explode(" ", $text); // تقسيم الجملة إلى كلمات
                        $result = "";
                            
                        foreach ($words as $key => $word) {
                            if ($key == 1 || $key == 2) { // التحقق إذا كانت الكلمة الحالية هي الثانية أو الثالثة
                                $result .= "<strong>".$word."</strong> ";
                            }else {
                                $result .= $word . " ";
                            }
                        }
                        echo'<div class="slider-intro">';
                            echo'<div class="info-cover">';
                                echo'<div class="title-intro">'.$result.'</div>';
                                
                               echo '<p>'.$intropost['description'].'</p>';  
                                echo '<div class="intro_buttun">';

                                    echo'<a class="content-btn" href="'.$intropost['contactlink'].'">'.$intropost['contactus'].'</a>';
                                    
                                echo'</div>';
                                if( !empty(trim($url)) ) {
                                    echo'<a class="contact-us-btn" href="'.$url.'">'.$button_name.'</a>'; 
                                }
                            echo'</div>';
                        echo'</div>';
                        if(IsSpeed() == false){
                            
                        
                            echo '<div class="img_back-yu" data-loader-style="--bg-intro:url('.$intropost['images']['url'].')"></div>';   
                        }
                    echo'</div>';
                }
            echo '</div>';
            
        echo '</div>';

    }
    public function form( $instance ) {

         WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "intro",
                "id"    => $this->get_field_id('intro'),
                "name"  => $this->get_field_name('intro'),
                "stack" => array(
                    
                    array(
                        "title"  => "العنوان",
                        "type"  => "text",
                        "id"    => $this->get_field_id("title"),
                        "singleID" => "title",
                        "name"    => $this->get_field_name("title"),
                    ),
                    
                    array(
                        "title"  => "الوصف",
                        "type"  => "textarea",
                        "id"    => $this->get_field_id("description"),
                        "singleID" => "description",
                        "name"    => $this->get_field_name("description"),
                    ),
                    array(
                        "title"  => "صورة ",
                        "type"  => "file",
                        "id"    => $this->get_field_id("images"),
                        "singleID" => "images",
                        "name"    => $this->get_field_name("images"),
                    ),
                    array(
                        "title"  => "الرابط",
                        "type"  => "text",
                        "id"    => $this->get_field_id("contactlink"),
                        "singleID" => "contactlink",
                        "name"    => $this->get_field_name("contactlink"),
                    ),
                    array(
                        "title"  => "اسم الزر",
                        "type"  => "text",
                        "id"    => $this->get_field_id("contactus"),
                        "singleID" => "contactus",
                        "name"    => $this->get_field_name("contactus"),
                    ),
                    
                      
                    
                    
                ),
                "repeat"=> true,
            )
        );
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        foreach( $new_instance as $k => $v ) {
            if( is_string($v) ) $v = stripslashes($v);
            $instance[$k] = ( ! empty( $v ) ) ? $v : '';
        }
        return $instance;
    }
}
function Theme__Widgets__intro() {
    register_widget( 'Theme__WidgetModel__intro' );
}
add_action( 'widgets_init', 'Theme__Widgets__intro' );