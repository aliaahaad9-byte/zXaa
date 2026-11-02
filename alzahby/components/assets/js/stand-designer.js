/* مولّد تصاميم الاستاندات: يركّب QR فريد لكل كود على صورة القالب ويصدّرها PDF أو ZIP — كله في المتصفح */
(function () {
  "use strict";
  var C = window.ALZD, D = C.design;
  var $ = function (id) { return document.getElementById(id); };
  var canvas = $("alzCanvas"), stage = $("alzStage"), box = $("alzBox");
  var img = new Image(), ready = false;

  /* ---------------- رسم تصميم واحد ---------------- */
  function num(k) { return parseFloat(D[k]) || 0; }

  function render(cv, code, scale) {
    var W = Math.round(img.naturalWidth * scale), H = Math.round(img.naturalHeight * scale);
    cv.width = W;
    cv.height = H;
    var ctx = cv.getContext("2d");
    ctx.imageSmoothingQuality = "high";
    ctx.drawImage(img, 0, 0, W, H);

    var bx = num("x") / 100 * W, by = num("y") / 100 * H, bw = num("w") / 100 * W, bh = num("h") / 100 * H;
    var showCode = !!(+D.code);
    var textH = showCode ? Math.max(12, Math.min(bw, bh) * 0.075) : 0;
    var gap = showCode ? textH * 0.6 : 0;
    var avail = Math.min(bw, bh - textH - gap) * (num("qr") / 100);

    var q = qrcode(0, "M");
    q.addData(C.base + code);
    q.make();
    var n = q.getModuleCount() + 4; // 2 وحدات هامش من كل جهة
    var cell = Math.max(1, Math.floor(avail / n));
    var size = cell * n;
    var qx = Math.round(bx + (bw - size) / 2);
    var qy = Math.round(by + (bh - size - gap - textH) / 2);

    ctx.fillStyle = "#fff";
    ctx.fillRect(qx, qy, size, size);
    ctx.fillStyle = D.color || "#000";
    var m = q.getModuleCount();
    for (var r = 0; r < m; r++) {
      for (var c = 0; c < m; c++) {
        if (q.isDark(r, c)) ctx.fillRect(qx + (c + 2) * cell, qy + (r + 2) * cell, cell, cell);
      }
    }

    if (showCode) {
      ctx.fillStyle = D.color || "#000";
      ctx.textAlign = "center";
      ctx.textBaseline = "top";
      ctx.direction = "rtl";
      ctx.font = "800 " + Math.round(textH) + "px Tahoma, Arial, sans-serif";
      ctx.fillText("كود التفعيل: " + code, bx + bw / 2, qy + size + gap);
    }
  }

  /* ---------------- المعاينة + سحب المربع ---------------- */
  function preview() {
    if (!ready) return;
    render(canvas, "DEMO1234", 1);
    placeBox();
  }
  function placeBox() {
    box.style.left = num("x") + "%";
    box.style.top = num("y") + "%";
    box.style.width = num("w") + "%";
    box.style.height = num("h") + "%";
  }
  function syncInputs() {
    document.querySelectorAll("#alzDesignForm [data-k]").forEach(function (el) {
      var k = el.getAttribute("data-k");
      if (el.type === "checkbox") el.checked = !!(+D[k]);
      else el.value = typeof D[k] === "number" ? Math.round(D[k] * 10) / 10 : D[k];
    });
  }
  document.querySelectorAll("#alzDesignForm [data-k]").forEach(function (el) {
    el.addEventListener("input", function () {
      var k = el.getAttribute("data-k");
      D[k] = el.type === "checkbox" ? (el.checked ? 1 : 0) : el.type === "color" ? el.value : parseFloat(el.value) || 0;
      preview();
    });
    if (el.type === "checkbox") el.addEventListener("change", function () { el.dispatchEvent(new Event("input")); });
  });

  var drag = null;
  box.addEventListener("pointerdown", function (e) {
    e.preventDefault();
    var r = stage.getBoundingClientRect();
    drag = { resize: e.target.classList.contains("alzd__handle"), sx: e.clientX, sy: e.clientY, r: r, x: num("x"), y: num("y"), w: num("w"), h: num("h") };
    box.setPointerCapture(e.pointerId);
  });
  box.addEventListener("pointermove", function (e) {
    if (!drag) return;
    var dx = (e.clientX - drag.sx) / drag.r.width * 100, dy = (e.clientY - drag.sy) / drag.r.height * 100;
    if (drag.resize) {
      D.w = Math.max(5, Math.min(100 - drag.x, drag.w + dx));
      D.h = Math.max(5, Math.min(100 - drag.y, drag.h + dy));
    } else {
      D.x = Math.max(0, Math.min(100 - drag.w, drag.x + dx));
      D.y = Math.max(0, Math.min(100 - drag.h, drag.y + dy));
    }
    placeBox();
  });
  function endDrag() {
    if (!drag) return;
    drag = null;
    syncInputs();
    preview();
  }
  box.addEventListener("pointerup", endDrag);
  box.addEventListener("pointercancel", endDrag);

  function loadTemplate(url) {
    ready = false;
    img = new Image();
    img.crossOrigin = "anonymous";
    img.onload = function () { ready = true; preview(); };
    img.onerror = function () { alert("تعذر تحميل صورة القالب — تأكد أن الرابط صحيح ومن نفس الموقع"); };
    img.src = url;
  }
  $("alzTemplate").addEventListener("change", function () { D.template = this.value; loadTemplate(this.value); });
  $("alzPickTemplate").addEventListener("click", function () {
    var f = wp.media({ title: "اختر صورة القالب", multiple: false, library: { type: "image" } });
    f.on("select", function () {
      var url = f.state().get("selection").first().toJSON().url;
      $("alzTemplate").value = url;
      D.template = url;
      loadTemplate(url);
    });
    f.open();
  });

  /* ---------------- ZIP (تخزين بدون ضغط — PNG مضغوطة أصلاً) ---------------- */
  var CRC = (function () {
    var t = new Uint32Array(256);
    for (var n = 0; n < 256; n++) { var c = n; for (var k = 0; k < 8; k++) c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1; t[n] = c >>> 0; }
    return t;
  })();
  function crc32(u8) { var c = 0xffffffff; for (var i = 0; i < u8.length; i++) c = CRC[(c ^ u8[i]) & 0xff] ^ (c >>> 8); return (c ^ 0xffffffff) >>> 0; }
  function makeZip(files) {
    var enc = new TextEncoder(), parts = [], central = [], offset = 0;
    files.forEach(function (f) {
      var name = enc.encode(f.name), data = f.data, crc = crc32(data);
      var lh = new DataView(new ArrayBuffer(30));
      lh.setUint32(0, 0x04034b50, true); lh.setUint16(4, 20, true); lh.setUint16(6, 0x0800, true);
      lh.setUint32(14, crc, true); lh.setUint32(18, data.length, true); lh.setUint32(22, data.length, true);
      lh.setUint16(26, name.length, true);
      parts.push(lh.buffer, name, data);
      var ch = new DataView(new ArrayBuffer(46));
      ch.setUint32(0, 0x02014b50, true); ch.setUint16(4, 20, true); ch.setUint16(6, 20, true); ch.setUint16(8, 0x0800, true);
      ch.setUint32(16, crc, true); ch.setUint32(20, data.length, true); ch.setUint32(24, data.length, true);
      ch.setUint16(28, name.length, true); ch.setUint32(42, offset, true);
      central.push(ch.buffer, name);
      offset += 30 + name.length + data.length;
    });
    var cdSize = central.reduce(function (s, p) { return s + p.byteLength; }, 0);
    var end = new DataView(new ArrayBuffer(22));
    end.setUint32(0, 0x06054b50, true); end.setUint16(8, files.length, true); end.setUint16(10, files.length, true);
    end.setUint32(12, cdSize, true); end.setUint32(16, offset, true);
    return new Blob(parts.concat(central, [end.buffer]), { type: "application/zip" });
  }

  /* ---------------- PDF (صفحة لكل استاند، صورة JPEG عالية الجودة) ---------------- */
  function makePdf(pages) {
    var enc = new TextEncoder(), chunks = [], len = 0, offsets = [];
    function add(x) { var b = typeof x === "string" ? enc.encode(x) : x; chunks.push(b); len += b.length; }
    function obj(id, body, stream) {
      offsets[id] = len;
      add(id + " 0 obj\n" + body);
      if (stream) { add("\nstream\n"); add(stream); add("\nendstream"); }
      add("\nendobj\n");
    }
    add("%PDF-1.4\n%\xE2\xE3\xCF\xD3\n");
    var kids = [], id = 3;
    pages.forEach(function () { kids.push(id + " 0 R"); id += 3; });
    obj(1, "<< /Type /Catalog /Pages 2 0 R >>");
    obj(2, "<< /Type /Pages /Count " + pages.length + " /Kids [" + kids.join(" ") + "] >>");
    id = 3;
    pages.forEach(function (p) {
      var pw = (p.w * 0.75 / p.scale).toFixed(2), ph = (p.h * 0.75 / p.scale).toFixed(2);
      var content = "q " + pw + " 0 0 " + ph + " 0 0 cm /Im0 Do Q";
      obj(id, "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 " + pw + " " + ph + "] /Resources << /XObject << /Im0 " + (id + 2) + " 0 R >> >> /Contents " + (id + 1) + " 0 R >>");
      obj(id + 1, "<< /Length " + content.length + " >>", enc.encode(content));
      obj(id + 2, "<< /Type /XObject /Subtype /Image /Width " + p.w + " /Height " + p.h + " /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " + p.jpg.length + " >>", p.jpg);
      id += 3;
    });
    var xref = len;
    var x = "xref\n0 " + id + "\n0000000000 65535 f \n";
    for (var i = 1; i < id; i++) x += String(offsets[i]).padStart(10, "0") + " 00000 n \n";
    add(x + "trailer\n<< /Size " + id + " /Root 1 0 R >>\nstartxref\n" + xref + "\n%%EOF");
    return new Blob(chunks, { type: "application/pdf" });
  }

  /* ---------------- التصدير ---------------- */
  var prog = $("alzProgress");
  function progress(i, n, label) {
    prog.hidden = false;
    prog.querySelector("span").style.width = (n ? i / n * 100 : 0) + "%";
    prog.querySelector("b").textContent = label || (i + " / " + n);
  }
  function toBytes(cv, type, q) {
    return new Promise(function (res) {
      cv.toBlob(function (b) { b.arrayBuffer().then(function (a) { res(new Uint8Array(a)); }); }, type, q);
    });
  }
  function download(blob, name) {
    var a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = name;
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(function () { URL.revokeObjectURL(a.href); }, 4000);
  }

  async function exportCodes(codes, batch) {
    if (!ready) return alert("انتظر تحميل صورة القالب");
    if (!codes.length) return alert("لا توجد أكواد في هذه الدفعة");
    var format = $("alzFormat").value, scale = parseFloat($("alzScale").value) || 1;
    var cv = document.createElement("canvas"), out = [];
    for (var i = 0; i < codes.length; i++) {
      render(cv, codes[i], scale);
      if (format === "pdf") out.push({ w: cv.width, h: cv.height, scale: scale, jpg: await toBytes(cv, "image/jpeg", 0.95) });
      else out.push({ name: batch + "/" + String(i + 1).padStart(3, "0") + "-" + codes[i] + ".png", data: await toBytes(cv, "image/png") });
      progress(i + 1, codes.length);
      await new Promise(function (r) { setTimeout(r, 0); });
    }
    progress(codes.length, codes.length, "جارِ تجهيز الملف…");
    if (format === "pdf") {
      download(makePdf(out), "stands-" + batch + ".pdf");
    } else {
      var csv = "﻿code,print_url\n" + codes.map(function (c) { return c + "," + C.base + c; }).join("\n");
      out.push({ name: batch + "/codes.csv", data: new TextEncoder().encode(csv) });
      download(makeZip(out), "stands-" + batch + ".zip");
    }
    progress(codes.length, codes.length, "تم ✓ " + codes.length + " استاند");
  }

  function api(method, path, body) {
    return fetch(C.api + path, {
      method: method,
      credentials: "same-origin",
      headers: { "X-WP-Nonce": C.nonce, "Content-Type": "application/json" },
      body: body ? JSON.stringify(body) : undefined
    }).then(function (r) {
      return r.json().then(function (j) { if (!r.ok) throw new Error(j.message || "حدث خطأ"); return j; });
    });
  }

  $("alzCount").addEventListener("change", function () { $("alzCountCustom").hidden = this.value !== "custom"; });

  $("alzGenerate").addEventListener("click", function () {
    var btn = this;
    var count = $("alzCount").value === "custom" ? parseInt($("alzCountCustom").value, 10) : parseInt($("alzCount").value, 10);
    if (!(count > 0 && count <= 500)) return alert("العدد من 1 إلى 500");
    if (!confirm("سيتم إنشاء " + count + " كود جديد وحفظها في قاعدة البيانات. متابعة؟")) return;
    btn.disabled = true;
    progress(0, count, "جارِ توليد الأكواد…");
    api("POST", "generate", { count: count, batch: $("alzBatch").value })
      .then(function (res) { return exportCodes(res.codes, res.batch); })
      .catch(function (e) { alert(e.message); prog.hidden = true; })
      .then(function () { btn.disabled = false; });
  });

  var re = $("alzRedownload");
  if (re) re.addEventListener("click", function () {
    var b = $("alzOldBatch").value, only = $("alzOnlyFree").checked ? "&only=free" : "";
    re.disabled = true;
    api("GET", "batch?batch=" + encodeURIComponent(b) + only)
      .then(function (res) { return exportCodes(res.codes, res.batch); })
      .catch(function (e) { alert(e.message); })
      .then(function () { re.disabled = false; });
  });

  syncInputs();
  loadTemplate(D.template);
})();
