<?php

//add 404 page selection option 
add_action('admin_init', 'add_404_page_option_to_setting');

function add_404_page_option_to_setting()
{
  register_setting('reading', '404_page', '');
  add_settings_field('404_page', '404 Page', 'settings_field_404_page', 'reading', 'default');
}

function settings_field_404_page($args)
{
  wp_dropdown_pages(array(
    'name' => '404_page',
    'show_option_none' => '&mdash; Select &mdash;',
    'option_none_value' => '0',
    'selected' => get_option('404_page'),
  ));
}

//set custom 404 page
$_404_page_id = get_option('404_page');
if ($_404_page_id) {
  add_filter('404_template', 'redirect_404', 10, 3);

  function redirect_404($template)
  {
    global $wp_query;
    $_404_page_id = get_option('404_page');
    $posts = get_posts(array('page_id' => $_404_page_id, 'post_type' => 'page'));
    foreach ($posts as $post) {
      setup_postdata($post);
    }
    $wp_query->posts = $posts;
    $wp_query->post_count = count($posts);
    $template = get_page_template();
    return $template;
  }
}
