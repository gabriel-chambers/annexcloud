<?php

function register_e25_author()
{
    register_block_type('e25m-custom/author', array(
        'editor_script' => 'berg-block-js-vendor',
        'editor_style' => 'berg-block-editor-css',
        'style' => 'e25m-style-css',
        'render_callback' => 'e25_author_render_callback',
        'attributes' => array(
            'selectedTemplate' => array(
                'type' => 'string',
                'default' => "basic"
            ),
            'prefix' => array(
                'type' => 'string',
                'default'=> "By",
            ),
        )
    ));
}

add_action('init', 'register_e25_author');

function e25_author_render_callback($block_attributes)
{
    $selected_template = $block_attributes['selectedTemplate'];
    $prefix = $block_attributes['prefix'];
    global $post;
    $author_id = $post->post_author;
    
    ob_start();

    include 'layouts/'.$selected_template.'.php';

    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}