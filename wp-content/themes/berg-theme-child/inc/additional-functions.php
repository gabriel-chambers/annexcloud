<?php
//Override Berg-theme functionalities from here

/*
 * Helper function to generate the color names
 */
if (!function_exists('create_color_name')) {
    function create_color_name($name)
    {
        $name = ignore_quotes_from_string($name);
        return ucwords(implode(" ", explode("-", $name)));
    }
}

/*
 * To remove single and double quotes from a string
 */
if (!function_exists('ignore_quotes_from_string')) {
    function ignore_quotes_from_string($name)
    {
        return str_replace(['"', "'"], "", $name);
    }
}

/*
 * To generate the custom color palette
 */
function custom_color_palette()
{
    $color_palette = json_decode(file_get_contents(__DIR__ . "/../assets/json/color-palette.json"), true);
    $color_palette = isset($color_palette['context']['colors']['Other']['swatches']) ?
        $color_palette['context']['colors']['Other']['swatches'] : null;

    if ($color_palette != null) {
        $colors = array();
        foreach ($color_palette as $value) {
            array_push(
                $colors,
                array(
                    'name' => __(create_color_name($value['name']), 'themeLangDomain'),
                    'slug' => ignore_quotes_from_string($value['name']),
                    'color' => $value['hex'],
                )
            );
        }
        add_theme_support('editor-color-palette', $colors);
    }
}
add_action('after_setup_theme', 'custom_color_palette');

/*
 * To hide all berg related plugins from the dashboard in production environment
 */
if (defined('WP_ENV') && WP_ENV == "production") {
    add_filter(
        'all_plugins',
        function ($plugins) {
            $hidden_plugins = [
                'berg/plugin.php',
                'berg-custom/plugin.php',
                'block-navigation/block-navigation.php',
                'realm/realm.php',
                'advanced-custom-fields-pro/acf.php',
                'wp-plugin-dependencies/plugin.php',
            ];
            foreach ($hidden_plugins as $hidden_plugin) {
                unset($plugins[$hidden_plugin]);
            }
            return $plugins;
        }
    );
}

/*
 * To remove empty paragraph tags from the page content
 */
function remove_empty_p($content)
{
    $content = force_balance_tags($content);
    $content = preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
    $content = preg_replace('~\s?<p>(\s|&nbsp;)+</p>\s?~', '', $content);
    return $content;
}
/* Uncomment this only if required */

/*
 * To remove WP embed script
 */
function speed_stop_loading_wp_embed()
{
    if (!is_admin()) {
        wp_deregister_script('wp-embed');
    }
}
add_action('init', 'speed_stop_loading_wp_embed');

/*
 * To remove WP emoji
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/*
 * To remove WP admin emoji
 */
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

/*
 * To remove unwanted header elements
 */

/* Uncomment these remove actions only if required */

/*
 * To remove recent comments wp_head CSS
 */
function my_remove_recent_comments_style()
{
    add_filter('show_recent_comments_widget_style', '__return_false');
}
add_action('widgets_init', 'my_remove_recent_comments_style');

/*
 * To remove thumbnail dimensions
 */
function remove_thumbnail_dimensions($html)
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3);

/*
 * To add additional file types
 */
function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    $mimes['doc'] = 'application/msword';
    $mimes['json'] = 'application/json';
    $mimes['ogg'] = 'video/ogg';
    $mimes['ext'] = 'webp';
    $mimes['type'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

/*
 * Defining REST API fields to be accessed from the Berg plugin
 */
add_action('rest_api_init', function () {
    register_rest_field(
        'theme',
        'block_classes',
        [
            'get_callback' => function () {
                $core_classes = file_exists(get_stylesheet_directory() . '/inc/core-classes.php')
                    ? require_once 'core-classes.php'
                    : [];
                $theme_classes = file_exists(get_stylesheet_directory() . '/inc/theme-classes.php')
                    ? require_once 'theme-classes.php'
                    : [];
                return (object) array_merge(
                    (array) $core_classes,
                    (array) $theme_classes
                );
            },
        ]
    );
    register_rest_field(
        'theme',
        'wp_environment_type',
        [
            'get_callback' => function () {
                return wp_get_environment_type();
            },
        ]
    );
});


// Yoast SEO plugin fix for remove social share from users
add_filter('user_contactmethods', 'yoast_seo_admin_user_remove_social', 99);
function yoast_seo_admin_user_remove_social($contactmethods)
{
    unset($contactmethods['facebook']);
    unset($contactmethods['instagram']);
    unset($contactmethods['linkedin']);
    unset($contactmethods['myspace']);
    unset($contactmethods['pinterest']);
    unset($contactmethods['soundcloud']);
    unset($contactmethods['tumblr']);
    unset($contactmethods['twitter']);
    unset($contactmethods['youtube']);
    unset($contactmethods['wikipedia']);
    return $contactmethods;
}

function theme_login_logo()
{
    $logo_url = get_theme_mod('secondary_logo');
    if (empty($logo_url)) {
        $header_logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_url($header_logo_id);
    }

    echo '<style type="text/css">
        .login h1 a {
            background-image:url(' . $logo_url . ') !important;
            width: 274px;
            background-position: center bottom;
            background-size: contain;
        }
    </style>';
}
add_action('login_head', 'theme_login_logo');

// change url of login logo link
add_filter('login_headerurl', 'custom_loginlogo_url');
function custom_loginlogo_url()
{
    return "https://www.annexcloud.com/";
}

// Resource Inner URL Customize for Permalink Manager Plugin
function blog_permastructure($permastructure, $post)
{
    // Filter only 'Resource' permalinks
    if (empty($post->post_type) || $post->post_type !== 'resource') {
        return $permastructure;
    }

    if (has_term('Blog', 'resource-type', $post)) {
        $permastructure = 'blog/%postname%';
    }

    return $permastructure;
}
add_filter('permalink_manager_filter_permastructure', 'blog_permastructure', 10, 2);

//hide admin post menu
add_action('admin_menu', 'remove_default_post_type');
function remove_default_post_type()
{
    remove_menu_page('edit.php');
}

add_action('admin_bar_menu', 'remove_default_post_type_menu_bar', 999);
function remove_default_post_type_menu_bar($wp_admin_bar)
{
    $wp_admin_bar->remove_node('new-post');
}

// Removes from admin menu
add_action('admin_menu', 'my_remove_admin_menus');
function my_remove_admin_menus()
{
    remove_menu_page('edit-comments.php');
}

// Removes from post and pages
add_action('init', 'remove_comment_support', 100);

function remove_comment_support()
{
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
}

// Removes from admin bar
function remove_comment_admin_bar_render()
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}

add_action('wp_before_admin_bar_render', 'remove_comment_admin_bar_render');

//enable excerpt for pages
add_post_type_support('page', 'excerpt');

//remove defualt profile pic in admin
add_filter('option_show_avatars', '__return_false');

//To hide counts from all dropdowns
add_filter('facetwp_facet_dropdown_show_counts', '__return_false');

function custom_search_query($query)
{
    //filter for search result page
    if (isset($_REQUEST['s']) && $query->is_main_query()) {
        $query->set('post__not_in', array(158));
    }

    // tag page pagination
    if ($query->is_tag() && $query->is_main_query()) {
        $query->set('post_type', array('resource'));
        $query->set('posts_per_page', 9);
    }

    return $query;
}
add_action('pre_get_posts', 'custom_search_query');


// stop WordPress guessing the permalink and return a 404 error
function stop_redirect_guess()
{
    return false;
}
add_filter('do_redirect_guess_404_permalink', 'stop_redirect_guess');

//Hide the search box from facet fselect
add_filter('facetwp_render_output', function ($output) {
    $output['settings']['by_category']['showSearch'] = false;
    $output['settings']['by_industry']['showSearch'] = false;
    $output['settings']['by_resource_type']['showSearch'] = false;
    $output['settings']['tags']['showSearch'] = false;
    $output['settings']['event_type']['showSearch'] = false;
    $output['settings']['use_case_industry']['showSearch'] = false;
    $output['settings']['use_cases']['showSearch'] = false;
    $output['settings']['use_cases_functionality']['showSearch'] = false;
    $output['settings']['news_year']['showSearch'] = false;
    $output['settings']['news_industry']['showSearch'] = false;
    $output['settings']['integrations_type']['showSearch'] = false;
    $output['settings']['marketplace_type']['showSearch'] = false;

    return $output;
});

/* Removing unused JS files from the home page */
add_action('wp_enqueue_scripts', 'dequeue_unwanted_js', 1003);
function dequeue_unwanted_js()
{
    /* Include the js handle names accordingly */
    $js_handles = ['heateor_sss_sharing_js'];
    if ((is_front_page() || is_page('terms-of-use')) && !empty($js_handles)) {
        foreach ($js_handles as $handle) {
            wp_deregister_script($handle);
        }
    }
}

// Add class to searched terms
function highlight_results($text)
{
    if (is_search() && !is_admin()) {
        $sr = get_query_var('s');
        if (trim($sr) != "") {
            $keys = explode(' ', $sr);
            $keys = array_filter($keys);
            $text = preg_replace('/(' . implode('|', $keys) . ')/iu',
             '<span class="search-highlight">\0</span>', $text);
        }
    }
    return $text;
}
add_filter('the_title', 'highlight_results');


add_filter('after_setup_theme', 'remove_redundant_shortlink');

function remove_redundant_shortlink()
{
    // remove HTML meta tag
    // <link rel='shortlink' href='http://example.com/?p=25' />
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);

    // remove HTTP header
    // Link: <https://example.com/?p=25>; rel=shortlink
    remove_action('template_redirect', 'wp_shortlink_header', 11);
}

/* Search Result option page */
if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title'     => 'Theme Setting',
        'menu_title'    => 'Theme Setting',
        'menu_slug'     => 'theme-setting',
        'capability'    => 'edit_posts',
        'redirect'        => false,
        'icon_url' => 'dashicons-welcome-widgets-menus',
    ));
}

//Inner page breadcrumb
function breadcrumb_shortcode()
{
    $html = '';
    $post_id = get_the_ID();
    $post_type = get_post_type($post_id);
    $li = '<li>';
    $li_close = '</li>';
    if ($post_type) {
        switch ($post_type) {
            case "resource":
                $first = "Resource";
                $type = 'resource-type';
                break;
            case "events":
                $first = "Event";
                $type = 'event-location';
                break;
            case "news":
                $first = "Newsroom";
                $type = 'news-category';
                break;
            case "careers":
                $first = "Career";
                $type = 'contract-type';
                break;
            case "usecases":
                $first = "Use Case";
                $type = 'usecases-category';
                break;
            case "integration":
                $first = "Integration";
                $type = 'integration-type';
                break;
            case "marketplace":
                $first = "Marketplace";
                $type = 'marketplace-type';
                break;
            default:
                $first = '';
                $type = '';
                break;
        }
        if ($first) {
            $html .= '<ul class="breadcrumb"><li>' . $first . $li_close;
        } else {
            $html .= '<ul class="breadcrumb">';
        }
        if ($type) {
            $html .= $li;
            $html .= implode(" | ", wp_get_object_terms($post_id, [$type], array("fields" => "names")));
            $html .= $li_close;
        }
        $breadcrumb_title = get_field('breadcrumb_title', get_the_ID());
        if ($breadcrumb_title) {
            $html .= $li . $breadcrumb_title . $li_close;
        } else {
            $html .= $li . get_the_title() . $li_close;
        }
    }

    if ($html) {
        $html .= '</ul>';
    }
    return $html;
}

add_shortcode('breadcrumb', 'breadcrumb_shortcode');

//Yoast SEO - Disabling the Primary category feature
add_filter('wpseo_primary_term_taxonomies', '__return_empty_array');

//excluding "You might also be interested in" section from search result
function rlv_no_e25m_related_post_blocks($block)
{
    if ('e25m/related-posts' === $block['blockName']) {
        return null;
    }
    return $block;
}
add_filter('relevanssi_block_to_render', 'rlv_no_e25m_related_post_blocks');

//If true, one-letter search terms are not allowed. If false, they are allowed. Default true.
add_filter('relevanssi_block_one_letter_searches', '__return_false');

//pdf embeded validation
function enqueue_pdf_embedded_customization()
{
    wp_register_script(
        'pdf-embd-block-xt',
        get_stylesheet_directory_uri() . '/dist/js/pdf_embd_blk_xt.js',
    );
}
add_action('admin_enqueue_scripts', 'enqueue_pdf_embedded_customization');

function add_pdf_emb_block_customization_dependency()
{
    $wp_scripts = wp_scripts();
    if (array_key_exists('pdfemb-gutenberg-block-js', $wp_scripts->registered)) {
        $pdfemb_block_js_script
            = $wp_scripts->registered['pdfemb-gutenberg-block-js'];
        $pdfemb_block_js_script->deps = array_merge(
            $pdfemb_block_js_script->deps,
            ['pdf-embd-block-xt']
        );
    }
}
add_action('wp_print_scripts', 'add_pdf_emb_block_customization_dependency');

function replace_pdf_text( $content ) {
    $text_strings_to_replace = array( 'PDF Embedder requires a url attribute' );
    $content = str_replace( $text_strings_to_replace, '', $content );
    return $content;
}

add_filter('the_content', 'replace_pdf_text');
