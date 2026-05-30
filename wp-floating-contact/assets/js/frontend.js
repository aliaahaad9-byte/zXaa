/**
 * WP Floating Contact Buttons — Frontend Tracking
 *
 * Tracking chain (most reliable → least):
 *
 *  1. sendBeacon( ajaxUrl, FormData )
 *     - FormData → multipart/form-data → PHP always populates $_POST correctly.
 *     - Fire-and-forget: works even when the phone dialer opens (tel:) or a
 *       new tab launches (WhatsApp).
 *     - Supported in all modern browsers including Safari iOS 11.3+.
 *
 *  2. fetch( ajaxUrl, { method:'POST', body: FormData, keepalive: true } )
 *     - keepalive ensures the request completes even during page navigation.
 *
 *  3. XMLHttpRequest (synchronous is deprecated; we use async with a short
 *     delay to give the browser time to send before any potential unload).
 *
 * WHY FormData, not Blob?
 *   sendBeacon + Blob typed as 'application/x-www-form-urlencoded' is broken
 *   on Safari iOS: the Content-Type header is dropped, PHP sees an empty body,
 *   and $_POST is empty. FormData avoids that entirely.
 *
 * WHY admin-ajax.php, not REST?
 *   The REST API has its own body-parsing layer that can miss multipart bodies
 *   in certain PHP/server configurations. admin-ajax.php reads $_POST directly.
 */
( function () {
    'use strict';

    /* wpfc_vars is printed by wp_localize_script in class-frontend.php */
    if ( typeof wpfc_vars === 'undefined' ) { return; }

    var AJAX_URL = wpfc_vars.ajax_url;
    var PAGE_URL = wpfc_vars.page_url || '';

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Builds a FormData object ready to POST to admin-ajax.php.
     *
     * @param {string} clickType  'phone' | 'whatsapp'
     * @returns {FormData}
     */
    function buildFormData( clickType ) {
        var fd = new FormData();
        fd.append( 'action',     'wpfc_track_click' );
        fd.append( 'click_type', clickType );
        fd.append( 'page_url',   PAGE_URL );
        return fd;
    }

    // ── Tracking attempts ─────────────────────────────────────────────────────

    /**
     * Attempt 1 — sendBeacon with FormData.
     * Returns true if the browser accepted the beacon.
     */
    function tryBeacon( clickType ) {
        if ( typeof navigator.sendBeacon !== 'function' ) { return false; }
        return navigator.sendBeacon( AJAX_URL, buildFormData( clickType ) );
    }

    /**
     * Attempt 2 — fetch with keepalive.
     * Returns true if fetch is available (regardless of async outcome).
     */
    function tryFetch( clickType ) {
        if ( typeof window.fetch !== 'function' ) { return false; }
        window.fetch( AJAX_URL, {
            method:      'POST',
            body:        buildFormData( clickType ),
            keepalive:   true,
            credentials: 'same-origin',
        } ).catch( function () {
            tryXhr( clickType ); // fetch failed — last resort
        } );
        return true;
    }

    /**
     * Attempt 3 — plain XMLHttpRequest.
     */
    function tryXhr( clickType ) {
        try {
            var xhr  = new XMLHttpRequest();
            var body = 'action=wpfc_track_click' +
                       '&click_type=' + encodeURIComponent( clickType ) +
                       '&page_url='   + encodeURIComponent( PAGE_URL );
            xhr.open( 'POST', AJAX_URL, true ); // async
            xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
            xhr.send( body );
        } catch ( e ) { /* silent */ }
    }

    // ── Master entry point ────────────────────────────────────────────────────

    function trackClick( clickType ) {
        if ( clickType !== 'phone' && clickType !== 'whatsapp' ) { return; }

        if ( tryBeacon( clickType ) ) { return; } // best path
        if ( tryFetch( clickType )  ) { return; } // fallback
        tryXhr( clickType );                        // last resort
    }

    // ── Event binding ─────────────────────────────────────────────────────────

    document.addEventListener( 'click', function ( e ) {
        // Walk up the DOM in case the click landed on a child (icon/span).
        var el = e.target;
        while ( el && el !== document ) {
            if ( el.classList && el.classList.contains( 'wpfc-btn' ) ) {
                var type = el.getAttribute( 'data-type' );
                if ( type ) { trackClick( type ); }
                return;
            }
            el = el.parentNode;
        }
    }, true ); // capture phase — fires before href navigation

} )();
