<?php
/**
 * المسار القديم /AjaxCenter/callupdate — يبقى للتوافق فقط.
 * المنطق كله في components/packs/callnumber/tracker.php،
 * والمسار المعتمد الآن هو admin-ajax.php لأنه لا يعتمد على الروابط الدائمة.
 */

header( 'Content-Type: application/json; charset=utf-8' );

if ( function_exists( 'yc_track_record' ) ) {
    echo wp_json_encode( yc_track_record( true ) );
} else {
    echo wp_json_encode( array( 'saved' => false, 'reason' => 'tracker-missing' ) );
}
die();
