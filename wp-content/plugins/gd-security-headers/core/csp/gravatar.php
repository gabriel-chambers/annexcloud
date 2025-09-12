<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_gravatar {
	public $basic = array( 'img' );

	public $img = array(
		'secure.gravatar.com',
		'www.gravatar.com',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_img', array( $this, 'add_img' ) );
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
}

new gdsih_csp_extra_gravatar();
