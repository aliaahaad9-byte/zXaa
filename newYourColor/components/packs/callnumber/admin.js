/* لوحة تتبع المكالمات — سكربت الإدارة (معزول داخل صفحة الإضافة فقط) */
(function ($) {
    'use strict';

    var state = {
        filter: 'today',
        paged: 1,
        selected: {}
    };

    var $wrap = $('.yc-calls-wrap');
    if (!$wrap.length || typeof YCCalls === 'undefined') {
        return;
    }

    /* ------------------------------------------------------------ */
    /* أدوات مساعدة                                                  */
    /* ------------------------------------------------------------ */

    function esc(text) {
        return $('<div>').text(text == null ? '' : String(text)).html();
    }

    function post(action, data) {
        return $.post(YCCalls.ajaxurl, $.extend({
            action: action,
            nonce: YCCalls.nonce
        }, data || {}));
    }

    var linkIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l82.7 0L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3l0 82.7c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160c0-17.7-14.3-32-32-32L320 0zM80 32C35.8 32 0 67.8 0 112L0 432c0 44.2 35.8 80 80 80l320 0c44.2 0 80-35.8 80-80l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 112c0 8.8-7.2 16-16 16L80 448c-8.8 0-16-7.2-16-16l0-320c0-8.8 7.2-16 16-16l112 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 32z"/></svg>';
    var trashIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>';
    var waIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157z"/></svg>';
    var phIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>';

    function typeBadge(type) {
        if (type === 'whatsapp') {
            return '<span class="yc-badge yc-badge-whatsapp">' + waIcon + ' واتساب</span>';
        }
        if (type === 'phone') {
            return '<span class="yc-badge yc-badge-phone">' + phIcon + ' اتصال</span>';
        }
        return '<span class="yc-badge yc-badge-unknown">غير محدد</span>';
    }

    /* ------------------------------------------------------------ */
    /* نافذة التأكيد                                                 */
    /* ------------------------------------------------------------ */

    var confirmCallback = null;

    function openConfirm(text, onConfirm) {
        confirmCallback = onConfirm;
        $('#yc-modal-text').text(text);
        $('#yc-modal').prop('hidden', false);
    }

    function closeConfirm() {
        confirmCallback = null;
        $('#yc-modal').prop('hidden', true);
    }

    $('#yc-modal-cancel').on('click', closeConfirm);
    $('#yc-modal').on('click', function (e) {
        if (e.target === this) closeConfirm();
    });
    $('#yc-modal-confirm').on('click', function () {
        var cb = confirmCallback;
        closeConfirm();
        if (cb) cb();
    });

    /* ------------------------------------------------------------ */
    /* عرض البيانات                                                  */
    /* ------------------------------------------------------------ */

    function renderStats(stats) {
        $('#yc-stat-whatsapp').text(stats.whatsapp);
        $('#yc-stat-phone').text(stats.phone);
        $('#yc-stat-total').text(stats.total);

        var $list = $('#yc-top-list').empty();
        if (!stats.top_pages.length) {
            $list.append('<li class="yc-empty">لا توجد بيانات لهذه الفترة</li>');
            return;
        }
        $.each(stats.top_pages, function (i, p) {
            $list.append(
                '<li class="yc-top-item">' +
                    '<span class="yc-top-rank">' + (i + 1) + '</span>' +
                    '<div>' +
                        '<div class="yc-top-name"><span class="yc-top-title" title="' + esc(p.title) + '">' + esc(p.title) + '</span>' +
                        '<span class="yc-top-count">' + p.count + ' نقرة</span></div>' +
                        '<div class="yc-top-meta">' +
                            '<span class="yc-top-percent">' + p.percent + '%</span>' +
                            '<span class="yc-top-bar"><span style="width:' + Math.min(100, p.percent) + '%"></span></span>' +
                        '</div>' +
                    '</div>' +
                '</li>'
            );
        });
    }

    function renderRows(rows) {
        var $body = $('#yc-rows').empty();
        if (!rows.length) {
            $body.append('<tr><td colspan="5" class="yc-empty">لا توجد سجلات لهذه الفترة</td></tr>');
            return;
        }
        $.each(rows, function (_, r) {
            var link = r.url
                ? '<a class="yc-page-link" href="' + esc(r.url) + '" target="_blank" rel="noopener" title="فتح الصفحة">' + linkIcon + '</a>'
                : '';
            $body.append(
                '<tr data-id="' + r.id + '">' +
                    '<td><input type="checkbox" class="yc-row-check" value="' + r.id + '"' + (state.selected[r.id] ? ' checked' : '') + '></td>' +
                    '<td><span class="yc-page-cell">' + link + '<span>' + esc(r.page) + '</span></span></td>' +
                    '<td>' + typeBadge(r.type) + '</td>' +
                    '<td class="yc-time-cell"><strong>' + esc(r.date) + '</strong><span>' + esc(r.time) + '</span></td>' +
                    '<td><button type="button" class="yc-row-delete" title="حذف السجل">' + trashIcon + '</button></td>' +
                '</tr>'
            );
        });
    }

    function renderPagination(paged, totalPages, totalRows) {
        var $p = $('#yc-pagination').empty();
        if (totalPages <= 1) return;

        $p.append('<button type="button" class="yc-page-btn" data-page="' + (paged - 1) + '"' + (paged <= 1 ? ' disabled' : '') + '>السابق</button>');

        var from = Math.max(1, paged - 2);
        var to = Math.min(totalPages, paged + 2);
        for (var i = from; i <= to; i++) {
            $p.append('<button type="button" class="yc-page-btn' + (i === paged ? ' is-current' : '') + '" data-page="' + i + '">' + i + '</button>');
        }

        $p.append('<button type="button" class="yc-page-btn" data-page="' + (paged + 1) + '"' + (paged >= totalPages ? ' disabled' : '') + '>التالي</button>');
        $p.append('<span class="yc-page-info">إجمالي السجلات: ' + totalRows + '</span>');
    }

    function updateSelectionUI() {
        var count = Object.keys(state.selected).length;
        $('#yc-delete-selected').prop('disabled', !count);
        var all = $('.yc-row-check').length && $('.yc-row-check:not(:checked)').length === 0;
        $('#yc-check-all').prop('checked', all);
    }

    function load() {
        $wrap.addClass('is-loading');
        post('yc_calls_data', { filter: state.filter, paged: state.paged })
            .done(function (res) {
                if (!res || !res.success) return;
                renderStats(res.data.stats);
                renderRows(res.data.rows);
                renderPagination(res.data.paged, res.data.total_pages, res.data.total_rows);
                state.paged = res.data.paged;
                updateSelectionUI();
            })
            .always(function () {
                $wrap.removeClass('is-loading');
            });
    }

    /* ------------------------------------------------------------ */
    /* الأحداث                                                       */
    /* ------------------------------------------------------------ */

    $('.yc-filter').on('click', function () {
        $('.yc-filter').removeClass('is-active');
        $(this).addClass('is-active');
        state.filter = $(this).data('filter');
        state.paged = 1;
        state.selected = {};
        load();
    });

    $('#yc-pagination').on('click', '.yc-page-btn:not(:disabled)', function () {
        state.paged = parseInt($(this).data('page'), 10) || 1;
        load();
    });

    $('#yc-rows').on('change', '.yc-row-check', function () {
        var id = $(this).val();
        if (this.checked) {
            state.selected[id] = true;
        } else {
            delete state.selected[id];
        }
        updateSelectionUI();
    });

    $('#yc-check-all').on('change', function () {
        var checked = this.checked;
        $('.yc-row-check').each(function () {
            this.checked = checked;
            if (checked) {
                state.selected[$(this).val()] = true;
            } else {
                delete state.selected[$(this).val()];
            }
        });
        updateSelectionUI();
    });

    function deleteIds(ids) {
        $wrap.addClass('is-loading');
        post('yc_calls_delete', { ids: ids }).done(function () {
            $.each(ids, function (_, id) { delete state.selected[id]; });
            load();
        });
    }

    $('#yc-rows').on('click', '.yc-row-delete', function () {
        var id = $(this).closest('tr').data('id');
        openConfirm('هل أنت متأكد من حذف هذا السجل؟ لا يمكن التراجع عن هذا الإجراء.', function () {
            deleteIds([id]);
        });
    });

    $('#yc-delete-selected').on('click', function () {
        var ids = Object.keys(state.selected);
        if (!ids.length) return;
        openConfirm('سيتم حذف ' + ids.length + ' من السجلات المحددة نهائيًا. هل تريد المتابعة؟', function () {
            deleteIds(ids);
        });
    });

    $('#yc-delete-month').on('click', function () {
        var month = $('#yc-month').val();
        openConfirm('سيتم حذف جميع سجلات شهر ' + month + ' نهائيًا لتفريغ قاعدة البيانات. هل تريد المتابعة؟', function () {
            $wrap.addClass('is-loading');
            post('yc_calls_delete_month', { month: month }).done(function () {
                state.selected = {};
                state.paged = 1;
                load();
            });
        });
    });

    /* ------------------------------------------------------------ */
    /* تقرير PDF (نافذة طباعة جاهزة للحفظ كـ PDF)                    */
    /* ------------------------------------------------------------ */

    $('#yc-export').on('click', function () {
        var month = $('#yc-month').val();
        var $btn = $(this).prop('disabled', true);

        post('yc_calls_report', { month: month })
            .done(function (res) {
                if (!res || !res.success) return;
                openReport(res.data);
            })
            .always(function () {
                $btn.prop('disabled', false);
            });
    });

    function openReport(data) {
        var rowsHtml = '';
        $.each(data.rows, function (i, r) {
            var type = r.type === 'whatsapp' ? 'واتساب' : (r.type === 'phone' ? 'اتصال' : 'غير محدد');
            var cls = r.type === 'whatsapp' ? 'wa' : (r.type === 'phone' ? 'ph' : 'un');
            rowsHtml +=
                '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td class="pg">' + esc(r.page) + '</td>' +
                '<td><span class="badge ' + cls + '">' + type + '</span></td>' +
                '<td>' + esc(r.date) + '</td>' +
                '<td>' + esc(r.time) + '</td>' +
                '</tr>';
        });
        if (!rowsHtml) {
            rowsHtml = '<tr><td colspan="5" style="text-align:center;color:#888;padding:20px">لا توجد سجلات لهذا الشهر</td></tr>';
        }

        var html =
            '<!DOCTYPE html><html dir="rtl" lang="ar"><head><meta charset="utf-8">' +
            '<title>تقرير المكالمات — ' + esc(data.month) + '</title>' +
            '<style>' +
            'body{font-family:Tahoma,Arial,sans-serif;color:#0f172a;margin:30px;font-size:12px}' +
            '.head{display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid #10b3a3;padding-bottom:14px;margin-bottom:20px}' +
            '.head h1{font-size:20px;margin:0}' +
            '.head p{color:#64748b;margin:4px 0 0;font-size:11px}' +
            '.kpis{display:flex;gap:12px;margin-bottom:22px}' +
            '.kpi{flex:1;border:1px solid #e2e8f0;border-radius:10px;padding:14px;text-align:center}' +
            '.kpi b{display:block;font-size:24px;margin-top:4px}' +
            '.kpi.wa b{color:#16a34a}.kpi.ph b{color:#2563eb}.kpi.tt b{color:#7c3aed}' +
            'table{width:100%;border-collapse:collapse}' +
            'th{background:#f1f5f9;padding:8px;font-size:11px;text-align:right;border:1px solid #e2e8f0}' +
            'td{padding:7px 8px;border:1px solid #e8edf5}' +
            'td.pg{max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}' +
            '.badge{padding:2px 10px;border-radius:99px;font-size:10.5px;font-weight:bold}' +
            '.badge.wa{background:#dcfce7;color:#15803d}.badge.ph{background:#dbeafe;color:#1d4ed8}.badge.un{background:#f1f5f9;color:#64748b}' +
            '.foot{margin-top:20px;color:#94a3b8;font-size:10px;text-align:center}' +
            '@media print{body{margin:12mm}}' +
            '</style></head><body>' +
            '<div class="head"><div><h1>تقرير المكالمات الشهري</h1>' +
            '<p>' + esc(data.site) + ' — شهر ' + esc(data.month) + ' — توقيت الرياض (GMT+3)</p></div>' +
            '<div style="font-size:11px;color:#64748b">تاريخ الإصدار: ' + new Date().toLocaleDateString('en-CA') + '</div></div>' +
            '<div class="kpis">' +
            '<div class="kpi wa">إجمالي اتصالات الواتساب<b>' + data.stats.whatsapp + '</b></div>' +
            '<div class="kpi ph">إجمالي الاتصالات الهاتفية<b>' + data.stats.phone + '</b></div>' +
            '<div class="kpi tt">الإجمالي الكلي للتحويلات<b>' + data.stats.total + '</b></div>' +
            '</div>' +
            '<table><thead><tr><th style="width:40px">#</th><th>اسم الصفحة</th><th style="width:90px">نوع الاتصال</th><th style="width:90px">التاريخ</th><th style="width:100px">الوقت</th></tr></thead>' +
            '<tbody>' + rowsHtml + '</tbody></table>' +
            '<div class="foot">تم إنشاء هذا التقرير تلقائيًا من لوحة تتبع المكالمات</div>' +
            '<script>window.onload=function(){window.print()}<\/script>' +
            '</body></html>';

        var win = window.open('', '_blank');
        if (!win) {
            alert('يرجى السماح بالنوافذ المنبثقة لتصدير التقرير');
            return;
        }
        win.document.open();
        win.document.write(html);
        win.document.close();
    }

    /* ------------------------------------------------------------ */

    load();

})(jQuery);
