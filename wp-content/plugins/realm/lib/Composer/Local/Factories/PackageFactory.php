<?php

namespace E25m\Realm\Composer\Local\Factories;

use E25m\Realm\Composer\Local\Package;
use E25m\Realm\Composer\Local\Interfaces\PackageFactoryInterface;
use E25m\Realm\Composer\Local\Interfaces\PackageInterface;

class PackageFactory implements PackageFactoryInterface
{

    /**
     * create and return a package instance
     *
     * @param String $package_path
     * @return PackageInterface
     */
    public function create(String $package_path): PackageInterface
    {
        return new Package($package_path);
    }
}
