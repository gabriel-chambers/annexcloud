<?php

namespace E25m\Realm\Composer;

use E25m\Realm\Composer\Installer;
use Composer\Script\Event;
use E25m\Realm\Composer\Local\Factories\PackageFactory;
use E25m\Realm\Composer\Local\PackageManager;

class SetupRealm extends Installer
{    
        
    /**
     * Link section files to the theme using composer script event
     *
     * @param  mixed $event
     * @return void
     */
    public static function linkFilesComposer(Event $event)
    {
        $composer = $event->getComposer();
        $installed_packages = $composer->getRepositoryManager()->getLocalRepository()->getPackages();

        foreach ($installed_packages as $package) {
            // set package
            self::$package = $package;
            self::$root_package = $package;

            if (!in_array(self::$package->getType(), self::REALM_PACKAGE_TYPES)) {
                continue;
            }

            self::extractExtras();
            self::$realm_path = getcwd();

            if (file_exists(self::$realm_path . '/wp-content')) {
                self::$realm_path = self::$realm_path . '/wp-content/plugins/realm';
            }

            $installation_manager = $composer->getInstallationManager();
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
    }

        
    /**
     * Manually link section files to the theme.
     *
     * @return void
     */
    public static function linkFilesManual()
    {
        // set package factory
        PackageManager::setPackageFactory(new PackageFactory);

        $installed_packages = PackageManager::getPackages();

        foreach ($installed_packages as $package) {
            // set package
            self::$package = $package;
            self::$root_package = $package;

            if (!in_array(self::$package->getType(), self::REALM_PACKAGE_TYPES)) {
                continue;
            }

            self::extractExtras();
            self::$realm_path = getcwd();

            if (file_exists(self::$realm_path . '/wp-content')) {
                self::$realm_path = self::$realm_path . '/wp-content/plugins/realm';
            }

            self::$package_installed_path = PackageManager::getInstallPath(self::$package);

            $common_files = self::getPackageCommonFiles(self::$package);
            $theme_files = self::getPackageThemeFiles(self::$package);

            if (sizeof($common_files) > 0) {
                self::moveCommonFiles($common_files);
            }
            if (sizeof($theme_files) > 0) {
                self::moveThemeFiles($theme_files);
            }
        }
    }
}
