<?
class Theme__WidgetModel__price extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__price',
        );
        parent::__construct( 'price', '[Widget] الاسعار', $widget_ops );
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
        $secondary_title = '';
        if( isset($instance['secondary_title']) ) {
            $secondary_title = $instance['secondary_title'];
        }
        $widget_description = '';
        if( isset($instance['widget_description']) ) {
            $widget_description = $instance['widget_description'];
        }
        $message = '';
        if( isset($instance['message']) ) {
            $message = $instance['message'];
        }
        $per = '';
        if( isset($instance['per']) ) {
            $per = $instance['per'];
        }
        $urls = '';
        if( isset($instance['url']) ) {
            $url = $instance['url'];
        }
        $button_name = '';
        if( isset($instance['button_name']) ) {
            $button_name = $instance['button_name'];
        }
        echo'<section section-concept="true" class="price-section">';
            echo'<div class="price container">';
                echo '<div class="titles_concept">';
                    echo '<div class="titles_concept_1">';
                         echo'<span>'.$beffore_title.'</span>';
                        echo'<h2>'.$title.'</h2>';
                        echo'<p>'.$widget_description.'</p>';
                    echo '</div>';
                echo '</div>';
                echo'<div class="price-block-setup">';
                    echo'<div class="price-block">';
                        $phone = get_option('phone');
                        $args = [ 'post_type'=>'price','posts_per_page'=> $per];
                        foreach (get_posts($args) as $i => $post) {
                            $image = get_the_post_thumbnail_url( $post->ID );
                            $title = $post->post_title;
                            $feature = get_post_meta($post->ID,'feature',1);
                            $price_text = get_post_meta($post->ID,'price_text',1);
                            $btn_title = get_post_meta($post->ID,'btn_title',1);
                            $link = get_the_permalink($post->ID);
                            $content = wp_trim_words($post->post_content, 7, '...');
                            $services_text = get_post_meta($post->ID,'services_text',1);
                            $offer = get_post_meta($post->ID,'offer',1);
                            $img_price = get_post_meta($post->ID,'img_price',1);
                            echo'<div class="box-price '.( ($feature == 'on') ? ' featuer' : '' ).'">';
                                    if (!empty($offer)) {
                                        echo'<em>'.$offer.'</em>';
                                    }
                                echo'<div class="price-block-offer">';
                                    echo'<p>'.$post->post_title.'</p>';
                                    
                                    echo'<h3>'.$price_text.'</h3>';
                                echo '</div>';
                                echo '<div class="content-price">'.$content.'</div>';
                                if(!empty($services_text)){
                                    echo '<div class ="list_services_price">';
                                        echo'<ul>';
                                            foreach ($services_text as $p ) {
                                                echo'<li><i class="fa-regular fa-circle-check"></i>'.$p['service_info'].'</li>';
                                            }
                                        echo'</ul>';
                                    echo '</div>';
                                }
                                
                                echo '<div class ="links_price">';
                                    echo'<a class="price_Alniks" href="tel:'.$phone.'">طلب الخدمة</a>';
                                echo '</div>';
                            echo'</div>';
                        }
                    echo'</div>';
                echo'</div>';
                if( !empty(trim($urls)) ) {
                    echo '<div class="ButtonConcept"><a href="'.$urls.'">'.$button_name.'<i class="fa-solid fa-chevron-left"></i></a></div>';
                } 
            echo'</div>';
        echo'</section>';
    }
    public function form( $instance ) {
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "services",
                "id"    => $this->get_field_id('services'),
                "name"  => $this->get_field_name('services'),
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
                        "title"  => "عدد  خطط الاسعار",
                        "type"  => "text",
                        "id"    => $this->get_field_id("per"),
                        "singleID" => "per",
                        "name"    => $this->get_field_name("per"),
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
        return $instance;
    }
}
function Theme__Widgets__price() {
    register_widget( 'Theme__WidgetModel__price' );
}
add_action( 'widgets_init', 'Theme__Widgets__price' );