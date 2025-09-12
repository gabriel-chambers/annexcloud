<?php

namespace E25m\Realm;

/**
 * Find and activate module
 */
class ModuleLoader
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
		$modules = $this->get_modules();
		$this->load_modules($modules);
	}

	/**
	 * Get All module main files
	 *
	 * @return array
	 */
	private function get_modules() : array {
		return glob(__DIR__ . '/*/Module.php');
	}

	/**
	 * Load modules
	 *
	 * @param  array $modules
	 * @return void
	 */
	private function load_modules(array $modules) {
		foreach ($modules as $path) {
			try {
				$class  = __NAMESPACE__ . '\\' . basename(dirname($path)) . '\\Module';
				$module = new $class();
				$this->activate_module($module); // Send to new method for type safety.
			} catch (\TypeError $e ) {
				// Invalid module.
				echo esc_html($e->getMessage()) . "\n\n";
				continue;
			} catch (\Exception $e) {
				echo esc_html($e->getMessage()) . "\n\n";
				return;
			}
		}
	}

	/**
	 * Activate loaded module
	 *
	 * @param  Interfaces\ModuleInterface $module
	 * @return void
	 */
	private function activate_module(Interfaces\ModuleInterface $module) {
		if ($module->is_enabled()) {
			$module->activate( $this->context);
		}
	}
}
