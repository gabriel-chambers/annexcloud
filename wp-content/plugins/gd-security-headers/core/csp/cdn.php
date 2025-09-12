<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_auto_cdn {
	public $basic = array( 'style', 'font', 'script', 'img', 'media' );

	public $cdns = array();

	public function __construct() {
		$this->cdns = gdsih_settings()->get( 'cdn', 'csp' );

		if ( ! empty( $this->cdns ) ) {
			add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

			foreach ( $this->basic as $rule ) {
				add_filter( 'gdsih_csp_build_custom_rules_for_' . $rule, array( $this, 'add_cdn' ) );
			}
		}
	}

	public function basic( $basic, $name ) {
		if ( in_array( $name, $this->basic ) ) {
			$basic = 'self';
		}

		return $basic;
	}

	public function add_cdn( $custom ) {
		return array_merge( $custom, $this->cdns );
	}
}

new gdsih_csp_auto_cdn();
