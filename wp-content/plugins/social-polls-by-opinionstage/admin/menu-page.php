<?php
/**
 * Registers admin pages
 *
 * @package OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();
require_once plugin_dir_path( __FILE__ ) . '../includes/client-session.php';
add_action( 'admin_menu', 'opinionstage_register_menu_page' );

/**
 * Registers admin pages
 */
function opinionstage_register_menu_page() {
	if ( function_exists( 'add_menu_page' ) ) {
		$os_client_logged_in = opinionstage_user_logged_in();
		if ( $os_client_logged_in ) {
			add_menu_page(
				__( 'Opinion Stage', 'social-polls-by-opinionstage' ),
				__( 'Opinion Stage', 'social-polls-by-opinionstage' ),
				'edit_posts',
				OPINIONSTAGE_MENU_SLUG,
				'opinionstage_load_template',
				plugins_url( 'admin/images/os-icon.svg', plugin_dir_path( __FILE__ ) ),
				'25.234323221'
			);
			add_submenu_page( OPINIONSTAGE_MENU_SLUG, 'View My Items', 'My Items', 'edit_posts', OPINIONSTAGE_MENU_SLUG );
			add_submenu_page( OPINIONSTAGE_MENU_SLUG, 'Tutorials & Help', 'Tutorials & Help', 'edit_posts', OPINIONSTAGE_HELP_RESOURCE_SLUG, 'opinionstage_load_template' );
		} else {
			add_menu_page(
				__( 'Opinion Stage', 'social-polls-by-opinionstage' ),
				__( 'Opinion Stage', 'social-polls-by-opinionstage' ),
				'edit_posts',
				OPINIONSTAGE_GETTING_STARTED_SLUG,
				'opinionstage_load_template',
				plugins_url( 'admin/images/os-icon.svg', plugin_dir_path( __FILE__ ) ),
				'25.234323221'
			);
			add_submenu_page( OPINIONSTAGE_GETTING_STARTED_SLUG, 'Get Started', 'Get Started', 'edit_posts', OPINIONSTAGE_GETTING_STARTED_SLUG, 'opinionstage_load_template' );
		}
	}
}

/**
 * Loads admin pages
 */
function opinionstage_load_template() {
	$OSAPL = OpinionStageAdminPageLoader::get_instance();
	$OSAPL->maybe_load_template_file();
}


/**
 * Helper function
 *
 * @return bool
 */
function opinionstage_is_my_items_admin_page() {
	$out = false;
	if ( function_exists( 'get_current_screen' ) ) {
		$current_screen = get_current_screen();
		$out            = 'toplevel_page_opinionstage-settings' === $current_screen->id;
	}
	return $out;
}
