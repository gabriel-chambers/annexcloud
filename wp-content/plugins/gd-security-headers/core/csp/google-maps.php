<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_google_maps {
	public $basic = array( 'style', 'script', 'img', 'frame', 'font', 'connect' );

	public $script = array(
		'maps.googleapis.com',
		'maps.google.com',
	);

	public $child = array(
		'ajax.googleapis.com',
		'maps.google.com',
		'maps.gstatic.com',
		'maps.googleapis.com',
	);

	public $img = array(
		'*.googleapis.com',
		'maps.google.com',
		'maps.gstatic.com',
		'www.gstatic.com',
		'*.ggpht.com',
		'data:',
		'blob:',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_style', array( $this, 'add_style' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_script', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_frame', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_connect', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_font', array( $this, 'add_font' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_img', array( $this, 'add_img' ) );
	}

	public function basic( $basic, $name ) {
		if ( in_array( $name, $this->basic ) ) {
			$basic = 'self';
		}

		return $basic;
	}

	public function add_style( $custom ) {
		$custom[] = "'unsafe-inline'";

		return array_merge( $custom, $this->script );
	}

	public function add_script( $custom ) {
		return array_merge( $custom, $this->script );
	}

	public function add_img( $custom ) {
		return array_merge( $custom, $this->img );
	}

	public function add_font( $custom ) {
		$custom[] = 'data:';

		return $custom;
	}
}

new gdsih_csp_extra_google_maps();
