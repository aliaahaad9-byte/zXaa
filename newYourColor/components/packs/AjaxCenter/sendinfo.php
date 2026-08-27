<?php
header("Content-Type: application/json");
    ob_start();
	$json = array();
	$name = $_POST['names'];
      $my_post = array(
      'post_title'    => wp_strip_all_tags( $_POST['names'] ),
      'post_content'  => '',
      'post_status'   => 'publish',
      'post_type'    => 'clint',
    );
	$post_id = wp_insert_post($my_post);
	update_post_meta($post_id,'email',$_POST['email']);
	update_post_meta($post_id,'phone',$_POST['phone']);
	update_post_meta($post_id,'message',$_POST['message']);
	update_post_meta($post_id,'address',$_POST['address']);
	echo '<div class="success" data-close="true">';
		echo '<span data-closeclick="true" id="Close" class="hoverable activable"><i class="fal fa-times"></i></span>';
        echo '<p>تم اضافة طلبك بنجاح </p>';
      echo '</div>';

$output = ob_get_clean();
$json['output'] = $output;
echo json_encode($json);