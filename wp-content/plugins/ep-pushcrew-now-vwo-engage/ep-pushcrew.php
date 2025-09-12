<?php
/*
Plugin Name: EP Pushcrew (now VWO Engage)
Plugin URI: https://exertplugins.com/
Description: This plugin will let you add PushCrew script to WordPress. PushCrew lets you send push notifications from your website to your users/visitor. Simply enable the plugin and start collecting subscribers for your push notification. Visit PushCrew for more details.
Author: Shafiqul
Author URI: https://suvronur.com/
Version: 1.0.0
Text Domain: eppc
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


// PLUGIN URL
define( 'EPPC_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

// PLUGIN PATH
define( 'EPPC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// PLUGIN VERSION
define( 'EPPC_NAME_VERSION', '1.0.0' );


if( !function_exists('eppc_plugin_loaded') ){
	function eppc_plugin_loaded(){
		load_plugin_textdomain( 'eppc', false, EPPC_PLUGIN_PATH . 'languages' );

		if( file_exists( EPPC_PLUGIN_PATH . '/init.php' )){
			require_once( EPPC_PLUGIN_PATH . '/init.php' );
		}
		
	}
	add_action( 'plugins_loaded', 'eppc_plugin_loaded');
} // --- End