/* صفحة العميل العامة: Deep Linking + نسخ أرقام المحافظ + عدادات */
(function () {
  var cfg = window.ALZ_PROFILE || {};
  var android = /android/i.test(navigator.userAgent);

  // Android: intent:// يفتح التطبيق إن وُجد وإلا يرجع للمتصفح — iOS: روابط https هي Universal Links أصلاً
  var PKG = {
    facebook: "com.facebook.katana",
    instagram: "com.instagram.android",
    tiktok: "com.zhiliaoapp.musically",
    youtube: "com.google.android.youtube",
    snapchat: "com.snapchat.android",
    x: "com.twitter.android",
    telegram: "org.telegram.messenger",
    maps: "com.google.android.apps.maps",
    whatsapp: "com.whatsapp"
  };

  function send(kind, id) {
    if (!cfg.track || !id) return;
    var body = JSON.stringify({ kind: kind, id: Number(id) });
    try {
      if (!(navigator.sendBeacon && navigator.sendBeacon(cfg.track, new Blob([body], { type: "application/json" })))) {
        fetch(cfg.track, { method: "POST", body: body, keepalive: true, headers: { "Content-Type": "application/json" } });
      }
    } catch (e) {}
  }

  var toastEl;
  function toast(title, sub) {
    if (!toastEl) {
      toastEl = document.createElement("div");
      toastEl.className = "lp-toast";
      document.body.appendChild(toastEl);
    }
    toastEl.textContent = title;
    if (sub) {
      var s = document.createElement("small");
      s.textContent = sub;
      toastEl.appendChild(s);
    }
    toastEl.classList.add("show");
    clearTimeout(toastEl._t);
    toastEl._t = setTimeout(function () { toastEl.classList.remove("show"); }, 2200);
  }

  function copy(text) {
    if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(text);
    var t = document.createElement("textarea");
    t.value = text;
    t.style.position = "fixed";
    t.style.opacity = "0";
    document.body.appendChild(t);
    t.select();
    try { document.execCommand("copy"); } catch (e) {}
    document.body.removeChild(t);
    return Promise.resolve();
  }

  function esc(s) {
    return String(s).replace(/[&<>"']/g, function (c) { return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]; });
  }
  function closeSheet() {
    var s = document.querySelector(".lp-sheet");
    if (s) s.remove();
    document.documentElement.style.overflow = "";
  }
  function sheet(html, cls) {
    closeSheet();
    var s = document.createElement("div");
    s.className = "lp-sheet " + (cls || "");
    s.innerHTML = '<div class="lp-sheet__box" role="dialog" aria-modal="true">' + html + "</div>";
    s.addEventListener("click", function (e) { if (e.target === s || e.target.closest("[data-close]")) closeSheet(); });
    document.body.appendChild(s);
    document.documentElement.style.overflow = "hidden";
    return s;
  }
  document.addEventListener("keydown", function (e) { if (e.key === "Escape") closeSheet(); });

  // واي فاي: اسم الشبكة + نسخ الباسورد
  function openWifi(el) {
    var ssid = el.getAttribute("data-wifi-ssid"), pass = el.getAttribute("data-wifi-pass") || "";
    var s = sheet(
      '<button class="lp-sheet__x" data-close aria-label="إغلاق">×</button>' +
      '<div class="lp-wifi__ic">' + (cfg.icons && cfg.icons.wifi || "") + "</div>" +
      "<h3>الواي فاي</h3>" +
      '<div class="lp-wifi__row"><small>اسم الشبكة</small><b dir="ltr">' + esc(ssid) + '</b><button data-c="ssid">نسخ</button></div>' +
      (pass
        ? '<div class="lp-wifi__row"><small>الباسورد</small><b dir="ltr">' + esc(pass) + '</b><button data-c="pass">نسخ</button></div>' +
          '<button class="lp-wifi__main" data-c="pass">نسخ الباسورد</button>'
        : '<p class="lp-wifi__open">الشبكة مفتوحة بدون باسورد</p>') +
      '<p class="lp-wifi__hint">افتح إعدادات الواي فاي في موبايلك واختر الشبكة ثم الصق الباسورد</p>',
      "lp-sheet--wifi"
    );
    s.addEventListener("click", function (e) {
      var b = e.target.closest("[data-c]");
      if (!b) return;
      var v = b.getAttribute("data-c") === "pass" ? pass : ssid;
      copy(v).then(function () { toast(b.getAttribute("data-c") === "pass" ? "تم نسخ الباسورد" : "تم نسخ اسم الشبكة", v); });
    });
  }

  // المنيو PDF: يُعرض داخل الصفحة (أندرويد لا يعرض PDF داخل iframe فنستخدم عارض جوجل)
  function openPdf(el) {
    var url = el.href;
    var src = android ? "https://docs.google.com/viewer?embedded=true&url=" + encodeURIComponent(url) : url + "#toolbar=0&view=FitH";
    sheet(
      '<div class="lp-pdf__bar"><b>' + esc(el.querySelector(".lb__t").textContent) + '</b><span><a href="' + esc(url) + '" target="_blank" rel="noopener">فتح في نافذة</a><a href="' + esc(url) + '" download>تحميل</a><button data-close aria-label="إغلاق">×</button></span></div>' +
      '<iframe class="lp-pdf__frame" src="' + esc(src) + '" title="المنيو"></iframe>',
      "lp-sheet--pdf"
    );
  }

  document.addEventListener("click", function (e) {
    var el = e.target.closest && e.target.closest(".lb");
    if (!el) return;
    send("click", el.getAttribute("data-id"));

    if (el.hasAttribute("data-wifi-ssid")) {
      e.preventDefault();
      openWifi(el);
      return;
    }
    if (el.hasAttribute("data-pdf")) {
      e.preventDefault();
      openPdf(el);
      return;
    }

    var c = el.getAttribute("data-copy");
    if (c !== null) {
      e.preventDefault();
      copy(c).then(function () { toast("تم نسخ " + (el.getAttribute("data-label") || ""), c); });
      return;
    }

    var type = el.getAttribute("data-type");
    var href = el.getAttribute("href") || "";
    if (android && PKG[type] && href.indexOf("https://") === 0) {
      e.preventDefault();
      window.location.href =
        "intent://" + href.slice(8) + "#Intent;scheme=https;package=" + PKG[type] +
        ";S.browser_fallback_url=" + encodeURIComponent(href) + ";end";
    }
  });

  // زيارة واحدة لكل جلسة
  try {
    var k = "alz_v_" + cfg.id;
    if (!sessionStorage.getItem(k)) {
      sessionStorage.setItem(k, "1");
      send("view", cfg.id);
    }
  } catch (e) {
    send("view", cfg.id);
  }
})();
