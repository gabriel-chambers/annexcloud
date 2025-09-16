<?php

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
		if (isset($_GET['searchBy'])
			&& isset($_GET['search'])
			&& $_GET['searchBy'] == 'post_title'
			&& is_string($_GET['search'])
			&& strlen($_GET['search']) > 0) {
			$query->set('search_query', $_GET['search']);
			add_filter('posts_where', 'post_title_filter', 10, 2);
		}
	}
	add_action('pre_get_posts', 'search_posts_only_by_title_filter');
}
