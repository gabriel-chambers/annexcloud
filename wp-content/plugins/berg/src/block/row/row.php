<?php
function row_visibility($block_content, $block)
{   
    if ($block['blockName'] === 'e25m/row') {        
        if(isset($block['attrs']['rowVisibility'])){
            return false;
        }
        else {
            return $block_content;
        }       
    }
    return $block_content;
}

add_filter('render_block', 'row_visibility', 10, 2);