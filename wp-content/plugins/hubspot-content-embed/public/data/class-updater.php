<?php

namespace ContentEmbed;

class Updater {

	const PLUGIN_VERSIONS_URL = 'https://api.hubspot.com/content-embed/v1/plugin/metadata';
	const PLUGIN_ID           = 'hubspot-content-embed/content-embed.php';
	const PLUGIN_SLUG         = 'hubspot-content-embed';
	const PLUGIN              = 'hubspot-content-embed/content-embed.php';

	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'pre_set_site_transient_update_plugins', array( __CLASS__, 'update_plugins_filter' ) );
		}
	}

	private static function check_for_updates() {
		$response = wp_remote_get( self::PLUGIN_VERSIONS_URL );

		if ( is_wp_error( $response ) ) {
			return 'Error: ' . $response->get_error_message();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || ! isset( $data['new_version'] ) ) {
			return 'Error: Invalid response';
		}

		return $data;
	}

	public static function update_plugins_filter( $transient ) {
		$update_data = self::check_for_updates();
		if ( ! is_array( $update_data ) ) {
			return $transient;
		}
		$new_version = $update_data['new_version'];
		$is_update   = version_compare( HS_EMBED_VERSION, $new_version, '<' );

		// Validate the transient
		if ( ! is_object( $transient ) ) {
			$transient = new \stdClass();
		}

		if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			$transient->response = array();
		}

		if ( ! isset( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
			$transient->no_update = array();
		}

		$base_obj = array(
			'id'     => self::PLUGIN_ID,
			'slug'   => self::PLUGIN_SLUG,
			'plugin' => self::PLUGIN,
		);

		if ( $is_update ) {
			// Update is available.
			$update                                 = (object) array_merge( $base_obj, $update_data );
			$transient->response[ self::PLUGIN_ID ] = $update;
		} else {
			$item                                    = (object) array_merge(
				$base_obj,
				array( 'new_version' => HS_EMBED_VERSION )
			);
			$transient->no_update[ self::PLUGIN_ID ] = $item;
		}

		return $transient;
	}
}
