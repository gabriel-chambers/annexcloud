<?php

function get_linked_posts($parent_id)
{
	$child_ids = get_linked_post_ids($parent_id);
	$children = array_map('get_post', $child_ids);
	// Return children
	return $children;
}

function get_linked_post_ids($parent_id)
{
	// // Build WP_Query arguments
	$link_args = related_post_link_args(BRP_PARENT, $parent_id);
	// Create link query
	$wp_query = new WP_Query();
	$posts    = $wp_query->query($link_args);
	// Store child ids
	$child_ids = array();
	foreach ($posts as $post) {
		$child_ids[$post->ID] = get_post_meta($post->ID, BRP_CHILD, true);
	}
	return $child_ids;
}


function related_post_link_args($meta_key, $post_id)
{
	$args = array(
		'post_type'           => BRP_LINK_PT,
		'posts_per_page'      => -1,
		'orderby'             => 'menu_order',
		'order'               => 'ASC',
		'ignore_sticky_posts' => 1,
		'meta_query'          => array(
			array(
				'key'     => $meta_key,
				'value'   => $post_id,
				'compare' => '=',
			)
		)
	);
	return $args;
}


function delete_post_link()
{
	if (!isset($_POST['id'])) {
		exit;
	}
	$post_id = $_POST['id'];
	// Checking if user is allowed to do this
	if (!current_user_can('edit_posts')) {
		return;
	}
	$target_post = get_post($post_id);
	// Only delete post type we control
	if ($target_post->post_type != BRP_LINK_PT) {
		return;
	}
	delete_post_link_by_id($post_id);
	//JSON response
	$response = json_encode(array('success' => true));
	header('Content-Type: application/json');
	echo $response;
	exit();
}

add_action('wp_ajax_nopriv_delete_post_link', 'delete_post_link');
add_action('wp_ajax_delete_post_link', 'delete_post_link');

function delete_post_link_by_id($link_id)
{
	wp_delete_post($link_id, true);
	return;
}

function get_taxonomy_terms_by_post_id($post_id) {

	$current_post_terms_array = [];
	$current_post_type = get_post_type($post_id);

	if($current_post_type == 'page') {
		return $current_post_terms_array;
	}
	//Checking if the option 'Only from same taxonomies' is enabled for the current post type
	$same_taxonomies_enabled = get_option('brp_same_taxes_' . $current_post_type);

	if($same_taxonomies_enabled) {
		$post_taxonomies = get_object_taxonomies( $current_post_type );
		$current_post_terms = wp_get_object_terms( $post_id, (array) $post_taxonomies );
		if ( ! is_wp_error( $current_post_terms ) && is_array($current_post_terms) && ! empty( $current_post_terms )) {
			foreach($current_post_terms as $term) {
				$current_post_terms_array[]['value'] = $term->term_taxonomy_id;
			}
		}
	}
	return $current_post_terms_array;
}