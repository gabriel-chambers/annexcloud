<?php

namespace Opinionstage\Modules;

defined( 'ABSPATH' ) || die();

use Opinionstage;
use Opinionstage\Core\Module;
use Opinionstage\Infrastructure\Helper;

define( 'OPINIONSTAGE_POLL_SHORTCODE', 'socialpoll' );
define( 'OPINIONSTAGE_WIDGET_SHORTCODE', 'os-widget' );

class Shortcodes {
    const POLL_SHORTCODE = 'socialpoll';
    const WIDGET_SHORTCODE = 'os-widget';

    use Module;

    public function init() {
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_assets'] );
        add_action('wp_head', [$this, 'maybe_add_flyout']);

        add_shortcode(self::POLL_SHORTCODE, [__CLASS__, 'poll']);
        add_shortcode(self::WIDGET_SHORTCODE, [__CLASS__, 'os_widget']);
    }

    public static function poll( $atts ) {

        if ( is_feed() ) {
            return __( "Note: There is a poll embedded within this post, please visit the site to participate in this post's poll.", 'social-polls-by-opinionstage' );
        }

        $shortcode_params = shortcode_atts(
            [
                'id' => 0,
                'type' => 'poll',
                'width' => '',
            ],
            $atts,
            self::POLL_SHORTCODE
        );

        $id    = intval( $shortcode_params['id'] );
        $type  = $shortcode_params['type'];
        $width = $shortcode_params['width'];

        return self::widget_embedded( self::poll_or_set_embed_code_url( $id, $type, $width ) );
    }

    public static function os_widget( $atts ) {
        if ( is_feed() ) {
            return __( "Note: There is a widget embedded within this post, please visit the site to participate in this post's widget.", 'social-polls-by-opinionstage' );
        }

        $shortcode_params = shortcode_atts(
            [
                'path' => 0,
                'width' => '',
            ],
            $atts,
            self::WIDGET_SHORTCODE
        );

        $path            = $shortcode_params['path'];
        $width           = $shortcode_params['width'];

        return self::widget_embedded( self::widget_embed_code_url( $path, $width ) );
    }

    private static function widget_embed_code_url( $path, $width ) {
        if ( ! empty( $path ) ) {
            return OPINIONSTAGE_API_PATH . '/widgets' . $path . '/code.json?width=' . $width;
        }
    }

    private static function poll_or_set_embed_code_url( $id, $type, $width ) {
        if ( $type == 'set' ) {
            $embed_code_url = OPINIONSTAGE_API_PATH . '/sets/' . $id . '/code.json';
        } else {
            $embed_code_url = OPINIONSTAGE_API_PATH . '/polls/' . $id . '/code.json?width=' . $width;
        }

        if ( is_home() ) {
            $embed_code_url .= '?h=1';
        }

        return $embed_code_url;
    }

    private static function widget_embedded( $url ) {
        return sprintf( '<div data-opinionstage-embed-url="%s" style="display: none; visibility: hidden;"></div>', esc_url( $url ) );
    }

    public function enqueue_assets() {

        wp_enqueue_script(
            Helper::get_asset_name( 'shortcodes' ),
            Opinionstage::get_instance()->plugin_url . 'assets/js/shortcodes.js',
            ['jquery'],
            OPINIONSTAGE_WIDGET_VERSION,
            true
        );
    }


    public function maybe_add_flyout() {
        if( is_admin() ) {
            return;
        }

        $os_options = (array) get_option(OPINIONSTAGE_OPTIONS_KEY);

        if (!empty($os_options['fly_id']) && $os_options['fly_out_active'] == 'true' ) {
            ?>
            <script type="text/javascript">//<![CDATA[
              window.AutoEngageSettings = {
                id : '<?php echo $os_options['fly_id']; ?>'
              };
              (function(d, s, id){
                var js,
                  fjs = d.getElementsByTagName(s)[0],
                  r = Math.floor(new Date().getTime() / 1000000);
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id; js.async=1;
                js.src = '<?php echo OPINIONSTAGE_SERVER_BASE; ?>' + '/assets/autoengage.js?' + r;
                fjs.parentNode.insertBefore(js, fjs);
              }(document, 'script', 'os-jssdk'));
              //]]></script>
            <?php
        }
    }

}
