<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_google_youtube {
	public $basic = array( 'child', 'frame', 'img' );

	public $img = array(
		'i.ytimg.com',
	);

	public $child = array(
		'www.youtube.com',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_img', array( $this, 'add_img' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_child', array( $this, 'add_child' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_frame', array( $this, 'add_child' ) );
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

	public function add_child( $custom ) {
		return array_merge( $custom, $this->child );
	}
}

new gdsih_csp_extra_google_youtube();
