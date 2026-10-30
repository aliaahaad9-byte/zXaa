<?php
// روابط التواصل الاجتماعي — كلها من إعدادات القالب ← معلومات الاتصال
$social_networks = array(
	'facebook'  => array( 'Facebook',  'fab fa-facebook' ),
	'instagram' => array( 'Instagram', 'fab fa-instagram' ),
	'twitter'   => array( 'X',         'fa-brands fa-x-twitter' ),
	'youtube'   => array( 'YouTube',   'fab fa-youtube' ),
	'tiktok'    => array( 'TikTok',    'fa-brands fa-tiktok' ),
	'snapchat'  => array( 'Snapchat',  'fa-brands fa-snapchat' ),
	'linkedin'  => array( 'LinkedIn',  'fab fa-linkedin' ),
	'telegram'  => array( 'Telegram',  'fab fa-telegram' ),
);
echo '<div class="social--footer">';
	foreach ( $social_networks as $social_key => $social_net ) {
		$social_url = trim( (string) get_option( $social_key ) );
		if ( '' === $social_url ) {
			continue;
		}
		echo '<a aria-label="'.esc_attr( $social_net[0] ).'" target="_blank" rel="noreferrer noopener" href="'.esc_url( $social_url ).'" class="'.esc_attr( $social_key ).'"><i class="'.esc_attr( $social_net[1] ).'" aria-hidden="true"></i></a>';
	}
echo '</div>';
