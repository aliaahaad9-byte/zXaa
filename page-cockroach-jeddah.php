<?php
/**
 * Template Name: مكافحة صراصير بجدة
 * Template Post Type: page
 * Description: صفحة هبوط مكافحة الصراصير بجدة - شركة التنفيذ
 */

get_header();
?>

<div class="tnf-page">

<style>
:root {
  --primary:       #1B6B3A;
  --primary-dark:  #0f3d22;
  --primary-light: #2E8B57;
  --accent:        #D4A017;
  --accent-light:  #f5c842;
  --dark:          #0f2d1c;
  --text:          #2c2c2c;
  --text-muted:    #6b7280;
  --bg-light:      #f4f9f5;
  --bg-white:      #ffffff;
  --border:        #e2e8e4;
  --success:       #16a34a;
  --danger:        #dc2626;
  --shadow:        0 4px 20px rgba(0,0,0,0.08);
  --shadow-lg:     0 8px 40px rgba(27,107,58,0.15);
  --radius:        12px;
  --radius-lg:     20px;
  --font:          'Cairo', sans-serif;
}

.tnf-page * { box-sizing: border-box; margin: 0; padding: 0; }
.tnf-page {
  font-family: var(--font);
  color: var(--text);
  line-height: 1.75;
  direction: rtl;
}
.tnf-page img { max-width: 100%; height: auto; display: block; border-radius: var(--radius); }
.tnf-page a { text-decoration: none; color: inherit; }

.tnf-section {
  padding: 60px 20px;
  max-width: 1100px;
  margin: 0 auto;
}
.tnf-section-full {
  padding: 60px 20px;
  width: 100%;
}

.tnf-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(212,160,23,0.12);
  color: var(--accent);
  border: 1px solid var(--accent);
  border-radius: 50px;
  padding: 6px 18px;
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 16px;
}

.tnf-h1 {
  font-size: clamp(26px, 4vw, 42px);
  font-weight: 900;
  color: var(--primary-dark);
  line-height: 1.3;
  margin-bottom: 16px;
}
.tnf-h2 {
  font-size: clamp(22px, 3vw, 34px);
  font-weight: 800;
  color: var(--primary-dark);
  line-height: 1.35;
  margin-bottom: 12px;
  text-align: center;
}
.tnf-h2 span { color: var(--primary); }
.tnf-subtitle {
  color: var(--text-muted);
  font-size: 16px;
  text-align: center;
  max-width: 680px;
  margin: 0 auto 40px;
}

.tnf-divider {
  width: 60px;
  height: 4px;
  background: linear-gradient(to left, var(--primary), var(--accent));
  border-radius: 4px;
  margin: 12px auto 30px;
}

.tnf-hero {
  background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 60%, var(--primary-light) 100%);
  color: #fff;
  padding: 70px 20px;
  position: relative;
  overflow: hidden;
}
.tnf-hero::before {
  content: '';
  position: absolute;
  top: -80px; left: -80px;
  width: 320px; height: 320px;
  background: rgba(255,255,255,0.04);
  border-radius: 50%;
}
.tnf-hero::after {
  content: '';
  position: absolute;
  bottom: -60px; right: -60px;
  width: 260px; height: 260px;
  background: rgba(212,160,23,0.08);
  border-radius: 50%;
}
.tnf-hero-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 50px;
  align-items: center;
  position: relative;
  z-index: 1;
}
.tnf-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(212,160,23,0.2);
  border: 1px solid var(--accent);
  color: var(--accent-light);
  border-radius: 50px;
  padding: 6px 18px;
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 18px;
}
.tnf-hero h1 {
  font-size: clamp(24px, 3.5vw, 40px);
  font-weight: 900;
  line-height: 1.3;
  margin-bottom: 14px;
  color: #fff;
}
.tnf-hero h1 em {
  font-style: normal;
  color: var(--accent-light);
}
.tnf-hero p {
  font-size: 16px;
  color: rgba(255,255,255,0.85);
  margin-bottom: 28px;
  max-width: 480px;
}

.tnf-hero-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 30px;
}
.tnf-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.2);
  color: #fff;
  border-radius: 50px;
  padding: 6px 14px;
  font-size: 13px;
  font-weight: 600;
}
.tnf-pill svg { flex-shrink: 0; }

.tnf-btn-wa {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #25D366;
  color: #fff;
  font-family: var(--font);
  font-size: 16px;
  font-weight: 700;
  padding: 14px 28px;
  border-radius: 50px;
  border: none;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 20px rgba(37,211,102,0.4);
  text-decoration: none;
}
.tnf-btn-wa:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(37,211,102,0.5);
}
.tnf-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--accent);
  color: var(--primary-dark);
  font-family: var(--font);
  font-size: 16px;
  font-weight: 700;
  padding: 14px 28px;
  border-radius: 50px;
  border: none;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
}
.tnf-btn-primary:hover { transform: translateY(-2px); }

.tnf-hero-btns {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
}

.tnf-hero-img-box {
  position: relative;
}
.tnf-hero-img-box img {
  border-radius: var(--radius-lg);
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  width: 100%;
  object-fit: cover;
}
.tnf-hero-stat {
  position: absolute;
  bottom: -16px;
  right: 20px;
  background: #fff;
  border-radius: var(--radius);
  padding: 14px 20px;
  box-shadow: var(--shadow-lg);
  display: flex;
  align-items: center;
  gap: 12px;
}
.tnf-hero-stat-num {
  font-size: 28px;
  font-weight: 900;
  color: var(--primary);
}
.tnf-hero-stat-label {
  font-size: 12px;
  color: var(--text-muted);
  font-weight: 600;
}

.tnf-numbers-bar {
  background: var(--bg-white);
  border-bottom: 1px solid var(--border);
  padding: 30px 20px;
}
.tnf-numbers-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}
.tnf-number-item {
  text-align: center;
  padding: 16px;
  border-left: 1px solid var(--border);
}
.tnf-number-item:last-child { border-left: none; }
.tnf-number-val {
  font-size: clamp(26px, 3vw, 38px);
  font-weight: 900;
  color: var(--primary);
  display: block;
  line-height: 1;
  margin-bottom: 6px;
}
.tnf-number-val span { color: var(--accent); }
.tnf-number-label {
  font-size: 13px;
  color: var(--text-muted);
  font-weight: 600;
}

.tnf-compare-wrap {
  overflow-x: auto;
  margin-top: 30px;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow);
}
.tnf-compare {
  width: 100%;
  border-collapse: collapse;
  background: var(--bg-white);
  min-width: 580px;
}
.tnf-compare thead th {
  padding: 18px 22px;
  font-size: 15px;
  font-weight: 800;
  text-align: center;
}
.tnf-compare thead th:first-child { text-align: right; }
.tnf-compare thead .th-us {
  background: var(--primary);
  color: #fff;
  border-radius: 0 var(--radius) 0 0;
}
.tnf-compare thead .th-them {
  background: #f1f5f9;
  color: var(--text-muted);
  border-radius: var(--radius) 0 0 0;
}
.tnf-compare thead .th-label {
  background: var(--primary-dark);
  color: #fff;
}
.tnf-compare tbody tr { border-bottom: 1px solid var(--border); }
.tnf-compare tbody tr:hover { background: var(--bg-light); }
.tnf-compare tbody td {
  padding: 16px 22px;
  font-size: 14px;
  text-align: center;
}
.tnf-compare tbody td:first-child {
  text-align: right;
  font-weight: 700;
  color: var(--primary-dark);
  white-space: nowrap;
}
.tnf-compare .td-us { font-weight: 700; color: var(--primary); }
.tnf-compare .td-them { color: var(--text-muted); }
.tnf-icon-check { color: var(--success); }
.tnf-icon-x { color: var(--danger); }

.tnf-steps {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
  margin-top: 30px;
}
.tnf-step {
  background: var(--bg-white);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 28px;
  position: relative;
  transition: box-shadow 0.3s, transform 0.3s;
}
.tnf-step:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-4px);
}
.tnf-step-num {
  position: absolute;
  top: -16px;
  right: 24px;
  width: 36px;
  height: 36px;
  background: var(--primary);
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  font-weight: 900;
  box-shadow: 0 4px 12px rgba(27,107,58,0.3);
}
.tnf-step-icon {
  width: 52px;
  height: 52px;
  background: var(--bg-light);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 14px;
}
.tnf-step h3 {
  font-size: 17px;
  font-weight: 800;
  color: var(--primary-dark);
  margin-bottom: 8px;
}
.tnf-step p {
  font-size: 14px;
  color: var(--text-muted);
  line-height: 1.7;
}

.tnf-img-split {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  align-items: center;
}
.tnf-img-split.reverse { direction: ltr; }
.tnf-img-split.reverse > * { direction: rtl; }
.tnf-img-split img { border-radius: var(--radius-lg); width: 100%; }
.tnf-img-content h2 {
  font-size: clamp(20px, 2.5vw, 30px);
  font-weight: 900;
  color: var(--primary-dark);
  margin-bottom: 12px;
  text-align: right;
}
.tnf-img-content p {
  font-size: 15px;
  color: var(--text-muted);
  margin-bottom: 16px;
}
.tnf-check-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.tnf-check-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
}
.tnf-check-list li svg { flex-shrink: 0; margin-top: 2px; }

.tnf-geo-wrap {
  background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
  border-radius: var(--radius-lg);
  padding: 50px 40px;
  color: #fff;
  position: relative;
  overflow: hidden;
}
.tnf-geo-wrap::before {
  content: '';
  position: absolute;
  top: -100px; left: -100px;
  width: 350px; height: 350px;
  background: rgba(255,255,255,0.03);
  border-radius: 50%;
}
.tnf-geo-wrap h2 { color: #fff; }
.tnf-geo-wrap h2 span { color: var(--accent-light); }
.tnf-geo-subtitle { color: rgba(255,255,255,0.75); text-align: center; margin: 12px 0 30px; font-size: 15px; }
.tnf-geo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 12px;
  margin-top: 10px;
}
.tnf-geo-item {
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: var(--radius);
  padding: 10px 14px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 600;
  transition: background 0.2s;
}
.tnf-geo-item:hover { background: rgba(255,255,255,0.18); }
.tnf-geo-note {
  margin-top: 28px;
  background: rgba(212,160,23,0.15);
  border: 1px solid rgba(212,160,23,0.3);
  border-radius: var(--radius);
  padding: 18px 22px;
  display: flex;
  align-items: center;
  gap: 14px;
}
.tnf-geo-note p {
  font-size: 14px;
  color: rgba(255,255,255,0.9);
  margin: 0;
}
.tnf-geo-note strong { color: var(--accent-light); }

.tnf-faq-wrap {
  max-width: 800px;
  margin: 30px auto 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.tnf-faq-item {
  background: var(--bg-white);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  overflow: hidden;
  transition: box-shadow 0.3s;
}
.tnf-faq-item:hover { box-shadow: var(--shadow); }
.tnf-faq-q {
  padding: 20px 24px;
  font-size: 15px;
  font-weight: 800;
  color: var(--primary-dark);
  display: flex;
  align-items: center;
  gap: 14px;
  cursor: pointer;
}
.tnf-faq-q-icon {
  width: 36px;
  height: 36px;
  background: var(--bg-light);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.tnf-faq-a {
  padding: 0 24px 20px 24px;
  font-size: 14px;
  color: var(--text-muted);
  line-height: 1.8;
  border-top: 1px solid var(--border);
  padding-top: 16px;
}

.tnf-cta-band {
  background: linear-gradient(135deg, var(--accent) 0%, #e8b820 100%);
  padding: 50px 20px;
  text-align: center;
}
.tnf-cta-band h2 {
  color: var(--primary-dark);
  font-size: clamp(20px, 3vw, 32px);
  margin-bottom: 8px;
}
.tnf-cta-band p {
  color: var(--primary-dark);
  opacity: 0.8;
  margin-bottom: 24px;
  font-size: 15px;
}
.tnf-btn-dark {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--primary-dark);
  color: #fff;
  font-family: var(--font);
  font-size: 16px;
  font-weight: 700;
  padding: 16px 32px;
  border-radius: 50px;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: transform 0.3s;
}
.tnf-btn-dark:hover { transform: translateY(-2px); }

@media (max-width: 768px) {
  .tnf-hero-inner { grid-template-columns: 1fr; gap: 30px; }
  .tnf-hero-img-box { display: none; }
  .tnf-numbers-inner { grid-template-columns: repeat(2, 1fr); }
  .tnf-number-item:nth-child(2) { border-left: none; }
  .tnf-steps { grid-template-columns: 1fr; }
  .tnf-img-split { grid-template-columns: 1fr; }
  .tnf-geo-wrap { padding: 30px 20px; }
  .tnf-geo-grid { grid-template-columns: repeat(2, 1fr); }
  .tnf-section { padding: 40px 16px; }
}
@media (max-width: 480px) {
  .tnf-numbers-inner { grid-template-columns: repeat(2, 1fr); }
  .tnf-geo-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<!-- HERO SECTION -->
<section class="tnf-hero">
  <div class="tnf-hero-inner">
    <div class="tnf-hero-content">
      <div class="tnf-hero-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        الرقم 1 في مكافحة الصراصير بجدة
      </div>

      <h1>
        لماذا يختار سكان جدة<br>
        <em>شركة التنفيذ</em><br>
        لمكافحة الصراصير؟
      </h1>

      <p>
        رطوبة جدة العالية وشبكات الصرف الصحي تجعل الصراصير أكثر مقاومةً للمبيدات التقليدية.
        نحن نتعامل مع هذا التحدي بـ4 أنواع مبيدات ألمانية معتمدة، مع ضمان مكتوب لمدة 6 أشهر.
      </p>

      <div class="tnf-hero-pills">
        <span class="tnf-pill">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          ضمان 6 أشهر مكتوب
        </span>
        <span class="tnf-pill">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          فنيون بخبرة +10 سنوات
        </span>
        <span class="tnf-pill">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          مبيدات ألمانية آمنة
        </span>
        <span class="tnf-pill">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          خدمة 24/7
        </span>
      </div>

      <div class="tnf-hero-btns">
        <a href="https://wa.me/966XXXXXXXXX?text=<?php echo rawurlencode('أريد طلب فحص مجاني لمكافحة الصراصير'); ?>" class="tnf-btn-wa" target="_blank" rel="nofollow noopener">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          طلب فحص مجاني عبر واتساب
        </a>
        <a href="tel:966XXXXXXXXX" class="tnf-btn-primary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.7A2 2 0 012 .82h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
          اتصل الآن
        </a>
      </div>
    </div>

    <div class="tnf-hero-img-box">
      <img
        src="<?php echo esc_url(get_template_directory_uri()); ?>/images/افضل-شركة-مكافحة-الصراصير-بجدة.webp"
        alt="أفضل شركة مكافحة الصراصير بجدة - شركة التنفيذ"
        loading="eager"
        width="520" height="400"
      >
      <div class="tnf-hero-stat">
        <div>
          <div class="tnf-hero-stat-num">+500</div>
          <div class="tnf-hero-stat-label">عميل راضٍ في جدة</div>
        </div>
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
      </div>
    </div>
  </div>
</section>

<!-- NUMBERS BAR -->
<div class="tnf-numbers-bar">
  <div class="tnf-numbers-inner">
    <div class="tnf-number-item">
      <span class="tnf-number-val">6<span>أشهر</span></span>
      <span class="tnf-number-label">ضمان مكتوب على الخدمة</span>
    </div>
    <div class="tnf-number-item">
      <span class="tnf-number-val">+10<span>سنوات</span></span>
      <span class="tnf-number-label">خبرة فنيينا في جدة</span>
    </div>
    <div class="tnf-number-item">
      <span class="tnf-number-val">4<span>أنواع</span></span>
      <span class="tnf-number-label">مبيدات ألمانية معتمدة</span>
    </div>
    <div class="tnf-number-item">
      <span class="tnf-number-val">24<span>/7</span></span>
      <span class="tnf-number-label">خدمة طوارئ متاحة</span>
    </div>
  </div>
</div>

<!-- SECTION 1: WHY US -->
<section class="tnf-section">
  <div class="tnf-img-split">
    <div class="tnf-img-content">
      <div class="tnf-badge">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        لماذا جدة تحتاج حلاً مختلفاً؟
      </div>
      <h2 style="text-align:right;">
        صراصير جدة أصعب...<br>ولذلك نعمل بطريقة مختلفة
      </h2>
      <p>
        رطوبة جدة التي تتجاوز 70% ودرجات حرارتها العالية توفر بيئة مثالية لتكاثر الصراصير، خاصةً صراصير الصرف الصحي التي طوّرت مقاومة للمبيدات التقليدية. لهذا السبب لا تكفي المبيدات العادية.
      </p>
      <ul class="tnf-check-list">
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          نستخدم مبيدات ألمانية تخترق أماكن تعشيش الصراصير في مواسير الصرف
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          نحدد نوع الصرصار (ألماني، أمريكي، بني) لنختار المبيد المناسب تماماً
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          ضمان مكتوب 6 أشهر - نعود مجاناً إذا عاد الصرصار
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          المبيدات آمنة تماماً على الأطفال والحيوانات الأليفة بعد الجفاف
        </li>
      </ul>
    </div>
    <img
      src="<?php echo esc_url(get_template_directory_uri()); ?>/images/ارخص-شركة-مكافحة-الصراصير-بجدة.webp"
      alt="أرخص شركة مكافحة الصراصير بجدة مع ضمان الجودة"
      loading="lazy"
      width="520" height="420"
    >
  </div>
</section>

<!-- SECTION 2: TRUST SIGNALS TABLE -->
<section class="tnf-section-full" style="background: var(--bg-light, #f4f9f5);">
  <div style="max-width:1100px; margin:0 auto; padding:0 20px;">
    <h2 class="tnf-h2">
      شركة التنفيذ مقابل <span>الطرق التقليدية</span>
    </h2>
    <div class="tnf-divider"></div>
    <p class="tnf-subtitle">
      لا تضيّع وقتك مع حلول مؤقتة. إليك الفرق الحقيقي بالأرقام.
    </p>

    <div class="tnf-compare-wrap">
      <table class="tnf-compare" role="table" aria-label="مقارنة شركة التنفيذ بالطرق التقليدية">
        <thead>
          <tr>
            <th class="th-label">معيار المقارنة</th>
            <th class="th-us">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="display:inline;margin-left:6px;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              شركة التنفيذ
            </th>
            <th class="th-them">الطرق التقليدية</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>نوع المبيد</td>
            <td class="td-us">4 أنواع ألمانية معتمدة ومتخصصة</td>
            <td class="td-them">مبيد واحد عام</td>
          </tr>
          <tr>
            <td>وقت الفعالية</td>
            <td class="td-us"><span class="tnf-icon-check">&#10004;</span> 24 - 48 ساعة</td>
            <td class="td-them"><span class="tnf-icon-x">&#10007;</span> أسابيع وقد لا تعمل</td>
          </tr>
          <tr>
            <td>الضمان</td>
            <td class="td-us"><span class="tnf-icon-check">&#10004;</span> 6 أشهر مكتوب</td>
            <td class="td-them"><span class="tnf-icon-x">&#10007;</span> لا يوجد ضمان</td>
          </tr>
          <tr>
            <td>السعر</td>
            <td class="td-us">شفاف ومنافس بدون مفاجآت</td>
            <td class="td-them">متغير وغير واضح</td>
          </tr>
          <tr>
            <td>سلامة الأطفال</td>
            <td class="td-us"><span class="tnf-icon-check">&#10004;</span> آمن بعد الجفاف (3 ساعات)</td>
            <td class="td-them"><span class="tnf-icon-x">&#10007;</span> قد يكون خطراً</td>
          </tr>
          <tr>
            <td>تحديد نوع الصرصار</td>
            <td class="td-us"><span class="tnf-icon-check">&#10004;</span> فحص ميداني متخصص</td>
            <td class="td-them"><span class="tnf-icon-x">&#10007;</span> معالجة عشوائية</td>
          </tr>
          <tr>
            <td>خبرة الفني</td>
            <td class="td-us">+10 سنوات خبرة في جدة</td>
            <td class="td-them">غير محدد</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- SECTION 3: SERVICE METHODOLOGY -->
<section class="tnf-section">
  <h2 class="tnf-h2">طريقة عملنا في <span>مكافحة الصراصير</span></h2>
  <div class="tnf-divider"></div>
  <p class="tnf-subtitle">
    4 خطوات علمية مدروسة تضمن القضاء التام على الصراصير من جذورها، لا مجرد إخفاء المشكلة مؤقتاً.
  </p>

  <div class="tnf-steps">
    <div class="tnf-step">
      <div class="tnf-step-num">1</div>
      <div class="tnf-step-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1B6B3A" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
      <h3>الفحص الشامل المجاني</h3>
      <p>يزور فنيّنا المنزل ويفحص جميع نقاط الدخول: مواسير المطبخ والحمام، الفجوات، الزوايا المخفية، وتحت الأجهزة. الفحص مجاني تماماً بدون أي التزام.</p>
    </div>
    <div class="tnf-step">
      <div class="tnf-step-num">2</div>
      <div class="tnf-step-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1B6B3A" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      </div>
      <h3>تحديد النوع ووضع الخطة</h3>
      <p>نحدد نوع الصرصار (ألماني، أمريكي، أو بني) ومصدره الرئيسي. بناءً على ذلك نختار المبيد الألماني الأنسب من بين 4 أنواع متخصصة للقضاء الكامل.</p>
    </div>
    <div class="tnf-step">
      <div class="tnf-step-num">3</div>
      <div class="tnf-step-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1B6B3A" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <h3>الرش الاحترافي بالمبيدات الألمانية</h3>
      <p>نرش بتقنية الجل والرذاذ الدقيق في كل الأماكن التي يختبئ فيها الصرصار. المبيدات الألمانية المعتمدة تقضي على البيض أيضاً، مما يمنع التكاثر من جديد.</p>
    </div>
    <div class="tnf-step">
      <div class="tnf-step-num">4</div>
      <div class="tnf-step-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#1B6B3A" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <h3>المتابعة وضمان 6 أشهر</h3>
      <p>بعد الرش نزورك مرة للمتابعة، ونمنحك ضماناً مكتوباً لمدة 6 أشهر. أي ظهور للصراصير خلال فترة الضمان؟ نعود فوراً ومجاناً.</p>
    </div>
  </div>
</section>

<!-- IMAGE BREAK -->
<div style="max-width:1100px; margin:0 auto 60px; padding:0 20px;">
  <img
    src="<?php echo esc_url(get_template_directory_uri()); ?>/images/شركة-مكافحة-الصراصير-بجدة-رخيصة.webp"
    alt="شركة مكافحة الصراصير بجدة بأسعار مناسبة وجودة عالية"
    loading="lazy"
    width="1100" height="450"
    style="width:100%; height:auto; object-fit:cover; border-radius:20px; max-height:380px;"
  >
</div>

<!-- SECTION 4: GEO TARGETING -->
<section class="tnf-section-full" style="background: var(--bg-light, #f4f9f5);">
  <div style="max-width:1100px; margin:0 auto; padding:0 20px;">
    <div class="tnf-geo-wrap">
      <h2 class="tnf-h2" style="color:#fff;">
        نصل إليك في <span>أي حي بجدة</span><br>خلال ساعة واحدة
      </h2>
      <p class="tnf-geo-subtitle">
        فريقنا متوزع على جميع أرجاء جدة ومستعد للاستجابة الفورية. شركة مكافحة صراصير بجدة تغطي:
      </p>

      <div class="tnf-geo-grid">
        <?php
        $areas = array(
          'أبحر الشمالية', 'أبحر الجنوبية', 'المرجان', 'الصفا', 'حي الروضة', 'الزهراء',
          'النزهة', 'الحمراء', 'العزيزية', 'الفيصلية', 'الشاطئ', 'السلامة',
          'بحرة', 'الرويس', 'المحمدية', 'القريات', 'الأمير فواز', 'الخالدية',
          'مشرفة', 'البوادي', 'الثغر', 'حي الجامعة', 'الشرفية', 'بريمان',
          'النهضة', 'السليمانية', 'طيبة', 'السبيل', 'الوزيرية', 'أم السلم',
          'الربوة', 'وجميع أحياء جدة'
        );
        foreach ($areas as $area) : ?>
          <div class="tnf-geo-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            <?php echo esc_html($area); ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="tnf-geo-note">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <p>
          <strong>سرعة الاستجابة مضمونة:</strong> فريقنا المنتشر في أحياء جدة يصل إليك في غضون ساعة واحدة من وقت التواصل، في أي وقت من اليوم.
          <a href="https://wa.me/966XXXXXXXXX?text=<?php echo rawurlencode('أريد طلب خدمة مكافحة صراصير في جدة'); ?>" style="color:#f5c842; font-weight:700; margin-right:8px;" target="_blank" rel="nofollow noopener">&larr; احجز الآن</a>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- SECTION: Image + Guarantee -->
<section class="tnf-section">
  <div class="tnf-img-split reverse">
    <img
      src="<?php echo esc_url(get_template_directory_uri()); ?>/images/شركة-مكافحة-الصراصير-بجدة-يالضمان.webp"
      alt="شركة مكافحة الصراصير بجدة بالضمان - التنفيذ"
      loading="lazy"
      width="520" height="420"
    >
    <div class="tnf-img-content">
      <div class="tnf-badge">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        ضمان موثق ومكتوب
      </div>
      <h2>ضمان 6 أشهر حقيقي،<br>لا مجرد وعد شفهي</h2>
      <p>
        نحن الشركة الوحيدة في جدة التي تقدم ضماناً مكتوباً وموثقاً لمدة 6 أشهر كاملة. إذا عاد الصرصار خلال فترة الضمان، نعود فوراً ونعيد الرش مجاناً بدون أي تكاليف إضافية.
      </p>
      <ul class="tnf-check-list">
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          ضمان مكتوب تحصل عليه بعد كل زيارة
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          زيارة متابعة مجانية بعد 2 أسبوع من الرش
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          خط دعم مباشر طوال فترة الضمان
        </li>
      </ul>
      <div style="margin-top:20px;">
        <a href="https://wa.me/966XXXXXXXXX?text=<?php echo rawurlencode('أريد الاستفسار عن الضمان'); ?>" class="tnf-btn-wa" target="_blank" rel="nofollow noopener" style="display:inline-flex;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          احصل على الضمان الآن
        </a>
      </div>
    </div>
  </div>
</section>

<!-- SECTION 5: FAQ -->
<section class="tnf-section-full" style="background: var(--bg-light, #f4f9f5);">
  <div style="max-width:1100px; margin:0 auto; padding:0 20px;">
    <h2 class="tnf-h2">أسئلة يسألها سكان جدة <span>كل يوم</span></h2>
    <div class="tnf-divider"></div>
    <p class="tnf-subtitle">إجابات مباشرة وواضحة من خبراء مكافحة الصراصير في شركة التنفيذ.</p>

    <div class="tnf-faq-wrap" itemscope itemtype="https://schema.org/FAQPage">

      <?php
      $faqs = array(
        array(
          'icon' => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
          'q' => 'هل مبيدات رش الصراصير آمنة على الأطفال والحيوانات الأليفة؟',
          'a' => 'نعم، المبيدات الألمانية التي نستخدمها معتمدة دولياً وآمنة تماماً على الأطفال والحيوانات الأليفة بعد جفافها. ننصح بالابتعاد عن المنزل لمدة 3 ساعات أثناء الرش وبعده مباشرةً، ثم يمكن العودة بأمان كامل. نستخدم تقنية الجل في المطابخ كإجراء إضافي للسلامة.'
        ),
        array(
          'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
          'q' => 'متى يمكنني العودة للمنزل بعد رش الصراصير؟',
          'a' => 'يمكنك العودة للمنزل بعد 3 ساعات من انتهاء الرش. نوصي بفتح النوافذ لتهوية المكان قبل عودة الأطفال والحيوانات. أما الأسطح التي تلامس الطعام (أسطح المطبخ) فنوصي بمسحها قبل الاستخدام. فنيّونا يشرحون كل تعليمات السلامة بالتفصيل قبل المغادرة.'
        ),
        array(
          'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
          'q' => 'كم مدة فعالية الرش وما مدة الضمان؟',
          'a' => 'المبيدات الألمانية التي نستخدمها تبدأ في القضاء على الصراصير خلال 24 - 48 ساعة، وتستمر فعاليتها من 3 إلى 6 أشهر. نضمن النتائج لمدة 6 أشهر كاملة بضمان مكتوب. إذا ظهر أي صرصار خلال فترة الضمان، نعود ونعيد الرش مجاناً.'
        ),
        array(
          'icon' => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>',
          'q' => 'هل تخدمون جميع أحياء جدة؟ وكم وقت الاستجابة؟',
          'a' => 'نعم، نغطي جميع أحياء جدة بدون استثناء، من أبحر الشمالية شمالاً حتى أم السلم جنوباً. فريقنا المنتشر في مناطق مختلفة من جدة يضمن الوصول إليك خلال ساعة واحدة في معظم الأحياء. نعمل 24 ساعة / 7 أيام بما فيها العطل الرسمية لطوارئ الصراصير.'
        ),
        array(
          'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
          'q' => 'ما هي تكلفة مكافحة الصراصير في جدة؟',
          'a' => 'السعر يعتمد على مساحة المكان ودرجة الإصابة. نقدم الفحص الأولي مجاناً، وبعد الفحص تحصل على سعر شفاف ومحدد قبل بدء العمل، بدون أي رسوم مخفية. نضمن أفضل سعر في السوق مع الحفاظ على أعلى جودة. تواصل معنا الآن للحصول على عرض سعر مجاني.'
        ),
        array(
          'icon' => '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>',
          'q' => 'لماذا تعود الصراصير بعد الرش؟ وكيف تحل شركة التنفيذ هذه المشكلة؟',
          'a' => 'الصراصير تعود عادةً لأسبابٍ ثلاثة: إما أن المبيد المستخدم لم يقضِ على البيض، أو أن مصدر الدخول لم يُعالج، أو أن الصرصار طوّر مقاومة للمبيد. نحن نحل هذه المشكلة بثلاث طرق: أولاً نستخدم مبيدات ألمانية تقضي على البيض أيضاً، ثانياً نحدد ونعالج جميع نقاط الدخول (المواسير والشقوق)، ثالثاً نستخدم 4 أنواع مختلفة من المبيدات لتجنب مقاومة الصرصار.'
        ),
      );

      foreach ($faqs as $faq) : ?>
        <div class="tnf-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <div class="tnf-faq-q">
            <div class="tnf-faq-q-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1B6B3A" stroke-width="2.5"><?php echo $faq['icon']; ?></svg>
            </div>
            <span itemprop="name"><?php echo esc_html($faq['q']); ?></span>
          </div>
          <div class="tnf-faq-a" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text"><?php echo esc_html($faq['a']); ?></div>
          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<!-- LAST IMAGE -->
<div style="max-width:1100px; margin:40px auto; padding:0 20px;">
  <img
    src="<?php echo esc_url(get_template_directory_uri()); ?>/images/شركة-رش-الصراصير-بجدة.webp"
    alt="شركة رش الصراصير بجدة - التنفيذ للخدمات المنزلية"
    loading="lazy"
    width="1100" height="450"
    style="width:100%; height:auto; object-fit:cover; border-radius:20px; max-height:380px;"
  >
</div>

<!-- CTA BAND -->
<div class="tnf-cta-band">
  <h2>هل تعاني من الصراصير الآن؟<br>الحل أسرع مما تتوقع</h2>
  <p>فحص مجاني + ضمان 6 أشهر + وصول خلال ساعة. لا تنتظر أكثر.</p>
  <a
    href="https://wa.me/966XXXXXXXXX?text=<?php echo rawurlencode('أريد طلب فحص مجاني لمكافحة الصراصير في جدة'); ?>"
    class="tnf-btn-dark"
    target="_blank"
    rel="nofollow noopener"
  >
    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    احجز فحصك المجاني الآن عبر واتساب
  </a>
</div>

</div><!-- end .tnf-page -->

<?php
get_footer();
