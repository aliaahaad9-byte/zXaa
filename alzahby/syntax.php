<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeStatic — موجّه القالب: يحدد أي Blade (@page) يُعرض لكل طلب
 * أجزاء الصفحة (#header / #footer) تُستدعى عبر Part()
 * مسارات QurTap (profile / stand / dashboard / login) تأتي من الـ query var: alz_route
 */
class ThemeStatic extends ThemeTree {

	public function Locate() {
		if ( is_feed() || is_robots() || is_trackback() || is_favicon() ) {
			return;
		}

		// مسارات المنصة — كل route له Blade مستقل بصفحة HTML كاملة خاصة به
		$route = get_query_var( 'alz_route' );
		if ( $route && in_array( $route, array( 'profile', 'stand', 'dashboard', 'login' ), true ) ) {
			$data = apply_filters( 'alz_route_data_' . $route, array() );
			if ( false === $data ) {
				status_header( 404 );
				$this->RenderNotFound();
				die();
			}
			$this->Blade( $route, $data );
			die();
		}

		if ( is_404() ) {
			status_header( 404 );
			$this->RenderNotFound();
			die();
		}

		global $post;
		do_action( 'BeforeHeader' );
		$this->Part( 'header' );
		do_action( 'AfterHeader' );

		if ( is_front_page() ) {
			$this->Blade( 'index' );
		} elseif ( is_search() ) {
			$this->Blade( 'search' );
		} elseif ( is_home() || is_archive() ) {
			$this->Blade( 'archive', is_home() ? false : get_queried_object() );
		} elseif ( is_singular( 'post' ) ) {
			$this->Blade( 'single', $post );
		} elseif ( is_singular() ) {
			$this->Blade( 'page', $post );
		} else {
			$this->Blade( 'archive' );
		}

		do_action( 'BeforeFooter' );
		$this->Part( 'footer' );
		do_action( 'AfterFooter' );
		die();
	}

	public function RenderNotFound() {
		$this->Part( 'header' );
		$this->Blade( '404' );
		$this->Part( 'footer' );
	}

	public function Part( $part, $vars = array() ) {
		foreach ( $this->Packages as $pack ) {
			if ( basename( $pack ) === '#' . $part ) {
				$CurrentURL = str_replace( get_template_directory(), get_template_directory_uri(), $pack );
				$CurrentURL = str_replace( '#', rawurlencode( '#' ), $CurrentURL );
				$this->Require( $pack . 'part.php', array_merge( array( 'CurrentDir' => $pack, 'CurrentURL' => $CurrentURL ), $vars ) );
			}
		}
	}

	public function Blade( $page, $obj = false, $fname = 'shape' ) {
		$vars = array();
		if ( is_object( $obj ) && isset( $obj->taxonomy ) ) {
			$vars = array( 'obj' => $obj );
		} elseif ( is_object( $obj ) && isset( $obj->ID ) ) {
			$vars = array( 'post' => $obj );
		} elseif ( is_array( $obj ) ) {
			$vars = $obj;
		}
		foreach ( $this->Packages as $pack ) {
			if ( basename( $pack ) === '@' . $page ) {
				$filename = $fname;
				if ( is_object( $obj ) && isset( $obj->post_type ) && file_exists( $pack . $obj->post_type . '.php' ) ) {
					$filename = $obj->post_type;
				}
				$CurrentURL = str_replace( get_template_directory(), get_template_directory_uri(), $pack );
				$CurrentURL = str_replace( '#', rawurlencode( '#' ), $CurrentURL );
				$this->Require( $pack . $filename . '.php', array_merge( array( 'CurrentDir' => $pack, 'CurrentURL' => $CurrentURL ), $vars ) );
			}
		}
	}

	public function Tree() {
		add_action( 'template_redirect', array( $this, 'Locate' ), 20 );
	}

	public function Paged() {
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
		return max( 1, (int) $paged );
	}

	public function Pagination( $wp_query = null ) {
		if ( ! $wp_query ) {
			global $wp_query;
		}
		$links = paginate_links(
			array(
				'total'     => $wp_query->max_num_pages,
				'current'   => $this->Paged(),
				'type'      => 'list',
				'prev_text' => '&rsaquo;',
				'next_text' => '&lsaquo;',
			)
		);
		if ( $links ) {
			echo '<nav class="pagination">' . $links . '</nav>'; // phpcs:ignore -- core markup
		}
	}
}

( new ThemeStatic() )->Tree();
