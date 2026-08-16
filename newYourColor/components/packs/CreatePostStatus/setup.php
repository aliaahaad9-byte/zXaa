<?php 
 function my_custom_status_creation(){
        register_post_status( 'readypublish', array(
            'label'                     => _x( 'جاهز للنشر ', 'post' ),
            'label_count'               => _n_noop( 'جاهز للنشر  <span class="count">(%s)</span>', 'جاهز للنشر  <span class="count">(%s)</span>'),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true
        ));
    }
    add_action( 'init', 'my_custom_status_creation' );

    function my_custom_status_add_in_quick_edit() {
        global $post;
        echo "<script>
        jQuery(document).ready( function() {
            jQuery( 'select[name=\"_status\"]' ).append( '<option ".(($post->post_status == 'readypublish') ? "selected=\"selected\"" : '')." value=\"readypublish\">جاهز للنشر </option>' );   
            ".(($post->post_status == 'readypublish') ? "jQuery( '#post-status-display' ).html('جاهز للنشر ');" : "")."
            ".(($post->post_status == 'readypublish') ? "jQuery( 'select[name=\"_status\"]' ).val('readypublish').change();" : "")."
        }); 
        </script>";
    }
    add_action('admin_footer-edit.php','my_custom_status_add_in_quick_edit');

    function my_custom_status_add_in_post_page() {
        global $post;
        echo "<script>
        jQuery(document).ready( function() {        
            jQuery( 'select[name=\"post_status\"]' ).append( '<option ".(($post->post_status == 'readypublish') ? "selected=\"selected\"" : '')." value=\"readypublish\">جاهز للنشر </option>' );
            ".(($post->post_status == 'readypublish') ? "jQuery( '#post-status-display' ).html('جاهز للنشر ');" : "")."
            ".(($post->post_status == 'readypublish') ? "jQuery( 'select[name=\"_status\"]' ).val('readypublish').change();" : "")."
        });
        </script>";
    }
    add_action('admin_footer-post.php', 'my_custom_status_add_in_post_page');
    add_action('admin_footer-post-new.php', 'my_custom_status_add_in_post_page');