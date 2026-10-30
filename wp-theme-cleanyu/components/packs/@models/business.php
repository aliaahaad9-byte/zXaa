<?php
/**
 * نموذج الصفحة: قطاع الأعمال — صفحة خفيفة (بلا صور) بنموذج طلب عرض سعر في أعلاها يُرسل عبر واتساب.
 * المحتوى من وثيقة «محتوى صفحة قطاع الأعمال»؛ الأسئلة الشائعة تُقرأ من حقل faq إن وُجد.
 */
global $post;
$wa    = apply_filters( 'yc_business_wa', '966503317073' );
$phone = '0503317073';
$mail  = 'info@altnfez.com';
$h1    = 'خدمات التنظيف ومكافحة الحشرات والعزل لقطاع الأعمال في السعودية';

$pg = function ( $id, $fallback = '' ) {
    if ( $id && 'publish' === get_post_status( $id ) ) {
        return get_permalink( $id );
    }
    return '' !== $fallback ? home_url( $fallback ) : '';
};
$policy = $pg( 223254, '/' );

$services = array( 'تنظيف', 'مكافحة حشرات', 'عزل', 'كشف تسربات', 'تسليك مجاري', 'أكثر من خدمة' );
$sectors  = array( 'مطعم أو مقهى', 'مكتب أو شركة', 'فندق أو شقق مفروشة', 'مدرسة أو حضانة', 'منشأة صحية', 'مسجد', 'عمارة أو مجمع سكني', 'مستودع أو مصنع', 'مطور أو مقاول', 'أخرى' );
$cities   = array( 'الرياض', 'الدمام', 'الخبر', 'القطيف', 'جدة', 'مكة', 'المدينة المنورة', 'الأحساء', 'سيهات', 'أخرى' );

// القطاعات: الاسم | ما نقدمه | كيف نخدمه | ما يكسبه | صفحة الخدمة (معرّف)
$rows = array(
    array( 'المطاعم والمقاهي والمطابخ التجارية', 'تنظيف المطاعم والمطابخ، تسليك المجاري، برنامج مكافحة حشرات وقوارض دوري', 'مواد مناسبة للأماكن التي يحضر فيها الطعام', 'موقع جاهز لتفتيش البلدية، وتجربة أنظف للزبائن، وتقليل خطر عودة الآفات', 1274 ),
    array( 'المكاتب والشركات', 'تنظيف دوري للمكاتب، غسيل المكيفات، تنظيف السجاد والموكيت', 'جدولة الزيارات بعد الدوام أو في الإجازات', 'بيئة عمل نظيفة وهواء أنقى من المكيفات، وانطباع أفضل عند الزوار والعملاء', 209513 ),
    array( 'الفنادق والشقق المفروشة والاستراحات وقاعات المناسبات', 'تنظيف الغرف والمجالس والكنب والمراتب، مكافحة بق الفراش والحشرات، غسيل السجاد', 'جداول تنسيق مع فترات الإشغال، وغسيل في المكان دون نقل الأثاث الكبير', 'غرف جاهزة للضيوف بشكل أسرع، وحماية مظهر الأثاث وعمره', 1342 ),
    array( 'المدارس والحضانات والمنشآت الصحية', 'تنظيف عام، مكافحة حشرات بمواد معتمدة، غسيل خزانات المياه', 'التنفيذ خارج أوقات الدراسة أو الاستقبال، وتنسيق مسبق مع الإدارة بشأن المواد وفترة التهوية', 'بيئة أنظف لمن يتواجد في المكان، وراحة للإدارة بوجود عرض مكتوب', 1300 ),
    array( 'المساجد', 'تنظيف السجاد والفرش والمكيفات والأرضيات', 'بالتنسيق مع الإمام أو الإدارة على أوقات لا تتعارض مع الصلوات', 'مسجد جاهز للمصلين وسجاد نظيف', 1404 ),
    array( 'العمائر والمجمعات السكنية', 'غسيل الخزانات، عزل الأسطح والخزانات، كشف تسربات المياه، تنظيف ما بعد التشطيب', 'معاينة شاملة للمبنى وتحديد الأولويات، وتنفيذ على مراحل حسب الخطة', 'حماية المبنى من التسربات والرطوبة، ومبنى أنظف عند التسليم للسكان', 1300 ),
    array( 'المستودعات والمصانع', 'برنامج مكافحة قوارض وحشرات دوري', 'خطة حسب نوع النشاط والمخزون، وزيارات متابعة', 'مخزون وأرضيات محمية، وتقليل خسائر التلف', 222780 ),
    array( 'المطورون والمقاولون', 'تنظيف الفلل الجديدة وما بعد التشطيب، العزل، كشف التسربات قبل التسليم', 'جهة واحدة للتنظيف والعزل والفحص بدل ثلاث جهات، وجدول مرتبط بموعد التسليم', 'تسليم للمالك بحالة أنظف، وتسربات تظهر قبل التسليم لا بعده', 1311 ),
);

// الأسئلة الشائعة: من حقل faq إن وُجد، وإلا الافتراضية
$faq = array();
$meta = get_post_meta( $post->ID, 'faq', true );
if ( is_array( $meta ) ) {
    foreach ( $meta as $q ) {
        if ( ! empty( $q['question'] ) && ! empty( $q['answer'] ) ) {
            $faq[] = array( wp_strip_all_tags( $q['question'] ), wp_strip_all_tags( $q['answer'] ) );
        }
    }
}
if ( ! $faq ) {
    $faq = array(
        array( 'ما القطاعات التي تخدمها شركة التنفيذ؟', 'نخدم المطاعم والمكاتب والفنادق والمدارس والمساجد والعمائر والمستودعات والمطورين والمقاولين في تسع مدن سعودية.' ),
        array( 'ما الخدمات التي تقدمها للمنشآت؟', 'أربعة مجالات: التنظيف، مكافحة الحشرات، العزل، كشف التسربات وتسليك المجاري.' ),
        array( 'كيف أحصل على عرض سعر لمنشأتي؟', 'املأ نموذج طلب عرض السعر في أعلى هذه الصفحة أو تواصل معنا على واتساب، ثم نحدد موعد المعاينة ونكتب لك العرض.' ),
        array( 'ماذا يتضمن عرض السعر؟', 'يذكر طريقة العمل والمادة المستخدمة وعدد الزيارات ومدة الضمان والسعر.' ),
        array( 'متى أدفع؟', 'بعد انتهاء الخدمة.' ),
        array( 'هل تعملون في مدن غير الرياض؟', 'نعم، لدينا فروع في الدمام والخبر والقطيف وجدة ومكة، ونخدم أيضا المدينة المنورة والأحساء وسيهات.' ),
        array( 'هل توجد ضمان على الخدمات؟', 'نعم، الضمان مكتوب وشروطه منشورة في صفحة سياسة الاسترجاع والاسترداد على موقعنا.' ),
        array( 'ما مواعيد العمل وكيف أتواصل معكم؟', 'من السبت إلى الخميس من 9 صباحا حتى 11 مساء، والجمعة من 2 ظهرا حتى 11 مساء. التواصل على ' . $phone . ' أو على ' . $mail . '.' ),
    );
}

$css = get_template_directory() . '/components/packs/BusinessPage/business.css';
if ( file_exists( $css ) ) {
    echo '<style>' . file_get_contents( $css ) . '</style>';
}
$wa_svg = function_exists( 'yc_pt_wa_svg' ) ? yc_pt_wa_svg() : '';
$e      = 'esc_html';

echo '<div class="yb" data-wa="' . esc_attr( $wa ) . '" data-url="' . esc_attr( get_permalink( $post ) ) . '">';

/* ---------- الهيرو + نموذج عرض السعر ---------- */
echo '<section class="yb-hero"><div class="container">';
  echo '<div class="yb-crumb"><breadcrumb>';
    Breadcrumb();
  echo '</breadcrumb></div>';
  echo '<div class="yb-hero__grid">';
    echo '<div class="yb-hero__intro">';
      echo '<h1>' . $e( $h1 ) . '</h1>';
      echo '<p class="yb-hero__lead">شركة التنفيذ شركة سعودية تعمل في المملكة منذ عام 2001. نخدم المطاعم والمكاتب والفنادق والمدارس والعمائر والمستودعات في تسع مدن. نعاين منشأتك أولا، ثم نكتب لك عرضا يذكر الطريقة والمادة وعدد الزيارات والضمان، ويكون الدفع بعد انتهاء الخدمة.</p>';
    echo '</div>';
    echo '<div class="yb-form" id="yb-quote">';
      echo '<h2>اطلب عرض سعر لمنشأتك</h2>';
      echo '<p class="yb-form__note">اكتب بيانات منشأتك والخدمات المطلوبة، ونتواصل معك لتحديد موعد المعاينة. السعر النهائي يُكتب بعد المعاينة.</p>';
      echo '<form novalidate>';
        $field = function ( $name, $label, $type, $req, $ph = '', $extra = '' ) use ( $e ) {
            echo '<div class="yb-f"><label for="yb-' . esc_attr( $name ) . '">' . $e( $label ) . ( $req ? ' <i aria-hidden="true">*</i>' : '' ) . '</label>';
            echo '<input type="' . esc_attr( $type ) . '" id="yb-' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '"' . ( $req ? ' required aria-required="true"' : '' ) . ( '' !== $ph ? ' placeholder="' . esc_attr( $ph ) . '"' : '' ) . ' ' . $extra . '>';
            echo '<span class="yb-err" data-e="' . esc_attr( $name ) . '" role="alert" hidden></span></div>';
        };
        $select = function ( $name, $label, $opts ) use ( $e ) {
            echo '<div class="yb-f"><label for="yb-' . esc_attr( $name ) . '">' . $e( $label ) . ' <i aria-hidden="true">*</i></label>';
            echo '<select id="yb-' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" required aria-required="true"><option value="">اختر</option>';
            foreach ( $opts as $o ) {
                echo '<option>' . $e( $o ) . '</option>';
            }
            echo '</select><span class="yb-err" data-e="' . esc_attr( $name ) . '" role="alert" hidden></span></div>';
        };
        echo '<div class="yb-row">'; $field( 'company', 'اسم المنشأة', 'text', true, '', 'autocomplete="organization"' ); $select( 'sector', 'نوع القطاع', $sectors ); echo '</div>';
        echo '<div class="yb-row">'; $field( 'person', 'اسم المسؤول ومسماه الوظيفي', 'text', true, 'مثال: محمد أحمد، مدير العمليات', 'autocomplete="name"' ); $field( 'phone', 'رقم الجوال', 'tel', true, '05xxxxxxxx', 'dir="ltr" inputmode="tel" autocomplete="tel"' ); echo '</div>';
        echo '<div class="yb-row">'; $field( 'email', 'البريد الإلكتروني (اختياري)', 'email', false, '', 'dir="ltr" autocomplete="email"' ); $select( 'city', 'المدينة', $cities ); echo '</div>';
        echo '<div class="yb-row">'; $field( 'district', 'الحي (اختياري)', 'text', false ); $field( 'sites', 'عدد المواقع أو الفروع (اختياري)', 'text', false, '', 'inputmode="numeric" autocomplete="off"' ); echo '</div>';
        echo '<fieldset class="yb-f"><legend>الخدمات المطلوبة <i aria-hidden="true">*</i></legend><div class="yb-chk">';
        foreach ( $services as $s ) {
            echo '<label><input type="checkbox" name="services" value="' . esc_attr( $s ) . '"><span>' . $e( $s ) . '</span></label>';
        }
        echo '</div><span class="yb-err" data-e="services" role="alert" hidden></span></fieldset>';
        echo '<div class="yb-f"><label for="yb-notes">ملاحظات (اختياري)</label><textarea id="yb-notes" name="notes" rows="3" placeholder="مثال: عدد الفروع، المساحة، موعد مناسب للمعاينة"></textarea></div>';
        echo '<label class="yb-agree"><input type="checkbox" name="agree" value="1"><span>أوافق على تواصل شركة التنفيذ معي بخصوص هذا الطلب، وعلى <a href="' . esc_url( function_exists( 'get_privacy_policy_url' ) && get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/' ) ) . '" target="_blank" rel="noopener">سياسة الخصوصية</a>.</span></label>';
        echo '<span class="yb-err" data-e="agree" role="alert" hidden></span>';
        echo '<a class="yb-btn yb-send" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener">' . $wa_svg . 'إرسال الطلب عبر واتساب</a>';
        echo '<p class="yb-ok" role="status" hidden>تم تجهيز طلبك، ويُفتح الآن واتساب لإرساله. شكرا لك، وسيتواصل معك أحد مختصينا لتحديد موعد المعاينة.</p>';
      echo '</form>';
    echo '</div>';
    echo '<div class="yb-hero__extra">';
      echo '<div class="yb-btns">';
        echo '<a class="yb-btn yb-btn--gold" href="#yb-quote">اطلب عرض سعر</a>';
        echo '<a class="yb-btn yb-btn--wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener">' . $wa_svg . 'واتساب ' . $e( $phone ) . '</a>';
        echo '<a class="yb-btn yb-btn--ghost" href="tel:' . esc_attr( $phone ) . '">اتصال مباشر</a>';
      echo '</div>';
      echo '<ul class="yb-facts"><li>منذ 2001 في السعودية</li><li>مقر في الرياض وخمسة فروع</li><li>أربعة مجالات خدمة متخصصة</li><li>ضمان مكتوب على الخدمات</li><li>الدفع بعد تنفيذ الخدمة</li></ul>';
    echo '</div>';

  echo '</div>';
echo '</div></section>';

/* ---------- القسم 2: ماذا نقدم ---------- */
$cards = array(
    array( 'التنظيف', 'تنظيف المكاتب والمطاعم والفنادق والمدارس والمساجد، مع المكيفات والسجاد والموكيت والكنب وخزانات المياه، وتنظيف ما بعد التشطيب لتسليم المباني.', '/شركة-تنظيف-بالرياض/', 'شركة تنظيف بالرياض' ),
    array( 'مكافحة الحشرات', 'معاينة ثم خطة حسب نوع الآفة والمكان: الصراصير والنمل والبق والفئران والحمام والنمل الأبيض، وبرامج دورية للمطاعم والمستودعات.', '/شركة-مكافحة-حشرات-بالرياض/', 'شركة مكافحة حشرات بالرياض' ),
    array( 'العزل', 'عزل الأسطح والخزانات والمواسير بأنظمة تناسب نوع المبنى.', '/شركة-عزل-خزانات-بالرياض/', 'شركة عزل خزانات بالرياض' ),
    array( 'كشف التسربات وتسليك المجاري', 'كشف تسربات المياه وتسليك المجاري والمطابخ.', '/شركة-كشف-تسربات-المياه-بالرياض/', 'شركة كشف تسربات المياه بالرياض' ),
);
echo '<section class="yb-sec"><div class="container">';
  echo '<h2>أربعة مجالات من جهة واحدة لمنشأتك</h2>';
  echo '<p class="yb-sub">منشأتك تحتاج إلى أكثر من خدمة، والتعامل مع جهة واحدة يوفر عليك التنسيق والمتابعة.</p>';
  echo '<div class="yb-cards">';
  foreach ( $cards as $c ) {
      echo '<article class="yb-card yb-card--accent"><h3>' . $e( $c[0] ) . '</h3><p>' . $e( $c[1] ) . '</p><a class="yb-more" href="' . esc_url( home_url( $c[2] ) ) . '">' . $e( $c[3] ) . '</a></article>';
  }
  echo '</div>';
echo '</div></section>';

/* ---------- القسم 3: القطاعات ---------- */
echo '<section class="yb-sec"><div class="container">';
  echo '<h2>كيف نخدم كل قطاع وما الذي يكسبه</h2>';
  echo '<p class="yb-sub">لكل قطاع احتياجاته ومواعيده واشتراطاته. نعاين الموقع في البداية ثم نبني الخطة على طبيعة النشاط.</p>';
  echo '<table class="yb-tbl yb-tbl--stack"><thead><tr><th>القطاع</th><th>ما نقدمه</th><th>كيف نخدمه</th><th>ما يكسبه</th></tr></thead><tbody>';
  foreach ( $rows as $r ) {
      $u    = $pg( $r[4] );
      $name = $u ? '<a href="' . esc_url( $u ) . '">' . $e( $r[0] ) . '</a>' : $e( $r[0] );
      echo '<tr><td>' . $name . '</td><td data-l="ما نقدمه">' . $e( $r[1] ) . '</td><td data-l="كيف نخدمه">' . $e( $r[2] ) . '</td><td data-l="ما يكسبه">' . $e( $r[3] ) . '</td></tr>';
  }
  echo '</tbody></table>';
echo '</div></section>';

/* ---------- القسم 4: طريقة العمل ---------- */
echo '<section class="yb-sec"><div class="container">';
  echo '<h2>من الطلب إلى التسليم في أربع خطوات</h2>';
  echo '<ol class="yb-steps">';
  foreach ( array(
      array( 'المعاينة', 'يزور المختص موقعك ويفحص الحالة والمساحة والمواد الموجودة.' ),
      array( 'عرض السعر المكتوب', 'يذكر الطريقة والمادة وعدد الزيارات والضمان والسعر.' ),
      array( 'التنفيذ', 'يشرف على العمل رئيس فريق مختص بالخدمة.' ),
      array( 'المتابعة', 'نعالج أي ملاحظة تظهر بعد التنفيذ ضمن الضمان المكتوب، ويكون الدفع بعد انتهاء الخدمة.' ),
  ) as $s ) {
      echo '<li><b>' . $e( $s[0] ) . '</b>' . $e( $s[1] ) . '</li>';
  }
  echo '</ol>';
  echo '<h3 style="font-size:20px;font-weight:800;margin:0 0 12px">أنواع التعاقد</h3>';
  echo '<table class="yb-tbl"><thead><tr><th>النوع</th><th>لمن يناسب</th></tr></thead><tbody>';
  echo '<tr><td>خدمة لمرة واحدة</td><td>منشأة تحتاج تنظيفا أو معالجة محددة</td></tr>';
  echo '<tr><td>تنفيذ خارج أوقات العمل</td><td>منشأة لا يمكنها إيقاف النشاط</td></tr>';
  echo '</tbody></table>';
echo '</div></section>';

/* ---------- القسم 5: الخبرة والفروع ---------- */
$term_url = function ( $name ) {
    if ( ! taxonomy_exists( 'country' ) ) {
        return '';
    }
    $t = get_term_by( 'name', $name, 'country' );
    if ( $t && ! is_wp_error( $t ) ) {
        $l = get_term_link( $t );
        return is_wp_error( $l ) ? '' : $l;
    }
    return '';
};
echo '<section class="yb-sec"><div class="container">';
  echo '<h2>خبرة تمتد منذ 2001 وفروع في تسع مدن</h2>';
  echo '<p class="yb-sub">المقر الرئيسي في حي العليا بالرياض. الفروع: الدمام والخبر والقطيف وجدة ومكة. ونخدم كذلك المدينة المنورة والأحساء وسيهات.</p>';
  echo '<ul class="yb-pills yb-pills--main"><li><span>الرياض — المقر الرئيسي</span></li></ul>';
  echo '<ul class="yb-pills">';
  foreach ( array( 'الدمام', 'الخبر', 'القطيف', 'جدة', 'مكة', 'المدينة المنورة', 'الأحساء', 'سيهات' ) as $c ) {
      $u = $term_url( $c );
      echo '<li>' . ( $u ? '<a href="' . esc_url( $u ) . '">' . $e( $c ) . '</a>' : '<span>' . $e( $c ) . '</span>' ) . '</li>';
  }
  echo '</ul>';
  $team_url = $pg( 221452 );
  echo '<h3 style="font-size:20px;font-weight:800;margin:0 0 12px">' . ( $team_url ? '<a href="' . esc_url( $team_url ) . '" style="color:inherit">فريق العمل</a>' : 'فريق العمل' ) . '</h3>';
  echo '<table class="yb-tbl"><thead><tr><th>الاسم</th><th>المسؤولية</th></tr></thead><tbody>';
  foreach ( array( array( 'بهاء أحمد', 'ضمان الجودة' ), array( 'أحمد السيد', 'رئيس فريق التنظيف العام' ), array( 'سعيد علي', 'التنظيف بالبخار' ), array( 'منصور القصيم', 'السلامة والصحة المهنية' ) ) as $t ) {
      echo '<tr><td>' . $e( $t[0] ) . '</td><td>' . $e( $t[1] ) . '</td></tr>';
  }
  echo '</tbody></table>';
echo '</div></section>';

/* ---------- القسم 6: الضمان والحماية ---------- */
$cr  = trim( (string) get_option( 'cr_number' ) );
$vat = trim( (string) get_option( 'vat_number' ) );
echo '<section class="yb-sec"><div class="container">';
  echo '<h2>ضمان مكتوب وحقوق واضحة لمنشأتك</h2>';
  echo '<div class="yb-box"><b>الضمان:</b> تبني شركة التنفيذ الثقة بضمانات مكتوبة لا بوعود تسويقية؛ ولأن الخدمات لا تُسترجع كالسلع، تضمن سياستنا العادلة إعادة تنفيذ الخدمة مجاناً، أو استرداد قيمتها المالية لإنصاف العميل. <a href="' . esc_url( $policy ) . '">اقرأ سياسة الاسترجاع والاسترداد كاملة</a></div>';
  echo '<div class="yb-box"><b>الفواتير:</b> سجل تجاري وفاتورة ضريبية لكل عملية.' . ( $cr ? ' السجل التجاري: <bdi>' . $e( $cr ) . '</bdi>.' : '' ) . ( $vat ? ' الرقم الضريبي: <bdi>' . $e( $vat ) . '</bdi>.' : '' ) . '</div>';
  echo '<div class="yb-box"><b>الالتزام بالأنظمة:</b> نعمل وفق اشتراطات البلدية والدفاع المدني المتعلقة بالمواد والسلامة.</div>';
  echo '<div class="yb-box"><b>الدفع:</b> بعد انتهاء الخدمة.</div>';
echo '</div></section>';

/* ---------- القسم 7: لماذا التنفيذ ---------- */
echo '<section class="yb-sec"><div class="container">';
  echo '<h2>ما الذي يميزنا في التعامل مع المنشآت</h2>';
  echo '<div class="yb-cards yb-cards--3" style="margin-top:18px">';
  foreach ( array(
      'جهة واحدة لأربعة مجالات بدل التنسيق بين عدة شركات.',
      'خبرة منذ 2001 وفروع في تسع مدن، مناسبة للشركات ذات المواقع المتعددة.',
      'معاينة قبل السعر، وعرض مكتوب يذكر الطريقة والمادة والزيارات.',
      'ضمان مكتوب بنص السياسة المنشورة.',
      'فريق بأسماء ومسؤوليات معلنة.',
      'جدولة تناسب نشاطك.',
  ) as $t ) {
      echo '<div class="yb-card"><p>' . $e( $t ) . '</p></div>';
  }
  echo '</div>';
echo '</div></section>';

/* ---------- القسم 9: الأسئلة الشائعة ---------- */
echo '<section class="yb-sec"><div class="container yb-faq">';
  echo '<h2 style="margin-bottom:16px">الأسئلة الشائعة عن خدمات قطاع الأعمال</h2>';
  foreach ( $faq as $i => $q ) {
      echo '<details' . ( 0 === $i ? ' open' : '' ) . '><summary>' . $e( $q[0] ) . '</summary><p>' . $e( $q[1] ) . '</p></details>';
  }
echo '</div></section>';

/* ---------- التواصل ---------- */
echo '<section class="yb-sec"><div class="container">';
  echo '<div class="yb-contact">';
    echo '<h2>جاهزون لمعاينة منشأتك</h2>';
    echo '<p>أرسل لنا نوع النشاط والمدينة والخدمة المطلوبة، ونرد عليك لتحديد موعد المعاينة وكتابة العرض.</p>';
    echo '<div class="yb-btns">';
      echo '<a class="yb-btn yb-btn--gold" href="#yb-quote">اطلب عرض سعر</a>';
      echo '<a class="yb-btn yb-btn--wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener">' . $wa_svg . 'واتساب ' . $e( $phone ) . '</a>';
      echo '<a class="yb-btn yb-btn--ghost" href="tel:' . esc_attr( $phone ) . '">اتصال مباشر</a>';
    echo '</div>';
    echo '<ul><li>البريد: <bdi>' . $e( $mail ) . '</bdi></li><li>المقر: حي العليا، الرياض</li><li>الدوام: من السبت إلى الخميس من 9 ص إلى 11 م، والجمعة من 2 ظ إلى 11 م</li></ul>';
  echo '</div>';
  $links = array( array( 'طلب خدمة', $pg( 172, '/order/' ) ), array( 'حاسبة الأسعار', $pg( 220497, '/altnfez-calculator/' ) ), array( 'سياسة الاسترجاع والاسترداد', $policy ) );
  echo '<p class="yb-links">';
  foreach ( $links as $l ) {
      if ( $l[1] ) {
          echo '<a href="' . esc_url( $l[1] ) . '">' . $e( $l[0] ) . '</a>';
      }
  }
  echo '</p>';
echo '</div></section>';

echo '</div>';

/* ---------- البيانات المنظّمة: FAQPage مطابق للمرئي فقط ---------- */
$ents = array();
foreach ( $faq as $q ) {
    $ents[] = array( '@type' => 'Question', 'name' => $q[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $q[1] ) );
}
echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $ents ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>';

$js = get_template_directory() . '/components/packs/BusinessPage/business.js';
if ( file_exists( $js ) ) {
    echo '<script>' . file_get_contents( $js ) . '</script>';
}
