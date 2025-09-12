<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_google_analytics {
	public $basic = array( 'img', 'script', 'connect' );

	public $scripts = array(
		'google-analytics.com',
		'www.google-analytics.com',
		'ssl.google-analytics.com',
		'stats.g.doubleclick.net',
	);

	public $images = array(
		'google-analytics.com',
		'www.google-analytics.com',
		'ssl.google-analytics.com',
		'www.google.com',
	);

	public $connect = array(
		'www.google-analytics.com',
		'stats.g.doubleclick.net',
		'ampcid.google.com',
		'analytics.google.com',
		'about:',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_script', array( $this, 'add_scripts' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_connect', array( $this, 'add_connect' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_font', array( $this, 'add_font' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_img', array( $this, 'add_images' ) );
	}

	public function basic( $basic, $name ) {
		if ( in_array( $name, $this->basic ) ) {
			$basic = 'self';
		}

		return $basic;
	}

	public function add_scripts( $custom ) {
		return array_merge( $custom, $this->scripts );
	}

	public function add_font( $custom ) {
		$custom[] = 'data:';

		return $custom;
	}

	public function add_images( $custom ) {
		$custom[] = 'data:';
		$custom[] = 'blob:';

		return array_merge( $custom, $this->images );
	}

	public function add_connect( $custom ) {
		return array_merge( $custom, $this->connect );
	}
}

new gdsih_csp_extra_google_analytics();
