<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

if (!function_exists('berg_block_assets')) {

	function berg_block_assets()
	{
		$enqueue_styles_in_frontend = apply_filters('berg_enqueue_styles', !is_admin());
		$enqueue_scripts_in_frontend = apply_filters('berg_enqueue_scripts', !is_admin());

		// Frontend block styles.
		if (is_admin() || $enqueue_styles_in_frontend) {
			wp_enqueue_style(
				'e25m-style-css',
				plugins_url('dist/frontend_blocks_styles.css', BERG_FILE),
				array(),
				BERG_VERSION
			);
		}

		// Frontend only scripts.
		if ($enqueue_scripts_in_frontend) {
			wp_enqueue_script(
				'common-vendor',
				plugins_url('dist/common_vendor.js', BERG_FILE),
				array(),
				BERG_VERSION
			);
			wp_enqueue_script(
				'e25m-block-frontend-js',
				plugins_url('dist/frontend_blocks_scripts.js', BERG_FILE),
				array('vendor-js', 'common-vendor', 'lodash', 'child-main-js'),
				BERG_VERSION,
				true
			);
			wp_localize_script(
				'e25m-block-frontend-js',
				'berg',
				array(
					'restUrl' => get_rest_url(),
				)
			);
		}
	}
	add_action('enqueue_block_assets', 'berg_block_assets');
}

if (!function_exists('berg_block_editor_assets')) {
	/**
	 * Enqueue block assets for backend editor
	 */
	function berg_block_editor_assets()
	{
		// Backend editor scripts: common vendor files.
		wp_enqueue_script(
			'berg-block-js-vendor',
			plugins_url('dist/common_vendor.js', BERG_FILE),
			array(),
			BERG_VERSION
		);

		// Backend editor scripts: blocks.
		$dependencies = array('berg-block-js-vendor', 'code-editor', 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-util', 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-api', 'wp-polyfill', 'wp-server-side-render','lodash');
		wp_enqueue_script(
			'berg-block-js',
			plugins_url('dist/editor_blocks_scripts.js', BERG_FILE),
			// wp-util for wp.ajax.
			// wp-plugins & wp-edit-post for Gutenberg plugins.
			apply_filters('berg_editor_blocks_dependencies', $dependencies),
			BERG_VERSION,
			true
		);

		// Backend editor only styles.
		wp_enqueue_style(
			'berg-block-editor-css',
			plugins_url('dist/editor_blocks_styles.css', BERG_FILE),
			array('wp-edit-blocks'),
			BERG_VERSION
		);
	}

	add_action('enqueue_block_editor_assets', 'berg_block_editor_assets');
}

//add page/post read time setting
if (file_exists(plugin_dir_path(__FILE__) . 'read-time-setting.php'))
	require_once plugin_dir_path(__FILE__) . 'read-time-setting.php';

//add usage page id column to reusable block table
if (file_exists(plugin_dir_path(__FILE__) . 'add-usage-page-ids-column.php'))
	require_once plugin_dir_path(__FILE__) . 'add-usage-page-ids-column.php';

/**
 * Block Initializer.
 */
if (file_exists(plugin_dir_path(__FILE__) . 'src/block/post-block/init.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/post-block/init.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/pro-button/pro-button.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/pro-button/pro-button.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/single-post/single-post.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/single-post/single-post.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/section/section.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/section/section.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/row/row.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/row/row.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/column/column.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/column/column.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/div/div.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/div/div.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/media-elements/init.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/media-elements/init.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/inner-menu/inner-menu.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/inner-menu/inner-menu.php';

// Packagist version of tab-slider-v2
if (file_exists(plugin_dir_path(__FILE__) . 'src/block/tab-slider-pro/init.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/tab-slider-pro/init.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/tab-slider-v2/init.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/tab-slider-v2/init.php';

// Packagist version of slider-v2
if (file_exists(plugin_dir_path(__FILE__) . 'src/block/slider-pro/init.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/slider-pro/init.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/slider-v2/init.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/slider-v2/init.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/logo-slider/init.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/logo-slider/init.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/advance-accordion/init.php'))
	require_once plugin_dir_path(__FILE__) . 'src/block/advance-accordion/init.php';

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/embedded-forms/embedded-forms.php'))
	require_once(plugin_dir_path(__FILE__) . 'src/block/embedded-forms/embedded-forms.php');

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/related-posts/init.php'))
	require_once(plugin_dir_path(__FILE__) . 'src/block/related-posts/init.php');

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/slider-accordion/init.php'))
	require_once(plugin_dir_path(__FILE__) . 'src/block/slider-accordion/init.php');

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/advanced-blurb/init.php'))
	require_once(plugin_dir_path(__FILE__) . 'src/block/advanced-blurb/init.php');

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/blurb/init.php'))
	require_once(plugin_dir_path(__FILE__) . 'src/block/blurb/init.php');

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/mobile-content/init.php'))
	require_once(plugin_dir_path(__FILE__) . 'src/block/mobile-content/init.php');

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/lottie-animator/init.php'))
	require_once(plugin_dir_path(__FILE__) . 'src/block/lottie-animator/init.php');
