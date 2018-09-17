<?php
/**
 * The template for displaying all single faq.
 *
 * @package lsx-faq
 */

get_header(); ?>

<?php lsx_content_wrap_before(); ?>

<?php
	$main_class = 'col-sm-12';

            $args = array(
                'taxonomy'   => 'faq-category',
                'hide_empty' => false,
            );

            $faq_categories = get_terms( $args );

 ?>

<?php if ( ! empty( $_GET ) ) { ?>
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
<?php
	//Set the class to accommodate the column
	$main_class = 'col-sm-8';
	}
?>

<div id="primary" class="content-area <?php echo esc_attr( $main_class ); ?>">

	<?php lsx_content_before(); ?>

	<main id="main" class="site-main lsx-faq-main">

		<?php lsx_content_top(); ?>

		<?php do_action( 'lsx-faq-content-before' ); ?>

			<div class="lsx-documentation-container">

				<?php
				$count = 1;
				$post_count = count( $faq_categories );
				foreach ( $faq_categories as $term ) {
					if ( 1 === $count ) {
						$output .= "<div class='row row-flex'>";
					}
					?>

					<div class="col-xs-12 col-sm-6 col-md-4 lsx-documentation-column">
						<article class="lsx-documentation-slot">
							<h5 class="lsx-documentation-title">
								<a href="/faq-category/<?php echo esc_url( $term->slug ); ?>"><?php echo esc_attr( $term->name ); ?></a>
							</h5>
							<div class="lsx-documentation-content">
								<a href="/faq-category/<?php echo esc_url( $term->slug ); ?>" class="moretag"><?php esc_html_e( 'View Documentation' ); ?></a>
							</div>
							<div class="lsx-documentation-tags">
								<?php echo get_the_term_list( get_the_ID(), 'faq-tags' ); ?>
							</div>
						</article>
					</div>

					<?php
					if ( 0 === $count % 3 || $count === $post_count ) {
					   echo '</div>';
						if ( $count < $post_count ) {
							echo "<div class='row row-flex'>";
						}
					}
					$count++;
				}
				?>
				<?php do_action( 'lsx-faq-content-after' ); ?>

			</div>

			<?php lsx_paging_nav(); ?>

		<?php lsx_content_bottom(); ?>

	</main><!-- #main -->

	<?php lsx_content_after(); ?>

</div><!-- #primary -->

<?php lsx_content_wrap_after(); ?>

<?php get_sidebar(); ?>

<?php get_footer();
