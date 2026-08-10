/**
 * WP Floating Contact Buttons — Admin Script
 *
 * Features:
 *  1. wp-color-picker init for the phone button color field.
 *  2. Real-time live-stats polling on the Dashboard page (every 6 s).
 *  3. "Clear All Logs" AJAX with animated DOM update.
 */
( function ( $ ) {
    'use strict';

    // ── 1. Color Picker ───────────────────────────────────────────────────────

    function initColorPicker() {
        $( '.wpfc-color-picker' ).wpColorPicker( {
            // Live preview — update the phone button preview swatch in the card heading.
            change: function ( event, ui ) {
                $( '.wpfc-card-icon-phone' ).css( 'background', ui.color.toString() );
            },
            clear: function () {
                $( '.wpfc-card-icon-phone' ).css( 'background', '' );
            },
        } );
    }

    // ── 2. Live Stats Polling ─────────────────────────────────────────────────

    var pollInterval = parseInt( wpfc_admin.poll_interval, 10 ) || 6000;
    var lastTotal    = -1; // track changes to flash the updated card

    function pollStats() {
        $.ajax( {
            url:  wpfc_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfc_get_stats',
                nonce:  wpfc_admin.nonce,
            },
            success: function ( response ) {
                if ( response.success && response.data ) {
                    updateStatCards( response.data );
                }
            },
            // Silent fail — dashboard is informational, not mission-critical.
            error: function () {},
        } );
    }

    /**
     * Updates the three live-stat counters.
     * Flashes the card when the number actually changed.
     *
     * @param {{ total: number, phone: number, whatsapp: number }} data
     */
    function updateStatCards( data ) {
        var newTotal = parseInt( data.total, 10 );

        setCounter( '#wpfc-stat-total',    data.total,    newTotal !== lastTotal );
        setCounter( '#wpfc-stat-phone',    data.phone,    false );
        setCounter( '#wpfc-stat-whatsapp', data.whatsapp, false );

        // Also refresh the analytics page summary counters if on that page.
        setCounter( '#wpfc-total-count', data.total,    false );
        setCounter( '#wpfc-phone-count', data.phone,    false );
        setCounter( '#wpfc-wa-count',    data.whatsapp, false );

        lastTotal = newTotal;
    }

    /**
     * Sets a counter element's text and optionally flashes it.
     *
     * @param {string}  selector
     * @param {number}  value
     * @param {boolean} flash
     */
    function setCounter( selector, value, flash ) {
        var $el = $( selector );
        if ( ! $el.length ) return;

        $el.text( Number( value ).toLocaleString() );

        if ( flash ) {
            $el.addClass( 'wpfc-counter-flash' );
            setTimeout( function () {
                $el.removeClass( 'wpfc-counter-flash' );
            }, 1000 );
        }
    }

    function startPolling() {
        // Only poll when the Dashboard tab is open.
        if ( wpfc_admin.is_dashboard !== '1' ) return;

        // Immediate first poll, then repeat.
        pollStats();
        setInterval( pollStats, pollInterval );
    }

    // ── 3. Clear Logs ─────────────────────────────────────────────────────────

    function initClearLogs() {
        $( document ).on( 'click', '#wpfc-clear-logs', function ( e ) {
            e.preventDefault();

            if ( ! window.confirm( wpfc_admin.confirm_clear ) ) {
                return;
            }

            var $btn         = $( this );
            var originalHtml = $btn.html();

            $btn.prop( 'disabled', true ).html(
                '<span class="wpfc-spinner"></span>' + wpfc_admin.clearing
            );

            $.ajax( {
                url:  wpfc_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'wpfc_clear_logs',
                    nonce:  wpfc_admin.nonce,
                },
                success: function ( response ) {
                    if ( response.success ) {
                        onClearSuccess( $btn, originalHtml );
                    } else {
                        var msg = ( response.data && response.data.message )
                            ? response.data.message
                            : wpfc_admin.error;
                        showNotice( 'error', msg );
                        $btn.prop( 'disabled', false ).html( originalHtml );
                    }
                },
                error: function () {
                    showNotice( 'error', wpfc_admin.error );
                    $btn.prop( 'disabled', false ).html( originalHtml );
                },
            } );
        } );
    }

    function onClearSuccess( $btn, originalHtml ) {
        showNotice( 'success', wpfc_admin.cleared );

        // Zero all summary counters.
        [ '#wpfc-total-count', '#wpfc-phone-count', '#wpfc-wa-count',
          '#wpfc-stat-total',  '#wpfc-stat-phone',  '#wpfc-stat-whatsapp' ].forEach( function ( sel ) {
            setCounter( sel, 0, false );
        } );

        lastTotal = 0;

        var $table      = $( '.wpfc-analytics-table' );
        var $pagination = $( '.wpfc-pagination' );
        var $card       = $( '#wpfc-logs-card' );

        $table.add( $pagination ).fadeOut( 350, function () {
            $table.remove();
            $pagination.remove();

            if ( ! $card.find( '.wpfc-empty-state' ).length ) {
                $card.append(
                    '<div class="wpfc-empty-state" id="wpfc-empty-state">' +
                    '<span class="dashicons dashicons-chart-bar"></span>' +
                    '<p>No click data recorded yet.</p>' +
                    '</div>'
                );
            }
        } );

        $btn.prop( 'disabled', true ).html( originalHtml );
    }

    // ── Utility: Admin notice ─────────────────────────────────────────────────

    function showNotice( type, message ) {
        $( '.wpfc-notice' ).remove();

        var $notice = $(
            '<div class="notice notice-' + type + ' is-dismissible wpfc-notice">' +
            '<p>' + $( '<span>' ).text( message ).html() + '</p>' +
            '<button type="button" class="notice-dismiss">' +
            '<span class="screen-reader-text">Dismiss</span>' +
            '</button>' +
            '</div>'
        );

        $( '.wpfc-wrap h1' ).first().after( $notice );

        $notice.find( '.notice-dismiss' ).on( 'click', function () {
            $notice.fadeOut( 300, function () { $( this ).remove(); } );
        } );

        setTimeout( function () {
            $notice.fadeOut( 400, function () { $( this ).remove(); } );
        }, 5000 );
    }

    // ── Bootstrap ─────────────────────────────────────────────────────────────

    $( document ).ready( function () {
        initColorPicker();
        initClearLogs();
        startPolling();
    } );

} )( jQuery );
