<?php

/**
 * Include Helper Functions
 */
require_once 'inc/helper.php';

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function create_block_media_elements_block_init_custom()
{
    $attributes = json_decode(file_get_contents(__DIR__ . "/inc/attributes.json"), true);
    unregister_block_type('e25m/media-elements');
    // The block is not registered, so register it
    register_block_type('e25m/media-elements', array(
        'editor_script' => 'e25m-media-elements-block-editor',
        'editor_style' => 'e25m-media-elements-block-editor',
        'style' => 'e25m-media-elements-block',
        'attributes' => $attributes,
        'render_callback' => 'render_media_elements_content_custom'
    ));
}
add_action('init', 'create_block_media_elements_block_init_custom', 11);


function render_media_elements_content_custom($attributes)
{
    ob_start();
    include "inc/views/{$attributes['media_type_choice']}.php";
    $output = ob_get_contents();
    ob_end_clean();
    $block_classes_array = $attributes['blockClassNames'];
    $block_classes = '';
    if ( ! empty( $block_classes_array ) ) {
        $block_classes = implode( ' ', array_column( $block_classes_array, 'value' ) );
    }

    if($attributes['mediaVisibility'] == true ||
        ($attributes['mediaVisibility'] == false && isset($_GET['isEditor']))) {
        $output = "<div class='media-elements ". $block_classes .
        ($attributes['mediaVisibility'] == true ? " enable" : " disable")."'>$output</div>";
    } else {       
        $output = null;       
    }

    return $output;
}
