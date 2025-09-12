<?php
/**
 * Resources
 *
 * @package OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();

/**
 * Registers common assets
 */
function opinionstage_common_load_resources() {
	$current_screen = get_current_screen();
	if ( in_array( $current_screen->id, array( 'toplevel_page_opinionstage-settings', 'opinion-stage_page_opinionstage-help-resource', 'toplevel_page_opinionstage-getting-started' ), true ) ) {
		opinionstage_register_css_asset( 'menu-page', 'menu-page.css' );
		opinionstage_enqueue_css_asset( 'menu-page' );
	}

	if ( opinionstage_is_my_items_admin_page() ) {
		opinionstage_register_javascript_asset( 'menu-page', 'menu-page.js', array( 'jquery' ) );
		opinionstage_enqueue_js_asset( 'menu-page' );
	}
}
