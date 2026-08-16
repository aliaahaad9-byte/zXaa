<?
// Project
  $posts = get_posts($args);
   $q=0; 
  foreach ($posts as $post) {$q++;
  		$link = get_the_permalink($post->ID);
      $title = get_the_title($post->ID);
      $content = wp_trim_words(get_post($post)->post_content, 30, '...');
      echo'<div class="faq-section '.(($q == 1) ? 'active' : '').'" >';
         echo'<div class="head-faq-title" data-open-faq="true" data-title="true">';
            echo'<h2>'.$title.'</h2>';
            
            echo'<div class="icon_faq" data-context="true">';
               echo'<i aria-hidden="true" class="fas fa-plus"></i>';
               echo'<i aria-hidden="true" class="fas fa-minus"></i>';
            echo'</div>';
         echo'</div>';
         echo'<div class="answer">';
            echo'<p>'.get_post($post)->post_content.'</p>';
         echo'</div>';
      echo'</div>';

  }