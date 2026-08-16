<?php
class Theme__WidgetModel__city extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__city',
        );
        parent::__construct( 'city', '[Widget] المدن', $widget_ops );
    }
    public function widget( $args, $instance ) {
        $title = '';
        if( isset($instance['title']) ) {
            $title = $instance['title'];
        }
        $secondary_title = '';
        if( isset($instance['secondary_title']) ) {
            $secondary_title = $instance['secondary_title'];
        }
        $widget_description = '';
        if( isset($instance['widget_description']) ) {
            $widget_description = $instance['widget_description'];
        }
        $cities = '';
        if( isset($instance['cities']) ) {
            $cities = $instance['cities'];
        }
        
        $services = '';
        if( isset($instance['services']) ) {
            $services = $instance['services'];
        }
        $city = get_terms(array( 'taxonomy' => 'country', 'hide_empty' => false, "number"=>$cities) );
        echo'<section section-concept="true" class="city-section ">';
            echo'<div class="container">';
                echo'<div class="services-info">';

                    echo '<div class="titles_concept_1">';
                        echo'<h2>'.$title.'</h2>';
                        echo'<p>'.$widget_description.'</p>';
                    echo '</div>';
                echo '</div>';
                echo'<div class="d-flex">';
                    $i = 0;
                    
                    foreach ($city as $c) {$i++;
                        $icon_country = get_term_meta($c->term_id, "icon", true);
                        if( empty($icon_country) ) {
                            $icon_country = '<i class="fa-solid fa-city"></i>';
                        }
                       
                        $btn = get_term_meta($c->term_id , 'btn' ,1);
                        echo'<div class="city-block">';         
                            echo '<a class="links" href="'.get_term_link($c).'">';  
                                echo '<div class ="icon_country">';
                                    echo '<span>'.$icon_country.'</span>';
                                echo '</div>';           
                                echo'<div class="head-block-city">';
                                    echo '<h3>'.$c->name.'</h3>';                                    
                                echo'</div>';    
                            echo '</a>';                                    
                        echo'</div>';
                    
                    }
                echo'</div>';
                echo'<div class="btn-services_1">';
                    if( !empty($url) ) {
                        echo '<div class="ButtonConcept"><a href="'.$url.'"><span>'.$button_name.' </span></a></div>';
                    }
                echo'</div>';
            echo'</div>';
        echo'</section>';
    }
    public function form( $instance ) {
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "cities",
                "id"    => $this->get_field_id('cities'),
                "name"  => $this->get_field_name('cities'),
                "stack" => array(
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
                        "title"  => "عدد المدن",
                        "type"  => "text",
                        "id"    => $this->get_field_id("cities"),
                        "singleID" => "cities",
                        "name"    => $this->get_field_name("cities"),
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
function Theme__Widgets__city() {
    register_widget( 'Theme__WidgetModel__city' );
}
add_action( 'widgets_init', 'Theme__Widgets__city' );