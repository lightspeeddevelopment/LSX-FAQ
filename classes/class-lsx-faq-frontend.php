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
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'template_include', array( $this, 'archive_template_include' ), 99 );
		add_filter( 'template_include', array( $this, 'taxonomy_template_include' ), 99 );
		add_action( 'template_redirect', array( $this, 'disable_single_templates' ) );
		add_filter( 'get_the_archive_title', array( $this, 'lsx_banner_archive_title' ), 99, 1 );
		add_filter( 'woocommerce_product_tabs', array( $this, 'register_product_tab' ), 20, 1 );

		add_action( 'lsx-faq-content-before', array( $this, 'archive_search_form' ) );

		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'breadcrumb_links' ), 90 );
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

	public function assets() {
		wp_enqueue_script( 'lsx-faq', LSX_FAQ_URL . 'assets/js/lsx-faq.min.js', array( 'jquery' ), LSX_FAQ_VER, true );
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

	/**
	 * Add the custom tab
	 */
	public function register_product_tab( $tabs ) {
		$faq_posts = get_post_meta( get_the_ID(), 'lsx_faq_posts', true );
		$faq_categories = get_post_meta( get_the_ID(), 'lsx_faq_categories', true );
		if ( ( false !== $faq_posts && '' !== $faq_posts ) || ( false !== $faq_categories && '' !== $faq_categories ) ) {
			$tabs['faq'] = array(
				'title'    => __( 'FAQ', 'lsx-faq' ),
				'callback' => array( $this, 'product_tab_content' ),
				'priority' => 50,
			);
		}
		return $tabs;
	}

	/**
	 * Function that displays output for the shipping tab.
	 *
	 * TODO This needs to become a template tag
	 */
	function product_tab_content( $slug, $tab ) {
		global $faq_counter;
		$faq_posts = get_post_meta( get_the_ID(), 'lsx_faq_posts', true );

		if ( false !== $faq_posts && '' !== $faq_posts ) {

			if ( ! is_array( $faq_posts ) ) {
				$faq_posts = explode( ',', $faq_posts );
			}

			if ( ! empty( $faq_posts ) ) {

				$faq_query = new \WP_Query(
					array(
						'post__in' => $faq_posts,
						'posts_per_page' => -1,
						'post_type' => 'faq',
						'post_status' => 'publish',
					)
				);

				if ( $faq_query->have_posts() ) {

					//If there are categories assign, and general posts then add them in.
					$faq_categories = get_post_meta( get_the_ID(), 'lsx_faq_categories', true );
					if ( false !== $faq_categories && '' !== $faq_categories ) {
						echo '<h3 class="faq">'.  __( 'General', 'lsx-faq' ) . '</h3>';
					}

					echo '<div class="parent-container-faq"><ul class="faq">';

                    $faq_counter = 1;
					while ( $faq_query->have_posts() ) {
						$faq_query->the_post();
						include( LSX_FAQ_PATH . '/templates/content-faq.php' );
					}

					echo '</ul></div>';
				}

				wp_reset_query();
				wp_reset_postdata();

			} else {
				echo wp_kses_post( '<article><p>' . __( 'There are no FAQ posts assigned', 'lsx-faq' ) . '</p></article>');
			}
		}

		$this->product_tab_content_category( $slug, $tab );
	}

	/**
	 * Grabs the categories assigned to the product and runs through them.
	 * @param $slug
	 * @param $tab
	 */
	function product_tab_content_category( $slug, $tab ) {
		global $faq_counter;
		$faq_categories = get_post_meta( get_the_ID(), 'lsx_faq_categories', true );

		if ( false !== $faq_categories && '' !== $faq_categories ) {

			if ( ! is_array( $faq_categories ) ) {
				$faq_categories = explode( ',', $faq_categories );
			}

			if ( ! empty( $faq_categories ) ) {

				foreach( $faq_categories as $category ) {

					$faq_query = new \WP_Query(
						array(
							'faq-category' => $category,
							'posts_per_page' => -1,
							'post_type' => 'faq',
							'post_status' => 'publish',
						)
					);

					if ( $faq_query->have_posts() ) {

						$category_obj = get_term_by( 'slug', $category, 'faq-category' );

						echo '<div class="parent-container-faq">';

						echo '<h3 class="faq">'.  $category_obj->name . '</h3>';

						echo '<ul class="faq">';

						$faq_counter = 1;
						while ( $faq_query->have_posts() ) {
							$faq_query->the_post();
							include( LSX_FAQ_PATH . '/templates/content-faq.php' );
						}

						echo '</ul></div>';
					}

					wp_reset_query();
					wp_reset_postdata();

				}

			} else {
				echo wp_kses_post( '<article><p>' . __( 'There are no FAQ posts assigned to this category', 'lsx-faq' ) . '</p></article>');
			}
		}
	}

	/**
	 * Adds the search form to the top of the archive.
	 */
	public function archive_search_form( ) {
		if ( ! isset( $_GET['fwp_faq_tags'] ) && ! isset( $_GET['fwp_faq_category'] ) && ! isset( $_GET['fwp_faq_search'] )  ) {
			lsx_faq_search();
		}
	}

	/**
	 * Replace the breadcrumbs link
	 * @param $crumbs
	 *
	 * @return mixed
	 */
	public function breadcrumb_links( $crumbs ) {
		if ( is_tax( 'faq-category' ) ) {
			$crumbs[1] = array(
				0 => __( 'FAQ', 'lsx-faq'),
				1  => home_url( '/faq/' ),
			);
		}

		return $crumbs;
	}
	
/**
 * Change the LSX Banners title for the FAQ archive.
 */
public function lsx_banner_archive_title( $title ) {
	if ( is_post_type_archive( 'faq' ) ) {
		$title = '<h1 class="archive-title">' . esc_html__( 'FAQ', 'lsx-faq' ) . '</h1>';
	}

	if ( is_tax( 'faq-category' ) ) {
	$tax = get_queried_object();
		$title = '<h1 class="archive-title">' . apply_filters( 'the_title', $tax->name ) . '</h1>';
	}

	return $title;
}

}//end class
