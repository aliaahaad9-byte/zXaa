<?php
$obj = get_queried_object();
if ( $obj && isset( $obj->term_id ) ) {
	update_term_meta( $obj->term_id, 'views', (int) get_term_meta( $obj->term_id, 'views', true ) + 1 );
}
require $CurrentDir . 'default.php';
