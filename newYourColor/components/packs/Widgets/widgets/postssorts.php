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
        $clients_list = '';
        if( isset($instance['clients_list']) ) {
            $clients_list = $instance['clients_list'];
        }
        $title_clients = '';
        if( isset($instance['title_clients']) ) {
            $title_clients = $instance['title_clients'];
        }
        $imagepin = get_option('imagepin')['url'];
        echo'<section section-concept="true" class="YC-list-category">';
            echo'<div class="container">';
                echo '<div class="info_POST-List-">';
                    echo '<div class="titles_POST-List-">';
                        echo'<h2>'.$category_shap.'</h2>';
                    echo '</div>';
                    echo'<p>'.$category_shap_content.'</p>';
                    
                echo '</div>';
               
                echo'<div class="POST-List-mobile">';
                    $imagepin = get_option('imagepin')['url'];
                    foreach( $clients_list as $category ) {
                       
                        echo '<div class= "POST-List-category-boxed">';
                            if (isset($category['is__clients']) && $category['is__clients'] == 'on') {
                                echo'<em>'.$category['title_clients'].'</em>';
                            }
                            echo '<div class= "-POST-List-image-boxed">';
                                echo '<div class= "-POST-List-image">';
                                    if(!empty($category['link'])){
                                        echo'<a href="'.$category['link'].'">';
                                            if(IsSpeed() == false){
                                                echo '<img width="100%" height="100%" alt="'.$category['title'].'" src="'.$category['images']['url'].'"/>';   
                                            }
                                        echo '</a>';
                                    }else {

                                        echo '<img width="100%" height="100%" alt="'.$category['title'].'" src="'.$category['images']['url'].'"/>';   
                                    }
                                echo '</div>';
                                echo '<div class="POST-List-title-boxed">';
                                     if(!empty($category['link'])){
                                        echo'<a href="'.$category['link'].'">';
                                           echo'<h3>'.$category['title'].'</h3>';
                                        echo '</a>';
                                    }else {
                                        echo'<h3>'.$category['title'].'</h3>';

                                    }
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    }        
                echo '</div>';
            
            
                
                
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
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "clients_list",
                "id"    => $this->get_field_id('clients_list'),
                "name"  => $this->get_field_name('clients_list'),
                "stack" => array(
                    array(
                        "title"  => "العنوان",
                        "type"  => "text",
                        "id"    => $this->get_field_id("title"),
                        "singleID" => "title",
                        "name"    => $this->get_field_name("title"),
                    ),
                    
                    array(
                        "title"  => "link",
                        "type"  => "text",
                        "id"    => $this->get_field_id("link"),
                        "singleID" => "link",
                        "name"    => $this->get_field_name("link"),
                    ),
                    array(
                        "title"  => "الايقونة",
                        "type"  => "file",
                        "id"    => $this->get_field_id("images"),
                        "singleID" => "images",
                        "name"    => $this->get_field_name("images"),
                    ),
                    array(
                        "title"  => "اظهار الخصم",
                        "type"  => "checkbox",
                        "id"    => $this->get_field_id("is__clients"),
                        "singleID" => "is__clients",
                        "name"    => $this->get_field_name("is__clients"),
                    ),
                    array(
                        "title"  => "عنوان الخصم",
                        "type"  => "text",
                        "id"    => $this->get_field_id("title_clients"),
                        "singleID" => "title_clients",
                        "name"    => $this->get_field_name("title_clients"),
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
function Theme__Widgets__postssorts() {
    register_widget( 'Theme__WidgetModel__postssorts' );
}
add_action( 'widgets_init', 'Theme__Widgets__postssorts' );