<?php
/**
 * Manage everything related to Post Types in SM.
 *
 * @package SM/Core
 */

defined( 'ABSPATH' ) or die;

/**
 * Class made to replace old functions for registering post types and taxonomies.
 *
 * @since 2.7
 */
class SM_Post_Types {
	/**
	 * Hooks into WordPress filtering functions.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
		add_action( 'sm_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {
		if ( ! is_blog_installed() ) {
			return;
		}

		if ( taxonomy_exists( 'wpfc_preacher' ) ) {
			return;
		}

		do_action( 'sm_register_taxonomy' );

		$permalinks = sm_get_permalink_structure();

		$capabilities = array(
			'manage_terms' => 'manage_wpfc_categories',
			'edit_terms'   => 'manage_wpfc_categories',
			'delete_terms' => 'manage_wpfc_categories',
			'assign_terms' => 'manage_wpfc_categories',
		);

		// The labels with their defaults in the singular lowercase form.
		$labels = array(
			'wpfc_preacher'     => SermonManager::getOption( 'preacher_label' ) ? strtolower( SermonManager::getOption( 'preacher_label' ) ) : __( 'Preacher', 'audiopod-wp' ),
			'wpfc_service_type' => SermonManager::getOption( 'service_type_label' ) ? strtolower( SermonManager::getOption( 'service_type_label' ) ) : __( 'Service Type', 'audiopod-wp' ),
		);

		register_taxonomy(
			'wpfc_preacher',
			apply_filters( 'sm_taxonomy_objects_wpfc_preacher', array( 'wpfc_sermon' ) ),
			apply_filters(
				'sm_taxonomy_args_wpfc_preacher',
				array(
					'hierarchical' => false,
					'label'        => ucwords( $labels['wpfc_preacher'] ),
					'labels'       => array(
						'name'              => ucwords( $labels['wpfc_preacher'] . 's' ),
						'singular_name'     => ucwords( $labels['wpfc_preacher'] ),
						'menu_name'         => ucwords( $labels['wpfc_preacher'] . 's' ),
						/* translators: %s preacher */
						'search_items'      => wp_sprintf( __( 'Search %s', 'audiopod-wp' ), $labels['wpfc_preacher'] ),
						/* translators: %s preacher */
						'all_items'         => wp_sprintf( __( 'All %s', 'audiopod-wp' ), $labels['wpfc_preacher'] ),
						'parent_item'       => null,
						'parent_item_colon' => null,
						/* translators: %s preacher */
						'edit_item'         => wp_sprintf( __( 'Edit %s', 'audiopod-wp' ), $labels['wpfc_preacher'] ),
						/* translators: %s preacher */
						'update_item'       => wp_sprintf( __( 'Update %s', 'audiopod-wp' ), $labels['wpfc_preacher'] ),
						/* translators: %s preacher */
						'add_new_item'      => wp_sprintf( __( 'Add new %s', 'audiopod-wp' ), $labels['wpfc_preacher'] ),
						/* translators: %s preacher */
						'new_item_name'     => wp_sprintf( __( 'New %s name', 'audiopod-wp' ), $labels['wpfc_preacher'] ),
						/* translators: %s preacher */
						'not_found'         => wp_sprintf( __( 'No %s found', 'audiopod-wp' ), $labels['wpfc_preacher'] ),
					),
					'show_ui'      => true,
					'query_var'    => true,
					'show_in_rest' => true,
					'rewrite'      => array(
						'slug'       => $permalinks['wpfc_preacher'],
						'with_front' => false,
					),
					'capabilities' => $capabilities,
				),
				$permalinks,
				$capabilities
			)
		);

		register_taxonomy(
			'wpfc_sermon_series',
			apply_filters( 'sm_taxonomy_objects_wpfc_sermon_series', array( 'wpfc_sermon' ) ),
			apply_filters(
				'sm_taxonomy_args_wpfc_sermon_series',
				array(
					'hierarchical' => false,
					'label'        => __( 'Series', 'audiopod-wp' ),
					'labels'       => array(
						'name'              => __( 'Series', 'audiopod-wp' ),
						'singular_name'     => __( 'Series', 'audiopod-wp' ),
						'menu_name'         => _x( 'Series', 'menu', 'audiopod-wp' ),
						'search_items'      => __( 'Search series', 'audiopod-wp' ),
						'all_items'         => __( 'All series', 'audiopod-wp' ),
						'parent_item'       => null,
						'parent_item_colon' => null,
						'edit_item'         => __( 'Edit series', 'audiopod-wp' ),
						'update_item'       => __( 'Update series', 'audiopod-wp' ),
						'add_new_item'      => __( 'Add new series', 'audiopod-wp' ),
						'new_item_name'     => __( 'New series name', 'audiopod-wp' ),
						'not_found'         => __( 'No series found', 'audiopod-wp' ),
					),
					'show_ui'      => true,
					'query_var'    => true,
					'show_in_rest' => true,
					'rewrite'      => array(
						'slug'       => $permalinks['wpfc_sermon_series'],
						'with_front' => false,
					),
					'capabilities' => $capabilities,
				),
				$permalinks,
				$capabilities
			)
		);

		register_taxonomy(
			'wpfc_sermon_topics',
			apply_filters( 'sm_taxonomy_objects_wpfc_sermon_topics', array( 'wpfc_sermon' ) ),
			apply_filters(
				'sm_taxonomy_args_wpfc_sermon_topics',
				array(
					'hierarchical' => false,
					'label'        => __( 'Topics', 'audiopod-wp' ),
					'labels'       => array(
						'name'              => __( 'Topics', 'audiopod-wp' ),
						'singular_name'     => __( 'Topic', 'audiopod-wp' ),
						'menu_name'         => _x( 'Topics', 'menu', 'audiopod-wp' ),
						'search_items'      => __( 'Search topics', 'audiopod-wp' ),
						'all_items'         => __( 'All topics', 'audiopod-wp' ),
						'parent_item'       => null,
						'parent_item_colon' => null,
						'edit_item'         => __( 'Edit topic', 'audiopod-wp' ),
						'update_item'       => __( 'Update topic', 'audiopod-wp' ),
						'add_new_item'      => __( 'Add new topic', 'audiopod-wp' ),
						'new_item_name'     => __( 'New topic name', 'audiopod-wp' ),
						'not_found'         => __( 'No topics found', 'audiopod-wp' ),
					),
					'show_ui'      => true,
					'query_var'    => true,
					'show_in_rest' => true,
					'rewrite'      => array(
						'slug'       => $permalinks['wpfc_sermon_topics'],
						'with_front' => false,
					),
					'capabilities' => $capabilities,
				),
				$permalinks,
				$capabilities
			)
		);

		register_taxonomy(
			'wpfc_bible_book',
			apply_filters( 'sm_taxonomy_objects_wpfc_bible_book', array( 'wpfc_sermon' ) ),
			apply_filters(
				'sm_taxonomy_args_wpfc_bible_book',
				array(
					'hierarchical' => false,
					'label'        => __( 'Books', 'audiopod-wp' ),
					'labels'       => array(
						'name'              => __( 'Bible books', 'audiopod-wp' ),
						'singular_name'     => __( 'Book', 'audiopod-wp' ),
						'menu_name'         => _x( 'Books', 'menu', 'audiopod-wp' ),
						'search_items'      => __( 'Search books', 'audiopod-wp' ),
						'all_items'         => __( 'All books', 'audiopod-wp' ),
						'parent_item'       => null,
						'parent_item_colon' => null,
						'edit_item'         => __( 'Edit book', 'audiopod-wp' ),
						'update_item'       => __( 'Update book', 'audiopod-wp' ),
						'add_new_item'      => __( 'Add new book', 'audiopod-wp' ),
						'new_item_name'     => __( 'New book name', 'audiopod-wp' ),
						'not_found'         => __( 'No books found', 'audiopod-wp' ),
					),
					'show_ui'      => true,
					'query_var'    => true,
					'show_in_rest' => true,
					'rewrite'      => array(
						'slug'       => $permalinks['wpfc_bible_book'],
						'with_front' => false,
					),
					'capabilities' => $capabilities,
				),
				$permalinks,
				$capabilities
			)
		);

		register_taxonomy(
			'wpfc_service_type',
			apply_filters( 'sm_taxonomy_objects_wpfc_service_type', array( 'wpfc_sermon' ) ),
			apply_filters(
				'sm_taxonomy_args_wpfc_service_type',
				array(
					'hierarchical' => false,
					'label'        => ucwords( $labels['wpfc_service_type'] ),
					'labels'       => array(
						'name'              => ucwords( $labels['wpfc_service_type'] . 's' ),
						'singular_name'     => ucwords( $labels['wpfc_service_type'] ),
						'menu_name'         => ucwords( $labels['wpfc_service_type'] . 's' ),
						/* translators: %s preacher */
						'search_items'      => wp_sprintf( __( 'Search %s', 'audiopod-wp' ), $labels['wpfc_service_type'] ),
						/* translators: %s preacher */
						'all_items'         => wp_sprintf( __( 'All %s', 'audiopod-wp' ), $labels['wpfc_service_type'] ),
						'parent_item'       => null,
						'parent_item_colon' => null,
						/* translators: %s preacher */
						'edit_item'         => wp_sprintf( __( 'Edit %s', 'audiopod-wp' ), $labels['wpfc_service_type'] ),
						/* translators: %s preacher */
						'update_item'       => wp_sprintf( __( 'Update %s', 'audiopod-wp' ), $labels['wpfc_service_type'] ),
						/* translators: %s preacher */
						'add_new_item'      => wp_sprintf( __( 'Add new %s', 'audiopod-wp' ), $labels['wpfc_service_type'] ),
						/* translators: %s preacher */
						'new_item_name'     => wp_sprintf( __( 'New %s name', 'audiopod-wp' ), $labels['wpfc_service_type'] ),
						/* translators: %s preacher */
						'not_found'         => wp_sprintf( __( 'No %s found', 'audiopod-wp' ), $labels['wpfc_service_type'] ),
					),
					'show_ui'      => true,
					'query_var'    => true,
					'show_in_rest' => true,
					'rewrite'      => array(
						'slug'       => $permalinks['wpfc_service_type'],
						'with_front' => false,
					),
					'capabilities' => $capabilities,
				),
				$permalinks,
				$capabilities
			)
		);

		do_action( 'sm_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'wpfc_sermon' ) ) {
			return;
		}

		do_action( 'sm_register_post_type' );

		$permalinks = sm_get_permalink_structure();

		register_post_type(
			'wpfc_sermon',
			apply_filters(
				'sm_register_post_type_wpfc_sermon',
				array(
					'labels'              => array(
						'name'                  => __( 'Sermons', 'audiopod-wp' ),
						'singular_name'         => __( 'Sermon', 'audiopod-wp' ),
						'all_items'             => __( 'Sermons', 'audiopod-wp' ),
						'menu_name'             => _x( 'Sermons', 'menu', 'audiopod-wp' ),
						'add_new'               => __( 'Add New', 'audiopod-wp' ),
						'add_new_item'          => __( 'Add New Sermon', 'audiopod-wp' ),
						'edit'                  => __( 'Edit', 'audiopod-wp' ),
						'edit_item'             => __( 'Edit sermon', 'audiopod-wp' ),
						'new_item'              => __( 'New sermon', 'audiopod-wp' ),
						'view'                  => __( 'View sermon', 'audiopod-wp' ),
						'view_item'             => __( 'View sermon', 'audiopod-wp' ),
						'search_items'          => __( 'Search sermon', 'audiopod-wp' ),
						'not_found'             => __( 'No sermons found', 'audiopod-wp' ),
						'not_found_in_trash'    => __( 'No sermons found in trash', 'audiopod-wp' ),
						'featured_image'        => __( 'Sermon image', 'audiopod-wp' ),
						'set_featured_image'    => __( 'Set sermon image', 'audiopod-wp' ),
						'remove_featured_image' => __( 'Remove sermon image', 'audiopod-wp' ),
						'use_featured_image'    => __( 'Use as sermon image', 'audiopod-wp' ),
						'insert_into_item'      => __( 'Insert to sermon', 'audiopod-wp' ),
						'uploaded_to_this_item' => __( 'Uploaded to this sermon', 'audiopod-wp' ),
						'filter_items_list'     => __( 'Filter sermon', 'audiopod-wp' ),
						'items_list_navigation' => __( 'Sermon navigation', 'audiopod-wp' ),
						'items_list'            => __( 'Sermon list', 'audiopod-wp' ),
					),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'wpfc_sermon',
					'capabilities'        => array(
						'manage_wpfc_categories'  => 'manage_wpfc_categories',
						'manage_wpfc_sm_settings' => 'manage_wpfc_sm_settings',
					),
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'show_in_menu'        => true,
					'menu_icon'           => 'dashicons-sermon-manager',
					'hierarchical'        => false,
					'rewrite'             => array(
						'slug'       => $permalinks['wpfc_sermon'],
						'with_front' => false,
					),
					'query_var'           => true,
					'show_in_nav_menus'   => true,
					'show_in_rest'        => true,
					'has_archive'         => true,
					'supports'            => array(
						'title',
						'thumbnail',
						'publicize',
						'wpcom-markdown',
						'comments',
						'entry-views',
						'elementor',
						'excerpt',
						'revisions',
						'author',
						'editor'
					),
				)
			)
		);

		do_action( 'sm_after_register_post_type' );
	}

	/**
	 * Flush rewrite rules.
	 */
	public static function flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	/**
	 * Add Sermon Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			/* @noinspection PhpUndefinedClassInspection */
			new Jetpack_Omnisearch_Posts( 'wpfc_sermon' );
		}
	}

	/**
	 * Add sermon support for Jetpack related posts.
	 *
	 * @param array $post_types Array of allowed post types.
	 *
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'wpfc_sermon';

		return $post_types;
	}

	/**
	 * Shorthand function for flush_rewrite_rules(true).
	 *
	 * @since 2.7.1
	 */
	public static function flush_rewrite_rules_hard() {
		\flush_rewrite_rules( true );
	}
}

SM_Post_Types::init();
