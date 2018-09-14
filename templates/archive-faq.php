<?php
/**
 * The template for displaying all single faq.
 *
 * @package lsx-faq
 */

get_header(); ?>

<?php lsx_content_wrap_before(); ?>

<div id="primary" class="content-area <?php echo esc_attr( lsx_main_class() ); ?>">

	<?php lsx_content_before(); ?>

	<main id="main" class="site-main">

		<?php lsx_content_top(); ?>

		<?php if ( have_posts() ) : ?>

			<div class="lsx-faq-container">
				<div class="row row-flex lsx-faq-row"">

					<?php do_action( 'lsx-faq-content-before' ); ?>

					<?php
						$count = 0;

						while ( have_posts() ) {
							the_post();
							include( LSX_FAQ_PATH . '/templates/content-faq.php' );
						}
					?>

					<?php do_action( 'lsx-faq-content-after' ); ?>

				</div>
			</div>

			<?php lsx_paging_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'partials/content', 'none' ); ?>

		<?php endif; ?>

		<?php lsx_content_bottom(); ?>

	</main><!-- #main -->

	<?php lsx_content_after(); ?>

</div><!-- #primary -->

<?php lsx_content_wrap_after(); ?>

<?php get_sidebar(); ?>

<?php get_footer();
