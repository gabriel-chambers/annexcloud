<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_core_db extends d4p_wpdb_core {
	public $_prefix = 'gdsec';
	public $_tables = array(
		'csp_reports',
		'xxp_reports',
	);
	public $_network_tables = array(
		'csp_reports',
		'xxp_reports',
	);

	public function csp_report( $data = array() ) {
		$defaults = array(
			'reported'            => $this->datetime(),
			'ip'                  => gdsih()->ip(),
			'violated_directive'  => '',
			'effective_directive' => '',
			'document_uri'        => '',
			'blocked_uri'         => '',
			'referrer'            => '',
			'original_policy'     => '',
			'user_agent'          => '',
		);
		$data     = wp_parse_args( $data, $defaults );

		if ( empty( $data['user_agent'] ) ) {
			$data['user_agent'] = gdsih()->ua();
		}

		$result = $this->insert( $this->csp_reports, $data );

		if ( $result !== false ) {
			return $this->get_insert_id();
		} else {
			return false;
		}
	}

	public function xxp_report( $data = array() ) {
		$defaults = array(
			'reported'     => $this->datetime(),
			'ip'           => gdsih()->ip(),
			'request_url'  => '',
			'request_body' => '',
			'user_agent'   => '',
		);
		$data     = wp_parse_args( $data, $defaults );

		if ( empty( $data['user_agent'] ) ) {
			$data['user_agent'] = gdsih()->ua();
		}

		$result = $this->insert( $this->xxp_reports, $data );

		if ( $result !== false ) {
			return $this->get_insert_id();
		} else {
			return false;
		}
	}

	public function empty_reports_logs( $tables ) {
		foreach ( $tables as $t ) {
			if ( $t == 'csp' ) {
				$this->query( "TRUNCATE TABLE " . $this->csp_reports );
			}

			if ( $t == 'xxp' ) {
				$this->query( "TRUNCATE TABLE " . $this->xxp_reports );
			}
		}
	}

	public function cleanup_reports( $days = 365, $tables = array() ) {
		foreach ( $tables as $t ) {
			if ( $t == 'csp' ) {
				$this->query( $this->prepare( "DELETE FROM " . $this->csp_reports . " WHERE reported < DATE_SUB(NOW(), INTERVAL %d DAY)", $days ) );
			}

			if ( $t == 'xxp' ) {
				$this->query( $this->prepare( "DELETE FROM " . $this->xxp_reports . " WHERE reported < DATE_SUB(NOW(), INTERVAL %d DAY)", $days ) );
			}
		}
	}
}
