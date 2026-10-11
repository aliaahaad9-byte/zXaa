<?php
/**
 * [Widget] إنجازاتنا بالأرقام — بطاقات أرقام ملوّنة + شريط ثقة.
 * القيم تُحرَّر من «المظهر ← الودجات ← الصفحة الرئيسية». عند ترك الحقول فارغة تظهر قيم افتراضية.
 */
class Theme__WidgetModel__stats extends WP_Widget {

    function __construct() {
        parent::__construct( 'yc_stats', '[Widget] إنجازاتنا بالأرقام', array( 'classname' => 'theme__stats' ) );
    }

    static function icons() {
        return array(
            'check'  => '<circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/>',
            'users'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
            'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
            'award'  => '<circle cx="12" cy="8" r="6"/><path d="M15.48 12.89L17 22l-5-3-5 3 1.52-9.11"/>',
            'wrench' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
            'pin'    => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
            'clock'  => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            'star'   => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="currentColor"/>',
            'home'   => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        );
    }

    static function svg( $k ) {
        $i = self::icons();
        $k = isset( $i[ $k ] ) ? $k : 'check';
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $i[ $k ] . '</svg>';
    }

    static function defaults() {
        return array(
            'badge'   => 'شركة التنفيذ — رواد الخدمات المنزلية في المملكة',
            'title'   => 'إنجازاتنا بالأرقام',
            'content' => 'نفخر بتقديم خدمات منزلية متميزة تعكس التزامنا بالجودة والاحترافية عبر مسيرتنا الممتدة، أرقامنا شهادة على ثقة عملائنا.',
            'stats'   => array(
                array( 'num' => '13,600', 'pre' => '+', 'suf' => '', 'label' => 'خدمة منجزة', 'icon' => 'check', 'color' => 'blue' ),
                array( 'num' => '7,000', 'pre' => '+', 'suf' => '', 'label' => 'عميل سعيد', 'icon' => 'users', 'color' => 'green' ),
                array( 'num' => '100', 'pre' => '', 'suf' => '%', 'label' => 'ضمان الجودة', 'icon' => 'shield', 'color' => 'amber' ),
                array( 'num' => '25', 'pre' => '', 'suf' => 'سنة', 'label' => 'خبرة في المجال', 'icon' => 'award', 'color' => 'red' ),
                array( 'num' => '40', 'pre' => '+', 'suf' => '', 'label' => 'فني متخصص', 'icon' => 'wrench', 'color' => 'blue' ),
                array( 'num' => '15', 'pre' => '', 'suf' => 'مدينة', 'label' => 'تغطية في المملكة', 'icon' => 'pin', 'color' => 'green' ),
                array( 'num' => '24/7', 'pre' => '', 'suf' => '', 'label' => 'دعم فني متواصل', 'icon' => 'clock', 'color' => 'amber' ),
                array( 'num' => '4.9', 'pre' => '', 'suf' => '/5', 'label' => 'تقييم العملاء', 'icon' => 'star', 'color' => 'red' ),
            ),
            'trust'   => array(
                array( 'title' => 'ضمان 15 سنة', 'sub' => 'على جميع أعمال المنازل والمنشآت التجارية', 'icon' => 'shield', 'color' => 'green' ),
                array( 'title' => 'مواد آمنة', 'sub' => 'معتمدة لمياه الشرب', 'icon' => 'check', 'color' => 'blue' ),
                array( 'title' => 'تقييم ممتاز', 'sub' => 'من آلاف العملاء', 'icon' => 'star', 'color' => 'amber' ),
            ),
        );
    }

    /** يعرض الرقم: البادئة والرموز (+ % /5) بجانب الرقم، والكلمات العربية (سنة/مدينة) بعده. */
    static function number( $s ) {
        $suf      = trim( (string) $s['suf'] );
        $word_suf = '' !== $suf && preg_match( '/\p{Arabic}/u', $suf );
        $num      = trim( (string) $s['num'] );
        $count    = preg_match( '/^\d[\d,]*(\.\d+)?$/', $num ) ? ' data-count="' . esc_attr( str_replace( ',', '', $num ) ) . '"' : '';
        $h  = '<b class="yc-st__num">';
        $h .= '<span class="yc-st__n" dir="ltr">';
        if ( '' !== trim( (string) $s['pre'] ) ) {
            $h .= '<small>' . esc_html( $s['pre'] ) . '</small>';
        }
        $h .= '<span' . $count . '>' . esc_html( $num ) . '</span>';
        if ( '' !== $suf && ! $word_suf ) {
            $h .= '<small>' . esc_html( $suf ) . '</small>';
        }
        $h .= '</span>';
        if ( $word_suf ) {
            $h .= ' <span class="yc-st__w">' . esc_html( $suf ) . '</span>';
        }
        return $h . '</b>';
    }

    public function widget( $args, $instance ) {
        $d       = self::defaults();
        $badge   = isset( $instance['badge'] ) && '' !== trim( $instance['badge'] ) ? $instance['badge'] : $d['badge'];
        $title   = isset( $instance['title'] ) && '' !== trim( $instance['title'] ) ? $instance['title'] : $d['title'];
        $content = isset( $instance['content'] ) && '' !== trim( $instance['content'] ) ? $instance['content'] : $d['content'];
        $stats   = ! empty( $instance['stats'] ) && is_array( $instance['stats'] ) ? array_values( $instance['stats'] ) : $d['stats'];
        $trust   = ! empty( $instance['trust'] ) && is_array( $instance['trust'] ) ? array_values( $instance['trust'] ) : $d['trust'];
        $colors  = array( 'blue', 'green', 'amber', 'red' );

        $css = get_template_directory() . '/components/packs/Widgets/assets/css/stats.css';
        if ( file_exists( $css ) ) {
            echo '<style>' . file_get_contents( $css ) . '</style>';
        }
        $uid = 'yst' . substr( md5( uniqid( '', true ) ), 0, 6 );

        echo '<section class="yc-st" id="' . esc_attr( $uid ) . '" section-concept="true" aria-labelledby="' . esc_attr( $uid ) . '-h">';
        echo '<div class="container">';
            echo '<div class="yc-st__head">';
                if ( '' !== trim( $badge ) ) {
                    echo '<span class="yc-st__badge">' . self::svg( 'award' ) . esc_html( $badge ) . '</span>';
                }
                echo '<h2 id="' . esc_attr( $uid ) . '-h">' . esc_html( $title ) . '</h2>';
                if ( '' !== trim( $content ) ) {
                    echo '<p>' . esc_html( $content ) . '</p>';
                }
            echo '</div>';

            echo '<div class="yc-st__grid">';
            foreach ( $stats as $i => $s ) {
                $s = wp_parse_args( (array) $s, array( 'num' => '', 'pre' => '', 'suf' => '', 'label' => '', 'icon' => 'check', 'color' => '' ) );
                if ( '' === trim( $s['num'] ) && '' === trim( $s['label'] ) ) {
                    continue;
                }
                $c = in_array( $s['color'], $colors, true ) ? $s['color'] : $colors[ $i % 4 ];
                echo '<div class="yc-st__card is-' . esc_attr( $c ) . '">';
                    echo '<div class="yc-st__txt">' . self::number( $s ) . '<span class="yc-st__label">' . esc_html( $s['label'] ) . '</span></div>';
                    echo '<span class="yc-st__ic">' . self::svg( $s['icon'] ) . '</span>';
                echo '</div>';
            }
            echo '</div>';

            $trust = array_filter( $trust, function ( $t ) { return ! empty( $t['title'] ); } );
            if ( $trust ) {
                echo '<ul class="yc-st__trust">';
                foreach ( array_values( $trust ) as $i => $t ) {
                    $t = wp_parse_args( (array) $t, array( 'title' => '', 'sub' => '', 'icon' => 'check', 'color' => '' ) );
                    $c = in_array( $t['color'], $colors, true ) ? $t['color'] : $colors[ $i % 4 ];
                    echo '<li class="is-' . esc_attr( $c ) . '"><span class="yc-st__tic">' . self::svg( $t['icon'] ) . '</span><div><b>' . esc_html( $t['title'] ) . '</b>';
                    if ( '' !== trim( $t['sub'] ) ) {
                        echo '<span>' . esc_html( $t['sub'] ) . '</span>';
                    }
                    echo '</div></li>';
                }
                echo '</ul>';
            }
        echo '</div>';
        echo '</section>';
        ?>
<script>
(function(){
  var root=document.getElementById('<?php echo esc_js( $uid ); ?>'); if(!root){return;}
  var els=root.querySelectorAll('[data-count]');
  if(!els.length||!('IntersectionObserver' in window)){return;}
  if(window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches){return;}
  function fmt(v,dec){return v.toLocaleString('en-US',{minimumFractionDigits:dec,maximumFractionDigits:dec});}
  var io=new IntersectionObserver(function(entries){
    entries.forEach(function(en){
      if(!en.isIntersecting){return;}
      io.unobserve(en.target);
      var el=en.target, end=parseFloat(el.getAttribute('data-count')), txt=el.textContent;
      var dec=(el.getAttribute('data-count').split('.')[1]||'').length, t0=null, dur=1400;
      function step(ts){ if(!t0){t0=ts;} var p=Math.min((ts-t0)/dur,1); var e=1-Math.pow(1-p,3);
        el.textContent=fmt(end*e,dec); if(p<1){requestAnimationFrame(step);}else{el.textContent=txt;} }
      requestAnimationFrame(step);
    });
  },{threshold:.4});
  Array.prototype.forEach.call(els,function(el){io.observe(el);});
})();
</script>
        <?php
    }

    public function form( $instance ) {
        $icons  = array( 'check' => 'علامة صح', 'users' => 'عملاء', 'shield' => 'درع / ضمان', 'award' => 'وسام / خبرة', 'wrench' => 'مفتاح / فني', 'pin' => 'موقع / مدن', 'clock' => 'ساعة / دعم', 'star' => 'نجمة / تقييم', 'home' => 'منزل' );
        $colors = array( 'blue' => 'أزرق', 'green' => 'أخضر', 'amber' => 'ذهبي', 'red' => 'أحمر' );
        echo '<p style="margin:8px 0;color:#646970">اترك الحقول فارغة لعرض القيم الافتراضية. اللون «إختر» = تلقائي بالتناوب.</p>';
        WidgetInput( array(
            'instance' => $instance, 'title' => 'العنوان والوصف', 'singleID' => 'stats_intro',
            'id' => $this->get_field_id( 'stats_intro' ), 'name' => $this->get_field_name( 'stats_intro' ),
            'stack' => array(
                array( 'title' => 'الشارة أعلى العنوان', 'type' => 'text', 'id' => $this->get_field_id( 'badge' ), 'singleID' => 'badge', 'name' => $this->get_field_name( 'badge' ) ),
                array( 'title' => 'العنوان', 'type' => 'text', 'id' => $this->get_field_id( 'title' ), 'singleID' => 'title', 'name' => $this->get_field_name( 'title' ) ),
                array( 'title' => 'الوصف', 'type' => 'textarea', 'id' => $this->get_field_id( 'content' ), 'singleID' => 'content', 'name' => $this->get_field_name( 'content' ) ),
            ),
            'repeat' => false,
        ) );
        WidgetInput( array(
            'instance' => $instance, 'title' => 'بطاقة رقم', 'singleID' => 'stats',
            'id' => $this->get_field_id( 'stats' ), 'name' => $this->get_field_name( 'stats' ),
            'stack' => array(
                array( 'title' => 'الرقم (مثل 13,600 أو 4.9 أو 24/7)', 'type' => 'text', 'id' => $this->get_field_id( 'num' ), 'singleID' => 'num', 'name' => $this->get_field_name( 'num' ) ),
                array( 'title' => 'قبل الرقم (مثل +)', 'type' => 'text', 'id' => $this->get_field_id( 'pre' ), 'singleID' => 'pre', 'name' => $this->get_field_name( 'pre' ) ),
                array( 'title' => 'بعد الرقم (مثل % أو /5 أو سنة)', 'type' => 'text', 'id' => $this->get_field_id( 'suf' ), 'singleID' => 'suf', 'name' => $this->get_field_name( 'suf' ) ),
                array( 'title' => 'الوصف تحت الرقم', 'type' => 'text', 'id' => $this->get_field_id( 'label' ), 'singleID' => 'label', 'name' => $this->get_field_name( 'label' ) ),
                array( 'title' => 'الأيقونة', 'type' => 'select', 'id' => $this->get_field_id( 'icon' ), 'singleID' => 'icon', 'name' => $this->get_field_name( 'icon' ), 'options' => $icons ),
                array( 'title' => 'اللون', 'type' => 'select', 'id' => $this->get_field_id( 'color' ), 'singleID' => 'color', 'name' => $this->get_field_name( 'color' ), 'options' => $colors ),
            ),
            'repeat' => true,
        ) );
        WidgetInput( array(
            'instance' => $instance, 'title' => 'ميزة في الشريط السفلي', 'singleID' => 'trust',
            'id' => $this->get_field_id( 'trust' ), 'name' => $this->get_field_name( 'trust' ),
            'stack' => array(
                array( 'title' => 'العنوان (مثل ضمان 15 سنة)', 'type' => 'text', 'id' => $this->get_field_id( 'title' ), 'singleID' => 'title', 'name' => $this->get_field_name( 'title' ) ),
                array( 'title' => 'الوصف', 'type' => 'text', 'id' => $this->get_field_id( 'sub' ), 'singleID' => 'sub', 'name' => $this->get_field_name( 'sub' ) ),
                array( 'title' => 'الأيقونة', 'type' => 'select', 'id' => $this->get_field_id( 'icon' ), 'singleID' => 'icon', 'name' => $this->get_field_name( 'icon' ), 'options' => $icons ),
                array( 'title' => 'اللون', 'type' => 'select', 'id' => $this->get_field_id( 'color' ), 'singleID' => 'color', 'name' => $this->get_field_name( 'color' ), 'options' => $colors ),
            ),
            'repeat' => true,
        ) );
    }

    public function update( $new_instance, $old_instance ) {
        $clean = function ( $v ) use ( &$clean ) {
            if ( is_array( $v ) ) {
                return array_map( $clean, $v );
            }
            return sanitize_text_field( stripslashes( (string) $v ) );
        };
        $instance = array();
        foreach ( (array) $new_instance as $k => $v ) {
            $instance[ $k ] = ( 'content' === $k ) ? sanitize_textarea_field( stripslashes( (string) $v ) ) : $clean( $v );
        }
        return $instance;
    }
}
function Theme__Widgets__stats() {
    register_widget( 'Theme__WidgetModel__stats' );
}
add_action( 'widgets_init', 'Theme__Widgets__stats' );
