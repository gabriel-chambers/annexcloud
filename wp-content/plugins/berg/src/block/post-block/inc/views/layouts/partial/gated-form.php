<?php
/**
 * Created by PhpStorm.
 * User: sivanoly
 * Date: 3/1/19
 * Time: 2:31 PM
 */
$_learn_more_type = fw_get_db_post_option($_post_id, 'learn_more/learn_more_type');
$_learn_more_link = fw_get_db_post_option($_post_id, "learn_more/$_learn_more_type/learn_more_link");
$_learn_more_label = fw_get_db_post_option($_post_id, 'learn_more_label');

if ($_learn_more_type == "fi_link") {
    $_gated_download = fw_get_db_post_option($_post_id, "learn_more/fi_link/learn_more_link/gated_download");
    if ($_gated_download == 'yes') {
        $_gated_form = fw_get_db_post_option($_post_id, "learn_more/fi_link/learn_more_link/yes/gated_form");
        echo "<div style='display: none;' class='bs-post__target bs-post__target--form' id='bs-post__form--$_post_id' data-post-id='$_post_id'>";
        echo do_shortcode($_gated_form);
        echo "</div>";
    }
}