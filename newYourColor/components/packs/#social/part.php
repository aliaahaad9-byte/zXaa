<?php
/**
 * روابط التواصل الاجتماعي — تُعرض فقط الروابط المعبّأة في إعدادات القالب.
 */

$sl_socials = array(
	'twitter'   => array( 'icon' => 'fa-brands fa-x-twitter', 'label' => 'X' ),
	'instagram' => array( 'icon' => 'fa-brands fa-instagram', 'label' => 'instagram' ),
	'facebook'  => array( 'icon' => 'fa-brands fa-facebook',  'label' => 'facebook' ),
	'youtube'   => array( 'icon' => 'fa-brands fa-youtube',   'label' => 'youtube' ),
	'linkedin'  => array( 'icon' => 'fa-brands fa-linkedin',  'label' => 'linkedin' ),
	'telegram'  => array( 'icon' => 'fa-brands fa-telegram',  'label' => 'telegram' ),
);

echo '<div class="social--footer">';
foreach ( $sl_socials as $sl_key => $sl_data ) {
	$sl_url = get_option( $sl_key );
	if ( empty( $sl_url ) ) {
		continue;
	}
	echo '<a aria-label="' . esc_attr( $sl_data['label'] ) . '" target="_blank" rel="noreferrer noopener" href="' . esc_url( $sl_url ) . '" class="' . esc_attr( $sl_key ) . '">';
	echo '<i class="' . esc_attr( $sl_data['icon'] ) . '" aria-hidden="true"></i>';
	echo '</a>';
}
echo '</div>';
