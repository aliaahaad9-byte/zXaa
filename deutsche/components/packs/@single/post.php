<?php
// Blog article.
global $post;
setup_postdata( $post );
$cats    = get_the_category( $post->ID );
$brands  = get_the_terms( $post->ID, 'car_brand' );
$words   = count( preg_split( '/\s+/u', wp_strip_all_tags( $post->post_content ) ) );
$minutes = max( 1, (int) ceil( $words / 180 ) );
$author  = get_userdata( $post->post_author );
$faq     = get_post_meta( $post->ID, 'faq', true );

echo '<main id="main" class="article">';
	echo '<header class="article__head"><div class="container container--narrow">';
		dg_breadcrumbs();
		if ( $cats ) { echo '<p class="tag"><a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a></p>'; }
		echo '<h1 class="article__title">' . esc_html( get_the_title( $post ) ) . '</h1>';
		echo '<p class="article__meta">';
			if ( $author ) { echo '<span>' . dg_icon( 'users' ) . esc_html( $author->display_name ) . '</span>'; }
			echo '<span>' . dg_icon( 'calendar' ) . '<time datetime="' . esc_attr( get_the_modified_date( 'c', $post ) ) . '">آخر تحديث ' . esc_html( get_the_modified_date( '', $post ) ) . '</time></span>';
			echo '<span>' . dg_icon( 'clock' ) . 'قراءة ' . $minutes . ' دقائق</span>';
		echo '</p>';
	echo '</div></header>';

	echo '<div class="container article__grid">';
		echo '<article class="article__body">';
			if ( has_post_thumbnail( $post ) ) {
				echo '<figure class="article__cover">' . get_the_post_thumbnail( $post, 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ) . '</figure>';
			}
			echo '<div class="prose">' . apply_filters( 'the_content', $post->post_content ) . '</div>';
			$tags = get_the_tags( $post->ID );
			if ( $tags || ( $brands && ! is_wp_error( $brands ) ) ) {
				echo '<ul class="cat-pills" role="list">';
				if ( $brands && ! is_wp_error( $brands ) ) { foreach ( $brands as $b ) { echo '<li><a href="' . esc_url( get_term_link( $b ) ) . '">' . esc_html( $b->name ) . '</a></li>'; } }
				if ( $tags ) { foreach ( $tags as $t ) { echo '<li><a href="' . esc_url( get_tag_link( $t ) ) . '">#' . esc_html( $t->name ) . '</a></li>'; } }
				echo '</ul>';
			}
			if ( $author ) {
				echo '<aside class="author-box">' . get_avatar( $author->ID, 64, '', '', array( 'loading' => 'lazy' ) ) . '<div><p class="author-box__name">' . esc_html( $author->display_name ) . '</p><p>' . esc_html( $author->description ? $author->description : 'فريق المهندسين في دويتش جراج — متخصصون في صيانة وبرمجة السيارات الألمانية بالإسكندرية.' ) . '</p></div></aside>';
			}
		echo '</article>';

		echo '<aside class="article__aside" aria-label="احجز الآن">';
			echo '<div class="aside-cta">';
				echo '<p class="aside-cta__k latin">Deutsche Garage</p>';
				echo '<p class="aside-cta__t">محتاج فحص أو صيانة لعربيتك؟</p>';
				echo '<p>تشخيص بأجهزة الوكالة وتسعيرة قبل الإصلاح — في سيدي جابر.</p>';
				echo dg_btn( 'احجز موعد', dg_booking_url(), 'primary', 'calendar' );
				echo dg_btn( 'واتساب', dg_wa( 'مرحبًا، قرأت مقال: ' . get_the_title( $post ) ), 'wa', 'whatsapp' );
			echo '</div>';
		echo '</aside>';
	echo '</div>';

	if ( is_array( $faq ) ) {
		$items = array();
		foreach ( $faq as $f ) {
			if ( ! empty( $f['question'] ) && ! empty( $f['answer'] ) ) { $items[] = array( $f['question'], wp_strip_all_tags( $f['answer'] ) ); }
		}
		dg_faq_block( $items, 'أسئلة متعلقة بالمقال', 'post-faq' );
	}

	$related = get_posts( array( 'posts_per_page' => 3, 'post__not_in' => array( $post->ID ), 'category__in' => wp_list_pluck( $cats, 'term_id' ) ) );
	if ( $related ) {
		echo '<section class="section section--alt"><div class="container">';
			dg_section_head( 'Related', 'مقالات ذات صلة' );
			echo '<ul class="posts-grid" role="list">';
			foreach ( $related as $r ) { dg_post_card( $r ); }
			echo '</ul>';
		echo '</div></section>';
	}
	if ( comments_open( $post->ID ) ) {
		echo '<section class="section"><div class="container container--narrow">';
		$this->Part( 'comments', array( 'post' => $post ) );
		echo '</div></section>';
	}

	if ( ! defined( 'WPSEO_VERSION' ) && ! defined( 'RANK_MATH_VERSION' ) ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array(
			'@context'      => 'https://schema.org',
			'@type'         => 'BlogPosting',
			'headline'      => get_the_title( $post ),
			'datePublished' => get_the_date( 'c', $post ),
			'dateModified'  => get_the_modified_date( 'c', $post ),
			'image'         => has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'large' ) : dg_img( 'og-cover' ),
			'author'        => array( '@type' => 'Person', 'name' => $author ? $author->display_name : dg_name() ),
			'publisher'     => array( '@id' => home_url( '/#business' ) ),
			'mainEntityOfPage' => get_permalink( $post ),
		), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
	}
echo '</main>';
wp_reset_postdata();
