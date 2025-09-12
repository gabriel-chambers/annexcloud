<?php
/**
* Plugin Name: Realm
* Description: Collection of sections built using Berg modules
* Version: 0.1.0
* Requires at least: 5.7
* Requires PHP: 7.3
* Author: Eight25Media (PVT) Ltd.
* Text Domain: realm
* Domain Path: /translations
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

require __DIR__ . '/vendor/autoload.php';

// Check minimum requirements for the plugins are available when activating the plugin.
// If not deactivate and print an error message
if (!function_exists('realm_php_requirement_activation_check')) {
	function realm_php_requirement_activation_check()
	{
		if (version_compare(PHP_VERSION, '7.3', '<')) {
			deactivate_plugins(basename(__FILE__));
			wp_die(
				sprintf(
					'%s"Realm" can not be activated. %s It requires PHP version 7.3 or higher, but PHP version %s is used on the site. Please upgrade your PHP version first ✌️ %s Back %s',
					'<strong>',
					'</strong><br><br>',
					PHP_VERSION,
					'<br /><br /><a href="' . esc_url(get_dashboard_url(get_current_user_id(), 'plugins.php')) . '" class="button button-primary">',
					'</a>'
				)
			);
		}
	}
	register_activation_hook(__FILE__, 'realm_php_requirement_activation_check');
}

// Print a notice in admin panel for minimum PHP version check
if (version_compare(PHP_VERSION, '7.3', '<')) {
	if (!function_exists('realm_php_requirement_notice')) {
		function realm_php_requirement_notice()
		{
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				sprintf(
					'"Realm" requires PHP version 7.3 or higher, but PHP version %s is used on the site.',
					PHP_VERSION
				)
			);
		}
	}
	add_action('admin_notices', 'realm_php_requirement_notice');
	return;
}

if (!function_exists('realm_main_plugin_file')) {
	function realm_main_plugin_file() {
		return __FILE__;
	}
}

function realm_blocks_load(): void {
	$context = [
		'url'     => plugin_dir_url(__FILE__),
		'path'    => plugin_dir_path(__FILE__),
		'version' => get_file_data(__FILE__, ['Version'])[0],
		'theme'   => sanitize_title(get_stylesheet()),
	];

	(new E25m\Realm\PluginLoader( $context ))->init();
}
add_action('plugins_loaded', 'realm_blocks_load');
