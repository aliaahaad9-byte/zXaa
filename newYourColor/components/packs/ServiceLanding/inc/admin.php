<?php
/**
 * حقول صفحة الهبوط في شاشة تحرير التصنيف + صفحة الأدوات وزر المحتوى المبدئي.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class ServiceLanding_Admin {

	const NONCE = 'sl_term_fields';

	public static function init() {
		$tax = ServiceLanding::TAXONOMY;
		add_action( $tax . '_edit_form_fields', array( __CLASS__, 'edit_fields' ), 10, 2 );
		add_action( 'edited_' . $tax, array( __CLASS__, 'save' ), 10, 1 );
		add_action( 'admin_menu', array( __CLASS__, 'tools_menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function assets( $hook ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$is_term = ( $screen && isset( $screen->taxonomy ) && $screen->taxonomy === ServiceLanding::TAXONOMY && $screen->base === 'term' );
		$is_tools = ( isset( $_GET['page'] ) && $_GET['page'] === 'sl-tools' );
		if ( ! $is_term && ! $is_tools ) {
			return;
		}
		$base = get_template_directory_uri() . '/components/packs/ServiceLanding/assets/';
		wp_enqueue_style( 'sl-admin', $base . 'admin.css', array(), ServiceLanding::VERSION );
		wp_enqueue_script( 'sl-admin', $base . 'admin.js', array(), ServiceLanding::VERSION, true );
	}

	/* ------------------------------ حقول التصنيف ------------------------------ */

	protected static function text( $tid, $key, $label, $desc = '', $type = 'text' ) {
		$val = ServiceLanding::get( $tid, $key );
		echo '<p class="sl-f"><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label>';
		if ( $type === 'textarea' ) {
			echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="3">' . esc_textarea( $val ) . '</textarea>';
		} else {
			echo '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
		}
		if ( $desc !== '' ) echo '<span class="sl-desc">' . esc_html( $desc ) . '</span>';
		echo '</p>';
	}

	protected static function repeater( $tid, $key, $label, $cols, $labels, $max = 0 ) {
		$rows = get_term_meta( $tid, $key, true );
		if ( ! is_array( $rows ) || empty( $rows ) ) {
			$rows = array( array_fill_keys( $cols, '' ) );
		}
		echo '<div class="sl-rep" data-sl-rep data-key="' . esc_attr( $key ) . '" data-max="' . (int) $max . '">';
		echo '<span class="sl-rep-label">' . esc_html( $label ) . '</span>';
		echo '<div class="sl-rep-rows">';
		$i = 0;
		foreach ( $rows as $row ) {
			echo '<div class="sl-rep-row">';
			echo '<span class="sl-rep-handle">' . esc_html( ServiceLanding::num( $i + 1 ) ) . '</span>';
			foreach ( $cols as $c ) {
				$v = isset( $row[ $c ] ) ? $row[ $c ] : '';
				$ph = isset( $labels[ $c ] ) ? $labels[ $c ] : $c;
				echo '<input type="text" name="' . esc_attr( $key ) . '[' . $i . '][' . esc_attr( $c ) . ']" value="' . esc_attr( $v ) . '" placeholder="' . esc_attr( $ph ) . '" aria-label="' . esc_attr( $ph ) . '">';
			}
			echo '<button type="button" class="sl-rep-del" aria-label="حذف الصف">&times;</button>';
			echo '</div>';
			$i++;
		}
		echo '</div>';
		echo '<button type="button" class="button sl-rep-add">إضافة صف</button>';
		echo '</div>';
	}

	public static function edit_fields( $term ) {
		$tid = $term->term_id;
		wp_nonce_field( self::NONCE, 'sl_nonce' );

		echo '<tr class="form-field sl-wrapper"><th colspan="2">';
		echo '<div class="sl-panel">';

		/* التفعيل */
		echo '<div class="sl-block sl-block--switch">';
		echo '<label class="sl-switch"><input type="checkbox" name="sl_enabled" value="1" ' . checked( ServiceLanding::enabled( $tid ), true, false ) . '>';
		echo '<span>تفعيل صفحة الهبوط لهذا التصنيف</span></label>';
		echo '<p class="sl-desc">عند إيقافه يبقى التصنيف على قالب الأرشيف الحالي بلا أي تغيير.</p>';
		echo '</div>';

		/* 1 المقدمة */
		echo '<fieldset class="sl-block"><legend>1 · المقدمة</legend>';
		self::text( $tid, 'sl_hero_title', 'العنوان الرئيسي' );
		self::text( $tid, 'sl_hero_promise', 'سطر الوعد', '', 'textarea' );
		self::repeater( $tid, 'sl_badges', 'شارات الثقة', array( 'text' ), array( 'text' => 'نص الشارة' ), 6 );
		self::text( $tid, 'sl_cta_call', 'نص زر الاتصال' );
		self::text( $tid, 'sl_cta_whatsapp', 'نص زر الواتساب' );
		self::text( $tid, 'sl_phone', 'رقم الهاتف', 'اتركه فارغًا لاستخدام رقم إعدادات القالب' );
		self::text( $tid, 'sl_whatsapp', 'رقم الواتساب', 'اتركه فارغًا لاستخدام رقم إعدادات القالب' );
		echo '</fieldset>';

		/* النموذج */
		echo '<fieldset class="sl-block"><legend>نصوص النموذج</legend>';
		self::text( $tid, 'sl_form_title', 'عنوان نموذج المقدمة' );
		self::text( $tid, 'sl_form_hint', 'سطر تحت العنوان' );
		self::text( $tid, 'sl_label_name', 'تسمية حقل الاسم' );
		self::text( $tid, 'sl_label_phone', 'تسمية حقل الجوال' );
		self::text( $tid, 'sl_label_city', 'تسمية حقل المدينة' );
		self::text( $tid, 'sl_label_service', 'تسمية حقل الخدمة' );
		self::text( $tid, 'sl_label_details', 'تسمية حقل التفاصيل' );
		self::text( $tid, 'sl_form_submit', 'نص زر الإرسال' );
		self::text( $tid, 'sl_form_note', 'ملاحظة أسفل النموذج' );
		echo '</fieldset>';

		/* 2 الأرقام */
		echo '<fieldset class="sl-block"><legend>2 · شريط الأرقام</legend>';
		self::repeater( $tid, 'sl_stats', 'الأرقام', array( 'value', 'label' ), array( 'value' => 'الرقم', 'label' => 'التسمية' ), 6 );
		echo '<p class="sl-desc">الأرقام تُعرض لاتينية بفاصل آلاف موحّد: 8400 تظهر 8,400.</p>';
		echo '</fieldset>';

		/* 3 المميزات */
		echo '<fieldset class="sl-block"><legend>3 · المميزات</legend>';
		self::text( $tid, 'sl_features_title', 'عنوان القسم' );
		self::text( $tid, 'sl_features_sub', 'وصف القسم' );
		self::repeater( $tid, 'sl_features', 'المميزات', array( 'icon', 'title', 'text', 'metric' ),
			array( 'icon' => 'كلاس الأيقونة', 'title' => 'العنوان', 'text' => 'الوصف', 'metric' => 'الوعد القابل للقياس' ), 12 );
		echo '</fieldset>';

		/* 4 الخطوات */
		echo '<fieldset class="sl-block"><legend>4 · خطوات العمل</legend>';
		self::text( $tid, 'sl_steps_title', 'عنوان القسم' );
		self::text( $tid, 'sl_steps_sub', 'وصف القسم' );
		self::repeater( $tid, 'sl_steps', 'الخطوات', array( 'title', 'text' ), array( 'title' => 'عنوان الخطوة', 'text' => 'الوصف' ), 8 );
		echo '<p class="sl-desc">الخطوة الأخيرة تُعرض باللون الأخضر تلقائيًا كنقطة إنجاز.</p>';
		echo '</fieldset>';

		/* 5 المدن */
		echo '<fieldset class="sl-block"><legend>5 · المدن</legend>';
		self::text( $tid, 'sl_cities_title', 'عنوان القسم' );
		self::text( $tid, 'sl_cities_sub', 'وصف القسم' );
		$saved = get_term_meta( $tid, 'sl_cities', true );
		$saved = is_array( $saved ) ? array_map( 'intval', $saved ) : array();
		$all   = get_terms( array( 'taxonomy' => ServiceLanding::CITY_TAX, 'hide_empty' => false ) );
		echo '<div class="sl-checks" data-sl-checkall>';
		if ( ! is_wp_error( $all ) && ! empty( $all ) ) {
			foreach ( $all as $c ) {
				echo '<label><input type="checkbox" name="sl_cities[]" value="' . (int) $c->term_id . '" ' . checked( in_array( (int) $c->term_id, $saved, true ), true, false ) . '> ' . esc_html( $c->name ) . '</label>';
			}
			echo '<button type="button" class="button sl-checkall">تحديد الكل</button>';
		} else {
			echo '<p class="sl-desc">لا توجد مدن في تصنيف المدن بعد.</p>';
		}
		echo '</div>';
		$mode = ServiceLanding::get( $tid, 'sl_city_mode', 'archive' );
		echo '<p class="sl-f"><label for="sl_city_mode">وجهة روابط المدن</label>';
		echo '<select id="sl_city_mode" name="sl_city_mode">';
		echo '<option value="archive" ' . selected( $mode, 'archive', false ) . '>أرشيف المدينة الافتراضي</option>';
		echo '<option value="service" ' . selected( $mode, 'service', false ) . '>رابط الخدمة داخل المدينة</option>';
		echo '</select></p>';
		echo '</fieldset>';

		/* 6 الأسئلة */
		echo '<fieldset class="sl-block"><legend>6 · الأسئلة الشائعة</legend>';
		self::text( $tid, 'sl_faq_title', 'عنوان القسم' );
		self::text( $tid, 'sl_faq_sub', 'وصف القسم' );
		$saved_f = get_term_meta( $tid, 'sl_faqs', true );
		$saved_f = is_array( $saved_f ) ? array_map( 'intval', $saved_f ) : array();
		$faqs    = get_posts( array( 'post_type' => ServiceLanding::FAQ_TYPE, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		echo '<div class="sl-checks sl-checks--tall" data-sl-checkall>';
		if ( ! empty( $faqs ) ) {
			foreach ( $faqs as $f ) {
				echo '<label><input type="checkbox" name="sl_faqs[]" value="' . (int) $f->ID . '" ' . checked( in_array( (int) $f->ID, $saved_f, true ), true, false ) . '> ' . esc_html( $f->post_title ) . '</label>';
			}
			echo '<button type="button" class="button sl-checkall">تحديد الكل</button>';
		} else {
			echo '<p class="sl-desc">لا توجد أسئلة منشورة في نوع محتوى الأسئلة بعد.</p>';
		}
		echo '</div>';
		echo '</fieldset>';

		/* 7 و 8 */
		echo '<fieldset class="sl-block"><legend>7 · نموذج الطلب &nbsp;·&nbsp; 8 · المقالات</legend>';
		self::text( $tid, 'sl_order_title', 'عنوان قسم الطلب' );
		self::text( $tid, 'sl_order_sub', 'وصف قسم الطلب' );
		self::text( $tid, 'sl_posts_title', 'عنوان قسم المقالات' );
		echo '</fieldset>';

		echo '</div></th></tr>';
	}

	/* --------------------------------- الحفظ --------------------------------- */

	public static function save( $term_id ) {
		if ( ! isset( $_POST['sl_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['sl_nonce'] ), self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}

		update_term_meta( $term_id, 'sl_enabled', isset( $_POST['sl_enabled'] ) ? '1' : '' );

		foreach ( ServiceLanding::text_fields() as $key ) {
			$val = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
			update_term_meta( $term_id, $key, $val );
		}

		foreach ( ServiceLanding::repeaters() as $key => $cols ) {
			$rows = array();
			if ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) {
				foreach ( wp_unslash( $_POST[ $key ] ) as $row ) {
					if ( ! is_array( $row ) ) continue;
					$clean = array();
					$has   = false;
					foreach ( $cols as $c ) {
						$v = isset( $row[ $c ] ) ? sanitize_text_field( $row[ $c ] ) : '';
						if ( trim( $v ) !== '' ) $has = true;
						$clean[ $c ] = $v;
					}
					if ( $has ) $rows[] = $clean;
				}
			}
			update_term_meta( $term_id, $key, $rows );
		}

		$cities = isset( $_POST['sl_cities'] ) ? array_map( 'intval', (array) $_POST['sl_cities'] ) : array();
		update_term_meta( $term_id, 'sl_cities', array_values( array_filter( $cities ) ) );

		$faqs = isset( $_POST['sl_faqs'] ) ? array_map( 'intval', (array) $_POST['sl_faqs'] ) : array();
		update_term_meta( $term_id, 'sl_faqs', array_values( array_filter( $faqs ) ) );

		$mode = isset( $_POST['sl_city_mode'] ) ? sanitize_key( $_POST['sl_city_mode'] ) : 'archive';
		update_term_meta( $term_id, 'sl_city_mode', in_array( $mode, array( 'archive', 'service' ), true ) ? $mode : 'archive' );
	}

	/* ------------------------------ صفحة الأدوات ------------------------------ */

	public static function tools_menu() {
		$parent = 'YTS';
		if ( ! menu_page_url( $parent, false ) ) {
			$parent = 'options-general.php';
		}
		add_submenu_page(
			$parent,
			'أدوات صفحات الخدمات',
			'أدوات صفحات الخدمات',
			'manage_categories',
			'sl-tools',
			array( __CLASS__, 'tools_page' )
		);
	}

	/** محتوى مبدئي جاهز للتحرير — يُكتب في الحقول الفارغة فقط */
	protected static function starter( $term ) {
		$name = $term->name;
		return array(
			'text' => array(
				'sl_hero_title'      => $name . ' — خدمة معتمدة بضمان كتابي',
				'sl_hero_promise'    => 'فريق متخصص يصل إليك في الموعد، بأسعار ثابتة معلنة مسبقًا وضمان مكتوب على الخدمة.',
				'sl_cta_call'        => 'اتصل الآن',
				'sl_cta_whatsapp'    => 'واتساب',
				'sl_form_title'      => 'اطلب زيارة معاينة مجانية',
				'sl_form_hint'       => 'نتصل بك خلال 15 دقيقة',
				'sl_label_name'      => 'الاسم',
				'sl_label_phone'     => 'رقم الجوال',
				'sl_label_city'      => 'المدينة',
				'sl_label_service'   => 'نوع الخدمة',
				'sl_label_details'   => 'تفاصيل إضافية',
				'sl_form_submit'     => 'أرسل الطلب',
				'sl_form_note'       => 'بياناتك لا تُشارك مع أي جهة',
				'sl_features_title'  => 'لماذا تختارنا في ' . $name,
				'sl_features_sub'    => 'كل ميزة هنا وعد نستطيع محاسبة أنفسنا عليه.',
				'sl_steps_title'     => 'كيف نعمل',
				'sl_steps_sub'       => 'أربع خطوات تسلّم إحداها الأخرى، من أول اتصال حتى الضمان.',
				'sl_cities_title'    => 'هل نصل إلى مدينتك؟',
				'sl_cities_sub'      => 'نغطي المدن التالية بفرق ميدانية.',
				'sl_faq_title'       => 'أسئلة شائعة',
				'sl_faq_sub'         => 'أكثر ما يسأل عنه عملاؤنا قبل الطلب.',
				'sl_order_title'     => 'اطلب عرض سعر مكتوب',
				'sl_order_sub'       => 'أرسل بياناتك ونرد عليك بعرض سعر واضح بلا رسوم مفاجئة.',
				'sl_posts_title'     => 'مقالات ' . $name,
			),
			'rep' => array(
				'sl_badges' => array(
					array( 'text' => 'ترخيص ساري' ),
					array( 'text' => 'ضمان مكتوب' ),
					array( 'text' => 'خدمة 24 ساعة' ),
				),
				'sl_stats' => array(
					array( 'value' => '12',   'label' => 'سنة خبرة' ),
					array( 'value' => '8400', 'label' => 'عميل خدمناه' ),
					array( 'value' => '24',   'label' => 'مدينة نغطيها' ),
					array( 'value' => '6',    'label' => 'أشهر ضمان' ),
				),
				'sl_features' => array(
					array( 'icon' => 'fa-regular fa-clock',        'title' => 'وصول سريع',           'text' => 'فرق موزّعة على الأحياء، ونلتزم بموعد الوصول المتفق عليه.', 'metric' => 'خلال ساعتين' ),
					array( 'icon' => 'fa-solid fa-shield-halved',  'title' => 'ضمان كتابي موثّق',   'text' => 'عقد مكتوب يُسلَّم لك بعد تنفيذ الخدمة مباشرة.',           'metric' => '6 أشهر ضمان' ),
					array( 'icon' => 'fa-solid fa-location-dot',   'title' => 'تغطية واسعة',        'text' => 'فروع ومناديب في المدن الرئيسية داخل المملكة.',            'metric' => '24 مدينة' ),
					array( 'icon' => 'fa-solid fa-user-check',     'title' => 'فنيون معتمدون',      'text' => 'كل فني يحمل شهادة سلامة مهنية ويخضع لاختبار دوري.',       'metric' => '38 فنيًا' ),
					array( 'icon' => 'fa-solid fa-tags',           'title' => 'أسعار ثابتة معلنة',  'text' => 'السعر يُتفق عليه قبل البدء ولا يتغير بعد التنفيذ.',       'metric' => 'بلا رسوم مفاجئة' ),
					array( 'icon' => 'fa-regular fa-star',         'title' => 'متابعة بعد الخدمة',  'text' => 'اتصال تقييم للتأكد من رضاك عن النتيجة.',                  'metric' => 'خلال 7 أيام' ),
				),
				'sl_steps' => array(
					array( 'title' => 'تواصل معنا',      'text' => 'اتصال أو رسالة واتساب، نأخذ منك تفاصيل الطلب وموقعك ووقتك المناسب.' ),
					array( 'title' => 'معاينة وتسعير',   'text' => 'زيارة معاينة مجانية نحدّد فيها نطاق العمل، ثم عرض سعر ثابت مكتوب.' ),
					array( 'title' => 'تنفيذ الخدمة',    'text' => 'فريق متخصص ينفّذ العمل بالأدوات والمواد المعتمدة في الموعد المحدد.' ),
					array( 'title' => 'الضمان والمتابعة', 'text' => 'تسليم عقد الضمان، ثم اتصال متابعة للتأكد من رضاك عن النتيجة.' ),
				),
			),
		);
	}

	public static function tools_page() {
		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_die( 'غير مصرح لك بالوصول لهذه الصفحة.' );
		}

		$done = array();
		if ( isset( $_POST['sl_seed_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['sl_seed_nonce'] ), 'sl_seed' ) ) {
			$targets = isset( $_POST['sl_seed_terms'] ) ? array_map( 'intval', (array) $_POST['sl_seed_terms'] ) : array();
			foreach ( $targets as $tid ) {
				$term = get_term( $tid, ServiceLanding::TAXONOMY );
				if ( ! $term || is_wp_error( $term ) ) continue;
				$done[ $term->name ] = self::seed( $term );
			}
		}

		$terms = get_terms( array( 'taxonomy' => ServiceLanding::TAXONOMY, 'hide_empty' => false ) );
		?>
		<div class="wrap sl-tools" dir="rtl">
			<h1>أدوات صفحات الخدمات</h1>
			<p class="sl-desc">يملأ الحقول <strong>الفارغة فقط</strong> بمحتوى مبدئي جاهز للتحرير. أي حقل كتبته لن يُمس.</p>

			<?php if ( ! empty( $done ) ) : ?>
				<div class="notice notice-success"><p>
				<?php foreach ( $done as $name => $count ) : ?>
					<strong><?php echo esc_html( $name ); ?></strong>: تمت تعبئة <?php echo esc_html( ServiceLanding::num( $count ) ); ?> حقلًا فارغًا.<br>
				<?php endforeach; ?>
				</p></div>
			<?php endif; ?>

			<form method="post">
				<?php wp_nonce_field( 'sl_seed', 'sl_seed_nonce' ); ?>
				<div class="sl-checks sl-checks--tall" data-sl-checkall>
					<?php if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
						<?php foreach ( $terms as $t ) : ?>
							<label>
								<input type="checkbox" name="sl_seed_terms[]" value="<?php echo (int) $t->term_id; ?>">
								<?php echo esc_html( $t->name ); ?>
								<?php if ( ServiceLanding::enabled( $t->term_id ) ) : ?>
									<span class="sl-on">مفعّل</span>
								<?php endif; ?>
							</label>
						<?php endforeach; ?>
						<button type="button" class="button sl-checkall">تحديد الكل</button>
					<?php else : ?>
						<p class="sl-desc">لا توجد تصنيفات بعد.</p>
					<?php endif; ?>
				</div>
				<p><button type="submit" class="button button-primary">املأ الحقول الفارغة بمحتوى مبدئي</button></p>
			</form>
		</div>
		<?php
	}

	/** يكتب فقط في الحقول الفارغة ويعيد عدد ما مُلئ */
	protected static function seed( $term ) {
		$tid  = $term->term_id;
		$data = self::starter( $term );
		$n    = 0;

		foreach ( $data['text'] as $key => $val ) {
			if ( ServiceLanding::get( $tid, $key ) === '' ) {
				update_term_meta( $tid, $key, $val );
				$n++;
			}
		}
		foreach ( $data['rep'] as $key => $rows ) {
			$existing = get_term_meta( $tid, $key, true );
			if ( ! is_array( $existing ) || empty( $existing ) ) {
				update_term_meta( $tid, $key, $rows );
				$n++;
			}
		}
		if ( ServiceLanding::get( $tid, 'sl_city_mode' ) === '' ) {
			update_term_meta( $tid, 'sl_city_mode', 'archive' );
			$n++;
		}
		return $n;
	}
}
