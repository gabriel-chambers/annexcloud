<?php

namespace E25m\Realm\Composer;

use Composer\Installer\PackageEvent;
use Composer\Package\CompletePackage;

/**
 * Composer post package install and update handlers
 * Moves common folder or theme folder files of installed package
 */
class Uninstaller extends InstallerBase
{
	/**
	 * Composer pre-package-uninstall handler
	 * @param  PackageEvent $event
	 * @return void
	 */
	public static function prePackageUninstall(PackageEvent $event) : void
	{
		self::$package = $event->getOperation()->getPackage();
		self::processPackage($event);
	}

	/**
	 * Process pre package uninstall
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
			self::removeCommonFiles($common_files);
		}
		if (sizeof($theme_files) > 0) {
			self::removeThemeFiles($theme_files);
		}
	}

	/**
	 * Remove common files of a package
	 * @param  array  $files
	 * @return void
	 */
	private static function removeCommonFiles(array $files) : void
	{
		$common_folder = self::$realm_path . '/' . self::$common_folder_path;
		if (!file_exists($common_folder) || !is_dir($common_folder) || !is_writable($common_folder)) {
			throw new \Exception("{$common_folder} is invalid or not writable");
		}
		foreach ($files as $file) {
			if (!array_key_exists('src', $file) || !array_key_exists('dest', $file)) {
				continue;
			}
			$path = $common_folder . '/' . $file['dest'];
			self::deleteFile($path);
		}
	}

	/**
	 * Remove theme files of a package
	 * @param  array  $files
	 * @return void
	 */
	private static function removeThemeFiles(array $files) : void
	{
		$theme_folder = self::$realm_path . '/' . self::$theme_folder_path;
		if (!file_exists($theme_folder) || !is_dir($theme_folder) || !is_writable($theme_folder)) {
			throw new \Exception("{$theme_folder} is invalid or not writable");
		}
		foreach ($files as $file) {
			if (!array_key_exists('src', $file) || !array_key_exists('dest', $file)) {
				continue;
			}
			$path = $theme_folder . '/' . $file['dest'];
			self::deleteFile($path);

			// Override files
			if (array_key_exists('overrideFilePath', $file)) {
				$override_file_path = $theme_folder . '/' . $file['overrideFilePath'];
				if (file_exists($override_file_path)) {
					$override_file_path = realpath($override_file_path);
					echo PHP_EOL;
					echo "\e[33mPlease remove file {$override_file_path} manually if it's not required.";
					echo PHP_EOL;
				}
			}
		}
	}

	/**
	 * Delete file and delete parent folder ONLY if empty
	 * @param  string $path
	 * @return void
	 */
	private static function deleteFile($path) : void
	{
		if (!file_exists($path)) {
			return;
		}
		unlink($path);
		$path_segments = explode(DIRECTORY_SEPARATOR, $path);
		array_pop($path_segments);
		$parent_folder_path = implode(DIRECTORY_SEPARATOR, $path_segments);
		if (!file_exists($parent_folder_path)
		|| !is_dir($parent_folder_path)
		|| (is_dir($parent_folder_path) && sizeof(scandir($parent_folder_path)) > 2)) return;
		rmdir($parent_folder_path);
	}
}
