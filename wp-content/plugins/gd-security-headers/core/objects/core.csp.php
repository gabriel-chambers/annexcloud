<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_component_csp {
	public $key = 'gdsih-csp-report';

	public $csp = null;

	public function __construct() {
		$this->csp = new gdsih_core_csp();

		if ( ! D4P_CRON && ! gdsih_settings()->get( 'htaccess' ) ) {
			header( $this->csp->build() );
		}

		if ( gdsih_settings()->get( 'log', 'csp' ) ) {
			add_action( 'template_redirect', array( $this, 'log' ) );
		}

		add_filter( 'gdsih_htaccess_build_list', array( $this, 'htaccess' ) );
	}

	public function htaccess( $htaccess = array() ) {
		$htaccess[] = D4P_TAB . '# add header: content-security-policy';
		$htaccess[] = D4P_TAB . 'Header set ' . $this->csp->build( true );

		return $htaccess;
	}

	public function log() {
		if ( isset( $_GET[ $this->key ] ) ) {
			$raw = file_get_contents( 'php://input' );

			if ( $csp = json_decode( $raw, true ) ) {
				if ( isset( $csp['csp-report'] ) ) {
					$this->event( $csp['csp-report'] );
				}
			}

			http_response_code( 204 );
			exit;
		}
	}

	private function event( $csp ) {
		$report = array_map( 'd4p_sanitize_basic', $csp );

		gdsih_db()->csp_report( array(
			'document_uri'        => $report['document-uri'],
			'blocked_uri'         => $report['blocked-uri'],
			'referrer'            => $report['referrer'],
			'violated_directive'  => $report['violated-directive'],
			'effective_directive' => $report['effective-directive'] ?? '',
			'original_policy'     => gdsih_settings()->get( 'log_original_policy', 'csp' ) ? $report['original-policy'] : '',
		) );
	}
}
