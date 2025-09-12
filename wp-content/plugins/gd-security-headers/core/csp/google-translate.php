<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_google_translate {
	public $basic = array( 'img', 'style', 'script', 'connect' );

	public $img = array(
		'translate.googleapis.com',
		'translate.google.com',
		'www.google.com',
		'www.gstatic.com',
	);

	public $style = array(
		'translate.googleapis.com',
	);

	public $connect = array(
		'translate.googleapis.com',
	);

	public $script = array(
		'translate.googleapis.com',
		'translate.google.com',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_img', array( $this, 'add_img' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_style', array( $this, 'add_style' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_script', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_connect', array( $this, 'add_connect' ) );
	}

	public function basic( $basic, $name ) {
		if ( in_array( $name, $this->basic ) ) {
			$basic = 'self';
		}

		return $basic;
	}

	public function add_img( $custom ) {
		return array_merge( $custom, $this->img );
	}

	public function add_style( $custom ) {
		return array_merge( $custom, $this->style );
	}

	public function add_script( $custom ) {
		return array_merge( $custom, $this->script );
	}

	public function add_connect( $custom ) {
		return array_merge( $custom, $this->connect );
	}
}

new gdsih_csp_extra_google_translate();
