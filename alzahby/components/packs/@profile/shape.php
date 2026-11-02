<?php
/**
 * صفحة العميل العامة (/{slug} و /u/{id}) — Mobile-First
 * صفحة مستقلة بالكامل بدون wp_head ولا أي سكربتات خارجية: CSS و JS مضمّنين inline
 *
 * @var object $profile
 * @var array  $links
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$urls = Alz::profile_urls( $profile );
$desc = $profile->bio ? wp_trim_words( $profile->bio, 30 ) : $profile->business_name . ' — كل روابطنا وطرق الدفع في مكان واحد';

$meta  = '<meta name="description" content="' . esc_attr( $desc ) . '">';
$meta .= '<link rel="canonical" href="' . esc_url( $urls['pretty'] ) . '">';
$meta .= '<meta name="theme-color" content="' . esc_attr( $profile->theme_primary ) . '">';
$meta .= '<meta property="og:type" content="profile"><meta property="og:title" content="' . esc_attr( $profile->business_name ) . '">';
$meta .= '<meta property="og:description" content="' . esc_attr( $desc ) . '"><meta property="og:url" content="' . esc_url( $urls['pretty'] ) . '">';
if ( $profile->logo_url ) {
	$meta .= '<meta property="og:image" content="' . esc_url( $profile->logo_url ) . '">';
	$meta .= '<link rel="preload" as="image" href="' . esc_url( $profile->logo_url ) . '">';
}
$meta .= '<style>' . Alz::inline_asset( 'css/profile.css' ) . '</style>';

Alz::head( $profile->business_name, $meta );
// أكواد إضافية من إعدادات القالب (Pixel / Analytics) — يضيفها الأدمن فقط
echo Alz::opt( 'profile_head_code' ); // phpcs:ignore
echo '</head><body>';

echo str_replace( 'class="lp"', 'class="lp lp--page"', Alz::render_profile( $profile, $links ) ); // phpcs:ignore -- escaped inside render_profile

echo '<script>window.ALZ_PROFILE=' . wp_json_encode(
	array(
		'id'    => $profile->id,
		'track' => rest_url( 'alzahby/v1/track' ),
		'icons' => array( 'wifi' => Alz::icon( 'wifi' ) ),
	)
) . ';' . Alz::inline_asset( 'js/profile.js' ) . '</script>'; // phpcs:ignore
echo '</body></html>';
