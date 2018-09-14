<?php
/**
 * Template Tags
 *
 * @package   LSX FAQ
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2018 LightSpeed
 */

/**
 *  Outputs a multiselect Field for the WooCommerce product tab
 * @param $field
 */
function woocommerce_wp_select_multiple( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['value']         = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : array() );

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="width: 80%;" multiple="multiple">';

	foreach ( $field['options'] as $key => $value ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
	}

	echo '</select> ';

	if ( ! empty( $field['description'] ) ) {
		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}
	}
	echo '</p>';

	echo "<script>$(document).ready(function() {
    	console.log('." . $field['id'] . "_field');
	    $('." . $field['id'] . "_field select').select2();
	});</script>";
}

/**
 * Outputs the LSX FAQ search form
 * @param array $args
 */
function lsx_faq_search( $args = array() ) {
	$defaults = array(
		'class' => '',
		'action' => get_post_type_archive_link( 'faq' ),
		'placeholder' => '',
		'facet_name' => 'fwp_faq_search',
		'button_text' => __( 'Search', 'lsx-faq' ),
		'column_class' => 'col-md-12',
	);

	$args = wp_parse_args( $args, $defaults );
	?>
	<div class="<?php echo esc_attr( $args['class'] ); ?>">
		<?php do_action( 'lsx-faq-search-form-before' ); ?>
		<form class="search-form lsx-faq-search-form <?php echo esc_attr( $args['class'] ); ?> " action="<?php echo esc_attr( $args['action'] ); ?>" method="get">
			<?php do_action( 'lsx-faq-search-form-top' ); ?>
			<div class="input-group">
				<div class="field">
					<input class="search-field form-control" name="<?php echo esc_attr( $args['facet_name'] ); ?>" type="search" placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" autocomplete="off"></div>
				<div class="field submit-button">
					<button class="btn cta-btn " type="submit"><?php echo esc_attr( $args['button_text'] ); ?></button>
				</div>
			</div>
			<?php do_action( 'lsx-faq-search-form-bottom' ); ?>
		</form>
		<?php do_action( 'lsx-faq-search-form-after' ); ?>
	</div>
	<?php
}
