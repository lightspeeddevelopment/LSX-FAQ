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
	 * Holds instance of the class
	 */
	public $taxonomies = array( 'faq-category' => 'faq-category' );

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_tabs', array( $this, 'my_simple_custom_product_tab' ) );
		add_action( 'init', array( $this, 'post_type_setup' ) );
		add_action( 'init', array( $this, 'taxonomy_setup' ) );
		add_action( 'init', array( $this, 'product_taxonomy_setup' ) );

		//add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

		add_action( 'init', array( $this, 'woo_new_product_tab_content' ) );

		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'register_wc_custom_field' ), 20 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_wc_custom_field' ) );

		//Handles the saving of the term image
		add_action( 'create_term', array( $this, 'save_meta' ), 20, 2 );
		add_action( 'edit_term', array( $this, 'save_meta' ), 20, 2 );

		foreach ( array_keys( $this->taxonomies ) as $taxonomy ) {
			add_action( "{$taxonomy}_edit_form_fields", array( $this, 'add_thumbnail_form_field' ), 3, 1 );
		}

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
				'custom-fields',
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

	/**
	 * Display the custom text field
	 * @since 1.0.0
	 */
	function register_wc_custom_field() {
		$args = array(
			'id' => 'lsx_faq_posts',
			'label' => __( 'FAQ', 'lsx-faq' ),
			'class' => 'lsx-faq-custom-field',
			'desc_tip' => true,
			'description' => __( 'Select the FAQ posts related to this product', 'lsx-faq' ),
		);
		woocommerce_wp_text_input( $args );
	}

	/**
	* Save the custom field
	* @since 1.0.0
	*/
	function save_wc_custom_field( $post_id ) {
		$product = wc_get_product( $post_id );
		$title = isset( $_POST['lsx_faq_posts'] ) ? $_POST['lsx_faq_posts'] : '';
		$product->update_meta_data( 'lsx_faq_posts', sanitize_text_field( $title ) );
		$product->save();
	}



	//This is the featured image functions
	/**
	 * Output the form field for this metadata when adding a new term
	 *
	 * @since 0.1.0
	 */
	public function add_thumbnail_form_field( $term = false ) {
		if ( is_object( $term ) ) {
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

				$image_preview = '<img src="' . $image_preview[0] . '" width="' . $width . '" height="' . $height . '" class="alignnone size-thumbnail wp-image-' . $value . '" />';
			}
		} else {
			$image_preview = false;
			$value         = false;
		}
		?>
		<tr class="form-field form-required term-thumbnail-wrap">
			<th scope="row"><label for="thumbnail"><?php esc_html_e( 'Featured Image', 'tour-operator' ); ?></label></th>
			<td>
				<input class="input_image_id" type="hidden" name="faq_thumbnail" value="<?php echo wp_kses_post( $value ); ?>">
				<div class="thumbnail-preview">
					<?php echo wp_kses_post( $image_preview ); ?>
				</div>
				<a style="<?php if ( '' !== $value && false !== $value ) { ?>display:none;<?php } ?>" class="button-secondary lsx-thumbnail-image-add"><?php esc_html_e( 'Choose Image', 'tour-operator' ); ?></a>
				<a style="<?php if ( '' === $value || false === $value ) { ?>display:none;<?php } ?>" class="button-secondary lsx-thumbnail-image-remove"><?php esc_html_e( 'Remove Image', 'tour-operator' ); ?></a>
				<?php wp_nonce_field( 'lsx_faq_save_term_thumbnail', 'lsx_faq_term_thumbnail_nonce' ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Saves the Taxnomy term banner image
	 *
	 * @since 0.1.0
	 *
	 * @param  int    $term_id
	 * @param  string $taxonomy
	 */
	public function save_meta( $term_id = 0, $taxonomy = '' ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['faq_thumbnail'] ) ) {
			return;
		}

		if ( check_admin_referer( 'lsx_faq_save_term_thumbnail', 'lsx_faq_term_thumbnail_nonce' ) ) {
			if ( ! isset( $_POST['faq_thumbnail'] ) ) {
				return;
			}

			$thumbnail_meta = sanitize_text_field( $_POST['faq_thumbnail'] );
			$thumbnail_meta = ! empty( $thumbnail_meta ) ? $thumbnail_meta : '';

			if ( empty( $thumbnail_meta ) ) {
				delete_term_meta( $term_id, 'thumbnail' );
			} else {
				update_term_meta( $term_id, 'thumbnail', $thumbnail_meta );
			}
		}
	}

}
//end class