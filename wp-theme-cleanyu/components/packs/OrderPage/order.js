(function () {
  'use strict';
  var roots = document.querySelectorAll('.yc-order[data-wa]');
  Array.prototype.forEach.call(roots, function (root) {
    if (root.getAttribute('data-ready')) { return; }
    root.setAttribute('data-ready', '1');

    var wa = root.getAttribute('data-wa');
    var pageUrl = root.getAttribute('data-url') || location.href;
    var form = root.querySelector('.yc-order__form');
    var btn = root.querySelector('.yc-order__submit');
    var ok = root.querySelector('.yc-order__ok');
    var help = root.querySelector('.yc-order__help');
    var loc = form.querySelector('[name=location]');
    var picked = { service: '', city: '' };

    function q(name) { return form.querySelector('[name=' + name + ']'); }
    function val(name) { var el = q(name); return el ? el.value.trim() : ''; }

    // تحويل الأرقام العربية/الفارسية إلى لاتينية
    function latin(s) {
      return String(s).replace(/[٠-٩]/g, function (d) { return d.charCodeAt(0) - 1632; })
                      .replace(/[۰-۹]/g, function (d) { return d.charCodeAt(0) - 1776; });
    }
    function normPhone(s) {
      var n = latin(s).replace(/[^\d+]/g, '');
      if (n.indexOf('+') === 0) { n = n.slice(1); }
      if (n.indexOf('00') === 0) { n = n.slice(2); }
      return n;
    }
    function phoneOk(s) {
      var n = normPhone(s);
      return /^05\d{8}$/.test(n) || /^9665\d{8}$/.test(n) || /^5\d{8}$/.test(n);
    }

    /* ---------- الأخطاء ---------- */
    function err(key, msg, focusEl) {
      var box = root.querySelector('[id$="-e-' + key + '"]');
      if (!box) { return; }
      box.textContent = msg || '';
      box.hidden = !msg;
      var target = q(key) || (focusEl || null);
      if (target && target.setAttribute) { target.setAttribute('aria-invalid', msg ? 'true' : 'false'); }
    }

    /* ---------- الأزرار المختارة (خدمة / مدينة) ---------- */
    Array.prototype.forEach.call(form.querySelectorAll('[data-group]'), function (group) {
      var key = group.getAttribute('data-group');
      var chips = group.querySelectorAll('.yc-order__chip');
      Array.prototype.forEach.call(chips, function (chip, i) {
        chip.tabIndex = i === 0 ? 0 : -1;
        chip.addEventListener('click', function () {
          Array.prototype.forEach.call(chips, function (c) {
            var on = c === chip;
            c.setAttribute('aria-checked', on ? 'true' : 'false');
            c.tabIndex = on ? 0 : -1;
          });
          picked[key] = chip.getAttribute('data-value');
          err(key, '');
          build();
        });
        chip.addEventListener('keydown', function (e) {
          var n = null;
          if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') { n = chips[(i + 1) % chips.length]; }
          else if (e.key === 'ArrowRight' || e.key === 'ArrowUp') { n = chips[(i - 1 + chips.length) % chips.length]; }
          if (n) { e.preventDefault(); n.focus(); n.click(); }
        });
      });
    });

    /* ---------- الموقع ---------- */
    function showHelp(msg) { help.textContent = msg; help.hidden = !msg; }
    var locate = root.querySelector('.yc-order__locate');
    if (locate) {
      locate.addEventListener('click', function () {
        if (!navigator.geolocation) { showHelp('المتصفح لا يدعم تحديد الموقع — الصق رابط Google Maps يدوياً.'); return; }
        locate.disabled = true;
        showHelp('جارٍ تحديد موقعك...');
        navigator.geolocation.getCurrentPosition(function (p) {
          loc.value = 'https://maps.google.com/?q=' + p.coords.latitude.toFixed(6) + ',' + p.coords.longitude.toFixed(6);
          locate.disabled = false; showHelp(''); build();
        }, function () {
          locate.disabled = false;
          showHelp('💡 إذا رفض المتصفح طلب الموقع: افتح إعدادات الموقع وفعّل صلاحية تحديد الموقع، أو الصق رابط Google Maps يدوياً.');
        }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 });
      });
    }
    var paste = root.querySelector('.yc-order__paste');
    if (paste) {
      paste.addEventListener('click', function () {
        if (!navigator.clipboard || !navigator.clipboard.readText) { loc.focus(); showHelp('اضغط مطولاً داخل الحقل واختر «لصق».'); return; }
        navigator.clipboard.readText().then(function (t) {
          if (t) { loc.value = t.trim(); showHelp(''); build(); }
        }).catch(function () { loc.focus(); showHelp('اضغط مطولاً داخل الحقل واختر «لصق».'); });
      });
    }

    /* ---------- رسالة واتساب ---------- */
    function build() {
      var lines = ['📋 طلب خدمة جديد', ''];
      lines.push('الاسم: ' + val('name'));
      lines.push('الجوال: ' + normPhone(val('phone')));
      if (picked.service) { lines.push('الخدمة: ' + picked.service); }
      if (picked.city) { lines.push('المدينة: ' + picked.city); }
      if (val('location')) { lines.push('الموقع: ' + val('location')); }
      if (val('date')) { lines.push('التاريخ المفضل: ' + val('date')); }
      if (val('time')) { lines.push('الوقت المفضل: ' + val('time')); }
      if (val('notes')) { lines.push('ملاحظات: ' + val('notes')); }
      lines.push('', pageUrl);
      if (btn) { btn.href = 'https://wa.me/' + wa + '?text=' + encodeURIComponent(lines.join('\n')); }
    }
    form.addEventListener('input', function (e) {
      // يختفي الخطأ بمجرد تصحيح الحقل
      var n = e.target.name;
      if (n === 'name' && val('name').length >= 2) { err('name', ''); }
      if (n === 'phone' && phoneOk(val('phone'))) { err('phone', ''); }
      build();
    });
    form.addEventListener('change', build);

    function validate() {
      var first = null;
      function fail(key, msg, el) { err(key, msg); if (!first) { first = el; } }
      if (val('name').length < 2) { fail('name', 'من فضلك اكتب اسمك.', q('name')); } else { err('name', ''); }
      if (!phoneOk(val('phone'))) { fail('phone', 'اكتب رقم جوال سعودي صحيح مثل 05xxxxxxxx.', q('phone')); } else { err('phone', ''); }
      var sg = form.querySelector('[data-group=service]');
      if (sg && !picked.service) { fail('service', 'اختر الخدمة المطلوبة.', sg.querySelector('.yc-order__chip')); }
      var cg = form.querySelector('[data-group=city]');
      if (cg && !picked.city) { fail('city', 'اختر مدينتك.', cg.querySelector('.yc-order__chip')); }
      if (first) { first.focus(); }
      return !first;
    }

    if (btn) {
      // في مرحلة الالتقاط: نوقف الانتقال (وعدّاد التتبع) إن كان النموذج ناقصًا
      btn.addEventListener('click', function (e) {
        build();
        if (!validate()) {
          e.preventDefault();
          e.stopImmediatePropagation();
          return;
        }
        ok.hidden = false;
        setTimeout(function () { ok.hidden = true; }, 6000);
      }, true);
    }
    form.addEventListener('submit', function (e) { e.preventDefault(); if (btn) { btn.click(); } });

    build();
  });
})();
