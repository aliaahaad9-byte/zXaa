<?php
/**
 * Deutsche Garage — content data (Arabic, with the technical German/English
 * terms owners of these cars search for). Edit here to change site copy.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function dg_default_pages() {
	return array(
		'vag-group'               => array( 'title' => 'صيانة فولكس فاجن وأودي وسكودا وسيات بالإسكندرية', 'model' => 'brand-vag', 'order' => 1 ),
		'mercedes-benz'           => array( 'title' => 'مركز صيانة مرسيدس بنز في الإسكندرية', 'model' => 'brand-mercedes', 'order' => 2 ),
		'bmw-mini'                => array( 'title' => 'صيانة بي إم دبليو وميني في الإسكندرية', 'model' => 'brand-bmw', 'order' => 3 ),
		'porsche-opel'            => array( 'title' => 'صيانة بورش وأوبل في الإسكندرية', 'model' => 'brand-porsche-opel', 'order' => 4 ),
		'pre-purchase-inspection' => array( 'title' => 'فحص السيارات قبل الشراء في الإسكندرية', 'model' => 'pre-purchase', 'order' => 5 ),
		'warning-lights'          => array( 'title' => 'دليل لمبات الأعطال في الطابلوه', 'model' => 'warning-lights', 'order' => 6 ),
		'maintenance-schedule'    => array( 'title' => 'جداول الصيانة الدورية للسيارات الألمانية', 'model' => 'maintenance', 'order' => 7 ),
		'booking'                 => array( 'title' => 'احجز موعد صيانة واطلب تسعيرة فورية', 'model' => 'booking', 'order' => 8 ),
		'cases'                   => array( 'title' => 'حالات إصلاح حقيقية من داخل الورشة', 'model' => 'cases', 'order' => 9 ),
		'about'                   => array( 'title' => 'عن دويتش جراج وجولة داخل الورشة', 'model' => 'about', 'order' => 10 ),
		'blog'                    => array( 'title' => 'المدونة ومركز المعرفة', 'model' => 'blog', 'order' => 11 ),
		'faq'                     => array( 'title' => 'الأسئلة الشائعة', 'model' => 'faq_model', 'order' => 12, 'content' => 'إجابات مختصرة على أكثر الأسئلة التي تصلنا من ملاك السيارات الألمانية في الإسكندرية.' ),
		'contact'                 => array( 'title' => 'تواصل معنا — موقع الورشة في سيدي جابر', 'model' => 'contact', 'order' => 13 ),
	);
}

/** Supported makes (shown as a strip of wordmarks — plain text, not trademarks' logos). */
function dg_brands() {
	return array(
		array( 'name' => 'Mercedes-Benz', 'ar' => 'مرسيدس',     'hub' => 'brand-mercedes' ),
		array( 'name' => 'BMW',           'ar' => 'بي إم دبليو', 'hub' => 'brand-bmw' ),
		array( 'name' => 'MINI',          'ar' => 'ميني',        'hub' => 'brand-bmw' ),
		array( 'name' => 'Volkswagen',    'ar' => 'فولكس فاجن',  'hub' => 'brand-vag' ),
		array( 'name' => 'Audi',          'ar' => 'أودي',        'hub' => 'brand-vag' ),
		array( 'name' => 'Škoda',         'ar' => 'سكودا',       'hub' => 'brand-vag' ),
		array( 'name' => 'SEAT',          'ar' => 'سيات',        'hub' => 'brand-vag' ),
		array( 'name' => 'Porsche',       'ar' => 'بورش',        'hub' => 'brand-porsche-opel' ),
		array( 'name' => 'Opel',          'ar' => 'أوبل',        'hub' => 'brand-porsche-opel' ),
	);
}

function dg_why() {
	return array(
		array( 'icon' => 'cpu',    'title' => 'أجهزة تشخيص وبرمجة أصلية', 'text' => 'ODIS لمجموعة VAG، و Xentry لمرسيدس، و ISTA لـ BMW — نقرأ الأعطال من مصدرها بدل التخمين.' ),
		array( 'icon' => 'pin',    'title' => 'في قلب سيدي جابر',          'text' => '16 خلف شارع بورسعيد — بدون مشوار للمناطق الصناعية البعيدة، وقريبون من سموحة وكليوباترا ورشدي.' ),
		array( 'icon' => 'receipt','title' => 'السعر قبل ما نبدأ',          'text' => 'تشخيص مكتوب وتسعيرة واضحة توافق عليها أولًا. لا بنود مفاجئة في الفاتورة.' ),
		array( 'icon' => 'shield', 'title' => 'قطع أصلية OEM وضمان',       'text' => 'قطع غيار أصلية أو OEM معتمدة، مع ضمان مكتوب على الإصلاح وتقرير قبل وبعد.' ),
	);
}

/** Six core services. */
function dg_services() {
	return array(
		array( 'slot' => 'svc-diagnostics', 'icon' => 'scan',   'title' => 'فحص كمبيوتر متقدم',          'en' => 'ECU Diagnostics',   'text' => 'قراءة كل الكنترولات بأجهزة الوكالة، وتقرير مكتوب بالأعطال وأولوية إصلاحها.', 'need' => 'فحص كمبيوتر' ),
		array( 'slot' => 'svc-maintenance', 'icon' => 'oil',    'title' => 'الصيانات الدورية',            'en' => 'Periodic Service',  'text' => 'Service A/B لمرسيدس، و CBS لـ BMW، وصيانات VAG بزيوت وفلاتر معتمدة لكل موديل.',  'need' => 'صيانة دورية' ),
		array( 'slot' => 'svc-gearbox',     'icon' => 'gear',   'title' => 'فتيس DSG والميكاترونيكس',     'en' => 'DSG · Mechatronics', 'text' => 'تغيير زيت DSG، إصلاح وبرمجة الميكاترونيكس، وتعليم الفتيس 7G/9G-Tronic.',     'need' => 'مشكلة في الفتيس' ),
		array( 'slot' => 'svc-coding',      'icon' => 'chip',   'title' => 'برمجة وتكويد',                'en' => 'ECU Coding',        'text' => 'تكويد القطع الجديدة، تفعيل الخصائص، وتحديث سوفت وير الكنترولات.',                'need' => 'برمجة وتكويد' ),
		array( 'slot' => 'svc-brakes',      'icon' => 'disc',   'title' => 'العفشة والفرامل',             'en' => 'Suspension · Brakes','text' => 'العفشة الهوائية والإلكترونية، تيل وطنابير، ومنظومة ABS/ESP.',                    'need' => 'عفشة وفرامل' ),
		array( 'slot' => 'svc-cooling',     'icon' => 'temp',   'title' => 'التبريد والمحرك',             'en' => 'Cooling · Engine',  'text' => 'طلمبات المياه، تسريبات الزيت والتبريد، ومحركات TSI/TFSI و TwinPower Turbo.',       'need' => 'تبريد / محرك' ),
	);
}

/** Interactive warning-light guide. level: red = stop now, amber = book soon. */
function dg_warning_lights() {
	return array(
		'check-engine' => array( 'title' => 'لمبة المحرك (Check Engine)', 'level' => 'amber', 'icon' => 'wl-engine',
			'causes' => array( 'حساس أكسجين أو حساس هواء MAF', 'بوجيهات أو موبينة ضعيفة (Misfire)', 'غطاء التانك غير محكم', 'مشكلة في الشكمان أو حساسات الانبعاثات' ),
			'advice' => 'لو اللمبة ثابتة: احجز فحص قريب. لو بتفلش: قلّل السرعة وتوجّه للمركز فورًا لأن المحرك بيضرب.' ),
		'epc' => array( 'title' => 'لمبة EPC (فولكس / أودي / سكودا / سيات)', 'level' => 'amber', 'icon' => 'wl-epc',
			'causes' => array( 'بوابة الهواء (Throttle Body)', 'حساس دواسة البنزين', 'حساس لمبة الفرامل', 'أعطال كهرباء في كنترول المحرك' ),
			'advice' => 'غالبًا هتلاحظ ضعف في السحب. ينفع توصل للمركز بهدوء، والتشخيص بجهاز ODIS بيحدد السبب بدقة.' ),
		'gearbox' => array( 'title' => 'عطل الفتيس (Gearbox Malfunction)', 'level' => 'red', 'icon' => 'wl-gear',
			'causes' => array( 'وحدة الميكاترونيكس في فتيس DSG', 'حرارة أو نقص زيت الفتيس', 'حساسات السرعة داخل الفتيس', 'فقد الاتصال مع كنترول الفتيس' ),
			'advice' => 'السيارة ممكن تدخل وضع الطوارئ (Limp Mode). تجنّب القيادة لمسافات طويلة واطلب الفحص فورًا.' ),
		'coolant' => array( 'title' => 'حرارة / نقص مياه التبريد', 'level' => 'red', 'icon' => 'wl-coolant',
			'causes' => array( 'تسريب في الخراطيم أو الرادياتير', 'طلمبة مياه تالفة (شائعة في TSI و BMW)', 'ثرموستات معلّق', 'مروحة التبريد لا تعمل' ),
			'advice' => 'أوقف السيارة فورًا واطفي المحرك. لا تفتح غطاء الرادياتير وهو سخن. كلّمنا ونوجّهك.' ),
		'oil' => array( 'title' => 'ضغط الزيت', 'level' => 'red', 'icon' => 'wl-oil',
			'causes' => array( 'نقص شديد في الزيت', 'طلمبة زيت ضعيفة', 'حساس ضغط الزيت', 'زيت غير مطابق للمواصفة' ),
			'advice' => 'أوقف المحرك فورًا — القيادة بدون ضغط زيت تتلف المحرك خلال دقائق.' ),
		'abs' => array( 'title' => 'لمبة ABS / ESP', 'level' => 'amber', 'icon' => 'wl-abs',
			'causes' => array( 'حساس سرعة عجلة', 'وحدة ABS', 'ضعف البطارية أو الجهد', 'حساس زاوية الدريكسيون' ),
			'advice' => 'الفرامل العادية تعمل، لكن منظومة منع الانغلاق متوقفة. قُد بحذر واحجز فحص.' ),
		'battery' => array( 'title' => 'الشحن / البطارية', 'level' => 'amber', 'icon' => 'wl-battery',
			'causes' => array( 'دينامو لا يشحن', 'بطارية ضعيفة أو غير مسجّلة (BMW / مرسيدس)', 'سير المكنة', 'أطراف بطارية مؤكسدة' ),
			'advice' => 'السيارة شغالة على البطارية فقط وممكن تقف. اطفي الأحمال وتوجّه للمركز مباشرة.' ),
		'brake' => array( 'title' => 'الفرامل / زيت الفرامل', 'level' => 'red', 'icon' => 'wl-brake',
			'causes' => array( 'نقص زيت الفرامل', 'تيل فرامل وصل للحد الأدنى', 'فرامل اليد مشدودة', 'تسريب في المنظومة' ),
			'advice' => 'لو الفرامل حاسس إنها أضعف: لا تقود السيارة. اتصل بنا للتوجيه.' ),
		'tpms' => array( 'title' => 'ضغط الإطارات (TPMS)', 'level' => 'amber', 'icon' => 'wl-tire',
			'causes' => array( 'إطار ضغطه منخفض', 'حساس ضغط بطارية ضعيفة', 'بعد تغيير الكاوتش بدون إعادة ضبط' ),
			'advice' => 'افحص الضغط في أقرب بنزينة. لو اللمبة استمرت، نعيد تعليم الحساسات بالكمبيوتر.' ),
	);
}

/** Maintenance schedules per group and mileage milestone (approximate, per manual). */
function dg_schedules() {
	$common = array(
		10  => array( 'زيت محرك معتمد بالمواصفة (VW 504/507 – MB 229.5 – BMW LL-01)', 'فلتر زيت', 'فحص كمبيوتر شامل', 'فحص الفرامل والإطارات', 'فحص السوائل والتسريبات' ),
		30  => array( 'كل بنود 10 آلاف', 'فلتر هواء المحرك', 'فلتر التكييف', 'فحص البطارية والشحن', 'فحص العفشة والمقصات' ),
		60  => array( 'كل بنود 30 ألف', 'بوجيهات (بنزين)', 'زيت فرامل', 'فلتر بنزين حسب الموديل', 'فحص سيور المكنة' ),
		100 => array( 'كل بنود 60 ألف', 'سائل التبريد وفحص طلمبة المياه', 'فحص سير/جنزير الكاتينة', 'تنظيف بوابة الهواء', 'فحص شامل للمحرك والفتيس' ),
	);
	return array(
		'vag' => array( 'label' => 'VAG — فولكس / أودي / سكودا / سيات', 'extra' => array( 60 => array( 'زيت وفلتر فتيس DSG (DQ250 / DQ381)' ), 100 => array( 'فحص الميكاترونيكس وكلاتش DSG' ) ) ),
		'mercedes' => array( 'label' => 'مرسيدس بنز', 'extra' => array( 10 => array( 'Service A حسب ASSYST' ), 30 => array( 'Service B' ), 60 => array( 'زيت فتيس 7G / 9G-Tronic' ), 100 => array( 'فحص العفشة الهوائية Airmatic' ) ) ),
		'bmw' => array( 'label' => 'BMW وميني', 'extra' => array( 10 => array( 'تصفير CBS بالكمبيوتر' ), 60 => array( 'فحص جوان غطاء البلوف وفلتر الزيت (تسريبات)' ), 100 => array( 'زيت فتيس ZF 8HP وطلمبة مياه كهربائية' ) ) ),
		'porsche-opel' => array( 'label' => 'بورش وأوبل', 'extra' => array( 30 => array( 'فحص منظومة التعليق الرياضي' ), 60 => array( 'زيت فتيس PDK / أوتوماتيك' ) ) ),
		'_common' => $common,
	);
}

/** Brand hub pages (landing pages per make group). */
function dg_hubs() {
	return array(
		'vag' => array(
			'slot'   => 'brand-vag',
			'kicker' => 'VAG Group Hub',
			'h1'     => 'خبراء مجموعة فولكس فاجن وأودي وسكودا وسيات في الإسكندرية',
			'lead'   => 'تخصص كامل في سيارات VW و Audi و Skoda و Seat: فتيس DSG والميكاترونيكس، محركات TSI/TFSI، طلمبات المياه، والفحص بجهاز ODIS الأصلي.',
			'cta'    => 'اطلب فحص كمبيوتر ODIS',
			'brand'  => 'Volkswagen',
			'tool'   => 'ODIS',
			'models' => array( 'Golf', 'Passat', 'Tiguan', 'Jetta', 'A3', 'A4', 'A6', 'Q5', 'Octavia', 'Superb', 'Kodiaq', 'Leon', 'Ibiza' ),
			'items'  => array(
				array( 'gear', 'فتيس DSG والميكاترونيكس', 'تشخيص رعشة التحويل، تغيير زيت DSG بالمواصفة، إصلاح وبرمجة وحدة الميكاترونيكس وتعليم الكلاتش (Basic Settings).' ),
				array( 'engine', 'محركات TSI / TFSI', 'استهلاك الزيت، الكاتينة، الحقن المباشر وتنظيف البلوف، وضغط التيربو.' ),
				array( 'temp', 'طلمبة المياه والتبريد', 'عطل شائع في محركات EA888 — نكشف التسريب ونغيّر الطلمبة بقطعة OEM.' ),
				array( 'scan', 'فحص ODIS الأصلي', 'قراءة كل الكنترولات وتقرير مكتوب بالأولويات قبل أي إصلاح.' ),
			),
			'faq'    => array(
				array( 'كل قد إيه أغيّر زيت فتيس DSG؟', 'غالبًا كل 60 ألف كم لفتيس DQ250 الرطب، وفتيس DQ200 الجاف لا يحتاج تغيير زيت دوري لكن يحتاج فحص الميكاترونيكس. نحدد النوع من رقم الشاسيه.' ),
				array( 'لمبة EPC نورت، أقدر أكمّل سواقة؟', 'غالبًا تقدر توصل للمركز بهدوء، لكن السحب هيكون ضعيف. التشخيص بجهاز ODIS بيحدد السبب في دقائق.' ),
			),
		),
		'mercedes' => array(
			'slot'   => 'brand-mercedes',
			'kicker' => 'Mercedes-Benz',
			'h1'     => 'عناية فائقة بسيارتك مرسيدس بنز بأحدث أجهزة التشخيص الألمانية',
			'lead'   => 'بديل احترافي وموثوق للتوكيل في سيدي جابر وسموحة: صيانات Service A & B، برمجة 7G/9G-Tronic، العفشة الهوائية، والفحص بجهاز Xentry.',
			'cta'    => 'احجز صيانة مرسيدس',
			'brand'  => 'Mercedes-Benz',
			'tool'   => 'Xentry',
			'models' => array( 'C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE', 'CLA', 'A-Class', 'ML', 'GLA' ),
			'items'  => array(
				array( 'oil', 'Service A & B', 'تنفيذ الصيانة حسب نظام ASSYST بزيوت MB 229.5 وتصفير العداد بالكمبيوتر.' ),
				array( 'gear', 'فتيس 7G / 9G-Tronic', 'تغيير الزيت والفلتر، إصلاح الكوندكتور بليت، وتعليم الفتيس بالبرمجة.' ),
				array( 'disc', 'Airmatic والعفشة', 'كشف تسريب العفشة الهوائية، الكمبروسر، والعفشة الإلكترونية ADS.' ),
				array( 'scan', 'فحص Xentry', 'تشخيص كامل لكل الوحدات مع تقرير مكتوب قبل أي إصلاح.' ),
			),
			'faq'    => array(
				array( 'إيه الفرق بين Service A و Service B؟', 'Service A صيانة أساسية (زيت وفلتر وفحص)، و Service B أشمل وتضيف فلاتر الهواء والتكييف وزيت الفرامل وفحص أعمق.' ),
				array( 'العربية واقعة من ورا (العفشة الهوائية)، ده خطر؟', 'غالبًا تسريب في مخدة هوائية أو بلف. لا تقُد لمسافات طويلة لأن الكمبروسر هيشتغل باستمرار ويتلف.' ),
			),
		),
		'bmw' => array(
			'slot'   => 'brand-bmw',
			'kicker' => 'BMW & MINI',
			'h1'     => 'أداء رياضي آمن لسيارتك BMW بأيدي مهندسين متخصصين',
			'lead'   => 'صيانة محركات TwinPower Turbo، علاج تسريبات الزيت والتبريد، برمجة الكنترولات بجهاز ISTA، ومنظومة iDrive والفرامل — لـ BMW و MINI.',
			'cta'    => 'احجز فحص وصيانة BMW',
			'brand'  => 'BMW',
			'tool'   => 'ISTA',
			'models' => array( '3 Series', '5 Series', '7 Series', 'X1', 'X3', 'X5', 'X6', 'MINI Cooper', 'Countryman' ),
			'items'  => array(
				array( 'engine', 'محركات TwinPower Turbo', 'N20 / B48 / N55 / B58: الكاتينة، الحقن، وضغط التيربو.' ),
				array( 'drop', 'تسريبات الزيت والتبريد', 'جوان غطاء البلوف وقاعدة فلتر الزيت، وطلمبة المياه الكهربائية.' ),
				array( 'chip', 'برمجة ISTA', 'تسجيل البطارية، تكويد القطع الجديدة، وتحديث الكنترولات.' ),
				array( 'disc', 'الفرامل و iDrive', 'تيل وطنابير بحساسات، وأعطال الشاشة ومنظومة iDrive.' ),
			),
			'faq'    => array(
				array( 'ليه لازم أسجّل البطارية الجديدة في BMW؟', 'نظام إدارة الطاقة بيشحن البطارية حسب عمرها. لو ما اتسجلتش بتتشحن غلط وعمرها يقل. بنسجلها بجهاز ISTA.' ),
				array( 'ريحة زيت محروق من الكبوت، السبب إيه؟', 'غالبًا تسريب من جوان غطاء البلوف أو قاعدة الفلتر على الشكمان. مشكلة شائعة ويُفضّل إصلاحها بدري.' ),
			),
		),
		'porsche-opel' => array(
			'slot'   => 'brand-porsche-opel',
			'kicker' => 'Porsche & Opel',
			'h1'     => 'تكنولوجيا تشخيص ألمانية متطورة لسيارات بورش وأوبل',
			'lead'   => 'فحص ميكانيكي وإلكتروني دقيق، برمجة متقدمة، وصيانة المحركات وأنظمة التعليق الرياضي لسيارات Porsche و Opel.',
			'cta'    => 'تواصل مع مهندس الصيانة',
			'brand'  => 'Porsche',
			'tool'   => 'PIWIS',
			'models' => array( 'Cayenne', 'Macan', 'Panamera', '911', 'Astra', 'Insignia', 'Corsa', 'Grandland', 'Mokka' ),
			'items'  => array(
				array( 'scan', 'فحص إلكتروني متقدم', 'قراءة الوحدات وتحليل البيانات الحية لتحديد العطل بدقة.' ),
				array( 'engine', 'صيانة المحركات', 'صيانات دورية وعلاج التسريبات وأعطال الإشعال والحقن.' ),
				array( 'disc', 'التعليق الرياضي', 'PASM والتعليق الهوائي، وضبط الزوايا بعد الإصلاح.' ),
				array( 'chip', 'برمجة وتكويد', 'تكويد القطع الجديدة وتحديث سوفت وير الكنترولات.' ),
			),
			'faq'    => array(
				array( 'بتصلّحوا أوبل الموديلات الجديدة؟', 'نعم، نتعامل مع موديلات أوبل الحديثة فحصًا وبرمجة وصيانة دورية.' ),
				array( 'هل الصيانة عندكم بتأثر على ضمان بورش؟', 'نستخدم قطع أصلية وزيوت بالمواصفة ونوثّق الصيانة بفاتورة وتقرير. لو السيارة تحت ضمان الوكيل نوضّح لك قبل البدء.' ),
			),
		),
	);
}

function dg_prepurchase_checks() {
	return array(
		array( 'paint',  'قياس سمك الدهان رقميًا', 'نكشف أي رش أو معجون في كل قطعة بجهاز قياس رقمي.' ),
		array( 'frame',  'فحص الشاسيه والقوائم', 'نتأكد إن السيارة سليمة من الحوادث الهيكلية.' ),
		array( 'engine', 'كبس المحرك والتسريبات', 'اختبار كبس السلندرات وفحص التيربو والتسريبات.' ),
		array( 'gear',   'اختبار الفتيس', 'تجربة طريق وفحص الفتيس والميكاترونيكس بالكمبيوتر.' ),
		array( 'gauge',  'كشف التلاعب بالعداد', 'مقارنة الكيلومترات المسجلة في أكثر من كنترول.' ),
		array( 'scan',   'فحص كمبيوتر لكل الوحدات', 'سجل الأعطال المخزنة والممسوحة حديثًا.' ),
		array( 'disc',   'العفشة والفرامل والإطارات', 'المقصات، المساعدين، التيل والطنابير وتاريخ الإطارات.' ),
	);
}

function dg_default_faq() {
	return array(
		array( 'هل بتعرفوني التكلفة قبل الإصلاح؟', 'نعم. بعد التشخيص بنرسلك تقرير وتسعيرة واضحة، ولا نبدأ أي شغل غير بموافقتك.' ),
		array( 'بتستخدموا قطع غيار أصلية؟', 'بنركّب قطع أصلية أو OEM معتمدة من نفس مصنّعي الوكالة، وبنسلّمك القطع القديمة لو حبيت.' ),
		array( 'هل في ضمان على الإصلاح؟', 'أيوه، ضمان مكتوب على الإصلاح وقطع الغيار حسب نوع الشغل.' ),
		array( 'محتاج حجز مسبق ولا ينفع آجي على طول؟', 'الحجز المسبق بيوفّر وقتك ويضمن جهاز التشخيص والفني المتخصص جاهزين. للطوارئ كلّمنا مباشرة.' ),
		array( 'فين مكانكم بالظبط؟', '16 خلف شارع بورسعيد، سيدي جابر، الإسكندرية — قريب من سموحة وكليوباترا ورشدي.' ),
	);
}

/** Reels gallery — admin group "reels" overrides these defaults. */
function dg_reels() {
	$items = get_option( 'reels' );
	$out   = array();
	if ( is_array( $items ) ) {
		foreach ( $items as $i => $r ) {
			if ( empty( $r['url'] ) ) { continue; }
			$thumb = isset( $r['thumb'] ) ? $r['thumb'] : '';
			if ( is_array( $thumb ) ) { $thumb = isset( $thumb['url'] ) ? $thumb['url'] : ''; }
			$out[] = array(
				'url'   => $r['url'],
				'title' => isset( $r['title'] ) ? $r['title'] : '',
				'thumb' => $thumb ? $thumb : dg_img( 'reel-' . ( ( $i % 6 ) + 1 ) ),
			);
		}
	}
	if ( $out ) { return $out; }
	$ig = dg_opt( 'instagram', 'https://www.instagram.com/deutsche_garage_/' );
	$tt = dg_opt( 'tiktok', 'https://www.tiktok.com/@deutsche_garage' );
	return array(
		array( 'url' => $tt, 'title' => 'تشخيص عطل بالكمبيوتر خطوة بخطوة', 'thumb' => dg_img( 'reel-1' ) ),
		array( 'url' => $ig, 'title' => 'تغيير زيت فتيس DSG بالمواصفة',     'thumb' => dg_img( 'reel-2' ) ),
		array( 'url' => $tt, 'title' => 'صيانة دورية مرسيدس Service B',     'thumb' => dg_img( 'reel-3' ) ),
		array( 'url' => $ig, 'title' => 'برمجة وتكويد كنترول BMW',          'thumb' => dg_img( 'reel-4' ) ),
		array( 'url' => $tt, 'title' => 'تغيير تيل وطنابير بحساسات',        'thumb' => dg_img( 'reel-5' ) ),
		array( 'url' => $ig, 'title' => 'فحص سيارة قبل الشراء',             'thumb' => dg_img( 'reel-6' ) ),
	);
}
