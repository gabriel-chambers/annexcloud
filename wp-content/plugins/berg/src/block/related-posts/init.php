<?php
require(__DIR__ . '/settings.php');
require(__DIR__ . '/main-query.php');
require(__DIR__ . '/admin/related-posts-grid.php');

// Custom post type function
function create_brp_link_post_type()
{
	register_post_type(BRP_LINK_PT, array('public' => false, 'label' => 'Berg Related Posts Link'));
}
add_action('init', 'create_brp_link_post_type');

// create new endpoint route
function get_related_post_api()
{
	register_rest_route('api/related-posts', 'getPosts', array(
		'methods'             => 'POST',
		'callback'            => 'get_related_post_api_data',
		'permission_callback' => '__return_true',
	));
}

add_action('rest_api_init', 'get_related_post_api');

//This filter is used to stop generating a dynamic excerpt field by wordpress if the field is not available
remove_filter('get_the_excerpt', 'wp_trim_excerpt');

function get_related_post_api_data($data)
{
	$params = $data->get_params();
	$results = get_related_posts($params, true);
	return new WP_REST_Response($results);
}

function insert_content_into_posts($content)
{
	$current_post_id = get_the_ID();
	if ((is_single() && in_the_loop() && is_main_query()) || (is_singular() && in_the_loop() && is_main_query())) {
		$related_posts = render_content($current_post_id);
		$content = $content . $related_posts;
	}
	return $content;
}
add_filter('the_content', 'insert_content_into_posts');

function render_content($current_post_id)
{
	// Get related posts specific meta.
	$post_meta = get_post_meta($current_post_id, 'brp_post_meta', true);
	$disable_here = isset($post_meta['brp_disable_here']) ? $post_meta['brp_disable_here'] : 0;

	if ($disable_here) {
		return "";
	}
	// Getting related posts from the database
	$children = get_linked_posts($current_post_id);
	$current_post_type = get_post_type($current_post_id);
	$automatic_linking = get_option('brp_automatic_linking_' . $current_post_type);

	if (count($children) == 0 && $automatic_linking == 1) {
		// Link posts dynamically if no linked posts are available
		create_link_bulk($current_post_id);
		// Retrieving the linked posts again
		$children = get_linked_posts($current_post_id);
	}

	if (!is_admin() && count($children) > 0 && $automatic_linking == 1) {
		$display_order = unserialize(get_option('brp_post_display_order'));
		$popup_display_order = unserialize(get_option('brp_popup_display_order'));

		$selected_display_order_values = [];
		if (is_array($display_order) && count($display_order) > 0) {
			foreach ($display_order as $item) {
				$selected_display_order_values[] = array("value" => $item, "label" =>  str_replace("_", " ", ucwords($item)));
			}
		}
		$selected_popup_display_order_values = [];
		if (is_array($popup_display_order) && count($popup_display_order) > 0) {
			foreach ($popup_display_order as $item) {
				$selected_popup_display_order_values[] = array("value" => $item, "label" =>  str_replace("_", " ", ucwords($item)));
			}
		}

		ob_start();
		$title_length = get_option('brp_title_length_' . $current_post_type);
		$title = get_option('brp_title_' . $current_post_type) ? get_option('brp_title_' . $current_post_type) : "";
		$title_tag = get_option('brp_title_tag_' . $current_post_type) != "" ? get_option('brp_title_tag_' . $current_post_type) : "h2";
		$description = get_option('brp_description_' . $current_post_type) ? get_option('brp_description_' . $current_post_type) : "";
		$description_tag = get_option('brp_description_tag_' . $current_post_type) != "" ? get_option('brp_description_tag_' . $current_post_type) : "p";
		$column_class = get_option('brp_posts_per_row') ? get_option('brp_posts_per_row') : "4";
		$date_format = get_option('brp_date_format') ? get_option('brp_date_format') : "d-m-Y";
		echo "<section class='wp-block-e25m-section bs-section bs-section---default bs-section--related-posts-for-" . $current_post_type . "'>";
		echo "<div class='container'>";
		echo "<div class='bs-related-posts bs-related-posts---default'>";
		if ($title || $description) {
			echo "<div class='row'><div class='col-md-12'>";
			if ($title) {
				echo "<" . $title_tag . " class='bs-related-posts__title'>" . $title . "</" . $title_tag . ">";
			}
			if ($description) {
				echo "<" . $description_tag . " class='bs-related-posts__description'>" . $description . "</" . $description_tag . ">";
			}
			echo "</div></div>";
		}
		echo "<div class='row'>";

		foreach ($children as $single_post) {
			// TO DO: Need to render these values from a common location
			$default_post_atts = array(
				'selectedPostType'     => $current_post_type,
				'selectedPost'         => ['value' => $single_post->ID, 'label' => ''],
				'postVisibility'      => 1,
				'imageAppearance' => 'image',
				'anchorAppearance'     => 'full',
				'dateFormat'           => $date_format,
				'displayOrder'         => [
					['value' => 'title', 'label' => ''],
					['value' => 'image', 'label' => ''],
					['value' => 'more', 'label' => ''],
				],
				'popupDisplayOrder'    => [
					['value' => 'title', 'label' => ''],
					['value' => 'image', 'label' => ''],
					['value' => 'more', 'label' => ''],
				],
				'titleTag'             => 'h5',
				'singlePostClass'      => "",
				'singlePostClassNames' => [['value' => 'bs-single-post---default', 'label' => 'Default']],
				'fancyboxStyleClassName' => "",
				'titleCharLimit' => $title_length ?? 0,
			);
			echo "<div class='col-md-" . $column_class . "'>";

			if (count($selected_display_order_values) > 0) {
				$default_post_atts['displayOrder'] = $selected_display_order_values;
			}
			if (count($selected_popup_display_order_values) > 0) {
				$default_post_atts['popupDisplayOrder'] = $selected_popup_display_order_values;
			}
			$output = single_post_render_callback($default_post_atts);
			echo $output;
			echo "</div>";
		}
		echo "</div>";
		echo "</div>";
		echo "</div>";
		echo "</section>";

		$post_output = ob_get_contents();
		ob_end_clean();
		return $post_output;
	}
}

