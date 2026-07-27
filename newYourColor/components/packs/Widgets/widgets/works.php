<?
class Theme__WidgetModel__works extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__works',
        );
        $sitename = get_option('sitename');
        parent::__construct( 'works', ' [ Widgets-[ '.$sitename.' ] ] الاعمال السابقة', $widget_ops );
        $this->ThemeStatic = new ThemeStatic;
    }
    public function SeparateTitle($string) {
        $string = str_replace('[b]', '<strong-color>', $string);
        $string = str_replace('[/b]', '</strong-color>', $string);
        return $string;
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
        $type = '';
        if( isset($instance['type']) ) {
            $type = $instance['type'];
        }
        $widget_description = '';
        if( isset($instance['widget_description']) ) {
            $widget_description = $instance['widget_description'];
        }
        $number = 4;
        if( isset($instance['number']) ) {
            $number = $instance['number'];
        }
        $orderby = '';
        if( isset($instance['orderby']) ) {
            $orderby = $instance['orderby'];
        }
        $arguments = array(
            "post_type"     => 'works',
            "posts_per_page"=> $number
        );
        if( $orderby == 'trending' ) {
            $arguments['meta_key'] = 'trending';
            $arguments['orderby'] = 'meta_value_num';
        }else if( $orderby == 'rand' ) {
            $arguments['orderby'] = 'rand';

        }else if( $orderby == 'old' ) {
            $arguments['order'] = 'ASC';
        }
        $urls = '';
        if( isset($instance['url']) ) {
            $url = $instance['url'];
        }
        $button_name = '';
        if( isset($instance['button_name']) ) {
            $button_name = $instance['button_name'];
        }
        $UniqId = uniqid();
        #
            echo'<div section-concept="true" class="model-works">';
                echo '<div class="container">';
                    echo '<div class="titles_concept">';
                        echo '<div class="titles_concept_1">';
                             echo'<span>'.$beffore_title.'</span>';
                            echo'<h2>'.$title.'</h2>';
                            echo'<p>'.$widget_description.'</p>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="section-box-workss">';
                        echo '<div class="-section-">';
                            $i=0;
                            echo '<div class="post-intro-slider-">';
                                foreach( get_posts($arguments) as $post ) {$i++;
                                 $url = get_the_permalink($post->ID);
                                 $img = get_the_post_thumbnail_url($post->ID, 'default');
                                 $time = 'منذ '.human_time_diff( date('U', strtotime($post->post_date)), current_time('timestamp') );
                                 $title = $post->post_title;
                                 $price = get_post_meta($post->ID, 'price', true);
                                 $space = get_post_meta($post->ID, 'space', true);
                                 $date = get_post_meta($post->ID, 'date', true);
                                 $site = get_post_meta($post->ID, 'site', true);
                                 $service = get_post_meta($post->ID, 'service', true);
                                 $Client = get_post_meta($post->ID, 'Client', true);
                                 $imageservice = get_post_meta($post->ID, 'imageservice', true);
                                 echo '<div data-id="'.$post->ID.'" class="-works">';
                                     echo '<div class="-works-ThumbBox">';
                                         echo '<div class="works-single-img- defimg">';
                                             if(!empty($imageservice)){
                                                 foreach ($imageservice as $src){
                                                     echo '<img width="100%" height="100%" alt="'.$title.'" data-loader-src="'.$src.'"/>';
                                                 }

                                             }
                                         echo '</div>';
                                        echo '<span>'.$price.'</span>';
                                     echo '</div>';
                                
                                    echo '<div class="-works-info-box">';
                                       echo '<div class="works-title-model">';
                                            echo '<span class="service-">'.$service.'</span>';
                                          echo '<h3>'.$title.'</h3>';
                                       echo '</div>';  
                                        echo '<ul class="box-widght-data">';
                                          if(!empty($Client)){
                                          echo '<li class="Client">';
                                              echo '<p>اسم العميل  :</p>';
                                              echo '<span>'.$Client.'</span>';
                                          echo '</li>';
                                          }
                                          if(!empty($site)){
                                          echo '<li class="site">';
                                              echo '<p>الموقع :</p>';
                                              echo '<span>'.$site.'</span>';
                                          echo '</li>';
                                          }
                                          if(!empty($date)){
                                          echo '<li class="date">';
                                              echo '<p>التاريخ :</p>';
                                              echo '<span>'.$date.'</span>';
                                          echo '</li>';
                                          }
                                          if(!empty($service)){
                                          echo '<li class="service">';
                                              echo '<p>نوع الخدمة :</p>';
                                              echo '<span>'.$service.'</span>';
                                          echo '</li>';
                                          }
                                          if(!empty($price)){
                                          echo '<li class="price">';
                                              echo '<p>السعر :</p>';
                                              echo '<span>'.$price.'</span>';
                                          echo '</li>';
                                          }
                                        echo '</ul>';
                                    echo '</div>';
                                 echo '</div>';

                                }

                           echo'</div>';

                        echo '</div>';
                    echo '</div>';
                    if(!empty($urls)){
                        echo ' <button class="btn">';
                            echo '<a class="btn-content" href="'.$urls.'">';
                                echo ' <div>'.$button_name.'</div>';
                                echo '<svg fill="none" viewBox="0 0 24 24" height="25px" width="25px" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linejoin="round" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" stroke="white" d="M11.6801 14.62L14.2401 12.06L11.6801 9.5"></path>
                                    <path stroke-linejoin="round" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" stroke="white" d="M4 12.0601H14.17"></path>
                                    <path stroke-linejoin="round" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" stroke="white" d="M12 4C16.42 4 20 7 20 12C20 17 16.42 20 12 20"></path>
                                </svg>';
                            echo ' </a>';
                        echo '</button>';
                    }
                echo '</div>';
            echo '</div>';
    }

    public function form( $instance ) {
        #
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "workss",
                "id"    => $this->get_field_id('workss'),
                "name"  => $this->get_field_name('workss'),
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
                        "title"  => "عدد  المواضيع  ",
                        "type"  => "text",
                        "id"    => $this->get_field_id("number"),
                        "singleID" => "number",
                        "name"    => $this->get_field_name("number"),
                    ),
                    
                    array(
                        "title"  => "إسم الزر",
                        "type"  => "text",
                        "id"    => $this->get_field_id("button_name"),
                        "singleID" => "button_name",
                        "name"    => $this->get_field_name("button_name"),
                    ),
                    array(
                        "title"  => " رابط الزر ",
                        "type"  => "text",
                        "id"    => $this->get_field_id("urls"),
                        "singleID" => "urls",
                        "name"    => $this->get_field_name("urls"),
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

        if( empty($instance['title']) ) {

            $instance['title'] = get_term($instance['category'], 'category')->name;

        }

        return $instance;

    }

}

function Theme__Widgets__works() {

    register_widget( 'Theme__WidgetModel__works' );

}

add_action( 'widgets_init', 'Theme__Widgets__works' );