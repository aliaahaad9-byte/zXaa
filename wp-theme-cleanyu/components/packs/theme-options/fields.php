<?php
	$termTerms = array(
        "all"   => 'الكل'
    );
    foreach( get_terms(array("taxonomy"=>'category', "hide_empty"=>0, "parent"=>0)) as $t ) {
        $termTerms[$t->term_id] = $t->name;
        $parents = get_terms(array("taxonomy"=>'category', "hide_empty"=>0, "parent"=>$t->term_id));
        foreach( $parents as $p ) {
            $termTerms[$p->term_id] = '— '.$p->name;
        }
    }
    
$metaboxes['General'] = array(
	'name'    => 'الإعدادات العامة',
	'nameEN'  => 'General settings',
	'type'    => 'fields',
	'icon'    => '<i class="fal fa-sliders-h"></i>',
	'fields'  => array(
		array(
			'name'  => 'الشعار',
			'nameEN'=> 'Logo',
			'type'  => 'file',
			'id'    => 'logo',
		),
		array(
			'name'  => 'الشعار',
			'nameEN'=> 'logo_mobile',
			'type'  => 'file',
			'id'    => 'logo_mobile',
		),
		array(
			'name'  => 'رمز الموقع',
			'nameEN'=> 'Favicon',
			'type'  => 'file',
			'id'    => 'favicon',
		),
		array(
			'name'  => 'لغة الموقع  ',
			'nameEN'=> 'yc_lang',
			'type'  => 'text',
			'id'    => 'yc_lang',
		),
		array(
			'name'  => 'Slogan',
			'nameEN'=> 'Slogan',
			'type'  => 'text',
			'id'    => 'slogan',
		),
		array(
			'name'  => 'إسم الموقع',
			'nameEN'=> 'Sitename',
			'type'  => 'text',
			'id'    => 'sitename',
		),
		array(
			'name'  => 'صورة التصنيف الثابته',
			'nameEN'=> 'imagepin',
			'type'  => 'file',
			'id'    => 'imagepin',
		),
		
		
		
		
				
	)
);
$metaboxes['contactus'] = array(
	'name'    => 'معلومات الاتصال',
	'nameEN'  => 'General settings',
	'type'    => 'fields',
	'icon'    => '<i class="fa-regular fa-id-card-clip"></i>',
	'fields'  => array(
	    array(
			'name'  => 'عنوان الفورم ',
			'nameEN'=> 'titleform',
			'type'  => 'text',
			'id'    => 'titleform',
		),
	    array(
			'name'  => 'العنوان',
			'nameEN'=> 'Adress',
			'type'  => 'text',
			'id'    => 'Adress',
		),
		array(
			'name'  => 'الأميل',
			'nameEN'=> 'Email',
			'type'  => 'text',
			'id'    => 'Email',
		),
		array(
			'name'  => 'الخريطة',
			'nameEN'=> 'map',
			'type'  => 'textarea_code',
			'id'    => 'map',
		),
		array(
			'name'  => 'رقم الهاتف',
			'nameEN'=> 'Phone',
			'type'  => 'text',
			'id'    => 'Phone',
		),
		array(
			'name'  => 'رقم الواتس اب',
			'nameEN'=> 'Whatsapp',
			'type'  => 'text',
			'id'    => 'Whatsapp',
		),
		
		# Social
		array(
			'name' => 'الروابط الإجتماعية',
			'nameEN' => 'Social Links',
			'type' => 'title',
			'id' => '',
		),
		array(
			'name' => 'رابط تويتر',
			'nameEN' => 'Twitter',
			'type' => 'text',
			'id' => 'twitter',
		),
		array(
			'name' => 'رابط الفيس بوك',
			'nameEN' => 'Facebook',
			'type' => 'text',
			'id' => 'facebook',
		),
		array(
			'name' => 'رابط الانستجرام',
			'nameEN' => 'Instagram',
			'type' => 'text',
			'id' => 'instagram',
		),
		array(
			'name' => 'رابط لينكدان',
			'nameEN' => 'Linkedin',
			'type' => 'text',
			'id' => 'linkedin',
		),
		array(
			'name' => 'رابط تيليجرام',
			'nameEN' => 'Telegram ',
			'type' => 'text',
			'id' => 'telegram',
		),
		array(
			'name' => 'رابط يوتيوب',
			'nameEN' => 'Youtube',
			'type' => 'text',
			'id' => 'youtube',
		),
				
	)
);
$metaboxes['Footer'] = array(
	'name'    => 'نص الفوتر',
	'nameEN'  => 'footer settings',
	'type'    => 'fields',
	'icon'    => '<i class="fa-regular fa-id-card-clip"></i>',
	'fields'  => array(
		array(
			'name'  => 'لوجو فوتر',
			'nameEN'=> 'logo Footer',
			'type'  => 'file',
			'id'    => 'logoFooter',
		),
		
			
	)
);
$metaboxes['coverposts'] = array(
	'name'    => 'صور الكفر',
	'nameEN'  => 'footer settings',
	'type'    => 'fields',
	'icon'    => '<i class="fa-regular fa-id-card-clip"></i>',
	'fields'  => array(
		array(
			'name'  => 'لوجو فوتر',
			'nameEN'=> 'logo Footer',
			'type'  => 'file_list',
			'id'    => 'imagecover',
		),
		array(
			'name'  => 'اخفاء / اظهار',
			'nameEN'=> 'show_coverimage',
			'type'  => 'checkbox',
			'id'    => 'show_coverimage',
		),
		
			
	)
);



$metaboxes['ads settings'] = array(
	'name'    => 'إعدادات الإعلانات',
	'nameEN'  => 'ads settings',
	'type'    => 'fields',
	'icon'    => '<i class="fas fa-ad"></i>',
	'fields'  => array(
		array(
			'name'  => 'إخفاء اعلان الهيدر(PC)',
			'nameEN'=> 'Show Header Code',
			'type'  => 'checkbox',
			'id'    => 'show_ads_header',
		),
		array(
			'name'  => 'اعلان الهيدر(PC)',
			'nameEN'=> 'ads Codes',
			'type'  => 'textarea_code',
			'id'    => 'ads_header',
		),
		array(
			'name'  => 'إخفاء اعلان الهيدر(Mobile)',
			'nameEN'=> 'TopBarOpen',
			'type'  => 'checkbox',
			'id'    => 'show_ads_mobile_header',
		),
		array(
			'name'  => 'اعلان الهيدر (Mobile)',
			'nameEN'=> 'ads Codes',
			'type'  => 'textarea_code',
			'id'    => 'mobile_ads_header',
		),
	)
);