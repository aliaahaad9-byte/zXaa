<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once __DIR__ . '/core.php';

// إنشاء / ترقية الجداول عند تفعيل القالب أو تغيّر إصدار قاعدة البيانات
add_action( 'after_switch_theme', array( 'Alz', 'install' ) );
add_action(
	'init',
	function () {
		if ( get_option( 'alz_db_version' ) !== Alz::DB_VERSION ) {
			Alz::install();
		}
	},
	5
);
