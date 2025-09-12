<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_component_xxp {
	public $key = 'gdsih-xxp-report';

	public function __construct() {
		if ( ! D4P_CRON && ! gdsih_settings()->get( 'htaccess' ) ) {
			header( $this->build() );
		}

		if ( gdsih_settings()->get( 'log', 'xxp' ) ) {
			add_action( 'template_redirect', array( $this, 'log' ) );
		}

		add_filter( 'gdsih_htaccess_build_list', array( $this, 'htaccess' ) );
	}

	public function log() {
		if ( isset( $_GET[ $this->key ] ) ) {
			$raw = file_get_contents( 'php://input' );

			if ( $csp = json_decode( $raw, true ) ) {
				if ( isset( $csp['xss-report'] ) ) {
					$this->event( $csp['xss-report'] );
				}
			}

			http_response_code( 204 );
			exit;
		}
	}

	public function url() : string {
		$base_url = gdsih_settings()->get( 'log_force_ssl', 'xxp' ) ? network_home_url( '', 'https' ) : network_home_url();

		return $base_url . '?' . $this->key;
	}

	public function htaccess( $htaccess = array() ) {
		$htaccess[] = D4P_TAB . '# add header: x-xss-protection';
		$htaccess[] = D4P_TAB . 'Header set ' . $this->build( true );

		return $htaccess;
	}

	public function build( $htaccess = false ) : string {
		$items = array(
			'1',
			'mode=block',
		);

		if ( gdsih_settings()->get( 'log', 'xxp' ) ) {
			$items[] = 'report=' . $this->url();
		}

		if ( $htaccess ) {
			return 'X-XSS-Protection "' . join( '; ', $items ) . ';"';
		} else {
			return 'X-XSS-Protection: ' . join( '; ', $items ) . ';';
		}
	}

	private function event( $csp ) {
		$report = array_map( 'd4p_sanitize_basic', $csp );

		gdsih_db()->xxp_report( array(
			'request_url'  => $report['request-url'],
			'request_body' => $report['request-body'],
		) );
	}
}
