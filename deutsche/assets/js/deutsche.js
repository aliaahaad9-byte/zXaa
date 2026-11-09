/* Deutsche Garage — front-end behaviour (vanilla, no dependencies). */
(function () {
  'use strict';
  var d = document;
  var $ = function (s, c) { return (c || d).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Header: scrolled state ---------- */
  var header = $('[data-dg-header]');
  if (header) {
    var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 24); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------- Mobile navigation drawer ---------- */
  var nav = $('[data-dg-nav]'), openBtn = $('[data-nav-open]'), scrim = $('.nav-scrim');
  function setNav(open) {
    if (!nav) return;
    nav.classList.toggle('is-open', open);
    d.documentElement.classList.toggle('nav-locked', open);
    if (openBtn) openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (scrim) scrim.hidden = !open;
    if (open) { var f = $('a, button', nav); if (f) f.focus(); } else if (openBtn) { openBtn.focus(); }
  }
  if (openBtn) openBtn.addEventListener('click', function () { setNav(true); });
  $$('[data-nav-close]').forEach(function (b) { b.addEventListener('click', function () { setNav(false); }); });
  d.addEventListener('keydown', function (e) { if (e.key === 'Escape' && nav && nav.classList.contains('is-open')) setNav(false); });

  // Sub-menus: add an accessible toggle next to each parent link.
  $$('.nav__list > li.menu-item-has-children').forEach(function (li, i) {
    var sub = $('.sub-menu', li); if (!sub) return;
    sub.id = sub.id || 'sub-' + i;
    var b = d.createElement('button');
    b.type = 'button'; b.className = 'sub-toggle';
    b.setAttribute('aria-expanded', 'false'); b.setAttribute('aria-controls', sub.id);
    b.setAttribute('aria-label', 'عرض القائمة الفرعية');
    b.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
    li.insertBefore(b, sub);
    b.addEventListener('click', function () {
      var o = b.getAttribute('aria-expanded') === 'true';
      b.setAttribute('aria-expanded', o ? 'false' : 'true');
      li.classList.toggle('is-open', !o);
    });
    var a = li.querySelector('a');
    if (a && a.getAttribute('href') === '#') a.addEventListener('click', function (e) { e.preventDefault(); b.click(); });
  });

  /* ---------- Generic tabs (schedules, warning lights) ---------- */
  function tabs(root, tabSel) {
    var list = $$(tabSel, root);
    function select(t, focus) {
      list.forEach(function (x) {
        var on = x === t;
        x.setAttribute('aria-selected', on ? 'true' : 'false');
        x.tabIndex = on ? 0 : -1;
        var p = d.getElementById(x.getAttribute('aria-controls'));
        if (p) p.hidden = !on;
      });
      if (focus) t.focus();
    }
    list.forEach(function (t, i) {
      t.addEventListener('click', function () {
        select(t);
        var p = d.getElementById(t.getAttribute('aria-controls'));
        if (p && window.innerWidth < 900 && root.hasAttribute('data-dg-wl')) p.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'nearest' });
      });
      t.addEventListener('keydown', function (e) {
        var k = e.key, n = null;
        // RTL: ArrowLeft moves forward.
        if (k === 'ArrowLeft' || k === 'ArrowDown') n = list[(i + 1) % list.length];
        if (k === 'ArrowRight' || k === 'ArrowUp') n = list[(i - 1 + list.length) % list.length];
        if (k === 'Home') n = list[0];
        if (k === 'End') n = list[list.length - 1];
        if (n) { e.preventDefault(); select(n, true); }
      });
    });
  }
  $$('[data-dg-tabs]').forEach(function (r) { tabs(r, '[role="tab"]'); });
  $$('[data-dg-wl]').forEach(function (r) { tabs(r, '[role="tab"]'); });

  /* ---------- Smart Service Estimator ---------- */
  $$('[data-dg-est]').forEach(function (form) {
    var steps = $$('.est__step', form), labels = $$('.est__steps li', form);
    var bar = $('.est__progress span', form), err = $('.est__error', form);
    var prev = $('[data-prev]', form), next = $('[data-next]', form), submit = $('[data-submit]', form);
    var need = form.elements.need, lightField = $('[data-light-field]', form);
    var cur = 0;

    function toLatin(s) { return String(s || '').replace(/[٠-٩]/g, function (c) { return '٠١٢٣٤٥٦٧٨٩'.indexOf(c); }); }
    function syncLight() { if (lightField && need) lightField.hidden = need.value !== 'لمبة عطل منورة'; }
    if (need) { need.addEventListener('change', syncLight); syncLight(); }

    function show(i) {
      cur = i;
      steps.forEach(function (s, j) { s.hidden = j !== i; s.classList.toggle('is-on', j === i); });
      labels.forEach(function (l, j) { l.classList.toggle('is-on', j <= i); if (j === i) l.setAttribute('aria-current', 'step'); else l.removeAttribute('aria-current'); });
      if (bar) bar.style.setProperty('--p', ((i + 1) / steps.length * 100) + '%');
      prev.hidden = i === 0;
      next.hidden = i === steps.length - 1;
      submit.hidden = i !== steps.length - 1;
      err.textContent = '';
    }
    function valid(i) {
      var s = steps[i], bad = null;
      $$('input, select, textarea', s).forEach(function (f) {
        f.removeAttribute('aria-invalid');
        if (bad) return;
        if (f.type === 'radio') {
          if (f.required && !form.querySelector('input[name="' + f.name + '"]:checked')) bad = f;
          return;
        }
        if (f.required && !String(f.value).trim()) bad = f;
        if (f.name === 'phone' && f.value && toLatin(f.value).replace(/\D/g, '').length < 10) bad = f;
      });
      if (bad) {
        var lbl = bad.type === 'radio' ? 'الماركة' : (form.querySelector('label[for="' + bad.id + '"]') || {}).textContent || '';
        err.textContent = 'من فضلك أكمل: ' + String(lbl).replace(/\(.*\)/, '').trim();
        if (bad.type !== 'radio') bad.setAttribute('aria-invalid', 'true');
        bad.focus();
        return false;
      }
      return true;
    }
    next.addEventListener('click', function () { if (valid(cur)) { show(cur + 1); var f = $('input:not([type=hidden]), select', steps[cur]); if (f) f.focus(); } });
    prev.addEventListener('click', function () { show(cur - 1); });
    $$('.chip input', form).forEach(function (r) { r.addEventListener('change', function () { var m = form.elements.model; if (m) m.focus(); }); });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!valid(cur)) return;
      var v = function (n) { var el = form.elements[n]; if (!el) return ''; if (el.length && !el.tagName) { var c = form.querySelector('input[name="' + n + '"]:checked'); return c ? c.value : ''; } return String(el.value || '').trim(); };
      var needTxt = v('need') + (v('light') && v('need') === 'لمبة عطل منورة' ? ' — ' + v('light') : '');
      var km = toLatin(v('km')).replace(/[^\d]/g, '');
      var lines = [
        'طلب تسعيرة / حجز — دويتش جراج',
        '━━━━━━━━━━━━',
        '🚗 السيارة: ' + v('brand') + ' ' + v('model'),
        '📅 سنة الصنع: ' + v('year'),
        '🔧 المطلوب: ' + needTxt
      ];
      if (km) lines.push('📍 العداد: ' + Number(km).toLocaleString('en') + ' كم');
      if (v('notes')) lines.push('📝 تفاصيل: ' + v('notes'));
      lines.push('👤 الاسم: ' + v('name'));
      lines.push('📞 الموبايل: ' + toLatin(v('phone')));
      var wa = 'https://wa.me/' + form.getAttribute('data-wa') + '?text=' + encodeURIComponent(lines.join('\n'));

      // Save the lead in the dashboard (best effort), then continue to WhatsApp.
      try {
        var fd = new FormData(form);
        fd.set('need', needTxt); fd.set('km', km); fd.set('phone', toLatin(v('phone')));
        fd.set('source', location.pathname);
        if (navigator.sendBeacon) navigator.sendBeacon(form.getAttribute('data-ajax'), fd);
        else fetch(form.getAttribute('data-ajax'), { method: 'POST', body: fd, keepalive: true });
      } catch (x) {}
      track('whatsapp');
      submit.disabled = true;
      submit.querySelector('span').textContent = 'جاري فتح واتساب…';
      window.location.href = wa;
      setTimeout(function () { submit.disabled = false; submit.querySelector('span').textContent = 'إرسال الطلب على واتساب'; }, 4000);
    });
    show(0);
  });

  /* ---------- Reels: load embeds only on click ---------- */
  var modal = $('[data-dg-modal]');
  function embedUrl(u) {
    var m = u.match(/tiktok\.com\/@[^/]+\/video\/(\d+)/);
    if (m) return 'https://www.tiktok.com/embed/v2/' + m[1];
    m = u.match(/instagram\.com\/(?:[^/]+\/)?(reel|p|tv)\/([\w-]+)/);
    if (m) return 'https://www.instagram.com/' + m[1] + '/' + m[2] + '/embed';
    m = u.match(/(?:youtu\.be\/|youtube\.com\/(?:shorts\/|watch\?v=|embed\/))([\w-]{11})/);
    if (m) return 'https://www.youtube-nocookie.com/embed/' + m[1] + '?autoplay=1';
    return '';
  }
  d.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('[data-dg-reel]');
    if (!a || !modal || !modal.showModal) return;
    var src = embedUrl(a.href);
    if (!src) return; // Profile links open in a new tab.
    e.preventDefault();
    $('.reel-modal__frame', modal).innerHTML = '<iframe src="' + src + '" title="فيديو من دويتش جراج" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
    modal.showModal();
  });
  if (modal) {
    var close = function () { $('.reel-modal__frame', modal).innerHTML = ''; if (modal.open) modal.close(); };
    $('[data-close]', modal).addEventListener('click', close);
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    modal.addEventListener('close', function () { $('.reel-modal__frame', modal).innerHTML = ''; });
  }

  /* ---------- Lazy Google map ---------- */
  $$('[data-dg-map]').forEach(function (w) {
    var load = function () { var t = $('template', w); if (t) w.innerHTML = t.innerHTML; };
    $('button', w).addEventListener('click', load);
    if ('IntersectionObserver' in window && !(navigator.connection && navigator.connection.saveData)) {
      // Auto-load when near the viewport on good connections only.
      var io = new IntersectionObserver(function (en) { if (en[0].isIntersecting) { io.disconnect(); setTimeout(load, 600); } }, { rootMargin: '200px' });
      io.observe(w);
    }
  });

  /* ---------- Hero video: respect reduced motion & data saver ---------- */
  var vid = $('[data-dg-video]');
  if (vid) {
    var save = navigator.connection && navigator.connection.saveData;
    if (reduce || save || window.innerWidth < 700) { vid.remove(); }
    else { vid.preload = 'auto'; vid.play && vid.play().catch(function () {}); }
  }

  /* ---------- Reveal on scroll ---------- */
  if (!reduce && 'IntersectionObserver' in window) {
    var els = $$('.sec-head, .why__card, .svc, .feat, .sched__card, .reel, .case, .post-card, .checklist__item, .step');
    els.forEach(function (el) { el.classList.add('rv'); });
    var ro = new IntersectionObserver(function (en) {
      en.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('rv-in'); ro.unobserve(x.target); } });
    }, { rootMargin: '0px 0px -8% 0px' });
    els.forEach(function (el) { ro.observe(el); });
  }

  /* ---------- Call / WhatsApp click tracking (callnumber dashboard) ---------- */
  var last = 0;
  function track(type) {
    if (!window.DG || !DG.track) return;
    var now = Date.now(); if (now - last < 1200) return; last = now;
    var h1 = $('h1'); var name = (h1 ? h1.textContent : d.title).replace(/\s+/g, ' ').trim().slice(0, 180);
    var fd = new FormData();
    fd.append('action', 'yc_call_track'); fd.append('call_type', type);
    fd.append('page', name); fd.append('page_url', location.href);
    var url = DG.ajax + '?action=yc_call_track&call_type=' + type + '&page=' + encodeURIComponent(name) + '&page_url=' + encodeURIComponent(location.href);
    try { if (navigator.sendBeacon && navigator.sendBeacon(url, fd)) return; fetch(url, { method: 'POST', body: fd, keepalive: true }); } catch (x) {}
  }
  d.addEventListener('click', function (e) {
    if (typeof window.YCTrackCall === 'function') return; // legacy script handles it
    var a = e.target.closest && e.target.closest('a[href^="tel:"], a[href*="wa.me/"], a[href*="api.whatsapp.com"]');
    if (!a) return;
    track(a.getAttribute('href').indexOf('tel:') === 0 ? 'call' : 'whatsapp');
  });
})();
