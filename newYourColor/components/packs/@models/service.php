<?php 
$title = get_the_title($post->ID);
$price_service = get_post_meta($post->ID,'price_service',1);
$content = wp_trim_words(get_the_content($post->ID), 100, '...');
$price_text = get_post_meta($post->ID,'price_text',1);
$image = get_the_post_thumbnail_url( $post->ID );
$adress = get_option('Adress');
$phone = get_option('Phone');
$link_adress = get_option('link_adress');
$link_twitter = get_option('link_twitter');
$link_facebook = get_option('link_facebook');
$link_instagram = get_option('link_instagram');
$link_linkedin = get_option('link_linkedin');
$link_telegram = get_option('link_telegram');
$link_youtube = get_option('link_youtube');
echo '<div class="seriver_model_bhaa">';
	echo'<div class="container">';
		echo'<div class="single-">';
			echo '<div class ="single-Breadcrumb">';
				echo'<breadcrumb>';
					Breadcrumb();
				echo'</breadcrumb>';
			echo '</div>';
			echo'<div class="titles-serive-model">';
	            echo'<h1>'.$title.'</h1>';
	            echo'<p>'.the_content().'</p>';
	            (new ThemeStatic)->Part("social");
	        echo '</div>';
		echo'</div>';
		echo'<div class="s-price-contact">';
			echo'<div class="title-contact">';
				echo'<div class="title-text">';
					echo'<h2><i class="fas fa-tools"></i>اطلب الخدمه</h2>';
					echo'<div class="price-num">';
						if (!empty($price_service)){
							echo'<span>'.$price_service.'</span>';
						}
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
					echo'<button type="submit" class="btn-brand hoverable activable"><i class="fal fa-external-link-square"></i>ارسل</button>';
				echo'</form>';
			echo'</div>';
		echo'</div>';
	echo'</div>';
	
	
echo'</div>';