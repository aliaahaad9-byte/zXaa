<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALZ_VERSION', '1.1.0' );

class ThemeTree {
	private $args;

	public $TempPath;
	public $TempURL;
	public $StylesURL;
	public $StylesPath;
	public $AssetsURL;
	public $AssetsPath;
	public $folderpath;
	public $packsPath;
	public $Packages;

	function __construct( $args = array() ) {
		$this->args       = $args;
		$this->TempPath   = get_template_directory();
		$this->TempURL    = get_template_directory_uri();
		$this->StylesURL  = get_template_directory_uri() . '/components/styles/';
		$this->StylesPath = get_template_directory() . '/components/styles/';
		$this->AssetsURL  = get_template_directory_uri() . '/components/assets/';
		$this->AssetsPath = get_template_directory() . '/components/assets/';
		$this->folderpath = $this->TempPath . '/components/packs/*/';
		$this->packsPath  = $this->TempPath . '/components/packs/';
		$this->Packages   = array_filter( glob( $this->folderpath ), 'is_dir' );
		if ( ! class_exists( 'ThemeStatic' ) ) {
			require $this->TempPath . '/syntax.php';
		}
	}

	public function Require( $path, $vars = array() ) {
		extract( $vars );
		if ( file_exists( $path ) ) {
			require $path;
		}
	}

	public function Initialize() {
		do_action( 'Initialize' );
	}

	/** رابط ملف أصول مع رقم الإصدار (filemtime) لكسر الكاش عند التحديث */
	public function Asset( $file ) {
		$path = $this->AssetsPath . $file;
		$ver  = file_exists( $path ) ? filemtime( $path ) : ALZ_VERSION;
		return $this->AssetsURL . $file . '?v=' . $ver;
	}
}

$ThemeTree = new ThemeTree();
add_action( 'init', array( $ThemeTree, 'Initialize' ) );

// تحميل الحزم: كل مجلد لا يبدأ بـ @ (صفحات Blade) أو # (أجزاء Part)
foreach ( $ThemeTree->Packages as $pack ) {
	$first = substr( basename( $pack ), 0, 1 );
	if ( '@' !== $first && '#' !== $first ) {
		$ThemeTree->Require( $pack . 'setup.php', array( 'CurrentDir' => $pack ) );
	}
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		load_theme_textdomain( 'alzahby', get_template_directory() . '/languages' );
	}
);

// تنظيف الواجهة من ملفات غير مستخدمة لسرعة أعلى
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_script( 'wp-embed' );
	},
	100
);
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
add_filter( 'rank_math/frontend/disable_adjacent_rel_links', '__return_true' );
add_filter( 'wpseo_next_rel_link', '__return_false' );
add_filter( 'wpseo_prev_rel_link', '__return_false' );
