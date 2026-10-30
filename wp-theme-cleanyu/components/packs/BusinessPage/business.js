(function () {
  'use strict';
  var root = document.querySelector('.yb[data-wa]');
  if (!root) { return; }
  var wa = root.getAttribute('data-wa');
  var pageUrl = root.getAttribute('data-url') || location.href;
  var form = root.querySelector('.yb-form form');
  var btn = root.querySelector('.yb-send');
  var ok = root.querySelector('.yb-ok');
  if (!form || !btn) { return; }

  function el(n) { return form.querySelector('[name=' + n + ']'); }
  function val(n) { var e = el(n); return e ? e.value.trim() : ''; }
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
  function services() {
    return [].slice.call(form.querySelectorAll('[name=services]:checked')).map(function (c) { return c.value; });
  }
  function setErr(key, msg) {
    var box = form.querySelector('[data-e="' + key + '"]');
    if (box) { box.textContent = msg || ''; box.hidden = !msg; }
    var f = el(key);
    if (f && f.setAttribute) { f.setAttribute('aria-invalid', msg ? 'true' : 'false'); }
  }

  function build() {
    var L = ['📋 طلب عرض سعر - قطاع الأعمال', ''];
    L.push('المنشأة: ' + val('company'));
    if (val('sector')) { L.push('القطاع: ' + val('sector')); }
    L.push('المسؤول: ' + val('person'));
    L.push('الجوال: ' + normPhone(val('phone')));
    if (val('city')) { L.push('المدينة: ' + val('city')); }
    if (val('district')) { L.push('الحي: ' + val('district')); }
    if (services().length) { L.push('الخدمات: ' + services().join('، ')); }
    if (val('notes')) { L.push('ملاحظات: ' + val('notes')); }
    L.push('', pageUrl);
    btn.href = 'https://wa.me/' + wa + '?text=' + encodeURIComponent(L.join('\n'));
  }

  function validate() {
    var first = null, bad = false;
    function fail(key, msg) { setErr(key, msg); bad = true; if (!first) { first = el(key) || form.querySelector('[data-e="' + key + '"]'); } }
    ['company', 'person', 'sector', 'city'].forEach(function (k) {
      if (!val(k)) { fail(k, 'هذا الحقل مطلوب.'); } else { setErr(k, ''); }
    });
    if (!phoneOk(val('phone'))) { fail('phone', 'رقم الجوال غير صحيح (مثال: 05xxxxxxxx).'); } else { setErr('phone', ''); }
    if (!services().length) { fail('services', 'اختر خدمة واحدة على الأقل.'); } else { setErr('services', ''); }
    if (!el('agree').checked) { fail('agree', 'الموافقة مطلوبة لإرسال الطلب.'); } else { setErr('agree', ''); }
    return bad ? first || true : null;
  }

  form.addEventListener('input', build);
  form.addEventListener('change', build);
  form.addEventListener('submit', function (e) { e.preventDefault(); });
  btn.addEventListener('click', function (e) {
    var bad = validate();
    if (bad) {
      e.preventDefault();
      if (ok) { ok.hidden = true; }
      if (bad.focus) { bad.focus(); }
      return;
    }
    build();
    if (ok) { ok.hidden = false; }
  });
  build();
})();
