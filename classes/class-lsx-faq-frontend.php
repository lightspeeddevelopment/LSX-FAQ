<?php
namespace lsx;

/**
 * LSX FAQ Frontend Class
 *
 * @package   LSX FAQ
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeed
 */
class LSX_FAQ_Frontend
{
	/**
	 * Holds instance of the class
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Redirect the Archive template and the category template
		add_filter( 'template_include', array( $this, 'archive_template_include' ), 99 );
		add_filter( 'template_include', array( $this, 'taxonomy_template_include' ), 99 );
		add_action( 'template_redirect', array( $this, 'disable_single_templates' ) );
	}
	/**
	 * Return an instance of this class.
	 *
	 * @return  object
	 */
	public static function init() {
		// If the single instance hasn't been set, set it now.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Check for an archive-faq.php in the theme, if not default to the plugin version
	 *
	 * @var $template string
	 * @return string
	 */
	public function archive_template_include( $template ) {
		if ( is_main_query() && is_post_type_archive( 'faq' ) ) {
			if ( empty( locate_template( array( 'archive-faq.php' ) ) ) && file_exists( LSX_FAQ_PATH . 'templates/archive-faq.php' ) ) {
				$template = LSX_FAQ_PATH . 'templates/archive-faq.php';
			}
		}
		return $template;
	}
	/**
	 * Redirect wordpress to the taxonomy located in the plugin
	 *
	 * @param     $template string
	 * @return    string
	 */
	public function taxonomy_template_include( $template ) {
		if ( is_main_query() && is_tax( array( 'faq-category' ) ) ) {
			$current_taxonomy = get_query_var( 'taxonomy' );
			if ( '' == locate_template( array( 'taxonomy-' . $current_taxonomy . '.php' ) ) && file_exists( LSX_FAQ_PATH . 'templates/taxonomy-' . $current_taxonomy . '.php' ) ) {
				$template = LSX_FAQ_PATH . 'templates/taxonomy-' . $current_taxonomy . '.php';
			}
		}
		return $template;
	}
	/**
	 * Removes access to single testimonial member posts.
	 */
	public function disable_single_templates() {
		$queried_post_type = get_query_var( 'post_type' );
		if ( is_single() && 'faq' === $queried_post_type ) {
			wp_redirect( home_url(), 301 );
			exit;
		}
	}

}//end class
