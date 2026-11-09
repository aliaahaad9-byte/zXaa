<?php
/**
 * Inline SVG icons (stroke style, 24px grid, currentColor).
 * Decorative by default (aria-hidden). Pass a label to expose it to screen readers.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function dg_icon( $name, $label = '', $class = '' ) {
	$p = array(
		'phone'   => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
		'pin'     => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
		'clock'   => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
		'cpu'     => '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 14h3M1 9h3M1 14h3"/>',
		'chip'    => '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 14h3M1 9h3M1 14h3"/>',
		'receipt' => '<path d="M4 2v20l3-2 3 2 3-2 3 2 3-2 1 .7V2l-1 .7L16 1l-3 2-3-2-3 2-3-2z"/><path d="M8 8h8M8 12h8M8 16h5"/>',
		'scan'    => '<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M6 13l3-5 3 6 2-3 2 2h2"/><path d="M8 21h8M12 17v4"/>',
		'oil'     => '<path d="M12 2.7S6 9.3 6 14a6 6 0 0 0 12 0c0-4.7-6-11.3-6-11.3z"/>',
		'drop'    => '<path d="M12 2.7S6 9.3 6 14a6 6 0 0 0 12 0c0-4.7-6-11.3-6-11.3z"/><path d="M9.5 15a2.5 2.5 0 0 0 2.5 2.5"/>',
		'gear'    => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
		'disc'    => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><path d="M12 5v.01M17 7v.01M19 12v.01M17 17v.01M12 19v.01M7 17v.01M5 12v.01M7 7v.01"/>',
		'temp'    => '<path d="M14 14.8V4a2 2 0 0 0-4 0v10.8a4 4 0 1 0 4 0z"/>',
		'engine'  => '<path d="M3 10h2V8h4V6h6v2h2l2 3h2v6h-2l-2 2H9l-2-2H5v-3H3z"/><path d="M10 6V4h4v2"/>',
		'gauge'   => '<path d="M12 14l4-4"/><path d="M3.3 19a10 10 0 1 1 17.4 0"/>',
		'paint'   => '<rect x="3" y="3" width="14" height="6" rx="1"/><path d="M17 6h3v6h-8v3"/><rect x="10" y="15" width="4" height="7" rx="1"/>',
		'frame'   => '<rect x="3" y="7" width="18" height="10" rx="2"/><path d="M7 7v10M17 7v10M3 12h18"/>',
		'check'   => '<path d="M20 6 9 17l-5-5"/>',
		'arrow'   => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
		'chevron' => '<path d="m6 9 6 6 6-6"/>',
		'play'    => '<polygon points="6 3 20 12 6 21 6 3" fill="currentColor"/>',
		'menu'    => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'close'   => '<path d="M18 6 6 18M6 6l12 12"/>',
		'search'  => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
		'star'    => '<polygon points="12 2 15.1 8.3 22 9.3 17 14.1 18.2 21 12 17.8 5.8 21 7 14.1 2 9.3 8.9 8.3 12 2" fill="currentColor"/>',
		'calendar'=> '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'alert'   => '<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/>',
		'users'   => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
		'file'    => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>',
		'whatsapp'=> '<path fill="currentColor" stroke="none" d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.3zM12 21.8a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4a9.8 9.8 0 1 1 8.3 4.6zm8.4-18.3A11.8 11.8 0 0 0 1.7 17.7L0 24l6.4-1.7A11.8 11.8 0 0 0 23.8 12a11.7 11.7 0 0 0-3.4-8.5z"/>',
		'instagram'=> '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
		'tiktok'  => '<path fill="currentColor" stroke="none" d="M19.6 6.7a4.8 4.8 0 0 1-3.8-4.2V2h-3.4v13.7a2.9 2.9 0 1 1-2-2.8V9.4a6.3 6.3 0 1 0 5.4 6.3V8.7a8.2 8.2 0 0 0 4.8 1.5V6.8z"/>',
		'facebook'=> '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		'youtube' => '<path d="M22.5 6.4a2.8 2.8 0 0 0-2-2C18.9 4 12 4 12 4s-6.9 0-8.6.5a2.8 2.8 0 0 0-1.9 2A29 29 0 0 0 1 12a29 29 0 0 0 .5 5.6 2.8 2.8 0 0 0 1.9 2c1.7.4 8.6.4 8.6.4s6.9 0 8.6-.5a2.8 2.8 0 0 0 1.9-2A29 29 0 0 0 23 12a29 29 0 0 0-.5-5.6z"/><polygon points="9.8 15 15.5 12 9.8 9 9.8 15" fill="currentColor"/>',

		// Dashboard warning-light symbols (simplified).
		'wl-engine' => '<path d="M3 10h2V8h3V6h7v2h2l2 3h2v6h-2l-2 2H9l-2-2H5v-3H3z"/>',
		'wl-epc'    => '<text x="12" y="16" text-anchor="middle" font-size="9" font-weight="800" fill="currentColor" stroke="none" font-family="Arial,sans-serif">EPC</text>',
		'wl-gear'   => '<circle cx="12" cy="12" r="6"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/><path d="M12 9.5v3M12 14.5h.01"/>',
		'wl-coolant'=> '<path d="M12 3v11M12 3h3M12 7h3M12 11h3"/><circle cx="12" cy="16" r="2.5"/><path d="M3 21c1.5-1 3-1 4.5 0s3 1 4.5 0 3-1 4.5 0 3 1 4.5 0"/>',
		'wl-oil'    => '<path d="M3 13h9l3-3 6 2-6 6H5a2 2 0 0 1-2-2z"/><path d="M7 13v-3h3M21 12v.01M22 15c0 1-1 1-1 2"/>',
		'wl-abs'    => '<circle cx="12" cy="12" r="7"/><path d="M3.5 7A10 10 0 0 0 3.5 17M20.5 7a10 10 0 0 1 0 10"/><text x="12" y="14.6" text-anchor="middle" font-size="6.5" font-weight="800" fill="currentColor" stroke="none" font-family="Arial,sans-serif">ABS</text>',
		'wl-battery'=> '<rect x="3" y="7" width="18" height="12" rx="1"/><path d="M7 7V5h3v2M14 7V5h3v2M6 13h4M15 11v4M13 13h4"/>',
		'wl-brake'  => '<circle cx="12" cy="12" r="7"/><path d="M3.5 7A10 10 0 0 0 3.5 17M20.5 7a10 10 0 0 1 0 10M12 8.5v4.5M12 15.5h.01"/>',
		'wl-tire'   => '<path d="M5 8c-1 1.5-1.5 3.3-1.5 5.2 0 2 .8 3.8 2 5.3h13c1.2-1.5 2-3.3 2-5.3 0-1.9-.5-3.7-1.5-5.2"/><path d="M12 8v5M12 15.5h.01M4 21h16"/>',
	);
	if ( ! isset( $p[ $name ] ) ) { return ''; }
	$a11y = $label !== '' ? 'role="img" aria-label="' . esc_attr( $label ) . '"' : 'aria-hidden="true" focusable="false"';
	return '<svg class="dg-ico ' . esc_attr( $class ) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ' . $a11y . '>' . $p[ $name ] . '</svg>';
}
