/* الصفحة الرئيسية: المعرض التفاعلي الحي */
(function () {
  var C = window.ALZ_DEMO, R = window.AlzRender;
  if (!C || !R) return;
  var screen = document.getElementById("demoScreen");
  var toastEl = document.getElementById("toast");
  var active = C.demo[0].id, over = {};

  function toast(m) {
    toastEl.textContent = m;
    toastEl.className = "toast show";
    clearTimeout(toastEl._t);
    toastEl._t = setTimeout(function () { toastEl.className = "toast"; }, 2600);
  }
  function biz() { for (var i = 0; i < C.demo.length; i++) if (C.demo[i].id === active) return C.demo[i]; return C.demo[0]; }
  function current() { var p = {}, b = biz().profile, k; for (k in b) p[k] = b[k]; for (k in over) p[k] = over[k]; return p; }

  var bizEl = document.querySelector("[data-demo-biz]");
  bizEl.innerHTML = C.demo.map(function (b) { return '<button data-b="' + b.id + '">' + R.esc(b.tab) + "</button>"; }).join("");

  function render() {
    var p = current();
    screen.innerHTML = R.profile(p, biz().links, C);
    screen.scrollTop = 0;
    document.querySelectorAll("[data-b]").forEach(function (x) { x.classList.toggle("on", x.getAttribute("data-b") === active); });
    document.querySelectorAll("[data-demo-style] [data-v]").forEach(function (x) { x.classList.toggle("on", x.getAttribute("data-v") === p.button_style); });
    document.querySelectorAll("[data-demo-style] [data-layout]").forEach(function (x) { x.classList.toggle("on", x.getAttribute("data-layout") === p.layout); });
    document.querySelectorAll("[data-demo-color] [data-c]").forEach(function (x) { x.classList.toggle("on", x.getAttribute("data-c") === p.theme_primary); });
  }

  bizEl.addEventListener("click", function (e) {
    var b = e.target.closest("[data-b]");
    if (!b) return;
    active = b.getAttribute("data-b");
    over = {};
    render();
  });
  document.querySelector("[data-demo-style]").addEventListener("click", function (e) {
    var b = e.target.closest("button");
    if (!b) return;
    if (b.hasAttribute("data-v")) over.button_style = b.getAttribute("data-v");
    if (b.hasAttribute("data-layout")) over.layout = b.getAttribute("data-layout");
    render();
  });
  document.querySelector("[data-demo-color]").addEventListener("click", function (e) {
    var b = e.target.closest("[data-c]");
    if (!b) return;
    over.theme_primary = b.getAttribute("data-c");
    render();
  });

  // أي ضغطة داخل الموبايلات التجريبية تعرض توضيح بدلاً من فتح الرابط
  document.addEventListener("click", function (e) {
    var lb = e.target.closest(".phone .lb, .phone .lp__brand");
    if (!lb) return;
    e.preventDefault();
    var type = lb.getAttribute("data-demo") || lb.getAttribute("data-type");
    var d = C.defs[type];
    if (lb.getAttribute("data-act") === "wifi" || lb.hasAttribute("data-wifi-ssid")) toast("يظهر اسم الشبكة ويُنسخ الباسورد بضغطة");
    else if (type === "menu") toast("يفتح المنيو PDF داخل الصفحة مباشرة");
    else if (lb.getAttribute("data-act") === "copy" || lb.hasAttribute("data-copy")) toast("يتم نسخ الرقم بضغطة: " + (lb.getAttribute("data-val") || lb.getAttribute("data-copy")));
    else if (d) toast("على الموبايل: يفتح " + d.label + " مباشرة");
  });

  render();
})();
