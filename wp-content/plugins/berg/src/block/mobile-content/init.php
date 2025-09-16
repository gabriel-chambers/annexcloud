<?php

/**
 * Initializes the inner blocks of the Mobile Content block.
 */
function mobile_content_block_init()
{
	register_block_type('e25m/desktop-content-wrapper', array(
		'render_callback' => 'render_mobile_content_inner_blocks'
	));
	register_block_type('e25m/mobile-content-wrapper', array(
		'render_callback' => 'render_mobile_content_inner_blocks'
	));
}
add_action('init', 'mobile_content_block_init');

/**
 * This function is called when the inner blocks are being rendered on the front end of the site
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 */
function render_mobile_content_inner_blocks($attributes, $content, $block)
{
	//Renders the block content
	return $content;
}

/**
 * Checks if the current device is a mobile device or the mobile preview mode is enabled
 */
function mobile_device_verification()
{
	if (isset($_GET['preview-mode'])) {
		return $_GET['preview-mode'] !== 'desktop';
	}
	// In-built Wordpress function to detect the current device
	return wp_is_mobile();
}

/**
 * Includes the mobile preview layout
 */
function mobile_content_preview_mode()
{
	include  plugin_dir_path(__FILE__) . 'templates/template-preview.php';
	wp_die();
}
add_action('wp_ajax_preview_mode', 'mobile_content_preview_mode');

/**
 * Overrides block render function
 *
 * @param string $pre_render_content
 * @param WP_Block $parsed_block
 * @param WP_Block|null $parent_block
 * @return void
 */

function pre_render_mobile_content_inner_blocks($pre_render_content, $parsed_block, $parent_block)
{
	$mobile_tab_enabled = false;
	if(isset($parent_block->parsed_block['blockName']) && $parent_block->parsed_block['blockName'] === 'e25m/mobile-content') {
		if(array_key_exists('enableMobileTab', $parent_block->parsed_block['attrs'])) {
			$mobile_tab_enabled = $parent_block->parsed_block['attrs']['enableMobileTab'];
		}
		if($parsed_block['blockName'] === 'e25m/desktop-content-wrapper') {
			if(mobile_device_verification() && $mobile_tab_enabled) {
				return "";
			}
		}
		else if($parsed_block['blockName'] === 'e25m/mobile-content-wrapper') {
			if(!mobile_device_verification() || (mobile_device_verification() && !$mobile_tab_enabled)) {
				return "";
			}
		}
	}
	return $pre_render_content;
}
add_filter('pre_render_block', 'pre_render_mobile_content_inner_blocks', 10, 3);

