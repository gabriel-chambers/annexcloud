<?php
//require(SINGLE_POST_BLOCK_ROOT . '/helper.php');
require(WP_PLUGIN_DIR  . "/berg/lib/helper/post-helper.php");

function register_single_post_modified()
{
	unregister_block_type('e25m/single-post');
	register_block_type('e25m/single-post', array(
		'editor_script' => 'berg-block-js-vendor',
		'editor_style' => 'berg-block-editor-css',
		'style' => 'e25m-style-css',
		'render_callback' => 'single_post_modified_render_callback',
		'attributes' => array(
			'postVisibility' => [
				'type' => 'boolean',
				'default' => true,
			],
			'className' => [
				'type' => 'string',
				'default' => ''
			],
			'selectedPost' => [
				'type' => 'object',
				'default' => ''
			],
			'selectedPostId' => [
				'type' => 'number',
				'default' => 0
			],
			'selectedPostType' => [
				'type' => 'string',
				'default' => ''
			],
			'imageAppearance' => [
				'type' => 'string',
				'default' => 'image'
			],
			'anchorAppearance' => [
				'type' => 'string',
				'default' => 'full'
			],
			'dateFormat' => [
				'type' => 'string',
				'default' => 'd-m-Y'
			],
			'displayOrder' => [
				'type' => 'array',
				'default' => [['value' => 'title', 'label' => 'Title']]
			],
			'popupDisplayOrder' => [
				'type' => 'array',
				'default' => [['value' => 'title', 'label' => 'Title']]
			],
			'titleTag' => [
				'type' => 'string',
				'default' => 'h5'
			],
			'singlePostClass' => [
				'type' => 'string',
				'default' => ''
			],
			'singlePostClassNames' => [
				'type' => 'array',
				'default' => [['value' => 'bs-single-post---default', 'label' => 'Default']]
			],
			'fancyboxStyleClassName' => [
				'type' => 'string',
				'default' => ''
			],
			'titleCharLimit' => [
				'type' => 'number',
				'default' => 0
			],
			'lastUpdated' => [
				'type' => 'number',
				'default' => 0
			],
			'enableTaxonomiesLink' => [
				'type' => 'boolean',
				'default' => false,
			],
			'postId' => [
				'type' => 'number',
				'default' => 0
			],
		)
	)
	);
}

add_action('init', 'register_single_post_modified', 11);

function single_post_modified_render_callback($block_attributes)
{
	$_post_id = (isset($block_attributes['selectedPostId']) && $block_attributes['selectedPostId'] != 0) ?
		$block_attributes['selectedPostId'] :
		(!empty($block_attributes['selectedPost']) ? $block_attributes['selectedPost']['value'] : 0);

	// Checking if a WPML translated post is available for the current language
	$current_language = apply_filters('wpml_current_language', NULL);
	$wpml_post_id = apply_filters('wpml_object_id', $_post_id, get_post_type($_post_id), false, $current_language);
	// If available, replacing the selected post id with translated post id
	$_post_id = $wpml_post_id > 0 ? $wpml_post_id : $_post_id;

	$display_order = $block_attributes['displayOrder'];
	$popup_display_order = $block_attributes['popupDisplayOrder'];
	$single_post_classes_arr = $block_attributes['singlePostClassNames'];
	$post_type = $block_attributes['selectedPostType'];
	$date_format = $block_attributes['dateFormat'];
	$image_appearance = $block_attributes['imageAppearance'];
	$anchor_appearance = $block_attributes['anchorAppearance'];
	$title_tag = $block_attributes['titleTag'];
	$show_custom_date = get_post_meta($_post_id, 'show_custom_date', true);
	$custom_date = get_post_meta($_post_id, 'custom_date', true);
	$fancyboxStyleClassName = $block_attributes['fancyboxStyleClassName'];
	$titleCharLimit = $block_attributes['titleCharLimit'];

	if ($show_custom_date == 1) {
		$_date = date($date_format, strtotime($custom_date));
	} else {
		$_date = get_the_date($date_format, $_post_id);
	}

	$read_more_text = get_post_meta($_post_id, 'learn_more_label', true);
	$read_more_text = (trim($read_more_text)) ? $read_more_text : "Read more";
	$link_attributes = get_post_link($_post_id, $anchor_appearance, $read_more_text, $fancyboxStyleClassName);
	$posts_blocks_class = uniqid('bs-post-');

	if (!empty($single_post_classes_arr)) {
		$posts_blocks_class .= ' ' . implode(' ', array_column($single_post_classes_arr, 'value'));
	}

	if (!empty($_post_id) && !empty($display_order)) {
		$args = array(
			'post_type' => 'any',
			'post_status' => array('publish'),
			'p' => $_post_id
		);
		$_the_query = new WP_Query($args);

		ob_start();
		include "layouts/layout.php";
		include "layouts/layout-popup.php";
		$post_output = ob_get_contents();
		ob_end_clean();

		if (
			$block_attributes['postVisibility'] == true ||
			($block_attributes['postVisibility'] == false && isset($_GET['isEditor']))
		) {
			return $post_output;
		}

	} else if (isset($_GET['isEditor'])) {
		return '<h5>No post selected</h5>';

	} else {
		return;
	}
}

/**
 * Filter for query posts by phrase of it's title
 */
if (!function_exists('post_title_filter')) {
	function post_title_filter($where, &$wp_query)
	{
		global $wpdb;
		if ($search_term = $wp_query->get('search_query')) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql($wpdb->esc_like($search_term)) . '%\'';
		}
		remove_filter(current_filter(), __FUNCTION__);
		return $where;
	}
}

if (!function_exists('search_posts_only_by_title_filter') && function_exists('post_title_filter')) {
	function search_posts_only_by_title_filter(WP_Query $query)
	{
		if (
			isset($_GET['searchBy'])
			&& isset($_GET['search'])
			&& $_GET['searchBy'] == 'post_title'
			&& is_string($_GET['search'])
			&& strlen($_GET['search']) > 0
		) {
			$query->set('search_query', $_GET['search']);
			add_filter('posts_where', 'post_title_filter', 10, 2);
		}
	}
	add_action('pre_get_posts', 'search_posts_only_by_title_filter');
}

function get_post_data_api_modified()
{
	register_rest_route('api/bs-single-post', 'getPostData', array(
		'methods' => 'POST',
		'callback' => 'get_single_post_data_call_back',
		'permission_callback' => '__return_true',
	)
	);
}

add_action('rest_api_init', 'get_post_data_api_modified');

if (!function_exists('get_single_post_data_call_back')) {
	function get_single_post_data_call_back($data)
	{
		$params = $data->get_params();

		if (!isset($params['postId'])) {
			return [];
		}
		$postId = $params['postId'];
		$title = get_the_title($postId);
		$post_type = get_post_type($postId);
		return new WP_REST_Response(['title' => $title, 'selectedPostType' => $post_type]);
	}
}