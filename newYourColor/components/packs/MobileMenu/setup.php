<?php
/**
 * قائمة الجوال — سكربت صغير بلا اعتماد على jQuery يضيف:
 * قفل تمرير الصفحة خلف القائمة، زر الإغلاق، مفتاح Esc، وأكورديون للقوائم الفرعية.
 * فتح القائمة نفسه يبقى بسكربت القالب (يضيف الصنف open على .menu-nav).
 */
add_action( 'wp_footer', function () {
	echo '<script src="' . esc_url( get_template_directory_uri() . '/components/packs/MobileMenu/menu.js?ver=1.0.0' ) . '"></script>';
}, 100 );
