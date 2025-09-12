<?php

/**
 * Plugin Name:       Activity Log
 * Plugin URI:        https://www.eight25media.com/
 * Description:       Logs user actions on Activity-Log Dashboard.
 * Version:           1.0
 * Author:            E25
 * Author URI:        https://www.eight25media.com/
 */

require_once 'class-e25-al-log.php';

defined('ACTIVITY_LOG_PLUGIN_ROOT_PATH') || define('ACTIVITY_LOG_PLUGIN_ROOT_PATH', plugin_dir_path(__FILE__));

// plugin constants
define('E25_AL_PLUGIN', 'Plugin');

// hides the plugin from plugin list if hide constant is defined
add_filter('all_plugins', 'e25_activity_log_hide');
function e25_activity_log_hide($plugins_array)
{
    if (defined('E25_ACTIVITY_LOG_HIDDEN') && E25_ACTIVITY_LOG_HIDDEN) {
        return array_filter($plugins_array, function (string $key) {
            return !str_contains($key, 'activity-log');
        }, ARRAY_FILTER_USE_KEY);
    }

    return $plugins_array;
}

// Enable revisions
add_filter('wp_revisions_to_keep', 'e25_activity_log_enable_post_revisions');
function e25_activity_log_enable_post_revisions()
{
    return 10; // Set the number of revisions to keep as desired
}

define('E25_AL_USER', 'User');
///////////////////////////////////////////////////////////////////////////////////////////////
////////////////////USER - login/logout events/////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_login', 'e25_activity_log_hooks_user_login', 10, 2);
add_action('wp_logout', 'e25_activity_log_hooks_user_logout');

function e25_activity_log_hooks_user_login(string $user_login, WP_User $user)
{
    $post_type = E25_AL_USER;
    $action = 'User logged in';

    $log_data = new E25_AL_Log($post_type, $action, $user_login, $user_login);
    $log_data->post_link = get_edit_user_link();
    $log_data->user = $user;

    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_hooks_user_logout(int $user_id)
{
    $user = get_userdata($user_id);
    $user_login = $user->user_login;
    $action = 'User logged out';

    $log_data = new E25_AL_Log(E25_AL_USER, $action, $user_login, $user_login);
    $log_data->post_link = get_edit_user_link($user_id);
    $log_data->user = $user;

    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////USER - register, profile update, and delete events/////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('user_register', 'e25_activity_log_hooks_log_user_activity');
add_action('profile_update', 'e25_activity_log_hooks_log_user_activity');
add_action('delete_user', 'e25_activity_log_hooks_log_user_activity');

function e25_activity_log_hooks_log_user_activity($user_id)
{
    $user = get_userdata($user_id);
    $title = $user->user_login;
    $action = '';

    if (current_filter() === 'user_register') {
        $action = 'Created';
    } elseif (current_filter() === 'profile_update') {
        $action = 'Updated';
    } elseif (current_filter() === 'delete_user') {
        $action = 'Deleted';
    }

    $action .= " - $title";

    $user_login = $user->user_login;
    $log_data = new E25_AL_Log(E25_AL_USER, $action, $user_login, $user_login);
    $log_data->post_link = get_edit_user_link($user_id);
    $log_data->user = $user;

    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////USER ROLE CHANGE///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('set_user_role', 'e25_activity_log_hooks_log_user_role_change', 10, 3);

function e25_activity_log_hooks_log_user_role_change($user_id, $new_role, $old_roles)
{
    $user = get_userdata($user_id);
    $title = $user->user_login;
    $old_roles_value = empty($old_roles) ? '' : ' ' . implode(', ', $old_roles);
    $action = empty($old_roles) ? "Added role '{$new_role}' to user: {$title}"
        : "Changed role from{$old_roles_value} to '{$new_role}'";

    $log_data = new E25_AL_Log(E25_AL_USER, $action, $title, $user_id);
    $log_data->post_link = get_edit_user_link($user_id);
    $log_data->user = $user;

    $log_data->content = array(
        'user_id' => $user_id,
        'new_role' => $new_role,
        'old_roles' => $old_roles,
    );

    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////POST & PAGE//////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('post_updated', 'e25_activity_log_post_updated', 10, 3);
add_action('delete_post', 'e25_activity_log_post_deleted', 10, 2);
add_action('transition_post_status', 'e25_activity_log_post_status', 10, 3);

function e25_activity_log_post_updated($post_id, $post_after, $post_before)
{
    if (
        get_transient("e25_activity_log_post_updated_ran_for_$post_id") ||
        wp_is_post_revision($post_id)
    ) {
        return;
    }

    // This is an auto-save, skip it
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $post_type = get_post_type($post_id);
    if (!e25_activity_log_is_content_post_type($post_type)) {
        return;
    }
    $action = e25_activity_log_post_diffs($post_before, $post_after);

    $log_data = new E25_AL_Log($post_type, $action, $post_after->post_title, $post_id);
    $log_data->post_link = get_edit_post_link($post_id);

    $log_data->content = array(
        'post_id' => $post_id,
        'post_before' => $post_before,
        'post_after' => $post_after,
    );

    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_post_updated_ran_for_$post_id", true, 4);
}

function e25_activity_log_post_deleted($post_id, WP_Post $post)
{
    if (get_transient("e25_activity_log_post_deleted_ran_for_$post_id")) {
        return;
    }

    $post_type = get_post_type($post_id);
    if (!e25_activity_log_is_content_post_type($post_type)) {
        return;
    }

    $action = "Post with postID {$post_id} deleted";
    $title = get_post_field('post_title', $post);

    $log_data = new E25_AL_Log($post_type, $action, $title, $post_id);
    $log_data->post_link = get_edit_post_link($post_id);
    $log_data->content = array('deleted_post' => $post);

    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_post_deleted_ran_for_$post_id", true, 4);
}

function e25_activity_log_post_status($new_status, $old_status, WP_Post $post)
{
    $post_id = $post->post_id;
    if (get_transient("e25_activity_log_post_status_ran_for_$post_id")) {
        return;
    }

    $post_type = get_post_type($post_id);
    if (!e25_activity_log_is_content_post_type($post_type)) {
        return;
    }

    if ($new_status === 'trash') {
        $action = 'Deleted';
    } elseif ($old_status === 'trash') {
        $action = 'Restored';
    } else {
        return;
    }

    $log_data = new E25_AL_Log($post_type, $action, get_post_field('post_title', $post), $post_id);
    $log_data->post_link = get_edit_post_link($post_id);
    $log_data->content = array(
        'old_status' => $old_status,
        'new_status' => $new_status,
        'post' => $post
    );
    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_post_status_ran_for_$post_id", true, 4);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////THEME - install, activate, and delete events//////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_filter('wp_redirect', 'e25_activity_log_hooks_theme_modify');
add_action('switch_theme', 'e25_activity_log_hooks_switch_theme', 10, 3);
add_action('delete_site_transient_update_themes', 'e25_activity_log_hooks_theme_deleted');

// Theme customizer
add_action('customize_save', 'e25_activity_log_hooks_theme_customizer_modified');

define('E25_AL_THEME', 'Theme');

function e25_activity_log_hooks_theme_modify($location)
{
    if (strpos($location, 'theme-editor.php') !== false && !empty($_POST) && $_POST['action'] === 'update') {
        $action = 'File updated';
        $post_type = E25_AL_THEME;
        $title = 'file unknown';

        if (!empty($_POST['file'])) {
            $title = $_POST['file'];
        }

        $log_data = new E25_AL_Log($post_type, $action, $title);
        $log_data->post_link = admin_url('theme-editor.php');
        e25_record_activity_log($log_data, __FUNCTION__);
    }
    // need to return the value to complete the filter.
    return $location;
}

function e25_activity_log_hooks_switch_theme($new_name, WP_Theme $new_theme, WP_Theme $old_theme)
{
    $action = "Theme switched";
    $action .= $old_theme->display('Name') ? " from {$old_theme->display('Name')}" : '';
    $action .= $new_theme->display('Name') ? " to {$new_theme->display('Name')}" : '';

    $post_type = E25_AL_THEME;

    $log_data = new E25_AL_Log($post_type, $action, $new_name);
    $log_data->post_link = $new_theme->display('ThemeURI');
    $log_data->content = array(
        'old_theme' => $old_theme,
        'new_theme' => $new_theme,
    );

    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_hooks_theme_customizer_modified(WP_Customize_Manager $obj)
{
    $action = 'Updated';
    $post_type = 'Theme Customizer';
    $theme = $obj->theme();
    $title = $theme->display('Name');

    if (current_filter() === 'customize_preview_init') {
        $action = 'Accessed';
    }

    $log_data = new E25_AL_Log($post_type, $action, $title);
    $log_data->post_link = $theme->display('ThemeURI');

    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_hooks_theme_deleted()
{
    $backtrace_history = debug_backtrace();

    $delete_theme_call = null;
    foreach ($backtrace_history as $call) {
        if (isset($call['function']) && 'delete_theme' === $call['function']) {
            $delete_theme_call = $call;
            break;
        }
    }

    if (empty($delete_theme_call)) {
        return;
    }

    $name = $delete_theme_call['args'][0];

    $action = 'Deleted';
    $post_type = E25_AL_THEME;

    $log_data = new E25_AL_Log($post_type, $action, $name);

    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////PLUGIN - plugin activate, deactivate, install, update, and delete events//////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('upgrader_process_complete', 'e25_activity_log_hooks_plugin_update', 10, 2);
add_action('upgrader_process_complete', 'e25_activity_log_hooks_plugin_install', 10, 2);
add_action('activated_plugin', 'e25_activity_log_hooks_plugin_event');
add_action('deactivated_plugin', 'e25_activity_log_hooks_plugin_event');
add_action('delete_plugin', 'e25_activity_log_hooks_plugin_event');

// Log plugin events
function e25_activity_log_hooks_plugin_event($plugin)
{
    $plugin_path = plugin_dir_path($plugin);
    $plugin_slug = plugin_basename($plugin);

    $plugin_id = $plugin_path . $plugin_slug;
    if (get_transient("e25_activity_log_hooks_plugin_event_ran_for_$plugin_id")) {
        return;
    }

    $plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_slug);
    $id = isset($plugin_info['TextDomain']) ? $plugin_info['TextDomain'] : '';
    $post_type = E25_AL_PLUGIN;
    $title = $plugin_info['Name'] ?? '';

    $action = '';
    if (current_filter() === 'activated_plugin') {
        $action = 'Activated';
    } elseif (current_filter() === 'deactivated_plugin') {
        $action = 'Deactivated';
    } elseif (current_filter() === 'delete_plugin') {
        $action = 'Deleted';
    }

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->content = array('plugin' => $plugin);
    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_hooks_plugin_event_ran_for_$plugin_id", true, 1);
}

function e25_activity_log_hooks_plugin_install($upgrader, $extra)
{
    if (!isset($extra['type']) || $extra['type'] !== 'plugin') {
        return;
    }

    $extra_action = $extra['action'];
    if ($extra_action !== 'install') {
        return;
    }

    $path = $upgrader->plugin_info();
    if (!$path) {
        return;
    }

    $data = get_plugin_data($upgrader->skin->result['local_destination'] . '/' . $path, true, false);

    $id = $data['Version'];
    $post_type = E25_AL_PLUGIN;
    $title = $data['Name'];
    $action = "Installed plugin '$title', version - $id";

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $path;
    $log_data->content = array('pluginExtra' => $extra);

    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_hooks_plugin_update($upgrader, $extra)
{
    if (!isset($extra['type']) || $extra['type'] !== 'plugin') {
        return;
    }

    $extra_action = $extra['action'];
    if ($extra_action !== 'update') {
        return;
    }

    if (isset($extra['bulk']) && $extra['bulk']) {
        $slugs = $extra['plugins'];
    } else {
        $plugin_slug = isset($upgrader->skin->plugin) ? $upgrader->skin->plugin : $extra['plugin'];

        if (empty($plugin_slug)) {
            return;
        }

        $slugs = array($plugin_slug);
    }

    foreach ($slugs as $slug) {
        $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $slug, true, false);

        $id = $data['Version'];
        $post_type = E25_AL_PLUGIN;
        $title = $data['Name'];
        $action = "Updated plugin '$title', version - $id";
        $post_link = $upgrader->plugin_info();
    }

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    $log_data->content = array('pluginExtra' => $extra);

    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////TAXONOMY//////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('created_term', 'e25_activity_log_hooks_created_edited_deleted_term', 10, 3);
add_action('edited_term', 'e25_activity_log_hooks_created_edited_deleted_term', 10, 3);
add_action('delete_term', 'e25_activity_log_hooks_created_edited_deleted_term', 10, 4);

function e25_activity_log_hooks_created_edited_deleted_term($term_id, $tt_id, $taxonomy, $deleted_term = null)
{
    // Make sure do not action nav menu taxonomy.
    $action = '';
    if ('nav_menu' === $taxonomy) {
        return;
    }

    if ('delete_term' === current_filter()) {
        $term = $deleted_term;
    } else {
        $term = get_term($term_id, $taxonomy);
    }

    $term_link = '_';
    if ($term && !is_wp_error($term)) {
        if ('edited_term' === current_filter()) {
            $action = 'Updated';
            $term_link = get_edit_term_link($term_id, $taxonomy);
        } elseif ('delete_term' === current_filter()) {
            $action  = 'Deleted';
        } else {
            $action = 'Created';
            $term_link = get_edit_term_link($term_id, $taxonomy);
        }

        $id = $term_id;
        $post_type = 'Taxonomy';
        $title = $term->name . ' from ' . $taxonomy . 'Taxonomy ID: ' . $tt_id;

        $log_data = new E25_AL_Log($post_type, $action, $title, $id);
        $log_data->post_link = $term_link;
        e25_record_activity_log($log_data, __FUNCTION__);
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////Hook into WordPress actions for menu management/////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_create_nav_menu', 'e25_activity_log_hooks_menu_created_or_updated');
add_action('wp_update_nav_menu', 'e25_activity_log_hooks_menu_created_or_updated');
add_action('wp_delete_nav_menu', 'e25_activity_log_hooks_menu_created_or_updated');

function e25_activity_log_hooks_menu_created_or_updated($menu_id)
{
    if (get_transient("e25_activity_log_hooks_menu_created_or_updated_ran_for$menu_id")) {
        return;
    }

    $id = $menu_id;
    $menu = wp_get_nav_menu_object($menu_id);
    $title = $menu ? $menu->name : '';
    $post_type = 'Menu';

    if (current_filter() === 'wp_create_nav_menu') {
        $action = 'Menu item created - ';
    } elseif (current_filter() === 'wp_update_nav_menu') {
        $action = 'Menu item updated - ';
    } elseif (current_filter() === 'wp_delete_nav_menu') {
        $action = 'Menu item deleted';
    }

    $action .= $title;

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_hooks_menu_created_or_updated_ran_for$menu_id", true, 1);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////WIDGET//////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_filter('widget_update_callback', 'e25_activity_log_hooks_widget_update_callback', 9999, 4);
add_action('delete_widget', 'e25_activity_log_hooks_widget_delete');

function e25_activity_log_hooks_widget_update_callback($instance, $new_instance, $old_instance, WP_Widget $widget)
{
    $id = $widget->id_base;

    if (empty($_REQUEST['sidebar']) || get_transient("e25_activity_log_hooks_widget_update_callback_ran_for_$id")) {
        return $instance;
    }

    $post_type = 'Widget';
    $title = $widget->name;
    $action = "Updated settings of {$title}";
    $post_link = '_';

    // a debug line to use the params to avoid Sonar issues
    // debugging will only occurr on Debug instances - no overhead on the server
    $instance_settings = serialize($old_instance) . serialize($new_instance);
    e25_activity_log_debug_logger('e25_activity_log_hooks_widget_update_callback', $instance_settings);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    $log_data->content = array(
        'oldInstance' => $old_instance,
        'newInstance' => $new_instance,
        'widget' => $widget,
        'instance' => $instance
    );
    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_hooks_widget_update_callback_ran_for_$id", true, 2);

    // need to return the instance, to complete the filter.
    return $instance;
}

function e25_activity_log_hooks_widget_delete($widget_id)
{
    $id = $widget_id;
    $post_type = 'Widget';
    $title = (isset($wp_registered_widgets) && isset($wp_registered_widgets[$id]['name'])) ?
        $wp_registered_widgets[$id]['name'] : 'Widget';
    $action = 'Deleted';

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////Attachments - file upload and delete///////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('add_attachment', 'e25_activity_log_hooks_file_activity');
add_action('delete_attachment', 'e25_activity_log_hooks_file_activity');
add_action('edit_attachment', 'e25_activity_log_hooks_file_activity');

function e25_activity_log_hooks_file_activity($attachment_id)
{
    $id = $attachment_id;
    $post_type = 'Attachment';

    $attachment = get_post($attachment_id);

    $title = $attachment->post_title;
    $file_url = wp_get_attachment_url($attachment_id);
    if (current_filter() === 'add_attachment') {
        $action = 'Uploaded';
    } elseif (current_filter() === 'delete_attachment') {
        $action = 'Deleted';
    } elseif (current_filter() === 'edit_attachment') {
        $action = 'Updated';
    }

    $action .= " - attachment ID:{$attachment->ID}, mime type:{$attachment->post_mime_type}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $file_url;
    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////Core-wp_update//////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('upgrader_process_complete', 'e25_activity_log_hooks_wp_update', 10, 2);

function e25_activity_log_hooks_wp_update($upgrader_object, $options)
{
    if ('core' === $options['type'] && !empty($options['version'])) {
        $title = 'WordPress';
        $version = $options['version'];
        $action = "Updated to version - $version";
        $post_link = admin_url('update-core.php');

        if ($upgrader_object->is_multi) {
            $title .= ' (Network)';
        }

        if (defined('AUTOMATIC_UPDATER_DISABLED') && AUTOMATIC_UPDATER_DISABLED) {
            $action .= ' (Auto)';
        }

        $log_data = new E25_AL_Log('wp_update', $action, $title);
        $log_data->content = array(
            'upgrader' => $upgrader_object,
            'options' => $options
        );
        $log_data->post_link = $post_link;
        e25_record_activity_log($log_data, __FUNCTION__);
    }
}


////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////Settings changes//////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('updated_option', 'e25_activity_log_hooks_settings_change', 10, 3);

define('E25_AL_WP_SETTINGS', 'wp_settings');

function e25_activity_log_hooks_settings_change($option_name, $old_value, $new_value)
{
    if (get_transient("e25_activity_log_hooks_settings_change_ran_for_$option_name")) {
        return;
    }

    // construct a list of option keys to react for changes
    $registered_settings = get_registered_settings();
    $custom_options = array();
    foreach ($registered_settings as $key => $value) {
        if (!isset($value['default'])) {
            continue;
        }
        $custom_options[] = $key;
    }

    $default_options = e25_activity_log_get_default_options();

    // ignore any other option keys - avoid unwanted internal setting updates being logged
    if (!in_array($option_name, $default_options) && !in_array($option_name, $custom_options)) {
        $log_msg = "'$option_name' not in default/custom options, skipping activity log.";
        e25_activity_log_debug_logger(__FUNCTION__, $log_msg);
        return;
    }

    $title = ucwords(str_replace('_', ' ', $option_name));

    // convert option values to human readable
    list($old_value, $new_value) = e25_activity_log_convert_option_values($option_name, $old_value, $new_value);

    // handle array value changes - only record as a change
    $from_text = '';
    $to_text = '';

    if (!empty($old_value) && !(is_array($old_value) || is_object($old_value))) {
        $from_text = " from '$old_value'";
    }

    if (!empty($new_value) && !(is_array($new_value) || is_object($new_value))) {
        $to_text = " to '$new_value'";
    }

    $action = "$title updated$from_text$to_text";
    $post_link = admin_url('options-general.php');

    $log_data = new E25_AL_Log(E25_AL_WP_SETTINGS, $action, $title);
    $log_data->content = array(
        'optionName' => $option_name,
        'oldValue' => $old_value,
        'newValue' => $new_value
    );
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_hooks_settings_change_ran_for_$option_name", true, 1);
}

///////////////////////////////////////////////////////////////////////
///////////// Export/Erase data //////////////////////////////////////
//////////////////////////////////////////////////////////////////////
define('E25_AL_EXPORT', 'Export');
add_action('export_wp', 'e25_activity_log_hooks_export');
function e25_activity_log_hooks_export($args)
{
    $args_str = serialize($args);
    if (get_transient("e25_activity_log_hooks_export_ran_for_$args_str")) {
        return $args;
    }

    $title = E25_AL_EXPORT;
    $action = 'Export initiated';

    $log_data = new E25_AL_Log(E25_AL_WP_SETTINGS, $action, $title);
    $log_data->content = array(
        'args' => $args,
    );
    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_hooks_export_ran_for_$args_str", true, 2);
}

add_action('wp_privacy_personal_data_export_file', 'e25_activity_log_hooks_export_personal_data');
function e25_activity_log_hooks_export_personal_data($request_id)
{
    $action = "Personal data export requested with id $request_id";
    $title = E25_AL_EXPORT;
    $log_data = new E25_AL_Log(E25_AL_WP_SETTINGS, $action, $title, $request_id);
    e25_record_activity_log($log_data, __FUNCTION__);
}

add_action('wp_privacy_personal_data_erased', 'e25_activity_log_hooks_erase_personal_data');
function e25_activity_log_hooks_erase_personal_data($request_id)
{
    $action = "Personal data erased - id $request_id";
    $title = 'Erase';
    $log_data = new E25_AL_Log(E25_AL_WP_SETTINGS, $action, $title, $request_id);
    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////Yoast SEO plugin changes//////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
add_action('added_post_meta', 'e25_activity_log_hooks_yoast_seo_meta_changes', 10, 4);
add_action('updated_post_meta', 'e25_activity_log_hooks_yoast_seo_meta_changes', 10, 4);
add_action('deleted_post_meta', 'e25_activity_log_hooks_yoast_seo_meta_changes', 10, 4);
add_action('wpseo_publishbox_misc_actions', 'e25_activity_log_yoast_seo_pblishbox_actions');

function e25_activity_log_yoast_seo_pblishbox_actions($post)
{
    $id = $post->ID;
    $post_type = E25_AL_PLUGIN;
    $title = 'Yoast SEO';
    $action = "Post metadata changed";

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->content = array('post' => $post);
    $log_data->post_link = get_edit_post_link($post->ID);

    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_hooks_yoast_seo_meta_changes($meta_id, $post_id, $meta_key, $meta_value)
{
    // Check if the meta key is from the Yoast SEO plugin.
    if (strpos($meta_key, '_yoast_') === 0) {
        $post = get_post($post_id);
        $action = 'Updated meta with id: ' . $meta_id;
        $post_type = 'Yoast SEO';
        $title = $post->post_title;

        if ($meta_value === '') {
            $action = 'deleted';
        }

        $action .= ' ' . $meta_key . ' of ' . $title . ' ' . $post->post_type;

        $log_data = new E25_AL_Log($post_type, $action, $title, $post_id);
        $log_data->content = array(
            'post' => $post,
            'metaKey' => $meta_key,
            'metaValue' => $meta_value,
            'metaId' => $meta_id,
        );
        $log_data->post_link = get_edit_post_link($post->ID);
        e25_record_activity_log($log_data, __FUNCTION__);
    }
}

////////////////////////////////
// Akismet Spam Detection plugin
// hooks listed under - https://adambrown.info/p/wp_hooks/hook/actions#:~:text=103,akismet_tabs
////////////////////////////////
add_action('akismet_batch_delete_count', 'e25_activity_log_akismet_batch_delete_count');
add_action('akismet_comment_check_response', 'e25_activity_log_akismet_comment_check_response');
add_action('akismet_delete_commentmeta_batch', 'e25_activity_log_akismet_delete_commentmeta_batch');
add_action('akismet_delete_comment_batch', 'e25_activity_log_akismet_delete_comment_batch');
add_action('akismet_https_disabled', 'e25_activity_log_akismet_https_disabled');
add_action('akismet_spam_caught', 'e25_activity_log_akismet_spam_caught');
add_action('akismet_submit_spam_comment', 'e25_activity_log_akismet_submit_spam_comment');

function e25_activity_log_akismet_batch_delete_count()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'Akismet';
    $action = 'Batch delete count';

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_akismet_comment_check_response()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'Akismet';
    $action = 'Comment check response';

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_akismet_delete_commentmeta_batch()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'Akismet';
    $action = 'Delete comment meta batch';

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_akismet_delete_comment_batch()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'Akismet';
    $action = 'Delete comment batch';

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_akismet_https_disabled()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'Akismet';
    $action = 'Https disabled';

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_akismet_spam_caught()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'Akismet';
    $action = 'Spam caught';

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_akismet_submit_spam_comment()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'Akismet';
    $action = 'Spam comment';

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////
// ACF plugin
// https://www.advancedcustomfields.com/resources/
//////////////////////
add_action('acf/save_post', 'e25_activity_log_acf_save_post');
add_action('acf/updated_field', 'e25_activity_log_acf_field_updated');
add_action('acf/delete_field', 'e25_activity_log_acf_field_deleted');
add_action('acf/trash_field', 'e25_activity_log_acf_field_trashed');

add_action('acf/import_field_group', 'e25_activity_log_acf_field_group_imported');
add_action('acf/duplicate_field_group', 'e25_activity_log_acf_field_group_duplicated');
add_action('acf/trash_field_group', 'e25_activity_log_acf_field_group_trashed');
add_action('acf/untrash_field_group', 'e25_activity_log_acf_field_group_untrashed');
add_action('acf/delete_field_group', 'e25_activity_log_acf_field_group_deleted');

define('ADVANCED_CUSTOM_FIELDS', 'Advanced Custom Fields');
function e25_activity_log_acf_save_post($post_id)
{
    $id = $post_id;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = 'New field';
    $post_link = get_edit_post_link($post_id);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_updated($field)
{
    $id = $field->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field updated: Label={$field->label}, Key={$field->key}, Name={$field->name}";
    $post_link = get_edit_post_link($field->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_deleted($field)
{
    $id = $field->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field deleted: Label={$field->label}, Key={$field->key}, Name={$field->name}";
    $post_link = get_edit_post_link($field->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_trashed($field)
{
    $id = $field->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field trashed: Label={$field->label}, Key={$field->key}, Name={$field->name}";
    $post_link = get_edit_post_link($field->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_untrashed($field)
{
    $id = $field->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field recovered: Label={$field->label}, Key={$field->key}, Name={$field->name}";
    $post_link = get_edit_post_link($field->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_group_untrashed($post)
{
    $id = $post->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field group recovered: Label={$post->label}, Key={$post->key}, Name={$post->name}";
    $post_link = get_edit_post_link($post->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_group_trashed($post)
{
    $id = $post->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field group trashed: Label={$post->label}, Key={$post->key}, Name={$post->name}";
    $post_link = get_edit_post_link($post->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_group_imported($post)
{
    $id = $post->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field group imported: Label={$post->label}, Key={$post->key}, Name={$post->name}";
    $post_link = get_edit_post_link($post->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_group_duplicated($post)
{
    $id = $post->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field group duplicated: Label={$post->label}, Key={$post->key}, Name={$post->name}";
    $post_link = get_edit_post_link($post->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_acf_field_group_deleted($post)
{
    $id = $post->ID;
    $post_type = E25_AL_PLUGIN;
    $title = ADVANCED_CUSTOM_FIELDS;
    $action = "Field group deleted: Label={$post->label}, Key={$post->key}, Name={$post->name}";
    $post_link = get_edit_post_link($post->ID);

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////
// Yoast Duplicate Post plugin
// https://yoast.com/
//////////////////////
add_action('duplicate_post_post_copy', 'e25_activity_log_yoast_duplicate_post_copy', 10, 2);
function e25_activity_log_yoast_duplicate_post_copy($new_post_id, $post)
{
    $id = $new_post_id;
    $post_type = E25_AL_PLUGIN;
    $title = 'Yoast duplicate post';
    $action = "Duplicate post created from ID: {$post->ID} - new ID: {$new_post_id}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->post_link = get_edit_post_link($new_post_id);
    $log_data->content = array('post' => $post);
    e25_record_activity_log($log_data, __FUNCTION__);
}

////////////////////////
// Permalink Manager plugin
// https://permalinkmanager.pro
//////////////////////
add_action('permalink_manager_updated_term_uri', 'e25_activity_log_permalink_manager_term_uri_update', 10, 3);
add_action('permalink_manager_updated_post_uri', 'e25_activity_log_permalink_manager_post_uri_update', 10, 3);
add_action('permalink_manager_new_post_uri', 'e25_activity_log_permalink_manager_new_post_uri', 10, 2);

define('E25_AL_PERMALINK_MANAGER', 'Permalink manager');
define('E25_AL_PERMALINK_MANAGER_URL', 'tools.php?page=permalink-manager&section=settings');

function e25_activity_log_permalink_manager_term_uri_update($pid, $new_uri, $old_uri)
{
    $post_type = E25_AL_PLUGIN;
    $title = E25_AL_PERMALINK_MANAGER;
    $action = "Term URI updated from {$old_uri} to {$new_uri}";
    $post_link = admin_url(E25_AL_PERMALINK_MANAGER_URL);

    $log_data = new E25_AL_Log($post_type, $action, $title, $pid);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_permalink_manager_post_uri_update($pid, $new_uri, $old_uri)
{
    $post_type = E25_AL_PLUGIN;
    $title = E25_AL_PERMALINK_MANAGER;
    $action = 'Post URI updated';
    $action .= ($old_uri !== $new_uri) ? " from {$old_uri} to {$new_uri}" : '';
    $post_link = admin_url(E25_AL_PERMALINK_MANAGER_URL);

    $log_data = new E25_AL_Log($post_type, $action, $title, $pid);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_permalink_manager_new_post_uri($post_id, $new_uri)
{
    $post_type = E25_AL_PLUGIN;
    $title = E25_AL_PERMALINK_MANAGER;
    $action = "New post URI added: {$new_uri}, postId: $post_id";
    $post_link = admin_url(E25_AL_PERMALINK_MANAGER_URL);

    $log_data = new E25_AL_Log($post_type, $action, $title, $post_id);
    $log_data->post_link = $post_link;
    e25_record_activity_log($log_data, __FUNCTION__);
}

/////////////////////////////////
// WPML Media Translation plugin
// https://wpml.org ////////////
/////////////////////////////////
add_action('wpml_pb_resave_post_translation', 'e25_activity_log_wpml_media_resave');
function e25_activity_log_wpml_media_resave($post_element)
{
    $post_type = E25_AL_PLUGIN;
    $title = 'WPML Media Translation';

    $lang_code = $post_element->get_language_code();
    $source_lang_code = $post_element->get_source_element()->get_language_code();
    $action = "Post media translated from {$source_lang_code} to {$lang_code}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $post_element->get_id());
    $log_data->post_link = get_edit_post_link($post_element->get_id());
    $log_data->content = array('postElement' => $post_element);
    e25_record_activity_log($log_data, __FUNCTION__);
}

/////////////////////////////////
// WPML String Translation plugin
// https://wpml.org ////////////
/////////////////////////////////
add_action('wpml_st_add_string_translation', 'e25_activity_log_wpml_string_translation_add');
add_action('wpml_st_language_of_strings_changed', 'e25_activity_log_wpml_string_lang_changed');

function e25_activity_log_wpml_string_translation_add()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'WPML String Translation';
    $action = "String added for translation";

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_wpml_string_lang_changed()
{
    $post_type = E25_AL_PLUGIN;
    $title = 'WPML String Translation';
    $action = "Language changed for strings";

    $log_data = new E25_AL_Log($post_type, $action, $title);
    e25_record_activity_log($log_data, __FUNCTION__);
}

/////////////////////////////////
// IThemesSecurity plugin
// https://ithemes.com/security//
/////////////////////////////////
add_action('itsec_create_user_group', 'e25_activity_log_itsec_create_user_group');
add_action('itsec_update_user_group', 'e25_activity_log_itsec_update_user_group');
add_action('updated_user_meta', 'e25_activity_log_itsec_updated_user_meta', 10, 2);
add_action('itsec_change_admin_user_id', 'e25_activity_log_itsec_admin_changed', 10, 2);

define('E25_AL_ITHEMESSECURITY', 'IThemes Security');

function itsec_create_user_group($user_group)
{
    $post_type = E25_AL_PLUGIN;
    $title = E25_AL_ITHEMESSECURITY;
    $action = "User group created: Name={$user_group->get_label()}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $user_group->get_id());
    $log_data->content = array(
        'userGroup' => $user_group,
    );
    e25_record_activity_log($log_data, __FUNCTION__);
}

function itsec_update_user_group($user_group)
{
    $post_type = E25_AL_PLUGIN;
    $title = E25_AL_ITHEMESSECURITY;
    $action = "User group updated: Name={$user_group->get_label()}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $user_group->get_id());
    $log_data->content = array(
        'userGroup' => $user_group,
    );
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_itsec_updated_user_meta($meta_id, $user_id)
{
    $transient_id = "$meta_id-$user_id";
    if (get_transient("e25_activity_log_itsec_updated_user_meta_ran_for_$transient_id")) {
        return;
    }

    $post_type = E25_AL_PLUGIN;
    $title = E25_AL_ITHEMESSECURITY;
    $action = "User meta updated: userid={$user_id}, metaid={$meta_id}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $user_id);
    e25_record_activity_log($log_data, __FUNCTION__);

    set_transient("e25_activity_log_itsec_updated_user_meta_ran_for_$transient_id", true, 1);
}

function e25_activity_log_itsec_admin_changed($new_user, $user)
{
    $post_type = E25_AL_PLUGIN;
    $title = E25_AL_ITHEMESSECURITY;
    $action = "Admin user changed from {$user->user_login} to {$new_user->user_login}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $user->user_login);
    $log_data->content = array(
        'user' => $user,
        'newUser' => $new_user,
    );
    e25_record_activity_log($log_data, __FUNCTION__);
}

/////////////////////////////////
// Google sitemap generator plugin
// https://auctollo.com///
/////////////////////////////////
add_action('sm_addsitemap', 'e25_activity_log_sm_add_sitemap');
add_action('sm_addurl', 'e25_activity_log_sm_add_url');

function e25_activity_log_sm_add_sitemap($sitemap)
{
    $id = $sitemap->get_url();
    $post_type = E25_AL_PLUGIN;
    $title = 'Sitemap generator for Google';
    $action = "New sitemap entry added with URL {$id}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $sitemap->get_url());
    $log_data->content = array('siteMap' => $sitemap);
    e25_record_activity_log($log_data, __FUNCTION__);
}

function e25_activity_log_sm_add_url($page)
{
    $id = $page->url;
    $post_type = E25_AL_PLUGIN;
    $title = 'Sitemap generator for Google';
    $action = "New sitemap url added: {$id}";

    $log_data = new E25_AL_Log($post_type, $action, $title, $id);
    $log_data->content = array('page' => $page);
    e25_record_activity_log($log_data, __FUNCTION__);
}

// --------------------------------------------------------------------------------------------------
// -- helpers --

define(
    'E25_AL_WEEK_DAY_MAP',
    array(
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    )
);

/**
 * convert option values to human readable
 */
function e25_activity_log_convert_option_values($option_name, $old_value, $new_value)
{
    switch ($option_name) {
        case 'start_of_week':
            $old_value = E25_AL_WEEK_DAY_MAP[$old_value];
            $new_value = E25_AL_WEEK_DAY_MAP[$new_value];
            break;
        case 'facetwp_settings':
            $old_value = '';
            $new_value = '';
            break;
        case 'default_post_format':
            $old_value = $old_value == 0 ? 'Standard' : $old_value;
            $new_value = $new_value == 0 ? 'Standard' : $new_value;
            break;
        case 'rss_use_excerpt':
            $old_value = $old_value == 0 ? 'Full text' : 'Excerpt';
            $new_value = $new_value == 0 ? 'Full text' : 'Excerpt';
            break;
        case 'gmt_offset':
            $old_value = !$old_value ? '0' : $old_value;
            $new_value = !$new_value ? '0' : $new_value;
            break;
        case 'blog_public':
            $old_value = !$old_value ? 'true' : 'false';
            $new_value = !$new_value ? 'true' : 'false';
            break;
        default:
            [$old_value, $new_value] = transform_boolean_to_readable($old_value, $new_value);
            break;
    }

    return array($old_value, $new_value);
}

/**
 * Transform & return default readable values for provided values
 */
function transform_boolean_to_readable($old_value, $new_value)
{
    if ($old_value == 0) {
        $old_value = 'false';
    } elseif ($old_value == 1) {
        $old_value = 'true';
    }

    if ($new_value == 0) {
        $new_value = 'false';
    } elseif ($new_value == 1) {
        $new_value = 'true';
    }

    return [$old_value, $new_value];
}

/**
 * Returns wordpress default options including supported third party plugins options
 */
function e25_activity_log_get_default_options()
{
    return array(
        // General
        'blogname',
        'blogdescription',
        'siteurl',
        'home',
        'admin_email',
        'users_can_register',
        'default_role',
        'WPLANG',
        'timezone_string',
        'date_format',
        'time_format',
        'start_of_week',
        'gmt_offset',
        'new_admin_email',

        // Writing
        'use_smilies',
        'use_balanceTags',
        'default_category',
        'default_post_format',
        'mailserver_url',
        'mailserver_login',
        'mailserver_pass',
        'default_email_category',
        'ping_sites',

        // Reading
        'show_on_front',
        'page_on_front',
        'page_for_posts',
        'posts_per_page',
        'posts_per_rss',
        'rss_use_excerpt',
        'blog_public',

        // Discussion
        'default_pingback_flag',
        'default_ping_status',
        'default_comment_status',
        'require_name_email',
        'comment_registration',
        'close_comments_for_old_posts',
        'close_comments_days_old',
        'thread_comments',
        'thread_comments_depth',
        'page_comments',
        'comments_per_page',
        'default_comments_page',
        'comment_order',
        'comments_notify',
        'moderation_notify',
        'comment_moderation',
        'comment_whitelist',
        'comment_max_links',
        'moderation_keys',
        'blacklist_keys',
        'show_avatars',
        'avatar_rating',
        'avatar_default',
        'disallowed_keys',
        'show_comments_cookies_opt_in',
        'comment_previously_approved',

        // Media
        'thumbnail_size_w',
        'thumbnail_size_h',
        'thumbnail_crop',
        'medium_size_w',
        'medium_size_h',
        'large_size_w',
        'large_size_h',
        'uploads_use_yearmonth_folders',

        // Permalinks
        'permalink_structure',
        'category_base',
        'tag_base',

        // Privacy
        'wp_page_for_privacy_policy',

        // Widgets
        'sidebars_widgets',

        // AAL
        'logs_lifespan',

        // custom plugins
        'sm_options', // google-sitemap-generator
        'sm_user_consent',
        'wpseo', // yoast seo
        'wpseo_titles',
        'heateor_sss', // sassy-social-share
        'accordion_blocks_load_scripts_globally', // accordion-blocks
        'accordion_blocks_defaults',
        'bodhi_svgs_settings', // SVG-support
        'facetwp_settings', // facetwp
        'itsec-storage', // ithemesecurity
        'megamenu_settings', // megamenu
        'megamenu_themes',
        'megamenu_css',
        'admin_post_megamenu_add_menu_location',
        'megamenu_after_theme_save',
        'megamenu_locations',
        'icl_sitepress_settings', // wpml
        'wpml_sitepress_settings',
        'wpml_tax_slug_translation_settings',
        'icl_st_settings',
        'wpml_st_display_strings_scan_notices',
        'permalink-manager', // permalink manager
        'permalink-manager-uris',
        'duplicate_post_show_notice', // yoast duplicate post
        'duplicate_post_copytitle',
        'duplicate_post_copydate',
        'duplicate_post_copystatus',
        'duplicate_post_copyslug',
        'duplicate_post_copyexcerpt',
        'duplicate_post_copycontent',
        'duplicate_post_copythumbnail',
        'duplicate_post_copytemplate',
        'duplicate_post_copyformat',
        'duplicate_post_copyauthor',
        'duplicate_post_copypassword',
        'duplicate_post_copyattachments',
        'duplicate_post_copychildren',
        'duplicate_post_copycomments',
        'duplicate_post_copymenuorder',
        'duplicate_post_taxonomies_blacklist',
        'duplicate_post_blacklist',
        'duplicate_post_types_enabled',
        'duplicate_post_show_original_column',
        'duplicate_post_show_original_in_post_states',
        'duplicate_post_show_original_meta_box',
        'duplicate_post_show_link_in',
        'permalink-manager-redirects',
        'wp_mail_smtp_review_notice', //wp mail smtp
        'wp_mail_smtp',
        '_wpml_media', // wpml media
    );
}

/**
 * Checks if the post type is content post or a custom post type
 * & not a default post type related to metadata
 */
function e25_activity_log_is_content_post_type($post_type)
{
    $default_ignored_post_types = array(
        'attachment',
        'revision',
        'nav_menu_item',
        'custom_css',
        'customize_changeset',
        'user_request',
        'wp_block',
        'acf-field'
    );

    return !in_array($post_type, $default_ignored_post_types);
}

/**
 * Checks difference in old & new post objects & returns the update log string
 */
function e25_activity_log_post_diffs($post_old, $post_new)
{
    // new post
    if (!$post_old) {
        $previous_title = '';
        $previous_content = '';
        $previous_excerpt = '';
    } else {
        $previous_title = $post_old->post_title;
        $previous_content = get_post_field('post_content', $post_old);
        $previous_excerpt = get_post_field('post_excerpt', $post_old);
    }

    $updated_title = $post_new->post_title;
    $updated_content = get_post_field('post_content', $post_new);
    $updated_excerpt = get_post_field('post_excerpt', $post_new);

    // Check if the title, content, or excerpt has changed
    // detect actual content changes
    if ($previous_title !== $updated_title) {
        $from_text = $previous_title ? " from '$previous_title'" : '';
        $title_action = "updated title$from_text to '$updated_title'";
    }
    if ($previous_content !== $updated_content) {
        $content_action = "updated content body area";
    }
    if ($previous_excerpt !== $updated_excerpt) {
        $from_excerpt_text = $previous_excerpt ? " from '$previous_excerpt'" : '';
        $excerpt_action = "updated excerpt$from_excerpt_text to '$updated_excerpt'";
    }

    // Build the final action string
    $action_parts = array();
    if (isset($title_action)) {
        $action_parts[] = $title_action;
    }
    if (isset($content_action)) {
        $action_parts[] = $content_action;
    }
    if (isset($excerpt_action)) {
        $action_parts[] = $excerpt_action;
    }

    $action = 'Updated';

    // Join the action parts with commas
    if (!empty($action_parts)) {
        $action .= ': ' . implode(", ", $action_parts);
    }

    return $action;
}

/**
 * Get user's Ip
 */
function e25_activity_log_get_current_user_ip()
{
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        // Get IP from shared internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Get IP from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        // Get IP from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        // Get IP from proxy
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        // Get IP from proxy
        $ip = $_SERVER['HTTP_FORWARDED'];
    } else {
        // Get IP from remote address
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Remove any potential multiple IP addresses
    $ip = explode(',', $ip);
    $ip = trim($ip[0]);

    return $ip;
}

/**
 * Send log messages to Lambda function
 * AWS function name - PageLogs
 */
function e25_record_activity_log(E25_AL_Log $log_data, $function = '')
{
    $user = $log_data->user ? $log_data->user : wp_get_current_user();
    $current_time = date('Y-m-d\TH:i:s');
    $project_name = get_bloginfo('name');
    $project_id = str_replace(' ', '_', $project_name);
    $url = 'https://kwoxlzw4bocfrncxs2vdgyrcgq0pykqo.lambda-url.eu-north-1.on.aws/';
    $current_user_ip = e25_activity_log_get_current_user_ip();

    $payload = array(
        'id' => $log_data->id,
        'project_id' => $project_id,
        'user' => $user->user_login,
        'email' => $user->user_email,
        'post_type' => $log_data->post_type,
        'action' => $log_data->action,
        'title' => $log_data->title,
        'post_link' => $log_data->post_link,
        'ip' => $current_user_ip,
        'current_time' => $current_time,
        'content' => isset($log_data->content) ? json_encode($log_data->content) : null
    );

    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($payload))
        )
    );

    e25_activity_log_debug_logger($function, $payload);

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    curl_close($ch);

    e25_activity_log_debug_logger($function, $response);

    return $response;
}

/**
 * Logs debug messages.
 * By default logs written to debug.log file within wp-content directory
 * file might not be created automatically, in such case, file will have to be created manually
 * & set with necessary permissions first
 */
function e25_activity_log_debug_logger($function, $message)
{
    if (WP_DEBUG === true) {
        error_log('E25_Log: ' . $function);
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}
