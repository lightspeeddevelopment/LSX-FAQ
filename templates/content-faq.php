<?php
/**
 * Vehicle Content Part
 *
 * @package    lsx-tour-operators
 * @category   vehicle
 */
global $lsx_archive;
if ( 1 !== $lsx_archive ) {
	$lsx_archive = false;
}
?>

<?php lsx_entry_before(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php lsx_entry_top(); ?>

	<?php if ( is_archive() ) { ?>
		<div class="col-sm-3">
			<div class="thumbnail">	
				<a href="<?php the_permalink(); ?>">
					<?php lsx_thumbnail( 'lsx-thumbnail-wide' ); ?>
				</a>
			</div>
		</div>				

		<div class="col-sm-9">
			<div class="col-sm-8">
				<header class="page-header">
					<?php the_title( '<h3 class="page-title"><a href="' . get_permalink() . '" title="' . __( 'Read more', 'to-vehicles' ) . '">', '</a></h3>' ); ?>
					<?php lsx_to_tagline( '<p class="tagline">', '</p>' ); ?>
				</header><!-- .entry-header -->				
		<?php
	}
		?>

		<div <?php lsx_to_entry_class( 'entry-content' ); ?>>

			<?php if ( is_single() ) { ?>
				<div class="single-main-info">
					<h3><?php _e( 'Summary', 'to-vehicles' ); ?></h3>
					<div class="meta taxonomies">
						<?php the_terms( get_the_ID(), 'travel-style', '<div class="meta travel-style">' . __( 'Travel Style', 'to-vehicles' ) . ': ', ', ', '</div>' ); ?>
						<?php lsx_to_connected_tours( '<div class="meta tours">' . __( 'Tours', 'to-vehicles' ) . ': ', '</div>' ); ?>
						<?php lsx_to_connected_accommodation( '<div class="meta accommodation">' . __( 'Accommodation', 'to-vehicles' ) . ': ', '</div>' ); ?>

						<?php lsx_vehicle_code( '<div class="meta code">' . __( 'Code', 'to-vehicles' ) . ': ', '</div>' ); ?>
						<?php lsx_vehicle_engine_type( '<div class="meta engine-type">' . __( 'Engine Type', 'to-vehicles' ) . ': ', '</div>' ); ?>
						<?php lsx_vehicle_gearbox( '<div class="meta gearbox">' . __( 'Gearbox', 'to-vehicles' ) . ': ', '</div>' ); ?>
						<?php lsx_vehicle_engine_size( '<div class="meta engine-size">' . __( 'Engine-size', 'to-vehicles' ) . ': ', '</div>' ); ?>
						<?php lsx_vehicle_gears( '<div class="meta gears">' . __( 'Gears', 'to-vehicles' ) . ': ', '</div>' ); ?>
						<?php lsx_vehicle_seating( '<div class="meta seating">' . __( 'Seating', 'to-vehicles' ) . ': ', '</div>' ); ?>
					</div>
					<?php lsx_to_sharing(); ?>
				</div>
				<?php the_content(); ?>
			<?php } else { ?>
				<?php the_excerpt(); ?>
			<?php } ?>
		</div><!-- .entry-content -->

	<?php if ( is_singular() && false === $lsx_archive && function_exists( 'lsx_to_has_team_member' ) && lsx_to_has_team_member() ) { ?>
		<div class="col-sm-3">
			<div class="team-member-widget">
				<?php lsx_to_team_member_panel( '<div class="team-member">', '</div>' ); ?>
				<?php lsx_to_enquire_modal(); ?>
			</div>
		</div>
	<?php } ?>		

	<?php if ( is_archive() ) { ?>		
		</div>
		<div class="col-sm-4">
			<div class="vehicle-details">
				<?php if ( false !== get_post_meta( get_the_ID(), 'code', true ) ) { ?>
					<div class="meta code"><?php _e( 'Code', 'to-vehicles' ); ?>: <span><?php echo get_post_meta( get_the_ID(), 'code', true ); ?></span></div>
				<?php } ?>
				<?php if ( false !== get_post_meta( get_the_ID(), 'seating', true ) ) { ?>
					<div class="meta seats"><?php _e( 'Seats', 'to-vehicles' ); ?>: <span><?php echo get_post_meta( get_the_ID(), 'seating', true ); ?></span></div>
				<?php } ?>				
				<?php lsx_to_connected_tours( '<div class="meta tours">' . __( 'Tours', 'to-vehicles' ) . ': ', '</div>' ); ?>
				<?php lsx_to_connected_accommodation( '<div class="meta accommodation">' . __( 'Accommodation', 'to-vehicles' ) . ': ', '</div>' ); ?>				
			</div>
		</div>
	</div>
	<?php } ?>

	<?php
		/**
		 * Hooked
		 *
		 *  - lsx_vehicle_metabox() - 10
		 */
		lsx_entry_bottom();
	?>

</article><!-- #post-## -->

<?php lsx_entry_after();
