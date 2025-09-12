<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gdsih_referrer_policies_list() : array {
	return array(
		'no-referrer'                     => __( 'Do not send referrer', 'gd-security-headers' ),
		'no-referrer-when-downgrade'      => __( 'No referrer when downgrade', 'gd-security-headers' ),
		'same-origin'                     => __( 'Same origin', 'gd-security-headers' ),
		'origin'                          => __( 'Origin', 'gd-security-headers' ),
		'strict-origin'                   => __( 'Strict origin', 'gd-security-headers' ),
		'origin-when-cross-origin'        => __( 'Origin when cross origin', 'gd-security-headers' ),
		'strict-origin-when-cross-origin' => __( 'Strict origin when cross origin', 'gd-security-headers' ),
		'unsafe-url'                      => __( 'Unsafe URL', 'gd-security-headers' ),
	);
}

function gdsih_strict_transport_security_list() : array {
	return array(
		'none'                     => __( 'Nothing', 'gd-security-headers' ),
		'includeSubDomains'        => __( 'Include Subdomains', 'gd-security-headers' ),
		'includeSubDomainsPreload' => __( 'Include Subdomains with Preload', 'gd-security-headers' ),
	);
}

function gdsih_x_frame_options_list() : array {
	return array(
		'DENY'       => __( 'Deny', 'gd-security-headers' ),
		'SAMEORIGIN' => __( 'Same origin', 'gd-security-headers' ),
		'ALLOW-FROM' => __( 'Allowed from listed domains', 'gd-security-headers' ),
	);
}
