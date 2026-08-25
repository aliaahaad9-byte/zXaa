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
$metaboxes['postwidgetOptions'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'ptype'=> array('post'),
	'name'    => 'مقالات  الودجيت',
	'nameEN'  => 'post widget options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
            'name'  => 'صورة  المقال الشورت',
            'nameEN'=> 'imgpostshort',
            'type'  => 'file',
            'id'    => 'imgpostshort'
        ), 
        array(
			'name'  => 'عنوان المقال  في الودجيت',
			'nameEN'=> 'titlepost',
			'type'  => 'text',
			'id'    => 'titlepost',
		),
		array(
			'name'  => 'اظهار البوست',
			'nameEN'=> 'show_post',
			'type'  => 'checkbox',
			'id'    => 'show_post',
		),
	),
);
$metaboxes['PostsEdOptions'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'ptype'=> array('post'),
	'name'    => 'خصائص',
	'nameEN'  => 'Page options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
			'name'  => 'رقم الاتصال',
			'nameEN'=> 'Call Number',
			'type'  => 'text',
			'id'    => 'call_number',
		),
		array(
			'name'  => 'رقم واتس اب',
			'nameEN'=> 'wwhatsapp_number',
			'type'  => 'text',
			'id'    => 'whatsapp_number',
		),
	
        array(
            'name'  => 'صورة كفر المقال',
            'nameEN'=> 'imagescovers',
            'type'  => 'file_list',
            'id'    => 'imagescovers'
        ),
	),
);


$metaboxes['CategoryOptions'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'taxonomy'=> array('category'),
	'name'    => 'إعدادات التصنيف',
	'nameEN'  => 'Category options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
			'name'  => 'تثبيت التصنيف',
			'nameEN'=> 'category pin',
			'type'  => 'checkbox',
			'id'    => 'catpin',
		),
		array(
			'name'  => 'صورة التصنيف',
			'nameEN'=> 'imgcat',
			'type'  => 'file',
			'id'    => 'imgcat',
		),
		array(
			'name'  => 'الايكونة',
			'nameEN'=> 'Icon',
			'type'  => 'textarea_code',
			'id'    => 'icon',
		),
		array(
			'name'  => 'ID الفيديو ',
			'nameEN'=> 'videoID',
			'type'  => 'text',
			'id'    => 'videoID',
		),
		array(
			'name'  => 'لون القسم',
			'nameEN'=> 'color',
			'type'  => 'colorpicker',
			'id'    => 'color',
		),
		array(
                'name'    => 'المدن',
                'id'      => 'country',
                'type'    => 'taxonomy_checkbox',
                'taxonomy' =>'country'
            ),        
            array(
    			'name'  => 'رقم الاتصال',
    			'nameEN'=> 'Call Number',
    			'type'  => 'text',
    			'id'    => 'call_number',
    		),
    		array(
    			'name'  => 'رقم واتس اب',
    			'nameEN'=> 'wwhatsapp_number',
    			'type'  => 'text',
    			'id'    => 'whatsapp_number',
    		),
    		
    		array(
    			'name'  => 'مثبت ',
    			'nameEN'=> 'select',
    			'type'  => 'checkbox',
    			'id'    => 'select',
    		),
    		array(
    			'name'  => 'سعر الخدمة',
    			'nameEN'=> 'Service_price',
    			'type'  => 'text',
    			'id'    => 'Service_price',
    		),

	),
);

$metaboxes['Price_Post'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'ptype'=> array('price'),
	'name'    => 'خصائص',
	'nameEN'  => 'Price options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
			'name'  => 'السعر',
			'nameEN'=> 'Price',
			'type'  => 'text',
			'id'    => 'price_text',
		),
		array(
			'name'  => 'ايكون',
			'nameEN'=> 'icon_1',
			'type'  => 'textarea_code',
			'id'    => 'icon_text',
		),
		array(
			'name'  => 'الاقسام',
			'nameEN'=> 'Categories',
			'type'  => 'taxonomy_checkbox',
			'id'    => 'categories',
			'taxonomy'=>'category'
		),
		array(
			'name'  => 'الخدمات المقدمه',
			'nameEN'=> 'services',
			'type'  => 'group',
			'id'    => 'services_text',
			'fields' => array(
				array(
					'name'  => 'خدمه',
					'nameEN'=> 'service',
					'type'  => 'text',
					'id'    => 'service_info',
				),
			)
		),
		array(
			'name'  => 'عنوان الزر',
			'nameEN'=> 'button Title',
			'type'  => 'text',
			'id'    => 'btn_title',
		),
		array(
			'name'  => 'مميز',
			'nameEN'=> 'feature',
			'type'  => 'checkbox',
			'id'    => 'feature',
		),
		array(
			'name'  => 'نسبة الخصم',
			'nameEN'=> 'offer',
			'type'  => 'text',
			'id'    => 'offer',
		),
	),
);
$metaboxes['countryOptions'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'taxonomy'=> array('country'),
	'name'    => 'خصائص',
	'nameEN'  => 'Sponsored options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
			'name'  => 'صورة',
			'nameEN'=> 'country img',
			'type'  => 'file',
			'id'    => 'country_img',
		),
		array(
			'name'  => 'ايكون',
			'nameEN'=> 'country_icon',
			'type'  => 'textarea_code',
			'id'    => 'icon_country',
		),
		array(
			'name'  => 'رقم الاتصال',
			'nameEN'=> 'Call Number',
			'type'  => 'text',
			'id'    => 'call_number',
		),
		array(
			'name'  => 'رقم واتس اب',
			'nameEN'=> 'wwhatsapp_number',
			'type'  => 'text',
			'id'    => 'whatsapp_number',
		),
		
	),
);

$metaboxes['worksOptions'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'ptype'=> array('works'),
	'name'    => 'خصائص',
	'nameEN'  => 'Page options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
            'name'  => 'الخطوات',
            'nameEN'     => 'Steps',
            'type'  => 'group',
            'id'    => 'works_steps',
            'fields'=> array(
                array(
                    'name'  => 'عنوان يمين الجدول',
                    'nameEN'=> 'Image',
                    'type'  => 'text',
                    'id'    => 'title_right'
                ),                      
                array(
                    'name'  => 'عنوان شمال الجدول',
                    'nameEN'=> 'Title',
                    'type'  => 'text',
                    'id'    => 'title_left'
                ),        
                
            )
        ),
        
		
		
	),
);

$metaboxes['ServiceLanding'] = array(
	'context' => 'normal',
	'priority'=> 'high',
	'taxonomy'=> array('category'),
	'name'    => 'صفحة هبوط الخدمة',
	'nameEN'  => 'Service landing page',
	'type'    => 'fields',
	'fields'  => array(
		array(
			'name' => 'تفعيل صفحة الهبوط',
			'nameEN' => 'تفعيل صفحة الهبوط',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'تحويل هذا التصنيف إلى صفحة هبوط (بدونه يبقى أرشيفًا عاديًا)',
			'nameEN'=> 'svc_enable',
			'type'  => 'checkbox',
			'id'    => 'svc_enable',
		),
		array(
			'name'  => 'نظام الأرقام: اتركه فارغًا للاتيني 8,400 — أو اكتب arabic للهندي ٨٬٤٠٠',
			'nameEN'=> 'svc_num_style',
			'type'  => 'text',
			'id'    => 'svc_num_style',
		),
		array(
			'name' => '١ — المقدمة',
			'nameEN' => '١ — المقدمة',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'العنوان الرئيسي',
			'nameEN'=> 'svc_title',
			'type'  => 'text',
			'id'    => 'svc_title',
		),
		array(
			'name'  => 'سطر الوعد',
			'nameEN'=> 'svc_promise',
			'type'  => 'textarea',
			'id'    => 'svc_promise',
		),
		array(
			'name'  => 'شارات الثقة (سطر لكل شارة)',
			'nameEN'=> 'svc_badges',
			'type'  => 'textarea',
			'id'    => 'svc_badges',
		),
		array(
			'name'  => 'نص زر الاتصال',
			'nameEN'=> 'svc_call_label',
			'type'  => 'text',
			'id'    => 'svc_call_label',
		),
		array(
			'name'  => 'نص زر الواتساب',
			'nameEN'=> 'svc_wa_label',
			'type'  => 'text',
			'id'    => 'svc_wa_label',
		),
		array(
			'name'  => 'عنوان نموذج المقدمة',
			'nameEN'=> 'svc_quick_title',
			'type'  => 'text',
			'id'    => 'svc_quick_title',
		),
		array(
			'name'  => 'وصف نموذج المقدمة',
			'nameEN'=> 'svc_quick_sub',
			'type'  => 'text',
			'id'    => 'svc_quick_sub',
		),
		array(
			'name'  => 'نص زر نموذج المقدمة',
			'nameEN'=> 'svc_quick_btn',
			'type'  => 'text',
			'id'    => 'svc_quick_btn',
		),
		array(
			'name'  => 'ملاحظة أسفل نموذج المقدمة',
			'nameEN'=> 'svc_quick_note',
			'type'  => 'text',
			'id'    => 'svc_quick_note',
		),
		array(
			'name' => '٢ — شريط الأرقام',
			'nameEN' => '٢ — شريط الأرقام',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'الأرقام (سطر لكل رقم: الرقم | التسمية)',
			'nameEN'=> 'svc_stats',
			'type'  => 'textarea',
			'id'    => 'svc_stats',
		),
		array(
			'name' => '٣ — المميزات',
			'nameEN' => '٣ — المميزات',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'عنوان قسم المميزات',
			'nameEN'=> 'svc_features_title',
			'type'  => 'text',
			'id'    => 'svc_features_title',
		),
		array(
			'name'  => 'وصف قسم المميزات',
			'nameEN'=> 'svc_features_sub',
			'type'  => 'text',
			'id'    => 'svc_features_sub',
		),
		array(
			'name'  => 'المميزات (ستة أسطر: العنوان | الوصف)',
			'nameEN'=> 'svc_features',
			'type'  => 'textarea',
			'id'    => 'svc_features',
		),
		array(
			'name' => '٤ — خطوات العمل',
			'nameEN' => '٤ — خطوات العمل',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'عنوان قسم الخطوات',
			'nameEN'=> 'svc_steps_title',
			'type'  => 'text',
			'id'    => 'svc_steps_title',
		),
		array(
			'name'  => 'وصف قسم الخطوات',
			'nameEN'=> 'svc_steps_sub',
			'type'  => 'text',
			'id'    => 'svc_steps_sub',
		),
		array(
			'name'  => 'الخطوات (سطر لكل خطوة: العنوان | الوصف) — الأخيرة تظهر خضراء',
			'nameEN'=> 'svc_steps',
			'type'  => 'textarea',
			'id'    => 'svc_steps',
		),
		array(
			'name' => '٥ — المدن',
			'nameEN' => '٥ — المدن',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'عنوان قسم المدن',
			'nameEN'=> 'svc_cities_title',
			'type'  => 'text',
			'id'    => 'svc_cities_title',
		),
		array(
			'name'  => 'وصف قسم المدن',
			'nameEN'=> 'svc_cities_sub',
			'type'  => 'text',
			'id'    => 'svc_cities_sub',
		),
		array(
			'name'  => 'حوّل روابط المدن إلى «الخدمة داخل المدينة» بدل أرشيف المدينة',
			'nameEN'=> 'svc_city_service_links',
			'type'  => 'checkbox',
			'id'    => 'svc_city_service_links',
		),
		array(
			'name' => '٦ — الأسئلة الشائعة',
			'nameEN' => '٦ — الأسئلة الشائعة',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'عنوان قسم الأسئلة',
			'nameEN'=> 'svc_faq_title',
			'type'  => 'text',
			'id'    => 'svc_faq_title',
		),
		array(
			'name'  => 'وصف قسم الأسئلة',
			'nameEN'=> 'svc_faq_sub',
			'type'  => 'text',
			'id'    => 'svc_faq_sub',
		),
		array(
			'name'  => 'عدد الأسئلة المعروضة',
			'nameEN'=> 'svc_faq_count',
			'type'  => 'text',
			'id'    => 'svc_faq_count',
		),
		array(
			'name' => '٧ — نموذج الطلب',
			'nameEN' => '٧ — نموذج الطلب',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'عنوان قسم الطلب',
			'nameEN'=> 'svc_order_title',
			'type'  => 'text',
			'id'    => 'svc_order_title',
		),
		array(
			'name'  => 'وصف قسم الطلب',
			'nameEN'=> 'svc_order_sub',
			'type'  => 'text',
			'id'    => 'svc_order_sub',
		),
		array(
			'name'  => 'نص زر الإرسال',
			'nameEN'=> 'svc_order_btn',
			'type'  => 'text',
			'id'    => 'svc_order_btn',
		),
		array(
			'name' => 'تسميات حقول النماذج',
			'nameEN' => 'تسميات حقول النماذج',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'تسمية حقل الاسم',
			'nameEN'=> 'svc_lbl_name',
			'type'  => 'text',
			'id'    => 'svc_lbl_name',
		),
		array(
			'name'  => 'تسمية حقل الجوال',
			'nameEN'=> 'svc_lbl_phone',
			'type'  => 'text',
			'id'    => 'svc_lbl_phone',
		),
		array(
			'name'  => 'تسمية حقل المدينة',
			'nameEN'=> 'svc_lbl_city',
			'type'  => 'text',
			'id'    => 'svc_lbl_city',
		),
		array(
			'name'  => 'تسمية حقل البريد',
			'nameEN'=> 'svc_lbl_email',
			'type'  => 'text',
			'id'    => 'svc_lbl_email',
		),
		array(
			'name'  => 'تسمية حقل التفاصيل',
			'nameEN'=> 'svc_lbl_details',
			'type'  => 'text',
			'id'    => 'svc_lbl_details',
		),
		array(
			'name' => '٨ — المقالات',
			'nameEN' => '٨ — المقالات',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name'  => 'عنوان قسم المقالات',
			'nameEN'=> 'svc_posts_title',
			'type'  => 'text',
			'id'    => 'svc_posts_title',
		),
	),
);
