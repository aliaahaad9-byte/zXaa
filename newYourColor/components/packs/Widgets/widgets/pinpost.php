<?
class Theme__WidgetModel__pinpost extends WP_Widget {
    function __construct() {
        $widget_ops = array( 
            'classname' => 'theme__pinpost',
        );
        $sitename = get_option('sitename');
        parent::__construct( 'pinpost', ' [ Widgets-[ '.$sitename.' ] ] مقالات مثبته', $widget_ops );
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
        $trending = 4;
        if( isset($instance['trending']) ) {
            $trending = $instance['trending'];
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
            "posts_per_page"=> $number,
            'meta_query'=>array(
                'relation' => 'and',
                    array(
                        'key'     => 'pin',
                        'value'   => 'on',
                        'compare' => '='
                    ),
                )
        );
        $UniqId = uniqid();
        #
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

        $argument = array(
            "post_type" => 'post',
            "posts_per_page"=>$trending,
        );
        $argument['meta_key'] = 'trending';
        $argument['orderby'] = 'meta_value_num';
        
        $date = true;
       
            echo '<div class="intro-page -model-'.$type.'">';
                echo '<div class="container">';
                    echo '<div class="titles_concept">';
                        echo '<div class="titles_concept_1">';
                            echo'<span>'.$beffore_title.'</span>';
                            echo'<h2>'.$title.'</h2>';
                            echo'<p>'.$widget_description.'</p>';
                        echo '</div>';
                        
                    echo '</div>';
                    echo '<div class="-section-box">';
                        echo '<div class="-section-">';
                            $i=0;
                            echo '<div class="post-intro-slider-">';
                                foreach( get_posts($arguments) as $post ) { 
                                    $i++;
                                    $this->ThemeStatic->Part("Griditem", array("post"=>$post,'model'=>1));
                            
                                }
                            echo'</div>';
                        echo '</div>';
                       
                        
                        /*echo '<div class="-post-box">';
                            echo '<div class="-Posts-intro">';
                                echo '<h2><span><i class="fa-solid fa-fire"></i></span>مقالات رائجة</h2>';
                                
                            echo '</div>';
                            echo '<div class="-Posts-intro-box">';
                                foreach( get_posts($argument) as $post ) {
                                    $this->ThemeStatic->Part("Griditem", array("post"=>$post,'model'=>3));
                                }

                                
                            echo '</div>';
                        echo '</div>';*/
                    echo '</div>';
                echo '</div>';

            echo '</div>';
        

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
        #
        WidgetInput(
            array(
                "instance" => $instance,
                "title"  => "خصائص",
                "singleID" => "pinpost",
                "id"    => $this->get_field_id('pinpost'),
                "name"  => $this->get_field_name('pinpost'),
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
                        "title"  => "عدد المقالات  ",
                        "type"  => "text",
                        "id"    => $this->get_field_id("number"),
                        "singleID" => "number",
                        "name"    => $this->get_field_name("number"),
                    ),
                    
                    array(
                        "title"  => "التصنيف",
                        "type"  => "select",
                        "id"    => $this->get_field_id("category"),
                        "singleID" => "category",
                        "name"    => $this->get_field_name("category"),
                        "options"   => $cats
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
function Theme__Widgets__pinpost() {
    register_widget( 'Theme__WidgetModel__pinpost' );
}
add_action( 'widgets_init', 'Theme__Widgets__pinpost' );