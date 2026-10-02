<?php
/*function wpse_11826_search_by_title( $search, $wp_query ) {
  if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
    global $wpdb;
    $q = $wp_query->query_vars;
    $n = ! empty( $q['exact'] ) ? '' : '%';
    $search = array();
    foreach( ( array ) $q['search_terms'] as $term )
    $search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
    if( ! is_user_logged_in() )
    $search[] = "$wpdb->posts.post_password = ''";
    $search = ' AND ' . implode( ' AND ', $search );
  }
  return $search;
}
add_filter( 'posts_search', 'wpse_11826_search_by_title', 10, 2 );*/
add_filter( 'posts_search', 'search_by_title_only', 10, 2 );
function search_by_title_only( $search, $wp_query ) {
    if ( !is_admin() || empty( $search ) ){
        return $search;
    }
    global $wpdb;
    $q = $wp_query->query_vars;
    $x = ! empty( $q['exact'] ) ? '' : '%';
    $search = '';
    $searchand = '';
    foreach ( (array) $q['search_terms'] as $term ) {
        $term = esc_sql( $wpdb->esc_like( $term ) );
        $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$x}{$term}{$x}')";
        $searchand = ' AND ';
    }
    if ( ! empty( $search ) ) {
        $search = " AND ({$search}) ";
    }
    return $search;
}