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
		add_action( 'init', array( $this, 'post_type_setup' ) );
		add_action( 'init', array( $this, 'taxonomy_setup' ) );
		add_action( 'init', array( $this, 'product_taxonomy_setup' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'register_wc_custom_field' ), 20 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_wc_custom_field' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'register_wc_custom_faq_terms_field' ), 20 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_wc_term_custom_field' ) );

		//Creates the custom meta boxs for the faqs		
		add_filter( 'cmb_meta_boxes', array( $this, 'field_setup' ), 1, 20 );
		add_action( 'cmb_save_custom', array( $this, 'post_relations' ), 3, 20 );

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
			'singular_name'     => esc_html_x( 'FAQ Category', 'taxonomy singular name', 'lsx-faq' ),
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
			'name'              => esc_html_x( 'Tags', 'taxonomy general name', 'lsx-faq' ),
			'singular_name'     => esc_html_x( 'Tag', 'taxonomy singular name', 'lsx-faq' ),
			'search_items'      => esc_html__( 'Search Tags', 'lsx-faq' ),
			'all_items'         => esc_html__( 'All Tags', 'lsx-faq' ),
			'parent_item'       => esc_html__( 'Parent Tag', 'lsx-faq' ),
			'parent_item_colon' => esc_html__( 'Parent Tag:', 'lsx-faq' ),
			'edit_item'         => esc_html__( 'Edit FAQ', 'lsx-faq' ),
			'update_item'       => esc_html__( 'Update Tag', 'lsx-faq' ),
			'add_new_item'      => esc_html__( 'Add New', 'lsx-faq' ),
			'new_item_name'     => esc_html__( 'New Tag Name', 'lsx-faq' ),
			'menu_name'         => esc_html__( 'Tags', 'lsx-faq' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'faq-tags',
			),
		);

		register_taxonomy( 'faq-tags', array( 'faq' ), $args );
	}

	/**
	 * Display the custom text field
	 * @since 1.0.0
	 */
	function register_wc_custom_field() {

		$faq_posts = new \WP_Query(
			array(
				'post_type' => 'faq',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'nopagin' => true,
			)
		);
		$options = array();
		if ( $faq_posts->have_posts() ) {
			foreach ( $faq_posts->posts as $faq_post ) {
				$options[ $faq_post->ID ] = $faq_post->post_title;
			}
		} else {
			$options[ 0 ] = __( 'Please add FAQ posts', 'lsx-faq' );
		}

		$args = array(
			'id' => 'lsx_faq_posts',
			'name' => 'lsx_faq_posts[]',
			'label' => __( 'FAQ', 'lsx-faq' ),
			'class' => 'lsx-faq-custom-field',
			'desc_tip' => true,
			'description' => __( 'Select the FAQ posts related to this product', 'lsx-faq' ),
			'options' => $options
		);
		//woocommerce_wp_text_input( $args );

		woocommerce_wp_select_multiple( $args );
	}

	/**
	* Save the custom field
	* @since 1.0.0
	*/
	function save_wc_custom_field( $post_id ) {
		$product = wc_get_product( $post_id );
		$title = isset( $_POST['lsx_faq_posts'] ) ? $_POST['lsx_faq_posts'] : '';

		//Grab all of the posts and save them to the corresponding FAQ post
		$previous_faq_posts = get_post_meta( $post_id, 'lsx_faq_posts', true );

		$faq_to_remove_from = array();
		$faq_to_add_to = array();
		//Look for posts to remove
		if ( ! empty( $previous_faq_posts ) && '' !== $previous_faq_posts ) {

			//If the current posts to save are empty then it mean we are deleting all of the previous ones.
			if ( ! empty( $title ) && '' !== $title ) {
				$faq_to_remove_from = array_diff( $previous_faq_posts, $title );
				$faq_to_add_to = array_diff( $title, $previous_faq_posts );
				if ( empty( $faq_to_add_to ) ) {
					$faq_to_add_to = $title;
				}
			} else { //Otherwise find the ones to delete.
				$faq_to_remove_from = $previous_faq_posts;
				$faq_to_add_to = $title;
			}
		} else {
			$faq_to_add_to = $title;
		}

		//Run through and remove the items.
		if ( ! empty( $faq_to_remove_from ) && '' !== $faq_to_remove_from ) {
			if ( ! is_array( $faq_to_remove_from ) ) {
				$faq_to_remove_from = array( $faq_to_remove_from );
			}
			foreach ( $faq_to_remove_from as $faq_post ) {
				delete_post_meta( $faq_post, 'faq_to_product', $post_id );
			}
		}

		if ( ! empty( $faq_to_add_to ) && '' !== $faq_to_add_to ) {
			if ( ! is_array( $faq_to_add_to ) ) {
				$faq_to_add_to = array( $faq_to_add_to );
			}
			foreach ( $faq_to_add_to as $faq_post_to_add ) {
				$current_product = get_post_meta( $faq_post_to_add, 'faq_to_product', false );
				if ( is_array( $current_product ) && ! empty( $current_product ) && in_array( $post_id, $current_product ) ) {
					continue;
				}
				add_post_meta( $faq_post_to_add, 'faq_to_product', $post_id, false );
			}
		}

		//Update the product Meta
		$product->update_meta_data( 'lsx_faq_posts', $title );
		$product->save();
	}

    /**
     * Display the custom term text field
     * @since 1.0.0
     */
    function register_wc_custom_faq_terms_field() {

		$term_args = array(
			'taxonomy' => 'faq-category',
			'hide_empty' => false,
			'orderby' => 'name',
			'order' => 'ASC'
		);

		$options = array();
		$tax_terms = get_terms($term_args);
		if( ! empty( $tax_terms ) ) {
			foreach ( $tax_terms as $tax_term ) {
				$options[ $tax_term->slug ] = $tax_term->name;
			}
		} else {
			$options[ 0 ] = __( 'Please add FAQ terms', 'lsx-faq' );
		}

        $args = array(
            'id' => 'lsx_faq_categories',
            'name' => 'lsx_faq_categories[]',
            'label' => __( 'FAQ Categories', 'lsx-faq' ),
            'class' => 'lsx-faq-custom-field',
            'desc_tip' => true,
            'description' => __( 'Select the categories related to this product', 'lsx-faq' ),
            'options' => $options
        );
        //woocommerce_wp_text_input( $args );

        woocommerce_wp_select_multiple( $args );
    }

	/**
	* Save the term custom field
	* @since 1.0.0
	*/
	function save_wc_term_custom_field( $post_id ) {
		$product = wc_get_product( $post_id );
		$title = isset( $_POST['lsx_faq_categories'] ) ? $_POST['lsx_faq_categories'] : '';
		$product->update_meta_data( 'lsx_faq_categories', $title );
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


		/**
	 * Add metabox with custom fields to the faq post type
	 */
	public function field_setup( $meta_boxes ) {
		
		 $fields[] = array(
		 	'name' => esc_html__( 'Product:', 'lsx-faq' ),
		 	'id' => 'faq_to_product',
		 	'type' => 'post_select',
		 	'use_ajax' => true,
		 	'query' => array(
		 		'post_type' => 'product',
		 		'nopagin' => true,
		 		'posts_per_page' => '50',
		 		'orderby' => 'title',
		 		'order' => 'ASC',
		 	),
		 	'repeatable' => true,
		 	'allow_none' => true,
		 	'cols' => 12,
		 );


		$meta_boxes[] = array(
			'title'  => esc_html__( 'Product Related to this FAQ', 'lsx-faq' ),
			'pages'  => 'faq',
			'fields' => $fields,
		);

		return $meta_boxes;
	}

	/**
	 * Sets up the "post relations".
	 */
	public function post_relations( $post_id, $field, $values ) {
		$connections = array(
			 'faq_to_product',
		);

		if ( in_array( $field['id'], $connections ) ) {

			$faq_to_remove_from = array();
			$faq_to_add_to = array();

			$previous_values = get_post_meta( $post_id, $field['id'], false );
			//If we are adding a new field.
			if ( ! empty( $previous_values )  ) {

				if ( ! empty( $values ) ) {
					$faq_to_remove_from = array_diff( $previous_values, $values );
					$faq_to_add_to = array_diff( $values, $previous_values );
				} else {
					$faq_to_add_to = $values;
					$faq_to_remove_from = $previous_values;
				}

			} else if ( ! empty( $values ) ) {
				$faq_to_add_to = $values;
			}

			//Run through and remove the items.
			if ( ! empty( $faq_to_remove_from ) && '' !== $faq_to_remove_from ) {
				if ( ! is_array( $faq_to_remove_from ) ) {
					$faq_to_remove_from = array( $faq_to_remove_from );
				}
				foreach ( $faq_to_remove_from as $faq_post ) {

					$previous_faq_posts = get_post_meta( $faq_post, 'lsx_faq_posts', true );
					if ( ! empty( $previous_faq_posts ) && '' !== $previous_faq_posts  ) {
						$temp = $previous_faq_posts;
						foreach( $previous_faq_posts as $pfp_key => $pfp_value ) {
							if ( $pfp_value == $post_id ) {
								unset( $temp[ $pfp_key ] );
							}
						}
						update_post_meta( $faq_post, 'lsx_faq_posts', $temp, $previous_faq_posts );
					}
				}
			}


			if ( ! empty( $faq_to_add_to ) && '' !== $faq_to_add_to ) {
				if ( ! is_array( $faq_to_add_to ) ) {
					$faq_to_add_to = array( $faq_to_add_to );
				}
				foreach ( $faq_to_add_to as $faq_post_to_add ) {
					$current_products = get_post_meta( $faq_post_to_add, 'lsx_faq_posts', true );
					$previous_products = $current_products;
					if ( is_array( $current_products ) && ! empty( $current_products ) && in_array( $post_id, $current_products ) ) {
						continue;
					}

					if ( ! is_array( $current_products ) ) {
						$current_products = array( $current_products );
					}
					$current_products[] = $post_id;
					update_post_meta( $faq_post_to_add, 'lsx_faq_posts', $current_products, $previous_products );
				}
			}
		}
	}

}
//end class