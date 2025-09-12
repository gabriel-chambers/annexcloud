<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_csp_extra_google_adsense {
	public $basic = array( 'img', 'frame', 'script', 'connect' );

	public $img = array(
		'*.googlesyndication.com',
		'stats.g.doubleclick.net',
	);

	public $frame = array(
		'*.googlesyndication.com',
		'googleads.g.doubleclick.net',
	);

	public $connect = array(
		'*.googlesyndication.com',
		'googleads.g.doubleclick.net',
		'stats.g.doubleclick.net',
	);

	public $script = array(
		'www.googletagservices.com',
		'*.googlesyndication.com',
		'*.googleadservices.com',
		'googleads.g.doubleclick.net',
		'adservice.google.com',
		'adservice.google.ae',
		'adservice.google.al',
		'adservice.google.at',
		'adservice.google.be',
		'adservice.google.bg',
		'adservice.google.bs',
		'adservice.google.ca',
		'adservice.google.ch',
		'adservice.google.ci',
		'adservice.google.cl',
		'adservice.google.co.bw',
		'adservice.google.co.cr',
		'adservice.google.co.id',
		'adservice.google.co.il',
		'adservice.google.co.in',
		'adservice.google.co.jp',
		'adservice.google.co.ke',
		'adservice.google.co.kr',
		'adservice.google.co.mz',
		'adservice.google.co.nz',
		'adservice.google.co.th',
		'adservice.google.co.tz',
		'adservice.google.co.uk',
		'adservice.google.co.uz',
		'adservice.google.co.ve',
		'adservice.google.co.za',
		'adservice.google.co.zm',
		'adservice.google.co.zw',
		'adservice.google.com.ai',
		'adservice.google.com.ar',
		'adservice.google.com.au',
		'adservice.google.com.bd',
		'adservice.google.com.bh',
		'adservice.google.com.bn',
		'adservice.google.com.bo',
		'adservice.google.com.br',
		'adservice.google.com.co',
		'adservice.google.com.cy',
		'adservice.google.com.ec',
		'adservice.google.com.eg',
		'adservice.google.com.et',
		'adservice.google.com.fj',
		'adservice.google.com.gh',
		'adservice.google.com.gi',
		'adservice.google.com.gt',
		'adservice.google.com.hk',
		'adservice.google.com.jm',
		'adservice.google.com.kh',
		'adservice.google.com.kw',
		'adservice.google.com.lb',
		'adservice.google.com.mm',
		'adservice.google.com.mt',
		'adservice.google.com.mx',
		'adservice.google.com.my',
		'adservice.google.com.ng',
		'adservice.google.com.ni',
		'adservice.google.com.np',
		'adservice.google.com.om',
		'adservice.google.com.pa',
		'adservice.google.com.pe',
		'adservice.google.com.ph',
		'adservice.google.com.pk',
		'adservice.google.com.pr',
		'adservice.google.com.py',
		'adservice.google.com.qa',
		'adservice.google.com.sa',
		'adservice.google.com.sg',
		'adservice.google.com.sv',
		'adservice.google.com.tr',
		'adservice.google.com.tw',
		'adservice.google.com.ua',
		'adservice.google.com.uy',
		'adservice.google.com.vn',
		'adservice.google.cz',
		'adservice.google.de',
		'adservice.google.dk',
		'adservice.google.dz',
		'adservice.google.ee',
		'adservice.google.es',
		'adservice.google.fi',
		'adservice.google.fr',
		'adservice.google.ge',
		'adservice.google.gr',
		'adservice.google.gy',
		'adservice.google.hn',
		'adservice.google.hr',
		'adservice.google.hu',
		'adservice.google.ie',
		'adservice.google.im',
		'adservice.google.iq',
		'adservice.google.is',
		'adservice.google.it',
		'adservice.google.jo',
		'adservice.google.kz',
		'adservice.google.li',
		'adservice.google.lk',
		'adservice.google.lt',
		'adservice.google.lu',
		'adservice.google.lv',
		'adservice.google.md',
		'adservice.google.mk',
		'adservice.google.mu',
		'adservice.google.nl',
		'adservice.google.no',
		'adservice.google.pl',
		'adservice.google.pt',
		'adservice.google.ro',
		'adservice.google.rs',
		'adservice.google.ru',
		'adservice.google.se',
		'adservice.google.si',
		'adservice.google.sk',
		'adservice.google.so',
		'adservice.google.sr',
		'adservice.google.tl',
		'adservice.google.tn',
		'adservice.google.tt',
	);

	public function __construct() {
		add_filter( 'gdsih_csp_build_basic_rule', array( $this, 'basic' ), 10, 2 );

		add_filter( 'gdsih_csp_build_custom_rules_for_img', array( $this, 'add_img' ) );
		add_filter( 'gdsih_csp_build_custom_rules_for_frame', array( $this, 'add_frame' ) );
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

	public function add_frame( $custom ) {
		return array_merge( $custom, $this->frame );
	}

	public function add_script( $custom ) {
		return array_merge( $custom, $this->script );
	}

	public function add_connect( $custom ) {
		return array_merge( $custom, $this->connect );
	}
}

new gdsih_csp_extra_google_adsense();
