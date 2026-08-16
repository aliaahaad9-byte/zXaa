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

$metaboxes['Page_Options'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'ptype'=> array('page'),
	'name'    => 'خصائص',
	'nameEN'  => 'Page options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		
		array(
            'name'  => 'photo_gallery_list',
            'nameEN'=> 'photo_gallery_list',
            'type'  => 'group',
            'id'    => 'photo_gallery_list',
            'fields'=> array(
                array(
                    'name'  => 'title',
                    'nameEN'=> 'title',
                    'type'  => 'text',
                    'id'    => 'title',
                ),
               	array(
					'name'  => 'photo_gallery',
					'nameEN'=> 'photo_gallery',
					'type'  => 'file_list',
					'id'    => 'photo_gallery',
				),
            ),
        ),
		
			
	)
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
$metaboxes['photo_gallery'] = array(
	'context' => 'normal', // normal - side
	'priority'=> 'high', // high - low - default
	'taxonomy'=> array('category'),
	'name'    => 'خصائص',
	'nameEN'  => 'Price options',
	'type'    => 'fields', // layouts - fields
	'fields'  => array(
		array(
			'name'  => 'photo_gallery',
			'nameEN'=> 'photo_gallery',
			'type'  => 'file_list',
			'id'    => 'photo_gallery',
		),
		
		
			
	)
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
		
	),
);
