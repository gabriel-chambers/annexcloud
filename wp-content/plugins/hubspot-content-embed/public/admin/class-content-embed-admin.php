<?php

namespace ContentEmbed\admin;

use ContentEmbed\EmbedTracking;
use ContentEmbed\AssetManager;
use \Leadin\AssetsManager as LeadinAssetsManager;
use \Leadin\admin\AdminConstants as LeadinAdminConstants;

/**
 * Class responsible for initializing the admin side of the plugin.
 */
class ContentEmbedAdmin {

	const REDIRECT_TRANSIENT = 'content_embed_redirect_after_activation';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'redirect_after_activation' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Check last synced time and forward bulk tracking data if over half an hour ago.
	 */
	public static function check_and_update_last_sync() {
		// Doing this with options instead of WP_CRON because WP_CRON not enabled always.
		$last_sync    = get_option( AssetManager::LAST_SYNCED_OPTION );
		$current_time = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

		// Option over half hour old
		if ( ! $last_sync || ( $current_time - $last_sync ) > 1800 ) {
			update_option( AssetManager::LAST_SYNCED_OPTION, $current_time );
			EmbedTracking::forward_bulk_tracking_data();
		}
	}


	/**
	 * Enqueue assets needed for admin section
	 */
	public static function enqueue_admin_scripts() {
		wp_enqueue_script( AssetManager::ADMIN_EMBED_TRACKER, HS_EMBED_PLUGIN_JS_BASE_PATH . '/adminPageLeadinSetup.js', array( 'jquery' ), HS_EMBED_VERSION, true );

		/**
		 * Add the leadin config object to the window.
		 * We need to do this independent of Leadin because there are places we need the iframe where they don't enqueue it.
		 * TODO: Maybe only do this if it's on the right page (plugins.php)?
		*/
		wp_localize_script( AssetManager::ADMIN_EMBED_TRACKER, LeadinAssetsManager::LEADIN_CONFIG, LeadinAdminConstants::get_background_leadin_config(), 1 );

		wp_enqueue_style( 'wp-components' );

		EmbedTracking::forward_bulk_tracking_data();
		self::track_plugin_activation();
		self::check_and_update_last_sync();
	}

	public static function track_plugin_activation() {
		$did_init = get_transient( AssetManager::EMBED_INIT_TRANSIENT );
		if ( $did_init ) {
			add_action( 'admin_footer', array( 'ContentEmbed\EmbedTracking', 'track_plugin_activation' ), PHP_INT_MAX );
			delete_transient( AssetManager::EMBED_INIT_TRANSIENT );
		}
	}

	public static function redirect_after_activation() {
		if ( get_transient( self::REDIRECT_TRANSIENT ) ) {
			delete_transient( self::REDIRECT_TRANSIENT );

			// Check if the plugin was installed via the embedded app. Don't redirect if so.
			$did_install_in_ui = get_option( 'leadin_content_embed_ui_install' );
			delete_option( 'leadin_content_embed_ui_install' );
			if ( ! $did_install_in_ui ) {
				$leadin_url = 'admin.php?page=leadin';
				wp_safe_redirect( admin_url( $leadin_url ) );
				exit;
			}
		}
	}
}
