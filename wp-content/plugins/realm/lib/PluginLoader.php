<?php

namespace E25m\Realm;

/**
 * Bootstrap Plugin modules
 */
class PluginLoader
{
	public $context;

	function __construct($context)
	{
		$this->context = $context;
	}

	/**
	 * @return void
	 */
	public function init() : void
	{
		(new ModuleLoader($this->context))->init();
	}
}
