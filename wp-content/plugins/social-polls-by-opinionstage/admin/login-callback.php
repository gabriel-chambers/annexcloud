<?php
/**
 * Opinionstage Login Callback
 *
 * @package OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();

add_action( 'admin_menu', 'opinionstage_register_login_callback_page' );
add_action( 'admin_init', 'opinionstage_login_and_redirect_to_settings_page' );

/**
 * Adds page for post-login redirect and setup in form of invisible menu page.
 */
function opinionstage_register_login_callback_page() {
	add_submenu_page(
		null,
		'',
		'',
		'edit_posts',
		OPINIONSTAGE_LOGIN_CALLBACK_SLUG
	);
}

/**
 * Performs redirect to plugin settings page, after user logged in.
 */
function opinionstage_login_and_redirect_to_settings_page() {

	if ( is_user_logged_in()
		&& current_user_can( 'edit_posts' )
		&& OPINIONSTAGE_LOGIN_CALLBACK_SLUG === filter_input( INPUT_GET, 'page' ) ) {

		$uid                  = isset( $_GET['opinionstage_uid'] ) ? sanitize_text_field( $_GET['opinionstage_uid'] ) : '';
		$token                = isset( $_GET['opinionstage_token'] ) ? sanitize_text_field( $_GET['opinionstage_token'] ) : '';
		$email                = isset( $_GET['opinionstage_email'] ) ? sanitize_email( $_GET['opinionstage_email'] ) : '';
		$fly_id               = isset( $_GET['opinionstage_fly_id'] ) ? intval( $_GET['opinionstage_fly_id'] ) : '';

		opinionstage_uninstall();
		opinionstage_validate_and_save_client_data(
			compact(
				'uid',
				'token',
				'email',
				'fly_id'
			)
		);

		if ( wp_safe_redirect( admin_url( 'admin.php?page=' . OPINIONSTAGE_MENU_SLUG ), 302 ) ) {
			exit;
		}
	}
}

/**
 * Validates and saves client data.
 *
 * @param array $raw raw data.
 */
function opinionstage_validate_and_save_client_data( $raw ) {

	$os_options = array(
		'uid'                      => $raw['uid'],
		'email'                    => $raw['email'],
		'fly_id'                   => $raw['fly_id'],
		'version'                  => OPINIONSTAGE_WIDGET_VERSION,
		'fly_out_active'           => 'false',
		'article_placement_active' => 'false',
		'sidebar_placement_active' => 'false',
		'token'                    => $raw['token'],
	);

	if ( preg_match( '/^[0-9]+$/', $raw['fly_id'] ) ) {
		update_option( OPINIONSTAGE_OPTIONS_KEY, $os_options );
	}
}
