<?php

namespace Opinionstage\Infrastructure;

use Opinionstage;

class TemplatesViewer {

    public static function require_template($template_name, $args = []) {
        $path = Opinionstage::get_instance()->plugin_path . $template_name . '.php';

        if( ! file_exists( $path ) ) {
            return;
        }

        extract($args);
        require( $path );
    }
}
