<?
global $post;
wp_reset_query();

$UniqId = uniqid();


echo '<div class="-single-blog-box">';
   echo '<div class="container">';
      echo '<div class="-homepage--container">';
         echo '<div class="category-headline">';
            echo '<div class="-Breadcrumb-SingularPost">';
               Breadcrumb($post);
                echo '<h1><i class="fa fa-earth-america"></i>'.$post->post_title.'</h1>';
            echo '</div>';
            
         echo '</div>';
         echo '<div class="ArticleDetails">';
             the_content();
         echo '</div>';
         echo '<ul class="-Tabs-loadmore-List" data-menu="true">';
            echo '<li class="-Tabs-loadmore active" data-uniq="'.$UniqId.'" data-hometab="last_posts"><span>احدث المقالات</span></li>';
            echo '<li class="-Tabs-loadmore" data-uniq="'.$UniqId.'" data-hometab="trending"><span>المقالات الاكثر شيوعا</span></li>';
            echo '<li class="-Tabs-loadmore" data-uniq="'.$UniqId.'" data-hometab="rand"><span> مقالات عشوائي </span></li>';
         echo '</ul>';   
         echo '<div class="postgrid-boxed">';
            echo '<h2>احدث المقالات</h2>';

            $this->Part('Posts',array('AutoLoadmore'=>false,'UniqId'=>$UniqId,'AutoLoadmore'=>true));
         echo '</div>';
      echo '</div>';
      echo '<div class="-single-parent-post--sidebar">';
         dynamic_sidebar('posts-sidebar');
      echo '</div>';
   echo '</div>';
echo '</div>';

