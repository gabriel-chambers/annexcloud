<?php

/**
 * Include Helper Functions
 */
require_once 'inc/helper.php';
require "inc/most-view-post-count.php";

function add_custom_meta_to_protected_meta($protected, $meta_key, $meta_type)
{
    $customMetaFields = [
        'show_custom_date',
        'custom_date',
        'featured',
        'featured_image',
        'image_alt_text',
        'learn_more_label',
        'learn_more_type',
        'learn_more_link',
        'show_popup',
        'learn_more_link_file',
        'event_date',
        'event_start_date',
        'event_end_date',
        'post_views_count',
        'featured_page_list',
        'disable_iframe',
        'enable_lazy_loading',
    ];

    return in_array($meta_key, $customMetaFields) ? true : $protected;
}

add_filter('is_protected_meta', 'add_custom_meta_to_protected_meta', 20, 3);

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function create_block_post_block_block_init()
{
    $attributes = json_decode(file_get_contents(__DIR__ . "/inc/attributes.json"), true);

    register_block_type('e25m/post-block', array(
        'editor_script' => 'e25m-post-block-block-editor',
        'editor_style' => 'e25m-post-block-block-editor',
        'style' => 'e25m-post-block-block',
        'attributes' => $attributes,
        'render_callback' => 'render_post_block_content'
    ));

    register_meta('post', 'show_custom_date', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'boolean',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'custom_date', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'string',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'featured', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'boolean',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'featured_image', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'number',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'learn_more_label', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'string',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'image_alt_text', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'string',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'learn_more_type', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'string',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'learn_more_link', [
        'single' => true,
        'show_in_rest' => array(
            'schema' => array(
                'type' => 'object',
                'properties' => array(),
                'additionalProperties' => array(
                    'type' => 'array',
                    'properties' => array(
                        'id' => array(
                            'type' => 'integer',
                        ),
                        'title' => array(
                            'type' => 'string',
                        ),
                        'type' => array(
                            'type' => 'string',
                        ),
                        'url' => array(
                            'type' => 'string',
                        ),
                        'opensInNewTab' => array(
                            'type' => 'boolean',
                        ),
                    ),
                    'additionalProperties' => true,
                ),
            ),
        ),
        'type' => 'object',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'show_popup', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'boolean',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'disable_iframe', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'boolean',
        'auth_callback' => function () {
            return true;
        }
    ]);

	register_meta('post', 'enable_lazy_loading', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'boolean',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'learn_more_link_file', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'number',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'event_date', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'boolean',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'event_start_date', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'string',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'event_end_date', [
        'single' => true,
        'show_in_rest' => true,
        'type' => 'string',
        'auth_callback' => function () {
            return true;
        }
    ]);

    register_meta('post', 'featured_page_list', [
        'single' => true,
        'show_in_rest' => array(
            'schema' => array(
                'type'  => 'array',
            ),
        ),
        'type' => 'object',
        'auth_callback' => function () {
            return true;
        }
    ]);
}

add_action('init', 'create_block_post_block_block_init');

function render_post_block_content($attributes)
{
    $attributes = berg_blog_posts_block_default_attributes($attributes);

    if (
        $attributes['postVisibility'] == true ||
        ($attributes['postVisibility'] == false && isset($_GET['isEditor']))
    ) {
		$attributes['search_text'] = '';
		$attributes['tax_select_filters'] = [];
		$attributes['featured_ids'] = [];
		$attributes['ajax'] = false;
		$attributes['paged'] = 1;
		$all_enabled = [];

		include "inc/variables.php";
		include "inc/query.php";

		ob_start();
		include "inc/views/layouts/{$attributes['postLayout']}.php";
		$post_output = ob_get_contents();
		ob_end_clean();

        return $post_output;
    }
}
