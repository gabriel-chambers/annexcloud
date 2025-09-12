<?php

namespace ContentEmbed;

/**
 * Plugin Name: HubSpot Content embed
 * Description: Create content using HubSpot's personalization tools and insert them on your WordPress site.
 * Plugin URI: https://www.hubspot.com/
 * Text Domain: contentembed
 * Domain Path: /languages
 * Version: 1.3.7
 * Author: HubSpot
 * Author URI: http://hubspot.com/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin name
if ( ! defined( 'HS_EMBED_NAME' ) ) {
	define( 'HS_EMBED_NAME', 'HubSpot Content embed plugin' );
}

// Plugin version
if ( ! defined( 'HS_EMBED_VERSION' ) ) {
	define( 'HS_EMBED_VERSION', '1.3.7' );
}

// Minimum version of Leadin
if ( ! defined( 'LEADIN_REQUIRED_MIN_VERSION' ) ) {
	define( 'LEADIN_REQUIRED_MIN_VERSION', '11.0.0' );
}

// Plugin Root File
if ( ! defined( 'HS_EMBED_PLUGIN_FILE' ) ) {
	define( 'HS_EMBED_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'HS_EMBED_BASE_PATH' ) ) {
	define( 'HS_EMBED_BASE_PATH', __FILE__ );
}

if ( ! defined( 'HS_EMBED_PLUGIN_DIR' ) ) {
	define( 'HS_EMBED_PLUGIN_DIR', untrailingslashit( dirname( HS_EMBED_BASE_PATH ) ) );
}

if ( ! defined( 'HS_EMBED_PLUGIN_BASE_PATH' ) ) {
	define( 'HS_EMBED_PLUGIN_BASE_PATH', plugins_url( '', HS_EMBED_PLUGIN_FILE ) );
}
if ( ! defined( 'HS_EMBED_PLUGIN_ASSETS_BASE_PATH' ) ) {
	define( 'HS_EMBED_PLUGIN_ASSETS_BASE_PATH', plugins_url( '', HS_EMBED_PLUGIN_FILE ) . '/public/assets' );
}

if ( ( ! defined( 'HS_EMBED_PLUGIN_JS_BASE_PATH' ) ) ) {
	define( 'HS_EMBED_PLUGIN_JS_BASE_PATH', plugins_url( '', HS_EMBED_PLUGIN_FILE ) . '/build' );
}

require_once HS_EMBED_PLUGIN_DIR . '/vendor/autoload.php';
require_once __DIR__ . '/inc/core.php';

use \ContentEmbed\ContentEmbed;

function hs_detect_has_leadin() {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
	$status = array(
		'has_leadin' => false,
		'message'    => 'leadinMissing',
	);

	if ( ! is_plugin_active( 'leadin/leadin.php' ) ) {
		return $status;
	}

	$leadin_data    = get_plugin_data( WP_PLUGIN_DIR . '/leadin/leadin.php', false, false );
	$leadin_version = $leadin_data['Version'];

	if ( '{{ DEV_VERSION }}' === $leadin_version || version_compare( $leadin_version, LEADIN_REQUIRED_MIN_VERSION, '>=' ) ) {
		$status = array(
			'has_leadin' => true,
			'message'    => '',
		);
	} else {
		$status['message'] = 'oldVersion';
	}

	return $status;
}

add_action(
	'plugins_loaded',
	function () {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';

		$leadin_status = hs_detect_has_leadin();

		if ( is_admin() && ! $leadin_status['has_leadin'] ) {
			deactivate_plugins( 'hubspot-content-embed/content-embed.php' );
			load_plugin_textdomain( 'contentembed', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			add_action(
				'admin_notices',
				function () use ( $leadin_status ) {
					?>
			<div id="content-embed-disconnected-banner" class="notice notice-error is-dismissible">
				<p>
						<img src="<?php echo esc_url( HS_EMBED_PLUGIN_ASSETS_BASE_PATH . '/images/sprocket-orange.svg' ); ?>" height="16" style="margin-bottom: -3px" />
					&nbsp;
						<?php
						// Need to do this so strings get inlined properly
						switch ( $leadin_status['message'] ) {
							case 'oldVersion':
								echo wp_kses_post( __( 'oldVersion', 'contentembed' ) );
								break;
							default:
								echo wp_kses_post( __( 'leadinMissing', 'contentembed' ) );
						}
						?>
				</p>
			</div>
					<?php
				}
			);
		} elseif ( $leadin_status['has_leadin'] ) {
			new ContentEmbed();
		}
	}
);



register_activation_hook( HS_EMBED_PLUGIN_FILE, 'ContentEmbed\content_embed_activation' );
register_deactivation_hook( HS_EMBED_PLUGIN_FILE, 'ContentEmbed\content_embed_uninstall' );

function content_embed_activation() {
	/**
	* Cannot use globals in activate hook, so declare here.
	* There are a few things associated w/ init here
	* 1) Embed init transient: did the plugin recently initialize?
	* 2) Embed init option: has the plugin ever initialized (in this install cycle)?
	* 3) Redirect transient: Once this loads, force a redirect to the main leadin page.
	*/

	// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	$EMBED_INIT_TRANSIENT = 'content_embed_did_init';
	$EMBED_INIT_OPTION    = 'content_embed_did_init';
	$REDIRECT_TRANSIENT   = 'content_embed_redirect_after_activation';

	if ( ! get_option( $EMBED_INIT_OPTION ) ) {
		add_option( $EMBED_INIT_OPTION, true );
		set_transient( $EMBED_INIT_TRANSIENT, true );
	}
	set_transient( $REDIRECT_TRANSIENT, true, 60 );
	// phpcs:enable
}


function content_embed_uninstall() {
	// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	$EMBED_INIT_OPTION = 'content_embed_did_init';
	delete_option( $EMBED_INIT_OPTION );
	// phpcs:enable
}


add_filter(
	'plugin_row_meta',
	function( $plugin_meta, $plugin_file, $plugin_data ) {
		if ( __FILE__ === path_join( WP_PLUGIN_DIR, $plugin_file ) ) {
			// Drop the 'View Details' link from the plugin meta row.
			unset( $plugin_meta[2] );

			// Add a 'Learn more' link to the plugin meta row.
			$url           = 'https://www.hubspot.com/products/content/embedabble-content-blocks';
			$plugin_meta[] = sprintf(
				'<a href="%s" class="" target="_blank" rel="noopener noreferrer" aria-label="%s" data-title="%s">%s</a>',
				esc_url( $url ),
				esc_attr( __( 'Learn more about HubSpot Content embed' ) ),
				esc_attr( $plugin_data['Name'] ),
				__( 'Learn more about HubSpot Content embed' )
			);
		}
		return $plugin_meta;
	},
	10,
	3
);
