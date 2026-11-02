<?php
/**
 * إعدادات القالب — لوحة خفيفة بدون مكتبات خارجية، محمية بـ nonce + manage_options
 * كل خيار يُحفظ باسم alz_{id} ويُقرأ عبر Alz::opt('id')
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AlzOptions {

	public static function fields() {
		return array(
			'general' => array(
				'title'  => 'الإعدادات العامة',
				'fields' => array(
					array( 'id' => 'brand_name', 'label' => 'اسم المنصة', 'type' => 'text', 'default' => 'QurTap' ),
					array( 'id' => 'brand_name_en', 'label' => 'الاسم بالإنجليزية', 'type' => 'text', 'default' => 'SMART NFC STAND' ),
					array( 'id' => 'logo_url', 'label' => 'الشعار (اختياري — بدلاً من الشعار الافتراضي)', 'type' => 'media' ),
					array( 'id' => 'order_whatsapp', 'label' => 'رقم واتساب استقبال الطلبات (صيغة دولية بدون +)', 'type' => 'text', 'placeholder' => '201000000000' ),
					array( 'id' => 'order_message', 'label' => 'رسالة الطلب الافتراضية', 'type' => 'text', 'default' => 'مرحباً، أريد طلب استاند QurTap الذكي' ),
				),
			),
			'auth'    => array(
				'title'  => 'تسجيل دخول العملاء',
				'fields' => array(
					array( 'id' => 'allow_signup', 'label' => 'السماح بإنشاء حسابات جديدة', 'type' => 'checkbox', 'default' => '1' ),
					array( 'id' => 'google_client_id', 'label' => 'Google Client ID', 'type' => 'text', 'ltr' => true ),
					array( 'id' => 'google_client_secret', 'label' => 'Google Client Secret', 'type' => 'secret', 'ltr' => true ),
					array( 'id' => '_google_help', 'type' => 'note', 'label' => 'Authorized redirect URI في Google Cloud Console:', 'value' => home_url( '/dashboard/login/google-callback/' ) ),
				),
			),
			'landing' => array(
				'title'  => 'الصفحة الرئيسية',
				'fields' => array(
					array( 'id' => 'hero_badge', 'label' => 'شارة أعلى العنوان', 'type' => 'text', 'default' => 'NFC + QR في استاند واحد' ),
					array( 'id' => 'hero_title', 'label' => 'العنوان الرئيسي', 'type' => 'text', 'default' => 'استاند واحد يفتح' ),
					array( 'id' => 'hero_highlight', 'label' => 'الجزء الملوّن من العنوان', 'type' => 'text', 'default' => 'كل روابط نشاطك' ),
					array( 'id' => 'hero_text', 'label' => 'النص التعريفي', 'type' => 'textarea', 'default' => 'الاستاندات التقليدية تفتح رابطاً واحداً فقط. استاند QurTap يفتح لزبونك صفحة ذكية فيها السوشيال ميديا، تقييم جوجل، اللوكيشن، إنستاباي والمحافظ الإلكترونية — وتعدّلها أنت في أي وقت بدون تغيير الاستاند.' ),
					array( 'id' => 'offer_badge', 'label' => 'شارة العرض', 'type' => 'text', 'default' => 'عرض لفترة محدودة' ),
					array( 'id' => 'offer_title', 'label' => 'عنوان العرض', 'type' => 'text', 'default' => 'اطلب استاندك الذكي الآن واحصل على تصميم مجاني بهوية نشاطك' ),
					array( 'id' => 'offer_text', 'label' => 'تفاصيل العرض', 'type' => 'textarea', 'default' => 'لوحة تحكم مجانية، تعديلات غير محدودة، ودعم فني لتجهيز صفحتك بالكامل.' ),
					array( 'id' => 'footer_text', 'label' => 'نص الفوتر', 'type' => 'text' ),
				),
			),
			'code'    => array(
				'title'  => 'أكواد إضافية',
				'fields' => array(
					array( 'id' => 'site_head_code', 'label' => 'كود داخل <head> لصفحات الموقع (Analytics مثلاً)', 'type' => 'code' ),
					array( 'id' => 'profile_head_code', 'label' => 'كود داخل <head> لصفحات العملاء (Pixel / Analytics)', 'type' => 'code' ),
				),
			),
		);
	}

	/** قيمة الخيار أو القيمة الافتراضية المعرّفة في الحقول */
	public static function get( $id ) {
		$val = get_option( 'alz_' . $id, '' );
		if ( '' !== $val && false !== $val ) {
			return $val;
		}
		foreach ( self::fields() as $section ) {
			foreach ( $section['fields'] as $f ) {
				if ( $f['id'] === $id ) {
					return isset( $f['default'] ) ? $f['default'] : '';
				}
			}
		}
		return '';
	}

	public static function order_link( $message = '' ) {
		$num = preg_replace( '/\D/', '', (string) get_option( 'alz_order_whatsapp', '' ) );
		$msg = $message ? $message : self::get( 'order_message' );
		return $num ? 'https://wa.me/' . $num . '?text=' . rawurlencode( $msg ) : home_url( '/#offer' );
	}

	public static function boot() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'defaults' ) );
		add_action( 'init', array( __CLASS__, 'rebrand' ), 1 );
	}

	/** ترحيل الاسم القديم (لايس ستور) إلى QurTap في الإعدادات المحفوظة — مرة واحدة */
	public static function rebrand() {
		if ( get_option( 'alz_rebrand_qurtap' ) ) {
			return;
		}
		$old = array( 'لايس ستور', 'Lais Store', 'LAIS STORE' );
		foreach ( array( 'brand_name', 'brand_name_en', 'order_message', 'hero_text', 'offer_title', 'offer_text', 'footer_text' ) as $id ) {
			$v = get_option( 'alz_' . $id );
			if ( is_string( $v ) && '' !== $v ) {
				$n = 'brand_name_en' === $id && 'LAIS STORE' === $v ? 'SMART NFC STAND' : str_replace( $old, 'QurTap', $v );
				if ( $n !== $v ) {
					update_option( 'alz_' . $id, $n );
				}
			}
		}
		$role = get_role( Alz::ROLE );
		if ( $role ) {
			remove_role( Alz::ROLE );
			add_role( Alz::ROLE, 'عميل QurTap', $role->capabilities );
		}
		update_option( 'alz_rebrand_qurtap', 1 );
	}

	/** يكتب القيم الافتراضية مرة واحدة */
	public static function defaults() {
		if ( get_option( 'alz_defaults_set' ) ) {
			return;
		}
		foreach ( self::fields() as $section ) {
			foreach ( $section['fields'] as $f ) {
				if ( isset( $f['default'] ) && false === get_option( 'alz_' . $f['id'] ) ) {
					update_option( 'alz_' . $f['id'], $f['default'] );
				}
			}
		}
		update_option( 'alz_defaults_set', 1 );
	}

	public static function menu() {
		add_menu_page( 'QurTap', 'QurTap', 'manage_options', 'alzahby', array( __CLASS__, 'page' ), 'dashicons-smartphone', 3 );
		add_submenu_page( 'alzahby', 'إعدادات القالب', 'إعدادات القالب', 'manage_options', 'alzahby', array( __CLASS__, 'page' ) );
	}

	private static function save() {
		check_admin_referer( 'alz_options' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'غير مسموح' );
		}
		foreach ( self::fields() as $section ) {
			foreach ( $section['fields'] as $f ) {
				if ( 'note' === $f['type'] ) {
					continue;
				}
				$key = 'alz_' . $f['id'];
				$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore
				switch ( $f['type'] ) {
					case 'checkbox':
						$val = '' !== $raw ? '1' : '0';
						break;
					case 'textarea':
						$val = sanitize_textarea_field( $raw );
						break;
					case 'media':
						$val = esc_url_raw( $raw );
						break;
					case 'secret':
						if ( '' === $raw ) {
							continue 2; // الحقل الفارغ = عدم التغيير
						}
						$val = sanitize_text_field( $raw );
						break;
					case 'code':
						// الأكواد الخام فقط لمن يملك صلاحية unfiltered_html
						$val = current_user_can( 'unfiltered_html' ) ? $raw : wp_kses_post( $raw );
						break;
					default:
						$val = sanitize_text_field( $raw );
				}
				if ( 'order_whatsapp' === $f['id'] ) {
					$val = preg_replace( '/\D/', '', $val );
				}
				update_option( $key, $val );
			}
		}
	}

	public static function page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$saved = false;
		if ( isset( $_POST['alz_options_submit'] ) ) {
			self::save();
			$saved = true;
		}
		wp_enqueue_media();
		?>
		<div class="wrap" dir="rtl">
			<h1>إعدادات QurTap</h1>
			<?php if ( $saved ) : ?>
				<div class="notice notice-success is-dismissible"><p>تم حفظ الإعدادات.</p></div>
			<?php endif; ?>
			<form method="post">
				<?php wp_nonce_field( 'alz_options' ); ?>
				<input type="hidden" name="alz_options_submit" value="1">
				<?php foreach ( self::fields() as $section ) : ?>
					<h2 class="title"><?php echo esc_html( $section['title'] ); ?></h2>
					<table class="form-table" role="presentation"><tbody>
					<?php
					foreach ( $section['fields'] as $f ) :
						$key = 'alz_' . $f['id'];
						$val = get_option( $key, isset( $f['default'] ) ? $f['default'] : '' );
						$dir = ! empty( $f['ltr'] ) ? ' dir="ltr"' : '';
						?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $f['label'] ); ?></label></th>
							<td>
							<?php
							switch ( $f['type'] ) {
								case 'note':
									echo '<code dir="ltr">' . esc_html( $f['value'] ) . '</code>';
									break;
								case 'checkbox':
									echo '<input type="checkbox" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="1" ' . checked( '1', (string) $val, false ) . '>';
									break;
								case 'textarea':
									echo '<textarea class="large-text" rows="3" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( $val ) . '</textarea>';
									break;
								case 'code':
									echo '<textarea class="large-text code" dir="ltr" rows="5" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( $val ) . '</textarea>';
									break;
								case 'secret':
									echo '<input type="password" class="regular-text" dir="ltr" autocomplete="new-password" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" placeholder="' . ( $val ? '•••••••• (محفوظ — اتركه فارغاً للإبقاء عليه)' : '' ) . '">';
									break;
								case 'media':
									echo '<input type="url" class="regular-text" dir="ltr" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '"> ';
									echo '<button type="button" class="button alz-media" data-target="' . esc_attr( $key ) . '">اختر صورة</button>';
									break;
								default:
									echo '<input type="text" class="regular-text"' . $dir . ' id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" placeholder="' . esc_attr( isset( $f['placeholder'] ) ? $f['placeholder'] : '' ) . '">'; // phpcs:ignore
							}
							?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody></table>
				<?php endforeach; ?>
				<?php submit_button( 'حفظ الإعدادات' ); ?>
			</form>
		</div>
		<script>
		document.querySelectorAll(".alz-media").forEach(function (b) {
			b.addEventListener("click", function () {
				var f = wp.media({ title: "اختر صورة", multiple: false, library: { type: "image" } });
				f.on("select", function () {
					document.getElementById(b.dataset.target).value = f.state().get("selection").first().toJSON().url;
				});
				f.open();
			});
		});
		</script>
		<?php
	}
}

AlzOptions::boot();
