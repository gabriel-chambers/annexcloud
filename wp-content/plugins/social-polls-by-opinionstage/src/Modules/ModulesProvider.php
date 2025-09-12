<?php

namespace Opinionstage\Modules;

defined( 'ABSPATH' ) || die();

use Opinionstage\Core\Provider;

/**
 * Class ModulesProvider
 *
 * @package Opinionstage\Modules
 */
final class ModulesProvider {

    use Provider;

    /**
     * Instantiate modules
     *
     * @return array
     */
    protected function get_modules() {
        return [
            Shortcodes::class,
            Admin::class,
            Gutenberg::class,
        ];
    }
}
