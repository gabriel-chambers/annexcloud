<?php

namespace E25m\Realm\Composer;

use Composer\Installer\PackageEvent;
use Composer\Package\CompletePackage;

/**
 * Composer post package install and update handlers
 * Moves common folder or theme folder files of installed package
 */
class Installer extends InstallerBase
{
	/**
	 * Composer post-package-install handler
	 * @param  PackageEvent $event
	 * @return void
	 */
	public static function postPackageInstall(PackageEvent $event) : void
	{
		self::$package = $event->getOperation()->getPackage();
		self::processPackage($event);
	}

	/**
	 * Composer post-package-update handler
	 * @param  PackageEvent $event
	 * @return void
	 */
	public static function postPackageUpdate(PackageEvent $event) : void
	{
		self::$package = $event->getOperation()->getTargetPackage();
		self::processPackage($event);
	}

	/**
	 * Process installed or updated package
	 * @param  PackageEvent $event
	 * @return void
	 */
	private static function processPackage(PackageEvent $event) : void
	{
		self::$root_package = $event->getComposer()->getPackage();
		if (!in_array(self::$package->getType(), self::REALM_PACKAGE_TYPES)) {
			return;
		}
		self::extractExtras();
		self::$realm_path = getcwd();
		if (file_exists(self::$realm_path . '/wp-content')) {
			self::$realm_path = self::$realm_path . '/wp-content/plugins/realm';
		}
		$installation_manager = $event->getComposer()->getInstallationManager();
		self::$package_installed_path = self::$realm_path . '/' . $installation_manager->getInstallPath(self::$package);

		$common_files = self::getPackageCommonFiles(self::$package);
		$theme_files = self::getPackageThemeFiles(self::$package);
		if (sizeof($common_files) > 0) {
			self::moveCommonFiles($common_files);
		}
		if (sizeof($theme_files) > 0) {
			self::moveThemeFiles($theme_files);
		}
	}

	/**
	 * Move common files of a package to common files folder path
	 * @param  array  $files
	 * @return void
	 */
	protected static function moveCommonFiles(array $files) : void
	{
		$common_folder = self::$realm_path . '/' . self::$common_folder_path;
		if (!file_exists($common_folder) || !is_dir($common_folder) || !is_writable($common_folder)) {
			throw new \Exception("{$common_folder} is invalid or not writable");
		}
		foreach ($files as $file) {
			if (!array_key_exists('src', $file) || !array_key_exists('dest', $file)) {
				continue;
			}
			$src = self::$package_installed_path . '/' . $file['src'];
			$dest = $common_folder . '/' . $file['dest'];
			self::linkFile($src, $dest);
		}
	}

	/**
	 * Move theme files of a package to theme files folder path
	 * @param  array  $files
	 * @return void
	 */
	protected static function moveThemeFiles(array $files) : void
	{
		$theme_folder = self::$realm_path . '/' . self::$theme_folder_path;
		if (!file_exists($theme_folder) || !is_dir($theme_folder) || !is_writable($theme_folder)) {
			throw new \Exception("{$theme_folder} is invalid or not writable");
		}
		foreach ($files as $file) {
			if (!array_key_exists('src', $file) || !array_key_exists('dest', $file)) {
				continue;
			}
			$src = self::$package_installed_path . '/' . $file['src'];
			$dest = $theme_folder . '/' . $file['dest'];
			self::linkFile($src, $dest);

			// Override files
			if (array_key_exists('overrideFilePath', $file)) {
				$override_file_path = $theme_folder . '/' . $file['overrideFilePath'];
				$package_name = self::$package->getName();
				$file_path_segments = explode('/', $override_file_path);
				$file_name = array_pop($file_path_segments);
				if ((!array_key_exists($package_name, self::$override_excludes)
						|| (array_key_exists($package_name, self::$override_excludes)
							&& !in_array($file_name, self::$override_excludes[$package_name])))
					&& !file_exists($override_file_path)) {
						$file_name_segments = explode('.', $file_name);
						$file_extension = array_pop($file_name_segments);
						self::createOverrideFileIfNotExists($dest, $override_file_path, $file_extension);
					}
			}
		}
	}

	/**
	 * Symlink file/folder from source to destination
	 * @param  string $src
	 * @param  string $dest
	 * @return void
	 */
	private static function linkFile($src, $dest) : void
	{
		$src_abs = realpath($src);
		if (!$src_abs || !file_exists($src_abs)) {
			throw new \Exception($src_abs ? "{$src_abs} path does not exists" : "{$src} does not exists");
		}

		$dest_path_tree = explode('/', $dest);
		array_pop($dest_path_tree);
		$dest_path_up_to_immediate_parent_dir = implode('/', $dest_path_tree);
		if (!file_exists($dest_path_up_to_immediate_parent_dir)) {
			mkdir($dest_path_up_to_immediate_parent_dir, 0777, true);
		}

		if (file_exists($dest)) {
			unlink($dest);
		}
		symlink($src, $dest);
	}

	/**
	 * Create override file if not already exists in the given path
	 * @param  string $source_file
	 * @param  string $dest_file_path
	 * @param  string $file_extension
	 * @return void
	 */
	private static function createOverrideFileIfNotExists($source_file,
		$dest_file_path, $file_extension) : void
	{
		$file_content = '';
		$common_path = '';
		$common_path_temp = '';
		$folder_index = 0;
		$dest_file_path_segments = explode('/', $dest_file_path);
		while (self::stringStartsWith($source_file, $common_path_temp)
			&& $folder_index < sizeof($dest_file_path_segments) -1) {
			$common_path = $common_path_temp;
			$common_path_temp  .= $dest_file_path_segments[$folder_index] . '/';
			$folder_index++;
		}
		$dest_file_path_relative = str_replace($common_path, '', $dest_file_path);
		$dest_file_path_relative_segments = explode('/', $dest_file_path_relative);
		array_pop($dest_file_path_relative_segments);
		$number_of_folders = sizeof($dest_file_path_relative_segments);
		$relative_path_to_file = './';
		for ($i=0; $i < $number_of_folders; $i++) {
			$relative_path_to_file .= '../';
		}
		$relative_path_to_file .= str_replace($common_path, '', $source_file);
		switch ($file_extension) {
			case 'scss':
				$file_content = "@import '{$relative_path_to_file}';";
				break;
			case 'js':
				$file_content = "require('{$relative_path_to_file}');";
				break;
		}
		array_pop($dest_file_path_segments);
		$dest_folder = implode('/', $dest_file_path_segments);
		if (!file_exists($dest_folder)) {
			mkdir($dest_folder, 0777, true);
		}
		file_put_contents($dest_file_path, $file_content);
	}

	/**
	 * Check given arg 1 string starts with arg 2 string
	 * @param string $string
	 * @param string $sub_string
	 * @return boolean
	 */
	private static function stringStartsWith($string, $sub_string) : bool
	{
		$len = strlen($sub_string);
		return (substr($string, 0, $len) === $sub_string);
	}
}
