<?php
/* Pre-loading stylesheets to avoid render blocking */
function add_rel_preload($html, $handle, $href, $media)
{
    if (is_admin()) {
        return $html;
    }
    return '<link rel="stylesheet preload" as="style"
        id="' . $handle . '-css"
        href="' . $href . '"
        media="' . $media . '">';
}
add_filter('style_loader_tag', 'add_rel_preload', 10, 4);

/* Moving jquery libraries to be loaded from footer to avoid render blocking */
function move_jquery_into_footer()
{
    // Moving jquery to footer only when the page doesn't have a post block.
    if (is_admin() || has_block('e25m/post-block')) {
        return;
    }
    wp_scripts()->add_data('jquery', 'group', 1);
    wp_scripts()->add_data('jquery-core', 'group', 1);
    wp_scripts()->add_data('jquery-migrate', 'group', 1);
}
add_action('wp_enqueue_scripts', 'move_jquery_into_footer');

/* Removing unused css files from the home page */
add_action('wp_enqueue_scripts', 'dequeue_unwanted_css', 1002);
function dequeue_unwanted_css()
{
    $style_handles = apply_filters('remove_unused_css_by_handle', array());
    if (is_front_page()) {
        if (!empty($style_handles)) {
            foreach ($style_handles as $handle) {
                wp_deregister_style($handle);
            }
        }
        if (!is_user_logged_in()) {
            wp_deregister_style('dashicons');
            wp_deregister_style('admin-bar');
        }
    }
}

/* Processes the "img" tag that's included in <picture> tags */
add_filter('rocket_specify_dimension_skip_pictures', '__return_false');

/* Enable setting image dimensions for external images */
add_filter('rocket_specify_image_dimensions_for_distant', '__return_true');
