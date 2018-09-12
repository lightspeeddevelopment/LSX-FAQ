<?php
/**
 * @package lsx-faq
 */
?>

<?php
	global $lsx_faq;

	$doc_categories = '';
	$doc_categories_class = '';
	$terms = get_the_terms( get_the_ID(), 'faq-category' );

if ( $terms && ! is_wp_error( $terms ) ) {
		$doc_categories = array();
		$doc_categories_class = array();

	foreach ( $terms as $term ) {
			$doc_categories[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
			$doc_categories_class[] = 'filter-' . $term->slug;
	}

		$doc_categories = join( ', ', $doc_categories );
		$doc_categories = join( ' ', $doc_categories );
}
?>

<div class="col-xs-12 col-sm-12 col-md-12 lsx-faq-column">
	<article class="lsx-faq-slot">
		<?php /*if ( ! empty( lsx_get_thumbnail( 'lsx-thumbnail-single' ) ) ) : ?>
			<figure class="lsx-faq-avatar"><?php lsx_thumbnail( 'lsx-thumbnail-single' ); ?></figure>
		<?php endif; */ ?>

		<h5 class="lsx-faq-title">
			<?php the_title(); ?>
		</h5>

		<?php the_content(); ?>

	</article>
</div>
