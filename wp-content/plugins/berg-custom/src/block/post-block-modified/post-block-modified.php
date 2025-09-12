<?php
require(POST_BLOCK_ROOT . '/inc/helper.php');

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function create_block_annex_post_block_block_init_modified()
{
    $attributes = json_decode(file_get_contents(POST_BLOCK_ROOT . "/inc/attributes.json"), true);

	unregister_block_type('e25m/post-block');
    register_block_type('e25m/post-block', array(
        'editor_script' => 'e25m-post-block-block-editor',
        'editor_style' => 'e25m-post-block-block-editor',
        'style' => 'e25m-post-block-block',
        'attributes' => $attributes,
        'render_callback' => 'render_annex_post_block_content_modified'
    ));

}

add_action('init', 'create_block_annex_post_block_block_init_modified', 11);

function render_annex_post_block_content_modified($attributes)
{
    $attributes = berg_blog_posts_block_default_attributes($attributes);

    $attributes['search_text'] = '';
    $attributes['tax_select_filters'] = [];
    $attributes['featured_ids'] = [];
    $attributes['ajax'] = false;
    $attributes['paged'] = 1;
    $all_enabled = [];

    include POST_BLOCK_ROOT."/inc/variables.php";
    include POST_BLOCK_ROOT."/inc/query.php";

    ob_start();
    include "inc/views/layouts/{$attributes['postLayout']}.php";
    $post_output = ob_get_contents();
    ob_end_clean();

    if($attributes['postVisibility'] == true || 
        ($attributes['postVisibility'] == false && isset($_GET['isEditor']))) {
        return $post_output;
    } 
}