<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_core_htaccess {
	public $io;
	public $marker = 'GD Security Headers';

	function __construct() {
		d4p_include( 'marker', 'classes', GDSIH_D4PLIB );

		$this->io = new d4p_core_marker( $this->path() );
	}

	public function path() : string {
		return ABSPATH . GDSIH_HTACCESS_FILE_NAME;
	}

	public function check() : array {
		global $is_apache;

		$mods = function_exists( 'apache_get_modules' ) ? apache_get_modules() : array();

		$status = array(
			'apache'             => $is_apache,
			'file'               => GDSIH_HTACCESS_FILE_NAME,
			'htaccess'           => $this->io->path,
			'found'              => $this->io->file_exists(),
			'writable'           => $this->io->is_writable(),
			'apache_get_modules' => ! empty( $mods ),
			'mod_headers'        => in_array( 'mod_headers', $mods ),
		);

		if ( ! $status['found'] ) {
			$status['writable'] = is_writable( ABSPATH );
		}

		$status['automatic'] = $status['writable'] && $status['apache'];

		return $status;
	}

	public function reset() : bool {
		return $this->io->remove( $this->marker );
	}

	public function write() : bool {
		$rules = array();

		$rules[] = '<IfModule mod_headers.c>';
		$rules   = apply_filters( 'gdsih_htaccess_build_list', $rules );
		$rules[] = '</IfModule>';

		if ( count( $rules ) > 0 ) {
			$idx = count( $rules ) - 1;

			if ( empty( $rules[ $idx ] ) ) {
				unset( $rules[ $idx ] );
			}
		}

		return $this->io->insert( $this->marker, $rules, 'start', true );
	}
}
