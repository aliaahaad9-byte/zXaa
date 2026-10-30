<?php
/**
 * AuthorEEAT — ملف الكاتب بمعايير الخبرة والتخصص والموثوقية والثقة.
 *
 * - حقول إضافية في صفحة الملف الشخصي للمستخدم (لوحة التحكم ← المستخدمون).
 * - صندوق «عن الكاتب» أسفل كل مقال، يُبرز خبرته في قسم المقال نفسه.
 * - صفحة كاتب احترافية + بيانات منظّمة ProfilePage / Person.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AuthorEEAT {

	const NONCE = 'eeat_profile_nonce';

	/** شبكات التواصل المدعومة: المفتاح => [التسمية، أيقونة Font Awesome] */
	public static function networks() {
		return array(
			'linkedin'  => array( 'LinkedIn', 'fa-brands fa-linkedin-in' ),
			'x'         => array( 'X (تويتر)', 'fa-brands fa-x-twitter' ),
			'facebook'  => array( 'Facebook', 'fa-brands fa-facebook-f' ),
			'instagram' => array( 'Instagram', 'fa-brands fa-instagram' ),
			'youtube'   => array( 'YouTube', 'fa-brands fa-youtube' ),
			'website'   => array( 'الموقع الشخصي', 'fa-solid fa-globe' ),
		);
	}

	/** أيقونات جاهزة لمجالات الخبرة. */
	public static function icons() {
		return array(
			'fa-solid fa-broom'           => 'تنظيف',
			'fa-solid fa-bug'             => 'مكافحة حشرات',
			'fa-solid fa-droplet'         => 'كشف تسربات',
			'fa-solid fa-shield-halved'   => 'عزل',
			'fa-solid fa-house-chimney'   => 'مباني',
			'fa-solid fa-spray-can'       => 'تعقيم',
			'fa-solid fa-temperature-half' => 'حراري',
			'fa-solid fa-screwdriver-wrench' => 'صيانة',
		);
	}

	public static function init() {
		add_action( 'show_user_profile', array( __CLASS__, 'profile_fields' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'profile_fields' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'save_profile' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'save_profile' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
		add_action( 'wp_head', array( __CLASS__, 'front_head' ), 30 );
		// صورة الكاتب المرفوعة تحل محل Gravatar في كل الموقع
		add_filter( 'get_avatar_url', array( __CLASS__, 'avatar_url' ), 10, 3 );
	}

	/* ------------------------------------------------------------------ */
	/* قراءة البيانات                                                      */
	/* ------------------------------------------------------------------ */

	private static function list_meta( $user_id, $key ) {
		$v = get_user_meta( $user_id, $key, true );
		return is_array( $v ) ? array_values( $v ) : array();
	}

	/** كل بيانات الكاتب في مصفوفة واحدة جاهزة للعرض. */
	public static function data( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return null;
		}
		$name  = trim( (string) get_user_meta( $user_id, 'eeat_full_name', true ) );
		$photo = trim( (string) get_user_meta( $user_id, 'eeat_photo', true ) );
		$about = trim( (string) get_user_meta( $user_id, 'eeat_about', true ) );
		$short = trim( (string) get_user_meta( $user_id, 'eeat_short_bio', true ) );

		$social = array();
		foreach ( self::networks() as $k => $n ) {
			$url = trim( (string) get_user_meta( $user_id, 'eeat_social_' . $k, true ) );
			if ( '' !== $url ) {
				$social[ $k ] = $url;
			}
		}

		$expertise = array();
		foreach ( self::list_meta( $user_id, 'eeat_expertise' ) as $row ) {
			if ( ! empty( $row['title'] ) ) {
				$expertise[] = wp_parse_args( $row, array( 'cat' => 0, 'icon' => '', 'title' => '', 'years' => '', 'text' => '' ) );
			}
		}
		$certs = array();
		foreach ( self::list_meta( $user_id, 'eeat_certs' ) as $row ) {
			if ( ! empty( $row['name'] ) ) {
				$certs[] = wp_parse_args( $row, array( 'name' => '', 'issuer' => '', 'year' => '', 'url' => '' ) );
			}
		}
		$members = array();
		foreach ( self::list_meta( $user_id, 'eeat_members' ) as $row ) {
			if ( ! empty( $row['name'] ) ) {
				$members[] = wp_parse_args( $row, array( 'name' => '', 'role' => '', 'url' => '' ) );
			}
		}
		$highlights = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) get_user_meta( $user_id, 'eeat_highlights', true ) ) ) );

		return array(
			'id'         => $user_id,
			'name'       => ( '' !== $name ) ? $name : $user->display_name,
			'job'        => trim( (string) get_user_meta( $user_id, 'eeat_job_title', true ) ),
			'photo'      => ( '' !== $photo ) ? $photo : get_avatar_url( $user_id, array( 'size' => 400 ) ),
			'has_photo'  => '' !== $photo,
			'years'      => absint( get_user_meta( $user_id, 'eeat_years', true ) ),
			'projects'   => absint( get_user_meta( $user_id, 'eeat_projects', true ) ),
			'region'     => trim( (string) get_user_meta( $user_id, 'eeat_region', true ) ),
			'verified'   => '1' === (string) get_user_meta( $user_id, 'eeat_verified', true ),
			'short'      => ( '' !== $short ) ? $short : wp_trim_words( ( '' !== $about ) ? $about : $user->description, 38 ),
			'about'      => ( '' !== $about ) ? $about : $user->description,
			'social'     => $social,
			'expertise'  => $expertise,
			'certs'      => $certs,
			'members'    => $members,
			'highlights' => array_values( $highlights ),
			'url'        => get_author_posts_url( $user_id ),
			'posts'      => (int) count_user_posts( $user_id, 'post', true ),
		);
	}

	/** الأرقام لاتينية بفاصل آلاف لاتيني (8,400) — نفس قاعدة بقية الموقع. */
	public static function num( $n ) {
		return number_format( (float) $n, 0, '.', ',' );
	}

	/**
	 * تمييز العدد بالعربية: 1 مفرد، 2 مثنى، 3-10 جمع، 11 فأكثر مفرد.
	 * $forms = [مفرد، مثنى، جمع]
	 */
	public static function count_label( $n, $forms ) {
		$n = (int) $n;
		$r = $n % 100;
		if ( 2 === $n ) {
			return $forms[1];
		}
		if ( $r >= 3 && $r <= 10 ) {
			return $forms[2];
		}
		return $forms[0];
	}

	public static function years_label( $n ) {
		return self::count_label( $n, array( 'سنة', 'سنتان', 'سنوات' ) );
	}

	public static function avatar_url( $url, $id_or_email, $args ) {
		$user_id = 0;
		if ( is_numeric( $id_or_email ) ) {
			$user_id = (int) $id_or_email;
		} elseif ( $id_or_email instanceof WP_User ) {
			$user_id = $id_or_email->ID;
		} elseif ( $id_or_email instanceof WP_Post ) {
			$user_id = (int) $id_or_email->post_author;
		} elseif ( $id_or_email instanceof WP_Comment ) {
			$user_id = (int) $id_or_email->user_id;
		}
		if ( $user_id ) {
			$photo = trim( (string) get_user_meta( $user_id, 'eeat_photo', true ) );
			if ( '' !== $photo ) {
				return $photo;
			}
		}
		return $url;
	}

	/* ------------------------------------------------------------------ */
	/* حقول الملف الشخصي                                                  */
	/* ------------------------------------------------------------------ */

	public static function admin_assets( $hook ) {
		if ( 'profile.php' !== $hook && 'user-edit.php' !== $hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'eeat-admin', get_template_directory_uri() . '/components/packs/AuthorEEAT/admin.css', array(), '1.0.0' );
		wp_enqueue_script( 'eeat-admin', get_template_directory_uri() . '/components/packs/AuthorEEAT/admin.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'eeat-admin', 'EEATDefaults', self::suggested_expertise() );
	}

	/**
	 * المجالات الأربعة المقترحة — تُملأ بزر في الملف الشخصي عند الطلب فقط،
	 * وتُربط تلقائيًا بالتصنيف الذي يحمل اسمًا مطابقًا إن وُجد.
	 */
	private static function suggested_expertise() {
		$rows = array(
			array(
				'match' => array( 'تنظيف', 'نظافة' ),
				'icon'  => 'fa-solid fa-broom',
				'title' => 'التنظيف والتعقيم',
				'text'  => 'معرفة عميقة بالمواد الكيميائية الآمنة ومعايير التعقيم المعتمدة من وزارة الصحة، والتعامل مع الأسطح الحساسة كالرخام والسجاد والجلود دون إتلافها.',
			),
			array(
				'match' => array( 'حشرات', 'آفات', 'مكافحة' ),
				'icon'  => 'fa-solid fa-bug',
				'title' => 'مكافحة الحشرات والآفات',
				'text'  => 'تحديد فصائل الآفات بدقة، واستخدام مبيدات آمنة بيئيًا مصرّح بها من الجهات الحكومية، مع إجراءات سلامة واضحة للأطفال والحيوانات الأليفة.',
			),
			array(
				'match' => array( 'تسرب', 'تسربات', 'تسريب' ),
				'icon'  => 'fa-solid fa-droplet',
				'title' => 'كشف تسربات المياه',
				'text'  => 'دراية بأحدث أجهزة الكشف كالكاميرات الحرارية وأجهزة الترددات الصوتية لتحديد موضع التسرب بدقة دون الحاجة لتكسير الجدران أو الأرضيات.',
			),
			array(
				'match' => array( 'عزل' ),
				'icon'  => 'fa-solid fa-shield-halved',
				'title' => 'العزل المائي والحراري',
				'text'  => 'معرفة هندسية بأنواع العزل المائي والحراري والفوم والإيبوكسي، ومعالجة الرطوبة واختيار نظام العزل المناسب لكل نوع من الأسطح.',
			),
		);
		$cats = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) );
		foreach ( $rows as &$r ) {
			$r['cat'] = 0;
			foreach ( is_array( $cats ) ? $cats : array() as $c ) {
				foreach ( $r['match'] as $word ) {
					if ( false !== mb_strpos( $c->name, $word ) ) {
						$r['cat'] = (int) $c->term_id;
						break 2;
					}
				}
			}
			unset( $r['match'] );
		}
		unset( $r );
		return $rows;
	}

	private static function cat_options( $selected ) {
		$out  = '<option value="0">— بدون ربط —</option>';
		$cats = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) );
		foreach ( is_array( $cats ) ? $cats : array() as $c ) {
			$out .= '<option value="' . esc_attr( $c->term_id ) . '"' . selected( (int) $selected, (int) $c->term_id, false ) . '>' . esc_html( $c->name ) . '</option>';
		}
		return $out;
	}

	private static function icon_options( $selected ) {
		$out = '';
		foreach ( self::icons() as $cls => $label ) {
			$out .= '<option value="' . esc_attr( $cls ) . '"' . selected( $selected, $cls, false ) . '>' . esc_html( $label ) . '</option>';
		}
		return $out;
	}

	/** صف مجال خبرة واحد — يُستخدم للصفوف المحفوظة ولقالب الصف الجديد. */
	private static function expertise_row( $i, $row ) {
		$row = wp_parse_args( $row, array( 'cat' => 0, 'icon' => 'fa-solid fa-broom', 'title' => '', 'years' => '', 'text' => '' ) );
		$n   = 'eeat_expertise[' . $i . ']';
		ob_start();
		?>
		<div class="eeat-row">
			<div class="eeat-grid eeat-grid--4">
				<label>عنوان المجال<input type="text" name="<?php echo esc_attr( $n ); ?>[title]" value="<?php echo esc_attr( $row['title'] ); ?>" data-k="title"></label>
				<label>القسم المرتبط<select name="<?php echo esc_attr( $n ); ?>[cat]" data-k="cat"><?php echo self::cat_options( $row['cat'] ); // phpcs:ignore ?></select></label>
				<label>الأيقونة<select name="<?php echo esc_attr( $n ); ?>[icon]" data-k="icon"><?php echo self::icon_options( $row['icon'] ); // phpcs:ignore ?></select></label>
				<label>سنوات الخبرة في المجال<input type="number" min="0" max="80" name="<?php echo esc_attr( $n ); ?>[years]" value="<?php echo esc_attr( $row['years'] ); ?>" data-k="years"></label>
			</div>
			<label>وصف الخبرة الميدانية<textarea rows="3" name="<?php echo esc_attr( $n ); ?>[text]" data-k="text"><?php echo esc_textarea( $row['text'] ); ?></textarea></label>
			<button type="button" class="button-link eeat-remove">حذف هذا المجال</button>
		</div>
		<?php
		return ob_get_clean();
	}

	private static function cert_row( $i, $row ) {
		$row = wp_parse_args( $row, array( 'name' => '', 'issuer' => '', 'year' => '', 'url' => '' ) );
		$n   = 'eeat_certs[' . $i . ']';
		ob_start();
		?>
		<div class="eeat-row">
			<div class="eeat-grid eeat-grid--4">
				<label>اسم الشهادة<input type="text" name="<?php echo esc_attr( $n ); ?>[name]" value="<?php echo esc_attr( $row['name'] ); ?>" placeholder="مثال: شهادة السلامة والصحة المهنية OSHA"></label>
				<label>الجهة المانحة<input type="text" name="<?php echo esc_attr( $n ); ?>[issuer]" value="<?php echo esc_attr( $row['issuer'] ); ?>" placeholder="مثال: أمانة منطقة الرياض"></label>
				<label>سنة الحصول<input type="number" min="1950" max="2100" name="<?php echo esc_attr( $n ); ?>[year]" value="<?php echo esc_attr( $row['year'] ); ?>"></label>
				<label>رابط التحقق (اختياري)<input type="url" name="<?php echo esc_attr( $n ); ?>[url]" value="<?php echo esc_attr( $row['url'] ); ?>" placeholder="https://"></label>
			</div>
			<button type="button" class="button-link eeat-remove">حذف</button>
		</div>
		<?php
		return ob_get_clean();
	}

	private static function member_row( $i, $row ) {
		$row = wp_parse_args( $row, array( 'name' => '', 'role' => '', 'url' => '' ) );
		$n   = 'eeat_members[' . $i . ']';
		ob_start();
		?>
		<div class="eeat-row">
			<div class="eeat-grid eeat-grid--3">
				<label>الجمعية أو الهيئة<input type="text" name="<?php echo esc_attr( $n ); ?>[name]" value="<?php echo esc_attr( $row['name'] ); ?>" placeholder="مثال: الهيئة السعودية للمهندسين"></label>
				<label>نوع العضوية<input type="text" name="<?php echo esc_attr( $n ); ?>[role]" value="<?php echo esc_attr( $row['role'] ); ?>" placeholder="مثال: عضو ممارس"></label>
				<label>رابط (اختياري)<input type="url" name="<?php echo esc_attr( $n ); ?>[url]" value="<?php echo esc_attr( $row['url'] ); ?>" placeholder="https://"></label>
			</div>
			<button type="button" class="button-link eeat-remove">حذف</button>
		</div>
		<?php
		return ob_get_clean();
	}

	private static function repeater( $key, $title, $desc, $rows, $renderer, $add_label, $extra = '' ) {
		echo '<div class="eeat-repeater" data-key="' . esc_attr( $key ) . '">';
		echo '<h3>' . esc_html( $title ) . '</h3>';
		echo '<p class="description">' . esc_html( $desc ) . '</p>';
		echo '<div class="eeat-rows">';
		foreach ( $rows as $i => $row ) {
			echo self::$renderer( $i, $row ); // phpcs:ignore
		}
		echo '</div>';
		echo '<script type="text/template" class="eeat-tpl">' . self::$renderer( '__i__', array() ) . '</script>'; // phpcs:ignore
		echo '<p class="eeat-actions"><button type="button" class="button eeat-add">' . esc_html( $add_label ) . '</button>' . $extra . '</p>'; // phpcs:ignore
		echo '</div>';
	}

	public static function profile_fields( $user ) {
		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}
		$m = function ( $k ) use ( $user ) {
			return (string) get_user_meta( $user->ID, $k, true );
		};
		wp_nonce_field( self::NONCE, self::NONCE );
		?>
		<div class="eeat-box" dir="rtl">
			<h2>ملف الخبير (معايير E-E-A-T)</h2>
			<p class="description">هذه البيانات تظهر في صفحة الكاتب وفي صندوق «عن الكاتب» أسفل كل مقال، وتُضاف كبيانات منظّمة يفهمها جوجل. اكتب معلومات حقيقية فقط — الشهادات والعضويات غير الحقيقية تضر بالموقع أكثر مما تنفعه.</p>

			<h3>١. التعريف الأساسي</h3>
			<div class="eeat-grid eeat-grid--2">
				<label>الاسم الكامل (ثنائي أو ثلاثي)<input type="text" name="eeat_full_name" value="<?php echo esc_attr( $m( 'eeat_full_name' ) ); ?>" placeholder="<?php echo esc_attr( $user->display_name ); ?>"></label>
				<label>المسمى الوظيفي الدقيق<input type="text" name="eeat_job_title" value="<?php echo esc_attr( $m( 'eeat_job_title' ) ); ?>" placeholder="مثال: خبير مكافحة آفات معتمد"></label>
			</div>
			<div class="eeat-photo">
				<img src="<?php echo esc_url( '' !== $m( 'eeat_photo' ) ? $m( 'eeat_photo' ) : get_avatar_url( $user->ID, array( 'size' => 160 ) ) ); ?>" alt="" class="eeat-photo__img">
				<div>
					<input type="hidden" name="eeat_photo" value="<?php echo esc_attr( $m( 'eeat_photo' ) ); ?>" class="eeat-photo__val">
					<button type="button" class="button eeat-photo__pick">اختيار صورة شخصية</button>
					<button type="button" class="button-link eeat-photo__clear">إزالة</button>
					<p class="description">صورة حقيقية ومهنية — يُفضّل بملابس العمل الميداني. مربعة بمقاس 600×600 على الأقل.</p>
				</div>
			</div>
			<label class="eeat-check"><input type="checkbox" name="eeat_verified" value="1" <?php checked( '1', $m( 'eeat_verified' ) ); ?>> إظهار شارة «خبير موثّق» (فعّلها فقط بعد التحقق من هوية الكاتب وشهاداته)</label>

			<h3>٢. الخبرة والأرقام</h3>
			<div class="eeat-grid eeat-grid--3">
				<label>إجمالي سنوات الخبرة الميدانية<input type="number" min="0" max="80" name="eeat_years" value="<?php echo esc_attr( $m( 'eeat_years' ) ); ?>"></label>
				<label>عدد المعاينات أو المشاريع المنفذة<input type="number" min="0" name="eeat_projects" value="<?php echo esc_attr( $m( 'eeat_projects' ) ); ?>"></label>
				<label>مناطق العمل<input type="text" name="eeat_region" value="<?php echo esc_attr( $m( 'eeat_region' ) ); ?>" placeholder="مثال: الرياض، جدة، الدمام"></label>
			</div>
			<label>نبذة مختصرة (تظهر أسفل المقالات — سطران أو ثلاثة)<textarea rows="3" name="eeat_short_bio"><?php echo esc_textarea( $m( 'eeat_short_bio' ) ); ?></textarea></label>
			<label>نبذة تفصيلية (تظهر في صفحة الكاتب — اتركها فارغة لاستخدام «معلومات السيرة الذاتية» أعلاه)<textarea rows="6" name="eeat_about"><?php echo esc_textarea( $m( 'eeat_about' ) ); ?></textarea></label>

			<?php
			self::repeater(
				'eeat_expertise',
				'٣. الخبرة الميدانية حسب القسم',
				'مجال لكل قسم يكتب فيه الكاتب. اربط كل مجال بتصنيفه حتى يظهر وصف الخبرة المناسب أسفل مقالات ذلك القسم تحديدًا.',
				self::list_meta( $user->ID, 'eeat_expertise' ),
				'expertise_row',
				'+ إضافة مجال خبرة',
				' <button type="button" class="button eeat-suggest">تعبئة المجالات الأربعة المقترحة</button>'
			);
			self::repeater(
				'eeat_certs',
				'٤. الشهادات والاعتمادات الرسمية',
				'أهم جزء في تقييم جوجل لمواقع الخدمات (YMYL): شهادات البلدية، السلامة المهنية، الشهادات الهندسية…',
				self::list_meta( $user->ID, 'eeat_certs' ),
				'cert_row',
				'+ إضافة شهادة'
			);
			self::repeater(
				'eeat_members',
				'٥. العضويات المهنية',
				'الانتساب لجمعيات أو هيئات محلية أو دولية ذات صلة.',
				self::list_meta( $user->ID, 'eeat_members' ),
				'member_row',
				'+ إضافة عضوية'
			);
			?>

			<h3>٦. سجل الإنجازات</h3>
			<label>إنجاز في كل سطر<textarea rows="4" name="eeat_highlights" placeholder="أكثر من 10 سنوات من العمل الميداني في فحص المباني وعزل الأسطح"><?php echo esc_textarea( $m( 'eeat_highlights' ) ); ?></textarea></label>

			<h3>٧. الحسابات المهنية الموثّقة</h3>
			<div class="eeat-grid eeat-grid--3">
				<?php foreach ( self::networks() as $k => $n ) : ?>
					<label><?php echo esc_html( $n[0] ); ?><input type="url" name="eeat_social_<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $m( 'eeat_social_' . $k ) ); ?>" placeholder="https://"></label>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	public static function save_profile( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( $_POST[ self::NONCE ] ), self::NONCE ) ) {
			return;
		}
		$text = array( 'eeat_full_name', 'eeat_job_title', 'eeat_region' );
		foreach ( $text as $k ) {
			update_user_meta( $user_id, $k, isset( $_POST[ $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) : '' );
		}
		foreach ( array( 'eeat_short_bio', 'eeat_about', 'eeat_highlights' ) as $k ) {
			update_user_meta( $user_id, $k, isset( $_POST[ $k ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $k ] ) ) : '' );
		}
		foreach ( array( 'eeat_years', 'eeat_projects' ) as $k ) {
			$v = isset( $_POST[ $k ] ) ? trim( (string) wp_unslash( $_POST[ $k ] ) ) : '';
			update_user_meta( $user_id, $k, ( '' === $v ) ? '' : absint( $v ) );
		}
		update_user_meta( $user_id, 'eeat_photo', isset( $_POST['eeat_photo'] ) ? esc_url_raw( wp_unslash( $_POST['eeat_photo'] ) ) : '' );
		update_user_meta( $user_id, 'eeat_verified', isset( $_POST['eeat_verified'] ) ? '1' : '' );
		foreach ( array_keys( self::networks() ) as $k ) {
			$f = 'eeat_social_' . $k;
			update_user_meta( $user_id, $f, isset( $_POST[ $f ] ) ? esc_url_raw( wp_unslash( $_POST[ $f ] ) ) : '' );
		}

		$icons     = array_keys( self::icons() );
		$expertise = array();
		foreach ( self::posted_rows( 'eeat_expertise' ) as $r ) {
			$title = sanitize_text_field( isset( $r['title'] ) ? $r['title'] : '' );
			if ( '' === $title ) {
				continue;
			}
			$icon        = isset( $r['icon'] ) ? (string) $r['icon'] : '';
			$expertise[] = array(
				'title' => $title,
				'cat'   => absint( isset( $r['cat'] ) ? $r['cat'] : 0 ),
				'icon'  => in_array( $icon, $icons, true ) ? $icon : $icons[0],
				'years' => ( isset( $r['years'] ) && '' !== trim( (string) $r['years'] ) ) ? absint( $r['years'] ) : '',
				'text'  => sanitize_textarea_field( isset( $r['text'] ) ? $r['text'] : '' ),
			);
		}
		update_user_meta( $user_id, 'eeat_expertise', $expertise );

		$certs = array();
		foreach ( self::posted_rows( 'eeat_certs' ) as $r ) {
			$name = sanitize_text_field( isset( $r['name'] ) ? $r['name'] : '' );
			if ( '' === $name ) {
				continue;
			}
			$certs[] = array(
				'name'   => $name,
				'issuer' => sanitize_text_field( isset( $r['issuer'] ) ? $r['issuer'] : '' ),
				'year'   => ( isset( $r['year'] ) && '' !== trim( (string) $r['year'] ) ) ? absint( $r['year'] ) : '',
				'url'    => esc_url_raw( isset( $r['url'] ) ? $r['url'] : '' ),
			);
		}
		update_user_meta( $user_id, 'eeat_certs', $certs );

		$members = array();
		foreach ( self::posted_rows( 'eeat_members' ) as $r ) {
			$name = sanitize_text_field( isset( $r['name'] ) ? $r['name'] : '' );
			if ( '' === $name ) {
				continue;
			}
			$members[] = array(
				'name' => $name,
				'role' => sanitize_text_field( isset( $r['role'] ) ? $r['role'] : '' ),
				'url'  => esc_url_raw( isset( $r['url'] ) ? $r['url'] : '' ),
			);
		}
		update_user_meta( $user_id, 'eeat_members', $members );
	}

	private static function posted_rows( $key ) {
		if ( empty( $_POST[ $key ] ) || ! is_array( $_POST[ $key ] ) ) { // phpcs:ignore
			return array();
		}
		$rows = array();
		foreach ( wp_unslash( $_POST[ $key ] ) as $i => $r ) { // phpcs:ignore
			if ( '__i__' === (string) $i || ! is_array( $r ) ) {
				continue;
			}
			$rows[] = $r;
		}
		return $rows;
	}

	/* ------------------------------------------------------------------ */
	/* الواجهة                                                            */
	/* ------------------------------------------------------------------ */

	public static function front_head() {
		if ( ! is_author() && ! is_singular( 'post' ) ) {
			return;
		}
		echo '<link rel="stylesheet" href="' . esc_url( get_template_directory_uri() . '/components/packs/AuthorEEAT/author.css?ver=1.0.0' ) . '">';

		$user_id = 0;
		if ( is_author() ) {
			$obj     = get_queried_object();
			$user_id = ( $obj instanceof WP_User ) ? $obj->ID : 0;
		} else {
			$post    = get_queried_object();
			$user_id = ( $post instanceof WP_Post ) ? (int) $post->post_author : 0;
		}
		if ( $user_id ) {
			self::schema( $user_id, is_author() );
		}
	}

	/** كيان Person بمعرّف ثابت، ويُغلَّف بـ ProfilePage في صفحة الكاتب. */
	public static function person_schema( $d ) {
		$site   = get_option( 'sitename' ) ? get_option( 'sitename' ) : get_bloginfo( 'name' );
		$person = array(
			'@type' => 'Person',
			'@id'   => $d['url'] . '#person',
			'name'  => $d['name'],
			'url'   => $d['url'],
			'image' => $d['photo'],
		);
		if ( '' !== $d['job'] ) {
			$person['jobTitle'] = $d['job'];
		}
		if ( '' !== $d['about'] ) {
			$person['description'] = wp_strip_all_tags( $d['about'] );
		}
		$person['worksFor'] = array(
			'@type' => 'Organization',
			'name'  => $site,
			'url'   => home_url( '/' ),
		);
		if ( $d['social'] ) {
			$person['sameAs'] = array_values( $d['social'] );
		}
		if ( $d['expertise'] ) {
			$person['knowsAbout'] = wp_list_pluck( $d['expertise'], 'title' );
		}
		if ( $d['certs'] ) {
			$person['hasCredential'] = array();
			foreach ( $d['certs'] as $c ) {
				$cred = array(
					'@type'              => 'EducationalOccupationalCredential',
					'name'               => $c['name'],
					'credentialCategory' => 'certificate',
				);
				if ( '' !== $c['issuer'] ) {
					$cred['recognizedBy'] = array( '@type' => 'Organization', 'name' => $c['issuer'] );
				}
				if ( '' !== (string) $c['year'] ) {
					$cred['dateCreated'] = (string) $c['year'];
				}
				if ( '' !== $c['url'] ) {
					$cred['url'] = $c['url'];
				}
				$person['hasCredential'][] = $cred;
			}
		}
		if ( $d['members'] ) {
			$person['memberOf'] = array();
			foreach ( $d['members'] as $mb ) {
				$org = array( '@type' => 'Organization', 'name' => $mb['name'] );
				if ( '' !== $mb['url'] ) {
					$org['url'] = $mb['url'];
				}
				$person['memberOf'][] = $org;
			}
		}
		if ( '' !== $d['job'] ) {
			$occ = array( '@type' => 'Occupation', 'name' => $d['job'] );
			if ( $d['years'] ) {
				$occ['experienceRequirements'] = array(
					'@type'              => 'OccupationalExperienceRequirements',
					'monthsOfExperience' => $d['years'] * 12,
				);
			}
			$person['hasOccupation'] = $occ;
		}
		return $person;
	}

	private static function schema( $user_id, $is_profile ) {
		$d = self::data( $user_id );
		if ( ! $d ) {
			return;
		}
		$person = self::person_schema( $d );
		if ( $is_profile ) {
			$graph = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'ProfilePage',
				'url'        => $d['url'],
				'name'       => $d['name'],
				'mainEntity' => $person,
			);
		} else {
			$graph = array( '@context' => 'https://schema.org' ) + $person;
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
	}

	private static function social_links( $social, $class ) {
		if ( ! $social ) {
			return '';
		}
		$nets = self::networks();
		$out  = '<div class="' . esc_attr( $class ) . '">';
		foreach ( $social as $k => $url ) {
			$out .= '<a href="' . esc_url( $url ) . '" target="_blank" rel="me noopener" aria-label="' . esc_attr( $nets[ $k ][0] ) . '"><i class="' . esc_attr( $nets[ $k ][1] ) . '" aria-hidden="true"></i></a>';
		}
		return $out . '</div>';
	}

	private static function verified_badge() {
		return '<span class="eeat-verified" title="خبير موثّق"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1.5 14.6 4l3.6-.4.9 3.5 3.1 1.9-1.4 3.3 1.4 3.3-3.1 1.9-.9 3.5-3.6-.4L12 22.5 9.4 20l-3.6.4-.9-3.5-3.1-1.9L3.2 12 1.8 8.7l3.1-1.9.9-3.5 3.6.4z"/><path d="m8 12.2 2.6 2.6L16.2 9" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>خبير موثّق</span>';
	}

	/** مجال الخبرة المطابق لتصنيف المقال (أو أحد آبائه). */
	private static function matching_expertise( $d, $post_id ) {
		if ( ! $d['expertise'] ) {
			return null;
		}
		$ids = array();
		foreach ( (array) get_the_terms( $post_id, 'category' ) as $t ) {
			if ( $t instanceof WP_Term ) {
				$ids[] = (int) $t->term_id;
				foreach ( get_ancestors( $t->term_id, 'category' ) as $a ) {
					$ids[] = (int) $a;
				}
			}
		}
		foreach ( $d['expertise'] as $e ) {
			if ( (int) $e['cat'] && in_array( (int) $e['cat'], $ids, true ) ) {
				return $e;
			}
		}
		return null;
	}

	/** صندوق «عن الكاتب» أسفل المقال. */
	public static function article_box( $post ) {
		$d = self::data( (int) $post->post_author );
		if ( ! $d ) {
			return;
		}
		$match = self::matching_expertise( $d, $post->ID );
		?>
		<section class="eeat-card" aria-label="عن الكاتب" dir="rtl">
			<div class="eeat-card__head">
				<a class="eeat-card__photo" href="<?php echo esc_url( $d['url'] ); ?>">
					<img src="<?php echo esc_url( $d['photo'] ); ?>" alt="<?php echo esc_attr( $d['name'] ); ?>" width="84" height="84" loading="lazy">
				</a>
				<div class="eeat-card__id">
					<span class="eeat-card__label">كتبه</span>
					<a class="eeat-card__name" href="<?php echo esc_url( $d['url'] ); ?>" rel="author"><?php echo esc_html( $d['name'] ); ?></a>
					<?php if ( '' !== $d['job'] || $d['verified'] ) : ?>
						<div class="eeat-card__job">
							<?php if ( '' !== $d['job'] ) : ?><span><?php echo esc_html( $d['job'] ); ?></span><?php endif; ?>
							<?php if ( $d['verified'] ) { echo self::verified_badge(); } // phpcs:ignore ?>
						</div>
					<?php endif; ?>
				</div>
				<?php echo self::social_links( $d['social'], 'eeat-social eeat-social--sm' ); // phpcs:ignore ?>
			</div>

			<?php if ( $d['years'] || $d['certs'] || $d['projects'] ) : ?>
				<ul class="eeat-card__facts">
					<?php if ( $d['years'] ) : ?>
						<li><i class="fa-solid fa-briefcase" aria-hidden="true"></i><span><b class="eeat-num"><?php echo esc_html( self::num( $d['years'] ) ); ?></b> <?php echo esc_html( self::years_label( $d['years'] ) ); ?> خبرة ميدانية</span></li>
					<?php endif; ?>
					<?php if ( $d['projects'] ) : ?>
						<li><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i><span><b class="eeat-num"><?php echo esc_html( self::num( $d['projects'] ) ); ?></b> <?php echo esc_html( self::count_label( $d['projects'], array( 'معاينة ومشروع منفّذ', 'معاينتان ومشروعان', 'معاينات ومشاريع منفّذة' ) ) ); ?></span></li>
					<?php endif; ?>
					<?php if ( $d['certs'] ) : ?>
						<li><i class="fa-solid fa-award" aria-hidden="true"></i><span><b class="eeat-num"><?php echo esc_html( self::num( count( $d['certs'] ) ) ); ?></b> <?php echo esc_html( self::count_label( count( $d['certs'] ), array( 'شهادة واعتماد مهني', 'شهادتان مهنيتان', 'شهادات واعتمادات مهنية' ) ) ); ?></span></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $match && '' !== $match['text'] ) : ?>
				<div class="eeat-card__match">
					<span class="eeat-card__match-icon"><i class="<?php echo esc_attr( $match['icon'] ); ?>" aria-hidden="true"></i></span>
					<div>
						<strong>خبرته في <?php echo esc_html( $match['title'] ); ?><?php if ( $match['years'] ) : ?> — <span class="eeat-num"><?php echo esc_html( self::num( $match['years'] ) ); ?></span> <?php echo esc_html( self::years_label( $match['years'] ) ); ?><?php endif; ?></strong>
						<p><?php echo esc_html( $match['text'] ); ?></p>
					</div>
				</div>
			<?php elseif ( '' !== $d['short'] ) : ?>
				<p class="eeat-card__bio"><?php echo esc_html( $d['short'] ); ?></p>
			<?php endif; ?>

			<?php if ( $d['certs'] ) : ?>
				<div class="eeat-card__certs">
					<?php foreach ( array_slice( $d['certs'], 0, 3 ) as $c ) : ?>
						<span><i class="fa-solid fa-certificate" aria-hidden="true"></i><?php echo esc_html( $c['name'] ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<a class="eeat-card__more" href="<?php echo esc_url( $d['url'] ); ?>">الملف المهني الكامل وكل مقالات الكاتب<i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
		</section>
		<?php
	}

	/** صفحة الكاتب — الأقسام قبل شبكة المقالات. */
	public static function profile( $user_id ) {
		$d = self::data( $user_id );
		if ( ! $d ) {
			return;
		}
		$stats = array();
		if ( $d['years'] ) {
			$stats[] = array( self::num( $d['years'] ), self::years_label( $d['years'] ) . ' خبرة ميدانية', 'fa-solid fa-briefcase' );
		}
		if ( $d['projects'] ) {
			$stats[] = array( self::num( $d['projects'] ), self::count_label( $d['projects'], array( 'معاينة ومشروع منفّذ', 'معاينتان ومشروعان', 'معاينات ومشاريع منفّذة' ) ), 'fa-solid fa-clipboard-check' );
		}
		$stats[] = array( self::num( $d['posts'] ), self::count_label( $d['posts'], array( 'مقال منشور', 'مقالان منشوران', 'مقالات منشورة' ) ), 'fa-solid fa-newspaper' );
		if ( $d['certs'] ) {
			$stats[] = array( self::num( count( $d['certs'] ) ), self::count_label( count( $d['certs'] ), array( 'شهادة معتمدة', 'شهادتان معتمدتان', 'شهادات معتمدة' ) ), 'fa-solid fa-award' );
		}
		?>
		<div class="eeat-profile" dir="rtl">

			<section class="eeat-hero">
				<div class="eeat-hero__photo">
					<img src="<?php echo esc_url( $d['photo'] ); ?>" alt="<?php echo esc_attr( $d['name'] ); ?>" width="220" height="220">
				</div>
				<div class="eeat-hero__body">
					<?php if ( $d['verified'] ) { echo self::verified_badge(); } // phpcs:ignore ?>
					<h1><?php echo esc_html( $d['name'] ); ?></h1>
					<?php if ( '' !== $d['job'] ) : ?><p class="eeat-hero__job"><?php echo esc_html( $d['job'] ); ?></p><?php endif; ?>
					<?php if ( '' !== $d['short'] ) : ?><p class="eeat-hero__lead"><?php echo esc_html( $d['short'] ); ?></p><?php endif; ?>
					<div class="eeat-hero__meta">
						<?php if ( '' !== $d['region'] ) : ?>
							<span><i class="fa-solid fa-location-dot" aria-hidden="true"></i><?php echo esc_html( $d['region'] ); ?></span>
						<?php endif; ?>
						<?php foreach ( array_slice( $d['expertise'], 0, 4 ) as $e ) : ?>
							<span><i class="<?php echo esc_attr( $e['icon'] ); ?>" aria-hidden="true"></i><?php echo esc_html( $e['title'] ); ?></span>
						<?php endforeach; ?>
					</div>
					<?php echo self::social_links( $d['social'], 'eeat-social' ); // phpcs:ignore ?>
				</div>
			</section>

			<div class="eeat-stats eeat-stats--<?php echo count( $stats ); ?>">
				<?php foreach ( $stats as $s ) : ?>
					<div class="eeat-stat">
						<i class="<?php echo esc_attr( $s[2] ); ?>" aria-hidden="true"></i>
						<b class="eeat-num"><?php echo esc_html( $s[0] ); ?></b>
						<span><?php echo esc_html( $s[1] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( '' !== $d['about'] ) : ?>
				<section class="eeat-block">
					<h2 class="eeat-title"><i class="fa-solid fa-user-tie" aria-hidden="true"></i>نبذة عن الكاتب</h2>
					<div class="eeat-about"><?php echo wp_kses_post( wpautop( $d['about'] ) ); ?></div>
				</section>
			<?php endif; ?>

			<?php if ( $d['expertise'] ) : ?>
				<section class="eeat-block">
					<h2 class="eeat-title"><i class="fa-solid fa-helmet-safety" aria-hidden="true"></i>الخبرة الميدانية حسب القسم</h2>
					<div class="eeat-exp">
						<?php foreach ( $d['expertise'] as $e ) : ?>
							<?php
							$link = $e['cat'] ? get_term_link( (int) $e['cat'], 'category' ) : '';
							$link = is_wp_error( $link ) ? '' : $link;
							?>
							<article class="eeat-exp__item">
								<div class="eeat-exp__head">
									<span class="eeat-exp__icon"><i class="<?php echo esc_attr( $e['icon'] ); ?>" aria-hidden="true"></i></span>
									<h3><?php echo esc_html( $e['title'] ); ?></h3>
									<?php if ( $e['years'] ) : ?><span class="eeat-exp__years"><b class="eeat-num"><?php echo esc_html( self::num( $e['years'] ) ); ?></b> <?php echo esc_html( self::years_label( $e['years'] ) ); ?></span><?php endif; ?>
								</div>
								<?php if ( '' !== $e['text'] ) : ?><p><?php echo esc_html( $e['text'] ); ?></p><?php endif; ?>
								<?php if ( $link ) : ?>
									<a class="eeat-exp__link" href="<?php echo esc_url( $link ); ?>">مقالاته في هذا القسم<i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $d['certs'] || $d['members'] ) : ?>
				<div class="eeat-split<?php echo ( $d['certs'] && $d['members'] ) ? '' : ' eeat-split--single'; ?>">
					<?php if ( $d['certs'] ) : ?>
						<section class="eeat-block">
							<h2 class="eeat-title"><i class="fa-solid fa-award" aria-hidden="true"></i>الشهادات والاعتمادات</h2>
							<ul class="eeat-list">
								<?php foreach ( $d['certs'] as $c ) : ?>
									<li>
										<span class="eeat-list__icon"><i class="fa-solid fa-certificate" aria-hidden="true"></i></span>
										<div>
											<strong><?php echo esc_html( $c['name'] ); ?></strong>
											<?php if ( '' !== $c['issuer'] || '' !== (string) $c['year'] ) : ?>
												<small><?php echo esc_html( $c['issuer'] ); ?><?php if ( '' !== $c['issuer'] && '' !== (string) $c['year'] ) { echo ' · '; } ?><?php if ( '' !== (string) $c['year'] ) : ?><span class="eeat-num"><?php echo esc_html( $c['year'] ); ?></span><?php endif; ?></small>
											<?php endif; ?>
										</div>
										<?php if ( '' !== $c['url'] ) : ?><a href="<?php echo esc_url( $c['url'] ); ?>" target="_blank" rel="noopener nofollow" class="eeat-list__verify">تحقّق</a><?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endif; ?>
					<?php if ( $d['members'] ) : ?>
						<section class="eeat-block">
							<h2 class="eeat-title"><i class="fa-solid fa-people-group" aria-hidden="true"></i>العضويات المهنية</h2>
							<ul class="eeat-list">
								<?php foreach ( $d['members'] as $mb ) : ?>
									<li>
										<span class="eeat-list__icon"><i class="fa-solid fa-building-columns" aria-hidden="true"></i></span>
										<div>
											<strong><?php echo esc_html( $mb['name'] ); ?></strong>
											<?php if ( '' !== $mb['role'] ) : ?><small><?php echo esc_html( $mb['role'] ); ?></small><?php endif; ?>
										</div>
										<?php if ( '' !== $mb['url'] ) : ?><a href="<?php echo esc_url( $mb['url'] ); ?>" target="_blank" rel="noopener nofollow" class="eeat-list__verify">زيارة</a><?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $d['highlights'] ) : ?>
				<section class="eeat-block">
					<h2 class="eeat-title"><i class="fa-solid fa-trophy" aria-hidden="true"></i>سجل الإنجازات</h2>
					<ul class="eeat-highlights">
						<?php foreach ( $d['highlights'] as $h ) : ?>
							<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php echo esc_html( $h ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<h2 class="eeat-title eeat-title--posts"><i class="fa-solid fa-newspaper" aria-hidden="true"></i>مقالات <?php echo esc_html( $d['name'] ); ?></h2>
		</div>
		<?php
	}
}

AuthorEEAT::init();
