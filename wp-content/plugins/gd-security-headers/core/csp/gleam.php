<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_gleam {
	public $basic = array( 'frame', 'script', 'img' );

	public $img = array(
		'*.gleam.io',
	);

	public $frame = array(
		'gleam.io',
	);

	public $script = array(
		'widget.gleamjs.io',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_frame', array( $this, 'add_frame' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_script', array( $this, 'add_script' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_img', array( $this, 'add_img' ) );
	}

	public function basic( $basic, $name ) {
		if ( in_array( $name, $this->basic ) ) {
			$basic = 'self';
		}

		return $basic;
	}

	public function add_frame( $custom ) {
		return array_merge( $custom, $this->frame );
	}

	public function add_img( $custom ) {
		return array_merge( $custom, $this->img );
	}

	public function add_script( $custom ) {
		return array_merge( $custom, $this->script );
	}
}

new gdsih_csp_extra_gleam();
