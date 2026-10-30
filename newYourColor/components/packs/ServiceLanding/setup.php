<?php
/**
 * ServiceLanding — تحويل تصنيف الخدمة إلى صفحة هبوط.
 *
 * الترتيب يتبع أسئلة العميل الأربعة قبل الاتصال:
 * هل تقدّمون خدمتي؟ (مقدمة + مميزات) → لماذا أثق بكم؟ (أرقام + خطوات)
 * → هل تصلون لمدينتي؟ (المدن) → كم التكلفة؟ (أسئلة + نموذج الطلب)
 *
 * لا يوجد أي نص تسويقي داخل هذا الملف — كل المحتوى من حقول شاشة تحرير التصنيف.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ServiceLanding {

	/** التصنيف الذي تعمل عليه صفحات الهبوط. */
	const TAXONOMY = 'category';

	/** تصنيف المدن الموجود في القالب. */
	const CITY_TAXONOMY = 'country';

	/** نوع المحتوى الموجود للأسئلة. */
	const FAQ_TYPE = 'faq';

	const NONCE = 'svc_landing_term_nonce';

	/**
	 * تعريف الحقول: المفتاح => [التسمية، النوع، الوصف]
	 * النوع: check | text | textarea | rows
	 * rows = عنصر في كل سطر، والحقول داخل السطر مفصولة بـ |
	 */
	public static function fields() {
		return array(
			'svc_enable'        => array( 'تفعيل صفحة الهبوط لهذا التصنيف', 'check', 'بدون التفعيل يبقى التصنيف على قالب الأرشيف الحالي تمامًا.' ),

			'svc_hero_eyebrow'  => array( 'سطر علوي صغير', 'text', 'كلمة أو جملة قصيرة فوق العنوان.' ),
			'svc_hero_title'    => array( 'عنوان المقدمة', 'text', 'اتركه فارغًا لاستخدام اسم التصنيف.' ),
			'svc_hero_promise'  => array( 'سطر الوعد', 'textarea', 'جملة أو جملتان توضّح ما يحصل عليه العميل.' ),
			'svc_hero_badges'   => array( 'شارات الثقة', 'rows', 'شارة في كل سطر.' ),

			'svc_stat_years'    => array( 'سنوات الخبرة', 'text', 'الرقم فقط، مثال: 15' ),
			'svc_stat_years_l'  => array( 'وصف سنوات الخبرة', 'text', '' ),
			'svc_stat_clients'  => array( 'عدد العملاء', 'text', 'الرقم فقط، مثال: 8400' ),
			'svc_stat_clients_l' => array( 'وصف عدد العملاء', 'text', '' ),
			'svc_stat_cities'   => array( 'عدد المدن', 'text', 'اتركه فارغًا ليُحسب تلقائيًا من المدن المختارة.' ),
			'svc_stat_cities_l' => array( 'وصف عدد المدن', 'text', '' ),
			'svc_stat_grt'      => array( 'الضمان', 'text', 'مثال: 6 أشهر' ),
			'svc_stat_grt_l'    => array( 'وصف الضمان', 'text', '' ),

			'svc_features_head' => array( 'عنوان قسم المميزات', 'text', '' ),
			'svc_features_sub'  => array( 'وصف قسم المميزات', 'textarea', '' ),
			'svc_features'      => array( 'المميزات (ست بطاقات)', 'rows', 'في كل سطر: أيقونة | العنوان | الوصف — مثال: fa-solid fa-shield-check | ضمان مكتوب | نعيد الزيارة مجانًا خلال 6 أشهر.' ),

			'svc_steps_head'    => array( 'عنوان قسم خطوات العمل', 'text', '' ),
			'svc_steps_sub'     => array( 'وصف قسم خطوات العمل', 'textarea', '' ),
			'svc_steps'         => array( 'خطوات العمل', 'rows', 'في كل سطر: العنوان | الوصف — الخطوة الأخيرة تظهر باللون الأخضر.' ),

			'svc_cities_head'   => array( 'عنوان قسم المدن', 'text', '' ),
			'svc_cities_sub'    => array( 'وصف قسم المدن', 'textarea', '' ),
			'svc_cities'        => array( 'المدن المعروضة', 'cities', 'اتركها بدون اختيار لعرض كل المدن.' ),
			'svc_cities_link'   => array( 'روابط المدن تفتح صفحة الخدمة داخل المدينة', 'check', 'الافتراضي: رابط أرشيف المدينة نفسه.' ),

			'svc_faq_head'      => array( 'عنوان قسم الأسئلة', 'text', '' ),
			'svc_faq_sub'       => array( 'وصف قسم الأسئلة', 'textarea', '' ),
			'svc_faq'           => array( 'الأسئلة الشائعة', 'rows', 'في كل سطر: السؤال | الجواب — اتركه فارغًا لجلب الأسئلة من قسم الأسئلة في الموقع.' ),

			'svc_order_head'    => array( 'عنوان نموذج الطلب', 'text', '' ),
			'svc_order_sub'     => array( 'وصف نموذج الطلب', 'textarea', '' ),

			'svc_posts_head'    => array( 'عنوان قسم المقالات', 'text', '' ),
		);
	}

	public static function init() {
		add_action( self::TAXONOMY . '_edit_form_fields', array( __CLASS__, 'render_fields' ), 20 );
		add_action( 'edited_' . self::TAXONOMY, array( __CLASS__, 'save_fields' ) );
		add_action( 'admin_menu', array( __CLASS__, 'tools_menu' ) );
		add_action( 'admin_post_svc_seed', array( __CLASS__, 'handle_seed' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
	}

	/* ------------------------------------------------------------------ */
	/* حالة التفعيل                                                        */
	/* ------------------------------------------------------------------ */

	/** الشرط الوحيد الذي يتفرّع به قالب الأرشيف. */
	public static function is_enabled( $term ) {
		if ( ! is_object( $term ) || empty( $term->term_id ) ) {
			return false;
		}
		if ( self::TAXONOMY !== $term->taxonomy ) {
			return false;
		}
		return '1' === (string) get_term_meta( $term->term_id, 'svc_enable', true );
	}

	private static function meta( $term_id, $key ) {
		return (string) get_term_meta( $term_id, $key, true );
	}

	/** تفكيك حقل الأسطر إلى مصفوفة أعمدة. */
	private static function rows( $term_id, $key, $columns = 1 ) {
		$raw = self::meta( $term_id, $key );
		if ( '' === trim( $raw ) ) {
			return array();
		}
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$parts = array_map( 'trim', explode( '|', $line ) );
			$parts = array_pad( $parts, $columns, '' );
			$out[] = $parts;
		}
		return $out;
	}

	/** الأرقام لاتينية بفاصل آلاف لاتيني موحّد (8,400). */
	private static function num( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '/^\d+$/', $value ) ) {
			return number_format( (float) $value, 0, '.', ',' );
		}
		return $value;
	}

	/* ------------------------------------------------------------------ */
	/* حقول شاشة تحرير التصنيف                                            */
	/* ------------------------------------------------------------------ */

	public static function admin_assets( $hook ) {
		if ( 'term.php' !== $hook && 'edit-tags.php' !== $hook && false === strpos( (string) $hook, 'svc-landing-tools' ) ) {
			return;
		}
		wp_add_inline_style( 'common', '
			.svc-adm-title{margin:26px 0 6px;padding-top:16px;border-top:2px solid #dcdcde;font-size:14px;font-weight:700;color:#1d2327}
			.svc-adm-desc{color:#646970;font-size:12.5px;margin:4px 0 0}
			.form-table td textarea.svc-adm-area{width:100%;max-width:640px;min-height:120px;font-family:Menlo,Consolas,monospace;font-size:12.5px;line-height:1.9}
			.svc-adm-cities{display:flex;flex-wrap:wrap;gap:8px;max-width:640px}
			.svc-adm-cities label{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:999px;font-size:12.5px}
			.svc-seed-card{background:#fff;border:1px solid #dcdcde;border-radius:10px;padding:22px;max-width:820px;margin-top:18px}
			.svc-seed-card h2{margin-top:0}
			.svc-seed-list{margin:14px 0 0;padding-inline-start:20px;color:#50575e;line-height:2}
		' );
	}

	public static function render_fields( $term ) {
		wp_nonce_field( self::NONCE, self::NONCE );
		$cities = get_terms( array( 'taxonomy' => self::CITY_TAXONOMY, 'hide_empty' => false ) );
		$chosen = (array) get_term_meta( $term->term_id, 'svc_cities', true );

		echo '<tr><td colspan="2"><p class="svc-adm-title">صفحة هبوط الخدمة (ServiceLanding)</p></td></tr>';

		foreach ( self::fields() as $key => $def ) {
			list( $label, $type, $desc ) = $def;
			$value = self::meta( $term->term_id, $key );

			echo '<tr class="form-field">';
			echo '<th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th>';
			echo '<td>';

			if ( 'check' === $type ) {
				echo '<label><input type="checkbox" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="1" ' . checked( '1', $value, false ) . '> ' . esc_html( $label ) . '</label>';
			} elseif ( 'textarea' === $type ) {
				echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="3" class="svc-adm-area">' . esc_textarea( $value ) . '</textarea>';
			} elseif ( 'rows' === $type ) {
				echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="7" class="svc-adm-area">' . esc_textarea( $value ) . '</textarea>';
			} elseif ( 'cities' === $type ) {
				echo '<div class="svc-adm-cities">';
				foreach ( (array) $cities as $city ) {
					if ( ! is_object( $city ) ) {
						continue;
					}
					echo '<label><input type="checkbox" name="svc_cities[]" value="' . esc_attr( $city->term_id ) . '" ' . checked( true, in_array( (string) $city->term_id, array_map( 'strval', $chosen ), true ), false ) . '> ' . esc_html( $city->name ) . '</label>';
				}
				echo '</div>';
			} else {
				echo '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="regular-text">';
			}

			if ( '' !== $desc ) {
				echo '<p class="svc-adm-desc">' . esc_html( $desc ) . '</p>';
			}
			echo '</td></tr>';
		}
	}

	public static function save_fields( $term_id ) {
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( $_POST[ self::NONCE ] ), self::NONCE ) ) {
			return;
		}

		foreach ( self::fields() as $key => $def ) {
			$type = $def[1];

			if ( 'check' === $type ) {
				update_term_meta( $term_id, $key, isset( $_POST[ $key ] ) ? '1' : '' );
			} elseif ( 'cities' === $type ) {
				$vals = isset( $_POST['svc_cities'] ) ? array_map( 'absint', (array) $_POST['svc_cities'] ) : array();
				update_term_meta( $term_id, 'svc_cities', $vals );
			} elseif ( in_array( $type, array( 'textarea', 'rows' ), true ) ) {
				$val = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
				update_term_meta( $term_id, $key, $val );
			} else {
				$val = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
				update_term_meta( $term_id, $key, $val );
			}
		}
	}

	/* ------------------------------------------------------------------ */
	/* صفحة الأدوات — تعبئة الحقول الفارغة فقط                            */
	/* ------------------------------------------------------------------ */

	public static function tools_menu() {
		add_submenu_page(
			'tools.php',
			'صفحات هبوط الخدمات',
			'صفحات هبوط الخدمات',
			'manage_categories',
			'svc-landing-tools',
			array( __CLASS__, 'tools_page' )
		);
	}

	public static function tools_page() {
		$terms = get_terms( array( 'taxonomy' => self::TAXONOMY, 'hide_empty' => false ) );
		$done  = isset( $_GET['seeded'] ) ? absint( $_GET['seeded'] ) : -1;
		?>
		<div class="wrap" dir="rtl">
			<h1>صفحات هبوط الخدمات</h1>
			<?php if ( $done >= 0 ) : ?>
				<div class="notice notice-success"><p>تمت تعبئة <strong><?php echo esc_html( self::num( $done ) ); ?></strong> حقلًا فارغًا. الحقول المكتوبة لم تُمس.</p></div>
			<?php endif; ?>

			<div class="svc-seed-card">
				<h2>تعبئة الحقول الفارغة بمحتوى مبدئي</h2>
				<p>يملأ الحقول الفارغة فقط في التصنيف المختار بمحتوى جاهز للتحرير — <strong>ولا يغيّر أي حقل كتبته بنفسك</strong>.</p>
				<ul class="svc-seed-list">
					<li>المميزات: ست بطاقات مبدئية</li>
					<li>الخطوات: أربع خطوات مبدئية</li>
					<li>الأسئلة: خمسة أسئلة مبدئية</li>
					<li>الأرقام وشارات الثقة والعناوين</li>
				</ul>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:18px">
					<input type="hidden" name="action" value="svc_seed">
					<?php wp_nonce_field( 'svc_seed_action', 'svc_seed_nonce' ); ?>
					<p>
						<label for="svc_term">التصنيف:</label><br>
						<select name="term_id" id="svc_term" required style="min-width:280px;margin-top:6px">
							<option value="">— اختر التصنيف —</option>
							<?php foreach ( (array) $terms as $t ) : ?>
								<?php if ( is_object( $t ) ) : ?>
									<option value="<?php echo esc_attr( $t->term_id ); ?>"><?php echo esc_html( $t->name ); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</p>
					<p>
						<label><input type="checkbox" name="enable" value="1" checked> تفعيل صفحة الهبوط لهذا التصنيف أيضًا</label>
					</p>
					<p><button type="submit" class="button button-primary">املأ الحقول الفارغة</button></p>
				</form>
			</div>
		</div>
		<?php
	}

	/** المحتوى المبدئي — يُستخدم فقط لملء الفراغ. */
	private static function seed_data( $name ) {
		return array(
			'svc_hero_eyebrow'   => 'خدمة معتمدة',
			'svc_hero_title'     => $name,
			'svc_hero_promise'   => 'فريق معتمد يصلك في نفس اليوم، بأسعار واضحة قبل بدء العمل وضمان مكتوب على الخدمة.',
			'svc_hero_badges'    => "فريق معتمد ومدرّب\nأسعار واضحة قبل البدء\nضمان مكتوب على الخدمة",

			'svc_stat_years'     => '15',
			'svc_stat_years_l'   => 'سنة خبرة',
			'svc_stat_clients'   => '8400',
			'svc_stat_clients_l' => 'عميل خدمناه',
			'svc_stat_cities_l'  => 'مدينة نغطيها',
			'svc_stat_grt'       => '6 أشهر',
			'svc_stat_grt_l'     => 'ضمان مكتوب',

			'svc_features_head'  => 'لماذا تختارنا لهذه الخدمة',
			'svc_features_sub'   => 'كل نقطة التزام قابل للقياس نلتزم به كتابةً قبل بدء العمل.',
			'svc_features'       => implode( "\n", array(
				'fa-solid fa-clock | وصول خلال 60 دقيقة | نصلك داخل المدينة خلال ساعة من تأكيد الطلب.',
				'fa-solid fa-file-invoice-dollar | سعر نهائي قبل البدء | نعاين ونكتب السعر، ولا تدفع أي زيادة بعده.',
				'fa-solid fa-shield-halved | ضمان مكتوب 6 أشهر | نعيد الزيارة مجانًا خلال مدة الضمان.',
				'fa-solid fa-user-check | فنيون معتمدون | فريق مدرّب يحمل بطاقات تعريف عند الوصول.',
				'fa-solid fa-flask | مواد مصرّحة | مواد معتمدة وآمنة على الأطفال بعد ساعتين.',
				'fa-solid fa-headset | متابعة بعد الخدمة | نتواصل معك خلال 48 ساعة للتأكد من النتيجة.',
			) ),

			'svc_steps_head'     => 'كيف نعمل',
			'svc_steps_sub'      => 'أربع خطوات واضحة من أول اتصال حتى تسليم العمل.',
			'svc_steps'          => implode( "\n", array(
				'تواصل معنا | اتصال أو رسالة واتساب، ونحدد معك الموعد المناسب.',
				'معاينة وتسعير | نعاين الموقع ونعطيك سعرًا نهائيًا مكتوبًا قبل البدء.',
				'تنفيذ الخدمة | فريق معتمد ينفّذ العمل بالمواد والمعدات المناسبة.',
				'التسليم والضمان | نسلّمك العمل ونمنحك ضمانًا مكتوبًا ومتابعة بعده.',
			) ),

			'svc_cities_head'    => 'المدن التي نغطيها',
			'svc_cities_sub'     => 'اختر مدينتك للاطلاع على تفاصيل الخدمة داخلها.',

			'svc_faq_head'       => 'الأسئلة الشائعة',
			'svc_faq_sub'        => 'إجابات مباشرة على أكثر ما يسأل عنه عملاؤنا قبل الطلب.',
			'svc_faq'            => implode( "\n", array(
				'كم تستغرق الخدمة؟ | تختلف حسب حجم الموقع، ونحدد لك المدة بدقة عند المعاينة.',
				'هل السعر نهائي؟ | نعم، السعر المكتوب بعد المعاينة نهائي ولا يزيد.',
				'هل يوجد ضمان؟ | نعم، ضمان مكتوب مع إعادة الزيارة مجانًا خلال مدته.',
				'هل المواد آمنة؟ | نستخدم مواد مصرّحة وآمنة، ونوضح مدة التهوية المطلوبة.',
				'كيف أدفع؟ | الدفع بعد إنهاء العمل ومعاينتك للنتيجة.',
			) ),

			'svc_order_head'     => 'اطلب الخدمة الآن',
			'svc_order_sub'      => 'اترك بياناتك ونتواصل معك خلال دقائق لتحديد الموعد والسعر.',
			'svc_posts_head'     => 'مقالات قد تهمّك',
		);
	}

	public static function handle_seed() {
		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_die( 'غير مصرح' );
		}
		check_admin_referer( 'svc_seed_action', 'svc_seed_nonce' );

		$term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;
		$term    = $term_id ? get_term( $term_id, self::TAXONOMY ) : null;
		if ( ! $term || is_wp_error( $term ) ) {
			wp_safe_redirect( admin_url( 'tools.php?page=svc-landing-tools' ) );
			exit;
		}

		$filled = 0;
		foreach ( self::seed_data( $term->name ) as $key => $value ) {
			// لا نلمس أي حقل مكتوب
			if ( '' === trim( self::meta( $term_id, $key ) ) ) {
				update_term_meta( $term_id, $key, $value );
				$filled++;
			}
		}
		if ( isset( $_POST['enable'] ) ) {
			update_term_meta( $term_id, 'svc_enable', '1' );
		}

		wp_safe_redirect( admin_url( 'tools.php?page=svc-landing-tools&seeded=' . $filled ) );
		exit;
	}

	/* ------------------------------------------------------------------ */
	/* البيانات المنظّمة                                                   */
	/* ------------------------------------------------------------------ */

	private static function schema( $term, $faqs, $cities ) {
		$areas = array();
		foreach ( $cities as $city ) {
			$areas[] = array( '@type' => 'City', 'name' => $city->name );
		}

		$service = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'name'        => self::meta( $term->term_id, 'svc_hero_title' ) ?: $term->name,
			'serviceType' => $term->name,
			'url'         => get_term_link( $term ),
			'provider'    => array(
				'@type' => 'LocalBusiness',
				'name'  => get_option( 'sitename' ) ? get_option( 'sitename' ) : get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
			),
		);
		$promise = self::meta( $term->term_id, 'svc_hero_promise' );
		if ( '' !== $promise ) {
			$service['description'] = $promise;
		}
		$phone = trim( (string) get_option( 'Phone' ) );
		if ( '' !== $phone ) {
			$service['provider']['telephone'] = $phone;
		}
		if ( $areas ) {
			$service['areaServed'] = $areas;
		}

		$out = wp_json_encode( $service, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		echo '<script type="application/ld+json">' . $out . '</script>';

		if ( $faqs ) {
			$entities = array();
			foreach ( $faqs as $faq ) {
				$entities[] = array(
					'@type'          => 'Question',
					'name'           => $faq['q'],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $faq['a'],
					),
				);
			}
			$faqpage = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $faqpage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
		}
	}

	/** جمع الأسئلة: من حقل التصنيف، وإلا من نوع محتوى الأسئلة الموجود. */
	private static function collect_faqs( $term_id ) {
		$out = array();
		foreach ( self::rows( $term_id, 'svc_faq', 2 ) as $row ) {
			if ( '' !== $row[0] ) {
				$out[] = array( 'q' => $row[0], 'a' => $row[1] );
			}
		}
		if ( $out ) {
			return $out;
		}
		$posts = get_posts( array( 'post_type' => self::FAQ_TYPE, 'posts_per_page' => 6 ) );
		foreach ( (array) $posts as $p ) {
			$out[] = array( 'q' => get_the_title( $p->ID ), 'a' => wp_strip_all_tags( $p->post_content ) );
		}
		return $out;
	}

	/** المدن: المختارة، وإلا كل مدن تصنيف المدن. */
	private static function collect_cities( $term_id ) {
		$ids  = (array) get_term_meta( $term_id, 'svc_cities', true );
		$ids  = array_filter( array_map( 'absint', $ids ) );
		$args = array( 'taxonomy' => self::CITY_TAXONOMY, 'hide_empty' => false );
		if ( $ids ) {
			$args['include'] = $ids;
			$args['orderby'] = 'include';
		}
		$terms = get_terms( $args );
		return ( is_array( $terms ) ) ? array_filter( $terms, 'is_object' ) : array();
	}

	/* ------------------------------------------------------------------ */
	/* العرض                                                              */
	/* ------------------------------------------------------------------ */

	/**
	 * @param WP_Term     $term  تصنيف الخدمة.
	 * @param ThemeStatic $theme سياق القالب لإعادة استخدام أجزائه.
	 */
	public static function render( $term, $theme = null ) {
		$id       = $term->term_id;
		$phone    = trim( (string) get_option( 'Phone' ) );
		$whatsapp = trim( (string) get_option( 'Whatsapp' ) );
		$faqs     = self::collect_faqs( $id );
		$cities   = self::collect_cities( $id );

		wp_enqueue_style(
			'svc-landing',
			get_template_directory_uri() . '/components/packs/ServiceLanding/landing.css',
			array(),
			'1.0.0'
		);

		$icon_phone = '<svg viewBox="0 0 512 512" aria-hidden="true"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>';
		$icon_wa    = '<svg viewBox="0 0 448 512" aria-hidden="true"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zM223.9 438.7c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6z"/></svg>';
		$icon_check = '<svg viewBox="0 0 512 512" aria-hidden="true"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>';

		echo '<div class="svc-landing">';

		self::schema( $term, $faqs, $cities );

		/* ---------- ١. المقدمة ---------- */
		$hero_title   = self::meta( $id, 'svc_hero_title' );
		$hero_title   = ( '' !== $hero_title ) ? $hero_title : $term->name;
		$hero_eyebrow = self::meta( $id, 'svc_hero_eyebrow' );
		$hero_promise = self::meta( $id, 'svc_hero_promise' );
		$badges       = self::rows( $id, 'svc_hero_badges', 1 );

		echo '<section class="svc-hero">';
			echo '<div class="svc-wrap">';
				echo '<div class="svc-hero__grid">';
					echo '<div class="svc-hero__intro">';
						if ( '' !== $hero_eyebrow ) {
							echo '<span class="svc-hero__eyebrow">' . $icon_check . esc_html( $hero_eyebrow ) . '</span>';
						}
						echo '<h1>' . esc_html( $hero_title ) . '</h1>';
						if ( '' !== $hero_promise ) {
							echo '<p class="svc-hero__promise">' . esc_html( $hero_promise ) . '</p>';
						}
						if ( $badges ) {
							echo '<div class="svc-badges">';
							foreach ( $badges as $b ) {
								echo '<span class="svc-badge">' . $icon_check . esc_html( $b[0] ) . '</span>';
							}
							echo '</div>';
						}
						echo '<div class="svc-hero__actions">';
							if ( '' !== $phone ) {
								echo '<a class="svc-btn svc-btn--call" href="tel:' . esc_attr( $phone ) . '" data-call="Phone">' . $icon_phone . '<span>اتصل بنا</span></a>';
							}
							if ( '' !== $whatsapp ) {
								echo '<a class="svc-btn svc-btn--wa" href="https://wa.me/' . esc_attr( $whatsapp ) . '" target="_blank" rel="nofollow noopener" data-call="whatsapp">' . $icon_wa . '<span>واتساب</span></a>';
							}
						echo '</div>';
					echo '</div>';

					/* نموذج ثلاثة حقول — نفس نظام استقبال الطلبات الموجود */
					echo '<aside class="svc-quickform">';
						echo '<p class="svc-quickform__title">' . esc_html( $hero_title ) . '</p>';
						echo '<p class="svc-quickform__sub">اترك رقمك ونتواصل معك</p>';
						echo '<div class="form-contact">';
							echo '<form method="post">';
								echo '<div class="svc-field"><label for="svc-q-name">الاسم</label><input id="svc-q-name" type="text" name="names" required></div>';
								echo '<div class="svc-field"><label for="svc-q-phone">رقم الجوال</label><input id="svc-q-phone" type="tel" name="phone" required></div>';
								echo '<div class="svc-field"><label for="svc-q-address">المدينة أو الحي</label><input id="svc-q-address" type="text" name="address"></div>';
								echo '<input type="hidden" name="email" value="">';
								echo '<input type="hidden" name="message" value="' . esc_attr( $term->name ) . '">';
								echo '<button type="submit" class="svc-btn svc-btn--call svc-btn--block">اطلب الخدمة</button>';
							echo '</form>';
						echo '</div>';
					echo '</aside>';
				echo '</div>';
			echo '</div>';
		echo '</section>';

		/* ---------- ٢. شريط الأرقام ---------- */
		$stats = array(
			array( self::num( self::meta( $id, 'svc_stat_years' ) ), self::meta( $id, 'svc_stat_years_l' ) ),
			array( self::num( self::meta( $id, 'svc_stat_clients' ) ), self::meta( $id, 'svc_stat_clients_l' ) ),
			array( self::num( self::meta( $id, 'svc_stat_cities' ) ) ?: self::num( count( $cities ) ), self::meta( $id, 'svc_stat_cities_l' ) ),
			array( self::meta( $id, 'svc_stat_grt' ), self::meta( $id, 'svc_stat_grt_l' ) ),
		);
		$has_stats = false;
		foreach ( $stats as $s ) {
			if ( '' !== trim( (string) $s[0] ) ) {
				$has_stats = true;
			}
		}
		if ( $has_stats ) {
			echo '<section class="svc-stats"><div class="svc-wrap"><div class="svc-stats__grid">';
			foreach ( $stats as $s ) {
				if ( '' === trim( (string) $s[0] ) ) {
					continue;
				}
				// الأرقام لاتينية بفاصل لاتيني، والوحدة العربية منفصلة عن الرقم
				if ( preg_match( '/^([\d,\.]+)\s*(.*)$/u', (string) $s[0], $m ) ) {
					$value = '<span class="svc-num">' . esc_html( $m[1] ) . '</span>';
					if ( '' !== trim( $m[2] ) ) {
						$value .= ' <small>' . esc_html( trim( $m[2] ) ) . '</small>';
					}
				} else {
					$value = esc_html( $s[0] );
				}
				echo '<div class="svc-stat">';
					echo '<span class="svc-stat__value">' . $value . '</span>';
					if ( '' !== $s[1] ) {
						echo '<span class="svc-stat__label">' . esc_html( $s[1] ) . '</span>';
					}
				echo '</div>';
			}
			echo '</div></div></section>';
		}

		/* ---------- ٣. المميزات ---------- */
		$features = self::rows( $id, 'svc_features', 3 );
		if ( $features ) {
			echo '<section class="svc-section"><div class="svc-wrap">';
				self::head( self::meta( $id, 'svc_features_head' ), self::meta( $id, 'svc_features_sub' ) );
				echo '<div class="svc-features">';
				foreach ( $features as $f ) {
					echo '<article class="svc-feature">';
						if ( '' !== $f[0] ) {
							echo '<span class="svc-feature__icon"><i class="' . esc_attr( $f[0] ) . '" aria-hidden="true"></i></span>';
						}
						echo '<h3>' . esc_html( $f[1] ) . '</h3>';
						if ( '' !== $f[2] ) {
							echo '<p>' . esc_html( $f[2] ) . '</p>';
						}
					echo '</article>';
				}
				echo '</div>';
			echo '</div></section>';
		}

		/* ---------- ٤. خطوات العمل ---------- */
		$steps = self::rows( $id, 'svc_steps', 2 );
		if ( $steps ) {
			$last = count( $steps ) - 1;
			echo '<section class="svc-section svc-section--tint"><div class="svc-wrap">';
				self::head( self::meta( $id, 'svc_steps_head' ), self::meta( $id, 'svc_steps_sub' ) );
				echo '<ol class="svc-steps">';
				foreach ( $steps as $i => $s ) {
					$final = ( $i === $last ) ? ' svc-step--final' : '';
					echo '<li class="svc-step' . $final . '">';
						echo '<span class="svc-step__num svc-num">' . esc_html( self::num( $i + 1 ) ) . '</span>';
						echo '<h3>' . esc_html( $s[0] ) . '</h3>';
						if ( '' !== $s[1] ) {
							echo '<p>' . esc_html( $s[1] ) . '</p>';
						}
					echo '</li>';
				}
				echo '</ol>';
			echo '</div></section>';
		}

		/* ---------- ٥. المدن ---------- */
		if ( $cities ) {
			$as_service = '1' === self::meta( $id, 'svc_cities_link' );
			echo '<section class="svc-section"><div class="svc-wrap">';
				self::head( self::meta( $id, 'svc_cities_head' ), self::meta( $id, 'svc_cities_sub' ) );
				echo '<div class="svc-cities">';
				foreach ( $cities as $city ) {
					$link = get_term_link( $city );
					if ( $as_service && class_exists( 'CityServices' ) ) {
						$service_link = ( new CityServices() )->ServiceQVar( $term, $city );
						if ( ! empty( $service_link ) ) {
							$link = $service_link;
						}
					}
					if ( is_wp_error( $link ) ) {
						continue;
					}
					echo '<a class="svc-city" href="' . esc_url( $link ) . '">' . esc_html( $city->name ) . '</a>';
				}
				echo '</div>';
			echo '</div></section>';
		}

		/* ---------- ٦. الأسئلة الشائعة ---------- */
		if ( $faqs ) {
			echo '<section class="svc-section svc-section--tint"><div class="svc-wrap">';
				self::head( self::meta( $id, 'svc_faq_head' ), self::meta( $id, 'svc_faq_sub' ) );
				echo '<div class="svc-faq">';
				foreach ( $faqs as $i => $faq ) {
					$open = ( 0 === $i ) ? ' is-open' : '';
					echo '<div class="svc-faq__item' . $open . '">';
						echo '<button type="button" class="svc-faq__q" aria-expanded="' . ( $open ? 'true' : 'false' ) . '">';
							echo '<span>' . esc_html( $faq['q'] ) . '</span>';
							echo '<span class="svc-faq__sign" aria-hidden="true">&#9662;</span>';
						echo '</button>';
						echo '<div class="svc-faq__a"><div>' . esc_html( $faq['a'] ) . '</div></div>';
					echo '</div>';
				}
				echo '</div>';
			echo '</div></section>';
		}

		/* ---------- ٧. نموذج الطلب الكامل ---------- */
		echo '<section class="svc-section"><div class="svc-wrap">';
			echo '<div class="svc-order">';
				echo '<div class="svc-order__aside">';
					$oh = self::meta( $id, 'svc_order_head' );
					$os = self::meta( $id, 'svc_order_sub' );
					if ( '' !== $oh ) {
						echo '<h2>' . esc_html( $oh ) . '</h2>';
					}
					if ( '' !== $os ) {
						echo '<p>' . esc_html( $os ) . '</p>';
					}
					echo '<div class="svc-order__contacts">';
						if ( '' !== $phone ) {
							echo '<a class="svc-btn svc-btn--call" href="tel:' . esc_attr( $phone ) . '" data-call="Phone">' . $icon_phone . '<span>اتصل بنا</span></a>';
						}
						if ( '' !== $whatsapp ) {
							echo '<a class="svc-btn svc-btn--wa" href="https://wa.me/' . esc_attr( $whatsapp ) . '" target="_blank" rel="nofollow noopener" data-call="whatsapp">' . $icon_wa . '<span>واتساب</span></a>';
						}
					echo '</div>';
				echo '</div>';

				echo '<div class="form-contact">';
					echo '<form method="post">';
						echo '<div class="svc-order__row">';
							echo '<div class="svc-field"><label for="svc-o-name">الاسم</label><input id="svc-o-name" type="text" name="names" required></div>';
							echo '<div class="svc-field"><label for="svc-o-phone">رقم الجوال</label><input id="svc-o-phone" type="tel" name="phone" required></div>';
						echo '</div>';
						echo '<div class="svc-order__row">';
							echo '<div class="svc-field"><label for="svc-o-email">البريد الإلكتروني</label><input id="svc-o-email" type="email" name="email"></div>';
							echo '<div class="svc-field"><label for="svc-o-address">المدينة أو الحي</label><input id="svc-o-address" type="text" name="address"></div>';
						echo '</div>';
						echo '<div class="svc-field"><label for="svc-o-message">تفاصيل الطلب</label><textarea id="svc-o-message" name="message">' . esc_textarea( $term->name ) . '</textarea></div>';
						echo '<button type="submit" class="svc-btn svc-btn--call svc-btn--block">إرسال الطلب</button>';
					echo '</form>';
				echo '</div>';
			echo '</div>';
		echo '</div></section>';

		/* ---------- ٨. المقالات — نفس الاستعلام ونفس البطاقة بلا تغيير ---------- */
		echo '<section class="svc-posts"><div class="svc-wrap">';
			$ph = self::meta( $id, 'svc_posts_head' );
			if ( '' !== $ph ) {
				echo '<div class="titles_concept"><h2>' . esc_html( $ph ) . '</h2></div>';
			}
			if ( is_object( $theme ) && method_exists( $theme, 'Part' ) ) {
				$theme->Part( 'Posts', array(
					'AutoLoadmore'  => true,
					'post__not_in'  => array( $term->term_id ),
					'UniqId'        => uniqid(),
					'term'          => array( $term ),
				) );
			}
		echo '</div></section>';

		echo '</div>';

		self::inline_script();
	}

	private static function head( $title, $sub ) {
		if ( '' === $title && '' === $sub ) {
			return;
		}
		// div وليس header — القالب يطبّق على وسم header خلفية بيضاء و position:sticky
		echo '<div class="svc-head">';
		if ( '' !== $title ) {
			echo '<h2>' . esc_html( $title ) . '</h2>';
		}
		if ( '' !== $sub ) {
			echo '<p>' . esc_html( $sub ) . '</p>';
		}
		echo '</div>';
	}

	/** تفاعل الأسئلة فقط — بدون اعتماد على أي مكتبة خارجية. */
	private static function inline_script() {
		?>
		<script>
		(function () {
			var items = document.querySelectorAll('.svc-landing .svc-faq__item');
			if (!items.length) { return; }

			function body(item) { return item.querySelector('.svc-faq__a'); }

			/* المفتوح يُترك بارتفاع تلقائي بعد انتهاء الحركة حتى لا يُقص نصه
			   عند تحمّل الخطوط أو تغيّر عرض الشاشة. */
			function open(item, animate) {
				var el = body(item), btn = item.querySelector('.svc-faq__q');
				if (!el || !btn) { return; }
				item.classList.add('is-open');
				btn.setAttribute('aria-expanded', 'true');
				if (!animate) { el.style.maxHeight = 'none'; return; }
				el.style.maxHeight = el.scrollHeight + 'px';
				window.setTimeout(function () {
					if (item.classList.contains('is-open')) { el.style.maxHeight = 'none'; }
				}, 300);
			}

			function close(item) {
				var el = body(item), btn = item.querySelector('.svc-faq__q');
				if (!el || !btn) { return; }
				if ('none' === el.style.maxHeight) {
					el.style.maxHeight = el.scrollHeight + 'px';
					void el.offsetHeight;
				}
				item.classList.remove('is-open');
				btn.setAttribute('aria-expanded', 'false');
				el.style.maxHeight = '0px';
			}

			items.forEach(function (item) {
				if (item.classList.contains('is-open')) { open(item, false); } else { close(item); }
				var btn = item.querySelector('.svc-faq__q');
				if (!btn) { return; }
				btn.addEventListener('click', function () {
					var willOpen = !item.classList.contains('is-open');
					items.forEach(close);
					if (willOpen) { open(item, true); }
				});
			});
		})();
		</script>
		<?php
	}
}

ServiceLanding::init();
