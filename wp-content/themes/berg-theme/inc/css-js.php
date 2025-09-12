<?php

/** calling for theme stylesheets **/
function e25b_assests()
{
    // vendor-styles
    wp_enqueue_style('vendor-styles', get_template_directory_uri() . '/dist/css/vendor.css', array());
    // vendor
    wp_enqueue_script('vendor-js', get_template_directory_uri() . '/dist/js/vendor.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'e25b_assests', 1000);

/**
 * Registers an editor stylesheet for the theme.
 */
function wpdocs_theme_add_editor_styles()
{
    add_editor_style('/dist/css/vendor.css');
    add_editor_style('/dist/css/style.css');
}
//Comment because of classic views
//add_action('admin_init', 'wpdocs_theme_add_editor_styles');

/**
 * load bootstrap js on page/post edit view
 * @param $hook
 */
function add_admin_scripts($hook)
{
    if ($hook == 'post-new.php' || $hook == 'post.php') {
        wp_enqueue_script('vendor_js', get_template_directory_uri() . '/dist/js/vendor.js', array('jquery'), '1.0');
        wp_enqueue_script('admin_scripts', get_template_directory_uri() . '/dist/js/admin_scripts.js', array('jquery'), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'add_admin_scripts', 10, 1);
