<?php

namespace E25m\Realm\Composer\Local\Interfaces;

interface PackageInterface
{
    /**
     * getName
     *
     * @return String
     */
    public function getName(): String;

    /**
     * getType
     *
     * @return String
     */
    public function getType(): String;

    /**
     * getPath
     *
     * @return String
     */
    public function getPath(): String;

    /**
     * getExtra
     *
     * @return array
     */
    public function getExtra(): array;

    /**
     * processPackage
     *
     * @return void
     */
    public function processPackage(): void;
}
