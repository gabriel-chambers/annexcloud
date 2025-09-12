<?php

namespace Opinionstage\Core;

defined( 'ABSPATH' ) || die();

/**
 * Trait Provider
 *
 * @package Opinionstage\Core
 */
trait Provider {
	use Module;

	/**
	 * @return void
	 */
	public function init() {
		foreach ($this->get_modules() as $module) {
			$module::get_instance();
		}
	}

	/**
	 * Return an array of modules
	 * @return array
	 */
	abstract protected function get_modules();
}