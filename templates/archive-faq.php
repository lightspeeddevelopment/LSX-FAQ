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

	<div class="col-xs-12 col-sm-12 col-md-12">
	<div class="row">
		<?php do_action( 'lsx-faq-content-before' ); ?>
	</div>
	</div>

			<div class="lsx-documentation-container">

				<?php
				$count = 1;
				$post_count = count( $faq_categories );
				foreach ( $faq_categories as $term ) {

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


							$faq_tags = get_the_terms( $faq_post, 'faq-tags' );
							if ( ! is_wp_error( $faq_tags ) && false !== $faq_tags && '' !== $faq_tags ) {
								foreach ( $faq_tags as $faq_tag ) {

									$link = get_term_link( $faq_tag, 'faq-tags' );
									if ( ! is_wp_error( $link ) && ! isset( $all_faq_tags[ $faq_tag->slug ] ) ) {
										$all_faq_tags[ $faq_tag->slug ] = '<a href="' . esc_url( $link ) . '" rel="tag">' . $faq_tag->name . '</a>';
									}
								}
							}
						}
					}

					//Check if we have an image
					$thumbnail = false;
					$value         = get_term_meta( $term->term_id, 'thumbnail', true );
					$image_preview = wp_get_attachment_image_src( $value, 'thumbnail' );

					if ( is_array( $image_preview ) ) {
						$width = $image_preview[1];
						if ( '1' === $width || 1 === $width) {
							$width = '150';
						}
						$height = $image_preview[2];
						if ( '1' === $height || 1 === $height) {
							$height = '150';
						}

						$thumbnail = '<img src="' . $image_preview[0] . '" width="' . $width . '" height="' . $height . '" class="alignnone size-thumbnail wp-image-' . $value . '" />';
					}

					//If its the first item in the array, then output a div
					if ( 1 === $count ) {
						echo "<div class='row row-flex'>";
					}
					?>

					<div class="col-xs-12 col-sm-6 col-md-4 lsx-documentation-column">
						<article class="lsx-documentation-slot">
							<figure class="lsx-documentation-avatar">
								<?php
									if ( false !== $thumbnail ) {
										echo wp_kses_post( $thumbnail );
									}
								?>
							</figure>

							<h5 class="lsx-documentation-title">
								<a href="<?php echo get_term_link( $term ); ?>"><?php echo esc_attr( $term->name ); ?> - (<?php echo wp_kses_post( $number_of_questions ); ?>)</a>
							</h5>
							<div class="lsx-documentation-tags">
								<?php if ( ! empty( $faq_tags ) ) {
									echo wp_kses_post( implode( ', ', $all_faq_tags ) );
								}?>
							</div>
							<div class="lsx-documentation-content">
								<a href="<?php echo get_term_link( $term ); ?>" class="moretag"><?php echo esc_html('View FAQ\'s'); ?></a>
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
