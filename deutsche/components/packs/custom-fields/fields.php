<?php
$ModelsPath = (new ThemeStatic)->packsPath.'@models';
$models = array(
	""	=> 'بدون'
);
foreach( glob($ModelsPath.'/*.php') as $model ) {
	$modelname = explode('@models/', $model)[1];
	$modelname = explode('/', $modelname)[0];
	$modelname = explode('.php', $modelname)[0];
	$model_ID = $modelname;
	$modelname = str_replace(array('-', '_'), ' ', $modelname);
	$models[$model_ID] = ucfirst($modelname);
}
// أسماء عربية للنماذج الجديدة
$yc_model_labels = array(
	'brand-vag'          => 'بوابة VAG (فولكس / أودي / سكودا / سيات)',
	'brand-mercedes'     => 'بوابة مرسيدس بنز',
	'brand-bmw'          => 'بوابة BMW وميني',
	'brand-porsche-opel' => 'بوابة بورش وأوبل',
	'pre-purchase'       => 'فحص ما قبل الشراء',
	'warning-lights'     => 'دليل لمبات الأعطال',
	'maintenance'        => 'جداول الصيانة الدورية',
	'booking'            => 'الحجز وحاسبة التسعير',
	'cases'              => 'حالات الإصلاح والريلز',
	'about'              => 'عن المركز وجولة الورشة',
	'blog'               => 'المدونة',
	'faq_model'          => 'الأسئلة الشائعة',
	'contact'            => 'تواصل معنا',
);
foreach( $yc_model_labels as $yc_k => $yc_l ) {
	if( isset($models[$yc_k]) ) { $models[$yc_k] = $yc_l; }
}
$metaboxes['PageOptions'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'ptype'=> array('page'),
	'name'    => 'خصائص',
	'nameEN'  => 'Page options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
			'name'  => 'النموذج',
			'nameEN'=> 'Template',
			'type'  => 'select',
			'id'    => 'template',
			"options"=>$models
		),
	),
);

$metaboxes['PostOptions_Faq'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'ptype'=> array('post'),
	'name'    => 'الأسئله الشائعه',
	'nameEN'  => 'Post Faq',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
			'name'  => 'الأسئله',
			'nameEN'=> 'Faq',
			'type'  => 'group',
			'id'    => 'faq',
			'fields'=> array(
				array(
					'name'  => 'السؤال',
					'nameEN'=> 'question',
					'type'  => 'text',
					'id'    => 'question',
				),
				array(
					'name'  => 'الاجابه',
					'nameEN'=> 'answer',
					'type'  => 'textarea',
					'id'    => 'answer',
				)
			),
		),
	),
);
$metaboxes['CaseOptions'] = array(
	'context' => 'normal',
	'priority'=> 'high',
	'ptype'   => array('dg_case'),
	'name'    => 'تفاصيل حالة الإصلاح',
	'nameEN'  => 'Repair case',
	'type'    => 'fields',
	'fields'  => array(
		array( 'name' => 'المشكلة', 'nameEN' => 'Problem', 'type' => 'textarea', 'id' => 'case_problem' ),
		array( 'name' => 'التشخيص بالكمبيوتر', 'nameEN' => 'Diagnosis', 'type' => 'textarea', 'id' => 'case_diagnosis' ),
		array( 'name' => 'الحل الفني', 'nameEN' => 'Solution', 'type' => 'textarea', 'id' => 'case_solution' ),
		array( 'name' => 'رابط الفيديو (TikTok / Instagram Reel / YouTube)', 'nameEN' => 'Video URL', 'type' => 'text', 'id' => 'case_video' ),
	),
);
