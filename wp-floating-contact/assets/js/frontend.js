/**
 * WP Floating Contact Buttons — Frontend Script
 *
 * Tracking strategy (most-reliable-first):
 *  1. navigator.sendBeacon → REST endpoint  (works even when page is
 *     backgrounded / phone dialer opens; no nonce needed).
 *  2. fetch() with keepalive: true → REST endpoint  (modern fallback).
 *  3. jQuery $.ajax → legacy AJAX endpoint  (old-browser last resort).
 *
 * This chain fixes the "counter stops on new mobile visitor" bug caused by
 * cached nonces failing for different users. The REST endpoint is fully
 * public and requires no nonce.
 */
( function ( $ ) {
    'use strict';

    var REST_URL  = wpfc_vars.rest_url;   // /wp-json/wpfc/v1/track
    var AJAX_URL  = wpfc_vars.ajax_url;   // admin-ajax.php  (fallback)
    var PAGE_URL  = wpfc_vars.page_url;

    /**
     * Builds a URL-encoded body string for the REST / AJAX payload.
     *
     * @param {string} clickType
     * @returns {string}
     */
    function buildBody( clickType ) {
        return 'click_type=' + encodeURIComponent( clickType ) +
               '&page_url='  + encodeURIComponent( PAGE_URL );
    }

    /**
     * Sends tracking data via sendBeacon (fire-and-forget, survives app-switch).
     *
     * @param {string} clickType
     * @returns {boolean}  true if sendBeacon accepted the request.
     */
    function trackViaBeacon( clickType ) {
        if ( typeof navigator.sendBeacon !== 'function' ) {
            return false;
        }
        var blob = new Blob( [ buildBody( clickType ) ], {
            type: 'application/x-www-form-urlencoded',
        } );
        return navigator.sendBeacon( REST_URL, blob );
    }

    /**
     * Fallback via fetch with keepalive (request survives page unload).
     *
     * @param {string} clickType
     */
    function trackViaFetch( clickType ) {
        if ( typeof window.fetch !== 'function' ) {
            return trackViaAjax( clickType );
        }
        fetch( REST_URL, {
            method:    'POST',
            headers:   { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:      buildBody( clickType ),
            keepalive: true,
        } ).catch( function () {
            // fetch failed — try AJAX last resort.
            trackViaAjax( clickType );
        } );
    }

    /**
     * Legacy jQuery AJAX to the old wp-admin/admin-ajax.php endpoint.
     *
     * @param {string} clickType
     */
    function trackViaAjax( clickType ) {
        $.ajax( {
            url:  AJAX_URL,
            type: 'POST',
            data: {
                action:     'wpfc_track_click',
                click_type: clickType,
                page_url:   PAGE_URL,
            },
        } );
    }

    /**
     * Master tracking function — walks down the fallback chain.
     *
     * @param {string} clickType  'phone' | 'whatsapp'
     */
    function trackClick( clickType ) {
        if ( clickType !== 'phone' && clickType !== 'whatsapp' ) {
            return;
        }

        // Try sendBeacon first (most reliable on mobile).
        if ( ! trackViaBeacon( clickType ) ) {
            // sendBeacon not supported or returned false → use fetch.
            trackViaFetch( clickType );
        }
    }

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
