<?php
// Post types. Repair cases (dg_case) and leads are registered in Deutsche/setup.php.
function PostTypes() {
	global $ThemeTree;
	$ThemeTree->AddPType( 'الأسئلة الشائعة', 'سؤال', 'ة', 'faq', true, false, array( 'title', 'editor', 'page-attributes' ), 8 );
}
add_action( 'Initialize', 'PostTypes', 10, 3 );
