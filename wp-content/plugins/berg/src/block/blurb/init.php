<?php

// create new endpoint route
function blurb_get_post_data_api()
{
    register_rest_route('api/bs-blurb', 'getPostData', array(
        'methods'             => 'POST',
        'callback'            => 'blurb_get_post_data',
        'permission_callback' => '__return_true',
    ));
}

add_action('rest_api_init', 'blurb_get_post_data_api');

if (!function_exists('blurb_get_post_data')) {
    function blurb_get_post_data($data)
    {
        $params = $data->get_params();

        if (!isset($params['postId'])) {
            return [];
        }
        $postId = $params['postId'];
        $title = get_the_title($postId);
        $excerpt = get_the_excerpt($postId);
        $url = get_the_post_thumbnail_url($postId);
        $learn_more_label = get_post_meta($postId, 'learn_more_label', true);
        return new WP_REST_Response(['postId' => $postId, 'title' => $title, 'content' => $excerpt, 'imgURL' => $url, 'bottomText' => $learn_more_label]);
    }
}
