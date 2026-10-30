/* قائمة الجوال: قفل التمرير، الإغلاق، والأكورديون */
(function () {
	'use strict';
	var nav = document.getElementById('yc-menu');
	if (!nav) { return; }

	var root = document.documentElement;
	var bar = document.querySelector('.menu_bar');
	var closeBtn = nav.querySelector('.menu-nav__close');
	var mq = window.matchMedia('(max-width:1150px)');

	function isOpen() { return nav.classList.contains('open'); }

	function shut() {
		nav.classList.remove('open');
		if (bar) { bar.classList.remove('icon'); }
	}

	/* سكربت القالب يضيف/يزيل الصنف open — نتابعه لقفل تمرير الصفحة ونقل التركيز */
	new MutationObserver(function () {
		var open = isOpen() && mq.matches;
		root.classList.toggle('yc-menu-open', open);
		if (open && closeBtn) {
			try { closeBtn.focus({ preventScroll: true }); } catch (e) { /* متصفح قديم */ }
		}
	}).observe(nav, { attributes: true, attributeFilter: ['class'] });

	if (closeBtn) { closeBtn.addEventListener('click', shut); }

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && isOpen()) { shut(); }
	});

	/* «عرض الكل»: رابط الصفحة الأم داخل القائمة الفرعية حتى لا يضيع (النقر على الأم يفتح/يغلق فقط) */
	Array.prototype.forEach.call(nav.querySelectorAll('.menu-item-has-children'), function (li) {
		var a = li.querySelector(':scope > a');
		var sub = li.querySelector(':scope > .sub-menu');
		if (!a || !sub) { return; }
		a.setAttribute('aria-expanded', 'false');
		var href = a.getAttribute('href') || '';
		if (href && href !== '#' && href.indexOf('javascript:') !== 0) {
			var item = document.createElement('li');
			item.className = 'menu-item yc-viewall';
			var link = document.createElement('a');
			link.href = href;
			link.textContent = 'عرض الكل';
			item.appendChild(link);
			sub.insertBefore(item, sub.firstChild);
		}
	});

	nav.addEventListener('click', function (e) {
		if (!mq.matches) { return; }

		/* أكورديون: النقر على عنصر له قائمة فرعية يفتحها ويغلق إخوته */
		var parentLink = e.target.closest('.menu-item-has-children > a');
		if (parentLink && nav.contains(parentLink)) {
			e.preventDefault();
			e.stopPropagation(); /* يمنع معالج القالب القديم من التبديل مرة ثانية */
			var li = parentLink.parentNode;
			var willOpen = !li.classList.contains('yc-open');
			Array.prototype.forEach.call(li.parentNode.children, function (sib) {
				if (sib !== li && sib.classList.contains('yc-open')) {
					sib.classList.remove('yc-open');
					var sa = sib.querySelector(':scope > a');
					if (sa) { sa.setAttribute('aria-expanded', 'false'); }
				}
			});
			li.classList.toggle('yc-open', willOpen);
			parentLink.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
			return;
		}

		/* رابط داخلي عادي: أغلق القائمة (يفيد عند التنقل بدون إعادة تحميل كاملة) */
		var link = e.target.closest('a[href]');
		if (link && link.host === window.location.host && link.getAttribute('href').charAt(0) !== '#' && link.target !== '_blank') {
			shut();
		}
	});

	/* عند تكبير الشاشة لعرض سطح المكتب: إعادة كل شيء لحالته */
	var onChange = function () {
		if (!mq.matches) {
			shut();
			root.classList.remove('yc-menu-open');
		}
	};
	if (mq.addEventListener) { mq.addEventListener('change', onChange); } else if (mq.addListener) { mq.addListener(onChange); }
})();
