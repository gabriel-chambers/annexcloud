<?php
/**
 * Gutenberg editor integration
 *
 * @package   OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();

if ( ! function_exists( 'register_block_type' ) ) {
	return;
}

require_once OPINIONSTAGE_PLUGIN_DIR . 'includes/client-session.php';

add_action( 'init', 'opinionstage_register_gutenberg_assets' );
add_filter( 'block_categories_all', 'opinionstage_register_gutenberg_categories' );

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * use "npm start" (development only) or "npm build"
 * from assets/gutenberg directory in order to compile assets.
 */
function opinionstage_register_gutenberg_assets() {
	$script_asset_path = OPINIONSTAGE_PLUGIN_DIR . 'assets/gutenberg/build/index.asset.php';

	$script_asset = require $script_asset_path;

	$index_js                = 'assets/gutenberg/build/index.js';
	$index_js_dependencies   = $script_asset['dependencies'];
	$index_js_dependencies[] = opinionstage_asset_name( 'content-popup' );
	wp_register_script(
		'opinionstage-gutenberg-block-editor',
		opinionstage_gutenberg_asset_url( $index_js ),
		$index_js_dependencies,
		$script_asset['version'],
		false
	);

	$editor_css = 'assets/gutenberg/build/index.css';
	wp_register_style(
		'opinionstage-gutenberg-block-editor',
		opinionstage_gutenberg_asset_url( $editor_css ),
		[],
		filemtime( OPINIONSTAGE_PLUGIN_DIR . $editor_css ),
		false
	);

    $block_type_args = [
        'editor_script' => 'opinionstage-gutenberg-block-editor',
        'editor_style'  => 'opinionstage-gutenberg-block-editor',
    ];

	register_block_type( 'opinion-stage/block-os-poll', $block_type_args );
	register_block_type( 'opinion-stage/block-os-survey', $block_type_args );
	register_block_type( 'opinion-stage/block-os-trivia', $block_type_args );
	register_block_type( 'opinion-stage/block-os-personality', $block_type_args );

	opinionstage_gutenberg_inject_data();
}

function opinionstage_register_gutenberg_categories( $categories ) {
	return array_merge(
		$categories,
        [
			[
				'slug'  => 'opinion-stage',
				'title' => __( 'Poll, Survey, Quiz & Form By Opinion Stage', 'opinion-stage' ),
            ],
		]
	);
}

/**
 * Adds javascript data (window.OPINIONSTAGE_GUTENBERG_DATA), for use by gutenberg javascript code.
 */
function opinionstage_gutenberg_inject_data() {
	wp_localize_script(
		'opinionstage-gutenberg-block-editor',
		'OPINIONSTAGE_GUTENBERG_DATA',
		[
			'userLoggedIn'       => opinionstage_user_logged_in(),
			'createNewWidgetUrl' => OPINIONSTAGE_REDIRECT_CREATE_WIDGET_API_UTM,
			'viewTemplateUrl'    => OPINIONSTAGE_REDIRECT_TEMPLATES_API_UTM,
			'loginPageUrl'       => get_admin_url( null, 'admin.php?page=' . OPINIONSTAGE_GETTING_STARTED_SLUG ),
			'OswpPluginVersion'  => OPINIONSTAGE_WIDGET_VERSION,
			'OswpClientToken'    => opinionstage_user_access_token(),
			'OswpFetchDataUrl'   => OPINIONSTAGE_CONTENT_POPUP_CLIENT_WIDGETS_API,
			'brandLogoUrl'       => plugin_dir_url( OPINIONSTAGE_PLUGIN_FILE ) . 'admin/images/os-logo.png',
		]
	);
}

/**
 * Returns plugins_url of the Gutenberg asset, specified by path.
 *
 * @param string $asset_path asset path, relative to the plugin's root directory.
 */
function opinionstage_gutenberg_asset_url( $asset_path ) {
	return plugins_url( basename( $asset_path ), OPINIONSTAGE_PLUGIN_DIR . $asset_path );
}
