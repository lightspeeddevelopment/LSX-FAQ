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

					// Get all the FAQ post ids attached to this term
					$current_terms_posts = new WP_Query(
						array(
							'post_type' => 'faq',
							'post_status' => 'publish',
							'nopagin' => true,
							'faq-category' => $term->slug,
							'fields' => 'ids',
						)
					);

					//Set our defaults
					$number_of_questions = 0;
					$all_faq_tags = array();

					//if our query has info, then use it
					if ( $current_terms_posts->have_posts() ) {
						foreach ( $current_terms_posts->posts as $faq_post ) {
							//Increment the number of faq posts.
							$number_of_questions++;

							$faq_tags = get_the_term_list( $faq_post, 'faq-tags' );
							if ( ! is_wp_error( $faq_tags ) && false !== $faq_tags && '' !== $faq_tags ) {
								$all_faq_tags[] = $faq_tags;
							}
						}
					}
					?>

					<div class="col-xs-12 col-sm-6 col-md-4 lsx-documentation-column">
						<article class="lsx-documentation-slot">
							<h5 class="lsx-documentation-title">
								<a href="<?php echo get_term_link( $term ); ?>"><?php echo esc_attr( $term->name ); ?> - (<?php echo wp_kses_post( $number_of_questions ); ?>)</a>
							</h5>
							<div class="lsx-documentation-tags">
								<?php if ( ! empty( $faq_tags ) ) {
									echo wp_kses_post( implode( ',', $all_faq_tags ) );
								}?>
							</div>
							<div class="lsx-documentation-content">
								<a href="<?php echo get_term_link( $term ); ?>" class="moretag"><?php esc_html_e( 'View Documentation' ); ?></a>
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
