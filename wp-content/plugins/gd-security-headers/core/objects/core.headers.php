<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_component_headers {
	public function __construct() {
		if ( ! D4P_CRON && ! gdsih_settings()->get( 'htaccess' ) ) {
			$this->_headers();
		}

		add_filter( 'gdsih_htaccess_build_list', array( $this, 'htaccess' ) );
	}

	public function htaccess( $htaccess = array() ) {
		$list = $this->build( true );

		if ( ! empty( $list ) ) {
			foreach ( $list as $key => $item ) {
				$htaccess[] = D4P_TAB . '# add header: ' . $key;
				$htaccess[] = D4P_TAB . 'Header always set ' . $item;
			}
		}

		return $htaccess;
	}

	public function build( $htaccess = false ) : array {
		$list = array();

		if ( gdsih_settings()->get( 'x_content_type_nosniff', 'headers' ) ) {
			$list['x-content-type-options'] = $this->_generate_x_content_type_nosniff( $htaccess );
		}

		if ( gdsih_settings()->get( 'x_frame_options_sameorigin', 'headers' ) ) {
			$list['x-frame-options'] = $this->_generate_x_frame_options_sameorigin( $htaccess );
		}

		if ( gdsih_settings()->get( 'strict_transport_security', 'headers' ) ) {
			$list['strict-transport-security'] = $this->_generate_strict_transport_security( $htaccess );
		}

		if ( gdsih_settings()->get( 'referrer_policy', 'headers' ) ) {
			$list['referrer-policy'] = $this->_generate_referrer_policy( $htaccess );
		}

		return $list;
	}

	private function _headers() {
		$list = $this->build();

		foreach ( $list as $value ) {
			header( $value );
		}
	}

	private function _generate_x_content_type_nosniff( $htaccess = false ) : string {
		return $htaccess ? 'X-Content-Type-Options "nosniff"' : 'X-Content-Type-Options: nosniff';
	}

	private function _generate_x_frame_options_sameorigin( $htaccess = false ) : string {
		$value = gdsih_settings()->get( 'x_frame_options_sameorigin_value', 'headers' );

		$values = array_keys( gdsih_x_frame_options_list() );

		if ( ! in_array( $value, $values ) ) {
			$value = 'ALLOW-FROM';
		}

		if ( $value == 'ALLOW-FROM' ) {
			$value .= ' ' . gdsih_settings()->get( 'x_frame_options_sameorigin_domains', 'headers' );
		}

		return $htaccess ? 'X-Frame-Options "' . $value . '"' : 'X-Frame-Options: ' . $value;
	}

	private function _generate_strict_transport_security( $htaccess = false ) : string {
		$max_age = gdsih_settings()->get( 'strict_transport_security_max_age', 'headers' );

		if ( gdsih_settings()->get( 'strict_transport_security_extra', 'headers' ) === 'includeSubDomains' ) {
			$max_age .= '; includeSubDomains';
		} else if ( gdsih_settings()->get( 'strict_transport_security_extra', 'headers' ) === 'includeSubDomainsPreload' ) {
			$max_age .= '; includeSubDomains; preload';
		}

		return $htaccess ? 'Strict-Transport-Security "max-age=' . $max_age . '"' : 'Strict-Transport-Security: max-age=' . $max_age;
	}

	private function _generate_referrer_policy( $htaccess = false ) : string {
		$policy = gdsih_settings()->get( 'referrer_policy_value', 'headers' );

		$policies = array_keys( gdsih_referrer_policies_list() );

		if ( ! in_array( $policy, $policies ) ) {
			$policy = 'no-referrer-when-downgrade';
		}

		return $htaccess ? 'Referrer-Policy "' . $policy . '"' : 'Referrer-Policy: ' . $policy;
	}
}
