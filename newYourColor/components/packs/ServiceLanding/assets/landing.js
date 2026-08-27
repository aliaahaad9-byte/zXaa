/* ServiceLanding — تفاعلات الواجهة: الأسئلة + إرسال الطلب عبر نظام القالب */
(function () {
	'use strict';
	var page = document.querySelector('.sl-page');
	if (!page) return;

	/* الأسئلة الشائعة */
	page.addEventListener('click', function (e) {
		var q = e.target.closest ? e.target.closest('.sl-faq-q') : null;
		if (!q || !page.contains(q)) return;
		var item = q.parentNode;
		var open = item.classList.toggle('is-open');
		q.setAttribute('aria-expanded', open ? 'true' : 'false');
	});

	/* إرسال الطلب عبر نفس نقطة استقبال الطلبات في القالب */
	var endpoint = (window.HomeURL || location.origin) + '/AjaxCenter/sendinfo';

	page.addEventListener('submit', function (e) {
		var form = e.target;
		if (!form.matches || !form.matches('[data-sl-form]')) return;
		e.preventDefault();

		var result = form.querySelector('.sl-form-result');
		var button = form.querySelector('.sl-submit');
		var required = form.querySelectorAll('[required]');
		var invalid = null;

		for (var i = 0; i < required.length; i++) {
			required[i].classList.add('is-touched');
			if (!required[i].value.trim() && !invalid) invalid = required[i];
		}
		if (invalid) {
			invalid.focus();
			return;
		}

		var data = new FormData(form);
		data.append('email', '');
		if (!data.get('message')) data.set('message', form.getAttribute('data-service') || '');

		button.disabled = true;
		if (result) { result.textContent = ''; result.classList.remove('is-error'); }

		var xhr = new XMLHttpRequest();
		xhr.open('POST', endpoint, true);
		xhr.onload = function () {
			button.disabled = false;
			var ok = xhr.status >= 200 && xhr.status < 300;
			if (!result) return;
			if (ok) {
				var text = '';
				try {
					var json = JSON.parse(xhr.responseText);
					var tmp = document.createElement('div');
					tmp.innerHTML = json.output || '';
					var p = tmp.querySelector('p');
					text = p ? p.textContent.trim() : '';
				} catch (err) { text = ''; }
				result.textContent = text;
				form.reset();
			} else {
				result.classList.add('is-error');
				result.textContent = '';
			}
		};
		xhr.onerror = function () {
			button.disabled = false;
			if (result) result.classList.add('is-error');
		};
		xhr.send(data);
	});
})();
