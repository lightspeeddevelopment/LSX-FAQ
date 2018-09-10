<?php
/**
 * Activity Widget Content Part
 *
 * @package    lsx-tour-operators
 * @category   activity
 * @subpackage widget
 */
global $disable_placeholder;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( '1' !== $disable_placeholder && true !== $disable_placeholder ) { ?>
		<div class="thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php lsx_thumbnail( 'lsx-thumbnail-wide' ); ?>
			</a>
		</div>
	<?php } ?>

	<h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

	<div class="widget-content">
		<?php if ( false !== get_post_meta( get_the_ID(), 'code', true ) ) { ?>
			<div class="meta code"><?php _e( 'Code', 'to-vehicles' ); ?>: <span><?php echo get_post_meta( get_the_ID(), 'code', true ); ?></span></div>
		<?php } ?>
		<?php if ( false !== get_post_meta( get_the_ID(), 'seating', true ) ) { ?>
			<div class="meta seats"><?php _e( 'Seats', 'to-vehicles' ); ?>: <span><?php echo get_post_meta( get_the_ID(), 'seating', true ); ?></span></div>
		<?php } ?>
		<div class="view-more" style="text-align:center;">
			<a href="<?php the_permalink(); ?>" class="btn btn-primary text-center"><?php _e( 'View Vehicle', 'to-vehicles' ); ?></a>
		</div>	
	</div>	
</article>
