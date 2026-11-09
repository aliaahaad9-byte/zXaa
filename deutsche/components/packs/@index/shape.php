<?php
/**
 * Homepage — Lead Generation Engine.
 * Acquisition (hero + brands) → Trust (why, services, reels, cases) → Action (estimator, map).
 * Extra sections can be appended from Appearance → Widgets → "الصفحة الرئيسية".
 */
echo '<main id="main" class="home">';
	dg_hero_home();
	dg_brands_strip();
	dg_why_block();
	dg_services_grid();
	dg_estimator();
	dg_warning_guide( 6 );
	dg_reels_block();
	dg_cases_block( 3 );
	dg_prepurchase_band();
	dg_schedules_block();
	dg_faq_block( dg_faq_items() );
	dg_latest_posts( 3 );
	if ( is_active_sidebar( 'home' ) ) {
		echo '<div class="container widgets-area">';
		dynamic_sidebar( 'home' );
		echo '</div>';
	}
	dg_map_block();
echo '</main>';
