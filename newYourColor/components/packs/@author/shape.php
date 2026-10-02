<?php
$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
$UniqId = uniqid();

echo '<section class="project project-archive eeat-page">';
	echo '<div class="container">';
		echo '<breadcrumb>';
			Breadcrumb();
		echo '</breadcrumb>';

		// الملف المهني (E-E-A-T): التعريف، الأرقام، الخبرة حسب القسم، الشهادات، العضويات، الإنجازات
		if ( $curauth && class_exists( 'AuthorEEAT' ) ) {
			AuthorEEAT::profile( $curauth->ID );
		}

		// شبكة المقالات — نفس الجزء ونفس البطاقة
		$this->Part('Posts',array('UniqId'=>$UniqId,'AutoLoadmore'=>true,'author'=>$curauth ? $curauth->ID : 0));
	echo '</div>';
echo '</section>';
