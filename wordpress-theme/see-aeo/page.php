<?php
/**
 * Single page template — See AEO.
 *
 * @package See_AEO
 */

get_header();
?>

<main>
  <section class="section" style="padding-top:140px;">
    <div class="container">
      <?php
      while ( have_posts() ) :
        the_post();
        ?>
        <div class="section__header">
          <h1 class="section__title"><?php the_title(); ?></h1>
        </div>
        <div class="about__para" style="max-width:820px;margin:0 auto;">
          <?php the_content(); ?>
        </div>
      <?php endwhile; ?>
    </div>
  </section>
</main>

<?php
get_footer();
