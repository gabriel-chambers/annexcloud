<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('berg_block_assets_custom')) {

    function berg_block_assets_custom()
    {
        $enqueue_styles_in_frontend = apply_filters('berg_enqueue_styles_custom', !is_admin());
        $enqueue_scripts_in_frontend = apply_filters('berg_enqueue_scripts_custom', !is_admin());

        // Frontend block styles.
        if (is_admin() || $enqueue_styles_in_frontend) {
            wp_enqueue_style(
                'e25m-style-css-custom',
                plugins_url('dist/frontend_blocks_styles.css', BERG_CUSTOM_FILE),
                array(),
                BERG_CUSTOM_VERSION
            );
        }

        // Frontend only scripts.
        if ($enqueue_scripts_in_frontend) {
            wp_enqueue_script(
                'e25m-block-frontend-js-custom',
                plugins_url('dist/frontend_blocks_scripts.js', BERG_CUSTOM_FILE),
				array('vendor-js', 'lodash', 'child-main-js'),
                BERG_CUSTOM_VERSION,
                true
            );
            // wp_localize_script('e25m-block-frontend-js-custom', 'berg', array(
            //     'restUrl' => get_rest_url(),
            // ));
        }
    }
    add_action('enqueue_block_assets', 'berg_block_assets_custom');
}

if (!function_exists('berg_block_editor_assets_custom')) {
    /**
     * Enqueue block assets for backend editor
     */
    function berg_block_editor_assets_custom()
    {
        // Backend editor scripts: common vendor files.
        wp_enqueue_script(
            'berg-block-js-vendor-custom',
            plugins_url('dist/common_vendor.js', BERG_CUSTOM_FILE),
            array(),
            BERG_CUSTOM_VERSION
        );

        // Backend editor scripts: blocks.
        $dependencies = array('lodash','berg-block-js-vendor-custom', 'code-editor', 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-util', 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-api', 'wp-polyfill', 'wp-server-side-render');
        wp_enqueue_script(
            'berg-block-js-custom',
            plugins_url('dist/editor_blocks_scripts.js', BERG_CUSTOM_FILE),
            // wp-util for wp.ajax.
            // wp-plugins & wp-edit-post for Gutenberg plugins.
            apply_filters('berg_custom_editor_blocks_dependencies', $dependencies),
            BERG_CUSTOM_VERSION
        );

        // Backend editor only styles.
        wp_enqueue_style(
            'berg-block-editor-css-custom',
            plugins_url('dist/editor_blocks_styles.css', BERG_CUSTOM_FILE),
            array('wp-edit-blocks'),
            BERG_CUSTOM_VERSION
        );
    }

    add_action('enqueue_block_editor_assets', 'berg_block_editor_assets_custom');
}

//post block and single post block modifications path
define('SINGLE_POST_BLOCK_ROOT', WP_PLUGIN_DIR . '/berg/src/block/single-post');
define('POST_BLOCK_ROOT', WP_PLUGIN_DIR . '/berg/src/block/post-block');

/**
 * Block Initializer.
 */

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/moving-logo-slider/moving-logo-slider.php')) {
    require_once plugin_dir_path(__FILE__) . 'src/block/moving-logo-slider/moving-logo-slider.php';
}

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/read-time/read-time.php')) {
    require_once plugin_dir_path(__FILE__) . 'src/block/read-time/read-time.php';
}

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/author/author.php')) {
    require_once plugin_dir_path(__FILE__) . 'src/block/author/author.php';
}

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/post-block-modified/post-block-modified.php')){
	require_once plugin_dir_path(__FILE__) . 'src/block/post-block-modified/post-block-modified.php';
}

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/single-post-modified/single-post-modified.php')) {
	require_once plugin_dir_path(__FILE__) . 'src/block/single-post-modified/single-post-modified.php';
}

if (file_exists(plugin_dir_path(__FILE__) . 'src/block/media-elements/init.php')) {
	require_once plugin_dir_path(__FILE__) . 'src/block/media-elements/init.php';
}
