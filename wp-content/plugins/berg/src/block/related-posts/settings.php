<?php

/** Includes **/
require(__DIR__ . '/lib/helper.php');
require(__DIR__ . '/inc/fields.php');
require(__DIR__ . '/inc/default-settings.php');

function brp_options_page()
{
	add_options_page(
		'Berg Related Posts', // page <title>Title</title>
		'Berg Related Posts', // menu link text
		'manage_options', // capability to access the page
		'berg-related-posts', // page URL slug
		'brp_settings_page_content', // callback function with content
	);
}
add_action('admin_menu', 'brp_options_page');

function brp_register_setting()
{
	foreach (brp_settings_groups() as $section => $settings) {
		add_settings_section(
			'berg_settings_' . $section, // section ID
			'', // title (if needed)
			'', // callback function (if needed)
			'berg-related-posts-' . $section // page slug
		);
		brp_register_settings_fields($settings, $section);
	}
}
add_action('admin_init',  'brp_register_setting');


function brp_register_settings_fields($settings, $section)
{
	foreach ($settings as $setting) {
		$args = wp_parse_args(
			$setting,
			array(
				'section'          => $section,
				'id'               => null,
				'name'             => '',
				'desc'             => '',
				'type'             => null,
				'options'          => '',
				'max'              => null,
				'min'              => null,
				'step'             => null,
				'size'             => null,
				'field_class'      => '',
				'field_attributes' => '',
				'placeholder'      => '',
			)
		);
		register_setting(
			'brp_' . $section . '_settings', // settings group name
			'brp_' . $args['id'], // option name
			'sanitize_text_field' // sanitization function
		);

		add_settings_field(
			'brp_' . $args['id'], // ID of the settings field. We save it within the brp_settings array.
			$args['name'],     // Label of the setting.
			'brp_' . $args['type'] . '_field_html', // Function to handle the setting.
			'berg-related-posts-' . $section,   // Page to display the setting. In our case it is the section as defined above.
			'berg_settings_' . $section,   // Name of the section.
			$args
		);
	}
}


function add_berg_custom_meta_box($post_type)
{
	// Limit meta box to certain post types.
	$post_types = get_post_types_by_support(array('editor', 'title', 'thumbnail'));

	if (in_array($post_type, $post_types)) {
		add_meta_box(
			'some_meta_box_name',
			__('Berg Related Posts', 'textdomain'),
			'render_meta_box_content',
			$post_type,
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'add_berg_custom_meta_box');


/**
 * Render Meta Box content.
 *
 * @param WP_Post $post The post object.
 */
function render_meta_box_content($post)
{
	// Get related posts specific meta.
	$post_meta = get_post_meta($post->ID, 'brp_post_meta', true);
	// Disable display option.
	$disable_here = isset($post_meta['brp_disable_here']) ? $post_meta['brp_disable_here'] : 0;

?>
	<p>
		<label for="brp_disable_here"><strong><?php esc_html_e('Disable Related Posts display:', 'berg-related-posts'); ?></strong></label>
		<input type="checkbox" id="brp_disable_here" name="brp_disable_here" <?php checked(1, $disable_here, true); ?> />
		<br />
		<em><?php esc_html_e('If this is checked, then Berg Related Posts will not automatically insert the related posts at the end of post content.', 'berg-related-posts'); ?></em>
	</p>



<?php

	echo "<div class='brp_mb_manage'>\n";
	// Get the children
	$children = get_linked_posts($post->ID);

	echo "<div class='brp_button_holder'>\n";

	// Build the related post link
	$url = get_admin_url() . "admin.php?page=berg_related_posts_list&amp;brp_parent=" . $post->ID;

	// WPML check
	if (isset($_GET['lang'])) {
		$url .= "&amp;lang=" . esc_attr($_GET['lang']);
	}

	echo "<span id='view-post-btn'>";
	echo "<a href='" . $url . "' class='button button-primary'>";
	_e('Add Related Posts', 'berg-related-posts');
	echo "</a>";
	echo "</span>\n";


	echo "</div>\n";

	if (count($children) > 0) {

		// Managet table
		echo "<table class='wp-list-table widefat pages brp_table_manage sortable'>\n";

		echo "<tbody>\n";
		$i = 0;
		foreach ($children as $link_id => $child) {
			$child_id = $child->ID;

			// set edit URL
			$edit_url = get_admin_url() . "post.php?post={$child_id}&amp;action=edit&amp;brp_parent={$post->ID}";

			// get post type
			$pt      = get_post_type_object($child->post_type);
			$pt_name = "undefined";

			if (null != $pt) {
				$pt_name = $pt->labels->singular_name;
			}

			echo "<tr id='{$link_id}'>\n";
			echo "<td>";
			echo "<strong><a href='{$edit_url}' class='row-title' title='{$child->post_title}'>{$child->post_title}</a></strong>\n";
			echo "<small>" . $pt_name . "</small>";
			echo "<div class='row-actions'>\n";
			echo "<span class='edit'><a href='{$edit_url}' title='" . __('Edit Post', 'berg-related-posts') . "'>";
			_e('Edit Post', 'berg-related-posts');
			echo "</a> | </span>";
			echo "<span class='trash'><a class='submitdelete' title='" . __('Unlink Related Post', 'berg-related-posts') . "' href='javascript:;'>";
			_e('Unlink Related Post', 'berg-related-posts');
			echo "</a></span>";
			echo "</div>\n";
			echo "</td>\n";
			echo "</tr>\n";
			$i++;
		}
		echo "</tbody>\n";
		echo "</table>\n";
	} else {

		echo '<br/>';
		_e('No related posts found.', 'berg-related-posts');
	}

	// Reset Post Data
	wp_reset_postdata();
	echo "</div>\n";
}


function brp_save_meta_box($post_id)
{
	$post_meta = array();
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	// If our current user can't edit this post, bail.
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	// Disable posts.
	if (isset($_POST['brp_disable_here'])) {
		$post_meta['brp_disable_here'] = 1;
	} else {
		$post_meta['brp_disable_here'] = 0;
	}

	$post_meta = apply_filters('brp_post_meta', $post_meta, $post_id);
	$post_meta_filtered = array_filter($post_meta);

	if (empty($post_meta_filtered)) {   // Checks if all the array items are 0 or empty.
		delete_post_meta($post_id, 'brp_post_meta');  // Delete the post meta if no options are set.
	} else {
		update_post_meta($post_id, 'brp_post_meta', $post_meta_filtered);
	}

	// Get the children
	$children = get_linked_posts($post_id);
	$current_post_type = get_post_type($post_id);
	$automatic_linking = get_option('brp_automatic_linking_' . $current_post_type);
	if (count($children) == 0 && $automatic_linking == 1) {
		create_link_bulk($post_id);
	}
	do_action('brp_save_meta_box', $post_id);
}
add_action('save_post', 'brp_save_meta_box');
add_action('edit_attachment', 'brp_save_meta_box');
