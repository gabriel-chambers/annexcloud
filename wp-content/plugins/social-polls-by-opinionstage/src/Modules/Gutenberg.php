<?php

namespace Opinionstage\Modules;

defined( 'ABSPATH' ) || die();

use Opinionstage;
use Opinionstage\Core\Module;
use Opinionstage\Infrastructure\Helper;


class Gutenberg {
    
    use Module;

    public function init() {
        add_action( 'init', [$this, 'register_gutenberg_assets'] );
        add_filter( 'block_categories_all', [$this, 'register_gutenberg_categories'] );
    }

    public function register_gutenberg_assets() {
        $script_asset_path = OPINIONSTAGE_PLUGIN_DIR . 'assets/gutenberg/build/index.asset.php';

        $script_asset = require $script_asset_path;

        $index_js                = 'assets/gutenberg/build/index.js';
        $index_js_dependencies   = $script_asset['dependencies'];
        $index_js_dependencies[] = Helper::get_asset_name( 'content-popup' );
        wp_register_script(
            'opinionstage-gutenberg-block-editor',
            Opinionstage::get_instance()->plugin_url . $index_js,
            $index_js_dependencies,
            $script_asset['version'],
            false
        );

        $editor_css = 'assets/gutenberg/build/index.css';
        wp_register_style(
            'opinionstage-gutenberg-block-editor',
            Opinionstage::get_instance()->plugin_url . $editor_css,
            [],
            filemtime( OPINIONSTAGE_PLUGIN_DIR . $editor_css ),
            false
        );

        $block_type_args = [
            'editor_script' => 'opinionstage-gutenberg-block-editor',
            'editor_style'  => 'opinionstage-gutenberg-block-editor',
        ];

        register_block_type( 'opinion-stage/block-os-poll', $block_type_args );
        register_block_type( 'opinion-stage/block-os-survey', $block_type_args );
        register_block_type( 'opinion-stage/block-os-trivia', $block_type_args );
        register_block_type( 'opinion-stage/block-os-personality', $block_type_args );

        $this->gutenberg_inject_data();
    }
    
    public function register_gutenberg_categories( $categories ) {
        return array_merge(
            $categories,
            [
                [
                    'slug'  => 'opinion-stage',
                    'title' => __( 'Poll, Survey, Quiz & Form By Opinion Stage', 'opinion-stage' ),
                ],
            ]
        );
    }
    
    private function gutenberg_inject_data() {
        wp_localize_script(
            'opinionstage-gutenberg-block-editor',
            'OPINIONSTAGE_GUTENBERG_DATA',
            [
                'userLoggedIn'       => Helper::is_user_logged_in(),
                'createNewWidgetUrl' => OPINIONSTAGE_REDIRECT_CREATE_WIDGET_API_UTM,
                'viewTemplateUrl'    => OPINIONSTAGE_REDIRECT_TEMPLATES_API_UTM,
                'loginPageUrl'       => get_admin_url( null, 'admin.php?page=' . OPINIONSTAGE_GETTING_STARTED_SLUG ),
                'OswpPluginVersion'  => OPINIONSTAGE_WIDGET_VERSION,
                'OswpClientToken'    => Helper::get_user_access_token(),
                'OswpFetchDataUrl'   => OPINIONSTAGE_CONTENT_POPUP_CLIENT_WIDGETS_API,
                'brandLogoUrl'       => Opinionstage::get_instance()->plugin_url . 'admin/images/os-logo.png',
            ]
        );
    }
}
