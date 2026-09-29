(function () {
  'use strict';
  var roots = document.querySelectorAll('.yc-calc[data-cur]');
  Array.prototype.forEach.call(roots, function (root) {
    if (root.getAttribute('data-ready')) { return; }
    root.setAttribute('data-ready', '1');

    var cur = root.getAttribute('data-cur') || '';
    var vat = parseFloat(root.getAttribute('data-vat')) || 0;
    var minOrder = parseFloat(root.getAttribute('data-min')) || 0;
    var wa = root.getAttribute('data-wa') || '';
    var pageUrl = root.getAttribute('data-url') || location.href;

    var tabs = root.querySelectorAll('[role="tab"]');
    var items = root.querySelectorAll('.yc-calc__item');
    var lines = root.querySelector('.yc-calc__lines');
    var none = root.querySelector('.yc-calc__none');
    var totals = root.querySelector('.yc-calc__totals');
    var subEl = root.querySelector('.yc-calc__sub');
    var vatEl = root.querySelector('.yc-calc__vat');
    var totalEl = root.querySelector('.yc-calc__total');
    var minMsg = root.querySelector('.yc-calc__minmsg');
    var waBtn = root.querySelector('.yc-calc__wa');
    var citySel = root.querySelector('.yc-calc__city select');
    var resetBtn = root.querySelector('.yc-calc__reset');
    var mini = root.querySelector('.yc-calc__mini');

    // أرقام لاتينية بفواصل: 8,400 أو 8,400.50
    function fmt(n) {
      var r = Math.round(n * 100) / 100;
      return r.toLocaleString('en-US', { minimumFractionDigits: r % 1 ? 2 : 0, maximumFractionDigits: 2 });
    }

    /* ---------- التبويبات ---------- */
    function selectTab(tab, focus) {
      Array.prototype.forEach.call(tabs, function (t) {
        var on = t === tab;
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.tabIndex = on ? 0 : -1;
        var p = document.getElementById(t.getAttribute('aria-controls'));
        if (p) { p.hidden = !on; }
      });
      if (focus) { tab.focus(); }
    }
    Array.prototype.forEach.call(tabs, function (t, i) {
      t.addEventListener('click', function () { selectTab(t); });
      t.addEventListener('keydown', function (e) {
        var k = e.key, n = null;
        // الاتجاه من اليمين لليسار: السهم الأيسر = التالي
        if (k === 'ArrowLeft') { n = tabs[(i + 1) % tabs.length]; }
        else if (k === 'ArrowRight') { n = tabs[(i - 1 + tabs.length) % tabs.length]; }
        else if (k === 'Home') { n = tabs[0]; }
        else if (k === 'End') { n = tabs[tabs.length - 1]; }
        if (n) { e.preventDefault(); selectTab(n, true); }
      });
    });

    /* ---------- الكميات ---------- */
    function qtyOf(item) {
      var v = parseInt(item.querySelector('input').value, 10);
      return isNaN(v) || v < 0 ? 0 : v;
    }
    function setQty(item, v) {
      var min = parseInt(item.getAttribute('data-min'), 10) || 1;
      v = Math.max(0, Math.floor(v || 0));
      if (v > 0 && v < min) { v = min; }
      if (v > 99999) { v = 99999; }
      item.querySelector('input').value = v;
      item.classList.toggle('is-on', v > 0);
    }

    Array.prototype.forEach.call(items, function (item) {
      var input = item.querySelector('input');
      var min = parseInt(item.getAttribute('data-min'), 10) || 1;
      item.querySelector('.yc-calc__plus').addEventListener('click', function () {
        var q = qtyOf(item);
        setQty(item, q === 0 ? min : q + 1); update();
      });
      item.querySelector('.yc-calc__minus').addEventListener('click', function () {
        var q = qtyOf(item);
        setQty(item, q <= min ? 0 : q - 1); update();
      });
      input.addEventListener('input', function () { item.classList.toggle('is-on', qtyOf(item) > 0); update(); });
      input.addEventListener('change', function () { setQty(item, qtyOf(item)); update(); });
    });

    if (citySel) { citySel.addEventListener('change', update); }
    if (resetBtn) {
      resetBtn.addEventListener('click', function () {
        Array.prototype.forEach.call(items, function (it) { setQty(it, 0); });
        update();
      });
    }

    /* ---------- الحساب ---------- */
    var lastSig = null;
    function update() {
      // لا نعيد بناء القائمة إن لم تتغير الكميات — حتى لا تضيع نقرة «✕»
      // عندما يُطلق حقل الكمية حدث change لحظة مغادرته
      var sig = Array.prototype.map.call(items, qtyOf).join(',') + '|' + (citySel ? citySel.value : '');
      if (sig === lastSig) { return; }
      lastSig = sig;
      var sub = 0, rows = [], perTab = {};
      lines.innerHTML = '';
      Array.prototype.forEach.call(items, function (item) {
        var q = qtyOf(item);
        if (!q) { return; }
        var price = parseFloat(item.getAttribute('data-price')) || 0;
        var name = item.getAttribute('data-name');
        var unit = item.getAttribute('data-unit');
        var cat = item.getAttribute('data-cat');
        var line = price * q;
        sub += line;
        perTab[cat] = (perTab[cat] || 0) + 1;
        rows.push({ name: name, unit: unit, q: q, line: line });

        var li = document.createElement('li');
        var s = document.createElement('span');
        s.textContent = name + ' × ' + fmt(q) + (unit ? ' ' + unit : '');
        var b = document.createElement('b');
        b.textContent = fmt(line) + ' ' + cur;
        var x = document.createElement('button');
        x.type = 'button'; x.className = 'yc-calc__rm'; x.textContent = '✕';
        x.setAttribute('aria-label', 'إزالة ' + name);
        x.addEventListener('click', function () { setQty(item, 0); update(); });
        li.appendChild(s); li.appendChild(b); li.appendChild(x);
        lines.appendChild(li);
      });

      Array.prototype.forEach.call(tabs, function (t, i) {
        var c = t.querySelector('.yc-calc__tabcount');
        if (!c) { return; }
        c.textContent = perTab[i] || 0;
        c.hidden = !perTab[i];
      });

      var vatVal = sub * vat / 100;
      var total = sub + vatVal;
      var has = rows.length > 0;
      var belowMin = has && minOrder > 0 && sub < minOrder;

      none.hidden = has;
      totals.hidden = !has;
      if (resetBtn) { resetBtn.hidden = !has; }
      subEl.textContent = fmt(sub) + ' ' + cur;
      if (vatEl) { vatEl.textContent = fmt(vatVal) + ' ' + cur; }
      totalEl.textContent = fmt(total) + ' ' + cur;
      if (mini) { mini.hidden = !has; mini.querySelector('b').textContent = fmt(total) + ' ' + cur; }
      if (minMsg) { minMsg.hidden = !belowMin; }

      if (waBtn) {
        var ok = has && !belowMin;
        waBtn.setAttribute('aria-disabled', ok ? 'false' : 'true');
        var msg = 'مرحبًا، أرغب في حجز الخدمات التالية:\n';
        rows.forEach(function (r) {
          msg += '• ' + r.name + ' × ' + fmt(r.q) + (r.unit ? ' ' + r.unit : '') + ' = ' + fmt(r.line) + ' ' + cur + '\n';
        });
        msg += '\nالإجمالي التقديري: ' + fmt(total) + ' ' + cur + (vat ? ' (شامل الضريبة)' : '');
        if (citySel && citySel.value) { msg += '\nالمدينة: ' + citySel.value; }
        msg += '\n\n' + pageUrl;
        waBtn.href = 'https://wa.me/' + wa + '?text=' + encodeURIComponent(msg);
      }
    }

    if (waBtn) {
      waBtn.addEventListener('click', function (e) {
        if (waBtn.getAttribute('aria-disabled') === 'true') {
          e.preventDefault();
          e.stopImmediatePropagation();
          var first = root.querySelector('.yc-calc__panel:not([hidden]) .yc-calc__plus');
          if (first) { first.focus(); }
        }
      }, true);
    }

    update();
  });
})();
