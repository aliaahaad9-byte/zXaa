<?php
ob_start();
header("Content-Type: application/json");

$json = array();
$page_name = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : '';
$calldate = date("Y-m-d H:i:s");

// نوع الاتصال : اتصال هاتفي عادي أو واتساب
$calltype = isset($_POST['calltype']) ? strtolower(trim($_POST['calltype'])) : '';
if( !in_array($calltype, array('phone','whatsapp'), true) ) {
    $calltype = 'phone';
}

$new_post = array(
    'post_title'   => '--',
    'post_type'    => 'callwebsite',
    'post_status'  => 'publish',
);

$post_id = wp_insert_post($new_post);

update_post_meta($post_id, 'calldate', $calldate);
update_post_meta($post_id, 'page', $page_name);
update_post_meta($post_id, 'calltype', $calltype);

$retrieved_calldate = get_post_meta($post_id, 'calldate', true);
$retrieved_page = get_post_meta($post_id, 'page', true);
$retrieved_calltype = get_post_meta($post_id, 'calltype', true);

$output = ob_get_clean();

$json['output'] = $output;
$json['calldate'] = $retrieved_calldate;
$json['page'] =  $retrieved_page;
$json['calltype'] = $retrieved_calltype;


echo json_encode($json);
