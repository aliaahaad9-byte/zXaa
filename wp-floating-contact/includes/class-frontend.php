<?php
/**
 * Frontend Class
 *
 * Enqueues frontend assets and injects the floating contact buttons into
 * the page footer. Respects the admin's enable/disable toggle and only
 * renders buttons whose numbers have actually been configured.
 *
 * @package WP_Floating_Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFC_Frontend {

    /** Fallback defaults — must match WPFC_Admin::$defaults. */
    private array $defaults = array(
        'phone_number'     => '',
        'whatsapp_number'  => '',
        'whatsapp_message' => 'Hello! I would like to get in touch.',
        'button_position'  => 'bottom-right',
        'enable_plugin'    => '1',
        'phone_color'      => '#1362bc',
        'phone_label'      => 'اتصل بنا',
        'whatsapp_label'   => 'واتساب',
    );

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_footer',          array( $this, 'render_buttons' ), 99 );
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function get_settings(): array {
        return wp_parse_args( get_option( 'wpfc_settings', array() ), $this->defaults );
    }

    private function is_enabled(): bool {
        return $this->get_settings()['enable_plugin'] === '1';
    }

    /**
     * Returns a darker shade of a hex colour for the gradient endpoint.
     *
     * @param string $hex    Six-digit hex colour (with or without #).
     * @param int    $amount Amount (0-255) to subtract from each channel.
     */
    private function get_current_page_title(): string {
        if ( is_singular() ) {
            return get_the_title();
        }
        if ( is_front_page() || is_home() ) {
            return get_bloginfo( 'name' );
        }
        if ( is_category() ) {
            return single_cat_title( '', false );
        }
        if ( is_tag() ) {
            return single_tag_title( '', false );
        }
        if ( is_archive() ) {
            return (string) get_the_archive_title();
        }
        if ( is_search() ) {
            return sprintf( 'Search: %s', get_search_query() );
        }
        return get_bloginfo( 'name' );
    }

    private function darken_hex( string $hex, int $amount = 30 ): string {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = max( 0, hexdec( substr( $hex, 0, 2 ) ) - $amount );
        $g = max( 0, hexdec( substr( $hex, 2, 2 ) ) - $amount );
        $b = max( 0, hexdec( substr( $hex, 4, 2 ) ) - $amount );
        return sprintf( '#%02x%02x%02x', $r, $g, $b );
    }

    // ─── Assets ───────────────────────────────────────────────────────────────

    public function enqueue_assets(): void {
        if ( ! $this->is_enabled() ) {
            return;
        }

        wp_enqueue_style(
            'wpfc-frontend',
            WPFC_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            WPFC_VERSION
        );

        wp_enqueue_script(
            'wpfc-frontend',
            WPFC_PLUGIN_URL . 'assets/js/frontend.js',
            array(), // no jQuery dependency — pure vanilla JS
            WPFC_VERSION,
            true
        );

        // Determine the current page URL safely.
        // esc_url_raw() is correct here (not esc_url) — esc_url encodes & as &amp;
        // which corrupts query strings when the value is used inside JavaScript.
        $page_url = '';
        if ( is_singular() ) {
            $page_url = (string) get_permalink();
        }
        if ( ! $page_url ) {
            $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $page_url    = home_url( $request_uri );
        }
        $page_url = esc_url_raw( $page_url );

        wp_localize_script(
            'wpfc-frontend',
            'wpfc_vars',
            array(
                'ajax_url'   => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
                'page_url'   => $page_url,
                'page_title' => sanitize_text_field( $this->get_current_page_title() ),
            )
        );
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render_buttons(): void {
        $settings = $this->get_settings();

        if ( $settings['enable_plugin'] !== '1' ) {
            return;
        }

        // Nothing to render if no numbers configured.
        if ( empty( $settings['phone_number'] ) && empty( $settings['whatsapp_number'] ) ) {
            return;
        }

        // Per-post number overrides (meta box values take priority over global settings).
        if ( is_singular() ) {
            $override_phone = get_post_meta( get_the_ID(), '_wpfc_phone', true );
            $override_wa    = get_post_meta( get_the_ID(), '_wpfc_whatsapp', true );
            if ( ! empty( $override_phone ) ) { $settings['phone_number']    = $override_phone; }
            if ( ! empty( $override_wa ) )    { $settings['whatsapp_number'] = $override_wa; }
        }

        $position       = sanitize_html_class( $settings['button_position'] );
        $phone_num      = esc_attr( $settings['phone_number'] );
        $phone_color    = sanitize_hex_color( $settings['phone_color'] ) ?: '#1362bc';
        $phone_label    = esc_html( $settings['phone_label'] ?: 'اتصل بنا' );
        $whatsapp_label = esc_html( $settings['whatsapp_label'] ?: 'واتساب' );
        // Strip everything except digits for the wa.me URL.
        $wa_digits   = preg_replace( '/[^0-9]/', '', $settings['whatsapp_number'] );
        $wa_message  = rawurlencode( $settings['whatsapp_message'] );
        $wa_href     = esc_url( "https://wa.me/{$wa_digits}?text={$wa_message}" );

        // Paint the phone button with the admin-chosen colour: solid background,
        // darker border, and the same colour on the icon inside the white badge.
        echo '<style>'
            . '.wpfc-btn-phone{background:' . esc_attr( $phone_color ) . '!important;'
            . 'border-color:' . esc_attr( $this->darken_hex( $phone_color, 30 ) ) . '!important}'
            . '.wpfc-btn-phone .wpfc-btn-icon svg{color:' . esc_attr( $phone_color ) . '!important}'
            . '</style>';
        ?>

        <div class="wpfc-buttons-container wpfc-position-<?php echo esc_attr( $position ); ?>"
             role="complementary"
             aria-label="<?php esc_attr_e( 'Contact Buttons', 'wp-floating-contact' ); ?>">

            <?php if ( ! empty( $settings['whatsapp_number'] ) ) : ?>
            <a href="<?php echo $wa_href; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — already esc_url'd above ?>"
               class="wpfc-btn wpfc-btn-whatsapp"
               data-type="whatsapp"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'wp-floating-contact' ); ?>"
               title="<?php esc_attr_e( 'WhatsApp', 'wp-floating-contact' ); ?>">

                <span class="wpfc-btn-icon" aria-hidden="true">
                    <!-- WhatsApp brand SVG (Font Awesome free / CC BY 4.0) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="26" height="26">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </span>
                <span class="wpfc-btn-label"><?php echo $whatsapp_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — already esc_html'd ?></span>
            </a>
            <?php endif; ?>

            <?php if ( ! empty( $settings['phone_number'] ) ) : ?>
            <a href="tel:<?php echo esc_attr( $phone_num ); ?>"
               class="wpfc-btn wpfc-btn-phone"
               data-type="phone"
               aria-label="<?php esc_attr_e( 'Call us', 'wp-floating-contact' ); ?>"
               title="<?php esc_attr_e( 'Call Us', 'wp-floating-contact' ); ?>">

                <span class="wpfc-btn-icon" aria-hidden="true">
                    <!-- Material phone SVG (Apache 2.0) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="26" height="26">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                </span>
                <span class="wpfc-btn-label"><?php echo $phone_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — already esc_html'd ?></span>
            </a>
            <?php endif; ?>

        </div>
        <?php
    }
}
