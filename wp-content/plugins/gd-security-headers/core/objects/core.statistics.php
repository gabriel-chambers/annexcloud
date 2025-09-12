<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_core_statistics {
	public function __construct() {
	}

	public static function instance( $ip = '' ) : gdsih_core_statistics {
		static $_gdsih_statistics = null;

		if ( is_null( $_gdsih_statistics ) ) {
			$_gdsih_statistics = new gdsih_core_statistics();
		}

		return $_gdsih_statistics;
	}

	public function permissions() : array {
		$list = array(
			'total'       => array(
				'icon'  => 'tags',
				'label' => __( 'Total Number of Rules', 'gd-security-headers' ),
				'color' => '#000088',
				'count' => 0,
			),
			'used'        => array(
				'icon'  => 'tag',
				'label' => __( 'Used Rules', 'gd-security-headers' ),
				'color' => '#008800',
				'css'   => 'width: 50%',
				'count' => 0,
			),
			'none'        => array(
				'icon'  => 'tag',
				'label' => __( 'Not Allowed', 'gd-security-headers' ),
				'color' => '#ec750c',
				'css'   => 'width: 50%; padding-left: 10px;',
				'count' => 0,
			),
			'all'         => array(
				'icon'  => 'tag',
				'label' => __( 'Allowed for All', 'gd-security-headers' ),
				'color' => '#484848',
				'css'   => 'width: 50%',
				'count' => 0,
			),
			'self'        => array(
				'icon'  => 'tag',
				'label' => __( 'Allowed for Self', 'gd-security-headers' ),
				'color' => '#484848',
				'css'   => 'width: 50%; padding-left: 10px;',
				'count' => 0,
			),
			'custom_self' => array(
				'icon'  => 'tag',
				'label' => __( 'Allowed for Self and Custom URL\'s', 'gd-security-headers' ),
				'color' => '#484848',
				'css'   => 'width: 50%',
				'count' => 0,
			),
			'custom'      => array(
				'icon'  => 'tag',
				'label' => __( 'Allowed for Custom URL\'s Only', 'gd-security-headers' ),
				'color' => '#484848',
				'css'   => 'width: 50%; padding-left: 10px;',
				'count' => 0,
			),
		);

		foreach ( array_keys( gdsih_settings()->features ) as $key ) {
			$list['total']['count'] ++;

			$basic = gdsih_settings()->get( $key . '_basic', 'feature' );

			if ( $basic != 'no' ) {
				$list['used']['count'] ++;
				$list[ $basic ]['count'] ++;
			}
		}

		return $list;
	}

	public function headers() : array {
		$list = array(
			array(
				'icon'        => 'tag',
				'label'       => __( 'Content Security Policy', 'gd-security-headers' ),
				'status'      => gdsih_settings()->get( 'mode', 'csp' ) != 'disable',
				'url'         => network_admin_url( 'admin.php?page=gd-security-headers-settings&panel=csp' ),
				'recommended' => true,
				'csp'         => true,
				'live'        => gdsih_settings()->get( 'mode', 'csp' ) == 'live',
				'report'      => gdsih_settings()->get( 'mode', 'csp' ) == 'report',
			),
			array(
				'icon'        => 'tag',
				'label'       => __( 'XSS Protection', 'gd-security-headers' ),
				'status'      => gdsih_settings()->get( 'x_xss_protection', 'xxp' ),
				'url'         => network_admin_url( 'admin.php?page=gd-security-headers-settings&panel=xxp' ),
				'recommended' => true,
			),
			array(
				'icon'        => 'tag',
				'label'       => __( 'Feature/Permissions Policy', 'gd-security-headers' ),
				'status'      => gdsih_settings()->get( 'protection', 'feature' ),
				'url'         => network_admin_url( 'admin.php?page=gd-security-headers-settings&panel=feature' ),
				'recommended' => true,
			),
			array(
				'icon'        => 'tag',
				'label'       => __( 'Referrer Policy', 'gd-security-headers' ),
				'status'      => gdsih_settings()->get( 'referrer_policy', 'headers' ),
				'url'         => network_admin_url( 'admin.php?page=gd-security-headers-settings&panel=headers' ),
				'recommended' => true,
			),
			array(
				'icon'        => 'tag',
				'label'       => __( 'Content Type', 'gd-security-headers' ),
				'status'      => gdsih_settings()->get( 'x_content_type_nosniff', 'headers' ),
				'url'         => network_admin_url( 'admin.php?page=gd-security-headers-settings&panel=headers' ),
				'recommended' => true,
			),
			array(
				'icon'        => 'tag',
				'label'       => __( 'Strict Transport Security', 'gd-security-headers' ),
				'status'      => gdsih_settings()->get( 'strict_transport_security', 'headers' ),
				'url'         => network_admin_url( 'admin.php?page=gd-security-headers-settings&panel=headers' ),
				'recommended' => is_ssl(),
				'ssl'         => ! is_ssl(),
			),
			array(
				'icon'        => 'tag',
				'label'       => __( 'Frame Options', 'gd-security-headers' ),
				'status'      => gdsih_settings()->get( 'x_frame_options_sameorigin', 'headers' ),
				'url'         => network_admin_url( 'admin.php?page=gd-security-headers-settings&panel=headers' ),
				'recommended' => gdsih_settings()->get( 'mode', 'csp' ) == 'disable',
			),
		);

		return $list;
	}

	public function get_reports_overview_week() : array {
		$csp = "SELECT COUNT(*) FROM " . gdsih_db()->csp_reports . " WHERE `reported` > DATE(NOW()) - INTERVAL 7 DAY";
		$xxp = "SELECT COUNT(*) FROM " . gdsih_db()->xxp_reports . " WHERE `reported` > DATE(NOW()) - INTERVAL 7 DAY";

		return array(
			'csp' => array(
				'label'   => 'CSP',
				'active'  => gdsih_settings()->get( 'mode', 'csp' ) != 'disable',
				'reports' => gdsih_db()->get_var( $csp ),
			),
			'xxp' => array(
				'label'   => 'XXP',
				'active'  => gdsih_settings()->get( 'x_xss_protection', 'xxp' ),
				'reports' => gdsih_db()->get_var( $xxp ),
			),
		);
	}

	public function get_csp_urls_week() {
		$sql = "SELECT `document_uri` AS url, COUNT(*) AS `reports` FROM " . gdsih_db()->csp_reports . " WHERE `reported` > DATE(NOW()) - INTERVAL 7 DAY GROUP BY `document_uri` ORDER BY `reports` DESC LIMIT 0, 10;";

		return gdsih_db()->get_results( $sql );
	}

	public function get_xxp_urls_week() {
		$sql = "SELECT `request_url` AS url, COUNT(*) AS `reports` FROM " . gdsih_db()->xxp_reports . " WHERE `reported` > DATE(NOW()) - INTERVAL 7 DAY GROUP BY `request_url` ORDER BY `reports` DESC LIMIT 0, 10;";

		return gdsih_db()->get_results( $sql );
	}
}

function gdsih_statistics() : gdsih_core_statistics {
	return gdsih_core_statistics::instance();
}
