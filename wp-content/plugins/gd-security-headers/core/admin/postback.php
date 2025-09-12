<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_admin_postback {
	public $valid = array(
		'd000' => 0,
		'd001' => 1,
		'd003' => 3,
		'd007' => 7,
		'd014' => 14,
		'd030' => 30,
		'd060' => 60,
		'd090' => 90,
		'd180' => 180,
		'd365' => 365,
	);

	public function __construct() {
		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] === 'gd-security-headers-settings' ) {
			$this->settings();
		}

		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] === 'gd-security-headers-tools' ) {
			$this->tools();
		}

		do_action( 'gdsih_admin_postback_handler' );
	}

	private function save_settings( $panel ) {
		d4p_includes( array(
			array( 'name' => 'settings', 'directory' => 'admin' ),
			array( 'name' => 'walkers', 'directory' => 'admin' ),
			array( 'name' => 'functions', 'directory' => 'admin' ),
		), GDSIH_D4PLIB );

		include( GDSIH_PATH . 'core/admin/options.php' );

		$scope = $_REQUEST['gdsih_scope'];

		$options  = new gdsih_admin_settings();
		$settings = $options->settings( $panel );

		$processor       = new d4pSettingsProcess( $settings );
		$processor->base = 'gdsihvalue';

		$data = $processor->process();

		if ( $scope === 'network' ) {
			foreach ( $data as $group => $values ) {
				if ( ! empty( $group ) ) {
					foreach ( $values as $name => $value ) {
						$value = apply_filters( 'gdsih_save_settings_value', $value, $name, $group );

						gdsih_settings()->set( $name, $value, $group );
					}

					gdsih_settings()->save( $group );
				}
			}
		}

		do_action( 'gdsih_save_settings_' . $panel );
		do_action( 'gdsih_saved_the_settings' );
	}

	private function settings() {
		check_admin_referer( 'gd-security-headers-settings-options' );

		$this->save_settings( gdsih_admin()->panel );

		wp_redirect( gdsih_admin()->current_url() . '&message=saved' );
		exit;
	}

	private function tools() {
		check_admin_referer( 'gd-security-headers-tools-options' );

		$data    = $_POST['gdsihtools'];
		$panel   = $data['panel'];
		$message = '';

		if ( $panel == 'import' ) {
			if ( is_uploaded_file( $_FILES['import_file']['tmp_name'] ) ) {
				$raw  = file_get_contents( $_FILES['import_file']['tmp_name'] );
				$data = json_decode( $raw );

				if ( is_object( $data ) ) {
					gdsih_settings()->import_from_object( $data );

					$message = '&message=imported';
				}
			}
		} else if ( $panel == 'reportslog' ) {
			set_time_limit( 0 );

			$tables = array();

			if ( isset( $data['reportslog']['csp'] ) && $data['reportslog']['csp'] == 'on' ) {
				$tables[] = 'csp';
			}

			if ( isset( $data['reportslog']['xxp'] ) && $data['reportslog']['xxp'] == 'on' ) {
				$tables[] = 'xxp';
			}

			$period = $data['reportslog']['period'];

			if ( isset( $this->valid[ $period ] ) && ! empty( $tables ) ) {
				if ( $this->valid[ $period ] == 0 ) {
					gdsih_db()->empty_reports_logs( $tables );
				} else {
					gdsih_db()->cleanup_reports( $this->valid[ $period ], $tables );
				}

				$message = '&message=reportslog';
			}
		} else if ( $panel == 'remove' ) {
			$remove = isset( $data['remove'] ) ? (array) $data['remove'] : array();

			if ( empty( $remove ) ) {
				$message = '&message=nothing-removed';
			} else {
				if ( isset( $remove['settings'] ) && $remove['settings'] == 'on' ) {
					gdsih_settings()->remove_plugin_settings();
				}

				if ( isset( $remove['htaccess'] ) && $remove['htaccess'] == 'on' ) {
					require_once( GDSIH_PATH . 'core/objects/core.htaccess.php' );

					$htaccess = new gdsih_core_htaccess();
					$htaccess->reset();

					gdsih_settings()->set( 'htaccess_added', false, 'core', true );
				}

				if ( isset( $remove['drop'] ) && $remove['drop'] == 'on' ) {
					require_once( GDSIH_PATH . 'core/admin/install.php' );

					gdsih_drop_database_tables();

					if ( ! isset( $remove['disable'] ) ) {
						gdsih_settings()->mark_for_update();
					}
				} else if ( isset( $remove['truncate'] ) && $remove['truncate'] == 'on' ) {
					require_once( GDSIH_PATH . 'core/admin/install.php' );

					gdsih_truncate_database_tables();
				}

				if ( isset( $remove['disable'] ) && $remove['disable'] == 'on' ) {
					deactivate_plugins( 'gd-security-headers/gd-security-headers.php', false, false );

					wp_redirect( admin_url( 'plugins.php' ) );
					exit;
				}

				$message = '&message=removed';
			}
		}

		wp_redirect( gdsih_admin()->current_url() . $message );
		exit;
	}
}
