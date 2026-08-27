/* ServiceLanding — صفوف متكررة وتحديد الكل داخل شاشة التصنيف وصفحة الأدوات */
(function () {
	'use strict';

	document.addEventListener('click', function (e) {
		var t = e.target;

		/* إضافة صف */
		if (t.classList && t.classList.contains('sl-rep-add')) {
			var box = t.closest('[data-sl-rep]');
			var rows = box.querySelector('.sl-rep-rows');
			var max = parseInt(box.getAttribute('data-max'), 10) || 0;
			var count = rows.children.length;
			if (max && count >= max) return;

			var last = rows.children[count - 1];
			if (!last) return;
			var clone = last.cloneNode(true);
			clone.querySelectorAll('input').forEach(function (input) {
				input.value = '';
				input.name = input.name.replace(/\[\d+\]/, '[' + count + ']');
			});
			var handle = clone.querySelector('.sl-rep-handle');
			if (handle) handle.textContent = String(count + 1);
			rows.appendChild(clone);
			var first = clone.querySelector('input');
			if (first) first.focus();
			return;
		}

		/* حذف صف */
		if (t.classList && t.classList.contains('sl-rep-del')) {
			var row = t.closest('.sl-rep-row');
			var host = row.parentNode;
			if (host.children.length <= 1) {
				row.querySelectorAll('input').forEach(function (i) { i.value = ''; });
				return;
			}
			row.remove();
			reindex(host);
			return;
		}

		/* تحديد الكل */
		if (t.classList && t.classList.contains('sl-checkall')) {
			var wrap = t.closest('[data-sl-checkall]');
			var boxes = wrap.querySelectorAll('input[type=checkbox]');
			var allOn = true;
			boxes.forEach(function (b) { if (!b.checked) allOn = false; });
			boxes.forEach(function (b) { b.checked = !allOn; });
		}
	});

	function reindex(host) {
		Array.prototype.forEach.call(host.children, function (row, i) {
			row.querySelectorAll('input').forEach(function (input) {
				input.name = input.name.replace(/\[\d+\]/, '[' + i + ']');
			});
			var h = row.querySelector('.sl-rep-handle');
			if (h) h.textContent = String(i + 1);
		});
	}
})();
