<?php

/*
    Dev: YourColor Dev team;
    Function_Name: IsSpeed;
    Function_Role: Check IF User Agent IS Google Speed;
    Return: true (if the user agent is Google )/ false ;
    default : false ;
*/
function IsSpeed() {
    $IsSpeed = false;
    $user_agent = ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if (strpos(($_SERVER['HTTP_USER_AGENT'] ?? ''), 'Lighthouse') !== false || strpos(($_SERVER['HTTP_USER_AGENT'] ?? ''), 'moto g') !== false) {
        $IsSpeed =  true;
    }
    return $IsSpeed;
}



 function YC_get_attachment($args) {

    /*
        'id'=>'ATTACHMENT ID',
        'alt'=>'ATTACHMENT CUSTOM ALT',
        'size'=>'ATTACHMENT SIZE NAME',
        'return__output'=>true || false
    */

    extract( $args );

    if( !isset( $id ) ) return false;
    if( !isset( $return_output ) ) $return_output = true;

    $imageAttributes = array(
        'class' => 'MaroonImg'
    );

    $attch_data  = wp_get_attachment_metadata($id);
    if( empty( $attch_data ) ) return false;
    $current__size = ( ( isset( $attch_data['sizes'][$size] ) ) ) ? $attch_data['sizes'][$size] : $attch_data;
    $imageAttributes['width'] = $current__size['width'];
    $imageAttributes['height'] = $current__size['height'];
    if(isset($size)){
        
        $src = wp_get_attachment_image_src($id,$size)[0];
    }else{
        $src = wp_get_upload_dir()['baseurl'].'/'.$current__size['file'];
    }
    if(!isset($lazyload)){
        $lazyload = true ;
    }
    if($lazyload){
        $imageAttributes['data-loader-src'] =$src ;
    }else{
        $imageAttributes['src'] =$src ;
    }
    $imageAttributes['alt'] = isset($alt) ? $alt : (isset($attch_data['image_meta']['alt']) ? $attch_data['image_meta']['alt'] : '');


    if( $return_output == false ) return $imageAttributes;

    $imageTag = '<img ';
    foreach ($imageAttributes as $attribute => $value) {
        $imageTag .= $attribute . '="' . $value . '" ';
    }
    $imageTag .= '/>';
    
    return $imageTag;
}

