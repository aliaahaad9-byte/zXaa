<?php 
function Taxonomies() {
	global $ThemeTree;
	$ThemeTree->AddTaxonomy('country', array("post", "price"), 'المدن', array('slug'=>get_option('cities_url')), false);
	$ThemeTree->AddTaxonomy('questions', "bot", 'الاسئلة', false, true);
}
add_action('Initialize', 'Taxonomies', 10, 3);