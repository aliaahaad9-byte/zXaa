<?php
/**
 * Deutsche Garage — theme options (لوحة "إعدادات القالب").
 */
$metaboxes['General'] = array(
	'name'   => 'الإعدادات العامة',
	'nameEN' => 'General settings',
	'type'   => 'fields',
	'icon'   => '<i class="fal fa-sliders-h"></i>',
	'fields' => array(
		array( 'name' => 'الشعار (نسخة داكنة للخلفيات الفاتحة)', 'nameEN' => 'Logo', 'type' => 'file', 'id' => 'logo' ),
		array( 'name' => 'الشعار الأبيض (للهيدر والفوتر الداكن)', 'nameEN' => 'Logo white', 'type' => 'file', 'id' => 'logo_white' ),
		array( 'name' => 'رمز الموقع Favicon', 'nameEN' => 'Favicon', 'type' => 'file', 'id' => 'favicon' ),
		array( 'name' => 'اسم الموقع', 'nameEN' => 'Sitename', 'type' => 'text', 'id' => 'sitename' ),
		array( 'name' => 'لغة الموقع (افتراضي ar-EG)', 'nameEN' => 'yc_lang', 'type' => 'text', 'id' => 'yc_lang' ),
	),
);
$metaboxes['contactus'] = array(
	'name'   => 'بيانات التواصل (NAP)',
	'nameEN' => 'Contact',
	'type'   => 'fields',
	'icon'   => '<i class="fa-regular fa-id-card-clip"></i>',
	'fields' => array(
		array( 'name' => 'رقم الهاتف الرئيسي', 'nameEN' => 'Phone', 'type' => 'text', 'id' => 'Phone' ),
		array( 'name' => 'رقم اتصال / واتساب بديل', 'nameEN' => 'Phone 2', 'type' => 'text', 'id' => 'Phone2' ),
		array( 'name' => 'رقم الواتساب', 'nameEN' => 'Whatsapp', 'type' => 'text', 'id' => 'Whatsapp' ),
		array( 'name' => 'العنوان (يظهر للزوار)', 'nameEN' => 'Address', 'type' => 'text', 'id' => 'Adress' ),
		array( 'name' => 'رابط خرائط جوجل', 'nameEN' => 'Google Maps link', 'type' => 'text', 'id' => 'map_url' ),
		array( 'name' => 'كود تضمين الخريطة (iframe) — اختياري', 'nameEN' => 'Map embed', 'type' => 'textarea_code', 'id' => 'map' ),
		array( 'name' => 'البريد لاستقبال الطلبات', 'nameEN' => 'Email', 'type' => 'text', 'id' => 'Email' ),
		array( 'name' => 'مواعيد العمل (كل سطر: الأيام = الوقت)', 'nameEN' => 'Working hours', 'type' => 'textarea', 'id' => 'work_hours' ),
		array( 'name' => 'روابط السوشيال', 'nameEN' => 'Social', 'type' => 'title', 'id' => '' ),
		array( 'name' => 'إنستجرام', 'nameEN' => 'Instagram', 'type' => 'text', 'id' => 'instagram' ),
		array( 'name' => 'تيك توك', 'nameEN' => 'TikTok', 'type' => 'text', 'id' => 'tiktok' ),
		array( 'name' => 'فيسبوك', 'nameEN' => 'Facebook', 'type' => 'text', 'id' => 'facebook' ),
		array( 'name' => 'يوتيوب', 'nameEN' => 'YouTube', 'type' => 'text', 'id' => 'youtube' ),
	),
);
$metaboxes['seo_local'] = array(
	'name'   => 'السيو المحلي (Schema)',
	'nameEN' => 'Local SEO',
	'type'   => 'fields',
	'icon'   => '<i class="fa-regular fa-location-dot"></i>',
	'fields' => array(
		array( 'name' => 'الاسم الرسمي المطابق لخرائط جوجل', 'nameEN' => 'Business name', 'type' => 'text', 'id' => 'schema_name' ),
		array( 'name' => 'العنوان بالإنجليزية (مطابق لخرائط جوجل)', 'nameEN' => 'Street address', 'type' => 'text', 'id' => 'schema_street' ),
		array( 'name' => 'الرمز البريدي', 'nameEN' => 'Postal code', 'type' => 'text', 'id' => 'schema_postal' ),
		array( 'name' => 'خط العرض Latitude', 'nameEN' => 'Latitude', 'type' => 'text', 'id' => 'geo_lat' ),
		array( 'name' => 'خط الطول Longitude', 'nameEN' => 'Longitude', 'type' => 'text', 'id' => 'geo_lng' ),
		array( 'name' => 'مواعيد العمل للسكيما (مثال: Saturday,Sunday=10:00-20:00)', 'nameEN' => 'Schema hours', 'type' => 'textarea', 'id' => 'schema_hours' ),
	),
);
$metaboxes['home'] = array(
	'name'   => 'الصفحة الرئيسية',
	'nameEN' => 'Homepage',
	'type'   => 'fields',
	'icon'   => '<i class="fa-regular fa-house"></i>',
	'fields' => array(
		array( 'name' => 'العنوان الرئيسي H1', 'nameEN' => 'Hero title', 'type' => 'textarea', 'id' => 'hero_title' ),
		array( 'name' => 'العنوان الفرعي', 'nameEN' => 'Hero subtitle', 'type' => 'textarea', 'id' => 'hero_sub' ),
		array( 'name' => 'فيديو الهيرو (رابط WebM صامت مضغوط — اختياري)', 'nameEN' => 'Hero video', 'type' => 'text', 'id' => 'hero_video' ),
		array( 'name' => 'فيديوهات الريلز وتيك توك', 'nameEN' => 'Reels', 'type' => 'title', 'id' => '' ),
		array(
			'name'   => 'الفيديوهات (رابط الفيديو + صورة مصغرة WebP عمودية 540×960)',
			'nameEN' => 'Reels',
			'type'   => 'group',
			'id'     => 'reels',
			'fields' => array(
				array( 'name' => 'عنوان الفيديو', 'nameEN' => 'Title', 'type' => 'text', 'id' => 'title' ),
				array( 'name' => 'رابط الفيديو (TikTok / Instagram Reel / YouTube)', 'nameEN' => 'URL', 'type' => 'text', 'id' => 'url' ),
				array( 'name' => 'الصورة المصغرة', 'nameEN' => 'Thumbnail', 'type' => 'file', 'id' => 'thumb' ),
			),
		),
	),
);
$metaboxes['images'] = array(
	'name'   => 'صور القالب',
	'nameEN' => 'Images',
	'type'   => 'fields',
	'icon'   => '<i class="fa-regular fa-images"></i>',
	'fields' => array(
		array( 'name' => 'ارفع صور الورشة الحقيقية لاستبدال الصور الافتراضية. اترك الحقل فارغًا لاستخدام صورة القالب.', 'nameEN' => 'Info', 'type' => 'title', 'id' => '' ),
		array( 'name' => 'صورة الهيرو (ديسكتوب) 1920×1080', 'nameEN' => 'hero-workshop', 'type' => 'file', 'id' => 'img_hero-workshop' ),
		array( 'name' => 'صورة الهيرو (موبايل) 900×1300', 'nameEN' => 'hero-mobile', 'type' => 'file', 'id' => 'img_hero-mobile' ),
		array( 'name' => 'خدمة: فحص كمبيوتر', 'nameEN' => 'svc-diagnostics', 'type' => 'file', 'id' => 'img_svc-diagnostics' ),
		array( 'name' => 'خدمة: الصيانات الدورية', 'nameEN' => 'svc-maintenance', 'type' => 'file', 'id' => 'img_svc-maintenance' ),
		array( 'name' => 'خدمة: فتيس DSG', 'nameEN' => 'svc-gearbox', 'type' => 'file', 'id' => 'img_svc-gearbox' ),
		array( 'name' => 'خدمة: برمجة وتكويد', 'nameEN' => 'svc-coding', 'type' => 'file', 'id' => 'img_svc-coding' ),
		array( 'name' => 'خدمة: العفشة والفرامل', 'nameEN' => 'svc-brakes', 'type' => 'file', 'id' => 'img_svc-brakes' ),
		array( 'name' => 'خدمة: التبريد والمحرك', 'nameEN' => 'svc-cooling', 'type' => 'file', 'id' => 'img_svc-cooling' ),
		array( 'name' => 'بوابة VAG', 'nameEN' => 'brand-vag', 'type' => 'file', 'id' => 'img_brand-vag' ),
		array( 'name' => 'بوابة مرسيدس', 'nameEN' => 'brand-mercedes', 'type' => 'file', 'id' => 'img_brand-mercedes' ),
		array( 'name' => 'بوابة BMW', 'nameEN' => 'brand-bmw', 'type' => 'file', 'id' => 'img_brand-bmw' ),
		array( 'name' => 'بوابة بورش وأوبل', 'nameEN' => 'brand-porsche-opel', 'type' => 'file', 'id' => 'img_brand-porsche-opel' ),
		array( 'name' => 'صفحة فحص قبل الشراء', 'nameEN' => 'pre-purchase', 'type' => 'file', 'id' => 'img_pre-purchase' ),
		array( 'name' => 'صفحة عن المركز (صورة الورشة)', 'nameEN' => 'workshop-about', 'type' => 'file', 'id' => 'img_workshop-about' ),
		array( 'name' => 'صورة المشاركة على السوشيال 1200×630', 'nameEN' => 'og-cover', 'type' => 'file', 'id' => 'img_og-cover' ),
	),
);
$metaboxes['Footer'] = array(
	'name'   => 'الفوتر',
	'nameEN' => 'Footer',
	'type'   => 'fields',
	'icon'   => '<i class="fa-regular fa-id-card-clip"></i>',
	'fields' => array(
		array( 'name' => 'لوجو الفوتر (اختياري)', 'nameEN' => 'Footer logo', 'type' => 'file', 'id' => 'logoFooter' ),
		array( 'name' => 'نبذة عن المركز', 'nameEN' => 'About', 'type' => 'textarea', 'id' => 'footer_about' ),
		array( 'name' => 'نص الحقوق ({year} و {sitename})', 'nameEN' => 'Copyright', 'type' => 'text', 'id' => 'copyright_text' ),
		array( 'name' => 'كلمة التطوير (افتراضي: تصميم وتطوير)', 'nameEN' => 'Developer label', 'type' => 'text', 'id' => 'dev_label' ),
		array( 'name' => 'اسم جهة التطوير', 'nameEN' => 'Developer name', 'type' => 'text', 'id' => 'dev_name' ),
		array( 'name' => 'رابط جهة التطوير', 'nameEN' => 'Developer link', 'type' => 'text', 'id' => 'dev_url' ),
		array( 'name' => 'إخفاء سطر التطوير', 'nameEN' => 'Hide developer credit', 'type' => 'checkbox', 'id' => 'hide_dev_credit' ),
	),
);
$metaboxes['tracking'] = array(
	'name'   => 'أكواد التتبع',
	'nameEN' => 'Tracking codes',
	'type'   => 'fields',
	'icon'   => '<i class="fas fa-code"></i>',
	'fields' => array(
		array( 'name' => 'إخفاء كود الهيدر (ديسكتوب)', 'nameEN' => 'Hide desktop code', 'type' => 'checkbox', 'id' => 'show_ads_header' ),
		array( 'name' => 'كود الهيدر — Google Analytics / Pixel (ديسكتوب)', 'nameEN' => 'Header code', 'type' => 'textarea_code', 'id' => 'ads_header' ),
		array( 'name' => 'إخفاء كود الهيدر (موبايل)', 'nameEN' => 'Hide mobile code', 'type' => 'checkbox', 'id' => 'show_ads_mobile_header' ),
		array( 'name' => 'كود الهيدر (موبايل)', 'nameEN' => 'Mobile header code', 'type' => 'textarea_code', 'id' => 'mobile_ads_header' ),
	),
);
