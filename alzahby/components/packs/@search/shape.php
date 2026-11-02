<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<main>
	<header class="page-head">
		<div class="container">
			<h1>نتائج البحث عن: <?php echo esc_html( get_search_query() ); ?></h1>
			<form class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
				<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="ابحث في المدونة">
				<button class="btn btn--dark" type="submit">بحث</button>
			</form>
		</div>
	</header>
	<div class="container">
		<?php $this->Part( 'Posts' ); ?>
		<?php $this->Pagination(); ?>
	</div>
</main>
