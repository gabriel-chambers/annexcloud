<?php

//Featuerd Image
add_theme_support('post-thumbnails');

//Editor Styles
add_theme_support('editor-styles');

// Styles of vendor libs like slick.js and fancybox.js
add_editor_style('/dist/css/vendor.css');

/**
 * @param $data
 * @param $limit
 * @param bool $wpAutoP
 *
 * @return string
 */
if (!function_exists('charLimit')) {
	function charLimit($data, $limit, $wpAutoP = true)
	{
		if ($wpAutoP) {
			return wpautop(html_cut($data, $limit));
		} else {
			return html_cut($data, $limit);
		}
	}
}

/**
 * @param $text
 * @param $max_length
 *
 * @return string
 */
if (!function_exists('html_cut')) {
	function html_cut($text, $max_length)
	{
		if (strlen($text) > $max_length) {
			$endStr = '...';
		} else {
			$endStr = '';
		}

		$tags   = array();
		$result = "";

		$is_open          = false;
		$grab_open        = false;
		$is_close         = false;
		$in_double_quotes = false;
		$in_single_quotes = false;
		$tag              = "";

		$i        = 0;
		$stripped = 0;

		$stripped_text = strip_tags($text);

		while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length) {
			$symbol = $text[$i];
			$result .= $symbol;

			switch ($symbol) {
				case '<':
					$is_open   = true;
					$grab_open = true;
					break;
				case '"':
					if ($in_double_quotes) {
						$in_double_quotes = false;
					} else {
						$in_double_quotes = true;
					}
					break;
				case "'":
					if ($in_single_quotes) {
						$in_single_quotes = false;
					} else {
						$in_single_quotes = true;
					}
					break;
				case '/':
					if ($is_open && !$in_double_quotes && !$in_single_quotes) {
						$is_close  = true;
						$is_open   = false;
						$grab_open = false;
					}
					break;
				case ' ':
					if ($is_open) {
						$grab_open = false;
					} else {
						$stripped++;
					}
					break;
				case '>':
					if ($is_open) {
						$is_open   = false;
						$grab_open = false;
						array_push($tags, $tag);
						$tag = "";
					} else if ($is_close) {
						$is_close = false;
						array_pop($tags);
						$tag = "";
					}
					break;
				default:
					if ($grab_open || $is_close) {
						$tag .= $symbol;
					}
					if (!$is_open && !$is_close) {
						$stripped++;
					}
			}
			$i++;
		}

		$tagCount = count($tags);
		$i        = 1;
		if ($tags) {
			while ($tags) {
				if ($i < $tagCount) {
					$result .= "</" . array_pop($tags) . ">";
				} else {
					$result .= $endStr . "</" . array_pop($tags) . ">";
				}
				$i++;
			}
		} else {
			$result .= $endStr;
		}

		return $result;
	}
}

// Reusable Menu
if (!function_exists('add_reusable_blocks_menu_to_admin_navigation')) {
	function add_reusable_blocks_menu_to_admin_navigation()
	{
		add_menu_page(
			'Reusable Blocks',
			'Reusable Blocks',
			'edit_posts',
			'edit.php?post_type=wp_block',
			'',
			'dashicons-editor-table',
			20
		);
	}
	add_action('admin_menu', 'add_reusable_blocks_menu_to_admin_navigation');
}

// To return three dots for excerpt more option
function custom_excerpt_more()
{
	return '...';
}
add_filter('excerpt_more', 'custom_excerpt_more');

function wp_title_for_home($title)
{
	if (empty($title) && (is_home() || is_front_page())) {
		$title = get_bloginfo('name') . ' | ' . get_bloginfo('description');
	}
	return $title;
}

add_filter('wp_title', 'wp_title_for_home');

/**
 * Enable unfiltered_html capability for Editors.
 *
 * @param  array  $caps    The user's capabilities.
 * @param  string $cap     Capability name.
 * @param  int    $user_id The user ID.
 * @return array  $caps    The user's capabilities, with 'unfiltered_html' potentially added.
 */

if (!function_exists('add_unfiltered_html_capability_to_editors')) {
	function add_unfiltered_html_capability_to_editors($caps, $cap, $user_id)
	{
		if ('unfiltered_html' === $cap && (user_can($user_id, 'editor') || user_can($user_id, 'administrator'))) {
			$caps = array('unfiltered_html');
		}
		return $caps;
	}
	add_filter('map_meta_cap', 'add_unfiltered_html_capability_to_editors', 1, 3);
}
