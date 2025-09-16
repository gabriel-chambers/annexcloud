<?php
function section_visibility($block_content, $block)
{   
    if ($block['blockName'] === 'e25m/section') {        
        if(isset($block['attrs']['sectionVisibility'])){
            return false;
        }
        else {
            return $block_content;
        }       
    }
    return $block_content;
}

add_filter('render_block', 'section_visibility', 10, 2);

function enqueue_section_script() {
    wp_enqueue_script(
        'section-script',
        plugins_url('js/section-events-handler-frontend.js', __FILE__),
        ['Jquery'],
        false,
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_section_script');