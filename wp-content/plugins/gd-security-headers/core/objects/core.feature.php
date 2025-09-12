<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_component_feature_policy {
	public $fep = null;

	public function __construct() {
		$this->fep = new gdsih_core_feature_policy();

		if ( ! D4P_CRON && ! gdsih_settings()->get( 'htaccess' ) ) {
			$headers = $this->fep->build();

			foreach ( $headers as $header ) {
				header( $header );
			}
		}

		add_filter( 'gdsih_htaccess_build_list', array( $this, 'htaccess' ) );
	}

	public function htaccess( $htaccess = array() ) {
		$headers = $this->fep->build( true );

		if ( ! empty( $headers ) ) {
			$htaccess[] = D4P_TAB . '# add header: feature-policy';

			foreach ( $headers as $header ) {
				$htaccess[] = D4P_TAB . 'Header set ' . $header;
			}
		}

		return $htaccess;
	}
}
