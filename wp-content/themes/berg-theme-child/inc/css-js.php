<?php

/** calling for child theme stylesheets and scripts **/
function e25b_child_theme_assets()
{
    // core-component styles
    wp_enqueue_style('child-core-styles', get_stylesheet_directory_uri() . '/dist/css/core-components.css', array());

    // common styles
    wp_enqueue_style('child-common-styles', get_stylesheet_directory_uri() . '/dist/css/style.css', array());

    wp_enqueue_script(
        'child-fancybox-js', get_stylesheet_directory_uri() . '/dist/js/fancybox.js',
        array('jquery', 'lodash'),
        null,
        true
    );

    // main
    wp_enqueue_script(
        'child-main-js',
        get_stylesheet_directory_uri() . '/dist/js/main.js',
        array('jquery', 'lodash', 'child-fancybox-js'),
        null,
        true
    );

    $current_page_id = get_the_ID();

    if (has_block('e25m/counter', $current_page_id)) {
        wp_enqueue_script(
            'child-counterup-js',
            get_stylesheet_directory_uri() . '/dist/js/counterup.js',
            array('jquery', 'lodash'),
            null,
            true
        );
    }

    if (has_block('e25m/lottie-animator', $current_page_id)) {
        wp_enqueue_script(
            'child-lottie-js',
            get_stylesheet_directory_uri() . '/dist/js/lottie.js',
            array('jquery', 'lodash'),
            null,
            true
        );
    }

    if (has_block('e25m/post-block', $current_page_id)) {
        wp_enqueue_script(
            'child-select2-js',
            get_stylesheet_directory_uri() . '/dist/js/select2.js',
            array('jquery', 'lodash'),
            null,
            true
        );
    }

    if (
        has_block('e25m/slider-v2', $current_page_id)
        || has_block('e25m/tab-slider-v2', $current_page_id)
        || has_block('e25m/slider-accordion', $current_page_id)
        || has_block('e25m/logo-slider', $current_page_id)
        || has_block('e25m/post-block', $current_page_id)
    ) {
        wp_enqueue_script(
            'child-slick-js',
            get_stylesheet_directory_uri() . '/dist/js/slick.js',
            array('jquery', 'lodash'),
            null,
            true
        );
    }

}
add_action('wp_enqueue_scripts', 'e25b_child_theme_assets', 1001);

/**
 * Registers an editor stylesheet for the theme.
 */
function e25_child_theme_add_editor_styles()
{
    wp_enqueue_style('admin-styles', get_stylesheet_directory_uri() . '/dist/css/admin-styles.css', array());
    add_editor_style('/dist/css/editor-styles.css');
}
add_action('admin_init', 'e25_child_theme_add_editor_styles');

/**
 * Registers an admin javascript for the theme.
 */
function e25_child_theme_add_admin_scripts()
{
    wp_enqueue_script(
        'child_admin_scripts',
        get_stylesheet_directory_uri() . '/dist/js/admin_scripts.js',
        array('jquery'),
        '1.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'e25_child_theme_add_admin_scripts', 10, 1);

/**
 * Enqueue Swagger assets for API Documentation
 *
 * @return void
 */
function e25b_child_theme_swagger_ui_assets()
{
    global $wp;
    $api_documentation_path_pattern = defined('SWAGGER_API_DOC_PATH_PATTERN')
        ? constant('SWAGGER_API_DOC_PATH_PATTERN')
        : '/^api-documentation-?.*$/';
    $swagger_ui_styles = [
        '/third-party-assets/swagger-ui/css/swagger-ui.css'
    ];
    $swagger_ui_scripts = [
        '/third-party-assets/swagger-ui/js/swagger-ui-bundle.js',
        '/third-party-assets/swagger-ui/js/swagger-ui-standalone-preset.js'
    ];
    preg_match($api_documentation_path_pattern, $wp->request, $path_matches);
    $is_a_swagger_file = isset($path_matches[0]) && !empty($path_matches[0]);

    if ($is_a_swagger_file) {
        foreach ($swagger_ui_styles as $key => $style) {
            if (file_exists(get_stylesheet_directory() . $style)
            ) {
                wp_enqueue_style(
                    "swagger-ui-styles-{$key}",
                    get_stylesheet_directory_uri() . $style
                );
            }
        }
        foreach ($swagger_ui_scripts as $key => $script) {
            if (file_exists(get_stylesheet_directory() . $script)) {
                wp_enqueue_script(
                    "swagger-ui-scripts-{$key}",
                    get_stylesheet_directory_uri() . $script,
                    [],
                    false
                );
            }
        }
    }

}
add_action('wp_enqueue_scripts', 'e25b_child_theme_swagger_ui_assets');
