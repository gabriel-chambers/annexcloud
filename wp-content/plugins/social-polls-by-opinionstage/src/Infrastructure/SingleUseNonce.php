<?php

namespace Opinionstage\Infrastructure;

defined( 'ABSPATH' ) || die();

class SingleUseNonce {
    private static $transient_name = 'opinionstage_login_nonce';
    private static $nonce_get_param = 'opinionstage_login_nonce';
    private static $nonce_lifetime = 3600;

    private static function create_nonce() {
        $nonce_hash = bin2hex( random_bytes( 32 ) );

        if ( set_transient( self::$transient_name, $nonce_hash, self::$nonce_lifetime ) ) {
            return $nonce_hash;
        }
        return false;
    }

    private static function validate_nonce($nonce_hash_url) {
        $nonce_data = get_transient( self::$transient_name );

        if ( !$nonce_data ) {
            return false;
        }

        return $nonce_hash_url === $nonce_data;
    }

    public static function is_valid_callback() {
        if ( empty( $_GET[self::$nonce_get_param] ) ) {
            return;
        }

        $is_valid = self::validate_nonce( sanitize_text_field($_GET[self::$nonce_get_param] ) );
        if ( $is_valid ) {
            self::delete_nonce();
            return true;
        }
        return false;
    }

    public static function add_nonce_to_url($url) {
        self::delete_nonce();
        $nonce = self::create_nonce();
        if ( !$nonce ) {
            return null;
        }
        return add_query_arg( self::$nonce_get_param, $nonce, $url );
    }

    private static function delete_nonce() {
        return delete_transient( self::$transient_name );
    }
}
