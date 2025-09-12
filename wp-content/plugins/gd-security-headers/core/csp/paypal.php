<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_paypal {
	public $basic = array( 'script', 'frame', 'child', 'img', 'font', 'connect' );

	public $script = array(
		'www.paypal.com',
		'www.paypalobjects.com',
	);

	public $child = array(
		'www.paypalobjects.com',
		'*.paypal.com',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_script', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_frame', array( $this, 'add_child' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_child', array( $this, 'add_child' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_connect', array( $this, 'add_child' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_font', array( $this, 'add_font' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_img', array( $this, 'add_img' ) );
	}

	public function basic( $basic, $name ) {
		if ( in_array( $name, $this->basic ) ) {
			$basic = 'self';
		}

		return $basic;
	}

	public function add_script( $custom ) {
		return array_merge( $custom, $this->script );
	}

	public function add_child( $custom ) {
		return array_merge( $custom, $this->child );
	}

	public function add_font( $custom ) {
		$custom[] = 'data:';

		return $custom;
	}

	public function add_img( $custom ) {
		$custom[] = 'data:';

		return array_merge( $custom, $this->child );
	}
}

new gdsih_csp_extra_paypal();
