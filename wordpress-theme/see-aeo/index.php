<?php
/**
 * Fallback main template (blog / archives) — See AEO.
 *
 * @package See_AEO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main>
  <section class="section" style="padding-top:140px;" id="blog">
    <div class="container">
      <div class="section__header">
        <span class="badge"><?php echo esc_html( is_home() ? 'المدونة' : wp_strip_all_tags( get_the_archive_title() ) ); ?></span>
        <h2 class="section__title"><?php echo esc_html( single_post_title( '', false ) ? single_post_title( '', false ) : 'أحدث المقالات' ); ?></h2>
      </div>

      <?php if ( have_posts() ) : ?>
        <div class="works-grid">
          <?php
          while ( have_posts() ) :
            the_post();
            ?>
            <article class="work-card">
              <span class="work-card__tag"><?php echo esc_html( get_the_date() ); ?></span>
              <h3 class="work-card__title"><a href="<?php echo esc_url( get_permalink() ); ?>" style="color:inherit;"><?php echo esc_html( get_the_title() ); ?></a></h3>
              <p class="work-card__desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
            </article>
          <?php endwhile; ?>
        </div>

        <div style="text-align:center;margin-top:40px;">
          <?php
          the_posts_pagination(
            array(
              'prev_text' => 'السابق',
              'next_text' => 'التالي',
            )
          );
          ?>
        </div>
      <?php else : ?>
        <p style="text-align:center;color:var(--clr-text);">لا توجد مقالات منشورة بعد.</p>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php
get_footer();
