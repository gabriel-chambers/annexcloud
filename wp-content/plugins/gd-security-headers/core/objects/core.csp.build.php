<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_core_csp {
	public $key = 'gdsih-csp-report';

	public $mode = '';
	public $rules = array(
		'default'         => 'default-src',
		'script'          => 'script-src',
		'style'           => 'style-src',
		'img'             => 'img-src',
		'connect'         => 'connect-src',
		'font'            => 'font-src',
		'object'          => 'object-src',
		'media'           => 'media-src',
		'frame'           => 'frame-src',
		'manifest'        => 'manifest-src',
		'child'           => 'child-src',
		'worker'          => 'worker-src',
		'base-uri'        => 'base-uri',
		'form-action'     => 'form-action',
		'frame-ancestors' => 'frame-ancestors',
		'prefetch'        => 'prefetch-src',
	);

	public function __construct() {
	}

	public function url() : string {
		$_url = trim( gdsih_settings()->get( 'log_url', 'csp' ) );

		if ( ! empty( $_url ) ) {
			return $_url;
		}

		$base_url = gdsih_settings()->get( 'log_force_ssl', 'csp' ) ? network_home_url( '', 'https' ) : network_home_url();

		return $base_url . '?' . $this->key;
	}

	public function rule( $items, $name ) {
		$basic  = gdsih_settings()->get( $name . '_basic', 'csp' );
		$custom = gdsih_settings()->get( $name . '_custom', 'csp' );

		$basic = apply_filters( 'gdsih_csp_build_basic_rule', $basic, $name );
		$basic = apply_filters( 'gdsih_csp_build_basic_rule_for_' . $name, $basic );

		if ( $basic != 'no' ) {
			$basic_value = $basic == 'all' ? '*' : "'" . $basic . "'";
			$item        = $this->rules[ $name ] . ' ' . $basic_value . ' ';

			if ( gdsih_settings()->get( 'auto_inline_rule', 'csp' ) && ( $name == 'default' || $name == 'script' || $name == 'style' ) ) {
				$item .= "'unsafe-inline' ";
			}

			if ( gdsih_settings()->get( 'auto_eval_rule', 'csp' ) && ( $name == 'default' || $name == 'script' ) ) {
				$item .= "'unsafe-eval' ";
			}

			if ( gdsih_settings()->get( 'auto_data_rule', 'csp' ) && ( $name == 'default' || $name == 'style' || $name == 'img' || $name == 'font' ) ) {
				$item .= "data: ";
			}

			if ( gdsih_settings()->get( 'auto_blob_rule' ) && ( $name == 'default' || $name == 'media' || $name == 'object' || $name == 'font' ) ) {
				$item .= "blob: ";
			}

			if ( gdsih_settings()->get( 'auto_mediastream_rule' ) && ( $name == 'default' || $name == 'media' || $name == 'object' || $name == 'font' ) ) {
				$item .= "mediastream: ";
			}

			if ( gdsih_settings()->get( 'auto_filesystem_rule' ) && ( $name == 'default' || $name == 'media' || $name == 'object' || $name == 'font' ) ) {
				$item .= "filesystem: ";
			}

			$custom = apply_filters( 'gdsih_csp_build_custom_rules_for_' . $name, $custom );

			$custom = array_unique( $custom );
			$custom = array_filter( $custom );

			$item .= join( ' ', $custom ) . ';';

			$items[] = $item;
		}

		return $items;
	}

	public function build( $htaccess = false ) : string {
		require_once( GDSIH_PATH . 'core/csp/cdn.php' );

		if ( gdsih_settings()->get( 'extra_wordpress', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/wordpress.php' );
		}

		if ( gdsih_settings()->get( 'extra_gravatar', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/gravatar.php' );
		}

		if ( gdsih_settings()->get( 'extra_gleam', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/gleam.php' );
		}

		if ( gdsih_settings()->get( 'extra_paypal', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/paypal.php' );
		}

		if ( gdsih_settings()->get( 'extra_instagram', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/instagram.php' );
		}

		if ( gdsih_settings()->get( 'extra_vimeo', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/vimeo.php' );
		}

		if ( gdsih_settings()->get( 'extra_google_adsense', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/google-adsense.php' );
		}

		if ( gdsih_settings()->get( 'extra_google_analytics', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/google-analytics.php' );
		}

		if ( gdsih_settings()->get( 'extra_google_fonts', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/google-fonts.php' );
		}

		if ( gdsih_settings()->get( 'extra_google_maps', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/google-maps.php' );
		}

		if ( gdsih_settings()->get( 'extra_google_translate', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/google-translate.php' );
		}

		if ( gdsih_settings()->get( 'extra_google_youtube', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/google-youtube.php' );
		}

		if ( gdsih_settings()->get( 'extra_google_tag_manager', 'csp' ) ) {
			require_once( GDSIH_PATH . 'core/csp/google-tag-manager.php' );
		}

		$this->mode = gdsih_settings()->get( 'mode', 'csp' );

		$items = array();

		$header = 'Content-Security-Policy-Report-Only';

		if ( $this->mode == 'live' ) {
			$header = 'Content-Security-Policy';
		}

		foreach ( array_keys( $this->rules ) as $key ) {
			$items = $this->rule( $items, $key );
		}

		if ( $this->mode == 'live' && gdsih_settings()->get( 'upgrade_insecure_requests', 'csp' ) ) {
			$items[] = 'upgrade-insecure-requests;';
		}

		if ( $htaccess && gdsih_settings()->get( 'disown_opener', 'csp' ) ) {
			$items[] = 'disown-opener;';
		}

		if ( gdsih_settings()->get( 'block_all_mixed_content', 'csp' ) ) {
			$items[] = 'block-all-mixed-content;';
		}

		if ( gdsih_settings()->get( 'log', 'csp' ) ) {
			$items[] = 'report-uri ' . $this->url() . ';';
		}

		if ( $htaccess ) {
			return $header . ' "' . join( ' ', $items ) . '"';
		} else {
			return $header . ': ' . join( ' ', $items );
		}
	}
}
