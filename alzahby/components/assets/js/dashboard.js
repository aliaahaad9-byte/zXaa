/* لوحة تحكم العميل — QurTap */
(function () {
  "use strict";
  var C = window.ALZ;
  var R = window.AlzRender;
  var I = C.icons;
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var esc = R.esc;

  /* ---------------- helpers ---------------- */
  var toastEl = $("#toast");
  function toast(msg, type) {
    toastEl.textContent = msg;
    toastEl.className = "toast show" + (type ? " toast--" + type : "");
    clearTimeout(toastEl._t);
    toastEl._t = setTimeout(function () { toastEl.className = "toast"; }, 3200);
  }

  function api(method, path, body) {
    var opts = { method: method, credentials: "same-origin", headers: { "X-WP-Nonce": C.nonce } };
    if (body instanceof FormData) opts.body = body;
    else if (body) {
      opts.body = JSON.stringify(body);
      opts.headers["Content-Type"] = "application/json";
    }
    return fetch(C.api + path, opts).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (j) {
        if (!r.ok) throw new Error((j && (j.error || j.message)) || "حدث خطأ، حاول مرة أخرى");
        return j;
      });
    });
  }

  function slugify(s) {
    return R.latin(s).toLowerCase().trim().replace(/[^a-z0-9\s-]/g, "").replace(/[\s_]+/g, "-").replace(/-+/g, "-").replace(/^-|-$/g, "").slice(0, 40);
  }

  function copyText(t) {
    if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(t);
    var a = document.createElement("textarea");
    a.value = t;
    document.body.appendChild(a);
    a.select();
    try { document.execCommand("copy"); } catch (e) {}
    a.remove();
    return Promise.resolve();
  }

  /* ---------------- Onboarding ---------------- */
  var onboard = $("#onboard");
  if (onboard) {
    var touched = false;
    onboard.business_name.addEventListener("input", function () {
      if (!touched) onboard.slug.value = slugify(onboard.business_name.value);
    });
    onboard.slug.addEventListener("input", function () {
      touched = true;
      onboard.slug.value = slugify(onboard.slug.value);
    });
    onboard.addEventListener("submit", function (e) {
      e.preventDefault();
      var err = $(".alert--error", onboard);
      var btn = $("button[type=submit]", onboard);
      err.hidden = true;
      btn.disabled = true;
      api("POST", "profile", {
        business_name: onboard.business_name.value,
        slug: onboard.slug.value,
        category: onboard.category.value,
        stand: onboard.stand ? onboard.stand.value.trim() : ""
      }).then(function (res) {
        if (res.stand && res.stand !== "claimed") alert("تم إنشاء صفحتك، لكن الاستاند لم يُربط: " + res.stand + "\nيمكنك ربطه من تبويب «QR والاستاند».");
        var q = res.stand ? "?tab=share" + (res.stand === "claimed" ? "&claimed=1" : "") : "";
        if (res.stand === "claimed" && res.stand_kind && res.stand_kind !== "profile") q += "&setup=" + encodeURIComponent(res.stand_kind);
        window.location.href = window.location.pathname + q;
      }).catch(function (ex) {
        err.textContent = ex.message;
        err.hidden = false;
        btn.disabled = false;
      });
    });
    return;
  }

  if (!C.profile) return;

  /* ---------------- State ---------------- */
  var EDITABLE = ["slug", "business_name", "category", "bio", "logo_url", "cover_url", "logo_shape", "theme_primary", "theme_background", "theme_text", "button_style", "layout", "is_published"];
  function pick(p) { var o = {}; EDITABLE.forEach(function (k) { o[k] = p[k]; }); return o; }
  function cloneLinks(ls) { return ls.map(function (l) { return { id: String(l.id), type: l.type, label: l.label || "", value: l.value, is_active: !!l.is_active, click_count: l.click_count || 0 }; }); }

  var saved = { profile: pick(C.profile), links: cloneLinks(C.links) };
  var draft = JSON.parse(JSON.stringify(saved.profile));
  var links = cloneLinks(saved.links);
  var urls = C.urls;
  var openId = null;
  var saving = false;
  var uid = 0;
  var tmpId = function () { return "tmp-" + Date.now().toString(36) + "-" + (++uid); };

  function snapshot(p, ls) {
    return JSON.stringify([p, ls.map(function (l) { return [l.id, l.type, l.label, l.value, l.is_active]; })]);
  }
  function isDirty() { return snapshot(draft, links) !== snapshot(saved.profile, saved.links); }

  /* ---------------- Validation (نسخة مبسطة من Alz::validate_link) ---------------- */
  function validate(l) {
    var v = String(l.value || "").trim();
    var d = C.defs[l.type];
    if (!d) return "نوع غير معروف";
    if (l.type === "wifi") {
      var w = R.wifi(v);
      if (!w || !w.ssid.trim()) return "اسم الشبكة مطلوب";
      if (w.ssid.length > 32) return "اسم الشبكة حتى 32 حرف";
      if (w.pass.length > 63) return "الباسورد حتى 63 حرف";
      return "";
    }
    if (!v) return "القيمة مطلوبة";
    if (/^\s*(javascript|data|vbscript|file):/i.test(v)) return "رابط غير مسموح";
    if (d.input === "phone" && R.latin(v).replace(/\D/g, "").length < 8) return "رقم غير صحيح";
    if (d.input === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return "بريد غير صحيح";
    if (d.input === "url" && /\s/.test(v)) return "رابط غير صحيح";
    return "";
  }

  /* ---------------- Preview ---------------- */
  var pv = $("#preview"), pvm = $("#previewMobile"), raf = 0;
  function renderPreview() {
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(function () {
      var html = R.profile(draft, links, C);
      pv.innerHTML = html;
      if (!$("#previewModal").hidden) pvm.innerHTML = html;
    });
  }
  function previewClick(e) {
    var b = e.target.closest(".lb");
    if (!b) return;
    var t = b.getAttribute("data-demo");
    var d = C.defs[t];
    if (b.getAttribute("data-act") === "copy") toast("سيتم نسخ: " + b.getAttribute("data-val"));
    else if (b.getAttribute("data-act") === "wifi") toast("يظهر اسم الشبكة ويُنسخ الباسورد بضغطة");
    else if (t === "menu" && /\.pdf(\?|$)/i.test(b.getAttribute("data-val"))) toast("يفتح المنيو PDF داخل الصفحة");
    else toast("على الموبايل: يفتح " + (d ? d.label : "") + " مباشرة");
  }
  pv.addEventListener("click", previewClick);
  pvm.addEventListener("click", previewClick);
  $("#previewBtn").addEventListener("click", function () {
    $("#previewModal").hidden = false;
    pvm.innerHTML = R.profile(draft, links, C);
  });
  $("#previewModal").addEventListener("click", function (e) {
    if (e.target.id === "previewModal") e.currentTarget.hidden = true;
  });

  /* ---------------- Status / save bar ---------------- */
  var statusEl = $('[data-bind="status"]'), saveBtn = $("#saveBtn"), undoBtn = $("#undoBtn");
  function refresh() {
    var dirty = isDirty();
    statusEl.textContent = dirty ? "لديك تعديلات غير محفوظة" : "كل التعديلات محفوظة";
    statusEl.classList.toggle("warn", dirty);
    saveBtn.disabled = !dirty || saving;
    undoBtn.hidden = !dirty;
    $('[data-bind="clicks"]').textContent = links.reduce(function (s, l) { return s + (l.click_count || 0); }, 0).toLocaleString("en-US");
    renderPreview();
  }
  window.addEventListener("beforeunload", function (e) {
    if (isDirty()) { e.preventDefault(); e.returnValue = ""; }
  });

  /* ---------------- Tabs ---------------- */
  function setTab(t) {
    $$(".tabs button").forEach(function (b) { b.classList.toggle("on", b.getAttribute("data-tab") === t); });
    $$("[data-panel]").forEach(function (p) { p.hidden = p.getAttribute("data-panel") !== t; });
    if (t === "share") drawQR();
    try { history.replaceState(null, "", "?tab=" + t); } catch (e) {}
  }
  $$(".tabs button").forEach(function (b) {
    b.addEventListener("click", function () { setTab(b.getAttribute("data-tab")); });
  });

  /* =================================================================
   *  Links editor
   * ================================================================= */
  var listEl = $("#links"), pickerEl = $("#picker"), addBtn = $("#addLinkBtn"), emptyAdd = $("#emptyAdd");

  // اختيار منصة
  (function buildPicker() {
    var h = "";
    Object.keys(C.groups).forEach(function (g) {
      h += '<p class="picker__g">' + esc(C.groups[g]) + '</p><div class="picker__grid">';
      Object.keys(C.defs).forEach(function (t) {
        var d = C.defs[t];
        if (d.group !== g) return;
        h += '<button type="button" data-add="' + t + '"><span class="chip-ic" style="background:' + (d.gradient || d.color) + ";color:" + d.on + '">' + I[t] + "</span>" + esc(d.label) + "</button>";
      });
      h += "</div>";
    });
    pickerEl.innerHTML = h;
  })();
  function togglePicker(force) {
    var show = typeof force === "boolean" ? force : pickerEl.hidden;
    pickerEl.hidden = !show;
    addBtn.innerHTML = show ? I.x_close + " إغلاق" : I.plus + " إضافة رابط";
    emptyAdd.hidden = show || links.length > 0;
  }
  addBtn.addEventListener("click", function () { togglePicker(); });
  emptyAdd.addEventListener("click", function () { togglePicker(true); });
  pickerEl.addEventListener("click", function (e) {
    var b = e.target.closest("[data-add]");
    if (!b) return;
    var l = { id: tmpId(), type: b.getAttribute("data-add"), label: "", value: "", is_active: true, click_count: 0 };
    links.push(l);
    openId = l.id;
    togglePicker(false);
    renderLinks();
    refresh();
    var input = $('[data-id="' + l.id + '"] [data-l="value"]');
    if (input) input.focus();
  });

  function typeOptions(sel) {
    return Object.keys(C.defs).map(function (t) {
      return '<option value="' + t + '"' + (t === sel ? " selected" : "") + ">" + esc(C.defs[t].label) + "</option>";
    }).join("");
  }

  /** النص المختصر تحت عنوان الرابط في القائمة */
  function summary(l) {
    if (l.type === "wifi") {
      var w = R.wifi(l.value);
      return w && w.ssid ? "الشبكة: " + w.ssid : "";
    }
    if (l.type === "menu" && /\.pdf(\?|$)/i.test(l.value)) return "ملف PDF: " + decodeURIComponent(l.value.split("/").pop());
    return l.value;
  }
  function isPdf(v) { return /\.pdf(\?|$)/i.test(v || ""); }

  function valueField(l, d, err) {
    var cls = err ? "bad" : "";
    var hint = '<small class="' + (err ? "err" : "muted") + '" data-l="hint">' + esc(err || d.hint || "") + "</small>";
    if (l.type === "wifi") {
      var w = R.wifi(l.value) || { ssid: "", pass: "" };
      return '<div class="grid-2">' +
        '<label class="field"><span>اسم الشبكة (SSID)</span><input data-l="ssid" dir="ltr" maxlength="32" placeholder="Cafe-WiFi" value="' + esc(w.ssid) + '" class="' + cls + '"></label>' +
        '<label class="field"><span>الباسورد</span><input data-l="pass" dir="ltr" maxlength="63" autocomplete="off" placeholder="اتركه فارغاً لو الشبكة مفتوحة" value="' + esc(w.pass) + '"></label>' +
        "</div>" + hint;
    }
    var vlabel = d.input === "phone" ? "الرقم" : d.input === "email" ? "البريد" : "الرابط / المعرّف";
    var mode = d.input === "phone" ? "tel" : d.input === "email" ? "email" : "url";
    var input = '<input data-l="value" dir="ltr" maxlength="500" inputmode="' + mode + '" placeholder="' + esc(d.placeholder) + '" value="' + esc(l.value) + '" class="' + cls + '">';
    if (l.type === "menu") {
      return '<div class="field"><span>المنيو</span>' +
        '<div class="upload-row">' + input + '<button type="button" class="btn btn--dark btn--sm" data-act="pdf">' + I.upload + " رفع PDF</button></div>" +
        (isPdf(l.value) ? '<a class="pdf-chip" href="' + esc(l.value) + '" target="_blank" rel="noopener">' + I.file + " عرض الملف المرفوع</a>" : "") +
        hint + "</div>";
    }
    return '<label class="field"><span>' + vlabel + "</span>" + input + hint + "</label>";
  }

  function linkHTML(l) {
    var d = C.defs[l.type];
    var err = l.value.trim() ? validate(l) : "";
    var open = openId === l.id;
    var h = '<li class="li' + (l.is_active ? "" : " li--off") + (open ? " li--open" : "") + '" data-id="' + esc(l.id) + '">';
    h += '<div class="li__row">';
    h += '<button type="button" class="li__grip" aria-label="سحب لإعادة الترتيب">' + I.grip + "</button>";
    h += '<span class="chip-ic" style="background:' + (d.gradient || d.color) + ";color:" + d.on + '">' + I[l.type] + "</span>";
    h += '<button type="button" class="li__sum" data-act="toggle"><b data-sum="label">' + esc(R.label(C, l.type, l.label)) + '</b><small data-sum="value" dir="auto" class="' + (summary(l) ? "" : "warn") + '">' + esc(summary(l) || "أضف الرابط / الرقم") + "</small></button>";
    if (l.click_count) h += '<span class="badge hide-sm">' + I.click + " " + l.click_count + "</span>";
    h += '<input type="checkbox" class="switch" data-act="active" aria-label="إظهار / إخفاء"' + (l.is_active ? " checked" : "") + ">";
    h += '<button type="button" class="li__chev" data-act="toggle" aria-label="تعديل">' + I.chevron + "</button>";
    h += "</div>";
    if (open) {
      h += '<div class="li__edit"><div class="grid-2">';
      h += '<label class="field"><span>نوع الرابط</span><select data-l="type">' + typeOptions(l.type) + "</select></label>";
      h += '<label class="field"><span>عنوان الزر (اختياري)</span><input data-l="label" maxlength="60" placeholder="' + esc(d.label) + '" value="' + esc(l.label) + '"></label>';
      h += "</div>";
      h += valueField(l, d, err);
      h += '<div class="li__actions"><button type="button" class="btn btn--outline btn--sm" data-act="dup">' + I.copy + ' تكرار</button><button type="button" class="btn btn--outline btn--sm danger" data-act="del">' + I.trash + " حذف</button></div>";
      h += "</div>";
    }
    return h + "</li>";
  }

  /** رفع منيو PDF (حتى 10 ميجا) ووضع رابطه في الزر */
  function uploadPdf(l, btn) {
    var input = document.createElement("input");
    input.type = "file";
    input.accept = "application/pdf,.pdf";
    input.addEventListener("change", function () {
      var file = input.files[0];
      if (!file) return;
      if (!/\.pdf$/i.test(file.name) && file.type !== "application/pdf") return toast("اختر ملف PDF", "err");
      if (file.size > 10 * 1024 * 1024) return toast("الحد الأقصى لملف المنيو 10 ميجا", "err");
      var fd = new FormData();
      fd.append("file", file, "menu.pdf");
      fd.append("kind", "pdf");
      btn.disabled = true;
      btn.innerHTML = I.loader.replace('class="ic"', 'class="ic spin"') + " جارِ الرفع";
      api("POST", "upload", fd).then(function (res) {
        l.value = res.url;
        renderLinks();
        refresh();
        toast("تم رفع المنيو — اضغط حفظ", "ok");
      }).catch(function (ex) {
        toast(ex.message, "err");
        btn.disabled = false;
        btn.innerHTML = I.upload + " رفع PDF";
      });
    });
    input.click();
  }

  function renderLinks() {
    listEl.innerHTML = links.map(linkHTML).join("");
    emptyAdd.hidden = links.length > 0 || !pickerEl.hidden;
  }
  function find(id) { for (var i = 0; i < links.length; i++) if (links[i].id === id) return i; return -1; }

  listEl.addEventListener("click", function (e) {
    var li = e.target.closest(".li");
    if (!li) return;
    var id = li.getAttribute("data-id"), i = find(id);
    var act = e.target.closest("[data-act]");
    act = act && act.getAttribute("data-act");
    if (act === "toggle") {
      openId = openId === id ? null : id;
      renderLinks();
    } else if (act === "dup") {
      var c = JSON.parse(JSON.stringify(links[i]));
      c.id = tmpId();
      c.click_count = 0;
      links.splice(i + 1, 0, c);
      openId = c.id;
      renderLinks();
      refresh();
    } else if (act === "pdf") {
      uploadPdf(links[i], e.target.closest("button"));
    } else if (act === "del") {
      if (!confirm("حذف هذا الرابط؟")) return;
      links.splice(i, 1);
      renderLinks();
      refresh();
    }
  });
  listEl.addEventListener("change", function (e) {
    var li = e.target.closest(".li");
    if (!li) return;
    var l = links[find(li.getAttribute("data-id"))];
    if (e.target.getAttribute("data-act") === "active") {
      l.is_active = e.target.checked;
      li.classList.toggle("li--off", !l.is_active);
      refresh();
    } else if (e.target.getAttribute("data-l") === "type") {
      var wasWifi = l.type === "wifi";
      l.type = e.target.value;
      if (wasWifi !== (l.type === "wifi")) l.value = ""; // صيغة القيمة مختلفة
      renderLinks();
      refresh();
    }
  });
  listEl.addEventListener("input", function (e) {
    var key = e.target.getAttribute("data-l");
    if (["label", "value", "ssid", "pass"].indexOf(key) < 0) return;
    var li = e.target.closest(".li");
    var l = links[find(li.getAttribute("data-id"))];
    if (key === "ssid" || key === "pass") {
      var w = R.wifi(l.value) || { ssid: "", pass: "" };
      w[key] = e.target.value;
      l.value = w.ssid || w.pass ? JSON.stringify({ s: w.ssid, p: w.pass }) : "";
    } else {
      l[key] = e.target.value;
    }
    $('[data-sum="label"]', li).textContent = R.label(C, l.type, l.label);
    var sv = $('[data-sum="value"]', li);
    sv.textContent = summary(l) || "أضف الرابط / الرقم";
    sv.className = summary(l) ? "" : "warn";
    if (key !== "label") {
      var err = l.value.trim() ? validate(l) : "";
      var hint = $('[data-l="hint"]', li);
      hint.textContent = err || C.defs[l.type].hint || "";
      hint.className = err ? "err" : "muted";
      e.target.classList.toggle("bad", !!err);
    }
    refresh();
  });

  /* ---- Drag & Drop (Pointer Events — يعمل باللمس والماوس) + أسهم الكيبورد ---- */
  var drag = null;
  listEl.addEventListener("pointerdown", function (e) {
    var grip = e.target.closest(".li__grip");
    if (!grip) return;
    e.preventDefault();
    var li = grip.closest(".li");
    drag = { li: li, grip: grip, pid: e.pointerId };
    grip.setPointerCapture(e.pointerId);
    li.classList.add("li--drag");
    document.body.classList.add("dragging");
  });
  listEl.addEventListener("pointermove", function (e) {
    if (!drag || e.pointerId !== drag.pid) return;
    var items = $$(".li", listEl).filter(function (x) { return x !== drag.li; });
    for (var k = 0; k < items.length; k++) {
      var r = items[k].getBoundingClientRect();
      if (e.clientY < r.top + r.height / 2) {
        if (items[k].previousElementSibling !== drag.li) listEl.insertBefore(drag.li, items[k]);
        return;
      }
    }
    if (listEl.lastElementChild !== drag.li) listEl.appendChild(drag.li);
  });
  function endDrag() {
    if (!drag) return;
    drag.li.classList.remove("li--drag");
    document.body.classList.remove("dragging");
    var order = $$(".li", listEl).map(function (x) { return x.getAttribute("data-id"); });
    links.sort(function (a, b) { return order.indexOf(a.id) - order.indexOf(b.id); });
    drag = null;
    refresh();
  }
  listEl.addEventListener("pointerup", endDrag);
  listEl.addEventListener("pointercancel", endDrag);
  listEl.addEventListener("keydown", function (e) {
    var grip = e.target.closest(".li__grip");
    if (!grip || (e.key !== "ArrowUp" && e.key !== "ArrowDown")) return;
    e.preventDefault();
    var id = grip.closest(".li").getAttribute("data-id"), i = find(id), j = e.key === "ArrowUp" ? i - 1 : i + 1;
    if (j < 0 || j >= links.length) return;
    var t = links[i]; links[i] = links[j]; links[j] = t;
    renderLinks();
    refresh();
    $('[data-id="' + id + '"] .li__grip').focus();
  });

  /* =================================================================
   *  Identity & Design bindings
   * ================================================================= */
  function syncFields() {
    $$("[data-field]").forEach(function (el) {
      var k = el.getAttribute("data-field");
      if (el.type === "checkbox") el.checked = !!draft[k];
      else el.value = draft[k] == null ? "" : draft[k];
    });
    $$("[data-color]").forEach(function (el) {
      var v = draft[el.getAttribute("data-color")];
      if (/^#[0-9a-f]{6}$/i.test(v)) el.value = v;
    });
    $$("[data-choice]").forEach(function (g) {
      var k = g.getAttribute("data-choice");
      $$("button", g).forEach(function (b) { b.classList.toggle("on", b.getAttribute("data-value") === draft[k]); });
    });
    $('[data-bind="bio_count"]').textContent = (draft.bio || "").length + "/300";
    $$("[data-image]").forEach(function (f) {
      var url = draft[f.getAttribute("data-image")];
      var drop = $(".img-drop", f);
      drop.innerHTML = url ? '<img src="' + esc(url) + '" alt="">' : I.image_plus;
      $(".img-remove", f).hidden = !url;
    });
    drop2Shape();
    $$("#themes button").forEach(function (b) {
      b.classList.toggle("on", b.getAttribute("data-p") === draft.theme_primary && b.getAttribute("data-b") === draft.theme_background);
    });
  }
  function drop2Shape() {
    var d = $(".img-drop--logo");
    d.classList.toggle("img-drop--circle", draft.logo_shape === "circle");
  }

  $$("[data-field]").forEach(function (el) {
    el.addEventListener("input", function () {
      var k = el.getAttribute("data-field");
      if (el.type === "checkbox") draft[k] = el.checked;
      else if (k === "slug") { el.value = slugify(el.value); draft[k] = el.value; }
      else draft[k] = el.value;
      if (k === "bio") $('[data-bind="bio_count"]').textContent = el.value.length + "/300";
      if (/^theme_/.test(k) && /^#[0-9a-f]{6}$/i.test(el.value)) $('[data-color="' + k + '"]').value = el.value;
      refresh();
    });
    if (el.type === "checkbox" || el.tagName === "SELECT") el.addEventListener("change", function () { el.dispatchEvent(new Event("input")); });
  });
  $$("[data-color]").forEach(function (el) {
    el.addEventListener("input", function () {
      var k = el.getAttribute("data-color");
      draft[k] = el.value.toLowerCase();
      $('[data-field="' + k + '"]').value = draft[k];
      syncFields();
      refresh();
    });
  });
  $$("[data-choice]").forEach(function (g) {
    g.addEventListener("click", function (e) {
      var b = e.target.closest("button");
      if (!b) return;
      draft[g.getAttribute("data-choice")] = b.getAttribute("data-value");
      syncFields();
      refresh();
    });
  });

  var THEMES = [
    ["كلاسيك", "#0f172a", "#f8fafc", "#0f172a"], ["ذهبي", "#b5841a", "#fffbeb", "#2b1d05"],
    ["طبي", "#0e7490", "#f0fdfa", "#083344"], ["مطاعم", "#b91c1c", "#fff7ed", "#431407"],
    ["طبيعي", "#15803d", "#f7fee7", "#14532d"], ["بنفسجي", "#7c3aed", "#faf5ff", "#2e1065"],
    ["ليلي", "#eec04a", "#0b1020", "#f8fafc"]
  ];
  $("#themes").innerHTML = THEMES.map(function (t) {
    return '<button type="button" data-p="' + t[1] + '" data-b="' + t[2] + '" data-t="' + t[3] + '"><span class="theme__sw" style="background:' + t[2] + '"><i style="background:' + t[1] + '"></i><em style="background:' + t[3] + '"></em></span>' + t[0] + "</button>";
  }).join("");
  $("#themes").addEventListener("click", function (e) {
    var b = e.target.closest("button");
    if (!b) return;
    draft.theme_primary = b.getAttribute("data-p");
    draft.theme_background = b.getAttribute("data-b");
    draft.theme_text = b.getAttribute("data-t");
    syncFields();
    refresh();
  });

  /* ---- رفع الصور: تصغير في المتصفح + WebP ثم رفع ---- */
  function resize(file, max) {
    return new Promise(function (resolve, reject) {
      var img = new Image();
      img.onload = function () {
        var s = Math.min(1, max / Math.max(img.width, img.height));
        var c = document.createElement("canvas");
        c.width = Math.round(img.width * s);
        c.height = Math.round(img.height * s);
        c.getContext("2d").drawImage(img, 0, 0, c.width, c.height);
        URL.revokeObjectURL(img.src);
        c.toBlob(function (b) { b ? resolve(b) : reject(new Error("تعذر معالجة الصورة")); }, "image/webp", 0.86);
      };
      img.onerror = function () { reject(new Error("ملف صورة غير صالح")); };
      img.src = URL.createObjectURL(file);
    });
  }
  $$("[data-image]").forEach(function (f) {
    var key = f.getAttribute("data-image"), kind = f.getAttribute("data-kind");
    var input = document.createElement("input");
    input.type = "file";
    input.accept = "image/png,image/jpeg,image/webp";
    input.hidden = true;
    f.appendChild(input);
    $(".img-drop", f).addEventListener("click", function () { input.click(); });
    $(".img-remove", f).addEventListener("click", function () { draft[key] = null; syncFields(); refresh(); });
    input.addEventListener("change", function () {
      var file = input.files[0];
      input.value = "";
      if (!file) return;
      if (!/^image\/(png|jpe?g|webp)$/.test(file.type)) return toast("الصيغ المسموحة: PNG / JPG / WEBP", "err");
      var drop = $(".img-drop", f);
      drop.classList.add("busy");
      resize(file, kind === "logo" ? 512 : 1400).then(function (blob) {
        var fd = new FormData();
        fd.append("file", blob, kind + ".webp");
        fd.append("kind", kind);
        return api("POST", "upload", fd);
      }).then(function (res) {
        draft[key] = res.url;
        syncFields();
        refresh();
      }).catch(function (ex) { toast(ex.message, "err"); })
        .then(function () { drop.classList.remove("busy"); });
    });
  });

  /* =================================================================
   *  Share: روابط + QR + استاندات
   * ================================================================= */
  var qrTarget = "permanent";
  function bindUrls() {
    $('[data-bind="permanent"]').value = urls.permanent;
    $('[data-bind="pretty"]').value = urls.pretty;
    var a = $('[data-bind="pretty_link"]');
    a.href = urls.pretty;
    a.textContent = urls.pretty.replace(/^https?:\/\//, "").replace(/\/$/, "");
    $('[data-bind="pretty_open"]').href = urls.pretty;
    $('[data-bind="saved_name"]').textContent = saved.profile.business_name;
  }
  $$("[data-copy-from]").forEach(function (b) {
    b.addEventListener("click", function () {
      copyText(urls[b.getAttribute("data-copy-from")]).then(function () {
        toast("تم نسخ الرابط", "ok");
        b.classList.add("done");
        setTimeout(function () { b.classList.remove("done"); }, 1200);
      });
    });
  });
  var shareBtn = $("#shareBtn");
  if (!navigator.share) shareBtn.hidden = true;
  shareBtn.addEventListener("click", function () {
    navigator.share({ title: saved.profile.business_name, url: urls.pretty }).catch(function () {});
  });

  function qrMatrix(text) {
    var q = qrcode(0, "H");
    q.addData(unescape(encodeURIComponent(text)));
    q.make();
    return q;
  }
  function qrColor() { return $("#qrColor").checked ? draft.theme_primary : "#000000"; }
  function paint(canvas, size) {
    var q = qrMatrix(urls[qrTarget]), n = q.getModuleCount(), m = 4;
    var cell = size / (n + m * 2);
    var ctx = canvas.getContext("2d");
    canvas.width = canvas.height = size;
    ctx.fillStyle = "#fff";
    ctx.fillRect(0, 0, size, size);
    ctx.fillStyle = qrColor();
    for (var r = 0; r < n; r++) for (var c = 0; c < n; c++) {
      if (q.isDark(r, c)) ctx.fillRect(Math.floor((c + m) * cell), Math.floor((r + m) * cell), Math.ceil(cell), Math.ceil(cell));
    }
  }
  function drawQR() { paint($("#qrCanvas"), 480); }
  $$("[data-qr-target] button").forEach(function (b) {
    b.addEventListener("click", function () {
      qrTarget = b.getAttribute("data-value");
      $$("[data-qr-target] button").forEach(function (x) { x.classList.toggle("on", x === b); });
      drawQR();
    });
  });
  $("#qrColor").addEventListener("change", drawQR);
  function download(href, name) {
    var a = document.createElement("a");
    a.href = href;
    a.download = name;
    document.body.appendChild(a);
    a.click();
    a.remove();
  }
  $$("[data-qr-download]").forEach(function (b) {
    b.addEventListener("click", function () {
      var kind = b.getAttribute("data-qr-download"), name = "qr-" + saved.profile.slug + "." + kind;
      if (kind === "png") {
        var c = document.createElement("canvas");
        paint(c, 2048);
        download(c.toDataURL("image/png"), name);
      } else {
        var q = qrMatrix(urls[qrTarget]), n = q.getModuleCount(), m = 4, d = "";
        for (var r = 0; r < n; r++) for (var cc = 0; cc < n; cc++) if (q.isDark(r, cc)) d += "M" + (cc + m) + " " + (r + m) + "h1v1h-1z";
        var size = n + m * 2;
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' + size + " " + size + '" shape-rendering="crispEdges"><rect width="100%" height="100%" fill="#fff"/><path fill="' + qrColor() + '" d="' + d + '"/></svg>';
        var url = URL.createObjectURL(new Blob([svg], { type: "image/svg+xml" }));
        download(url, name);
        setTimeout(function () { URL.revokeObjectURL(url); }, 1500);
      }
    });
  });

  var standsEl = $("#stands");
  var KINDS = C.kinds || {};
  function kindBadge(k) {
    var d = KINDS[k] || KINDS.profile;
    if (!d) return "";
    return '<span class="kind" style="--kc:' + esc(d.color) + '">' + (I[d.icon] || "") + esc(d.short) + "</span>";
  }
  // عدد الاستاندات المربوطة من كل نوع بجانب حقل رابطه
  function countKinds(list) {
    $$("[data-target]").forEach(function (box) {
      var k = box.getAttribute("data-target");
      var mine = list.filter(function (s) { return s.kind === k; });
      $("[data-count]", box).textContent = mine.length ? mine.length + " استاند مربوط" : "لا يوجد استاند مربوط";
      $("[data-stands]", box).innerHTML = mine.map(function (s) {
        return '<li><b dir="ltr">' + esc(s.code) + '</b><small class="muted">' + Number(s.scan_count || 0).toLocaleString("en-US") + ' مسح</small><button type="button" class="btn btn--ghost btn--sm danger" data-release="' + esc(s.code) + '">' + I.unlink + " فك الربط</button></li>";
      }).join("");
    });
  }
  function renderStands(list) {
    countKinds(list);
    standsEl.innerHTML = list.length ? list.map(function (s) {
      return '<li><div><b dir="ltr">' + esc(s.code) + kindBadge(s.kind || "profile") + "</b><small class=\"muted\">" + Number(s.scan_count || 0).toLocaleString("en-US") + ' مسح / لمسة</small></div><button type="button" class="btn btn--ghost btn--sm danger" data-release="' + esc(s.code) + '">' + I.unlink + " فك الربط</button></li>";
    }).join("") : '<li class="muted center">لا توجد استاندات مربوطة بعد</li>';
  }
  $("#claimForm").addEventListener("submit", function (e) {
    e.preventDefault();
    var f = e.currentTarget, btn = $("button", f);
    btn.disabled = true;
    api("POST", "stands/claim", { code: f.code.value.trim() }).then(function (res) {
      renderStands(res.stands);
      f.reset();
      var d = KINDS[res.kind];
      if (d && res.kind !== "profile") {
        var inp = $('#targetsForm [name="' + res.kind + '"]');
        if (inp && !inp.value.trim()) {
          focusTarget(res.kind);
          return toast("تم ربط " + d.label + " — أضف رابطك الآن", "ok");
        }
      }
      toast("تم ربط الاستاند", "ok");
    }).catch(function (ex) { toast(ex.message, "err"); }).then(function () { btn.disabled = false; });
  });
  function releaseClick(e) {
    var b = e.target.closest("[data-release]");
    if (!b || !confirm("فك ربط هذا الاستاند؟ لن يفتح صفحتك بعد ذلك.")) return;
    api("POST", "stands/release", { code: b.getAttribute("data-release") }).then(function (res) {
      renderStands(res.stands);
      toast("تم فك ربط الاستاند", "ok");
    }).catch(function (ex) { toast(ex.message, "err"); });
  }
  standsEl.addEventListener("click", releaseClick);

  /* ---------------- روابط استاندات الدفع والتقييم ---------------- */
  var tForm = $("#targetsForm");
  function focusTarget(k) {
    $$("[data-target]").forEach(function (b) { b.classList.toggle("focus", b.getAttribute("data-target") === k); });
    var inp = $('[name="' + k + '"]', tForm);
    if (inp) {
      inp.scrollIntoView({ behavior: "smooth", block: "center" });
      setTimeout(function () { inp.focus(); }, 350);
    }
  }
  function fillTargets(t) {
    if (!t) return;
    ["instapay", "vcash", "greview"].forEach(function (k) { tForm[k].value = t[k] || ""; });
    var box = $("[data-review]", tForm);
    if (!t.greview) { box.hidden = true; return; }
    box.hidden = false;
    box.classList.toggle("target__warn", !t.review_ready);
    box.innerHTML = t.review_ready
      ? I.check + ' يفتح قسم المراجعات مباشرة — <a href="' + esc(t.review_url) + '" target="_blank" rel="noopener">جرّب الرابط</a>'
      : "⚠ لم نتمكن من الوصول لقسم المراجعات بعد — الاستاند يفتح نشاطك على الخرائط وسنعيد المحاولة تلقائياً";
  }
  var tSaved = C.targets || {};
  function tDirty() {
    return ["instapay", "vcash", "greview"].some(function (k) { return tForm[k].value.trim() !== (tSaved[k] || ""); });
  }
  window.addEventListener("beforeunload", function (e) {
    if (tDirty()) { e.preventDefault(); e.returnValue = ""; }
  });
  // ربط استاند من خانة المنصة بالكود المكتوب تحت الـ QR
  function claimIn(box) {
    var k = box.getAttribute("data-target"), inp = $("[data-code]", box), code = inp.value.trim();
    if (!code) return Promise.resolve(false);
    if (!/^[A-Za-z0-9]{6,16}$/.test(code)) return Promise.reject(new Error("كود الاستاند غير صحيح (" + KINDS[k].short + ")"));
    return api("POST", "stands/claim", { code: code, kind: k }).then(function (res) {
      inp.value = "";
      renderStands(res.stands);
      return true;
    });
  }
  tForm.addEventListener("click", function (e) {
    releaseClick(e);
    var b = e.target.closest("[data-claim]");
    if (!b) return;
    var box = b.closest("[data-target]"), k = box.getAttribute("data-target");
    if (!$("[data-code]", box).value.trim()) { $("[data-code]", box).focus(); return toast("اكتب الكود المطبوع تحت الـ QR", "err"); }
    b.disabled = true;
    claimIn(box).then(function () {
      var v = tForm[k].value.trim();
      toast(v && v === (tSaved[k] || "") ? "تم ربط " + KINDS[k].label + " — يفتح رابطك الآن" : "تم ربط " + KINDS[k].label + " — أضف رابطك واحفظ", "ok");
      if (!v) tForm[k].focus();
    }).catch(function (ex) { toast(ex.message, "err"); }).then(function () { b.disabled = false; });
  });
  tForm.addEventListener("keydown", function (e) {
    if (e.key === "Enter" && e.target.hasAttribute("data-code")) {
      e.preventDefault();
      $("[data-claim]", e.target.closest("[data-target]")).click();
    }
  });
  tForm.addEventListener("submit", function (e) {
    e.preventDefault();
    var btn = $("button[type=submit]", tForm);
    btn.disabled = true;
    btn.innerHTML = I.loader.replace('class="ic"', 'class="ic spin"') + " جارِ الحفظ";
    var claimed = 0;
    // أي كود مكتوب في الخانات يتم ربطه مع الحفظ
    $$("[data-target]", tForm).reduce(function (p, box) {
      return p.then(function () { return claimIn(box).then(function (ok) { if (ok) claimed++; }); });
    }, Promise.resolve()).then(function () {
      return api("PUT", "targets", {
      instapay: tForm.instapay.value.trim(),
      vcash: tForm.vcash.value.trim(),
      greview: tForm.greview.value.trim()
      });
    }).then(function (res) {
      tSaved = res.targets;
      fillTargets(res.targets);
      $$("[data-target]").forEach(function (b) { b.classList.remove("focus"); });
      toast(res.notice || (claimed ? "تم ربط " + claimed + " استاند وحفظ الروابط" : "تم حفظ الروابط — الاستاندات تفتحها الآن"), res.notice ? "err" : "ok");
    }).catch(function (ex) { toast(ex.message, "err"); }).then(function () {
      btn.disabled = false;
      btn.innerHTML = I.save + " حفظ الروابط";
    });
  });

  /* =================================================================
   *  Save / Undo
   * ================================================================= */
  undoBtn.addEventListener("click", function () {
    draft = JSON.parse(JSON.stringify(saved.profile));
    links = cloneLinks(saved.links);
    openId = null;
    syncFields();
    renderLinks();
    refresh();
  });

  saveBtn.addEventListener("click", function () {
    if (!draft.business_name.trim()) return toast("اسم النشاط مطلوب", "err");
    if (!/^[a-z0-9](?:[a-z0-9-]{1,38})[a-z0-9]$/.test(draft.slug)) { setTab("identity"); return toast("رابط الصفحة غير صحيح", "err"); }
    for (var i = 0; i < links.length; i++) {
      var err = validate(links[i]);
      if (err) {
        setTab("links");
        openId = links[i].id;
        renderLinks();
        return toast("راجع رابط «" + R.label(C, links[i].type, links[i].label) + "»: " + err, "err");
      }
    }
    saving = true;
    saveBtn.innerHTML = I.loader.replace('class="ic"', 'class="ic spin"') + " جارِ الحفظ";
    refresh();
    api("PUT", "profile", {
      profile: draft,
      links: links.map(function (l) { return { id: l.id, type: l.type, label: l.label, value: l.value, is_active: l.is_active }; })
    }).then(function (res) {
      var map = res.idmap || {};
      if (openId && map[openId]) openId = String(map[openId]);
      saved = { profile: pick(res.profile), links: cloneLinks(res.links) };
      draft = JSON.parse(JSON.stringify(saved.profile));
      links = cloneLinks(res.links);
      urls = { permanent: C.home + "u/" + res.profile.id + "/", pretty: C.home + res.profile.slug + "/" };
      bindUrls();
      syncFields();
      renderLinks();
      toast("تم الحفظ — التعديلات ظاهرة الآن على الاستاند", "ok");
    }).catch(function (ex) { toast(ex.message, "err"); }).then(function () {
      saving = false;
      saveBtn.innerHTML = I.save + " حفظ";
      refresh();
    });
  });

  // اختصار الحفظ Ctrl+S
  document.addEventListener("keydown", function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "s") {
      e.preventDefault();
      if (!saveBtn.disabled) saveBtn.click();
    }
  });

  /* ---------------- Init ---------------- */
  bindUrls();
  syncFields();
  renderLinks();
  fillTargets(C.targets);
  renderStands(C.stands || []);
  setTab(C.tab || "links");
  if (C.setup) setTimeout(function () { focusTarget(C.setup); }, 300);
  refresh();
  if (C.notice) toast(C.notice, "ok");
})();
