<?php
/**
 * Utility Functions
 *
 * @package   OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();

require_once OPINIONSTAGE_PLUGIN_DIR . 'includes/logging.php';

/**
 * Returns default UTM parameters and merges passed ones
 *
 * @param array $query array of url parameters.
 *
 * @return string
 */
function opinionstage_utm_query( $query = array() ) {
	return http_build_query( array_merge( $query, OPINIONSTAGE_UTM_PARAMETERS ) );
}

/**
 * Returns UTM url
 *
 * @param string $path api path.
 * @param array  $query query args.
 * @return string
 */
function opinionstage_utm_url( $path, $query = array() ) {
	return OPINIONSTAGE_SERVER_BASE . '/' . $path . '?' . opinionstage_utm_query( $query );
}

/**
 * Registers JS on admin pages
 *
 * @param string $name asset name.
 * @param string $relative_path relative path.
 * @param array  $deps list of dependencies.
 * @param bool   $in_footer footer.
 */
function opinionstage_register_javascript_asset( $name, $relative_path, $deps = array(), $in_footer = true ) {
	$registered = wp_register_script(
		opinionstage_asset_name( $name ),
		plugins_url( opinionstage_asset_path() . '/js/' . $relative_path, plugin_dir_path( __FILE__ ) ),
		$deps,
		OPINIONSTAGE_WIDGET_VERSION,
		$in_footer
	);

	if ( ! $registered ) {
		opinionstage_error_log( "javascript asset '$name' registration failed" );
	}
}

/**
 * Registers CSS on admin pages
 *
 * @param string $name name.
 * @param string $relative_path relative path.
 */
function opinionstage_register_css_asset( $name, $relative_path ) {
	wp_register_style(
		opinionstage_asset_name( $name ),
		plugins_url( opinionstage_asset_path() . '/css/' . $relative_path, plugin_dir_path( __FILE__ ) ),
		null,
		OPINIONSTAGE_WIDGET_VERSION
	);
}

/**
 * Enqueue JS
 *
 * @param string $name name.
 */
function opinionstage_enqueue_js_asset( $name ) {
	wp_enqueue_script( opinionstage_asset_name( $name ) );
}

/**
 * Enqueue CSS
 *
 * @param string $name name.
 */
function opinionstage_enqueue_css_asset( $name ) {
	wp_enqueue_style( opinionstage_asset_name( $name ) );
}

/**
 * Generates name of asset
 *
 * @param string $name name.
 * @return string
 */
function opinionstage_asset_name( $name ) {
	return 'opinionstage-' . $name;
}

/**
 * Returns path of asset
 *
 * @return string
 */
function opinionstage_asset_path() {
	return is_admin() ? 'admin' : 'public';
}

/**
 * Generates a to the callback page used to connect the plugin to the Opinion Stage account
 */
function opinionstage_callback_url() {
	return menu_page_url( OPINIONSTAGE_LOGIN_CALLBACK_SLUG, false );
}


/**
 * Returns widget templates link
 *
 * @param string $type widget type.
 * @return string
 */
function opinionstage_get_templates_url_for_type( $type ) {
	return add_query_arg( 'page', $type, OPINIONSTAGE_REDIRECT_TEMPLATES_API_UTM );
}
