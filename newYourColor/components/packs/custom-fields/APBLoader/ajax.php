<?php
add_action( 'wp_ajax_APBLayoutsBuilder', 'APBLayoutsBuilder' );

function APBLayoutsBuilder() {
	$APB = new APB();
	//
	(new APBFields)->AjaxFields($_POST['layout'], $_POST['fields'], $_POST['numb']);
	wp_die();
}
add_action( 'wp_ajax_APBAddLayoutBuilder', 'APBAddLayoutBuilder' );

function APBAddLayoutBuilder() {
	$APB = new APB();
	//
	(new APBFields)->APBAddLayoutBuilder($_POST['metabox'], $_POST['numb']);
	wp_die();
}
add_action( 'wp_ajax_APBAddGroupFields', 'APBAddGroupFields' );

function APBAddGroupFields() {
	$APB = new APB();
	//
	(new APBFields)->APBAddGroupFields($_POST['metabox'], $_POST['group'], $_POST['numb']);
	wp_die();
}
add_action( 'wp_ajax_pinned', 'pinned' );
function pinned() {
    $id = $_POST['id'];
    $pin = $_POST['pin'];
    if(get_post_meta($id,'pin',1)=='on'){
    	update_post_meta($id,'pin','');
    }else{
    	update_post_meta($id,'pin','on');
    }
    wp_die(); 
}
add_action( 'wp_ajax_removepost', 'removepost' );
function removepost() {
    header("Content-Type: application/json");
	ob_start();
	$json = array();
	if( isset( $_POST['removedID'] ) ){
		$RemoveList = array();
		if( strpos($_POST['removedID'],',') !== FALSE ){
			$RemoveList = explode(',', $_POST['removedID']);
		}else{
			$RemoveList[] = $_POST['removedID'];
		}
		foreach ($RemoveList as $post_id) {
			$post = get_post($post_id);
			if( isset( $post->ID ) ){
				wp_delete_post($post->ID);
				$json['type'] = 'sucsses';
			}		
		}

		if( isset( $Ajax__data['location'] ) && $Ajax__data['location'] != 'stay' ){
			$json['reload__page'] = $Ajax__data['location'];
		}


	}else{
		$json['type'] = 'error';
	}
	echo json_encode($json);
    wp_die(); 
}