<?php

namespace E25m\Realm\Interfaces;

interface ModuleInterface {

	/**
	 * Activate module
	 *
	 * @param  array $context;
	 * @return void
	 */
	public function activate(array $context) : void;

	/**
	 * Check whether the module is enabled
	 *
	 * @return boolean
	 */
	public function is_enabled() : bool;
}
