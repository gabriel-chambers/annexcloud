<?php
function div_visibility($block_content, $block)
{
    if ($block['blockName'] === 'e25m/div') {
        if (isset($block['attrs']['divVisibility'])) {
            return false;
        } else {
            return $block_content;
        }
    }
    return $block_content;
}

add_filter('render_block', 'div_visibility', 10, 2);
