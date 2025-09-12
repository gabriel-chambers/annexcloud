<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_core_feature_policy {
	public function __construct() {
	}

	public function rule( $items, $name, $method = 'feature-policy' ) {
		$basic  = gdsih_settings()->get( $name . '_basic', 'feature' );
		$custom = gdsih_settings()->get( $name . '_custom', 'feature' );

		$basic = apply_filters( 'gdsih_feature-policy_build_basic_rule', $basic, $name );
		$basic = apply_filters( 'gdsih_feature-policy_build_basic_rule_for_' . $name, $basic );

		if ( $basic != 'no' ) {
			$item_content = '';

			if ( $basic == 'none' ) {
				$item_content = $method == 'feature-policy' ? "'none'" : '';
			} else if ( $basic == 'all' ) {
				$item_content = '*';
			} else if ( $basic == 'self' ) {
				$item_content = $method == 'feature-policy' ? "'self'" : "self";
			} else if ( $basic == 'custom' || $basic == 'custom_self' ) {
				$custom = apply_filters( 'gdsih_feature-policy_build_custom_rules_for_' . $name, $custom );

				$custom = array_unique( $custom );
				$custom = array_filter( $custom );

				if ( $basic == 'custom_self' ) {
					$item_content = $method == 'feature-policy' ? "'self'" : "self";
				}

				if ( ! empty( $custom ) ) {
					$item_content = $method == 'feature-policy' ? ' ' . join( ' ', $custom ) : " '" . join( "', '", $custom ) . "'";
				}
			}

			$item_content = trim( $item_content );

			if ( ! empty( $item_content ) || $method != 'feature-policy' ) {
				$item = $method == 'feature-policy' ? $name . ' ' : $name . '=(';
				$item .= $item_content;
				$item .= $method == 'feature-policy' ? ';' : '),';

				$items[] = $item;
			}
		}

		return $items;
	}

	public function build( $htaccess = false ) : array {
		$variant = gdsih_settings()->get( 'variant', 'feature' );

		$headers = array();

		if ( $variant == 'feature-policy' || $variant == 'both' ) {
			$header = 'Feature-Policy';
			$items  = array();

			foreach ( array_keys( gdsih_settings()->features ) as $key ) {
				$items = $this->rule( $items, $key );
			}

			if ( ! empty( $items ) ) {
				$_rule = join( ' ', $items );

				if ( $htaccess ) {
					$headers[] = $header . ' "' . $_rule . '"';
				} else {
					$headers[] = $header . ': ' . $_rule;
				}
			}
		}

		if ( $variant == 'permissions-policy' || $variant == 'both' ) {
			$header = 'Permissions-Policy';
			$items  = array();

			foreach ( array_keys( gdsih_settings()->features ) as $key ) {
				$items = $this->rule( $items, $key, 'permissions-policy' );
			}

			if ( ! empty( $items ) ) {
				$_rule = trim( join( ' ', $items ), ',' );

				if ( $htaccess ) {
					$headers[] = $header . ' "' . $_rule . '"';
				} else {
					$headers[] = $header . ': ' . $_rule;
				}
			}
		}

		return $headers;
	}
}
