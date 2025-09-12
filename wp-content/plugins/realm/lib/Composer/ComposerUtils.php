<?php

namespace E25m\Realm\Composer;

use Composer\Script\Event;
use Exception;
use ZipArchive;

/**
 * Composer post package install and update handlers
 * Moves common folder or theme folder files of installed package
 */
class ComposerUtils
{
	const REALM_PACKAGE_TYPES = [
		'e25-realm-section',
		'e25-realm-base-section'
	];
	const DEFAULT_COMMON_FOLDER_PATH = 'src/common-components';
	const DEFAULT_THEME_FOLDER_PATH = '../../themes/berg-theme-child';
	const TEMP_FOLDER = '.tmp';
	private static $common_folder_path = null;
	private static $theme_folder_path = null;
	private static $package = null;
	private static $root_package = null;
	private static $realm_path = null;
	private static $temp_path = null;
	private static $package_installed_path = null;
	private static $output_dir = null;
	private static $override_excludes = [];

	/**
	 * Archive Realm section
	 * @param  Event $event
	 * @return void
	 */
	public static function archivePackage(Event $event) : void
	{
		$args = $event->getArguments();
		if (sizeof($args) < 2) {
			throw new Exception("Package name and output directory is required.(ex: \"composer archive-package e25/card-stack-layout-2-imp-1\" ~/Desktop)");
		}
		self::$root_package = $event->getComposer()->getPackage();
		self::extractExtras();
		self::$realm_path = getcwd();
		if (file_exists(self::$realm_path . '/wp-content')) {
			self::$realm_path = self::$realm_path . '/wp-content/plugins/realm';
		}
		self::$temp_path = self::$realm_path . '/' . self::TEMP_FOLDER;

		$package_name = $args[0];
		self::$output_dir = $args[1];
		self::$package = $event->getComposer()->getRepositoryManager()->findPackage($package_name, '*');
		if (is_null(self::$package)
				|| (!is_null(self::$package)
					&& !in_array(self::$package->getType(), self::REALM_PACKAGE_TYPES))) {
			throw new Exception('Invalid package or package not found.');
		}

		if (!file_exists(self::$output_dir) || !is_writable(self::$output_dir)) {
			throw new Exception('Output directory is invalid or not writable.', );
		}

		$installation_manager = $event->getComposer()->getInstallationManager();
		self::$package_installed_path = self::$realm_path . '/' . $installation_manager->getInstallPath(self::$package);

		$common_files = self::getPackageCommonFiles(self::$package);
		$theme_files = self::getPackageThemeFiles(self::$package);

		self::recursiveCopy(self::$package_installed_path, self::$temp_path);
		self::copyCommonFiles($common_files);
		self::copyThemeFiles($theme_files);
		self::createZip();
		self::deleteDirectory(self::$temp_path);
	}

	/**
	 * Extract composer.json extra values like common and theme folder paths from root package
	 * @return void
	 */
	private static function extractExtras() : void
	{
		$extra = self::$root_package->getExtra();
		if (!array_key_exists('realm', $extra)) {
			return;
		}
		$extra = $extra['realm'];
		self::$common_folder_path = array_key_exists('common_folder_path', $extra)
			&& is_string($extra['common_folder_path'])
				? $extra['common_folder_path']
				: self::DEFAULT_COMMON_FOLDER_PATH;
		self::$theme_folder_path = array_key_exists('theme_folder_path', $extra)
			&& is_string($extra['theme_folder_path'])
				? $extra['theme_folder_path']
				: self::DEFAULT_THEME_FOLDER_PATH;
		self::$override_excludes = array_key_exists('exclude_theme_overrides', $extra)
			&& is_array($extra['exclude_theme_overrides'])
				? $extra['exclude_theme_overrides']
				: [];
	}

	/**
	 * Get common files of installed package
	 * @return array
	 */
	private static function getPackageCommonFiles() : array
	{
		$common_files = [];
		$extra = self::$package->getExtra();
		if (array_key_exists('common_files', $extra) && is_array($extra['common_files'])) {
			$common_files = $extra['common_files'];
		}
		return $common_files;
	}

	/**
	 * Get theme files of installed package
	 * @return array
	 */
	private static function getPackageThemeFiles() : array
	{
		$theme_files = [];
		$extra = self::$package->getExtra();
		if (array_key_exists('theme_files', $extra) && is_array($extra['theme_files'])) {
			$theme_files = $extra['theme_files'];
		}
		return $theme_files;
	}

	/**
	 * Recursively copy all files and folders in $src to $dest
	 * @param string $src
	 * @param string $dest
	 * @return void
	 */
	private static function recursiveCopy($src, $dest)
	{
		$dir = opendir($src);
		if (!file_exists($dest)) {
			mkdir($dest, 0777, true);
		}
		while (($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					self::recursiveCopy("{$src}/{$file}", "{$dest}/{$file}");
				} else {
					copy("{$src}/{$file}", "{$dest}/{$file}");
				}
			}
		}
		closedir($dir);
	}

	/**
	 * Copy common files to temp folder
	 * @param array $files
	 * @return void
	 */
	private static function copyCommonFiles(array $files) : void
	{
		$common_folder = self::$realm_path . '/' . self::$common_folder_path;
		if (!file_exists($common_folder) || !is_dir($common_folder) || !is_readable($common_folder)) {
			throw new \Exception("{$common_folder} is invalid or not writable");
		}

		foreach ($files as $file) {
			if (!array_key_exists('src', $file) || !array_key_exists('dest', $file)) {
				continue;
			}
			$src = $common_folder . '/' . $file['dest'];
			$dest = self::$temp_path . '/' . $file['src'];
			$dest_segments = explode('/', $dest);
			array_pop($dest_segments);
			$dest_without_file = implode('/', $dest_segments);
			if (!file_exists($dest_without_file)) {
				mkdir($dest_without_file, 0777, true);
			}
			if (file_exists($src)) {
				copy($src, $dest);
			}
		}
	}

	/**
	 * Copy theme files to temp folder
	 * @param array $files
	 * @return void
	 */
	private static function copyThemeFiles(array $files) : void
	{
		$theme_folder = self::$realm_path . '/' . self::$theme_folder_path;
		if (!file_exists($theme_folder) || !is_dir($theme_folder) || !is_writable($theme_folder)) {
			throw new \Exception("{$theme_folder} is invalid or not writable");
		}

		foreach ($files as $file) {
			if (!array_key_exists('src', $file) || !array_key_exists('dest', $file)) {
				continue;
			}
			$src = $theme_folder . '/' . $file['dest'];
			$dest = self::$temp_path . '/' . $file['src'];
			$dest_segments = explode('/', $dest);
			array_pop($dest_segments);
			$dest_without_file = implode('/', $dest_segments);
			if (!file_exists($dest_without_file)) {
				mkdir($dest_without_file, 0777, true);
			}
			if (file_exists($src)) {
				copy($src, $dest);
			}

			if (array_key_exists('overrideFilePath', $file)) {
				$override_file_path = $theme_folder . '/' . $file['overrideFilePath'];
				$package_name = self::$package->getName();
				$file_path_segments = explode('/', $override_file_path);
				$file_name = array_pop($file_path_segments);
				if ((!array_key_exists($package_name, self::$override_excludes)
					|| (array_key_exists($package_name, self::$override_excludes)
						&& !in_array($file_name, self::$override_excludes[$package_name])))
					&& file_exists($override_file_path)) {
					$custom_files_folder = self::$temp_path . '/custom_files';
					if (!file_exists($custom_files_folder)) {
						mkdir($custom_files_folder);
					}
					copy($override_file_path, $custom_files_folder . '/' . $file_name);
				}
			}
		}
	}

	/**
	 * ZIP .tmp folder content
	 * @return void
	 */
	private static function createZip() : void
	{
		$zip_file_name = str_replace('/', '_', self::$package->getName());
		$zip_file_path = realpath(self::$output_dir) . '/' . $zip_file_name;
		$zip = new ZipArchive();
		$zip->open($zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator(self::$temp_path),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file) {
			if (!$file->isDir()) {
				$file_path = $file->getRealPath();
				$relative_path = substr($file_path, strlen(self::$temp_path) + 1);
				$zip->addFile($file_path, $relative_path);
			}
		}
		$zip->close();
	}

	/**
	 * Delete non-empty directory
	 * @param string $dir
	 * @return void
	 */
	private static function deleteDirectory($dir)
	{
		if (!file_exists($dir)) {
			return true;
		}
		if (!is_dir($dir)) {
			return unlink($dir);
		}
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}
			if (!self::deleteDirectory("{$dir}/{$item}")) {
				return false;
			}
		}
		return rmdir($dir);
	}
}
