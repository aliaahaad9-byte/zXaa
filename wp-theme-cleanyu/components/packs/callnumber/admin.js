/**
 * Calls Tracking Dashboard — admin.php?page=add_callnumber_fields
 * AJAX filters, pagination, secure deletes (confirmation modal) and PDF export.
 */
(function ($) {
    'use strict';

    var state = {
        range: 'all',
        paged: 1,
        pendingAction: null // callback executed when the modal is confirmed
    };

    var $dash = $('.cn-dash');
    if (!$dash.length || typeof CallnumberData === 'undefined') {
        return;
    }

    /* ---------------- Helpers ---------------- */

    function toast(message, isError) {
        var $t = $('#cn-toast');
        $t.text(message)
            .toggleClass('cn-toast--error', !!isError)
            .prop('hidden', false);
        clearTimeout($t.data('timer'));
        $t.data('timer', setTimeout(function () {
            $t.prop('hidden', true);
        }, 3200));
    }

    function openModal(text, onConfirm) {
        $('#cn-modal-text').text(text);
        state.pendingAction = onConfirm;
        $('#cn-modal').prop('hidden', false);
    }

    function closeModal() {
        state.pendingAction = null;
        $('#cn-modal').prop('hidden', true);
    }

    function updateBulkButton() {
        var count = $('.cn-row-check:checked').length;
        $('#cn-delete-selected').prop('disabled', count === 0)
            .find('span').text(count ? 'حذف المحدد (' + count + ')' : 'حذف المحدد');
    }

    function ajaxPost(data) {
        data.nonce = CallnumberData.nonce;
        return $.post(CallnumberData.ajaxUrl, data);
    }

    /* ---------------- Filtering / pagination ---------------- */

    function refresh() {
        $dash.addClass('is-loading');
        ajaxPost({
            action: 'callnumber_filter',
            range: state.range,
            paged: state.paged
        }).done(function (res) {
            if (!res || !res.success) {
                toast('حدث خطأ أثناء تحميل البيانات.', true);
                return;
            }
            $('#cn-cards-wrap').html(res.data.cards);
            $('#cn-top-pages-wrap').html(res.data.top_pages);
            $('#cn-table-body').html(res.data.rows);
            $('#cn-pagination-wrap').html(res.data.pagination);
            $('#cn-check-all').prop('checked', false);
            updateBulkButton();
        }).fail(function () {
            toast('تعذر الاتصال بالخادم.', true);
        }).always(function () {
            $dash.removeClass('is-loading');
        });
    }

    $dash.on('click', '.cn-filter', function () {
        var $btn = $(this);
        if ($btn.hasClass('is-active')) {
            return;
        }
        $('.cn-filter').removeClass('is-active');
        $btn.addClass('is-active');
        state.range = $btn.data('range');
        state.paged = 1;
        refresh();
    });

    $dash.on('click', '.cn-page-btn:not(:disabled)', function () {
        state.paged = parseInt($(this).data('page'), 10) || 1;
        refresh();
    });

    /* ---------------- Row selection ---------------- */

    $dash.on('change', '#cn-check-all', function () {
        $('.cn-row-check').prop('checked', this.checked);
        updateBulkButton();
    });

    $dash.on('change', '.cn-row-check', updateBulkButton);

    /* ---------------- Deletes ---------------- */

    function deleteRecords(ids) {
        ids.forEach(function (id) {
            $('tr[data-record-id="' + id + '"]').addClass('is-removing');
        });
        ajaxPost({
            action: 'callnumber_delete_records',
            ids: ids
        }).done(function (res) {
            if (res && res.success) {
                toast('تم حذف ' + res.data.deleted + ' سجل بنجاح.');
                refresh();
            } else {
                toast((res && res.data && res.data.message) || 'تعذر الحذف.', true);
                refresh();
            }
        }).fail(function () {
            toast('تعذر الاتصال بالخادم.', true);
            refresh();
        });
    }

    // Single row delete
    $dash.on('click', '.cn-delete-row', function () {
        var id = parseInt($(this).closest('tr').data('record-id'), 10);
        if (!id) {
            return;
        }
        openModal('هل أنت متأكد من حذف هذا السجل؟ لا يمكن التراجع عن هذا الإجراء.', function () {
            deleteRecords([id]);
        });
    });

    // Bulk delete
    $dash.on('click', '#cn-delete-selected', function () {
        var ids = $('.cn-row-check:checked').map(function () {
            return parseInt(this.value, 10);
        }).get();
        if (!ids.length) {
            return;
        }
        openModal('سيتم حذف ' + ids.length + ' سجل نهائيًا. هل أنت متأكد؟', function () {
            deleteRecords(ids);
        });
    });

    // Whole month delete
    $dash.on('click', '#cn-delete-month', function () {
        var month = $('#cn-month-select').val();
        if (!month) {
            toast('لا يوجد شهر متاح للحذف.', true);
            return;
        }
        openModal('سيتم حذف جميع سجلات شهر ' + month + ' نهائيًا لتفريغ قاعدة البيانات. هل أنت متأكد؟', function () {
            $dash.addClass('is-loading');
            ajaxPost({
                action: 'callnumber_delete_month',
                month: month
            }).done(function (res) {
                if (res && res.success) {
                    toast('تم حذف ' + res.data.deleted + ' سجل من شهر ' + month + '.');
                    var $opt = $('#cn-month-select option[value="' + month + '"]');
                    if ($opt.length && $('#cn-month-select option').length > 1) {
                        $opt.remove();
                    }
                    refresh();
                } else {
                    toast((res && res.data && res.data.message) || 'تعذر الحذف.', true);
                }
            }).fail(function () {
                toast('تعذر الاتصال بالخادم.', true);
            }).always(function () {
                $dash.removeClass('is-loading');
            });
        });
    });

    /* ---------------- Modal wiring ---------------- */

    $dash.on('click', '#cn-modal-confirm', function () {
        var action = state.pendingAction;
        closeModal();
        if (typeof action === 'function') {
            action();
        }
    });
    $dash.on('click', '#cn-modal-cancel, .cn-modal__backdrop', closeModal);
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    /* ---------------- تنظيف السجلات المهملة ---------------- */

    $dash.on('click', '#cn-clean-junk', function () {
        openModal('سيتم حذف كل السجلات التي لا تحمل اسم صفحة ولا نوع اتصال نهائيًا. هل أنت متأكد؟', function () {
            $dash.addClass('is-loading');
            ajaxPost({ action: 'callnumber_clean_junk' }).done(function (res) {
                if (res && res.success) {
                    toast('تم حذف ' + res.data.deleted + ' سجل مهمل.');
                    if (res.data.remaining > 0) {
                        toast('تبقّى ' + res.data.remaining + ' سجل — اضغط الزر مرة أخرى لإكمال التنظيف.');
                    } else {
                        $('#cn-junk-note').remove();
                    }
                    refresh();
                } else {
                    toast((res && res.data && res.data.message) || 'تعذر التنظيف.', true);
                }
            }).fail(function () {
                toast('تعذر الاتصال بالخادم.', true);
            }).always(function () {
                $dash.removeClass('is-loading');
            });
        });
    });

    /* ---------------- PDF export ---------------- */

    $dash.on('click', '#cn-export-pdf', function (e) {
        var month = $('#cn-month-select').val();
        if (!month) {
            e.preventDefault();
            toast('لا يوجد شهر متاح للتصدير.', true);
            return;
        }
        var base = $(this).data('base');
        $(this).attr('href', base + '&month=' + encodeURIComponent(month));
    });

})(jQuery);
