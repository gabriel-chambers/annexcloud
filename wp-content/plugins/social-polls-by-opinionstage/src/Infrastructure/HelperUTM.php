<?php

namespace Opinionstage\Infrastructure;

defined( 'ABSPATH' ) || die();

class HelperUTM {
    /**
     * Returns UTM url
     *
     * @param string $path api path.
     * @param array  $query query args.
     * @return string
     */
    public static function utm_url( $path, $query = array() ) {
        return OPINIONSTAGE_SERVER_BASE . '/' . $path . '?' . http_build_query( array_merge( $query, OPINIONSTAGE_UTM_PARAMETERS ) );
    }

    /**
     * Generates a callback page URL, that is used to connect the plugin to the Opinion Stage account
     */
    public static function callback_url() {
        return menu_page_url( OPINIONSTAGE_LOGIN_CALLBACK_SLUG, false );
    }

    /**
     * Returns widget templates link
     *
     * @param string $type widget type.
     * @return string
     */
    public static function get_templates_url_for_type( $type ) {
        return add_query_arg( 'page', $type, OPINIONSTAGE_REDIRECT_TEMPLATES_API_UTM );
    }
}
