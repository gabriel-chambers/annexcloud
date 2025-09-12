<?php

namespace Opinionstage\Modules;

defined( 'ABSPATH' ) || die();

use Opinionstage;
use Opinionstage\Core\Module;
use Opinionstage\Infrastructure\Helper;
use Opinionstage\Infrastructure\SingleUseNonce;
use Opinionstage\Infrastructure\TemplatesViewer;


class Admin {

    use Module;

    public function init() {
        // login callbacks
        add_action( 'admin_menu', [ $this, 'register_login_callback_page' ] );
        add_action( 'admin_init', [ $this, 'login_and_redirect_to_settings_page' ] );

        // disconnect callbacks
        add_action( 'admin_menu', [ $this, 'disconnect_account_menu' ] );
        add_action( 'admin_init', [ $this, 'disconnect_account_action' ] );

        // classic editor media button
        add_action( 'media_buttons', [ $this, 'print_content_popup_add_editor_button' ] );

        add_action( 'admin_menu', [ $this, 'register_menu_page' ] );

        add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
    }

    public function print_content_popup_add_editor_button() {
        // todo - move to template
        require Opinionstage::get_instance()->plugin_path . 'admin/content-popup-button.php';
    }

    public function load_assets($hook) {
        wp_enqueue_style(
            'opinionstage-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@400;500;600;700&display=swap',
            null,
            OPINIONSTAGE_WIDGET_VERSION
        );


        if ( in_array( $hook, [
            'toplevel_page_opinionstage-settings',
            'opinion-stage_page_opinionstage-help-resource',
            'toplevel_page_opinionstage-getting-started'
        ], true ) ) {
            wp_enqueue_style(
                Helper::get_asset_name( 'menu-page' ),
                Opinionstage::get_instance()->plugin_url . '/admin/css/menu-page.css',
                null,
                OPINIONSTAGE_WIDGET_VERSION
            );
        }

        if ( $hook === 'toplevel_page_opinionstage-settings' ) {
            $menu_page_js = 'admin/js/menu-page.js';
            wp_enqueue_script(
                Helper::get_asset_name( 'menu-page' ),
                Opinionstage::get_instance()->plugin_url . $menu_page_js,
                [ 'jquery' ],
                OPINIONSTAGE_WIDGET_VERSION,
                false
            );
        }

        if ( in_array( $hook, [
            'post.php',
            'widgets.php',
            'post-new.php',
            'toplevel_page_opinionstage-settings'
        ] ) ) {
            $index_js = 'assets/content-popup/build/index.js';
            wp_enqueue_script(
                Helper::get_asset_name( 'content-popup' ),
                Opinionstage::get_instance()->plugin_url . $index_js,
                [ 'jquery' ],
                OPINIONSTAGE_WIDGET_VERSION,
                false
            );
            add_action( 'admin_footer', [ $this, 'print_content_popup_html' ] );
        }

        if ( 'widgets.php' === $hook ) {
            wp_enqueue_script(
                Helper::get_asset_name( 'widgets-page' ),
                Opinionstage::get_instance()->plugin_url . 'admin/js/widgets-page.js',
                [ 'jquery' ],
                OPINIONSTAGE_WIDGET_VERSION,
                false
            );
        }
    }

    public function print_content_popup_html() {
		TemplatesViewer::require_template('admin/template-parts/content-popup-template.html');
    }

    /**
     * Adds page for post-login redirect and setup in form of invisible menu page.
     */
    public function register_login_callback_page() {
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
    public function login_and_redirect_to_settings_page() {

        if (
            is_user_logged_in()
            && current_user_can( 'edit_posts' )
            && OPINIONSTAGE_LOGIN_CALLBACK_SLUG === filter_input( INPUT_GET, 'page' )
            && SingleUseNonce::is_valid_callback()
		) {
            $uid = isset( $_GET['opinionstage_uid'] ) ? sanitize_text_field( $_GET['opinionstage_uid'] ) : '';
            $token = isset( $_GET['opinionstage_token'] ) ? sanitize_text_field( $_GET['opinionstage_token'] ) : '';
            $email = isset( $_GET['opinionstage_email'] ) ? sanitize_email( $_GET['opinionstage_email'] ) : '';
            $fly_id = isset( $_GET['opinionstage_fly_id'] ) ? intval( $_GET['opinionstage_fly_id'] ) : '';

            delete_option(OPINIONSTAGE_OPTIONS_KEY);
            self::opinionstage_validate_and_save_client_data(
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
    private static function opinionstage_validate_and_save_client_data($raw) {

        $os_options = array(
            'uid' => $raw['uid'],
            'email' => $raw['email'],
            'fly_id' => $raw['fly_id'],
            'version' => OPINIONSTAGE_WIDGET_VERSION,
            'fly_out_active' => 'false',
            'article_placement_active' => 'false',
            'sidebar_placement_active' => 'false',
            'token' => $raw['token'],
        );

        if ( preg_match( '/^[0-9]+$/', $raw['fly_id'] ) ) {
            update_option( OPINIONSTAGE_OPTIONS_KEY, $os_options );
        }
    }


    // adds page for post-logout redirect and setup in form of invisible menu page,
    // and url: http://wp-host.com/wp-admin/admin.php?page=disconnect-page
    public function disconnect_account_menu() {
        if ( function_exists( 'add_menu_page' ) ) {
            add_submenu_page(
                null,
                '',
                '',
                'edit_posts',
                OPINIONSTAGE_DISCONNECT_PAGE
            );
        }
    }

// performs redirect to plugin settings page, after user logout
    public function disconnect_account_action() {
        if ( OPINIONSTAGE_DISCONNECT_PAGE === filter_input( INPUT_GET, 'page' ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            delete_option( OPINIONSTAGE_OPTIONS_KEY );

            $redirect_url = get_admin_url( null, 'admin.php?page=' . OPINIONSTAGE_GETTING_STARTED_SLUG );

            if ( wp_redirect( $redirect_url, 302 ) ) {
                exit;
            }
        }
    }

    public function register_menu_page() {
        if ( function_exists( 'add_menu_page' ) ) {
            $os_client_logged_in = Helper::is_user_logged_in();
            if ( $os_client_logged_in ) {
                add_menu_page(
                    __( 'Opinion Stage', 'social-polls-by-opinionstage' ),
                    __( 'Opinion Stage', 'social-polls-by-opinionstage' ),
                    'edit_posts',
                    OPINIONSTAGE_MENU_SLUG,
                    [ __CLASS__, 'load_template' ],
                    Opinionstage::get_instance()->plugin_url . 'admin/images/os-icon.svg',
                    '25.234323221'
                );
                add_submenu_page( OPINIONSTAGE_MENU_SLUG, 'View My Items', 'My Items', 'edit_posts', OPINIONSTAGE_MENU_SLUG );
                add_submenu_page( OPINIONSTAGE_MENU_SLUG, 'Tutorials & Help', 'Tutorials & Help', 'edit_posts', OPINIONSTAGE_HELP_RESOURCE_SLUG, [ $this, 'load_template' ] );
            } else {
                add_menu_page(
                    __( 'Opinion Stage', 'social-polls-by-opinionstage' ),
                    __( 'Opinion Stage', 'social-polls-by-opinionstage' ),
                    'edit_posts',
                    OPINIONSTAGE_GETTING_STARTED_SLUG,
                    [ __CLASS__, 'load_template' ],
                    Opinionstage::get_instance()->plugin_url . 'admin/images/os-icon.svg',
                    '25.234323221'
                );
                add_submenu_page( OPINIONSTAGE_GETTING_STARTED_SLUG, 'Get Started', 'Get Started', 'edit_posts', OPINIONSTAGE_GETTING_STARTED_SLUG, [ $this, 'load_template' ] );
            }
        }
    }

    public static function load_template() {

        $view_file_name = self::prepare_view_file_name_form_current_page();
        if ( !$view_file_name ) {
            return;
        }

        $os_client_logged_in = Helper::is_user_logged_in();
        $os_options = Helper::get_opinionstage_option();

        TemplatesViewer::require_template( 'admin/views/' . $view_file_name, compact( 'os_client_logged_in', 'os_options' ) );
    }

    private static function prepare_view_file_name_form_current_page() {
        $view_file_name = '';

        if ( !empty( $_REQUEST['page'] ) ) {
            $qry_str_check_os = sanitize_text_field( $_REQUEST['page'] );
            $qry_str_check_os = explode( '-', $qry_str_check_os );
            if ( 'opinionstage' === $qry_str_check_os[0] ) {
                $view_file_name = str_replace( 'opinionstage-', '', sanitize_text_field( $_REQUEST['page'] ) );
                $view_file_name = str_replace( '-', '_', $view_file_name );
            }
        }

        return $view_file_name;
    }
}
