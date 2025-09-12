<?php
/*
  Plugin Name: CM Tooltip Glossary Pro+
  Plugin URI: https://www.cminds.com/
  Description: PRO+ Version! Parses posts for defined glossary terms and adds links to the static glossary page containing the definition and a tooltip with the definition.
  Version: 4.4.1
  Text Domain: cm-tooltip-glossary
  Author: CreativeMindsSolutions
  Author URI: https://www.cminds.com/
 * Requires at least: 4.6
 * Requires PHP: 7.4
 */

/**
 * Define Plugin Version
 *
 * @since 1.0
 */
if (!defined('CMTT_VERSION')) {
    define('CMTT_VERSION', '4.4.1');
}

/**
 * Define Plugin name
 *
 * @since 1.0
 */
if (!defined('CMTT_NAME')) {
    define('CMTT_NAME', 'CM Tooltip Glossary Pro+');
}

/**
 * Define Plugin canonical name
 *
 * @since 1.0
 */
if (!defined('CMTT_CANONICAL_NAME')) {
    define('CMTT_CANONICAL_NAME', 'CM Tooltip Glossary Pro');
}

/**
 * Define Plugin license name
 *
 * @since 1.0
 */
if (!defined('CMTT_LICENSE_NAME')) {
    define('CMTT_LICENSE_NAME', 'CM Tooltip Glossary Pro+');
}

/**
 * Define Plugin File Name
 *
 * @since 1.0
 */
if (!defined('CMTT_PLUGIN_FILE')) {
    define('CMTT_PLUGIN_FILE', __FILE__);
}

/**
 * Define Plugin URL
 *
 * @since 1.0
 */
if (!defined('CMTT_URL')) {
    define('CMTT_URL', 'https://www.cminds.com/store/tooltipglossary/');
}

/**
 * Define Plugin release notes url
 *
 * @since 1.0
 */
if (!defined('CMTT_RELEASE_NOTES')) {
    define('CMTT_RELEASE_NOTES', 'https://tooltip.cminds.com/release-notes-pro-plugin/');
}

add_action('cmtt_include_files_after', function( ) {
	require_once plugin_dir_path( __FILE__ ). 'glossaryPro.php';
	require_once plugin_dir_path(__FILE__) . "glossaryPlus.php";
});

add_action('cmtt_init_files_after', function( ) {
	CMTT_Pro::init();
	CMTT_Glossary_Plus::init();
});

require_once plugin_dir_path(__FILE__) . "glossaryFree.php";
CMTT_Free::init();

register_activation_hook(__FILE__, array('CMTT_Free', '_install'));

CMTT_Glossary_Plus::after();
