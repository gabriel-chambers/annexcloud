<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_admin_getback {
	public function __construct() {
		if ( gdsih_admin()->page === 'tools' ) {
			if ( isset( $_GET['run'] ) && $_GET['run'] == 'export' ) {
				$this->tools_export();
			}
		}

		if ( gdsih_admin()->page === 'csp-reports' || gdsih_admin()->page === 'xxp-reports' ) {
			if ( isset( $_GET['single-action'] ) && $_GET['single-action'] == 'delete' ) {
				if ( gdsih_admin()->page === 'csp-reports' ) {
					$this->csp_delete();
				} else if ( gdsih_admin()->page === 'xxp-reports' ) {
					$this->xxp_delete();
				}
			}

			if (
				( isset( $_GET['action'] ) && $_GET['action'] != '-1' ) ||
				( isset( $_GET['action2'] ) && $_GET['action2'] != '-1' ) ) {
				if ( gdsih_admin()->page === 'csp-reports' ) {
					$this->csp_bulk();
				} else if ( gdsih_admin()->page === 'xxp-reports' ) {
					$this->xxp_bulk();
				}
			}
		}

		do_action( 'gdsih_admin_getback_handler' );
	}

	private function _load_maintenance() {
		require_once( GDSIH_PATH . 'core/admin/maintenance.php' );
	}

	private function _bulk_action() {
		$action = isset( $_GET['action'] ) && $_GET['action'] != '' && $_GET['action'] != '-1' ? $_GET['action'] : '';

		if ( $action == '' ) {
			$action = isset( $_GET['action2'] ) && $_GET['action2'] != '' && $_GET['action2'] != '-1' ? $_GET['action2'] : '';
		}

		return $action;
	}

	private function tools_export() {
		check_ajax_referer( 'dev4press-plugin-export' );

		if ( ! d4p_is_current_user_admin() ) {
			wp_die( __( 'Only administrators can use export features.', 'gd-security-headers' ) );
		}

		$export_date = date( 'Y-m-d-H-m-s' );

		header( 'Content-type: application/json' );
		header( 'Content-Disposition: attachment; filename="gd_security_headers_settings_' . $export_date . '.json"' );

		die( gdsih_settings()->export_to_json() );
	}

	private function csp_delete() {
		check_ajax_referer( 'gdsih-admin-panel' );

		$log_id = isset( $_GET['csps'] ) ? absint( $_GET['csps'] ) : 0;

		$url = gdsih_admin()->current_url();

		if ( $log_id > 0 ) {
			$this->_load_maintenance();

			gdsih_admin_maintenance::delete_csp_report( $log_id );

			$url .= '&message=removed';
		}

		wp_redirect( $url );
		exit;
	}

	private function xxp_delete() {
		check_ajax_referer( 'gdsih-admin-panel' );

		$log_id = isset( $_GET['xxps'] ) ? absint( $_GET['xxps'] ) : 0;

		$url = gdsih_admin()->current_url();

		if ( $log_id > 0 ) {
			$this->_load_maintenance();

			gdsih_admin_maintenance::delete_xxp_report( $log_id );

			$url .= '&message=removed';
		}

		wp_redirect( $url );
		exit;
	}

	private function csp_bulk() {
		check_admin_referer( 'bulk-csps' );

		$action = $this->_bulk_action();

		if ( $action != '' ) {
			$items = isset( $_GET['csp'] ) ? (array) $_GET['csp'] : array();

			$url = gdsih_admin()->current_url();

			if ( ! empty( $items ) ) {
				$this->_load_maintenance();

				switch ( $action ) {
					case 'delete':
						gdsih_admin_maintenance::delete_csp_report( $items );
						break;
				}

				$url .= '&message=removed';
			}

			wp_redirect( $url );
			exit;
		}
	}

	private function xxp_bulk() {
		check_admin_referer( 'bulk-xxps' );

		$action = $this->_bulk_action();

		if ( $action != '' ) {
			$items = isset( $_GET['xxp'] ) ? (array) $_GET['xxp'] : array();

			$url = gdsih_admin()->current_url();

			if ( ! empty( $items ) ) {
				$this->_load_maintenance();

				switch ( $action ) {
					case 'delete':
						gdsih_admin_maintenance::delete_xxp_report( $items );
						break;
				}

				$url .= '&message=removed';
			}

			wp_redirect( $url );
			exit;
		}
	}
}
