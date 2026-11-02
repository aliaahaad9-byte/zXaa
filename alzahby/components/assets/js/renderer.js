/* AlzRender — نسخة جافاسكربت من Alz::render_profile() للمعاينة الحية والديمو (نفس الـ markup والكلاسات) */
(function (w) {
  function esc(s) {
    return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }
  var latin = function (s) {
    return String(s || "").replace(/[٠-٩]/g, function (d) { return d.charCodeAt(0) - 0x0660; })
      .replace(/[۰-۹]/g, function (d) { return d.charCodeAt(0) - 0x06f0; });
  };
  var isUrl = function (v) { return /^https?:\/\//i.test(String(v).trim()); };

  /** الواي فاي يُخزَّن JSON: {"s": اسم الشبكة, "p": الباسورد} */
  function wifi(value) {
    try {
      var d = JSON.parse(value || "");
      return d && typeof d.s === "string" ? { ssid: d.s, pass: typeof d.p === "string" ? d.p : "" } : null;
    } catch (e) { return null; }
  }

  function action(type, value) {
    if (type === "wifi") return "wifi";
    if (type === "wallet") return "copy";
    if (type === "instapay" && !isUrl(value)) return "copy";
    return "open";
  }
  function copyValue(type, value) {
    return type === "wallet" ? latin(value).replace(/\D/g, "") : String(value).trim();
  }
  function label(cfg, type, l) {
    l = (l || "").trim();
    return l || cfg.defs[type].label;
  }
  function btnStyle(p, d) {
    var bg = d.gradient || d.color;
    switch (p.button_style) {
      case "filled": return "background:" + p.theme_primary + ";color:#fff";
      case "outline": return "background:transparent;color:" + p.theme_primary + ";border:2px solid " + p.theme_primary;
      case "soft": return "background:" + p.theme_primary + "14;color:" + p.theme_primary;
      default: return "background:" + bg + ";color:" + d.on;
    }
  }

  function profile(p, links, cfg) {
    var solid = p.button_style === "brand" || p.button_style === "filled";
    var initials = (p.business_name || "").trim().slice(0, 2) || "؟";
    var h = '<div class="lp" style="--p:' + esc(p.theme_primary) + ";--bg:" + esc(p.theme_background) + ";--tx:" + esc(p.theme_text) + '">';
    h += '<div class="lp__in"><div class="lp__cover">' + (p.cover_url ? '<img src="' + esc(p.cover_url) + '" alt="">' : "") + "</div>";
    h += '<div class="lp__head"><div class="lp__logo lp__logo--' + esc(p.logo_shape) + '">';
    h += p.logo_url ? '<img src="' + esc(p.logo_url) + '" alt="">' : esc(initials);
    h += '</div><h1 class="lp__name">' + esc(p.business_name) + "</h1>";
    if (p.bio) h += '<p class="lp__bio">' + esc(p.bio).replace(/\n/g, "<br>") + "</p>";
    h += '</div><div class="lp__links lp__links--' + esc(p.layout) + '">';
    var n = 0;
    (links || []).forEach(function (l) {
      var d = cfg.defs[l.type];
      if (!d || !l.is_active || !String(l.value || "").trim()) return;
      var wrap = solid ? "background:rgba(255,255,255,.18)" : "background:" + (d.gradient || d.color) + ";color:" + d.on;
      var inner = '<span class="lb__ic" style="' + wrap + '">' + (cfg.icons[l.type] || cfg.icons.link) + '</span><span class="lb__t">' + esc(label(cfg, l.type, l.label)) + "</span>";
      var a = action(l.type, l.value);
      var val = a === "copy" ? copyValue(l.type, l.value) : l.value;
      if (a === "copy") {
        inner += '<span class="lb__copy" dir="ltr">' + esc(val) + cfg.icons.copy + "</span>";
      } else if (a === "wifi") {
        var w = wifi(l.value);
        if (!w || !w.ssid.trim()) return;
        inner += '<span class="lb__copy" dir="ltr">' + esc(w.ssid) + "</span>";
        val = w.pass;
      }
      h += '<button type="button" class="lb" data-demo="' + esc(l.type) + '" data-act="' + a + '" data-val="' + esc(val) + '" style="' + btnStyle(p, d) + '">' + inner + "</button>";
      n++;
    });
    if (!n) h += '<p class="lp__empty">لا توجد روابط بعد</p>';
    h += '</div></div><span class="lp__brand">صُنع بواسطة <b>' + esc(cfg.brand) + "</b></span></div>";
    return h;
  }

  w.AlzRender = { profile: profile, esc: esc, action: action, copyValue: copyValue, label: label, latin: latin, wifi: wifi };
})(window);
