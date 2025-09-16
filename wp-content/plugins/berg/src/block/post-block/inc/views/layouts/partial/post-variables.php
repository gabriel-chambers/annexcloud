<?php
$_post_id = get_the_ID();
$read_more_text = get_post_meta($_post_id, 'learn_more_label', true);
$read_more_text = (trim($read_more_text)) ? $read_more_text : "Read More";
$term_list_slug = array();
if (count($filters)) {
    foreach ($filters as $_filter) {
        if ($_filter != 'search') {
            $term_list_slug[] = wp_get_post_terms($_post_id, $_filter, array("fields" => "slugs"));
        }
    }
    if (!empty($term_list_slug)) {
        $term_list_slug = call_user_func_array('array_merge', $term_list_slug);
    }
}
$show_custom_date = get_post_meta($_post_id, 'show_custom_date', true);
$custom_date = get_post_meta($_post_id, 'custom_date', true);
$_custom_date = '';
$fancy_box_class  = $attributes['fancyboxStyleClassName'];
if ($show_custom_date == '1') {
    $_date = $_custom_date = date($dateFormat, strtotime($custom_date));
} else {
    $_date = get_the_time($dateFormat);
}
$_link_attributes = get_post_link($_post_id, $anchorElementAppearance, $read_more_text, $fancy_box_class);
