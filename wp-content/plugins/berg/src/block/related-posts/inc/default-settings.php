<?php

function brp_settings_page_content()
{
	echo "<div class='nav-tab-wrapper'>";
	$settings_groups = brp_settings_tabs();
	$post_types = get_post_types_by_support(array('editor', 'title', 'thumbnail'));
	$first_tab = true;
	foreach ($settings_groups as $tab => $tab_description) {
		if ($first_tab) {
			$nav_tab_url = add_query_arg(array('action' => $tab . '_' . $post_types[0]), admin_url('admin.php?page=berg-related-posts'));
			$nav_tab_class = (!isset($_GET['action'])) || (isset($_GET['action']) && $_GET['action'] == $tab . '_' . $post_types[0]) ? "nav-tab-active" : "";
		} else {
			$nav_tab_url = add_query_arg(array('action' => $tab), admin_url('admin.php?page=berg-related-posts'));
			$nav_tab_class = (isset($_GET['action']) && $_GET['action'] == $tab) ? "nav-tab-active" : "";
		}
		echo "<a href='" . esc_url($nav_tab_url) . "' class='nav-tab " . $nav_tab_class . "'>" . ucwords($tab) . "</a>";
		$first_tab = false;
	}
	echo "</div>";
	echo '<div class="wrap">
	<h1>' . esc_html__('Berg Related Posts - Settings', 'berg-related-posts') . '</h1>
	<form method="post" action="options.php">';
	$current_tab = isset($_GET['action']) ? $_GET['action'] : "general_" . $post_types[0];
	if (strpos($current_tab, 'general_') !== false) {
		$selected_post_type = explode('_', $current_tab);
		$selected_post_type = array_pop($selected_post_type);
		echo "<div class='brp-post-type-switcher'>";
		echo "<ul class='subsubsub'>";
		echo "<li class='label'>Post Type: &nbsp; | </li>";
		foreach ($post_types as $post_type) {
			$current = ($selected_post_type == $post_type) ? "current" : "";
			echo "<li>";
			echo "<a class='" . $current . "' href='" . add_query_arg(array('action' => "general_" . $post_type), admin_url('admin.php?page=berg-related-posts')) . "'>" . ucwords($post_type) . "</a>";
			echo ' | ';
			echo "</li>";
		}
		echo "</ul>";
		echo "</div>";
	}
	if (isset($settings_groups[$current_tab])) {
		echo '<div class="notice-info updated"><p><strong>' . $settings_groups[$current_tab] . '</strong></p></div>';
	}
	settings_fields('brp_' . $current_tab . '_settings'); // settings group name
	do_settings_sections('berg-related-posts-' . $current_tab); // page slug
	if ($current_tab != "weights") {
		submit_button();
	}
	echo '</form></div>';
}

function brp_settings_tabs()
{
	$tabs = array(
		'general' => 'The following options affect how related posts are automatically linked.',
		'configuration' => 'Full control on how your related posts are displayed.',
		'weights' => 'Posts will be considered as related based on the title and the content fields.',
		'installer' => 'Below you\'ll find your post types available for installation, by installing a post type we\'ll set up a cache and offer you the ability to automatic link (custom) posts.'
	);

	return $tabs;
}

function brp_settings_groups()
{
	$settings_groups = [];
	$post_types = get_post_types_by_support(array('editor', 'title', 'thumbnail'));
	foreach ($post_types as $post_type) {
		$settings_groups['general_' . $post_type] = brp_general_settings($post_type);
	}
	$settings_groups['configuration'] = brp_configuration_settings();
	$settings_groups['weights'] = brp_weight_settings();
	$settings_groups['installer'] = brp_installer_settings();
	return $settings_groups;
}

function brp_general_settings($post_type)
{
	$general_settings = array(
		'automatic_linking_' . $post_type => array(
			'id'          => 'automatic_linking_' . $post_type,
			'name'       =>  esc_html__('Enable', 'berg-related-posts'),
			'desc' => esc_html__('Checking this will enable automatically linking posts to the selected post type', 'berg-related-posts'),
			'type'        => 'checkbox',
			'default'     => 0,
		),
		'title_' . $post_type => array(
			'id'      => 'title_' . $post_type,
			'name'    => esc_html__('Heading of posts', 'berg-related-posts'),
			'desc'    => esc_html__('Displayed before the list of the posts as a master heading', 'berg-related-posts'),
			'type'    => 'text',
			'default' => '',
		),
		'title_tag' => array(
			'id'      => 'title_tag_' . $post_type,
			'name'    => esc_html__('Title tag', 'berg-related-posts'),
			'desc'    => esc_html__('The HTML tag to be used for the title', 'berg-related-posts'),
			'type'    => 'select',
			'options' => array(
				'h1' => '<h1>',
				'h2' => '<h2>',
				'h3' => '<h3>',
				'h4' => '<h4>',
				'h5' => '<h5>',
				'h6' => '<h6>',
			),
			'default'     => 'h2',
			'size'    => 'large',
		),
		'title_length_' . $post_type => array(
			'id'      => 'title_length_' . $post_type,
			'name'    => esc_html__('Title length', 'berg-related-posts'),
			'desc'    => esc_html__('The amount of characters to be displayed in the title. To disable, set value to 0.', 'berg-related-posts'),
			'type'    => 'number',
			'default' => 0,
		),
		'description_' . $post_type => array(
			'id'      => 'description_' . $post_type,
			'name'    => esc_html__('Description', 'berg-related-posts'),
			'desc'    => esc_html__('Displayed after the title as a general description', 'berg-related-posts'),
			'type'    => 'textarea',
			'default' => '',
		),
		'description_tag' => array(
			'id'      => 'description_tag_' . $post_type,
			'name'    => esc_html__('Description tag', 'berg-related-posts'),
			'desc'    => esc_html__('The HTML tag to be used for the description', 'berg-related-posts'),
			'type'    => 'select',
			'options' => array(
				'h1' => '<h1>',
				'h2' => '<h2>',
				'h3' => '<h3>',
				'h4' => '<h4>',
				'h5' => '<h5>',
				'h6' => '<h6>',
				'p' => '<p>',
			),
			'default'     => 'p',
			'size'    => 'large',
		),
		'limit_' . $post_type => array(
			'id'      => 'limit_' . $post_type,
			'name'    => esc_html__('Number of posts to display', 'berg-related-posts'),
			'desc'    => esc_html__('Maximum number of posts that will be displayed in the list.', 'berg-related-posts'),
			'type'    => 'number',
			'default' => 3,
		),
		'same_taxes_' . $post_type => array(
			'id'          => 'same_taxes_' . $post_type,
			'name'    => esc_html__( 'Only from same taxonomies', 'berg-related-posts' ),
			'desc'    => esc_html__( 'Limit the related posts only to the categories, tags, and/or taxonomies of the current post.', 'berg-related-posts'),
			'type'        => 'checkbox',
			'default'     => 0,
		),

	);
	return $general_settings;
}

function brp_configuration_settings()
{

	$configuration_settings = array(
		'post_display_order' => array(
			'id'      => 'post_display_order',
			'name'    => esc_html__('Post display order', 'berg-related-posts'),
			'desc'    => esc_html__('Order of the elements in the post', 'berg-related-posts'),
			'type'    => 'displayorder',
			'default' => ["title", "image", "more"],
			'size'    => 'large',
		),
		'popup_display_order' => array(
			'id'      => 'popup_display_order',
			'name'    => esc_html__('Popup display order', 'berg-related-posts'),
			'desc'    => esc_html__('Order of the elements in the detailed view popup', 'berg-related-posts'),
			'type'    => 'displayorder',
			'default' => ["title", "image", "more"],
			'size'    => 'large',
		),
		'posts_per_row' => array(
			'id'      => 'posts_per_row',
			'name'    => esc_html__('Posts per row', 'berg-related-posts'),
			'desc'    => esc_html__('The amount of related posts per row. ', 'berg-related-posts'),
			'type'    => 'select',
			'options' => array(
				12 => '1',
				6 => '2',
				4 => '3',
				3 => '4'
			),
			'default'     => 4,
			'size'    => 'large',
		),
		'order_by' => array(
			'id'      => 'order_by',
			'name'    => esc_html__('Order posts', 'berg-related-posts'),
			'desc'    => '',
			'type'    => 'select',
			'options' => array(
				'relevance' => 'By relevance',
				'date' => 'By date',
				'title' => 'By title'
			),
			'default'     => 'relevance',
			'size'    => 'large',
		),
		'order' => array(
			'id'      => 'order',
			'name'    => esc_html__('Sort order', 'berg-related-posts'),
			'desc'    => '',
			'type'    => 'select',
			'options' => array(
				'ASC' => 'Ascending',
				'DESC' => 'Descending',
			),
			'default'     => 'DESC',
			'size'    => 'large',
		),
		'date_format' => array(
			'id'      => 'date_format',
			'name'    => esc_html__('Date format', 'berg-related-posts'),
			'desc'    => '',
			'type'    => 'text',
			'default' => 'd-m-Y',
		),
	);
	return $configuration_settings;
}

function brp_weight_settings()
{

	$weight_settings = array(
		// TO DO: Weight calculator will be implemented in the next release
	);
	return $weight_settings;
}

function brp_installer_settings()
{
	$installer_settings = array(
		'post_installer' => array(
			'id'      => 'post_installer',
			'name'    => esc_html__('Relink related posts', 'berg-related-posts'),
			'desc'    => esc_html__('NOTE: This will remove all existing links and relink related posts of the selected post type.', 'berg-related-posts'),
			'type'    => 'post_type',
			'default' => array('post', 'page'),
			'size'    => 'large',
		),
	);
	return $installer_settings;
}