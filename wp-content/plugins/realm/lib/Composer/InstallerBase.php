<?php

namespace E25m\Realm\Composer;

use Composer\Installer\PackageEvent;
use Composer\Package\CompletePackage;

class InstallerBase
{
	const REALM_PACKAGE_TYPES = [
		'e25-realm-section',
		'e25-realm-base-section'
	];
	const DEFAULT_COMMON_FOLDER_PATH = 'src/common-components';
	const DEFAULT_THEME_FOLDER_PATH = '../../themes/berg-theme-child';
	protected static $common_folder_path = self::DEFAULT_COMMON_FOLDER_PATH;
	protected static $theme_folder_path = self::DEFAULT_THEME_FOLDER_PATH;
	protected static $package = null;
	protected static $root_package = null;
	protected static $realm_path = null;
	protected static $package_installed_path = null;
	protected static $override_excludes = [];

	/**
	 * Get common files of installed package
	 * @return array
	 */
	protected static function getPackageCommonFiles() : array
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
	protected static function getPackageThemeFiles() : array
	{
		$theme_files = [];
		$extra = self::$package->getExtra();
		if (array_key_exists('theme_files', $extra) && is_array($extra['theme_files'])) {
			$theme_files = $extra['theme_files'];
		}
		return $theme_files;
	}

	/**
	 * Extract composer.json extra values like common and theme folder paths from root package
	 * @return void
	 */
	protected static function extractExtras() : void
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
}
