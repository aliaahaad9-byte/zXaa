<?php

echo '<div class="social--footer">';
	if (!empty(get_option('facebook'))){
		echo '<a aria-label="facebook" target="_blank" rel="noreferrer noopener" href="'.get_option('facebook').'" class="facebook"><i class="fab fa-facebook"></i></a>';
	}
	if (!empty(get_option('twitter'))){
		echo '<a aria-label="twitter" target="_blank" rel="noreferrer noopener" href="'.get_option('twitter').'" class="twitter"><i class="fab fa-twitter"></i></a>';
	}
	if (!empty(get_option('youtube'))){
		echo '<a aria-label="youtube" target="_blank" rel="noreferrer noopener"  href="'.get_option('youtube').'" class="youtube"><i class="fab fa-youtube"></i></a>';
	}
	if (!empty(get_option('linkedin'))){
		echo '<a aria-label="linkedin" target="_blank" rel="noreferrer noopener"  href="'.get_option('linkedin').'" class="linkedin"><i class="fab fa-linkedin"></i></a>';
	}
	if (!empty(get_option('telegram'))){
		echo '<a aria-label="telegram" target="_blank" rel="noreferrer noopener" href="'.get_option('telegram').'" class="telegram"><i class="fab fa-telegram"></i></a>';
	}
echo '</div>';
