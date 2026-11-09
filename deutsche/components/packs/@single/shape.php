<?php
wp_reset_query();
global $post;
$path = $CurrentDir . $post->post_type . '.php';
if ( ! file_exists( $path ) ) { $path = $CurrentDir . 'post.php'; }
require $path;
