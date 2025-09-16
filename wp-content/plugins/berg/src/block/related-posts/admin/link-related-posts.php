<?php

function link_berg_related_posts()
{

	//Checking if the link is selected
	if (isset($_GET['brp_create_link']) && isset($_GET['brp_parent'])) {
		// Checking if the current user has the edit permissions
		if (!current_user_can('edit_posts')) {
			wp_die('There was a problem loading this page, you may not have the necessary permissions.');
		}
		$parent_id = $_GET['brp_parent'];
		$child_id = $_GET['brp_create_link'];
		$parent_post_type = get_post_type($parent_id);
		// Creating the link
		create_link($parent_id, $child_id, $parent_post_type, false, true);
		// Redirecting back to the edit view
		$redirect_url = get_admin_url() . "post.php?post={$parent_id}&action=edit";
		// Checking if a particular language is set
		if (isset($_GET['lang'])) {
			$redirect_url .= "&amp;lang=" . $_GET['lang'];
		}
		wp_redirect($redirect_url);
		exit;
	}
}
add_action('admin_init', 'link_berg_related_posts');


function create_link($parent_id, $child_id, $post_type_parent, $batch = false, $manual = false)
{
	global $wpdb;

	// Setup the insert data
	$data = array(
		'post' => "('" . current_time('mysql', 0) . "', '" . current_time('mysql', 1) . "','','Berg Related Post Link','" . BRP_LINK_PT . "','publish')",
		'meta' => array(
			"(%d, '" . BRP_PT_PARENT . "', '$post_type_parent')",
			"(%d, '" . BRP_PARENT . "', '$parent_id')",
			"(%d, '" . BRP_CHILD . "', '$child_id')",
		)
	);


	if (true == $manual) {
		$data['meta'][] = "(%d, '" . BRP_MANUAL . "', '1')";
	}

	// If this is a batch insert, return data
	if (true === $batch) {
		return $data;
	}

	// Create post link
	$wpdb->query("	INSERT INTO `$wpdb->posts`
						(`post_date`,`post_date_gmt`,`post_content`,`post_title`,`post_type`,`post_status`)
						VALUES
						{$data['post']}
						");

	// Get the link ID
	$link_id = $wpdb->insert_id;

	// Create post meta
	$wpdb->query("INSERT INTO `$wpdb->postmeta`
				(`post_id`,`meta_key`,`meta_value`)
				VALUES
				" . implode(',', array_map(array(
		$wpdb,
		'prepare'
	), $data['meta'], array_fill(0, count($data['meta']), $link_id))) . "
				");

	// Return link id
	return $link_id;
}


function create_link_bulk($post_id)
{
	$current_post_type = get_post_type($post_id);
	$number_of_records = get_option('brp_limit_' . $current_post_type);
	$order_by = get_option('brp_order_by');
	$order = get_option('brp_order');

	$args = array(
		"numberOfPosts" => $number_of_records,
		"currentPostId" => $post_id,
		"orderBy" => $order_by,
		"order" => $order,
		"postTaxonomies" => get_taxonomy_terms_by_post_id($post_id),
	);
	$related_posts = get_related_posts($args);

	if (count($related_posts) > 0) {
		foreach ($related_posts as $post) {
			create_link($post_id, $post['ID'], $current_post_type);
		}
	}
	return;
}

function create_link_bulk_from_admin()
{
	if (isset($_POST['brp_bulk']) && isset($_GET['brp_parent'])) {
		$parent = $_GET['brp_parent'];
		$parent_post_type = get_post_type($parent);

		if (!current_user_can('edit_posts')) {
			wp_die('There was a problem loading this page, you may not have the necessary permissions.');
		}
		if (count($_POST['brp_bulk']) > 0) {
			foreach ($_POST['brp_bulk'] as $bulk_post) {
				// Create link
				create_link($parent, $bulk_post, $parent_post_type, false, true);
			}
		}
		// Send back
		$redirect_url = get_admin_url() . "post.php?post={$parent}&action=edit";
		// WPML check
		if (isset($_GET['lang'])) {
			$redirect_url .= "&amp;lang=" . $_GET['lang'];
		}
		wp_redirect($redirect_url);
		exit;
	}
}

function delete_links_by_post_type($post_type) {
	global $wpdb;
	//Getting all the published post ids for this post type
	$results = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish'", $post_type ), ARRAY_A );
	if(count($results) > 0 ){
		foreach($results as $post){
			// Deleting all the links of the post
			delete_links_by_post_id($post['ID']);
		}
	}
	return;
}

function delete_links_by_post_id($post_id) {
	global $wpdb;
	//Getting all the existing links for this post
	$existing_links = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '%s' and meta_value = '%s'", BRP_PARENT, $post_id ), ARRAY_A );
	if(count($existing_links) > 0 ){
		foreach($existing_links as $link){
			// Deleting linked posts by the link id
			delete_post_link_by_id($link['post_id']);
		}
	}
	return;
}

function enable_output_buffer() {
	ob_start();
}
add_action('admin_init', 'enable_output_buffer');
