/**
 * WP Floating Contact Buttons — Admin Script
 *
 * Handles the "Clear All Logs" button on the Analytics page:
 *  1. Shows a native confirm() dialog.
 *  2. POSTs to the AJAX endpoint with a nonce for CSRF protection.
 *  3. Animates the table rows out and replaces them with an empty-state notice.
 *  4. Resets the summary counters to zero without a full page reload.
 */
( function ( $ ) {
    'use strict';

    // ── Utilities ─────────────────────────────────────────────────────────────

    /**
     * Inserts a dismissible WordPress-style admin notice directly below the <h1>.
     *
     * @param {'success'|'error'} type
     * @param {string}            message
     */
    function showNotice( type, message ) {
        $( '.wpfc-notice' ).remove(); // clear any existing notice first

        var $notice = $(
            '<div class="notice notice-' + type + ' is-dismissible wpfc-notice">' +
            '<p>' + $( '<span>' ).text( message ).html() + '</p>' +
            '<button type="button" class="notice-dismiss">' +
            '<span class="screen-reader-text">Dismiss this notice.</span>' +
            '</button>' +
            '</div>'
        );

        $( '.wpfc-wrap h1' ).first().after( $notice );

        // Honour the standard WP dismiss button.
        $notice.find( '.notice-dismiss' ).on( 'click', function () {
            $notice.fadeOut( 300, function () { $( this ).remove(); } );
        } );

        // Auto-dismiss after 5 s.
        setTimeout( function () {
            $notice.fadeOut( 400, function () { $( this ).remove(); } );
        }, 5000 );
    }

    // ── Clear logs handler ────────────────────────────────────────────────────

    function initClearLogs() {
        $( document ).on( 'click', '#wpfc-clear-logs', function ( e ) {
            e.preventDefault();

            // Step 1: Confirm — native dialog keeps it simple and avoids extra markup.
            if ( ! window.confirm( wpfc_admin.confirm_clear ) ) {
                return;
            }

            var $btn         = $( this );
            var originalHtml = $btn.html();

            // Step 2: Loading state.
            $btn.prop( 'disabled', true ).html(
                '<span class="wpfc-spinner"></span>' + wpfc_admin.clearing
            );

            // Step 3: AJAX call.
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

    /**
     * Called when the server confirms logs have been deleted.
     * Animates the table out and inserts an empty-state placeholder.
     *
     * @param {jQuery} $btn          The clear button element.
     * @param {string} originalHtml  Button label to restore.
     */
    function onClearSuccess( $btn, originalHtml ) {
        showNotice( 'success', wpfc_admin.cleared );

        // Zero out summary counters.
        $( '#wpfc-total-count, #wpfc-phone-count, #wpfc-wa-count' ).text( '0' );

        // Also update the dashboard stat cards if they're on the page.
        $( '.wpfc-stat-number' ).filter( function () {
            return /^\d/.test( $( this ).text() );
        } ).text( '0' );

        // Animate rows out, then swap in empty state.
        var $table      = $( '.wpfc-analytics-table' );
        var $pagination = $( '.wpfc-pagination' );
        var $card       = $( '#wpfc-logs-card' );

        $table.add( $pagination ).fadeOut( 350, function () {
            $table.remove();
            $pagination.remove();

            // Only inject empty state once.
            if ( ! $card.find( '.wpfc-empty-state' ).length ) {
                $card.append(
                    '<div class="wpfc-empty-state" id="wpfc-empty-state">' +
                    '<span class="dashicons dashicons-chart-bar"></span>' +
                    '<p>No click data recorded yet. Clicks on the floating buttons will appear here automatically.</p>' +
                    '</div>'
                );
            }
        } );

        // Keep the button disabled — there's nothing left to clear.
        $btn.prop( 'disabled', true ).html( originalHtml );
    }

    // ── Bootstrap ─────────────────────────────────────────────────────────────

    $( document ).ready( initClearLogs );

} )( jQuery );
