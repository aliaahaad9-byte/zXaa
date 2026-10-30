<?php
function DisplayDate($time) {
	if( date('Y', $time) != date('Y') ) {
		$displayed = date_i18n('l, d F Y', $time);
	}else {
		if( date('Y-m-d', $time) == date('Y-m-d') ) {
			$displayed = 'منذ '.human_time_diff( date('U', $time), current_time('timestamp') );
		}else {
			$displayed = date_i18n('l, d F', $time);
		}
	}
	return $displayed;
}
/**
 * تاريخ عربي بأشهر ميلادية وأرقام لاتينية: 25 ديسمبر 2022
 * مستقل عن لغة ووردبريس حتى يظهر بنفس الشكل دائمًا.
 */
if ( ! function_exists( 'YC_ArabicDate' ) ) {
	function YC_ArabicDate( $timestamp ) {
		$months = array( 1 => 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر' );
		return (int) date( 'j', $timestamp ) . ' ' . $months[ (int) date( 'n', $timestamp ) ] . ' ' . date( 'Y', $timestamp );
	}
}

/**
 * سطر «نُشر: … · آخر تحديث: …» — يظهر تاريخ التحديث فقط إذا عُدّل المقال في يوم لاحق.
 */
if ( ! function_exists( 'YC_PostDates' ) ) {
	function YC_PostDates( $post, $class = 'post-dates' ) {
		$published = strtotime( $post->post_date );
		$modified  = strtotime( $post->post_modified );
		$out  = '<div class="' . esc_attr( $class ) . '">';
		$out .= '<span class="post-dates__item"><i class="fa-regular fa-calendar" aria-hidden="true"></i>نُشر: <time datetime="' . esc_attr( get_post_time( 'c', false, $post ) ) . '">' . esc_html( YC_ArabicDate( $published ) ) . '</time></span>';
		if ( $modified > $published && date( 'Y-m-d', $modified ) !== date( 'Y-m-d', $published ) ) {
			$out .= '<span class="post-dates__sep" aria-hidden="true">·</span>';
			$out .= '<span class="post-dates__item"><i class="fa-regular fa-arrows-rotate" aria-hidden="true"></i>آخر تحديث: <time datetime="' . esc_attr( get_post_modified_time( 'c', false, $post ) ) . '">' . esc_html( YC_ArabicDate( $modified ) ) . '</time></span>';
		}
		return $out . '</div>';
	}
}
