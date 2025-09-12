<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_vimeo {
	public $basic = array( 'default', 'child', 'frame', 'script', 'style', 'connect' );

	public $default = array(
		'*.vimeo.com',
	);

	public $child = array(
		'*.vimeo.com',
		'*.vimeocdn.com',
	);

	public $script = array(
		'*.vimeo.com',
		'*.vimeocdn.com',
		'*.newrelic.com',
		'*.nr-data.net',
	);

	public $style = array(
		'*.vimeocdn.com',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_default', array( $this, 'add_default' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_connect', array( $this, 'add_default' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_child', array( $this, 'add_child' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_frame', array( $this, 'add_child' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_script', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_style', array( $this, 'add_style' ) );
	}

	public function basic( $basic, $name ) {
		if ( in_array( $name, $this->basic ) ) {
			$basic = 'self';
		}

		return $basic;
	}

	public function add_default( $custom ) {
		return array_merge( $custom, $this->default );
	}

	public function add_child( $custom ) {
		return array_merge( $custom, $this->child );
	}

	public function add_script( $custom ) {
		return array_merge( $custom, $this->script );
	}

	public function add_style( $custom ) {
		return array_merge( $custom, $this->style );
	}
}

new gdsih_csp_extra_vimeo();
