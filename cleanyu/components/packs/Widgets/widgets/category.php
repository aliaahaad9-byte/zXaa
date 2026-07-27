<?php
class Theme__WidgetModel__category_shap extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__category_shap',
        );
        parent::__construct( 'category_shap', '[Widget] تصنيفات', $widget_ops );
        $this->ThemeStatic = new ThemeStatic;
    }
    public function widget( $args, $instance ) {
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
        
       
         $TermsArgs = array(
            'taxonomy' => 'category',
            'hide_empty' => true,
            'number' => $number_services,
            
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
        $imagepin = get_option( 'imagepin' )['url'];
        echo'<section section-concept="true" class="bh_category_shap" data-loader-style="--imgbk:url('.get_template_directory_uri().'/components/styles/img/bg-services.png)">';
            echo'<div class="container">';
                echo '<div class="titles_concept">';
                    echo '<div class="titles_concept_1">';
                        
                        echo'<h2>'.$category_shap.'</h2>';
                        echo'<p>'.$category_shap_content.'</p>';
                    echo '</div>';
                    
                echo '</div>';
               
                echo'<div class="category_panner-mobile">';
                    $imagepin = get_option('imagepin')['url'];
                    foreach( $terms as $category ) {
                        $content = wp_trim_words($category->description, 15, '...');
                        $icon = get_term_meta($category->term_id, "icon", true);
                        if( empty($icon) ) {
                            $icon = '<i aria-hidden="true" class="fas fa-grip-horizontal"></i>';
                        }
                        $image = get_term_meta($category->term_id, "imgcat", true);
                        if( empty($image) ) {
                            $image = $imagepin;
                        }
                        $Service_price = get_term_meta($category->term_id, "Service_price", true);
                        echo '<div class= "-category-boxed">';
                            echo '<div class= "-category-image">';
                                echo'<a href="'.get_term_link($category).'" title="'.$category->name.'">';
                                    echo '<img width="100%" height="100%" alt="'.$category->name.'" data-loader-src="'.$image.'"/>';
                                   
                                echo '</a>';
                            echo '</div>';
                            echo '<div class= "cat_title_boxed">';
                                echo'<a href="'.get_term_link($category).'" title="'.$category->name.'">';
                                   echo'<h3>'.$category->name.'</h3>';
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
        $cats = array();
        foreach( get_terms(array("taxonomy"=>'category', "hide_empty"=>0, "parent"=>0)) as $t ) {
            $cats[$t->term_id] = $t->name;
            $parents = get_terms(array("taxonomy"=>'category', "hide_empty"=>0, "parent"=>$t->term_id));
            foreach( $parents as $p ) {
                $cats[$p->term_id] = '— '.$p->name;
            }
        }
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "category_shap",
                "id"    => $this->get_field_id('category_shap'),
                "name"  => $this->get_field_name('category_shap'),
                "stack" => array(
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
                        "title"  => "عدد  التصنيفات  ",
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
function Theme__Widgets__category_shap() {
    register_widget( 'Theme__WidgetModel__category_shap' );
}
add_action( 'widgets_init', 'Theme__Widgets__category_shap' );