<?php

echo '<div class="social--footer">';
	if (!empty(get_option('facebook'))){
		echo '<a aria-label="facebook" target="_blank" rel="noreferrer noopener" href="'.get_option('facebook').'" class="facebook"><i class="fab fa-facebook"></i></a>';
	}
	if (!empty(get_option('twitter'))){
		echo '<a aria-label="X" target="_blank" rel="noreferrer noopener" href="'.get_option('twitter').'" class="twitter x-twitter"><svg class="icon-x" viewBox="0 0 512 512" fill="currentColor" role="img" aria-hidden="true" focusable="false"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg></a>';
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
