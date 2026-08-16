<?php 
class Theme__WidgetModel__clients extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__clients',
        );
        parent::__construct( 'clients', '[Widget] العملاء', $widget_ops );
    }
    public function widget( $args, $instance ) {
       
        $clients = '';
        if( isset($instance['clients']) ) {
            $clients = $instance['clients'];
        }
        $title_clients = '';
        if( isset($instance['title_clients']) ) {
            $title_clients = $instance['title_clients'];
        }
        
        $content = '';
        if( isset($instance['content']) ) {
            $content = $instance['content'];
        }
        
        echo '<clients section-concept="true" '.( ( !IsSpeed() ) ? ' data-loader-style="--bg-clients:url('.get_template_directory_uri().'/components/styles/img/shape_bkground.jpg'.')"': '' ).'>';
            echo '<div class="YC-container-lest">';
                echo '<div class="container">';
                    echo '<div class="YC-container-lest-boxed">';
                        echo '<div class="clients-titles_concept">';
                            echo'<h1>'.$title_clients.'</h1>';
                        echo '</div>';
                        echo '<div class="clients_concept">';
                            foreach($clients as $box){
                                echo '<div class="clients_list">';
                                    if(IsSpeed() == false){
                                    echo '<div class="images_clients">';
                                        echo '<img width="100%" height="100%" alt="'.get_option('Sitename').'" data-loader-src="'.$box['images']['url'].'"/>';
                                    echo '</div>';
                                    }
                                    
                                echo '</div>';
                            }
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</clients>';

    }
    public function form( $instance ) {
       
        WidgetInput(
             array(
                "instance" => $instance,
                "title"  => "اعدادات وادحيت عملائنا",
                "singleID" => "clients_intro",
                "id"    => $this->get_field_id('clients_intro'),
                "name"  => $this->get_field_name('clients_intro'),
                "stack" => array(
                    
                    array(
                        "title"  => "العنوان",
                        "type"  => "text",
                        "id"    => $this->get_field_id("title_clients"),
                        "singleID" => "title_clients",
                        "name"    => $this->get_field_name("title_clients"),
                    ),
                    
                    
                ),
                "repeat"=> false
            )
        );
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "صور العملاء",
                "singleID" => "clients",
                "id"    => $this->get_field_id('clients'),
                "name"  => $this->get_field_name('clients'),
                "stack" => array(
                    
                    array(
                        "title"  => "صورة العميل",
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
function Theme__Widgets__clients() {
    register_widget( 'Theme__WidgetModel__clients' );
}
add_action( 'widgets_init', 'Theme__Widgets__clients' );