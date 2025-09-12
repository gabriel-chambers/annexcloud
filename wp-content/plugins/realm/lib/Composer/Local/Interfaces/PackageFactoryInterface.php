<?php

namespace E25m\Realm\Composer\Local\Interfaces;

use E25m\Realm\Composer\Local\Interfaces\PackageInterface;

interface PackageFactoryInterface
{
    /**
     * create
     *
     * @param  String $path
     * @return PackageInterface
     */
    public function create(String $path): PackageInterface;
}
