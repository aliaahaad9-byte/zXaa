/**
 * WP Floating Contact Buttons — Frontend Script
 *
 * Listens for clicks on .wpfc-btn elements and fires an AJAX request to
 * log the interaction. Tracking is fire-and-forget; it never interrupts
 * the user's navigation (the tel: / wa.me link opens independently).
 */
( function ( $ ) {
    'use strict';

    /**
     * Sends a click-tracking request to the WordPress AJAX endpoint.
     *
     * @param {string} clickType  'phone' | 'whatsapp'
     */
    function trackClick( clickType ) {
        // Guard: only track known types (belt-and-suspenders on top of server validation).
        if ( clickType !== 'phone' && clickType !== 'whatsapp' ) {
            return;
        }

        $.ajax( {
            url:  wpfc_vars.ajax_url,
            type: 'POST',
            data: {
                action:     'wpfc_track_click',
                nonce:      wpfc_vars.nonce,
                click_type: clickType,
                page_url:   wpfc_vars.page_url,
            },
            // Tracking failures are silent — we never block the user's action.
            error: function ( jqXHR, textStatus ) {
                if ( window.console && console.warn ) {
                    console.warn( '[WPFC] Click tracking failed:', textStatus );
                }
            },
        } );
    }

    /**
     * Attaches a delegated click handler to the document so it works even
     * if the container is injected after DOMContentLoaded.
     */
    function init() {
        $( document ).on( 'click', '.wpfc-btn', function () {
            var type = $( this ).data( 'type' );
            if ( type ) {
                trackClick( String( type ) );
            }
        } );
    }

    $( document ).ready( init );

} )( jQuery );
