<?php

namespace Opinionstage\Core;

defined( 'ABSPATH' ) || die();

/**
 * Trait Module
 *
 * @package Opinionstage\Core
 */
trait Module {

    use Singleton;

    /**
     * Module constructor.
     */
    private function __construct() {
        $this->init();
    }

    /**
     * @return void
     */
    abstract public function init();
}