<?php
function column_visibility($block_content, $block)
{   
    if ($block['blockName'] === 'e25m/column') {        
        if(isset($block['attrs']['columnVisibility'])){
            return false;
        }
        else {
            return $block_content;
        }       
    }
    return $block_content;
}

add_filter('render_block', 'column_visibility', 10, 2);