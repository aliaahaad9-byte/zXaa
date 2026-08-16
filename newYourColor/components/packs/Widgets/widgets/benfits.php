<?php 
class Theme__WidgetModel__benfits extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__benfits',
        );
        parent::__construct( 'benfits', '[Widget] المميزات', $widget_ops );
    }
    public function widget( $args, $instance ) {
        $beffore_title = '';
        if( isset($instance['beffore_title']) ) {
            $beffore_title = $instance['beffore_title'];
        }
        $benfits = '';
        if( isset($instance['benfits']) ) {
            $benfits = $instance['benfits'];
        }
        $title = '';
        if( isset($instance['title']) ) {
            $title = $instance['title'];
        }
        
        $content = '';
        if( isset($instance['content']) ) {
            $content = $instance['content'];
        }
        
        echo '<features section-concept="true" '.( ( !IsSpeed() ) ? ' data-loader-style="--bg-benfits:url('.get_template_directory_uri().'/components/styles/img/shape_bkground.jpg'.')"': '' ).'>';
            echo '<div class="container">';
                echo '<div class="titles_concept">';
                    echo '<div class="titles_concept_1">';
                         echo'<span>'.$beffore_title.'</span>';
                        echo'<h1>'.$title.'</h1>';
                        echo'<p>'.$content.'</p>';
                    echo '</div>';
                echo '</div>';
                echo '<benfits>';
                    foreach($benfits as $box){
                        echo '<benfit>';
                            if(IsSpeed() == false){
                            echo '<div class="images_features">';
                                echo '<img width="80" height="80" alt="'.get_option('Sitename').'" data-loader-src="'.$box['images']['url'].'"/>';
                            echo '</div>';
                            }
                            echo '<div class="titles_features">';
                                echo '<div>'.$box['title'].'</div>';
                                echo '<p>'.$box['message'].'</p>';
                            echo '</div>';
                        echo '</benfit>';
                    }
                echo '</benfits>';
            echo '</div>';
        echo '</features>';

    }
    public function form( $instance ) {
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "benfits_intro",
                "id"    => $this->get_field_id('benfits_intro'),
                "name"  => $this->get_field_name('benfits_intro'),
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
                        "title"  => "المحتوي",
                        "type"  => "textarea",
                        "id"    => $this->get_field_id("content"),
                        "singleID" => "content",
                        "name"    => $this->get_field_name("content"),
                    ),
                ),
                "repeat"=> false
            )
        );
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "benfits",
                "id"    => $this->get_field_id('benfits'),
                "name"  => $this->get_field_name('benfits'),
                "stack" => array(
                    array(
                        "title"  => "العنوان",
                        "type"  => "text",
                        "id"    => $this->get_field_id("title"),
                        "singleID" => "title",
                        "name"    => $this->get_field_name("title"),
                    ),
                    array(
                        "title"  => "المحتوي",
                        "type"  => "textarea",
                        "id"    => $this->get_field_id("message"),
                        "singleID" => "message",
                        "name"    => $this->get_field_name("message"),
                    ),
                    array(
                        "title"  => "الايقونة",
                        "type"  => "file",
                        "id"    => $this->get_field_id("images"),
                        "singleID" => "images",
                        "name"    => $this->get_field_name("images"),
                    ),
                ),
                "repeat"=> true
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
function Theme__Widgets__benfits() {
    register_widget( 'Theme__WidgetModel__benfits' );
}
add_action( 'widgets_init', 'Theme__Widgets__benfits' );