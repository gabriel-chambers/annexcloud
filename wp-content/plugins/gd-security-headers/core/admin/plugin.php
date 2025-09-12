<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_admin_core extends d4p_admin_core {
	public $plugin = 'gd-security-headers';

	function __construct() {
		parent::__construct();

		$this->url = GDSIH_URL;

		add_action( 'gdsih_plugin_init', array( $this, 'core' ) );
	}

	public function core() {
		parent::core();

		add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );

		add_filter( 'set-screen-option', array( $this, 'screen_options_grid_rows_save' ), 10, 3 );

		$this->init_ready();

		if ( gdsih_scope()->is_master_network_admin() ) {
			if ( gdsih_settings()->is_install() ) {
				add_action( 'admin_notices', array( $this, 'install_notice' ) );
			}

			if ( gdsih_settings()->is_update() ) {
				add_action( 'admin_notices', array( $this, 'update_notice' ) );
			}
		}
	}

	public function screen_options_grid_rows_save( $status, $option, $value ) {
		if (
			in_array( $option, array(
				'gdsih_rows_per_page_csp_reports',
				'gdsih_rows_per_page_xxp_reports',
			) ) ) {
			return $value;
		}

		return $status;
	}

	public function screen_options_grid_rows_csp_reports() {
		$key = 'gdsih_rows_per_page_csp_reports';

		$args = array(
			'label'   => __( 'Rows', 'gd-security-headers' ),
			'default' => 25,
			'option'  => $key,
		);

		add_screen_option( 'per_page', $args );

		require_once( GDSIH_PATH . 'core/grids/csp.php' );

		new gdsih_csp_report_grid();
	}

	public function screen_options_grid_rows_xxp_reports() {
		$key = 'gdsih_rows_per_page_xxp_reports';

		$args = array(
			'label'   => __( 'Rows', 'gd-security-headers' ),
			'default' => 25,
			'option'  => $key,
		);

		add_screen_option( 'per_page', $args );

		require_once( GDSIH_PATH . 'core/grids/xxp.php' );

		new gdsih_xxp_report_grid();
	}

	public function install_notice() {
		if ( current_user_can( 'install_plugins' ) && $this->page === false ) {
			echo '<div class="updated"><p>';
			echo __( 'GD Security Headers is activated and it needs to finish installation.', 'gd-security-headers' );
			echo ' <a href="' . network_admin_url( 'admin.php?page=gd-security-headers-front' ) . '">' . __( 'Click Here', 'gd-security-headers' ) . '</a>.';
			echo '</p></div>';
		}
	}

	public function update_notice() {
		if ( current_user_can( 'install_plugins' ) && $this->page === false ) {
			echo '<div class="updated"><p>';
			echo __( 'GD Security Headers is updated and it needs to finish the update process.', 'gd-security-headers' );
			echo ' <a href="' . network_admin_url( 'admin.php?page=gd-security-headers-front' ) . '">' . __( 'Click Here', 'gd-security-headers' ) . '</a>.';
			echo '</p></div>';
		}
	}

	public function init_ready() {
		$this->menu_items = array(
			'front'       => array( 'title' => __( 'Overview', 'gd-security-headers' ), 'icon' => 'home' ),
			'about'       => array( 'title' => __( 'About', 'gd-security-headers' ), 'icon' => 'info-circle' ),
			'csp-reports' => array( 'title' => __( 'CSP Reports', 'gd-security-headers' ), 'icon' => 'info-circle' ),
			'xxp-reports' => array( 'title' => __( 'XXP Reports', 'gd-security-headers' ), 'icon' => 'info-circle' ),
			'headers'     => array( 'title' => __( 'Headers', 'gd-security-headers' ), 'icon' => 'code' ),
			'settings'    => array( 'title' => __( 'Settings', 'gd-security-headers' ), 'icon' => 'cogs' ),
			'tools'       => array( 'title' => __( 'Tools', 'gd-security-headers' ), 'icon' => 'wrench' ),
		);
	}

	public function admin_init() {
		d4p_include( 'grid', 'admin', GDSIH_D4PLIB );

		do_action( 'gdsih_admin_init' );
	}

	public function title() {
		return 'GD Security Headers';
	}

	public function admin_menu() {
		if ( is_multisite() && is_blog_admin() ) {
			return;
		}

		$parent = 'gd-security-headers-front';

		$this->page_ids[] = add_menu_page(
			'GD Security Headers',
			'Security Headers',
			gdsih()->cap,
			$parent,
			array( $this, 'panel_general' ),
			gdsih()->svg_icon );

		foreach ( $this->menu_items as $item => $data ) {
			$this->page_ids[] = add_submenu_page( $parent,
				'GD Security Headers: ' . $data['title'],
				$data['title'],
				gdsih()->cap,
				'gd-security-headers-' . $item,
				array( $this, 'panel_general' ) );
		}

		$this->admin_load_hooks();
	}

	public function enqueue_scripts( $hook ) {
		$load_admin_data = false;

		if ( $this->page !== false ) {
			d4p_admin_enqueue_defaults();

			wp_enqueue_script( 'jquery-form' );

			wp_enqueue_style( 'fontawesome', GDSIH_URL . 'd4plib/resources/fontawesome/css/font-awesome.min.css' );

			wp_enqueue_style( 'd4plib-font', $this->file( 'css', 'font', true ), array(), D4P_VERSION );
			wp_enqueue_style( 'd4plib-shared', $this->file( 'css', 'shared', true ), array(), D4P_VERSION );
			wp_enqueue_style( 'd4plib-admin', $this->file( 'css', 'admin', true ), array( 'd4plib-shared' ), D4P_VERSION );

			wp_enqueue_script( 'd4plib-shared', $this->file( 'js', 'shared', true ), array( 'jquery', 'wp-color-picker' ), D4P_VERSION, true );
			wp_enqueue_script( 'd4plib-admin', $this->file( 'js', 'admin', true ), array( 'd4plib-shared' ), D4P_VERSION, true );

			wp_enqueue_style( 'gdsih-plugin', $this->file( 'css', 'plugin' ), array( 'd4plib-admin', 'wp-jquery-ui-dialog' ), gdsih_settings()->file_version() );
			wp_enqueue_script( 'gdsih-plugin', $this->file( 'js', 'plugin' ), array( 'd4plib-admin', 'wpdialogs' ), gdsih_settings()->file_version(), true );

			if ( $this->page == 'about' ) {
				wp_enqueue_style( 'd4plib-grid', $this->file( 'css', 'grid', true ), array(), D4P_VERSION . '.' . D4P_BUILD );
			}

			$_data = array(
				'nonce'                     => wp_create_nonce( 'gdsih-admin-internal' ),
				'wp_version'                => GDSIH_WPV,
				'page'                      => $this->page,
				'panel'                     => $this->panel,
				'button_icon_ok'            => '<i class="fa fa-check fa-fw" aria-hidden="true"></i> ',
				'button_icon_cancel'        => '<i class="fa fa-times fa-fw" aria-hidden="true"></i> ',
				'button_icon_delete'        => '<i class="fa fa-trash fa-fw" aria-hidden="true"></i> ',
				'dialog_button_ok'          => __( 'OK', 'gd-security-headers' ),
				'dialog_button_cancel'      => __( 'Cancel', 'gd-security-headers' ),
				'dialog_button_delete'      => __( 'Delete', 'gd-security-headers' ),
				'dialog_button_remove'      => __( 'Remove', 'gd-security-headers' ),
				'dialog_button_clear'       => __( 'Clear', 'gd-security-headers' ),
				'dialog_title_areyousure'   => __( 'Are you sure you want to do this?', 'gd-security-headers' ),
				'dialog_content_pleasewait' => __( 'Please Wait...', 'gd-security-headers' ),
			);

			wp_localize_script( 'gdsih-plugin', 'gdsih_data', $_data );

			$load_admin_data = true;
		}

		if ( $load_admin_data ) {
			wp_localize_script( 'd4plib-shared', 'd4plib_admin_data', array(
				'string_media_image_title'  => __( 'Select Image', 'gd-security-headers' ),
				'string_media_image_button' => __( 'Use Selected Image', 'gd-security-headers' ),
				'string_are_you_sure'       => __( 'Are you sure you want to do this?', 'gd-security-headers' ),
				'string_image_not_selected' => __( 'Image not selected.', 'gd-security-headers' ),
			) );
		}
	}

	public function admin_load_hooks() {
		foreach ( $this->page_ids as $id ) {
			add_action( 'load-' . $id, array( $this, 'load_admin_page' ) );
		}

		add_action( 'load-security-headers_page_gd-security-headers-csp-reports', array( $this, 'screen_options_grid_rows_csp_reports' ) );
		add_action( 'load-security-headers_page_gd-security-headers-xxp-reports', array( $this, 'screen_options_grid_rows_xxp_reports' ) );
	}

	public function current_screen( $screen ) {
		if ( isset( $_GET['panel'] ) && $_GET['panel'] != '' ) {
			$this->panel = d4p_sanitize_slug( $_GET['panel'] );
		}

		$id = $screen->id;

		if ( gdsih_scope()->is_network_admin() ) {
			if ( $id == 'toplevel_page_gd-security-headers-front-network' ) {
				$this->page = 'front';
			} else if ( substr( $id, 0, 42 ) == 'security-headers_page_gd-security-headers-' ) {
				$this->page = substr( $id, 42, strlen( $id ) - 50 );
			}
		} else {
			if ( $id == 'toplevel_page_gd-security-headers-front' ) {
				$this->page = 'front';
			} else if ( substr( $id, 0, 42 ) == 'security-headers_page_gd-security-headers-' ) {
				$this->page = substr( $id, 42 );
			}
		}

		if ( is_super_admin() ) {
			if ( isset( $_POST['gdsih_handler'] ) && sanitize_key( $_POST['gdsih_handler'] ) === 'postback' ) {
				require_once( GDSIH_PATH . 'core/admin/postback.php' );

				new gdsih_admin_postback();
			} else if ( isset( $_GET['gdsih_handler'] ) && sanitize_key( $_GET['gdsih_handler'] ) === 'getback' ) {
				require_once( GDSIH_PATH . 'core/admin/getback.php' );

				new gdsih_admin_getback();
			}
		}
	}

	public function help_tab_getting_help() {
		if ( $this->panel == 'csp' ) {
			get_current_screen()->add_help_tab(
				array(
					'id'      => 'gdsec-help-settings-csp-header',
					'title'   => __( 'CSP Header', 'gd-security-headers' ),
					'content' => $this->help_csp_header(),
				)
			);

			get_current_screen()->add_help_tab(
				array(
					'id'      => 'gdsih-help-settings-csp-res',
					'title'   => __( 'CSP Resources', 'gd-security-headers' ),
					'content' => $this->help_csp_resources(),
				)
			);
		}

		get_current_screen()->add_help_tab(
			array(
				'id'      => 'd4p-help-info',
				'title'   => __( 'Getting Help', 'gd-security-headers' ),
				'content' => '<p>' . __( 'To get help with this plugin, you can start with Knowledge Base list of frequently asked questions and articles. If you have any questions, or you want to report a bug, or you have a suggestion, you can use support forum. All important links for this are on the right side of this help dialog.', 'gd-security-headers' ) . '</p>',
			)
		);
	}

	public function help_csp_header() {
		$render = '<p>' . __( 'There are few more things you need to think about when setting up this rating addon.', 'gd-security-headers' ) . '</p>';
		$render .= '<ul>';
		$render .= '<li>' . __( 'Do not switch to the Live policy mode before you make all the tests with the Report policy mode.', 'gd-security-headers' ) . '</li>';
		$render .= '<li>' . __( 'During the testing phase, it is best to disable Log option, or you will end up with a lot of reports logged. Use Log feature when you switch to Live policy mode.', 'gd-security-headers' ) . '</li>';
		$render .= '<li>' . __( 'To test CSP, use Google Chrome or Mozilla Firefox with Firebug. But will display detailed information in the Console about each CSP issue.', 'gd-security-headers' ) . '</li>';
		$render .= '</ul>';
		$render .= '<p>' . __( 'To make sure you add valid sources to all source rules, here are few examples on what is accepted by the browsers. You can use \'*\' character as wildcard.', 'gd-security-headers' ) . '</p>';
		$render .= '<ul>';
		$render .= '<li><strong>https:</strong> - ' . __( 'Matches any url over HTTPS scheme.', 'gd-security-headers' ) . '</li>';
		$render .= '<li><strong>example.com</strong> - ' . __( 'Matches both HTTP and HTTPS version of the URL.', 'gd-security-headers' ) . '</li>';
		$render .= '<li><strong>https://*.example.com</strong> - ' . __( 'Matches HTTPS subdomains for the URL, but now the main domain.', 'gd-security-headers' ) . '</li>';
		$render .= '<li><strong>www.example.com:443</strong> - ' . __( 'Matches exact domain URL, with the specified port.', 'gd-security-headers' ) . '</li>';
		$render .= '<li><strong>*://*.example.com:*</strong> - ' . __( 'Matches any scheme for subdomain and any port, but not the main domain.', 'gd-security-headers' ) . '</li>';
		$render .= '<li><strong>www.example.com</strong> - ' . __( 'Matches exact domain URL, no other subdomains.', 'gd-security-headers' ) . '</li>';
		$render .= '</ul>';

		return $render;
	}

	public function help_csp_resources() {
		$render = '<p>' . __( 'To get more information about the Content Security Policy, check out these online resources.', 'gd-security-headers' ) . '</p>';
		$render .= '<ul>';
		$render .= '<li><a href="https://content-security-policy.com/" target="_blank">Content Security Policy (CSP) Quick Reference Guide</a></li>';
		$render .= '<li><a href="https://www.w3.org/TR/CSP/" target="_blank">W3C - Content Security Policy</a></li>';
		$render .= '</ul>';

		return $render;
	}

	public function load_admin_page() {
		$this->help_tab_sidebar();

		do_action( 'gdsih_load_admin_page_' . $this->page );

		if ( $this->panel !== false && $this->panel != '' ) {
			do_action( 'gdsih_load_admin_page_' . $this->page . '_' . $this->panel );
		}

		$this->help_tab_getting_help();
	}

	public function install_or_update() {
		$install = gdsih_settings()->is_install();
		$update  = gdsih_settings()->is_update();

		if ( $install ) {
			include( GDSIH_PATH . 'forms/install.php' );
		} else if ( $update ) {
			include( GDSIH_PATH . 'forms/update.php' );
		}

		return $install || $update;
	}

	public function panel_general() {
		if ( ! $this->install_or_update() ) {
			$path = GDSIH_PATH . 'forms/' . $this->page . '.php';

			$path = apply_filters( 'gdsih_admin_panel_' . $this->page, $path );

			include( $path );
		}
	}

	public function current_url( $with_panel = true ) {
		$page = 'admin.php?page=gd-security-headers-';

		$page .= $this->page;

		if ( $with_panel && $this->panel !== false && $this->panel != '' ) {
			$page .= '&panel=' . $this->panel;
		}

		return self_admin_url( $page );
	}
}

global $_gdsih_core_admin;
$_gdsih_core_admin = new gdsih_admin_core();

function gdsih_admin() {
	global $_gdsih_core_admin;

	return $_gdsih_core_admin;
}
