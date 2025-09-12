<?php

/**
 * Adds Berg classic theme settings to "Writing" section of the Wordpress settings
 */
function add_berg_classic_theme_settings_to_writing_settings()
{
    add_settings_field(
        'berg_classic_theme_enabled',
        __('Berg Classic Theme For Editor', 'berg_classic_editor_theme_option'),
        'berg_classic_theme_settings_callback',
        'writing',
        'default',
        ['value' => get_option('berg_classic_theme_enabled', true)]
    );
    register_setting(
        'writing', 
        'berg_classic_theme_enabled',
        ['type' => 'boolean', 'default' => true]
    );
}
add_action('admin_init', 'add_berg_classic_theme_settings_to_writing_settings');

/**
 * Render Berg classic theme settings to "Writing" section of the Wordpress settings
 */
function berg_classic_theme_settings_callback($args)
{
    $current_value = $args['value'];
    $enabled = boolval($current_value);
    include __DIR__ . '/view_setting.php';
}

function get_classic_theme_eligible_post_types()
{
    $post_types = get_post_types();
    return array_filter($post_types, function ($post_type) {
        return !in_array($post_type, [
                'attachment', 
                'nav_menu_item', 
                'custom_css', 
                'customize_changeset', 
                'oembed_cache', 
                'user_request'
            ]);
    }, ARRAY_FILTER_USE_KEY);
}

/**
 * Register post meta to store post wise settings
 */
function register_berg_classic_theme_meta_box_for_post_editor()
{
    $post_types = get_classic_theme_eligible_post_types();
    foreach ($post_types as $post_type) {
        register_post_meta(
            $post_type, 
            'berg-classic-theme-enabled', 
            ['type' => 'boolean', 'default' => true]
        );
    }
}
add_action('init', 'register_berg_classic_theme_meta_box_for_post_editor');

/**
 * Adds HTML inputs to enable/disable classic theme from editor
 */
function add_berg_classic_theme_post_meta_fields_to_screens()
{
    $post_types = get_classic_theme_eligible_post_types();
    add_meta_box(
        'berg_classic_theme_enabled_on_post',
        'Editor Mode',
        'berg_classic_theme_posts_callback',
        $post_types,
        'side',
        'low'
    );
}
add_action('add_meta_boxes', 'add_berg_classic_theme_post_meta_fields_to_screens');

/**
 * Render inputs on post editor side bar
 */
function berg_classic_theme_posts_callback($post)
{
    $enabled = classic_theme_enable_on_post($post->ID);
    include __DIR__ . '/view_post_setting.php';
}

/**
 * Update post meta value on submit
 */
function save_berg_classic_theme_enabled_for_post($post_id)
{
    if (!isset($_POST['berg_classic_theme_enabled_on_post'])) {
        return;
    }

    $value = boolval(sanitize_text_field($_POST['berg_classic_theme_enabled_on_post']));
    update_post_meta($post_id, 'berg_classic_theme_enabled_on_post', $value);
}
add_action('save_post', 'save_berg_classic_theme_enabled_for_post');

/**
 * Get whether the classic theme is enabled on post or not
 */
function classic_theme_enable_on_post($post_id)
{
    $global_setting_value = get_option('berg_classic_theme_enabled', true);
    // takes value as an error otherwise "get_post_meta" returns false if not available
    $meta_values = get_post_meta($post_id, 'berg_classic_theme_enabled_on_post', false);
    $enabled_on_post = array_shift($meta_values);
    return is_null($enabled_on_post)
        ? boolval($global_setting_value) 
        : boolval($enabled_on_post);
}

/**
 * Berg classic theme - add 'berg-classic-theme' class to body tag if enable the classic mode
 * @param $classes
 * @return mixed|string
 */
function berg_classic_theme_add_body_class($classes)
{
    //get current page
    global $pagenow;
    global $post;

    if ($post != null && (($pagenow === 'post.php' && isset($_GET['post'])) || ($pagenow === 'post-new.php'))) {
        $post_id = $post->ID;
        //check if the current page is post.php or post-new.php AND check is classic theme enable
        if (classic_theme_enable_on_post($post_id)) {
            $classes .= ' berg-classic-theme';
            return $classes;
        }
    }

    return $classes;
}
add_filter('admin_body_class', 'berg_classic_theme_add_body_class');

/**
 * @param $classes
 * @return array
 */
function berg_classic_theme_remove_body_class($classes)
{
    //get current page
    global $pagenow;
    global $post;

    if ($post != null && (($pagenow === 'post.php' && isset($_GET['post'])) || ($pagenow === 'post-new.php'))) {
        $post_id = $post->ID;
        if (!classic_theme_enable_on_post($post_id)) {
            $remove_classes = ['berg-classic-theme'];
            $classes = array_diff($classes, $remove_classes);
        }
    }

    return $classes;
}
add_filter('body_class', 'berg_classic_theme_remove_body_class');
