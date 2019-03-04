<?php
/**
 * @package lsx-faq
 */
global $faq_counter;
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
}
?>

	<li><h3 class="question">
			<?php if ( is_tax() || is_single() ) { ?><span class="faq-counter"><?php echo esc_attr( $faq_counter ); $faq_counter++; ?>)</span><?php } ?>
	<?php the_title(); ?><div class="plus-minus-toggle collapsed"></div></h3>


	<div class="answer"><?php echo $post->post_content; ?></div></li>
