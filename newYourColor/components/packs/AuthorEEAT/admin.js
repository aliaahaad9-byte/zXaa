/* ملف الخبير — صفوف قابلة للتكرار + اختيار الصورة */
(function ($) {
	'use strict';

	function nextIndex($rep) {
		var max = -1;
		$rep.find('.eeat-rows [name]').each(function () {
			var m = this.name.match(/\[(\d+)\]/);
			if (m) { max = Math.max(max, parseInt(m[1], 10)); }
		});
		return max + 1;
	}

	function addRow($rep, values) {
		var html = $rep.find('.eeat-tpl').html().replace(/__i__/g, nextIndex($rep));
		var $row = $(html);
		if (values) {
			$.each(values, function (k, v) {
				$row.find('[data-k="' + k + '"]').val(v);
			});
		}
		$rep.find('.eeat-rows').append($row);
		return $row;
	}

	$(document).on('click', '.eeat-add', function () {
		addRow($(this).closest('.eeat-repeater')).find('input,textarea').first().trigger('focus');
	});

	$(document).on('click', '.eeat-remove', function () {
		$(this).closest('.eeat-row').remove();
	});

	/* يملأ المجالات الأربعة المقترحة دون تكرار ما هو مكتوب بالفعل */
	$(document).on('click', '.eeat-suggest', function () {
		var $rep = $(this).closest('.eeat-repeater');
		var existing = $rep.find('[data-k="title"]').map(function () { return $.trim(this.value); }).get();
		var added = 0;
		$.each(window.EEATDefaults || [], function (_, row) {
			if ($.inArray(row.title, existing) !== -1) { return; }
			addRow($rep, row);
			added++;
		});
		if (!added) { window.alert('المجالات المقترحة موجودة بالفعل.'); }
	});

	var frame;
	$(document).on('click', '.eeat-photo__pick', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.eeat-photo');
		frame = wp.media({ title: 'الصورة الشخصية للكاتب', library: { type: 'image' }, multiple: false });
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			var url = (a.sizes && a.sizes.medium_large) ? a.sizes.medium_large.url : a.url;
			$box.find('.eeat-photo__val').val(url);
			$box.find('.eeat-photo__img').attr('src', url);
		});
		frame.open();
	});

	$(document).on('click', '.eeat-photo__clear', function () {
		var $box = $(this).closest('.eeat-photo');
		$box.find('.eeat-photo__val').val('');
		$box.find('.eeat-photo__img').attr('src', '');
	});
})(jQuery);
