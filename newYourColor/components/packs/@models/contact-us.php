<?php 

	$title = get_the_title($post->ID);

	$price_service = get_post_meta($post->ID,'price_service',1);

	$content = wp_trim_words(get_the_content($post->ID), 100, '...');

	$price_text = get_post_meta($post->ID,'price_text',1);

	$image = get_the_post_thumbnail_url( $post->ID );

	$adress = get_option('Adress');

	$Phone = get_option('Phone');

	$email = get_option('email');

	echo '<div class="seriver_model_bhaa">';

		echo'<div class="container">';

			echo'<div class="single-">';

				echo '<div class ="single-Breadcrumb">';

					echo'<breadcrumb>';

						Breadcrumb();

					echo'</breadcrumb>';

		        	echo'<h1>'.$title.'</h1>';

				echo '</div>';

			

				echo '<div class="titles-serive-model ArticleDetails">';

		            echo'<p>'.the_content().'</p>';

	        	echo '</div>';

	            echo'<div class="contact-info">';

	    			echo'<ul class="block-cotact">';

	    				echo'<li>

	    					<a href="tel:'.$Phone.'">

	    						<i class="fas fa-phone-alt"></i>

	    						<div class="dt-contact">

		    						<span>رقم الهاتف </span>

		    						<p>'.$Phone.'</p>

	    						</div>

	    					</a>

	    				</li>';

	    				echo'<li>

	    					<div class="adress-contact">

    						<i class="fal fa-map-marker-alt"></i>

    						<div class="dt-contact">

	    						<span>العنوان</span>

	    						<p>'.$adress.'</p>

    						</div>

	    					

	    				</li>';

	    			

	    			echo'</ul>';

	    		echo'</div>';

				

			echo'</div>';

			echo'<div class="s-price-contact">';

				echo'<div class="title-text">';

					echo'<span><i class="fas fa-tools"></i></span>';

					$titleform = get_option('titleform');

					if (empty($titleform)) {

						$titleform ='طلب الخدمة';

					}

					echo'<h3>'.$titleform.'</h3>';

					

				echo'</div>';

				echo'<div class="form-contact ">';

					echo'<form method="Post">';

						echo'<div class="line">';

							echo'<input type="text" name="names" required placeholder="الأسم بالكامل">';

    						echo'<input type="email" name="email" placeholder="البريد الالكتروني">';

    						echo'<input type="number" name="phone" placeholder="رقم  الهاتف">';

    						echo'<input type="text" name="address" placeholder="العنوان">';

    						echo'<textarea name="message" placeholder="ارسال التفاصيل"></textarea>';

						echo'</div>';

						echo'<button type="submit" id="savepost" class="btn-brand hoverable activable">رسال البيانات</button>';

					echo'</form>';

				echo'</div>';

			echo'</div>';

		echo'</div>';

		

	echo'</div>';