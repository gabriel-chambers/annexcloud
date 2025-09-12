<?php

namespace Opinionstage\Infrastructure;

defined( 'ABSPATH' ) || die();

class Helper {

    public static function is_user_logged_in() {
        $os_options = (array) get_option( OPINIONSTAGE_OPTIONS_KEY );
        return isset( $os_options['uid'] ) && isset( $os_options['email'] );
    }

    public static function get_user_access_token() {
        $os_options = self::get_opinionstage_option();

        if ( isset( $os_options['token'] ) ) {
            return $os_options['token'];
        } else {
            return null;
        }
    }

    public static function get_opinionstage_option() {
        return (array) get_option( OPINIONSTAGE_OPTIONS_KEY );
    }

    public static function is_my_items_admin_page() {
        if( ! function_exists( 'get_current_screen' ) ) {
            return false;
        }

        $current_screen = get_current_screen();
        return $current_screen->id === 'toplevel_page_opinionstage-settings';
    }

    public static function generate_template_url( $path ) {
		return add_query_arg(
			OPINIONSTAGE_UTM_PARAMETERS,
			OPINIONSTAGE_SERVER_BASE . '/templates/' . $path
		);
	}

    public static function get_link_target_blank_attribute() {
        return ' target="_blank" rel="noopener" ';
    }

    /**
     * Check if the requested plugin is already available
     */
    public static function check_plugin_available( $plugin_key ) {
        $other_widget = (array) get_option($plugin_key); // Check the key of the other plugin

        // Check if OpinionStage plugin already installed.
        return (isset($other_widget['uid']) ||
            isset($other_widget['email']));
    }

    /**
     * Logging function, should be used with care, users dislike debug/error messages
     *
     * If WordPress has debugging enabled, this function will output messages to
     * WP_DEBUG_LOG ( usually wp-contents/debug.log file)
     *
     * @param string $message message to log.
     */
    public static function error_log( $message ) {
        if ( ! defined( 'WP_DEBUG' ) || true !== WP_DEBUG ) {
            return;
        }

        // phpcs:disable WordPress.PHP.DevelopmentFunctions
        error_log( '[opinionstage plugin] ' . $message );
        // phpcs:enable
    }

    /**
     * Generates name of asset
     *
     * @param string $name name.
     * @return string
     */
    public static function get_asset_name( $name ) {
        return 'opinionstage-' . $name;
    }
}
