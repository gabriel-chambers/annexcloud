<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_admin_maintenance {
	public static function delete_csp_report( $log_id ) {
		$log_id = (array) $log_id;

		$sql = "DELETE FROM " . gdsih_db()->csp_reports . " 
                WHERE id in (" . join( ', ', $log_id ) . ")";
		gdsih_db()->query( $sql );
	}

	public static function delete_xxp_report( $log_id ) {
		$log_id = (array) $log_id;

		$sql = "DELETE FROM " . gdsih_db()->xxp_reports . " 
                WHERE id in (" . join( ', ', $log_id ) . ")";
		gdsih_db()->query( $sql );
	}
}
