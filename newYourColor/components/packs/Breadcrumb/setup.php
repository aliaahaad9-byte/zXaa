<?php
wp_reset_query();
wp_reset_postdata();
function Breadcrumb() {
	$position = 1;
	echo '<ol itemscope itemtype="http://schema.org/BreadcrumbList" class="BreadcrumbsFilters">';
	  echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	  	echo '<i class="fa fa-home-lg"></i>';
	    echo '<a class="unline" itemprop="item" href="'.home_url().'">';
	    echo '<span itemprop="name">الرئيسية</span></a>';
	    echo '<meta itemprop="position" content="'.$position.'" />';
	  echo '</li>';
	  echo '<li class="none_after"><i class="fa-solid fa-arrow-left-long"></i></li>';
	  if( is_page() ) {
	    global $post;
	    if( $post->post_parent == 0 ) {
	      $position++;
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_the_permalink($post->ID).'"><span itemprop="name">'.$post->post_title.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	    }else {
	      $position++;
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_the_permalink($post->post_parent).'"><span itemprop="name">'.get_the_title($post->post_parent).'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	      $position++;
	      echo '<li class="none_after"><i class="fa-solid fa-arrow-left-long"></i></li>';
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_the_permalink($post->ID).'"><span itemprop="name">'.$post->post_title.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	    }
	  }else if( is_single() ) {
	    global $post;
	    $category = (is_array(get_the_terms($post->ID, 'category', ''))) ? get_the_terms($post->ID, 'category', '') : array();
	    foreach ($category as $cat) {
	      $position++;
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($cat).'"><span itemprop="name">'.$cat->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	      echo '<li class="none_after"><i class="fa-solid fa-arrow-left-long"></i></li>';
	    }
	    $position++;
	    echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	      echo '<span itemprop="name">'.$post->post_title.'</span>';
	      echo '<meta itemprop="position" content="'.$position.'" />';
	    echo '</li>';
	  }else if( is_search() ) {
	    $SearchQuery = get_search_query();
	    $position++;
	    echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	      echo '<a class="unline" itemprop="item" href="'.home_url('/search/'.str_replace(' ', '+', $SearchQuery)).'">';
	      echo '<span itemprop="name">نتائج البحث عن : '.$SearchQuery.'</span></a>';
	      echo '<meta itemprop="position" content="'.$position.'" />';
	    echo '</li>';
	  }else if( is_author() ) {
	    $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
	    $position++;
	    echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	      echo '<a class="unline" itemprop="item" href="'.get_author_posts_url($curauth->ID).'">';
	      echo '<span itemprop="name">'.$curauth->display_name.'</span></a>';
	      echo '<meta itemprop="position" content="'.$position.'" />';
	    echo '</li>';
	  }else if( is_category() ) {
	    $obj = get_queried_object();
	    $parentID = $obj->parent;
	    if( $parentID == 0 ) {
	      $position++;
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($obj).'"><span itemprop="name">'.$obj->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	    }else {
	      $position++;
	      $parent = get_term($parentID, $obj->taxonomy);
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($parent).'"><span itemprop="name">'.$parent->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	      $position++;
	      echo '<li class="none_after"><i class="fa-solid fa-arrow-left-long"></i></li>';
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($obj).'"><span itemprop="name">'.$obj->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	    }
	  }else if( is_tag() or is_tax() ) {
	    $obj = get_queried_object();
	    $parentID = 0;
	    if( $obj->parent > 0 ) {
	      $parentID = $obj->parent;
	    }
	    if( $parentID == 0 ) {
	      $position++;
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($obj).'"><span itemprop="name">'.$obj->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	    }else {
	      $position++;
	      $parent = get_term($parentID, $obj->taxonomy);
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($parent).'"><span itemprop="name">'.$parent->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	      $position++;
	      echo '<li class="none_after"><i class="fa-solid fa-arrow-left-long"></i></li>';
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($obj).'"><span itemprop="name">'.$obj->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	    }
	  }else if($CityServices = get_query_var(get_option('services_url'))){
	    if( $term = (new CityServices)->GetCurrentTerm($CityServices) ) {
	      $position++;
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($term['city']).'"><span itemprop="name">'.$term['city']->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	      echo '<li class="none_after"><i class="fa-solid fa-arrow-left-long"></i></li>';
	      $position++;
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.get_term_link($term['category']).'"><span itemprop="name">'.$term['category']->name.'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	      echo '<li class="none_after"><i class="fa-solid fa-arrow-left-long"></i></li>';
	      $position++;
	      echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	        echo '<a class="unline" itemprop="item" href="'.(new CityServices)->ServiceQVar($term['category'], $term['city']).'"><span itemprop="name">'.(new CityServices)->ServiceQVar_title($term['category'], $term['city']).'</span></a>';
	        echo '<meta itemprop="position" content="'.$position.'" />';
	      echo '</li>';
	    }
	  }
	  if( (new ThemeStatic)->Paged() > 1 ) {
	    $position++;
	    echo '<li class="none_after"><i class="fa-solid fa-arrow-left-long"></i></li>';
	    echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	      echo '<a class="unline" itemprop="item" href="'.(new ThemeStatic)->GetCurrentURL().'">';
	      echo '<span itemprop="name">صفحة '.(new ThemeStatic)->Paged().'</span></a>';
	      echo '<meta itemprop="position" content="'.$position.'" />';
	    echo '</li>';
	  }
	echo '</ol>';
}