<?php
class Theme__WidgetModel__faq extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__faq',
        );
        parent::__construct( 'faq', '[Widget] الاسئله', $widget_ops );
    }
    public function widget( $args, $instance ) {
        $beffore_title = '';
        if( isset($instance['beffore_title']) ) {
            $beffore_title = $instance['beffore_title'];
        }
        $title = '';
        if( isset($instance['title']) ) {
            $title = $instance['title'];
        }
        
        $widget_description = '';
        if( isset($instance['widget_description']) ) {
            $widget_description = $instance['widget_description'];
        }
        $number = '';
        if( isset($instance['number']) ) {
            $number = $instance['number'];
        }
        $per = '';
        if( isset($instance['per']) ) {
            $per = $instance['per'];
        }
        $url = '';
        if( isset($instance['url']) ) {
            $url = $instance['url'];
        }
        $button_name = '';
        if( isset($instance['button_name']) ) {
            $button_name = $instance['button_name'];
        }
        $Phone = get_option('Phone');
        $Whatsapp = get_option('Whatsapp');
        echo'<div section-concept="true" class="section-faq">';
            echo '<div class="container">';
                echo '<div class="YC-Widget-box">';
                    echo '<div class="titles_concept">';
                        echo '<div class="titles_concept_1">';
                            echo'<span>'.$beffore_title.'</span>';
                            echo'<h2>'.$title.'</h2>';
                            echo'<p>'.$widget_description.'</p>';
                        echo '</div>';
                        echo'<div class="btn-services_1">';
                            if( !empty(trim($url)) ) {
                                echo '<div class="ButtonConcept"><a href="'.$url.'"><span>'.$button_name.'<i class="fa-solid fa-chevron-left"></i></span></a></div>';
                            }
                        echo'</div>';
                    echo '</div>';
                    echo '<div class ="faq_posts">';
                        echo'<div class="faq-info">';
                            $args = [ 'post_type'=>'faq','posts_per_page'=>$number];
                            (new ThemeStatic)->Part("faq", array("args"=>$args));
                        echo'</div>';
                        
                    echo'</div>';
                echo'</div>';
            echo'</div>';
        echo'</div>';
    }
    public function form( $instance ) {
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "faq",
                "id"    => $this->get_field_id('faq'),
                "name"  => $this->get_field_name('faq'),
                "stack" => array(
                    array(
                        "title"  => "قبل  العنوان",
                        "type"  => "text",
                        "id"    => $this->get_field_id("beffore_title"),
                        "singleID" => "beffore_title",
                        "name"    => $this->get_field_name("beffore_title"),
                    ),
                    array(
                        "title"  => "العنوان",
                        "type"  => "text",
                        "id"    => $this->get_field_id("title"),
                        "singleID" => "title",
                        "name"    => $this->get_field_name("title"),
                    ),
                    
                    array(
                        "title"  => "وصف",
                        "type"  => "textarea",
                        "id"    => $this->get_field_id("widget_description"),
                        "singleID" => "widget_description",
                        "name"    => $this->get_field_name("widget_description"),
                    ),
                    array(
                        "title"  => "عدد الاسئلة",
                        "type"  => "text",
                        "id"    => $this->get_field_id("number"),
                        "singleID" => "number",
                        "name"    => $this->get_field_name("number"),
                    ),
                   
                    array(
                        "title"  => "رابط الزر",
                        "type"  => "text",
                        "id"    => $this->get_field_id("url"),
                        "singleID" => "url",
                        "name"    => $this->get_field_name("url"),
                    ),
                     array(
                        "title"  => "اسم الزر",
                        "type"  => "text",
                        "id"    => $this->get_field_id("button_name"),
                        "singleID" => "button_name",
                        "name"    => $this->get_field_name("button_name"),
                    ),
                    
                ),
                "repeat"=> false
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
function Theme__Widgets__faq() {
    register_widget( 'Theme__WidgetModel__faq' );
}
add_action( 'widgets_init', 'Theme__Widgets__faq' );