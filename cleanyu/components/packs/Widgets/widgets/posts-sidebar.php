<?php
class Theme__WidgetModel__posts_sidebar extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__posts_sidebar',
        );
        $sitename = get_option('sitename');
        parent::__construct( 'intro_posts', ' [ Widgets-[ '.$sitename.' ] ] بلوك مقالات جانبية', $widget_ops );
        $this->ThemeStatic = new ThemeStatic;
    }
    public function SeparateTitle($string) {
        $string = str_replace('[b]', '<strong-color>', $string);
        $string = str_replace('[/b]', '</strong-color>', $string);
        return $string;
    }
    public function widget( $args, $instance ) {
        $title = '';
        if( isset($instance['title']) ) {
            $title = $instance['title'];
        }
        $content = '';
        if( isset($instance['content']) ) {
            $content = $instance['content'];
        }
        $type = '';
        if( isset($instance['type']) ) {
            $type = $instance['type'];
        }
        $number = 5;
        if( isset($instance['number']) ) {
            $number = $instance['number'];
        }
        $custom_category = false;
        if( isset($instance['category']) ) {
            $custom_category = $instance['category'];
        }
        $orderby = '';
        if( isset($instance['orderby']) ) {
            $orderby = $instance['orderby'];
        }
        $arguments = array(
            "post_type"     => 'post',
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
        $Category = false;
        
        if( $custom_category != false and $custom_category != 'all' ) {
            $arguments['cat']   = $custom_category;
        }

        if(empty($icon)) $icon = '<i class="fa fa-earth-america"></i>';
        #
        echo '<div class="-post-sidebar -model-'.$type.'">';
            echo '<div class="-sidebar-header">';
                echo '<div class ="sidebar-title">';
                    echo '<h3></span>'.$title.'</h3>';
                    echo '<p>'.$content.'</p>';
                echo '</div>';
            echo '</div>';
            echo '<div class="-posts-sidebar-body -model-'.$type.'">';
                if( $type == 1 ) {
                    
                        $i =0;
                        foreach( get_posts($arguments) as $post ) {$i++;
                            
                            $this->ThemeStatic->Part("Griditem", array("post"=>$post,"i"=>$i,"strlen"=>50,'model'=>3));
                            
                        }
                    
                }else if( $type == 2 ){
                    foreach( get_posts($arguments) as $post ) {
                       $this->ThemeStatic->Part("Griditem", array("post"=>$post,"strlen"=>50,'model'=>3));
                    }
                }
            echo '</div>';
        echo '</div>';
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
        #
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "posts_sidebar",
                "id"    => $this->get_field_id('posts_sidebar'),
                "name"  => $this->get_field_name('posts_sidebar'),
                "stack" => array(
                    array(
                        "title"  => "العنوان",
                        "type"  => "text",
                        "id"    => $this->get_field_id("title"),
                        "singleID" => "title",
                        "name"    => $this->get_field_name("title"),
                    ),
                    array(
                        "title"  => "نص",
                        "type"  => "textarea",
                        "id"    => $this->get_field_id("content"),
                        "singleID" => "content",
                        "name"    => $this->get_field_name("content"),
                    ),
                    array(
                        "title"  => "العدد",
                        "type"  => "text",
                        "id"    => $this->get_field_id("number"),
                        "singleID" => "number",
                        "name"    => $this->get_field_name("number"),
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
                        "title"  => "التصنيف",
                        "type"  => "select",
                        "id"    => $this->get_field_id("category"),
                        "singleID" => "category",
                        "name"    => $this->get_field_name("category"),
                        "options"   => $cats
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
        if(empty($instance['title']) ) {
            $instance['title'] = get_term($instance['category'], 'category')->name;
        }
       
        return $instance;
    }
}
function Theme__Widgets__posts_sidebar() {
    register_widget( 'Theme__WidgetModel__posts_sidebar' );
}
add_action( 'widgets_init', 'Theme__Widgets__posts_sidebar' );