<?php
class Theme__WidgetModel__postssorts extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__postssorts',
        );
        parent::__construct( 'postssorts', '[Widget] مقالات شورت ', $widget_ops );
        $this->ThemeStatic = new ThemeStatic;
    }
    public function widget( $args, $instance ) {
        $beffore_title = '';
        if( isset($instance['beffore_title']) ) {
            $beffore_title = $instance['beffore_title'];
        }
        $category_shap = '';
        if( isset($instance['category_shap']) ) {
            $category_shap = $instance['category_shap'];
        }
        
        $category_shap_content = '';
        if( isset($instance['category_shap_content']) ) {
            $category_shap_content = $instance['category_shap_content'];
        }      
        $per = '';
        if( isset($instance['per']) ) {
            $per = $instance['per'];
        }    
        $categoriees  = '';
        if( isset($instance['categoriees ']) ) {
            $categoriees  = $instance['categoriees '];
        }  
        $number_services = '';
        if( isset($instance['number_services']) ) {
            $number_services = $instance['number_services'];
        }
         $number_post = '';
        if( isset($instance['number_post']) ) {
            $number_post = $instance['number_post'];
        }
        $type = '';
        if( isset($instance['type']) ) {
            $type = $instance['type'];
        }
        
       
         

        $arguments = array(
          "post_type"     => 'post',
          "posts_per_page"=> $number_services,
            'meta_query'=>array(
                'relation' => 'and',
                    array(
                        'key'     => 'show_post',
                        'value'   => 'on',
                        'compare' => '='
                    ),
                )
          
        );
        $terms = get_terms($TermsArgs);
        
        $urls = '';
        if( isset($instance['urls']) ) {
            $urls = $instance['urls'];
        }
        $button_name = '';
        if( isset($instance['button_name']) ) {
            $button_name = $instance['button_name'];
        }
        $imagepin = get_option('imagepin')['url'];
        echo'<section section-concept="true" class="bh_category_shap" style="--imgbk:url('.get_template_directory_uri().'/components/styles/img/bg-services.png)">';
            echo'<div class="container">';
                echo '<div class="titles_concept">';
                    echo '<div class="titles_concept_1">';
                        echo'<span>'.$beffore_title.'</span>';
                        echo'<h2>'.$category_shap.'</h2>';
                        echo'<p>'.$category_shap_content.'</p>';
                    echo '</div>';
                    
                echo '</div>';
               
                echo'<div class="category_panner-mobile">';
                    $imagepin = get_option('imagepin')['url'];
                    foreach( get_posts($arguments) as $category ) {
                       $title = $category->post_title;
                        $image = get_post_meta($category->ID, "imgpostshort", true);
                        $titlepost = get_post_meta($category->ID, "titlepost", true);
                        if( empty($image) ) {
                            $image = $imagepin;
                        }
                        if( empty($titlepost) ) {
                            $titlepost = $title;
                        }
                        echo '<div class= "-category-boxed">';
                            echo '<div class= "-category-image">';
                                echo'<a href="'.get_the_permalink($category).'">';
                                    echo '<img width="100%" height="100%" alt="'.$category->name.'" src="'.$image.'"/>';
                                   
                                echo '</a>';
                            echo '</div>';
                            echo '<div class= "cat_title_boxed">';
                                echo'<a href="'.get_the_permalink($category).'">';
                                   echo'<h3>'.$titlepost.'</h3>';
                                echo '</a>';
                            echo '</div>';
                        echo '</div>';
                    }        
                echo '</div>';
            
            
                if(!empty($urls)){
                    echo'<div class="btn-services_1">';
                        if( !empty(trim($urls)) ) {
                            echo '<div class="ButtonConcept"><a href="'.$urls.'"><span>'.$button_name.'<i class="fa-solid fa-chevron-left"></i></span></a></div>';
                        }
                    echo'</div>';
                }
                
            echo'</div>';
        echo'</section>';
    }
    public function form( $instance ) {
        
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "postssorts",
                "id"    => $this->get_field_id('postssorts'),
                "name"  => $this->get_field_name('postssorts'),
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
                        "id"    => $this->get_field_id("category_shap"),
                        "singleID" => "category_shap",
                        "name"    => $this->get_field_name("category_shap"),
                    ),
                    
                    array(
                        "title"  => " العنوان الثاني",
                        "type"  => "textarea",
                        "id"    => $this->get_field_id("category_shap_content"),
                        "singleID" => "category_shap_content",
                        "name"    => $this->get_field_name("category_shap_content"),
                    ),
                    array(
                        "title"  => "عدد  المقالات  ",
                        "type"  => "text",
                        "id"    => $this->get_field_id("number_services"),
                        "singleID" => "number_services",
                        "name"    => $this->get_field_name("number_services"),
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
function Theme__Widgets__postssorts() {
    register_widget( 'Theme__WidgetModel__postssorts' );
}
add_action( 'widgets_init', 'Theme__Widgets__postssorts' );