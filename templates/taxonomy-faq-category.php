<?php
/**
 * The template for displaying all single faq.
 *
 * @package lsx-faq
 */
global $faq_counter;

get_header(); ?>

<?php lsx_content_wrap_before(); ?>

<div id="secondary" class="col-md-4 widget-area">
	<div class="widget">
		<h3><?php _e( 'Search' ); ?></h3>
		<?php echo do_shortcode( '[facetwp facet="faq_search"]' ); ?>
	</div>

	<?php if ( ! is_tax() ) { ?>
		<div class="widget">
			<h3><?php _e( 'Categories' ); ?></h3>
			<?php echo do_shortcode( '[facetwp facet="faq_category"]' ); ?>
		</div>
	<?php } ?>

	<div class="widget">
		<h3><?php _e( 'Tags' ); ?></h3>
		<?php echo do_shortcode( '[facetwp facet="faq_tags"]' ); ?>
	</div>
</div>

<div id="primary" class="content-area col-md-8">

	<?php lsx_content_before(); ?>

	<main id="main" class="site-main">

		<?php lsx_content_top(); ?>

		<?php if ( have_posts() ) : ?>

			<div class="lsx-faq-container">
				<div class="row row-flex lsx-faq-row"">

					<?php
						$faq_counter = 1;

						while ( have_posts() ) {
							the_post();
							include( LSX_FAQ_PATH . '/templates/content-faq.php' );
						}
					?>

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

<?php get_footer();
