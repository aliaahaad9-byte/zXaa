/* لوحة تتبع المكالمات — سكربت معزول يعمل داخل صفحة الإضافة فقط */
(function ($) {
	'use strict';

	if (typeof YCCalls === 'undefined') return;

	var state = { period: 'today', paged: 1 };
	var $wrap = $('.yc-calls-wrap');
	if (!$wrap.length) return;

	var $loader = $('#yc-loader');
	var $modal = $('#yc-modal');
	var modalAction = null;

	function loading(on) {
		$loader.toggleClass('is-on', !!on);
	}

	function openModal(title, text, onConfirm) {
		$('#yc-modal-title').text(title);
		$('#yc-modal-text').text(text);
		modalAction = onConfirm;
		$modal.addClass('is-open');
	}

	function closeModal() {
		$modal.removeClass('is-open');
		modalAction = null;
	}

	$('#yc-modal-cancel').on('click', closeModal);
	$modal.on('click', function (e) { if (e.target === this) closeModal(); });
	$('#yc-modal-confirm').on('click', function () {
		if (typeof modalAction === 'function') modalAction();
		closeModal();
	});

	function refresh() {
		loading(true);
		$.post(YCCalls.ajaxurl, {
			action: 'yc_calls_data',
			nonce: YCCalls.nonce,
			period: state.period,
			paged: state.paged
		}).done(function (res) {
			if (!res || !res.success) return;
			var d = res.data;
			$('#yc-kpi-whatsapp').text(d.kpis.whatsapp);
			$('#yc-kpi-call').text(d.kpis.call);
			$('#yc-kpi-total').text(d.kpis.total);
			$('#yc-top-pages').html(d.top5);
			$('#yc-logs-body').html(d.rows);
			$('#yc-pagination-holder').html(d.pagination);
			$('#yc-check-all').prop('checked', false);
			updateSelectedState();
		}).always(function () {
			loading(false);
		});
	}

	/* الفلاتر الزمنية */
	$wrap.on('click', '.yc-filter', function () {
		var $btn = $(this);
		if ($btn.hasClass('is-active')) return;
		$('.yc-filter').removeClass('is-active');
		$btn.addClass('is-active');
		state.period = $btn.data('period');
		state.paged = 1;
		refresh();
	});

	/* ترقيم الصفحات */
	$wrap.on('click', '.yc-page-btn', function () {
		var paged = parseInt($(this).data('paged'), 10);
		if (!paged || paged < 1) return;
		state.paged = paged;
		refresh();
	});

	/* التحديد الجماعي */
	function updateSelectedState() {
		var count = $('.yc-row-check:checked').length;
		$('#yc-delete-selected').prop('disabled', count === 0)
			.find('.yc-count').remove();
	}

	$wrap.on('change', '#yc-check-all', function () {
		$('.yc-row-check').prop('checked', this.checked);
		updateSelectedState();
	});
	$wrap.on('change', '.yc-row-check', updateSelectedState);

	function deleteIds(ids, confirmText) {
		openModal('تأكيد الحذف', confirmText, function () {
			loading(true);
			$.post(YCCalls.ajaxurl, {
				action: 'yc_calls_delete',
				nonce: YCCalls.nonce,
				ids: ids
			}).done(refresh).always(function () { loading(false); });
		});
	}

	/* حذف صف واحد */
	$wrap.on('click', '.yc-del-row', function () {
		var id = $(this).data('id');
		deleteIds([id], 'هل أنت متأكد من حذف هذا السجل؟ لا يمكن التراجع عن هذه العملية.');
	});

	/* حذف المحدد */
	$('#yc-delete-selected').on('click', function () {
		var ids = $('.yc-row-check:checked').map(function () { return this.value; }).get();
		if (!ids.length) return;
		deleteIds(ids, 'سيتم حذف ' + ids.length + ' سجل بشكل نهائي. هل أنت متأكد؟');
	});

	/* حذف سجلات شهر كامل */
	$('#yc-delete-month').on('click', function () {
		var month = $('#yc-month').val();
		openModal('حذف سجلات الشهر', 'سيتم حذف جميع سجلات شهر ' + month + ' نهائيًا لتفريغ قاعدة البيانات. هل أنت متأكد؟', function () {
			loading(true);
			$.post(YCCalls.ajaxurl, {
				action: 'yc_calls_delete_month',
				nonce: YCCalls.nonce,
				month: month
			}).done(refresh).always(function () { loading(false); });
		});
	});

	/* تصدير تقرير PDF (صفحة طباعة جاهزة) */
	$('#yc-export-pdf').on('click', function () {
		var month = $('#yc-month').val();
		var url = YCCalls.ajaxurl +
			'?action=yc_calls_report&month=' + encodeURIComponent(month) +
			'&nonce=' + encodeURIComponent(YCCalls.nonce);
		window.open(url, '_blank');
	});

})(jQuery);
