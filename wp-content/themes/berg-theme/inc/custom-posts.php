<?php

if ( ! function_exists( 'add_event_post_type' ) ) {

	function add_event_post_type() {
		$args = array(
			'labels'             => array(
				'name'               => 'Events',
				'singular_name'      => 'Event',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New',
				'edit'               => 'Edit',
				'edit_item'          => 'Edit',
				'new_item'           => 'New',
				'view'               => 'View',
				'view_item'          => 'View',
				'search_items'       => 'Search',
				'not_found'          => 'No Event found',
				'not_found_in_trash' => 'No Resource found in Trash'
			),
			'public'             => true,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'menu_icon'          => 'dashicons-archive',
			'taxonomies'         => array(),
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'with_front' => false, 'slug' => 'events' ),
			'capability_type'    => 'post',
			'has_archive'        => false
		);
		register_post_type( 'events', $args );
		register_taxonomy(
			'event-location',
			array( 'events' ),
			array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => 'Event Location',
					'menu_name'         => 'Event Location',
					'singular_name'     => 'Event Location',
					'search_items'      => 'Search Locations',
					'all_items'         => 'All Locations',
					'parent_item'       => 'Parent Location',
					'parent_item_colon' => 'Parent Location:',
					'edit_item'         => 'Edit Location',
					'update_item'       => 'Update Location',
					'add_new_item'      => 'Add New Location',
					'new_item_name'     => 'New Location',
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'event-location' )
			)
		);
	}

	add_action( 'after_setup_theme', 'add_event_post_type' );
}


if ( ! function_exists( 'add_careers_post_type' ) ) {

	function add_careers_post_type() {
		$args = array(
			'labels'             => array(
				'name'               => 'Careers',
				'singular_name'      => 'Career',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New',
				'edit'               => 'Edit',
				'edit_item'          => 'Edit',
				'new_item'           => 'New',
				'view'               => 'View',
				'view_item'          => 'View',
				'search_items'       => 'Search',
				'not_found'          => 'No Event found',
				'not_found_in_trash' => 'No Careers found in Trash'
			),
			'public'             => true,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'menu_icon'          => 'dashicons-welcome-learn-more',
			'taxonomies'         => array(),
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'with_front' => false, 'slug' => 'careers' ),
			'capability_type'    => 'post',
			'has_archive'        => false
		);
		register_post_type( 'careers', $args );
		register_taxonomy(
			'department',
			array( 'careers' ),
			array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => 'Department',
					'menu_name'         => 'Department',
					'singular_name'     => 'Departments',
					'search_items'      => 'Search Departments',
					'all_items'         => 'All Departments',
					'parent_item'       => 'Parent Department',
					'parent_item_colon' => 'Parent Department:',
					'edit_item'         => 'Edit Department',
					'update_item'       => 'Update Department',
					'add_new_item'      => 'Add New Department',
					'new_item_name'     => 'New Department',
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'department' )
			)
		);
		register_taxonomy(
			'contract-type',
			array( 'careers' ),
			array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => 'Contract Types',
					'menu_name'         => 'Contract Types',
					'singular_name'     => 'Contract Type',
					'search_items'      => 'Search Contract Types',
					'all_items'         => 'All Contract Types',
					'parent_item'       => 'Parent Contract Type',
					'parent_item_colon' => 'Parent Contract Type:',
					'edit_item'         => 'Edit Contract Type',
					'update_item'       => 'Update Contract Type',
					'add_new_item'      => 'Add New Contract Type',
					'new_item_name'     => 'New Contract Types',
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'contract-type' )
			)
		);
		register_taxonomy(
			'job-location',
			array( 'careers' ),
			array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => 'Locations',
					'menu_name'         => 'Locations',
					'singular_name'     => 'Location',
					'search_items'      => 'Search Locations',
					'all_items'         => 'All Locations',
					'parent_item'       => 'Parent Location',
					'parent_item_colon' => 'Parent Location:',
					'edit_item'         => 'Edit Location',
					'update_item'       => 'Update Location',
					'add_new_item'      => 'Add New Location',
					'new_item_name'     => 'New Location',
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'job-location' )
			)
		);
	}

	add_action( 'after_setup_theme', 'add_careers_post_type' );
}

if ( ! function_exists( 'add_resource_post_type' ) ) {

	function add_resource_post_type() {
		$args = array(
			'labels'             => array(
				'name'               => 'Resources',
				'singular_name'      => 'Resource',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New',
				'edit'               => 'Edit',
				'edit_item'          => 'Edit',
				'new_item'           => 'New',
				'view'               => 'View',
				'view_item'          => 'View',
				'search_items'       => 'Search',
				'not_found'          => 'No Resource found',
				'not_found_in_trash' => 'No Resource found in Trash'
			),
			'public'             => true,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'custom-fields' ),
			'menu_icon'          => 'dashicons-networking',
			'taxonomies'         => array(),
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'with_front' => false, 'slug' => 'resources' ),
			'capability_type'    => 'post',
			'has_archive'        => false
		);
		register_post_type( 'resource', $args );
		register_taxonomy(
			'resource-category',
			array( 'resource' ),
			array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => 'Resource Category',
					'menu_name'         => 'Resource Category',
					'singular_name'     => 'Resource Category',
					'search_items'      => 'Search Categories',
					'all_items'         => 'All Categories',
					'parent_item'       => 'Parent Category',
					'parent_item_colon' => 'Parent Category:',
					'edit_item'         => 'Edit Category',
					'update_item'       => 'Update Category',
					'add_new_item'      => 'Add New Category',
					'new_item_name'     => 'New Category',
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'resource-category' )
			)
		);
	}

	add_action( 'after_setup_theme', 'add_resource_post_type' );
}

if ( ! function_exists( 'add_news_post_type' ) ) {

	function add_news_post_type() {
		$args = array(
			'labels'             => array(
				'name'               => 'News',
				'singular_name'      => 'News',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New',
				'edit'               => 'Edit',
				'edit_item'          => 'Edit',
				'new_item'           => 'New',
				'view'               => 'View',
				'view_item'          => 'View',
				'search_items'       => 'Search',
				'not_found'          => 'No News found',
				'not_found_in_trash' => 'No News found in Trash'
			),
			'public'             => true,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'menu_icon'          => 'dashicons-media-document',
			'taxonomies'         => array(),
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'with_front' => false, 'slug' => 'about/iot-security-news' ),
			'capability_type'    => 'post',
			'has_archive'        => false
		);
		register_post_type( 'news', $args );
		register_taxonomy(
			'news-category',
			array( 'news' ),
			array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => 'News Category',
					'menu_name'         => 'News Category',
					'singular_name'     => 'News Category',
					'search_items'      => 'Search News Category',
					'all_items'         => 'All News Categories',
					'parent_item'       => 'Parent News Category',
					'parent_item_colon' => 'Parent Category:',
					'edit_item'         => 'Edit Category',
					'update_item'       => 'Update Category',
					'add_new_item'      => 'Add New Category',
					'new_item_name'     => 'New Category',
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				//'rewrite' => array('slug' => 'resource-category')
			)
		);
	}

	add_action( 'after_setup_theme', 'add_news_post_type' );
}

if ( ! function_exists( 'add_leadership_post_type' ) ) {

	function add_leadership_post_type() {
		$args = array(
			'labels'             => array(
				'name'               => 'Leadership',
				'singular_name'      => 'Leadership',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New',
				'edit'               => 'Edit',
				'edit_item'          => 'Edit',
				'new_item'           => 'New',
				'view'               => 'View',
				'view_item'          => 'View',
				'search_items'       => 'Search',
				'not_found'          => 'No Leadership found',
				'not_found_in_trash' => 'No Leadership found in Trash'
			),
			'public'             => true,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'menu_icon'          => 'dashicons-groups',
			'taxonomies'         => array(),
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'with_front' => false, 'slug' => 'leadership' ),
			'capability_type'    => 'post',
			'has_archive'        => false
		);
		register_post_type( 'leadership', $args );
		register_taxonomy(
			'leadership-types',
			array( 'leadership' ),
			array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => 'Leadership Types',
					'menu_name'         => 'Leadership Types',
					'singular_name'     => 'Leadership Type',
					'search_items'      => 'Search Leadership Types',
					'all_items'         => 'All Leadership Types',
					'parent_item'       => 'Parent Leadership Type',
					'parent_item_colon' => 'Parent Leadership Type:',
					'edit_item'         => 'Edit Leadership Type',
					'update_item'       => 'Update Leadership Type',
					'add_new_item'      => 'Add New Leadership Type',
					'new_item_name'     => 'New Leadership Types',
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'leadership-type' )
			)
		);
	}

	add_action( 'after_setup_theme', 'add_leadership_post_type' );
}

