<?php
/**
 * Header template — See AEO
 *
 * @package See_AEO
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

  <!-- NAVBAR -->
  <header class="navbar" id="navbar">
    <div class="navbar__inner">

      <?php
      // Logo: custom logo if set, otherwise SVG brand mark.
      if ( has_custom_logo() ) {
        echo '<div class="navbar__logo">';
        the_custom_logo();
        echo '</div>';
      } else { ?>
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-logo navbar__logo" aria-label="See AEO">
        <span class="brand-logo__mark">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/></svg>
        </span>
        <span class="brand-logo__text">See<b>AEO</b></span>
      </a>
      <?php } ?>

      <nav class="navbar__links" id="navLinks">
        <?php
        wp_nav_menu( array(
          'theme_location' => 'primary',
          'container'      => false,
          'items_wrap'     => '%3$s',
          'fallback_cb'    => 'see_aeo_default_menu',
          'depth'          => 1,
        ) );
        ?>
      </nav>

      <a href="<?php echo esc_url( home_url( '/works/' ) ); ?>" class="btn--portfolio navbar__cta">
        <svg class="ico" viewBox="0 0 24 24"><rect x="2.5" y="7" width="19" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M2.5 12h19"/></svg>
        <?php esc_html_e( 'ملف الأعمال', 'see-aeo' ); ?>
      </a>

      <button class="hamburger" id="hamburger" aria-label="<?php esc_attr_e( 'القائمة', 'see-aeo' ); ?>" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <div class="mobile-nav" id="mobileNav" aria-hidden="true">
    <?php
    wp_nav_menu( array(
      'theme_location' => 'primary',
      'container'      => false,
      'menu_class'     => '',
      'items_wrap'     => '%3$s',
      'fallback_cb'    => 'see_aeo_default_menu',
      'depth'          => 1,
    ) );
    ?>
    <a href="<?php echo esc_url( home_url( '/works/' ) ); ?>" class="btn--portfolio" onclick="closeMobileNav()">
      <svg class="ico" viewBox="0 0 24 24"><rect x="2.5" y="7" width="19" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M2.5 12h19"/></svg>
      <?php esc_html_e( 'ملف الأعمال', 'see-aeo' ); ?>
    </a>
  </div>
