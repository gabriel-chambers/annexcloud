<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gdsih_list_database_tables() : array {
	global $wpdb;

	return array(
		$wpdb->base_prefix . 'gdsec_csp_reports' => 10,
		$wpdb->base_prefix . 'gdsec_xxp_reports' => 6,
	);
}

function gdsih_install_database() : array {
	global $wpdb;

	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "default CHARACTER SET $wpdb->charset";
	}

	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE $wpdb->collate";
	}

	$tables = array(
		'csp_reports' => $wpdb->base_prefix . 'gdsec_csp_reports',
		'xxp_reports' => $wpdb->base_prefix . 'gdsec_xxp_reports',
	);

	$query = "CREATE TABLE " . $tables['csp_reports'] . " (
 id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 reported datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 ip varchar(64) NOT NULL,
 violated_directive varchar(128) NOT NULL DEFAULT '',
 effective_directive varchar(128) NOT NULL DEFAULT '',
 document_uri text NOT NULL,
 blocked_uri text NOT NULL,
 referrer text NOT NULL,
 original_policy text NOT NULL,
 user_agent text NOT NULL,
 PRIMARY KEY  (id),
 KEY reported (reported),
 KEY ip (ip),
 KEY violated_directive (violated_directive),
 KEY effective_directive (effective_directive)
) $charset_collate;
CREATE TABLE " . $tables['xxp_reports'] . " (
 id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 reported datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 ip varchar(64) NOT NULL,
 request_url text NOT NULL,
 request_body longtext NOT NULL,
 user_agent text NOT NULL,
 PRIMARY KEY  (id),
 KEY reported (reported),
 KEY ip (ip)
) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	return dbDelta( $query );
}

function gdsih_check_database() : array {
	global $wpdb;

	$result = array();
	$tables = gdsih_list_database_tables();

	foreach ( $tables as $table => $count ) {
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) == $table ) {
			$columns = $wpdb->get_results( "SHOW COLUMNS FROM $table" );

			if ( $count != count( $columns ) ) {
				$result[ $table ] = array( "status" => "error", "msg" => __( 'Some columns are missing.', 'gd-security-headers' ) );
			} else {
				$result[ $table ] = array( "status" => "ok" );
			}
		} else {
			$result[ $table ] = array( "status" => "error", "msg" => __( 'Table missing.', 'gd-security-headers' ) );
		}
	}

	return $result;
}

function gdsih_truncate_database_tables() {
	global $wpdb;

	$tables = array_keys( gdsih_list_database_tables() );

	foreach ( $tables as $table ) {
		$wpdb->query( "TRUNCATE TABLE " . $table );
	}
}

function gdsih_drop_database_tables() {
	global $wpdb;

	$tables = array_keys( gdsih_list_database_tables() );

	foreach ( $tables as $table ) {
		$wpdb->query( "DROP TABLE IF EXISTS " . $table );
	}
}
