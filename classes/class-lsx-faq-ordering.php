<?php
namespace lsx;

/**
 * LSX FAQ Ordering Class
 *
 * @package   LSX FAQ
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeed
 */

class LSX_FAQ_Ordering {

	/**
	 * Holds instance of the class
	 */
	private static $instance;

	/**
	 * LSX_FAQ_Ordering constructor.
	 */
	function __construct() {
		if ( ! get_option( 'lsx_faq_scporder_install' ) ) {
			$this->install();
		}

		add_action( 'admin_init', array( $this, 'refresh' ) );
		add_action( 'admin_init', array( $this, 'load_script_css' ) );

		//These are the menu order updaters
		add_action( 'wp_ajax_update-menu-order', array(
			$this,
			'update_menu_order',
		) );
		add_action( 'wp_ajax_update-menu-order-tags', array(
			$this,
			'update_menu_order_tags',
		) );
		add_action( 'save_post', array(
			$this,
			'update_category_ordering',
		) );

		//  These are the query statement
		add_action( 'pre_get_posts', array(
			$this,
			'pre_get_posts',
		) );

		add_filter( 'get_previous_post_where', array(
			$this,
			'previous_post_where',
		) );
		add_filter( 'get_previous_post_sort', array(
			$this,
			'previous_post_sort',
		) );
		add_filter( 'get_next_post_where', array(
			$this,
			'next_post_where',
		) );
		add_filter( 'get_next_post_sort', array(
			$this,
			'next_post_sort',
		) );

		/*add_filter( 'get_terms_orderby', array(
			$this,
			'get_terms_orderby',
		), 10, 3 );*/

		add_filter( 'wp_get_object_terms', array(
			$this,
			'get_object_terms',
		), 10, 4 );
		add_filter( 'get_terms', array(
			$this,
			'get_object_terms',
		), 10, 4 );
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

	function install() {
		global $wpdb;
		$result = $wpdb->query( "DESCRIBE $wpdb->terms `lsx_faq_term_order`" );

		if ( ! $result ) {
			$result = $wpdb->query( "ALTER TABLE $wpdb->terms ADD `lsx_faq_term_order` INT(4) NULL DEFAULT '0'" );
		}

		update_option( 'lsx_faq_scporder_install', 1 );
	}

	function _check_load_script_css() {
		$active = false;

		$objects = $this->get_objects();
		$tags    = $this->get_tags();

		if ( empty( $objects ) && empty( $tags ) ) {
			return false;
		}

		if ( isset( $_GET['orderby'] ) || strstr( sanitize_text_field( $_SERVER['REQUEST_URI'] ), 'action=edit' ) || strstr( sanitize_text_field( $_SERVER['REQUEST_URI'] ), 'wp-admin/post-new.php' ) ) {
			return false;
		}

		if ( ! empty( $objects ) ) {
			if ( isset( $_GET['post_type'] ) && array_key_exists( sanitize_text_field( $_GET['post_type'] ), $objects ) ) { // if page or custom post types
				$active = true;
			}
			if ( ! isset( $_GET['post_type'] ) && strstr( sanitize_text_field( $_SERVER['REQUEST_URI'] ), 'wp-admin/edit.php' ) && array_key_exists( 'post', $objects ) ) { // if post
				$active = true;
			}
		}

		if ( ! empty( $tags ) ) {
			if ( isset( $_GET['taxonomy'] ) && array_key_exists( sanitize_text_field( $_GET['taxonomy'] ), $tags ) ) {
				$active = true;
			}
		}

		return $active;
	}

	function load_script_css() {
		if ( $this->_check_load_script_css() ) {
			wp_enqueue_script( 'lsx-faq-ordering', LSX_FAQ_URL . '/assets/js/src/lsx-faq-ordering.js', array( 'jquery', 'jquery-ui-sortable' ), null, true );

			$scporderjs_params = array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'scporder' ),
			);
			wp_localize_script( 'lsx-faq-ordering', 'scporderjs_params', $scporderjs_params );

			wp_enqueue_style( 'scporder', LSX_FAQ_URL . '/assets/css/scporder.css', array(), null );
			wp_style_add_data( 'scporder', 'rtl', 'replace' );
		}
	}

	function refresh() {
		global $wpdb;
		$objects = $this->get_objects();
		$tags    = $this->get_tags();

		if ( ! empty( $objects ) ) {
			foreach ( $objects as $object => $object_data ) {
				$result = $wpdb->get_results( $wpdb->prepare( "
					SELECT count(*) as cnt, max(menu_order) as max, min(menu_order) as min
					FROM $wpdb->posts
					WHERE post_type = '%s' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
				", $object ) );

				if ( 0 == $result[0]->cnt || $result[0]->cnt == $result[0]->max ) {
					continue;
				}

				$results = $wpdb->get_results( $wpdb->prepare( "
					SELECT ID
					FROM $wpdb->posts
					WHERE post_type = '%s' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
					ORDER BY menu_order ASC
				", $object ) );

				foreach ( $results as $key => $result ) {
					$wpdb->update(
						$wpdb->posts,
						array(
							'menu_order' => $key + 1,
						),
						array(
							'ID' => $result->ID,
						)
					);
				}
			}
		}

		if ( ! empty( $tags ) ) {
			foreach ( $tags as $taxonomy => $taxonomy_data ) {
				$result = $wpdb->get_results( $wpdb->prepare( "
					SELECT count(*) as cnt, max(lsx_faq_term_order) as max, min(lsx_faq_term_order) as min
					FROM $wpdb->terms AS terms
					INNER JOIN $wpdb->term_taxonomy AS term_taxonomy ON ( terms.term_id = term_taxonomy.term_id )
					WHERE term_taxonomy.taxonomy = '%s'
				", $taxonomy ) );

				if ( 0 == $result[0]->cnt || $result[0]->cnt == $result[0]->max ) {
					continue;
				}

				$results = $wpdb->get_results( $wpdb->prepare( "
					SELECT terms.term_id
					FROM $wpdb->terms AS terms
					INNER JOIN $wpdb->term_taxonomy AS term_taxonomy ON ( terms.term_id = term_taxonomy.term_id )
					WHERE term_taxonomy.taxonomy = '%s'
					ORDER BY lsx_faq_term_order ASC
				", $taxonomy ) );

				foreach ( $results as $key => $result ) {
					$wpdb->update(
						$wpdb->terms,
						array(
							'lsx_faq_term_order' => $key + 1,
						),
						array(
							'term_id' => $result->term_id,
						)
					);
				}
			}
		}
	}

	function update_menu_order() {

		global $wpdb;

		parse_str( sanitize_text_field( $_POST['order'] ), $data );

		if ( ! is_array( $data ) ) {
			return false;
		}

		$current_term = false;
		if ( isset( $_POST['term'] ) ) {
			$current_term = sanitize_text_field( $_POST['term'] );
		}

		$id_arr = array();

		foreach ( $data as $key => $values ) {
			foreach ( $values as $position => $id ) {
				$id_arr[] = $id;
			}
		}

		$menu_order_arr = array();

		//If we are just ordering posts
		if ( false === $current_term || 'false' === $current_term ) {
			foreach ( $id_arr as $key => $id ) {
				$results = $wpdb->get_results( "SELECT menu_order FROM $wpdb->posts WHERE ID = " . intval( $id ) );
				foreach ( $results as $result ) {
					$menu_order_arr[] = $result->menu_order;
				}
			}

			sort( $menu_order_arr );

			foreach ( $data as $key => $values ) {
				foreach ( $values as $position => $id ) {
					$wpdb->update(
						$wpdb->posts,
						array(
							'menu_order' => $menu_order_arr[ $position ],
						),
						array(
							'ID' => intval( $id ),
						)
					);
				}
			}
		} else {
			//Order by the category term.

			foreach ( $data as $key => $values ) {
				foreach ( $values as $position => $id ) {
					$old_value = get_post_meta( $id, 'menu_order_' . $current_term, true );
					update_post_meta( $id, 'menu_order_' . $current_term, $position, $old_value );
				}
			}
		}
		die();
	}

	function update_menu_order_tags() {
		//check_ajax_referer( 'scporder', 'security' );
		global $wpdb;

		parse_str( sanitize_text_field( $_POST['order'] ), $data );

		if ( ! is_array( $data ) ) {
			return false;
		}

		/*$id_arr = array();

		foreach ( $data as $key => $values ) {
			foreach ( $values as $position => $id ) {
				$id_arr[] = $id;
			}
		}
		$menu_order_arr = array();

		foreach ( $id_arr as $key => $id ) {
			$results = $wpdb->get_results( "SELECT lsx_faq_term_order FROM $wpdb->terms WHERE term_id = " . intval( $id ) );

			print_r($results);
			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					$menu_order_arr[] = $result->lsx_faq_term_order;
				}
			} else {
				$menu_order_arr[] = [0];
			}
		}

		sort( $menu_order_arr );*/

		foreach ( $data as $key => $values ) {
			foreach ( $values as $position => $id ) {

				delete_term_meta( $id, 'lsx_faq_term_order' );
				add_term_meta( $id, 'lsx_faq_term_order', (int) ( $position + 1 ) );

				/*$wpdb->update(
					$wpdb->terms,
					array(
						'lsx_faq_term_order' => (int) $position + 1,
					),
					array(
						'term_id' => intval( $id ),
					)
				);*/
			}
		}
		die('complete');
	}

	function previous_post_where( $where ) {
		global $post;
		$objects = $this->get_objects();

		if ( empty( $objects ) ) {
			return $where;
		}

		if ( isset( $post->post_type ) && array_key_exists( $post->post_type, $objects ) ) {
			$current_menu_order = $post->menu_order;
			$where              = "WHERE p.menu_order > '" . $current_menu_order . "' AND p.post_type = '" . $post->post_type . "' AND p.post_status = 'publish'";
		}

		return $where;
	}

	function previous_post_sort( $orderby ) {
		global $post;
		$objects = $this->get_objects();

		if ( empty( $objects ) ) {
			return $orderby;
		}

		if ( isset( $post->post_type ) && array_key_exists( $post->post_type, $objects ) ) {
			$orderby = 'ORDER BY p.menu_order ASC LIMIT 1';
		}

		return $orderby;
	}

	function next_post_where( $where ) {
		global $post;
		$objects = $this->get_objects();

		if ( empty( $objects ) ) {
			return $where;
		}

		if ( isset( $post->post_type ) && array_key_exists( $post->post_type, $objects ) ) {
			$current_menu_order = $post->menu_order;
			$where              = "WHERE p.menu_order < '" . $current_menu_order . "' AND p.post_type = '" . $post->post_type . "' AND p.post_status = 'publish'";
		}

		return $where;
	}

	function next_post_sort( $orderby ) {
		global $post;
		$objects = $this->get_objects();

		if ( empty( $objects ) ) {
			return $orderby;
		}

		if ( isset( $post->post_type ) && array_key_exists( $post->post_type, $objects ) ) {
			$orderby = 'ORDER BY p.menu_order DESC LIMIT 1';
		}

		return $orderby;
	}

	function pre_get_posts( $wp_query ) {
		$objects = $this->get_objects();

		if ( empty( $objects ) ) {
			return false;
		}

		if ( is_admin() ) {

			if ( isset( $wp_query->query['post_type'] ) && ! isset( $_GET['orderby'] ) && array_key_exists( $wp_query->query['post_type'], $objects ) ) {

				//Check if we need to order by the FAQ category
				if ( isset( $wp_query->query['faq-category'] ) ) {
					$wp_query->set( 'orderby', 'meta_value_num' );
					$wp_query->set( 'order', 'ASC' );
					$wp_query->set( 'meta_key', 'menu_order_' . $wp_query->query['faq-category'] );
					$wp_query->set( 'posts_per_page', -1 );

				} else {
					$wp_query->set( 'orderby', 'menu_order' );
					$wp_query->set( 'order', 'ASC' );
				}
			}
		} else {
			$active = false;
			$is_taxonomy = false;
			$taxonomy_slug = '';

			if ( isset( $wp_query->query['post_type'] ) ) {
				if ( ! is_array( $wp_query->query['post_type'] ) ) {
					if ( array_key_exists( $wp_query->query['post_type'], $objects ) ) {
						$active = true;
					}
				}
			} else {
				if ( array_key_exists( 'post', $objects ) ) {
					$active = true;
				}
			}

			//Check if there is a taxonomy active
			$tags = $this->get_tags();
			if ( ! empty( $tags ) ) {
				foreach ( $tags as $tag ) {
					if ( isset( $wp_query->query[ $tag ] ) ) {
						$is_taxonomy = true;
						$taxonomy_slug = $wp_query->query[ $tag ];
						$active = true;
					}
				}
			}

			if ( ! $active ) {
				return false;
			}

			if ( isset( $wp_query->query['disabled_custom_post_order'] ) ) {
				return false;
			}

			//Check if its a term archive,  if so use the normal ordering.

			if ( isset( $wp_query->query['suppress_filters'] ) ) {
				if ( $wp_query->get( 'orderby' ) == 'date' ) {

					if ( false === $is_taxonomy) {
						$wp_query->set( 'orderby', 'menu_order' );
					} else {
						$wp_query->set( 'meta_key', 'menu_order_' . $taxonomy_slug );
						$wp_query->set( 'orderby', 'meta_value_num' );
					}
				}

				if ( $wp_query->get( 'order' ) == 'DESC' ) {
					$wp_query->set( 'order', 'ASC' );
				}
			} else {
				if ( ! $wp_query->get( 'orderby' ) ) {
					//Check if we need to use the custom field to order.
					if ( false === $is_taxonomy) {
						$wp_query->set( 'orderby', 'menu_order' );
					} else {
						$wp_query->set( 'meta_key', 'menu_order_' . $taxonomy_slug );
						$wp_query->set( 'orderby', 'meta_value_num' );
					}
				}

				if ( ! $wp_query->get( 'order' ) ) {
					$wp_query->set( 'order', 'ASC' );
				}
			}
		}
	}

	function get_terms_orderby( $orderby, $args ) {
		/*if ( is_admin() ) {
			return $orderby;
		}*/

		if ( isset( $args['disabled_custom_post_order'] ) ) {
			return $orderby;
		}

		$tags = $this->get_tags();

		if ( ! isset( $args['taxonomy'] ) ) {
			return $orderby;
		}

		$taxonomy = $args['taxonomy'];
		if ( is_array( $taxonomy ) && count( $taxonomy ) == 1 ) {
			$taxonomy = $taxonomy[0];
		}
		if ( ! is_array( $taxonomy ) && ! array_key_exists( $taxonomy, $tags ) ) {
			return $orderby;
		}

		$orderby = 't.lsx_faq_term_order';

		return $orderby;
	}

	function get_object_terms( $terms, $not_used, $args_1, $args_2 = null ) {
		$tags = $this->get_tags();

		if ( isset( $_GET['orderby'] ) ) {
			return $terms;
		}

		$args = is_null( $args_2 ) || ! is_array( $args_2 ) ? $args_1 : $args_2;

		if ( isset( $args['disabled_custom_post_order'] ) ) {
			return $terms;
		}

		foreach ( $terms as $key => $term ) {
			if ( is_object( $term ) && isset( $term->taxonomy ) ) {
				$taxonomy = $term->taxonomy;
				if ( ! array_key_exists( $taxonomy, $tags ) ) {
					return $terms;
				}
			} else {
				return $terms;
			}
		}

		usort( $terms, array( $this, 'taxcmp' ) );

		return $terms;
	}

	function taxcmp( $a, $b ) {
		if ( (int) $a->lsx_faq_term_order == (int) $b->lsx_faq_term_order ) {
			return 0;
		}

		return ( (int) $a->lsx_faq_term_order < (int) $b->lsx_faq_term_order ) ? - 1 : 1;
	}

	function get_objects() {
		return array( 'faq' => 'faq' );
	}

	function get_tags() {
		return array( 'faq-category' => 'faq-category' );
	}

	/**
	 * Saves a menu order if there is none
	 * @param $post_id
	 *
	 * @return bool
	 */
	function update_category_ordering( $post_id ) {

		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) )
			return false;

		//If this is not one of our designated posts.
		$objects = $this->get_objects();
		if ( empty( $objects ) || ! array_key_exists( get_post_type( $post_id ), $objects ) ) {
			return false;
		}

		//If there are no taxonomies we must work with then escape.
		$taxonomies = $this->get_tags();
		if ( empty( $taxonomies ) ) {
			return false;
		} else {
			$valid_terms = array();

			$terms = wp_get_object_terms( $post_id, $taxonomies, array(
				'fields' => 'slugs'
			) );

			if ( false !== $terms && ! empty( $terms ) ) {
				foreach ( $terms as $slug ) {
					$old_value = get_post_meta( $post_id, 'menu_order_' . $slug, true );
					if ( false === $old_value || '' === $old_value) {
						add_post_meta( $post_id, 'menu_order_' . $slug, '0', true );
					}
				}
			}
		}
	}
}
