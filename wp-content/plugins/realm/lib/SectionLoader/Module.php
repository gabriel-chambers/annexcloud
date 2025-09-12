<?php

namespace E25m\Realm\SectionLoader;

use E25m\Realm\Interfaces\ModuleInterface;

/**
 *
 */
class Module implements ModuleInterface
{
	private $context;

	public function is_enabled() : bool
	{
		return apply_filters('enable_e25_realm_sections', true);
	}

	public function activate(array $context): void
	{
		$this->context = $context;
		(new AssetManager($context))->enqueueAssets();
		$this->includeBlockRegistrationFiles();
	}

	private function includeBlockRegistrationFiles()
	{
		$module_path = $this->context['path'] . 'sec/section';

        if (file_exists($module_path)) {
            $modules = glob($module_path . '/*', GLOB_ONLYDIR);
            foreach ($modules as $module) {
                $module_path_segments = explode('/', $module);
                $module_name = array_pop($module_path_segments);
                if (preg_match("/block-template/", $module_name) === 1) {
                    continue;
                } // ignore block template
                $module_initializer_file = "{$module_name}.php";
                $module_initializer_file_full_path = "{$module}/{$module_initializer_file}";
                if (file_exists($module_initializer_file_full_path)) {
                    require_once $module_initializer_file_full_path;
                }
            }
        }
	}
}
