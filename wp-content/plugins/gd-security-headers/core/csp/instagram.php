<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_instagram {
	public $basic = array( 'script', 'frame' );

	public $script = array(
		'platform.instagram.com',
		'www.instagram.com',
	);

	public $child = array(
		'www.instagram.com',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_script', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_frame', array( $this, 'add_child' ) );
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
}

new gdsih_csp_extra_instagram();
