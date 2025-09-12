<?php
/**
 * Entry point for all plugin functionality for admin area
 *
 * @package   OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();

require plugin_dir_path( __FILE__ ) . 'login-callback.php';
require plugin_dir_path( __FILE__ ) . 'disconnect.php';
require plugin_dir_path( __FILE__ ) . 'menu-page.php';
require plugin_dir_path( __FILE__ ) . 'admin-page-loader.php';
