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

		<?php
			$args = array(
				'taxonomy'   => 'faq-category',
				'hide_empty' => false,
			);

			$doc_categories = get_terms( $args );
			$doc_categories_selected = get_query_var( 'faq-category' );

			if ( count( $doc_categories ) > 0 ) :
			?>

			

			<?php
			endif;
		?>

		<?php if ( have_posts() ) : ?>

			<div class="lsx-faq-container">
				<div class="row row-flex lsx-faq-row"">

					<?php
						$count = 0;

						while ( have_posts() ) {
							the_post();
							include( LSX_FAQ_PATH . '/templates/content-archive-faq.php' );
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

<?php get_sidebar(); ?>

<?php get_footer();
