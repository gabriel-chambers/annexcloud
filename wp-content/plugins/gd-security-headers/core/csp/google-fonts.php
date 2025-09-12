<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_google_fonts {
	public $basic = array( 'font', 'style', 'script' );

	public $style = array(
		'fonts.googleapis.com',
	);

	public $font = array(
		'fonts.gstatic.com',
		'fonts.googleapis.com',
	);

	public $script = array(
		'ajax.googleapis.com',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_style', array( $this, 'add_style' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_script', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_font', array( $this, 'add_font' ) );
	}

	public function basic( $basic, $name ) {
		if ( in_array( $name, $this->basic ) ) {
			$basic = 'self';
		}

		return $basic;
	}

	public function add_font( $custom ) {
		return array_merge( $custom, $this->font );
	}

	public function add_style( $custom ) {
		return array_merge( $custom, $this->style );
	}

	public function add_script( $custom ) {
		return array_merge( $custom, $this->script );
	}
}

new gdsih_csp_extra_google_fonts();
