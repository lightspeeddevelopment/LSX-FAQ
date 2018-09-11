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
			'singular_name'     => esc_html_x( 'Doc Category', 'taxonomy singular name', 'lsx-faq' ),
			'search_items'      => esc_html__( 'Search Doc Categories', 'lsx-faq' ),
			'all_items'         => esc_html__( 'All FAQ', 'lsx-faq' ),
			'parent_item'       => esc_html__( 'Parent FAQ', 'lsx-faq' ),
			'parent_item_colon' => esc_html__( 'Parent FAQ:', 'lsx-faq' ),
			'edit_item'         => esc_html__( 'Edit FAQ', 'lsx-faq' ),
			'update_item'       => esc_html__( 'Update FAQ', 'lsx-faq' ),
			'add_new_item'      => esc_html__( 'Add New', 'lsx-faq' ),
			'new_item_name'     => esc_html__( 'New FAQ Name', 'lsx-faq' ),
			'menu_name'         => esc_html__( 'FAQ Category', 'lsx-faq' ),
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

	/**
	 * Add metabox with custom fields to the FAQ post type
	 */
	public function field_setup( $meta_boxes ) {
		$prefix = 'lsx_faq_';

		$fields = array(
			array(
				'name' => esc_html__( 'Featured:', 'lsx-faq' ),
				'id'   => $prefix . 'featured',
				'type' => 'checkbox',
			),
			array(
				'name' => esc_html__( 'URL for the related Woocommerce Product:', 'lsx-faq' ),
				'id'   => $prefix . 'url',
				'type' => 'text',
			),
		);

            $group_fields = array(
            array(
                'name' => esc_html__( 'Question:', 'lsx-faq' ),
                'id'   => 'faqquestion',
                'type' => 'textarea',
            ),
            array(
                'name' => esc_html__( 'Answer:', 'lsx-faq' ),
                'id'   => 'faqanswer',
                'type' => 'textarea',
            ),
        );        
		// $fields[] = array(
		// 	'name' => esc_html__( 'FAQ:', 'lsx-faq' ),
		// 	'id' => 'faq_to_faq',
		// 	'type' => 'post_select',
		// 	'use_ajax' => false,
		// 	'query' => array(
		// 		'post_type' => 'faq',
		// 		'nopagin' => true,
		// 		'posts_per_page' => '50',
		// 		'orderby' => 'title',
		// 		'order' => 'ASC',
		// 	),
		// 	'repeatable' => true,
		// 	'allow_none' => true,
		// 	'cols' => 12,
		// );

		if ( class_exists( 'LSX_Services' ) ) {
			$fields[] = array(
				'name' => esc_html__( 'Services related to this faq:', 'lsx-faq' ),
				'id' => 'service_to_faq',
				'type' => 'post_select',
				'use_ajax' => false,
				'query' => array(
					'post_type' => 'service',
					'nopagin' => true,
					'posts_per_page' => '50',
					'orderby' => 'title',
					'order' => 'ASC',
				),
				'repeatable' => true,
				'allow_none' => true,
				'cols' => 12,
			);
		}

		if ( class_exists( 'LSX_Testimonials' ) ) {
			$fields[] = array(
				'name' => esc_html__( 'Testimonials related to this faq:', 'lsx-faq' ),
				'id' => 'testimonial_to_faq',
				'type' => 'post_select',
				'use_ajax' => false,
				'query' => array(
					'post_type' => 'testimonial',
					'nopagin' => true,
					'posts_per_page' => '50',
					'orderby' => 'title',
					'order' => 'ASC',
				),
				'repeatable' => true,
				'allow_none' => true,
				'cols' => 12,
			);
		}

		if ( class_exists( 'LSX_Team' ) ) {
			$fields[] = array(
				'name' => esc_html__( 'Team members involved with this faq:', 'lsx-faq' ),
				'id' => 'team_to_faq',
				'type' => 'post_select',
				'use_ajax' => false,
				'query' => array(
					'post_type' => 'team',
					'nopagin' => true,
					'posts_per_page' => '50',
					'orderby' => 'title',
					'order' => 'ASC',
				),
				'repeatable' => true,
				'allow_none' => true,
				'cols' => 12,
			);
		}


		if ( class_exists( 'woocommerce' ) ) {
			$fields[] = array(
				'name' => esc_html__( 'Products used for this faq:', 'lsx-faq' ),
				'id' => 'product_to_faq',
				'type' => 'post_select',
				'use_ajax' => false,
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
		}
		
	$fields[] =    array(
            'id'         => 'gp',
            'name'       => 'FAQ',
            'type'       => 'group',
            'repeatable' => true,
            'sortable'   => true,
            'fields'     => $group_fields,
            'desc'       => 'This is the group description.',
        );    

		$meta_boxes[] = array(
			'title'  => esc_html__( 'FAQ Details', 'lsx-faq' ),
			'pages'  => 'faq',
			'fields' => $fields,
		);

		return $meta_boxes;
	}

	/**
	 * Sets up the "post relations".
	 */
	public function post_relations( $post_id, $field, $value ) {
		$connections = array(
			// 'faq_to_faq',

			'faq_to_service',
			'service_to_faq',

			'faq_to_testimonial',
			'testimonial_to_faq',

			'faq_to_team',
			'team_to_faq',
		);

		if ( in_array( $field['id'], $connections ) ) {
			$this->save_related_post( $connections, $post_id, $field, $value );
		}
	}

	/**
	 * Save the reverse post relation.
	 */
	public function save_related_post( $connections, $post_id, $field, $value ) {
		$ids = explode( '_to_', $field['id'] );
		$relation = $ids[1] . '_to_' . $ids[0];

		if ( in_array( $relation, $connections ) ) {
			$previous_values = get_post_meta( $post_id, $field['id'], false );

			if ( ! empty( $previous_values ) ) {
				foreach ( $previous_values as $v ) {
					delete_post_meta( $v, $relation, $post_id );
				}
			}

			if ( is_array( $value ) ) {
				foreach ( $value as $v ) {
					if ( ! empty( $v ) ) {
						add_post_meta( $v, $relation, $post_id );
					}
				}
			}
		}
	}

}//end class
