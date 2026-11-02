<?php
/**
 * الصفحة الرئيسية التسويقية
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$order = AlzOptions::order_link();
$ic    = function ( $n ) {
	echo Alz::icon( $n ); // phpcs:ignore
};

$l = function ( $id, $type, $value, $label = '' ) {
	return array( 'id' => $id, 'type' => $type, 'value' => $value, 'label' => $label, 'is_active' => true );
};
$demo = array(
	array(
		'id'      => 'clinic',
		'tab'     => 'عيادة',
		'profile' => array( 'business_name' => 'عيادة د. سارة للأسنان', 'bio' => 'تجميل وزراعة الأسنان • احجز موعدك بضغطة', 'logo_url' => '', 'cover_url' => '', 'logo_shape' => 'circle', 'theme_primary' => '#0e7490', 'theme_background' => '#f0fdfa', 'theme_text' => '#083344', 'button_style' => 'soft', 'layout' => 'list' ),
		'links'   => array( $l( 1, 'whatsapp', '01000000000', 'احجز على واتساب' ), $l( 2, 'phone', '01000000000', 'اتصل بالعيادة' ), $l( 3, 'maps', 'https://maps.google.com', 'فرع المعادي' ), $l( 4, 'maps', 'https://maps.google.com', 'فرع مدينة نصر' ), $l( 5, 'google_review', 'https://google.com' ), $l( 6, 'instagram', 'qurtap' ), $l( 7, 'instapay', 'clinic@instapay', 'ادفع الكشف بإنستاباي' ) ),
	),
	array(
		'id'      => 'restaurant',
		'tab'     => 'مطعم',
		'profile' => array( 'business_name' => 'مطعم بيت الشاورما', 'bio' => 'أطيب شاورما في المدينة 🔥 توصيل سريع', 'logo_url' => '', 'cover_url' => '', 'logo_shape' => 'square', 'theme_primary' => '#b91c1c', 'theme_background' => '#fff7ed', 'theme_text' => '#431407', 'button_style' => 'brand', 'layout' => 'grid' ),
		'links'   => array( $l( 1, 'menu', 'https://example.com', 'المنيو' ), $l( 2, 'whatsapp', '01000000000', 'اطلب دليفري' ), $l( 3, 'google_review', 'https://google.com', 'قيّمنا' ), $l( 4, 'maps', 'https://maps.google.com' ), $l( 5, 'tiktok', 'qurtap' ), $l( 6, 'facebook', 'qurtap' ), $l( 7, 'instapay', 'https://ipn.eg' ), $l( 8, 'wallet', '01000000000', 'فودافون كاش' ), $l( 9, 'wifi', wp_json_encode( array( 's' => 'Shawarma-Guest', 'p' => 'eat2026' ) ), 'واي فاي' ) ),
	),
	array(
		'id'      => 'lawyer',
		'tab'     => 'مكتب محاماة',
		'profile' => array( 'business_name' => 'مكتب الأستاذ أحمد للمحاماة', 'bio' => 'استشارات قانونية • قضايا مدنية وتجارية', 'logo_url' => '', 'cover_url' => '', 'logo_shape' => 'circle', 'theme_primary' => '#1e293b', 'theme_background' => '#f8fafc', 'theme_text' => '#0f172a', 'button_style' => 'filled', 'layout' => 'list' ),
		'links'   => array( $l( 1, 'phone', '01000000000', 'اتصال مباشر' ), $l( 2, 'whatsapp', '01000000000', 'استشارة واتساب' ), $l( 3, 'email', 'office@example.com' ), $l( 4, 'maps', 'https://maps.google.com', 'مقر المكتب' ), $l( 5, 'website', 'https://example.com' ), $l( 6, 'instapay', 'law@instapay', 'أتعاب الاستشارة' ) ),
	),
	array(
		'id'      => 'market',
		'tab'     => 'سوبر ماركت',
		'profile' => array( 'business_name' => 'ماركت الخير', 'bio' => 'كل احتياجات البيت • توصيل مجاني فوق 300 ج', 'logo_url' => '', 'cover_url' => '', 'logo_shape' => 'square', 'theme_primary' => '#15803d', 'theme_background' => '#f7fee7', 'theme_text' => '#14532d', 'button_style' => 'outline', 'layout' => 'list' ),
		'links'   => array( $l( 1, 'whatsapp', '01000000000', 'اطلب على واتساب' ), $l( 2, 'facebook', 'qurtap', 'عروض الأسبوع' ), $l( 3, 'wallet', '01000000000', 'فودافون كاش' ), $l( 4, 'wallet', '01100000000', 'اتصالات كاش' ), $l( 5, 'instapay', 'market@instapay' ), $l( 6, 'google_review', 'https://google.com' ) ),
	),
);

// الهيرو يُرسم على السيرفر (يظهر بدون جافاسكربت) — الديمو التفاعلي يُرسم بـ renderer.js
$hero = $demo[1];
$hero_profile = (object) $hero['profile'];
$hero_links   = array_map(
	function ( $x ) {
		return (object) $x;
	},
	$hero['links']
);
$defs = Alz::link_defs();
?>
<main class="landing">

	<!-- Hero -->
	<section class="hero">
		<div class="hero__grid-bg"></div>
		<div class="hero__glow"></div>
		<div class="container hero__in">
			<div class="hero__copy">
				<span class="pill"><?php $ic( 'nfc' ); ?> <?php echo esc_html( AlzOptions::get( 'hero_badge' ) ); ?></span>
				<h1><?php echo esc_html( AlzOptions::get( 'hero_title' ) ); ?> <span class="gold-text"><?php echo esc_html( AlzOptions::get( 'hero_highlight' ) ); ?></span></h1>
				<p class="lead"><?php echo esc_html( AlzOptions::get( 'hero_text' ) ); ?></p>
				<ul class="ticks">
					<?php foreach ( array( 'يعمل باللمس NFC ومسح QR', 'تعديل لحظي وغير محدود', 'روابط مكررة (فرعين، حسابين)', 'يفتح التطبيقات مباشرة' ) as $t ) : ?>
						<li><span><?php $ic( 'check' ); ?></span><?php echo esc_html( $t ); ?></li>
					<?php endforeach; ?>
				</ul>
				<div class="hero__btns">
					<a class="btn btn--gold btn--lg" href="<?php echo esc_url( $order ); ?>" target="_blank" rel="noopener">اطلب الاستاند الآن</a>
					<a class="btn btn--glass btn--lg" href="#demo"><?php $ic( 'qr' ); ?> شاهد صفحة تجريبية</a>
				</div>
			</div>
			<div class="hero__phone">
				<span class="float float--a">⭐ +120 تقييم جوجل هذا الشهر</span>
				<span class="float float--b">💸 دفع فوري بإنستاباي</span>
				<span class="ring"></span>
				<div class="phone"><div class="phone__notch"></div><div class="phone__screen" data-static-demo>
					<?php echo Alz::render_profile( $hero_profile, $hero_links ); // phpcs:ignore ?>
				</div></div>
			</div>
		</div>
	</section>

	<!-- Use cases -->
	<section class="uses">
		<div class="container">
			<p class="center muted b">مناسب لكل الأنشطة</p>
			<div class="uses__grid">
				<?php foreach ( array( 'doctor' => 'الأطباء والعيادات', 'briefcase' => 'المحامين والاستشاريين', 'utensils' => 'المطاعم والكافيهات', 'cart' => 'السوبر ماركت', 'building' => 'الشركات', 'store' => 'المحلات التجارية' ) as $k => $v ) : ?>
					<div class="uses__item"><?php $ic( $k ); ?><?php echo esc_html( $v ); ?></div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Comparison -->
	<section class="sec">
		<div class="container">
			<div class="sec__title"><span>ليه QurTap؟</span><h2>الفرق بين الاستاند التقليدي والاستاند الذكي</h2></div>
			<div class="compare">
				<div class="compare__old">
					<h3>الاستاند التقليدي</h3>
					<ul>
						<?php foreach ( array( 'رابط واحد فقط', 'لازم تشتري استاند جديد لو غيرت الرابط', 'استاند للتقييم واستاند للدفع واستاند للسوشيال', 'بدون إحصائيات' ) as $t ) : ?>
							<li><span class="x"><?php $ic( 'x_close' ); ?></span><?php echo esc_html( $t ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="compare__new">
					<span class="tag">الأذكى</span>
					<h3>استاند <?php echo esc_html( Alz::opt( 'brand_name', 'QurTap' ) ); ?></h3>
					<ul>
						<?php foreach ( array( 'كل روابطك وطرق الدفع في صفحة واحدة', 'عدّل المحتوى في أي وقت مجاناً — الاستاند ثابت', 'استاند واحد يغني عن الكل', 'عدد الزيارات والضغطات لكل زر' ) as $t ) : ?>
							<li><span class="v"><?php $ic( 'check' ); ?></span><?php echo esc_html( $t ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- Features -->
	<section class="sec sec--soft" id="features">
		<div class="container">
			<div class="sec__title"><span>المميزات</span><h2>كل قنواتك في مكان واحد</h2><p>سوشيال ميديا، جوجل ريفيو، لوكيشن، إنستاباي ومحافظ إلكترونية — بضغطة واحدة من زبونك.</p></div>
			<div class="channels">
				<?php
				foreach ( array( 'facebook', 'instagram', 'tiktok', 'whatsapp', 'google_review', 'maps', 'instapay', 'wallet', 'phone', 'menu', 'wifi', 'youtube' ) as $t ) :
					$d = $defs[ $t ];
					?>
					<span style="background:<?php echo esc_attr( isset( $d['gradient'] ) ? $d['gradient'] : $d['color'] ); ?>;color:<?php echo esc_attr( $d['on'] ); ?>"><?php echo Alz::icon( $t ); // phpcs:ignore ?><?php echo esc_html( $d['label'] ); ?></span>
				<?php endforeach; ?>
			</div>
			<div class="features">
				<?php
				$features = array(
					array( 'nfc', 'NFC و QR معاً', 'الزبون يقرّب موبايله أو يمسح الكود — الاثنين يفتحوا نفس صفحتك فوراً.' ),
					array( 'pencil', 'تعديل لحظي', 'غيّر الروابط والصور والألوان من لوحة التحكم في أي وقت وبعدد غير محدود.' ),
					array( 'layers', 'روابط مكررة', 'أضف أكثر من لوكيشن لفروعك، أو أكثر من حساب إنستاباي ومحفظة.' ),
					array( 'smartphone', 'يفتح التطبيق مباشرة', 'Deep Linking يفتح انستجرام وتيك توك والخرائط داخل التطبيق وليس المتصفح.' ),
					array( 'zap', 'سرعة فائقة', 'صفحة خفيفة جداً بدون سكربتات خارجية تفتح في أقل من ثانية على أي شبكة.' ),
					array( 'shield', 'حماية كاملة', 'لا أحد يستطيع تعديل صفحتك غيرك، والرابط المطبوع على الاستاند ثابت للأبد.' ),
				);
				foreach ( $features as $f ) :
					?>
					<div class="feature"><span class="feature__ic"><?php $ic( $f[0] ); ?></span><h3><?php echo esc_html( $f[1] ); ?></h3><p><?php echo esc_html( $f[2] ); ?></p></div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Live demo -->
	<section class="demo" id="demo">
		<div class="hero__grid-bg"></div>
		<div class="container demo__in">
			<div>
				<span class="eyebrow">معرض تفاعلي حي</span>
				<h2>جرّب بنفسك كيف تبدو صفحة عميلك</h2>
				<p class="lead">اختار نوع النشاط وغيّر الألوان وشكل الأزرار — هذا بالضبط ما سيراه زبونك عند لمس الاستاند. اضغط على أي زر لتجربته.</p>
				<p class="demo__lbl">نوع النشاط</p>
				<div class="demo__chips" data-demo-biz></div>
				<p class="demo__lbl">شكل الأزرار</p>
				<div class="demo__chips" data-demo-style>
					<button data-v="brand">ألوان المنصات</button><button data-v="filled">لون موحد</button><button data-v="soft">ناعم</button><button data-v="outline">إطار</button>
					<span class="sep"></span>
					<button data-layout="list" aria-label="قائمة"><?php $ic( 'list' ); ?></button><button data-layout="grid" aria-label="شبكة"><?php $ic( 'grid' ); ?></button>
				</div>
				<p class="demo__lbl">لون الهوية</p>
				<div class="demo__colors" data-demo-color>
					<?php foreach ( array( '#0e7490', '#b91c1c', '#1e293b', '#15803d', '#7c3aed', '#d9a521' ) as $c ) : ?>
						<button style="background:<?php echo esc_attr( $c ); ?>" data-c="<?php echo esc_attr( $c ); ?>" aria-label="<?php echo esc_attr( $c ); ?>"></button>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="phone"><div class="phone__notch"></div><div class="phone__screen" id="demoScreen"></div></div>
		</div>
	</section>

	<!-- How -->
	<section class="sec" id="how">
		<div class="container">
			<div class="sec__title"><span>طريقة العمل</span><h2>3 خطوات وتبدأ</h2></div>
			<div class="steps">
				<?php
				foreach ( array(
					array( 'cart', 'اطلب الاستاند', 'اختار التصميم واطلب — يوصلك مطبوع عليه QR وشريحة NFC.' ),
					array( 'qr', 'امسح الكود وفعّله', 'سجّل دخول بحساب جوجل وامسح كود الاستاند — يرتبط بصفحتك تلقائياً.' ),
					array( 'infinity', 'عدّل براحتك', 'أضف روابطك وغيّرها في أي وقت — الاستاند يفضل شغال بنفس الكود.' ),
				) as $n => $s ) :
					?>
					<div class="step"><b class="step__n"><?php echo (int) $n + 1; ?></b><span class="step__ic"><?php $ic( $s[0] ); ?></span><h3><?php echo esc_html( $s[1] ); ?></h3><p><?php echo esc_html( $s[2] ); ?></p></div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Offer CTA -->
	<section class="sec" id="offer">
		<div class="container">
			<div class="offer">
				<span class="pill pill--gold"><?php $ic( 'gift' ); ?> <?php echo esc_html( AlzOptions::get( 'offer_badge' ) ); ?></span>
				<h2><?php echo esc_html( AlzOptions::get( 'offer_title' ) ); ?></h2>
				<p class="lead"><?php echo esc_html( AlzOptions::get( 'offer_text' ) ); ?></p>
				<div class="hero__btns hero__btns--center">
					<a class="btn btn--gold btn--lg" href="<?php echo esc_url( $order ); ?>" target="_blank" rel="noopener">اطلب الاستاند عبر واتساب</a>
					<a class="btn btn--glass btn--lg" href="<?php echo esc_url( Alz::login_url() ); ?>">لدي استاند — فعّله الآن</a>
				</div>
			</div>
		</div>
	</section>
</main>

<div class="toast" id="toast" role="status"></div>
<script>window.ALZ_DEMO = <?php echo wp_json_encode( array_merge( Alz::js_config(), array( 'demo' => $demo ) ) ); ?>;</script>
<script src="<?php echo esc_url( Alz::asset_url( 'js/renderer.js' ) ); ?>" defer></script>
<script src="<?php echo esc_url( Alz::asset_url( 'js/landing.js' ) ); ?>" defer></script>
