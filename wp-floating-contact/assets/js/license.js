/**
 * WP Floating Contact — License Activation JS
 *
 * Handles:
 *  - Serial input auto-formatting (inserts dashes, forces uppercase)
 *  - AJAX activate / deactivate calls
 *  - Visual feedback (shake on error, success state, spinner)
 */
( function () {
    'use strict';

    if ( typeof wpfc_license === 'undefined' ) { return; }

    var AJAX_URL = wpfc_license.ajax_url;
    var NONCE    = wpfc_license.nonce;

    // ── Serial input auto-formatter ───────────────────────────────────────────

    var $input = document.getElementById( 'wpfc-serial-input' );
    if ( $input ) {
        $input.addEventListener( 'input', function () {
            // Strip everything except allowed chars, force uppercase.
            var raw     = this.value.toUpperCase().replace( /[^A-Z0-9]/g, '' );
            var parts   = [];
            var offset  = 0;

            // First chunk after "WPFC" prefix is 4 chars; remaining 3 chunks 6 chars.
            // Expected format: WPFC-XXXXXX-XXXXXX-XXXXXX  (total 22 chars without dashes)
            // Part 0: "WPFC" (fixed)
            // Parts 1-3: 6 chars each

            // We prepend WPFC if not already there.
            if ( raw.startsWith( 'WPFC' ) ) {
                raw = raw.slice( 4 );
            }

            var chunk1 = raw.slice( 0, 6 );
            var chunk2 = raw.slice( 6, 12 );
            var chunk3 = raw.slice( 12, 18 );

            var formatted = 'WPFC';
            if ( chunk1 ) { formatted += '-' + chunk1; }
            if ( chunk2 ) { formatted += '-' + chunk2; }
            if ( chunk3 ) { formatted += '-' + chunk3; }

            this.value = formatted;
        } );

        // Prevent lowercase pasting.
        $input.addEventListener( 'paste', function ( e ) {
            e.preventDefault();
            var text = ( e.clipboardData || window.clipboardData ).getData( 'text' );
            document.execCommand( 'insertText', false, text.toUpperCase() );
        } );
    }

    // ── Activate handler ──────────────────────────────────────────────────────

    var $form = document.getElementById( 'wpfc-license-form' );
    if ( $form ) {
        $form.addEventListener( 'submit', function ( e ) {
            e.preventDefault();
            doActivate();
        } );
    }

    function doActivate() {
        var serial = $input ? $input.value.trim() : '';
        if ( ! serial ) {
            showMsg( 'error', wpfc_license.msg_empty );
            shakeInput();
            return;
        }

        var $btn = document.getElementById( 'wpfc-activate-btn' );
        var $msg = document.getElementById( 'wpfc-license-msg' );

        setBtnLoading( $btn, true );
        hideMsg( $msg );

        var fd = new FormData();
        fd.append( 'action', 'wpfc_activate_license' );
        fd.append( 'nonce',  NONCE );
        fd.append( 'serial', serial );

        fetch( AJAX_URL, { method: 'POST', body: fd, credentials: 'same-origin' } )
            .then( function ( r ) { return r.json(); } )
            .then( function ( data ) {
                setBtnLoading( $btn, false );
                if ( data.success ) {
                    $input.classList.add( 'wpfc-input-success' );
                    showMsg( 'success', wpfc_license.msg_success );
                    // Reload after a short delay so the full admin UI loads.
                    setTimeout( function () { window.location.reload(); }, 1200 );
                } else {
                    shakeInput();
                    showMsg( 'error', data.data && data.data.message
                        ? data.data.message
                        : wpfc_license.msg_invalid );
                }
            } )
            .catch( function () {
                setBtnLoading( $btn, false );
                showMsg( 'error', wpfc_license.msg_error );
            } );
    }

    // ── Deactivate handler ────────────────────────────────────────────────────

    var $deactivateBtn = document.getElementById( 'wpfc-deactivate-btn' );
    if ( $deactivateBtn ) {
        $deactivateBtn.addEventListener( 'click', function () {
            if ( ! window.confirm( wpfc_license.confirm_deactivate ) ) { return; }

            var fd = new FormData();
            fd.append( 'action', 'wpfc_deactivate_license' );
            fd.append( 'nonce',  NONCE );

            fetch( AJAX_URL, { method: 'POST', body: fd, credentials: 'same-origin' } )
                .then( function ( r ) { return r.json(); } )
                .then( function ( data ) {
                    if ( data.success ) {
                        window.location.reload();
                    }
                } );
        } );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    function setBtnLoading( btn, loading ) {
        if ( ! btn ) { return; }
        if ( loading ) {
            btn.disabled = true;
            btn.dataset.orig = btn.innerHTML;
            btn.innerHTML = '<span class="wpfc-spinner"></span>' + wpfc_license.activating;
        } else {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.orig || wpfc_license.activate_label;
        }
    }

    function showMsg( type, text ) {
        var $msg = document.getElementById( 'wpfc-license-msg' );
        if ( ! $msg ) { return; }
        $msg.className = 'wpfc-license-msg wpfc-msg-' + type;
        $msg.textContent = text;
    }

    function hideMsg( el ) {
        if ( el ) { el.className = 'wpfc-license-msg'; el.textContent = ''; }
    }

    function shakeInput() {
        if ( ! $input ) { return; }
        $input.classList.remove( 'wpfc-input-error' );
        void $input.offsetWidth; // reflow to restart animation
        $input.classList.add( 'wpfc-input-error' );
        setTimeout( function () { $input.classList.remove( 'wpfc-input-error' ); }, 400 );
    }

} )();
