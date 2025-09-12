<?php

/**
 * Plugin Name: Berg Custom - Gutenberg Blocks
 * Requires Plugins: advanced-custom-fields-pro
 * Version: 1.0.0
 *
 * @package Berg Custom
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

defined('BERG_CUSTOM_VERSION') || define('BERG_CUSTOM_VERSION', '1.0.0');
defined('BERG_CUSTOM_FILE') || define('BERG_CUSTOM_FILE', __FILE__);
defined('BERG_CUSTOM_I18N') || define('BERG_CUSTOM_I18N', 'berg-custom-ultimate-gutenberg-blocks'); // Plugin slug.

/********************************************************************************************
 * Activation & PHP version checks.
 ********************************************************************************************/

if (!function_exists('berg_php_requirement_activation_check_custom')) {

	/**
	 * check if we have the proper PHP version.
	 * Show an error if needed and don't continue with the plugin.
	 *
	 *
	 */
	function berg_php_requirement_activation_check_custom()
	{
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			deactivate_plugins(basename(__FILE__));
			wp_die(
				sprintf(
					__('%s"berg" can not be activated. %s It requires PHP version 5.3.0 or higher, but PHP version %s is used on the site. Please upgrade your PHP version first ✌️ %s Back %s', BERG_I18N),
					'<strong>',
					'</strong><br><br>',
					PHP_VERSION,
					'<br /><br /><a href="' . esc_url(get_dashboard_url(get_current_user_id(), 'plugins.php')) . '" class="button button-primary">',
					'</a>'
				)
			);
		}
	}
	register_activation_hook(__FILE__, 'berg_php_requirement_activation_check_custom');
}

/**
 * check the PHP version.
 * If the PHP version isn't match, don't continue to prevent any unwanted errors.
 *
 */
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	if (!function_exists('berg_php_requirement_notice_custom')) {
		function berg_php_requirement_notice_custom()
		{
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				sprintf(__('"Berg" requires PHP version 5.3.0 or higher, but PHP version %s is used on the site.', BERG_CUSTOM_I18N), PHP_VERSION)
			);
		}
	}
	add_action('admin_notices', 'berg_php_requirement_notice_custom');
	return;
}

/**
 * Block Initializer.
 */
require_once(plugin_dir_path(__FILE__) . 'init.php');
