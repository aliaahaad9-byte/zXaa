<?php 
$model = get_post_meta($post->ID, 'template', true);
$model = $this->packsPath.'@models/'.$model.'.php';
if( file_exists($model) ) {
	require($model);
}else {
	global $post;
	$image = get_the_post_thumbnail_url( $post->ID );
	$title = get_the_title($post->ID);
	$feature = get_post_meta($post->ID,'feature',1);
	$price_text = get_post_meta($post->ID,'price_text',1);
	$btn_title = get_post_meta($post->ID,'btn_title',1);
	$comment_num = get_comments_number($post->ID);
	$time = 'منذ  ' . human_time_diff( get_the_time('U',$post->ID), current_time('timestamp') );
	$auther = get_user_by ('ID',$post->post_author)->display_name;
	$content = wp_trim_words(get_the_content($post->ID), 100, '...');
	$services_text = get_post_meta($post->ID,'services_text',1);
	$offer = get_post_meta($post->ID,'offer',1);
echo '<div class="single-post">';
    echo '<div class="container">';
		echo'<div class="single-price">';
			echo'<div class="s-price-content">';
				echo'<breadcrumb>';
			        Breadcrumb($post);
			    echo'</breadcrumb>';
				echo '<div class="s-price-content1">';
					echo'<h1>'.$title.'</h1>';
					echo'<p>'.$content.'</p>';
				echo '</div>';
				echo '<ul class="services_text">';
					foreach ($services_text as $s) {
						echo'<li><i class="fas fa-check"></i>'.$s['service_info'].'</li>';
					}
				echo'</ul>';
			echo'</div>';
			echo'<div class="price-contact-left">';
				echo'<div class="price-contact">';
					echo'<div class="title-contact">';
						echo'<h3><i class="fas fa-tools"></i>اطلب الخدمه</h3>';
						echo'<div class="price-num">';
							echo'<span>'.$price_text.'</span>';
						echo'</div>';
					echo'</div>';
					echo'<form method="GET">';
						echo'<div class="line">';
							echo'<input type="text" name="name" placeholder="الأسم بالكامل">';
							echo'<input type="text" name="email" placeholder="البريد الالكتروني">';
							echo'<input type="text" name="phone" placeholder="رقم  الهاتف">';
							echo'<input type="text" name="adress" placeholder="العنوان">';
							echo'<textarea placeholder="التفاصيل"></textarea>';
						echo'</div>';
						echo'<button type="submit" class="btn-brand hoverable activable">ارسال البيانات</button>';
					echo'</form>';
				echo'</div>';
			echo'</div>';
		echo'</div>';
	echo'</div>';
echo'</section>';

}