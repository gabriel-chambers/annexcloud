<?php

function get_related_posts($data, $api = false)
{
	global $wpdb;
	$fields = '';
	$where = '';
	$queryOrder = '';
	$limit = '';
	$child_ids = array();
	$joinQuery = '';

	$match_fields = array('post_title', 'post_content');

	//create fulltext index for match fields
	create_full_text_index('related_post_full_text', $match_fields);
	//create fulltext index for title fields
	create_full_text_index('related_post_title_text', array('post_title'));

	$number_of_records = (isset($data['numberOfPosts'])) ? $data['numberOfPosts'] : 0;
	$current_post_id = $data['currentPostId'];
	$order_by = (isset($data['orderBy']) && $data['orderBy']) ? $data['orderBy'] : '';
	$order = (isset($data['order']) && $data['order']) ? $data['order'] : '';
	$post_types = array(get_post_type($current_post_id));
	$post_taxonomies = isset($data['postTaxonomies']) ? $data['postTaxonomies'] : [];

	if (!$api) {
		$child_ids = get_linked_post_ids($current_post_id);
	}

	$current_post   = get_post($current_post_id);
	$current_post_title = $current_post->post_title;
	$current_post_content = $current_post->post_excerpt;

	// Set order by in case of date.
	switch ($order_by) {
		case "date":
			$queryOrder = " $wpdb->posts.post_date $order";
			break;
		case "title":
			$queryOrder = " $wpdb->posts.post_title $order ";
			break;
		default:
			$queryOrder = "title_relevance DESC, relevance DESC";
	}

	if (!empty($queryOrder)) {
		$queryOrder = ' ORDER BY ' . $queryOrder;
	}

	//create sql query
	$match_fields = implode(',', $match_fields);
	$match_fields_content = $current_post_title . ' ' . $current_post_content;
	$match = $wpdb->prepare('MATCH (' . $match_fields . ') AGAINST (%s) ', $match_fields_content);

	//Prepare select fields
	$title_relevance_field = $wpdb->prepare('MATCH (post_title) AGAINST (%s) AS title_relevance', $current_post_title);
	$relevance_field = $wpdb->prepare('MATCH (' . $match_fields . ') AGAINST (%s) AS relevance', $match_fields_content);
	$fields = " $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_type, $wpdb->posts.post_date, $title_relevance_field, $relevance_field ";

	// Create the base WHERE clause.
	$where  = $match;
	$where .= " AND $wpdb->posts.post_status IN ('publish') ";
	$where .= $wpdb->prepare(" AND {$wpdb->posts}.ID != %d ", $current_post_id); //ignore current post id
	$where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $post_types) . "') ";    // Array of post types.

	$where .= " AND $wpdb->posts.post_title != ''";

	if (count($child_ids) > 0) {
		$where .= " AND $wpdb->posts.ID NOT IN ('" . join("', '", array_values($child_ids)) . "') ";    // Excluding the post ids already saved in the db
	}

	if (!empty($post_taxonomies)) {
		$post_taxonomy_ids = array_column($post_taxonomies, 'value');
		$posts_by_taxonomies = "SELECT $wpdb->posts.ID
		FROM $wpdb->posts, $wpdb->term_relationships, $wpdb->terms
		WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id
		AND $wpdb->terms.term_id = $wpdb->term_relationships.term_taxonomy_id AND
		$wpdb->terms.term_id IN ('" . join("', '", $post_taxonomy_ids) . "')";
		$where .= " AND $wpdb->posts.ID IN ($posts_by_taxonomies)";
	}

	// Create the base LIMITS clause.
	if ($number_of_records > 0) {
		$limit = $wpdb->prepare(' LIMIT %d', $number_of_records);
	}

	if (!function_exists('is_plugin_active')) {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	}

	if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
		//Defining and appending the prefix since we cannot get the wpml table names using the $wpdb object
		$prefix = $wpdb->prefix;
		$current_language = apply_filters('wpml_current_language', NULL);
		$joinQuery = " LEFT JOIN {$prefix}icl_translations ON {$wpdb->posts}.ID = {$prefix}icl_translations.element_id ";
		$where .= $wpdb->prepare(" AND {$prefix}icl_translations.language_code = %s ", $current_language);
	}

	$sql = "SELECT DISTINCT $fields FROM $wpdb->posts $joinQuery WHERE $where $queryOrder $limit";
	return $wpdb->get_results($sql, ARRAY_A);
}

function create_full_text_index($key, $columns)
{
	global $wpdb;
	$show_index_query = "SHOW INDEX FROM $wpdb->posts WHERE Key_name = '{$key}'";
	if (!$wpdb->get_results($show_index_query)) {
		$requiredColumns = implode(',', $columns);
		$sql = "ALTER TABLE {$wpdb->posts} DROP INDEX IF EXISTS {$key};";
		$wpdb->get_results($sql);
		$sql = "ALTER TABLE {$wpdb->posts} ADD FULLTEXT {$key} (" . $requiredColumns . ");";
		$wpdb->get_results($sql);
	}
}
