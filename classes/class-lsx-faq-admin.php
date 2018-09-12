<?php
namespace lsx;

/**
 * LSX FAQ Admin Class
 *
 * @package   LSX FAQ
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeed
 */
class LSX_FAQ_Admin 
{


	/**
	 * Holds instance of the class
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_tabs', array( $this, 'my_simple_custom_product_tab' ) );
		add_action( 'init', array( $this, 'post_type_setup' ) );
		add_action( 'init', array( $this, 'taxonomy_setup' ) );
		add_action( 'init', array( $this, 'product_taxonomy_setup' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'init', array( $this, 'woo_new_product_tab_content' ) );

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
	 * Register the FAQ and Product Tag post type
	 */
	public function post_type_setup() {
		$labels = array(
			'name'               => esc_html_x( 'FAQ', 'post type general name', 'lsx-faq' ),
			'singular_name'      => esc_html_x( 'FAQ', 'post type singular name', 'lsx-faq' ),
			'add_new'            => esc_html_x( 'Add New', 'post type general name', 'lsx-faq' ),
			'add_new_item'       => esc_html__( 'Add New FAQ', 'lsx-faq' ),
			'edit_item'          => esc_html__( 'Edit FAQ', 'lsx-faq' ),
			'new_item'           => esc_html__( 'New FAQ', 'lsx-faq' ),
			'all_items'          => esc_html__( 'All FAQ', 'lsx-faq' ),
			'view_item'          => esc_html__( 'View FAQ', 'lsx-faq' ),
			'search_items'       => esc_html__( 'Search FAQ', 'lsx-faq' ),
			'not_found'          => esc_html__( 'No faq found', 'lsx-faq' ),
			'not_found_in_trash' => esc_html__( 'No faq found in Trash', 'lsx-faq' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html_x( 'FAQ', 'admin menu', 'lsx-faq' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-welcome-write-blog',
			'query_var'          => true,
			'rewrite'            => array(
				'slug' => 'faq',
			),
			'capability_type'    => 'post',
			'has_archive'        => 'faq',
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
			),
		);

		register_post_type( 'faq', $args );
	}

	/**
	 * Register the FAQ Category taxonomy
	 */
	public function taxonomy_setup() {
		$labels = array(
			'name'              => esc_html_x( 'FAQ Categories', 'taxonomy general name', 'lsx-faq' ),
			'singular_name'     => esc_html_x( 'FAQ Cateogry', 'taxonomy singular name', 'lsx-faq' ),
			'search_items'      => esc_html__( 'Search FAQ', 'lsx-faq' ),
			'all_items'         => esc_html__( 'All FAQ', 'lsx-faq' ),
			'parent_item'       => esc_html__( 'Parent FAQ', 'lsx-faq' ),
			'parent_item_colon' => esc_html__( 'Parent FAQ:', 'lsx-faq' ),
			'edit_item'         => esc_html__( 'Edit FAQ', 'lsx-faq' ),
			'update_item'       => esc_html__( 'Update FAQ', 'lsx-faq' ),
			'add_new_item'      => esc_html__( 'Add New', 'lsx-faq' ),
			'new_item_name'     => esc_html__( 'New FAQ', 'lsx-faq' ),
			'menu_name'         => esc_html__( 'Category', 'lsx-faq' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'faq-category',
			),
		);

		register_taxonomy( 'faq-category', array( 'faq' ), $args );
	}

	/**
	 * Register the Product tag taxonomy
	 */
	public function product_taxonomy_setup() {
		$labels = array(
			'name'              => esc_html_x( 'Product Tags', 'taxonomy general name', 'lsx-faq' ),
			'singular_name'     => esc_html_x( 'Product Tag', 'taxonomy singular name', 'lsx-faq' ),
			'search_items'      => esc_html__( 'Search Product Tags', 'lsx-faq' ),
			'all_items'         => esc_html__( 'All Product Tags', 'lsx-faq' ),
			'parent_item'       => esc_html__( 'Parent Product Tags', 'lsx-faq' ),
			'parent_item_colon' => esc_html__( 'Parent Product Tags:', 'lsx-faq' ),
			'edit_item'         => esc_html__( 'Edit FAQ', 'lsx-faq' ),
			'update_item'       => esc_html__( 'Update Product Tags', 'lsx-faq' ),
			'add_new_item'      => esc_html__( 'Add New', 'lsx-faq' ),
			'new_item_name'     => esc_html__( 'New Product Tag Name', 'lsx-faq' ),
			'menu_name'         => esc_html__( 'Product Tags', 'lsx-faq' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'product-tags',
			),
		);

		register_taxonomy( 'product-tags', array( 'faq' ), $args );
	}

	public function assets() {
		// wp_enqueue_media();.
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_script( 'lsx-faq-admin', LSX_FAQ_URL . 'assets/js/lsx-faq-admin.min.js', array( 'jquery' ), LSX_FAQ_VER, true );
		wp_enqueue_style( 'lsx-faq-admin', LSX_FAQ_URL . 'assets/css/lsx-faq-admin.css', array(), LSX_FAQ_VER );
	}

}
//end class