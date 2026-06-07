<?php
/**
 * Front page template — See AEO landing page.
 *
 * @package See_AEO
 */

get_header();
?>

  <main>


    <!-- HERO -->
    <section class="hero" id="hero">
      <canvas class="hero__canvas" id="particleCanvas"></canvas>
      <div class="hero__orb hero__orb--1"></div>
      <div class="hero__orb hero__orb--2"></div>

      <div class="container hero__content fade-in">
        <span class="badge">الرائدون في تحسين محركات البحث والذكاء الاصطناعي</span>
        <h1 class="hero__title">
          مع <span class="text--cyan">See AEO</span> تتصدّر نتائج البحث<br class="hide-mobile" />
          وإجابات الذكاء الاصطناعي
        </h1>
        <p class="hero__subtitle">
          في See AEO، ندمج بين الـ SEO وثورة الـ AEO والـ GEO لابتكار مواقع وتطبيقات ذكية
          تضع عملك في مقدمة إجابات الجيل الجديد ومحركات البحث.
        </p>
        <div class="hero__actions">
          <a href="#contact" class="btn btn--primary">ابدأ رحلتك</a>
          <a href="#about"   class="btn btn--ghost">تعرف علينا</a>
        </div>
        <div class="hero__stats">
          <div class="stat"><span class="stat__number" data-target="500">0</span><span class="stat__suffix">+</span><span class="stat__label">عميل راضٍ</span></div>
          <div class="stat__divider"></div>
          <div class="stat"><span class="stat__number" data-target="98">0</span><span class="stat__suffix">%</span><span class="stat__label">معدل نجاح</span></div>
          <div class="stat__divider"></div>
          <div class="stat"><span class="stat__number" data-target="5">0</span><span class="stat__suffix">+</span><span class="stat__label">سنوات خبرة</span></div>
          <div class="stat__divider"></div>
          <div class="stat"><span class="stat__number" data-target="1000">0</span><span class="stat__suffix">+</span><span class="stat__label">مشروع منجز</span></div>
        </div>
      </div>
      <div class="hero__scroll-indicator"><div class="scroll-dot"></div></div>
    </section>

    <!-- SERVICES -->
    <section class="section section--alt" id="services">
      <div class="container">
        <div class="section__header fade-in">
          <span class="badge">خدماتنا</span>
          <h2 class="section__title">كل ما تحتاجه لحضور رقمي قوي</h2>
          <p class="section__subtitle">نقدم حلولاً متكاملة ومتخصصة تضمن لك التميز في الفضاء الرقمي</p>
        </div>
        <div class="services-grid">
          <article class="service-card fade-in">
            <div class="service-card__icon"><svg class="ico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></div>
            <div class="service-card__tag">SEO</div>
            <h3 class="service-card__title">تحسين محركات البحث</h3>
            <p class="service-card__desc">نرفع ترتيب موقعك في نتائج البحث العضوية من خلال استراتيجيات SEO متكاملة ومدروسة</p>
            <ul class="service-card__features"><li>تحليل الكلمات المفتاحية</li><li>تحسين المحتوى</li><li>بناء الروابط</li><li>التقارير الدورية</li></ul>
            <a href="#contact" class="service-card__link">اعرف المزيد <span aria-hidden="true">←</span></a>
          </article>
          <article class="service-card fade-in">
            <div class="service-card__icon"><svg class="ico" viewBox="0 0 24 24"><rect x="5" y="5" width="14" height="14" rx="2"/><rect x="9" y="9" width="6" height="6" rx="1"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/></svg></div>
            <div class="service-card__tag">AEO</div>
            <h3 class="service-card__title">تحسين محركات الإجابة</h3>
            <p class="service-card__desc">نجعل موقعك المصدر الأول للإجابات في الذكاء الاصطناعي وصناديق الإجابات المباشرة</p>
            <ul class="service-card__features"><li>تحسين البيانات المنظمة</li><li>Featured Snippets</li><li>محتوى موجه للذكاء الاصطناعي</li><li>تحليل نية البحث</li></ul>
            <a href="#contact" class="service-card__link">اعرف المزيد <span aria-hidden="true">←</span></a>
          </article>
          <article class="service-card fade-in">
            <div class="service-card__icon"><svg class="ico" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></div>
            <div class="service-card__tag">GEO</div>
            <h3 class="service-card__title">التحسين الجغرافي</h3>
            <p class="service-card__desc">نعزز ظهورك في نتائج البحث المحلية والجغرافية لاستهداف عملائك في موقعهم الصحيح</p>
            <ul class="service-card__features"><li>Google My Business</li><li>الكلمات المحلية</li><li>الخرائط والنتائج المحلية</li><li>مراجعات العملاء</li></ul>
            <a href="#contact" class="service-card__link">اعرف المزيد <span aria-hidden="true">←</span></a>
          </article>
          <article class="service-card fade-in">
            <div class="service-card__icon"><svg class="ico" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="m9.5 8-2 2 2 2M14.5 8l2 2-2 2"/></svg></div>
            <div class="service-card__tag">Web Design</div>
            <h3 class="service-card__title">تصميم المواقع</h3>
            <p class="service-card__desc">نصمم مواقع ويب احترافية سريعة وجميلة تعكس هوية علامتك التجارية وتحول الزوار لعملاء</p>
            <ul class="service-card__features"><li>تصميم متجاوب</li><li>سرعة عالية</li><li>تجربة مستخدم مثالية</li><li>تحسين معدل التحويل</li></ul>
            <a href="#contact" class="service-card__link">اعرف المزيد <span aria-hidden="true">←</span></a>
          </article>
        </div>
      </div>
    </section>

    <!-- CLIENTS -->
    <section class="section section--alt" id="clients">
      <div class="container">
        <div class="section__header fade-in">
          <span class="badge">عملاءنا</span>
          <h2 class="section__title">ثقة أكثر من 500 عميل</h2>
          <p class="section__subtitle">شركات وأعمال تجارية تثق في See AEO لتحقيق نجاحها الرقمي</p>
        </div>
        <div class="clients-wrapper fade-in">
          <div class="clients-track">
            <div class="clients-marquee">
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,167,233,0.15);color:#00A7E9;">ر</div><span class="client-logo__name">الرياض التقنية</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(106,64,144,0.2);color:#9B6ED0;">ب</div><span class="client-logo__name">البناء الرقمي</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,167,233,0.15);color:#00A7E9;">T</div><span class="client-logo__name">TechSolutions</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(255,160,0,0.15);color:#FFA000;">م</div><span class="client-logo__name">ميديا برو</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,200,100,0.15);color:#00C864;">أ</div><span class="client-logo__name">الأفق الإبداعي</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,167,233,0.15);color:#00A7E9;">S</div><span class="client-logo__name">Smart Digital</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(255,80,80,0.15);color:#FF5050;">و</div><span class="client-logo__name">وان كريتف</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(106,64,144,0.2);color:#9B6ED0;">E</div><span class="client-logo__name">Elite Market</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,167,233,0.15);color:#00A7E9;">ن</div><span class="client-logo__name">نوفا للتسويق</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(255,160,0,0.15);color:#FFA000;">G</div><span class="client-logo__name">GlobalBrands</span></div>
              <!-- Duplicate for seamless loop -->
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,167,233,0.15);color:#00A7E9;">ر</div><span class="client-logo__name">الرياض التقنية</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(106,64,144,0.2);color:#9B6ED0;">ب</div><span class="client-logo__name">البناء الرقمي</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,167,233,0.15);color:#00A7E9;">T</div><span class="client-logo__name">TechSolutions</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(255,160,0,0.15);color:#FFA000;">م</div><span class="client-logo__name">ميديا برو</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,200,100,0.15);color:#00C864;">أ</div><span class="client-logo__name">الأفق الإبداعي</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,167,233,0.15);color:#00A7E9;">S</div><span class="client-logo__name">Smart Digital</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(255,80,80,0.15);color:#FF5050;">و</div><span class="client-logo__name">وان كريتف</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(106,64,144,0.2);color:#9B6ED0;">E</div><span class="client-logo__name">Elite Market</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(0,167,233,0.15);color:#00A7E9;">ن</div><span class="client-logo__name">نوفا للتسويق</span></div>
              <div class="client-logo"><div class="client-logo__icon" style="background:rgba(255,160,0,0.15);color:#FFA000;">G</div><span class="client-logo__name">GlobalBrands</span></div>
            </div>
          </div>
          <div class="clients-stats fade-in">
            <div class="clients-stat"><span class="clients-stat__num">500+</span><span class="clients-stat__lbl">عميل نشط</span></div>
            <div class="clients-stat"><span class="clients-stat__num">98%</span><span class="clients-stat__lbl">معدل الرضا</span></div>
            <div class="clients-stat"><span class="clients-stat__num">12+</span><span class="clients-stat__lbl">قطاع مختلف</span></div>
            <div class="clients-stat"><span class="clients-stat__num">5★</span><span class="clients-stat__lbl">متوسط التقييم</span></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ABOUT -->
    <section class="section" id="about">
      <div class="container">
        <div class="about__inner">
          <div class="about__text fade-in">
            <span class="badge">من نحن</span>
            <h2 class="section__title" style="margin-top:16px;">شركاؤك في<br /><span class="text--cyan">النجاح الرقمي</span></h2>
            <p class="about__para">نحن فريق من الخبراء المتخصصين في تحسين محركات البحث والتسويق الرقمي. نؤمن بأن كل عمل تجاري يستحق حضوراً رقمياً قوياً يعكس قيمته الحقيقية ويصل إلى جمهوره المستهدف.</p>
            <p class="about__para">منذ تأسيسنا، نجحنا في خدمة أكثر من <strong class="text--cyan">500 عميل</strong> من مختلف القطاعات، محققين نتائج قابلة للقياس ونمواً مستداماً في الحضور الرقمي.</p>
            <div class="about__highlights">
              <div class="highlight">
                <span class="highlight__icon"><svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M8.5 13 7 22l5-3 5 3-1.5-9"/></svg></span>
                <div><strong>جودة عالمية</strong><p>نلتزم بأعلى المعايير الدولية في كل مشروع</p></div>
              </div>
              <div class="highlight">
                <span class="highlight__icon"><svg class="ico" viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg></span>
                <div><strong>خصوصية تامة</strong><p>بيانات عملائنا في أمان تام ومحمية</p></div>
              </div>
            </div>
          </div>
          <div class="about__visual fade-in">
            <div class="about__card-stack">
              <div class="about__stat-card about__stat-card--1"><span class="stat-card__num">500+</span><span class="stat-card__lbl">عميل سعيد</span></div>
              <div class="about__stat-card about__stat-card--2"><span class="stat-card__num">98%</span><span class="stat-card__lbl">معدل رضا</span></div>
              <div class="about__stat-card about__stat-card--3"><span class="stat-card__num">#1</span><span class="stat-card__lbl">نتائج بحث</span></div>
              <div class="about__center-logo"><span>See<b>AEO</b></span><small>شريكك الرقمي</small></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- WHY US -->
    <section class="section section--alt" id="why">
      <div class="container">
        <div class="section__header fade-in">
          <span class="badge">لماذا See AEO</span>
          <h2 class="section__title">الفرق الحقيقي يظهر في النتائج</h2>
          <p class="section__subtitle">نتميز بمنهجية عمل علمية ومثبتة توصل عملاءنا إلى القمة</p>
        </div>
        <div class="why-grid">
          <div class="why-card fade-in">
            <div class="why-card__icon"><svg class="ico" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/></svg></div>
            <h3 class="why-card__title">نتائج قابلة للقياس</h3>
            <p class="why-card__desc">نقدم تقارير شاملة وتحليلات دقيقة لكل حملة حتى تعرف بالضبط أين يذهب استثمارك</p>
          </div>
          <div class="why-card fade-in">
            <div class="why-card__icon"><svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/></svg></div>
            <h3 class="why-card__title">استراتيجية مخصصة</h3>
            <p class="why-card__desc">كل عميل يحصل على خطة عمل مصممة خصيصاً لأهدافه وطبيعة سوقه وميزانيته</p>
          </div>
          <div class="why-card fade-in">
            <div class="why-card__icon"><svg class="ico" viewBox="0 0 24 24"><path d="M10 2v6.5L5 17a2.5 2.5 0 0 0 2.2 3.7h9.6A2.5 2.5 0 0 0 19 17l-5-8.5V2"/><path d="M8.5 2h7"/></svg></div>
            <h3 class="why-card__title">تقنيات متقدمة</h3>
            <p class="why-card__desc">نستخدم أحدث أدوات وتقنيات الذكاء الاصطناعي في تحليل البيانات وتطوير الاستراتيجيات</p>
          </div>
          <div class="why-card fade-in">
            <div class="why-card__icon"><svg class="ico" viewBox="0 0 24 24"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="2.5" y="14" width="4" height="6" rx="1.5"/><rect x="17.5" y="14" width="4" height="6" rx="1.5"/></svg></div>
            <h3 class="why-card__title">دعم مستمر</h3>
            <p class="why-card__desc">فريقنا متاح على مدار الساعة للإجابة على استفساراتك وحل أي مشكلة بأسرع وقت</p>
          </div>
          <div class="why-card fade-in">
            <div class="why-card__icon"><svg class="ico" viewBox="0 0 24 24"><path d="M3 17 9 11l4 4 8-8"/><path d="M17 7h4v4"/></svg></div>
            <h3 class="why-card__title">نمو مستدام</h3>
            <p class="why-card__desc">نبني حضوراً رقمياً طويل الأمد لا يتأثر بتغييرات الخوارزميات أو تقلبات السوق</p>
          </div>
          <div class="why-card fade-in">
            <div class="why-card__icon"><svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="9" r="6"/><path d="M8.5 14 7 22l5-3 5 3-1.5-8"/></svg></div>
            <h3 class="why-card__title">خبرة مثبتة</h3>
            <p class="why-card__desc">سنوات من النجاح مع عملاء من مختلف القطاعات تجعلنا الخيار الأمثل لنمو أعمالك</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PROCESS -->
    <section class="section" id="process">
      <div class="container">
        <div class="section__header fade-in">
          <span class="badge">منهجيتنا</span>
          <h2 class="section__title">كيف نعمل؟</h2>
          <p class="section__subtitle">أربع خطوات واضحة توصلك من نقطة الانطلاق إلى القمة</p>
        </div>
        <div class="process-inner">
          <div class="process-track">
            <div class="process-line"></div>
            <div class="process-step fade-in"><div class="process-step__num">01</div><div class="process-step__content"><h3>التحليل والتشخيص</h3><p>نحلل وضعك الرقمي الحالي بدقة، ندرس منافسيك، ونفهم أهدافك وجمهورك المستهدف لبناء أساس قوي</p></div></div>
            <div class="process-step fade-in"><div class="process-step__num">02</div><div class="process-step__content"><h3>وضع الاستراتيجية</h3><p>نضع خطة عمل مخصصة ومفصلة تحدد الأولويات والخطوات والجداول الزمنية لتحقيق أهدافك</p></div></div>
            <div class="process-step fade-in"><div class="process-step__num">03</div><div class="process-step__content"><h3>التنفيذ والتطوير</h3><p>ننفذ الاستراتيجية بكفاءة واحترافية عالية مع متابعة مستمرة للتأكد من سير الأمور وفق الخطة</p></div></div>
            <div class="process-step fade-in"><div class="process-step__num">04</div><div class="process-step__content"><h3>القياس والتحسين</h3><p>نراقب النتائج بدقة ونحسن باستمرار بناءً على البيانات الفعلية لضمان أفضل أداء ممكن</p></div></div>
          </div>

          <div class="process-visual fade-in">
            <div class="process-image" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/img/lanx-dashboard.png' ); ?>');"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- WORKS / PORTFOLIO -->
    <section class="section section--alt" id="works">
      <div class="container">
        <div class="section__header fade-in">
          <span class="badge">أعمالنا</span>
          <h2 class="section__title">نماذج من مشاريعنا الناجحة</h2>
          <p class="section__subtitle">قصص نجاح حقيقية تعكس أثر عملنا على نمو عملائنا</p>
        </div>
        <div class="works-grid">
          <article class="work-card fade-in"><span class="work-card__tag">SEO · متجر إلكتروني</span><h3 class="work-card__title">+320% زيارات عضوية</h3><p class="work-card__desc">تصدّر الكلمات التنافسية خلال 6 أشهر لمتجر تجزئة رائد</p></article>
          <article class="work-card fade-in"><span class="work-card__tag">AEO · منصة تقنية</span><h3 class="work-card__title">المصدر #1 في إجابات AI</h3><p class="work-card__desc">ظهور دائم في إجابات الذكاء الاصطناعي وصناديق الإجابات</p></article>
          <article class="work-card fade-in"><span class="work-card__tag">Web Design · شركة خدمات</span><h3 class="work-card__title">موقع متكامل سريع</h3><p class="work-card__desc">تصميم وتطوير موقع رفع معدل التحويل بنسبة 45%</p></article>
        </div>
      </div>
    </section>

    <!-- CONTACT -->
    <section class="section" id="contact">
      <div class="container">
        <div class="section__header fade-in">
          <span class="badge">تواصل معنا</span>
          <h2 class="section__title">تواصل معنا</h2>
          <p class="section__subtitle">دعنا نناقش كيف يمكننا تطوير حضورك الرقمي وتحقيق أهدافك</p>
        </div>
        <div class="contact__inner">
          <div class="contact__form-wrap fade-in">
            <form class="contact-form" id="contactForm" novalidate>
              <div class="form-row">
                <div class="form-group"><label for="name">الاسم الكامل</label><input type="text" id="name" name="name" placeholder="أدخل اسمك الكامل" required /><span class="form-error" id="nameError"></span></div>
                <div class="form-group"><label for="email">البريد الإلكتروني</label><input type="email" id="email" name="email" placeholder="example@domain.com" required /><span class="form-error" id="emailError"></span></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label for="phone">رقم الهاتف</label><input type="tel" id="phone" name="phone" placeholder="+966 5x xxx xxxx" /><span class="form-error" id="phoneError"></span></div>
                <div class="form-group"><label for="service">الخدمة المطلوبة</label><select id="service" name="service"><option value="" disabled selected>اختر الخدمة</option><option value="seo">SEO - تحسين محركات البحث</option><option value="aeo">AEO - تحسين محركات الإجابة</option><option value="geo">GEO - التحسين الجغرافي</option><option value="web">Web Design - تصميم المواقع</option><option value="all">جميع الخدمات</option></select></div>
              </div>
              <div class="form-group"><label for="message">رسالتك</label><textarea id="message" name="message" rows="5" placeholder="أخبرنا عن مشروعك وأهدافك..." required></textarea><span class="form-error" id="messageError"></span></div>
              <button type="submit" class="btn btn--primary btn--full"><span class="btn-text">أرسل رسالتك</span><span class="btn-icon">✉</span></button>
              <div class="form-success" id="formSuccess" aria-live="polite">تم إرسال رسالتك بنجاح! سنتواصل معك في أقرب وقت ممكن 🎉</div>
            </form>
          </div>
          <div class="contact__info fade-in">
            <div class="contact-info-card">
              <div class="contact-info__item">
                <div class="contact-info__icon-wrap"><svg class="ico" viewBox="0 0 24 24"><rect x="2.5" y="4.5" width="19" height="15" rx="2"/><path d="m3 6 9 6 9-6"/></svg></div>
                <div><strong>البريد الإلكتروني</strong><a href="mailto:hello@seeaeo.com" class="contact-info__link">hello@seeaeo.com</a></div>
              </div>
              <div class="contact-info__item">
                <div class="contact-info__icon-wrap"><svg class="ico" viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2 4.2 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.8 2Z"/></svg></div>
                <div><strong>رقم الهاتف</strong><a href="tel:+966500000000" class="contact-info__link" dir="ltr">+966 50 000 0000</a></div>
              </div>
              <div class="contact-info__item">
                <div class="contact-info__icon-wrap"><svg class="ico" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></div>
                <div><strong>الموقع</strong><span class="contact-info__text">الرياض، المملكة العربية السعودية</span></div>
              </div>
            </div>
            <div class="social-links">
              <p class="social-links__label">تابعنا على</p>
              <div class="social-links__icons">
                <a href="#" class="social-link" aria-label="X"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                <a href="#" class="social-link" aria-label="LinkedIn"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                <a href="#" class="social-link" aria-label="Instagram"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                <a href="#" class="social-link" aria-label="YouTube"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php
get_footer();
