<?php

namespace E25m\Realm\traits;

trait AssetHelper
{
	/**
	 * Get dependencies and version of script file
	 * @param  string $script_file_name
	 * @return array
	 */
	public function getScriptDependenciesAndVersion(string $script_file_name) : array 
	{
		$deps_and_version = [ 'version' => null, 'dependencies' => [] ];
		if (!isset($this->context)) return $deps_and_version;

		$file_path = $this->context['path'] . "dist/{$script_file_name}.asset.php";
		if (file_exists($file_path)) {
			$deps_and_version = include($file_path);
		}
		return $deps_and_version;
	}
}
