<?php

namespace E25m\Realm\Composer\Local;

use Generator;
use E25m\Realm\Composer\Local\Interfaces\PackageFactoryInterface;
use E25m\Realm\Composer\Local\Interfaces\PackageInterface;

class PackageManager
{
    protected static $package_factory;
    const INSTALLATION_PATH = __DIR__ . '/../../../src/sections';

    /**
     * Set package factory
     *
     * @param PackageFactoryInterface $package_factory
     * @return void
     */
    public static function setPackageFactory(PackageFactoryInterface $package_factory): void
    {
        self::$package_factory = $package_factory;
    }

    /**
     * Get the list of installed packages
     *
     * @return Generator
     */
    public static function getPackages(): Generator
    {
        $package_directory = self::INSTALLATION_PATH . '/*';

        foreach (glob($package_directory, GLOB_ONLYDIR) as $package_path) {
            yield self::$package_factory->create($package_path);
        }
    }

    /**
     * Get the package installation path
     *
     * @param PackageInterface $package
     * @return String
     */
    public static function getInstallPath(PackageInterface $package): String
    {
        return $package->getPath();
    }
}
