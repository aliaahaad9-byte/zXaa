=== WP Floating Contact Buttons ===
Contributors: wpfloatingcontact
Tags: floating buttons, whatsapp, phone, contact, click tracking
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 1.0.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add floating phone and WhatsApp contact buttons to your site with real-time click analytics — works on all devices.

== Description ==

**WP Floating Contact Buttons** places beautiful pill-shaped floating contact buttons on every page of your WordPress site. Visitors can call you or open a WhatsApp chat with a single tap — on mobile or desktop.

All click data is tracked in your own database; nothing is sent to any external server.

= Key Features =

* **Floating phone & WhatsApp buttons** — pill-shaped, consistent on all screen sizes
* **Custom phone button color** — built-in WordPress color picker
* **Button position** — choose bottom-right or bottom-left
* **Pre-filled WhatsApp message** — customisable greeting text
* **AJAX click analytics** — every button click is logged with page URL and timestamp
* **Real-time live dashboard** — click counters refresh automatically every 6 seconds
* **Paginated analytics log** — browse and bulk-clear your full click history
* **One-click enable / disable** — hide buttons without deactivating the plugin
* **No jQuery dependency** — lightweight vanilla JS frontend
* **Works on cached pages** — uses `sendBeacon` + `FormData` for reliable tracking

= How It Works =

1. Install and activate the plugin.
2. Go to **Floating Contact → Settings** and enter your phone and/or WhatsApp number.
3. Buttons appear immediately on the frontend.
4. Visit **Floating Contact → Dashboard** to see live click counts and recent activity.

= Privacy =

This plugin stores click logs (button type + page URL + timestamp) in your own WordPress database. No data is shared with third parties. You can clear all logs at any time from the Analytics page.

== Installation ==

= Automatic installation =

1. Go to **Plugins → Add New** in your WordPress admin.
2. Search for **WP Floating Contact Buttons**.
3. Click **Install Now**, then **Activate**.
4. Navigate to **Floating Contact → Settings** to configure your numbers.

= Manual installation =

1. Download the plugin ZIP file.
2. Go to **Plugins → Add New → Upload Plugin**.
3. Choose the ZIP file and click **Install Now**, then **Activate**.

= After activation =

1. Go to **Floating Contact → Settings**.
2. Enter your phone number (with country code, e.g. `+966501234567`).
3. Enter your WhatsApp number (digits only, e.g. `966501234567`).
4. Optionally customise the button color, position, and WhatsApp message.
5. Click **Save Settings**.

== Frequently Asked Questions ==

= Does click tracking work on cached pages? =

Yes. Tracking is sent via `navigator.sendBeacon()` with `FormData` (not a nonce-protected request), so it works correctly even when a page is served from cache and multiple different visitors hit the same cached page.

= Which browsers are supported? =

All modern browsers including Safari iOS 11.3+, Chrome, Firefox, Edge, and Samsung Internet.

= Where is the click data stored? =

Everything is stored in a custom table (`{prefix}wpfc_click_logs`) inside your own WordPress database. No data leaves your server.

= Can I disable the buttons temporarily? =

Yes — use the **Enable Plugin** toggle in Settings. The buttons disappear from the frontend instantly without losing your settings or analytics data.

= Can I change the phone button color? =

Yes — a full WordPress color picker is available in the Settings page. The WhatsApp button stays green to respect the WhatsApp brand guidelines.

= How do I clear old analytics data? =

Go to **Floating Contact → Analytics** and click **Clear All Logs**.

= What happens if I uninstall the plugin? =

All plugin data (settings, click logs, and database table) is removed cleanly on uninstall.

== Screenshots ==

1. Frontend floating buttons — pill-shaped on both desktop and mobile.
2. Admin dashboard with live click counter and recent activity.
3. Plugin settings page — numbers, position, and phone color picker.
4. Full analytics log with pagination and clear-all action.

== Changelog ==

= 1.0.4 =
* Fixed floating buttons rendering flat inside the footer instead of floating on themes with aggressive CSS.
* Hardened all frontend styles so theme rules such as `a { display: block }` and `svg { width: 100% }` can no longer break the widget.
* The button container is now re-parented to `<body>` on load, so a theme wrapper using `transform` or `will-change` can no longer trap the fixed-position buttons.
* Raised the specificity of the injected phone colour so the admin's chosen colour always wins.
* Buttons are hidden when printing a page.

= 1.0.3 =
* New button design: rounded-rectangle buttons with the brand icon in a white circular badge.
* New monthly PDF report — pick a month and save a client-ready report from your browser.
* Report includes a summary, a daily activity chart, top pages, and the full click log.
* Analytics now resolves each logged URL to its real page title via url_to_postid().
* Click timestamps are stored in UTC and displayed in Riyadh time (UTC+3), 12-hour AM/PM.
* Added per-post phone and WhatsApp number overrides via a post editor meta box.
* Added customisable button labels for both the phone and WhatsApp buttons.

= 1.0.2 =
* Added 3-day free trial period on first activation.
* Added serial number license activation system.
* Added WhatsApp purchase button at the bottom of the dashboard.
* Security hardening: rate limiting on tracking endpoint (15 req/IP/min).
* Security hardening: URL length cap (2 000 chars) enforced at two layers.
* Fixed `esc_url` → `esc_url_raw` for page URL passed to JavaScript.
* Improved `REQUEST_URI` sanitization chain.
* Removed unused REST API endpoint reference from frontend JS.

= 1.0.1 =
* Fixed click tracking reliability on Safari iOS (switched from Blob to FormData with sendBeacon).
* Added real-time live stats dashboard with 6-second polling.
* Added phone button color picker (uses WordPress native wp-color-picker).
* Enlarged floating buttons for better mobile tap targets.
* Fixed tracking failure for multiple visitors on cached pages (removed nonce from nopriv AJAX).

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.4 =
Important fix: the floating buttons could render flat inside the footer on some themes. Update recommended for all users.

= 1.0.3 =
Redesigned floating buttons plus a downloadable monthly PDF report. Timestamps now display in Riyadh time.

= 1.0.2 =
Adds a 3-day free trial and optional serial-number activation. Existing installs continue to work normally after upgrading.
