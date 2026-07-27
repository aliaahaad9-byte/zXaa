<?php
class Theme__WidgetModel__postsmodel extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__postitem',
        );
        parent::__construct( 'postsmodel', ' [ Widgets-[ yourcolor ] ] ودجيت المقالات', $widget_ops );
        $this->ThemeStatic = new ThemeStatic;
    }
    public function widget( $args, $instance ) {
        $title = '';
        if( isset($instance['title']) ) {
            $title = $instance['title'];
        }
        $widget_description = '';
        if( isset($instance['widget_description']) ) {
            $widget_description = $instance['widget_description'];
        }
        $type = '';
        if( isset($instance['type']) ) {
            $type = $instance['type'];
        }
        $show_tabs = '';
        if( isset($instance['show_tabs']) ) {
            $show_tabs = $instance['show_tabs'];
        }
        $custom_category = false;
        if( isset($instance['category']) ) {
            $custom_category = $instance['category'];
        }
        $number_posts = '';
        if( isset($instance['number_posts']) ) {
            $number_posts = $instance['number_posts'];
        }
        $orderby = '';
        if( isset($instance['orderby']) ) {
            $orderby = $instance['orderby'];
        }
        $arguments = array(
          "post_type"     => 'post',
          "posts_per_page"=> $number_posts,
          
        );
        if( $orderby == 'trending' ) {
            $arguments['meta_key'] = 'trending';
            $arguments['orderby'] = 'meta_value_num';
        }else if( $orderby == 'rand' ) {
            $arguments['orderby'] = 'rand';
        }else if( $orderby == 'old' ) {
            $arguments['order'] = 'ASC';
        }

        if( $custom_category != false and $custom_category != 'all' ) {
            $arguments['cat'] = $custom_category;
        }
        $k = [];
        $Category = false;
        $color = false;
        $icon = '';
        $termIDs = array();
        $termTerms = array();
        if($custom_category != false){
            $Category = get_term_by('id',$custom_category,'category');
            $termTerms[] = $Category;
            $termIDs[] = $Category->term_id;
            if($Category->parent > 0){
                $icon = get_term_meta($Category->parent,'icon',true);
                $color = get_term_meta($Category->parent, 'color', true);
            }else{
                $icon = get_term_meta($Category->term_id,'icon',true);
                $color = get_term_meta($Category->term_id, 'color', true);
            }         
        }
        
        if($color == false) $color = 'var(--uicolor)';
        if(empty($icon)) $icon = '<i class="fa fa-earth-america"></i>';
        $urls = '';
        if( isset($instance['urls']) ) {
            $urls = $instance['urls'];
        }
        $button_name = '';
        if( isset($instance['button_name']) ) {
            $button_name = $instance['button_name'];
        }
       
        $AjaxMore = false;
        $UniqId = uniqid();
        echo'<div section-concept="true" class="model-'.$type.'">';
            echo '<div class="container">';
                echo '<div class="model_shap_one_title">';
                    echo '<div class="titles_concept">';
                        echo '<div class="titles_concept_1">';
                            echo'<h2>'.$title.'</h2>';
                            echo'<p>'.$widget_description.'</p>';
                        echo '</div>';
                        
                    echo '</div>';
                    
                echo '</div>';
                $i=0;
                echo '<div class="post-model-'.$type.'">';
                    if( $type == 1 ) { 
                        echo  '<div class="postmodel model-'.$type.'">';
                            foreach( get_posts($arguments) as $posts ) {$i++;
                                $this->ThemeStatic->Part("Griditem", array("post"=>$posts,'model'=>2));
                            }
                        echo  '</div>';
                    }else if( $type == 2 ) { 
                        echo  '<div class="postmodel-model-'.$type.'">';
                            foreach( get_posts($arguments) as $post ) { 
                                $i++;
                                $this->ThemeStatic->Part("Griditem", array("post"=>$post,'model'=>1));
                            }
                        echo  '</div>';
                    }
                echo'</div>';           
            echo'</div>';
            if($Category != false){
                echo '<div class="ButtonConcept"><a href="'.get_term_link($Category).'"><span>المزيد من '.$Category->name.'<i class="fa-solid fa-chevron-left"></i></span></a></div>';
            }else {
                if( !empty(trim($urls)) ) {
                    echo '<div class="ButtonConcept"><a href="'.$urls.'">'.$button_name.'<i class="fa-solid fa-chevron-left"></i></a></div>';
                }      
            }
        echo '</div>';

        #end
    }
    public function form( $instance ) {
        $cats = array(
            "all"   => 'الكل'
        );
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
                "type"  => "خصائص",
                "singleID" => "postsmodel",
                "id"    => $this->get_field_id('postsmodel'),
                "name"  => $this->get_field_name('postsmodel'),
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
                        "title"  => "عدد المقالات  ",
                        "type"  => "text",
                        "id"    => $this->get_field_id("number_posts"),
                        "singleID" => "number_posts",
                        "name"    => $this->get_field_name("number_posts"),
                    ),
                    array(
                        "title"  => "التصنيف",
                        "type"  => "select",
                        "id"    => $this->get_field_id("category"),
                        "singleID" => "category",
                        "name"    => $this->get_field_name("category"),
                        "options"   => $cats
                    ),
                     array(
                        "title"  => "ترتيب حسب",
                        "type"  => "select",
                        "id"    => $this->get_field_id("orderby"),
                        "singleID" => "orderby",
                        "name"    => $this->get_field_name("orderby"),
                        "options"   => array(
                            "trending"  => "اخبار ترند",
                            "date"  => "احدث الاخبار",
                            "rand"  => "مقالات عشوائية",
                            "old"  => "اقدم الاخبار",
                        )
                    ),
                    array(
                        "title"  => "الشكل",
                        "type"  => "select",
                        "id"    => $this->get_field_id("type"),
                        "singleID" => "type",
                        "name"    => $this->get_field_name("type"),
                        "options"   => array(
                            "1" => 'موديل 1',
                            "2" => 'موديل 2',
                        )
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
function Theme__Widgets__postsmodel() {
    register_widget( 'Theme__WidgetModel__postsmodel' );
}
add_action( 'widgets_init', 'Theme__Widgets__postsmodel' );