<?php
/**
 * Created by PhpStorm.
 * User: sivanoly
 * Date: 2/13/19
 * Time: 3:08 PM
 */
$terms = get_terms($_filter, array('hide_empty' => true));
$taxonomy = get_taxonomy($_filter);
if (count($terms)) {
    foreach ($terms as $term) {
        $_link_attributes = array(
            'link' => 'javascript:void(0);',
            'class' => "bs-posts__filter--btn",
            'label' => $term->name,
            'target' => '',
            'attributes' => "data-filter='.$term->slug'",
            'anchor_appearance' => "inner",
        );

        echo render_link("full", $_link_attributes);
    }
}
