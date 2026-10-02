<?php
/**
 * [Widget] إنجازاتنا بالأرقام — للصفحة الرئيسية.
 * عنوان ووصف، ثم شبكة أرقام، ثم شريط دعوة للتواصل عبر واتساب.
 * كل النصوص والأرقام قابلة للتعديل من المظهر ← الودجات.
 */
class Theme__WidgetModel__achievements extends WP_Widget {

    function __construct() {
        parent::__construct( 'achievements', '[Widget] إنجازاتنا بالأرقام', array( 'classname' => 'theme__achievements' ) );
    }

    /** أيقونات جاهزة للأرقام (Font Awesome المحلي في القالب). */
    public static function icons() {
        return array(
            'fa-solid fa-circle-check'    => 'خدمة منجزة',
            'fa-solid fa-face-smile'      => 'عملاء سعداء',
            'fa-solid fa-shield-halved'   => 'ضمان',
            'fa-solid fa-briefcase'       => 'سنوات خبرة',
            'fa-solid fa-flag'            => 'تأسيس',
            'fa-solid fa-location-dot'    => 'مدن',
            'fa-solid fa-headset'         => 'دعم فني',
            'fa-solid fa-star'            => 'تقييم',
            'fa-solid fa-users'           => 'فريق',
            'fa-solid fa-award'           => 'جوائز',
            'fa-solid fa-truck-fast'      => 'سرعة',
            'fa-solid fa-house-circle-check' => 'منازل',
        );
    }

    /** المحتوى المبدئي عند إضافة الويدجيت أول مرة — يُعدَّل من لوحة الودجات. */
    public static function defaults() {
        return array(
            'eyebrow'  => 'إنجازاتنا بالأرقام',
            'title'    => 'إنجازات شركة الألمانية',
            'content'  => 'تتربع شركة الألمانية على قمة قطاع الخدمات المنزلية في المملكة العربية السعودية، حيث استطاعت على مدار عقود أن تبني جسوراً متينة من الثقة مع عملائها.',
            'cta_title'  => 'انضم إلى آلاف العملاء السعداء',
            'cta_text'   => 'احصل على خدمة احترافية بضمان حقيقي وأسعار شفافة',
            'cta_button' => 'ابدأ الآن عبر واتساب',
            'whatsapp'   => '',
            'stats'    => array(
                array( 'value' => '12,476+', 'label' => 'خدمة منجزة',           'icon' => 'fa-solid fa-circle-check' ),
                array( 'value' => '10,620+', 'label' => 'عميل سعيد',            'icon' => 'fa-solid fa-face-smile' ),
                array( 'value' => '100%',    'label' => 'ضمان الجودة',          'icon' => 'fa-solid fa-shield-halved' ),
                array( 'value' => '21',      'label' => 'سنة خبرة في المجال',   'icon' => 'fa-solid fa-briefcase' ),
                array( 'value' => '2005',    'label' => 'تأسست عام',            'icon' => 'fa-solid fa-flag' ),
                array( 'value' => '6',       'label' => 'مدن تغطية في المملكة', 'icon' => 'fa-solid fa-location-dot' ),
                array( 'value' => '24/7',    'label' => 'دعم فني متواصل',       'icon' => 'fa-solid fa-headset' ),
                array( 'value' => '5/5',     'label' => 'تقييم العملاء',        'icon' => 'fa-solid fa-star' ),
            ),
        );
    }

    private static function prepare( $instance ) {
        // لم يُحفظ الويدجيت بعد ← نعرض المحتوى المبدئي
        if ( empty( $instance ) || ! isset( $instance['stats'] ) ) {
            return self::defaults();
        }
        return wp_parse_args( $instance, array_merge( self::defaults(), array( 'stats' => array() ) ) );
    }

    /**
     * هل يُعدّ الرقم تصاعديًا عند الظهور؟ فقط الأرقام الصريحة مثل 12,476+ أو 100% أو 21.
     * لا يُعدّ: التواريخ (2005) ولا الكسور/النسب (24/7، 5/5) لأن عدّها من الصفر بلا معنى.
     */
    private static function countable( $value ) {
        if ( ! preg_match( '/^([^\d]*)([\d,]+)([^\d\/]*)$/u', $value, $m ) ) {
            return null;
        }
        $n = (int) str_replace( ',', '', $m[2] );
        if ( false === strpos( $m[2], ',' ) && '' === trim( $m[1] . $m[3] ) && $n >= 1900 && $n <= 2100 ) {
            return null;
        }
        return array( 'prefix' => $m[1], 'n' => $n, 'suffix' => $m[3], 'comma' => false !== strpos( $m[2], ',' ) || $n >= 1000 );
    }

    public function widget( $args, $instance ) {
        $d        = self::prepare( $instance );
        $whatsapp = preg_replace( '/[^0-9]/', '', ( '' !== trim( (string) $d['whatsapp'] ) ) ? $d['whatsapp'] : (string) get_option( 'Whatsapp' ) );
        $stats    = array_values( array_filter( (array) $d['stats'], function ( $s ) {
            return is_array( $s ) && '' !== trim( (string) ( isset( $s['value'] ) ? $s['value'] : '' ) );
        } ) );
        $uid      = 'ach-' . substr( md5( $this->id ), 0, 8 );
        $count    = count( $stats );
        $cols     = ( 0 === $count % 4 ) ? 4 : ( ( 0 === $count % 3 ) ? 3 : min( 4, max( 1, $count ) ) );

        echo '<section class="ach" id="' . esc_attr( $uid ) . '" aria-labelledby="' . esc_attr( $uid ) . '-title">';
            echo '<div class="container">';

                echo '<div class="ach__head">'; // div وليس header: القالب يجعل كل header لاصقًا بخلفية بيضاء
                    if ( '' !== trim( $d['eyebrow'] ) ) {
                        echo '<span class="ach__eyebrow"><i class="fa-solid fa-trophy" aria-hidden="true"></i>' . esc_html( $d['eyebrow'] ) . '</span>';
                    }
                    if ( '' !== trim( $d['title'] ) ) {
                        echo '<h2 class="ach__title" id="' . esc_attr( $uid ) . '-title">' . esc_html( $d['title'] ) . '</h2>';
                    }
                    if ( '' !== trim( $d['content'] ) ) {
                        echo '<p class="ach__lead">' . esc_html( $d['content'] ) . '</p>';
                    }
                echo '</div>';

                if ( $stats ) {
                    echo '<ul class="ach__grid ach__grid--' . (int) $cols . '">';
                    foreach ( $stats as $s ) {
                        $value = trim( (string) $s['value'] );
                        $icon  = isset( $s['icon'] ) && '' !== $s['icon'] ? $s['icon'] : 'fa-solid fa-circle-check';
                        $c     = IsSpeed() ? null : self::countable( $value );
                        $attrs = $c ? ' data-count="' . (int) $c['n'] . '" data-prefix="' . esc_attr( $c['prefix'] ) . '" data-suffix="' . esc_attr( $c['suffix'] ) . '" data-comma="' . ( $c['comma'] ? '1' : '0' ) . '"' : '';
                        echo '<li class="ach__item">';
                            echo '<span class="ach__icon"><i class="' . esc_attr( $icon ) . '" aria-hidden="true"></i></span>';
                            // القيمة النهائية مكتوبة في HTML دائمًا — العدّ مجرد حركة بصرية
                            echo '<strong class="ach__value" dir="ltr"' . $attrs . '>' . esc_html( $value ) . '</strong>';
                            echo '<span class="ach__label">' . esc_html( isset( $s['label'] ) ? $s['label'] : '' ) . '</span>';
                        echo '</li>';
                    }
                    echo '</ul>';
                }

                if ( '' !== trim( $d['cta_title'] ) || '' !== $whatsapp ) {
                    echo '<div class="ach__cta">';
                        echo '<div class="ach__cta-text">';
                            if ( '' !== trim( $d['cta_title'] ) ) {
                                echo '<h3>' . esc_html( $d['cta_title'] ) . '</h3>';
                            }
                            if ( '' !== trim( $d['cta_text'] ) ) {
                                echo '<p>' . esc_html( $d['cta_text'] ) . '</p>';
                            }
                        echo '</div>';
                        if ( '' !== $whatsapp ) {
                            echo '<a class="ach__btn" href="https://wa.me/' . esc_attr( $whatsapp ) . '" target="_blank" rel="nofollow noopener" data-call="whatsapp">';
                                echo '<i class="fa-brands fa-whatsapp" aria-hidden="true"></i>';
                                echo '<span>' . esc_html( '' !== trim( $d['cta_button'] ) ? $d['cta_button'] : 'واتساب' ) . '</span>';
                            echo '</a>';
                        }
                    echo '</div>';
                }

            echo '</div>';
        echo '</section>';

        self::script();
    }

    /** عدّاد تصاعدي عند ظهور الأرقام — مرة واحدة، ويحترم «تقليل الحركة». */
    private static function script() {
        static $printed = false;
        if ( $printed || IsSpeed() ) {
            return;
        }
        $printed = true;
        ?>
        <script>
        (function () {
            var els = document.querySelectorAll('.ach__value[data-count]');
            if (!els.length || !('IntersectionObserver' in window)) { return; }
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
            function fmt(n, comma) { n = Math.round(n); return comma ? n.toLocaleString('en-US') : String(n); }
            function run(el) {
                var end = +el.getAttribute('data-count'), pre = el.getAttribute('data-prefix') || '',
                    suf = el.getAttribute('data-suffix') || '', comma = el.getAttribute('data-comma') === '1',
                    dur = 1400, t0 = null;
                el.style.minWidth = el.offsetWidth + 'px';   // منع اهتزاز التخطيط أثناء العدّ
                function step(t) {
                    if (!t0) { t0 = t; }
                    var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
                    el.textContent = pre + fmt(end * e, comma) + suf;
                    if (p < 1) { requestAnimationFrame(step); }
                }
                requestAnimationFrame(step);
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) {
                    if (en.isIntersecting) { io.unobserve(en.target); run(en.target); }
                });
            }, { threshold: 0.4 });
            els.forEach(function (el) { io.observe(el); });
        })();
        </script>
        <?php
    }

    public function form( $instance ) {
        $instance = self::prepare( $instance );
        $text = function ( $label, $key, $type = 'text' ) {
            return array(
                'title'    => $label,
                'type'     => $type,
                'id'       => $this->get_field_id( $key ),
                'singleID' => $key,
                'name'     => $this->get_field_name( $key ),
            );
        };
        WidgetInput( array(
            'instance' => $instance,
            'title'    => 'العنوان والوصف',
            'singleID' => 'ach_intro',
            'id'       => $this->get_field_id( 'ach_intro' ),
            'name'     => $this->get_field_name( 'ach_intro' ),
            'stack'    => array(
                $text( 'السطر العلوي الصغير', 'eyebrow' ),
                $text( 'العنوان', 'title' ),
                $text( 'الوصف', 'content', 'textarea' ),
            ),
            'repeat'   => false,
        ) );
        WidgetInput( array(
            'instance' => $instance,
            'title'    => 'رقم',
            'singleID' => 'stats',
            'id'       => $this->get_field_id( 'stats' ),
            'name'     => $this->get_field_name( 'stats' ),
            'stack'    => array(
                $text( 'القيمة (مثال: 12,476+ أو 100% أو 24/7)', 'value' ),
                $text( 'الوصف', 'label' ),
                array(
                    'title'    => 'الأيقونة',
                    'type'     => 'select',
                    'id'       => $this->get_field_id( 'icon' ),
                    'singleID' => 'icon',
                    'name'     => $this->get_field_name( 'icon' ),
                    'options'  => self::icons(),
                ),
            ),
            'repeat'   => true,
        ) );
        WidgetInput( array(
            'instance' => $instance,
            'title'    => 'شريط الدعوة للتواصل',
            'singleID' => 'ach_cta',
            'id'       => $this->get_field_id( 'ach_cta' ),
            'name'     => $this->get_field_name( 'ach_cta' ),
            'stack'    => array(
                $text( 'العنوان', 'cta_title' ),
                $text( 'النص', 'cta_text' ),
                $text( 'نص الزر', 'cta_button' ),
                $text( 'رقم واتساب (اتركه فارغًا لاستخدام رقم إعدادات القالب)', 'whatsapp' ),
            ),
            'repeat'   => false,
        ) );
    }

    public function update( $new_instance, $old_instance ) {
        // إضافة الويدجيت دون إرسال النموذج (مثل الإضافة البرمجية) ← المحتوى المبدئي بدل حقول فارغة
        if ( ! isset( $new_instance['stats'] ) && ! isset( $new_instance['title'] ) && empty( $old_instance ) ) {
            return self::defaults();
        }
        $instance = array();
        foreach ( array( 'eyebrow', 'title', 'cta_title', 'cta_text', 'cta_button', 'whatsapp' ) as $k ) {
            $instance[ $k ] = isset( $new_instance[ $k ] ) ? sanitize_text_field( wp_unslash( $new_instance[ $k ] ) ) : '';
        }
        $instance['content'] = isset( $new_instance['content'] ) ? sanitize_textarea_field( wp_unslash( $new_instance['content'] ) ) : '';
        $icons = array_keys( self::icons() );
        $instance['stats'] = array();
        if ( isset( $new_instance['stats'] ) && is_array( $new_instance['stats'] ) ) {
            foreach ( $new_instance['stats'] as $k => $row ) {
                if ( '{key}' === (string) $k || ! is_array( $row ) ) {
                    continue;
                }
                $value = isset( $row['value'] ) ? sanitize_text_field( wp_unslash( $row['value'] ) ) : '';
                if ( '' === $value ) {
                    continue;
                }
                $icon = isset( $row['icon'] ) ? (string) $row['icon'] : '';
                $instance['stats'][] = array(
                    'value' => $value,
                    'label' => isset( $row['label'] ) ? sanitize_text_field( wp_unslash( $row['label'] ) ) : '',
                    'icon'  => in_array( $icon, $icons, true ) ? $icon : $icons[0],
                );
            }
        }
        return $instance;
    }
}

function Theme__Widgets__achievements() {
    register_widget( 'Theme__WidgetModel__achievements' );
}
add_action( 'widgets_init', 'Theme__Widgets__achievements' );

// تنسيق الويدجيت يُحمَّل فقط في الصفحات التي يظهر فيها
add_action( 'wp_head', function () {
    if ( is_active_widget( false, false, 'achievements', true ) ) {
        echo '<link rel="stylesheet" href="' . esc_url( get_template_directory_uri() . '/components/packs/Widgets/assets/css/achievements.css?ver=1.0.0' ) . '">';
    }
}, 30 );
