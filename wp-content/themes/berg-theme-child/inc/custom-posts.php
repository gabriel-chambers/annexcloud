<?php

$GLOBALS['repeating_strings'] = [
'add_new' => 'Add New',
'event_location_str' => 'Event Location',
'event_type_str' => 'Event Type',
'search_type_str' => 'Search Type',
'all_type_str' => 'All Type',
'parent_type_str' => 'Parent Type',
'edit_type_str' => 'Edit Type',
'update_type_str' => 'Update Type',
'add_new_type_str' => 'Add New Type',
'new_type_str' => 'New Type',
'resource_type_str' => 'Resource Type',
'resource_category_str' => 'Resource Category',
'parent_category_str' => 'Parent Category',
'edit_category_str' => 'Edit Category',
'update_category_str' => 'Update Category',
'add_new_category_str' => 'Add New Category',
'new_category_str' => 'New Category',
'resource_industry_str' => 'Resource Industry',
'parent_industry_str' => 'Parent Industry',
'edit_industry_str' => 'Edit Industry',
'update_industry_str' => 'Update Industry',
'add_new_industry_str' => 'Add New Industry',
'new_industry_str' => 'New Industry',
'news_category_str' => 'News Category',
'news_year_str' => 'News Year',
'news_industry_str' => 'News Industry',
'use_case_category_str' => 'Use Case Category',
'use_case_functionality_str' => 'Use Case Functionality',
'use_case_industry_str' => 'Use Case Industry',
'integration_type_str' => 'Integration Type',
'marketplace_type_str' => 'Marketplace Type',
];

if (!function_exists('add_event_post_type')) {
function add_event_post_type()
{
global $repeating_strings;
extract($repeating_strings);
$args = array(
'labels'             => array(
'name'               => 'Events',
'singular_name'      => 'Event',
'add_new'            => $add_new,
'add_new_item'       => $add_new,
'edit'               => 'Edit',
'edit_item'          => 'Edit',
'new_item'           => 'New',
'view'               => 'View',
'view_item'          => 'View',
'search_items'       => 'Search',
'not_found'          => 'No Event found',
'not_found_in_trash' => 'No Event found in Trash'
),
'public'             => true,
'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields'),
'menu_icon'          => 'dashicons-archive',
'taxonomies'         => array(),
'publicly_queryable' => true,
'show_ui'            => true,
'show_in_rest'       => true,
'show_in_menu'       => true,
'query_var'          => true,
'rewrite'            => array('with_front' => false, 'slug' => 'events'),
'capability_type'    => 'post',
'has_archive'        => false
);
register_post_type('events', $args);
register_taxonomy(
'event-location',
array('events'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $event_location_str,
'menu_name'         => $event_location_str,
'singular_name'     => $event_location_str,
'search_items'      => 'Search Locations',
'all_items'         => 'All Locations',
'parent_item'       => 'Parent Location',
'parent_item_colon' => 'Parent Location:',
'edit_item'         => 'Edit Location',
'update_item'       => 'Update Location',
'add_new_item'      => 'Add New Location',
'new_item_name'     => 'New Location',
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'event-location')
)
);
register_taxonomy(
'event-type',
array('events'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $event_type_str,
'menu_name'         => $event_type_str,
'singular_name'     => $event_type_str,
'search_items'      => $search_type_str,
'all_items'         => $all_type_str,
'parent_item'       => $parent_type_str,
'parent_item_colon' => $parent_type_str . ':',
'edit_item'         => $edit_type_str,
'update_item'       => $update_type_str,
'add_new_item'      => $add_new_type_str,
'new_item_name'     => $new_type_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'event-type')
)
);
}
add_action('after_setup_theme', 'add_event_post_type');
}


if (!function_exists('add_careers_post_type')) {
function add_careers_post_type()
{
global $repeating_strings;
extract($repeating_strings);
$args = array(
'labels'             => array(
'name'               => 'Careers',
'singular_name'      => 'Career',
'add_new'            => $add_new,
'add_new_item'       => $add_new,
'edit'               => 'Edit',
'edit_item'          => 'Edit',
'new_item'           => 'New',
'view'               => 'View',
'view_item'          => 'View',
'search_items'       => 'Search',
'not_found'          => 'No Event found',
'not_found_in_trash' => 'No Careers found in Trash'
),
'public'             => true,
'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields'),
'menu_icon'          => 'dashicons-welcome-learn-more',
'taxonomies'         => array(),
'publicly_queryable' => true,
'show_ui'            => true,
'show_in_rest'       => true,
'show_in_menu'       => true,
'query_var'          => true,
'rewrite'            => array('with_front' => false, 'slug' => 'careers'),
'capability_type'    => 'post',
'has_archive'        => false
);
register_post_type('careers', $args);
register_taxonomy(
'department',
array('careers'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => 'Department',
'menu_name'         => 'Department',
'singular_name'     => 'Departments',
'search_items'      => 'Search Departments',
'all_items'         => 'All Departments',
'parent_item'       => 'Parent Department',
'parent_item_colon' => 'Parent Department:',
'edit_item'         => 'Edit Department',
'update_item'       => 'Update Department',
'add_new_item'      => 'Add New Department',
'new_item_name'     => 'New Department',
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'department')
)
);
register_taxonomy(
'contract-type',
array('careers'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => 'Contract Types',
'menu_name'         => 'Contract Types',
'singular_name'     => 'Contract Type',
'search_items'      => 'Search Contract Types',
'all_items'         => 'All Contract Types',
'parent_item'       => 'Parent Contract Type',
'parent_item_colon' => 'Parent Contract Type:',
'edit_item'         => 'Edit Contract Type',
'update_item'       => 'Update Contract Type',
'add_new_item'      => 'Add New Contract Type',
'new_item_name'     => 'New Contract Types',
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'contract-type')
)
);
register_taxonomy(
'job-location',
array('careers'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => 'Locations',
'menu_name'         => 'Locations',
'singular_name'     => 'Location',
'search_items'      => 'Search Locations',
'all_items'         => 'All Locations',
'parent_item'       => 'Parent Location',
'parent_item_colon' => 'Parent Location:',
'edit_item'         => 'Edit Location',
'update_item'       => 'Update Location',
'add_new_item'      => 'Add New Location',
'new_item_name'     => 'New Location',
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'job-location')
)
);
}
add_action('after_setup_theme', 'add_careers_post_type');
}

if (!function_exists('add_resource_post_type')) {
function add_resource_post_type()
{
global $repeating_strings;
extract($repeating_strings);
$args = array(
'labels'             => array(
'name'               => 'Resources',
'singular_name'      => 'Resource',
'add_new'            => $add_new,
'add_new_item'       => $add_new,
'edit'               => 'Edit',
'edit_item'          => 'Edit',
'new_item'           => 'New',
'view'               => 'View',
'view_item'          => 'View',
'search_items'       => 'Search',
'not_found'          => 'No Resource found',
'not_found_in_trash' => 'No Resource found in Trash'
),
'public'             => true,
'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields'),
'menu_icon'          => 'dashicons-networking',
'taxonomies'         => array(),
'publicly_queryable' => true,
'show_ui'            => true,
'show_in_rest'       => true,
'show_in_menu'       => true,
'query_var'          => true,
'rewrite'            => array('with_front' => false, 'slug' => 'resources'),
'capability_type'    => 'post',
'has_archive'        => false
);
register_post_type('resource', $args);
register_taxonomy(
'resource-category',
array('resource'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $resource_category_str,
'menu_name'         => $resource_category_str,
'singular_name'     => $resource_category_str,
'search_items'      => 'Search Categories',
'all_items'         => 'All Categories',
'parent_item'       => $parent_category_str,
'parent_item_colon' => $parent_category_str . ':',
'edit_item'         => $edit_category_str,
'update_item'       => $update_category_str,
'add_new_item'      => $add_new_category_str,
'new_item_name'     => $new_category_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'resource-category')
)
);
register_taxonomy(
'resource-type',
array('resource'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $resource_type_str,
'menu_name'         => $resource_type_str,
'singular_name'     => $resource_type_str,
'search_items'      => $search_type_str,
'all_items'         => $all_type_str,
'parent_item'       => $parent_type_str,
'parent_item_colon' => $parent_type_str . ':',
'edit_item'         => $edit_type_str,
'update_item'       => $update_type_str,
'add_new_item'      => $add_new_type_str,
'new_item_name'     => $new_type_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'resource-type')
)
);
register_taxonomy(
'resource-industry',
array('resource'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $resource_industry_str,
'menu_name'         => $resource_industry_str,
'singular_name'     => $resource_industry_str,
'search_items'      => 'Search Industry',
'all_items'         => 'All Industry',
'parent_item'       => $parent_industry_str,
'parent_item_colon' => $parent_industry_str . ':',
'edit_item'         => $edit_industry_str,
'update_item'       => $update_industry_str,
'add_new_item'      => $add_new_industry_str,
'new_item_name'     => $new_industry_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'resource-industry')
)
);
}
add_action('after_setup_theme', 'add_resource_post_type');
}

function add_tags_categories()
{
register_taxonomy_for_object_type('post_tag', 'resource');
}
add_action('init', 'add_tags_categories');

if (!function_exists('add_news_post_type')) {
function add_news_post_type()
{
global $repeating_strings;
extract($repeating_strings);
$args = array(
'labels'             => array(
'name'               => 'News',
'singular_name'      => 'News',
'add_new'            => $add_new,
'add_new_item'       => $add_new,
'edit'               => 'Edit',
'edit_item'          => 'Edit',
'new_item'           => 'New',
'view'               => 'View',
'view_item'          => 'View',
'search_items'       => 'Search',
'not_found'          => 'No News found',
'not_found_in_trash' => 'No News found in Trash'
),
'public'             => true,
'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields'),
'menu_icon'          => 'dashicons-media-document',
'taxonomies'         => array(),
'publicly_queryable' => true,
'show_ui'            => true,
'show_in_rest'       => true,
'show_in_menu'       => true,
'query_var'          => true,
'rewrite'            => array('with_front' => false, 'slug' => 'press-releases'),
'capability_type'    => 'post',
'has_archive'        => false
);
register_post_type('news', $args);
register_taxonomy(
'news-category',
array('news'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $news_category_str,
'menu_name'         => $news_category_str,
'singular_name'     => $news_category_str,
'search_items'      => 'Search News Category',
'all_items'         => 'All News Categories',
'parent_item'       => 'Parent News Category',
'parent_item_colon' => $parent_category_str . ':',
'edit_item'         => $edit_category_str,
'update_item'       => $update_category_str,
'add_new_item'      => $add_new_category_str,
'new_item_name'     => $new_category_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite' => array('slug' => 'news-category')
)
);
register_taxonomy(
'news-year',
array('news'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $news_year_str,
'menu_name'         => $news_year_str,
'singular_name'     => $news_year_str,
'search_items'      => 'Search News Year',
'all_items'         => 'All News Year',
'parent_item'       => 'Parent News Year',
'parent_item_colon' => 'Parent Year:',
'edit_item'         => 'Edit Year',
'update_item'       => 'Update Year',
'add_new_item'      => 'Add New Year',
'new_item_name'     => 'New Year',
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite' => array('slug' => 'news-year')
)
);
register_taxonomy(
'news-industry',
array('news'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $news_industry_str,
'menu_name'         => $news_industry_str,
'singular_name'     => $news_industry_str,
'search_items'      => 'Search News Industry',
'all_items'         => 'All News Industry',
'parent_item'       => 'Parent News Industry',
'parent_item_colon' => $parent_industry_str . ':',
'edit_item'         => $edit_industry_str,
'update_item'       => $update_industry_str,
'add_new_item'      => $add_new_industry_str,
'new_item_name'     => $new_industry_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite' => array('slug' => 'news-industry')
)
);
}
add_action('after_setup_theme', 'add_news_post_type');
}

if (!function_exists('add_leadership_post_type')) {
function add_leadership_post_type()
{
global $repeating_strings;
extract($repeating_strings);
$args = array(
'labels'             => array(
'name'               => 'Leadership',
'singular_name'      => 'Leadership',
'add_new'            => $add_new,
'add_new_item'       => $add_new,
'edit'               => 'Edit',
'edit_item'          => 'Edit',
'new_item'           => 'New',
'view'               => 'View',
'view_item'          => 'View',
'search_items'       => 'Search',
'not_found'          => 'No Leadership found',
'not_found_in_trash' => 'No Leadership found in Trash'
),
'public'             => true,
'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields'),
'menu_icon'          => 'dashicons-groups',
'taxonomies'         => array(),
'publicly_queryable' => true,
'show_ui'            => true,
'show_in_rest'       => true,
'show_in_menu'       => true,
'query_var'          => true,
'rewrite'            => array('with_front' => false, 'slug' => 'leadership'),
'capability_type'    => 'post',
'has_archive'        => false
);
register_post_type('leadership', $args);
register_taxonomy(
'leadership-types',
array('leadership'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => 'Leadership Types',
'menu_name'         => 'Leadership Types',
'singular_name'     => 'Leadership Type',
'search_items'      => 'Search Leadership Types',
'all_items'         => 'All Leadership Types',
'parent_item'       => 'Parent Leadership Type',
'parent_item_colon' => 'Parent Leadership Type:',
'edit_item'         => 'Edit Leadership Type',
'update_item'       => 'Update Leadership Type',
'add_new_item'      => 'Add New Leadership Type',
'new_item_name'     => 'New Leadership Types',
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'leadership-type')
)
);
}
add_action('after_setup_theme', 'add_leadership_post_type');
}

if (!function_exists('add_usecases_post_type')) {
function add_usecases_post_type()
{
global $repeating_strings;
extract($repeating_strings);
$args = array(
'labels'             => array(
'name'               => 'Use Cases',
'singular_name'      => 'Use Case',
'add_new'            => $add_new,
'add_new_item'       => $add_new,
'edit'               => 'Edit',
'edit_item'          => 'Edit',
'new_item'           => 'New',
'view'               => 'View',
'view_item'          => 'View',
'search_items'       => 'Search',
'not_found'          => 'No Use Case found',
'not_found_in_trash' => 'No Use Case found in Trash'
),
'public'             => true,
'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields'),
'menu_icon'          => 'dashicons-feedback',
'taxonomies'         => array(),
'publicly_queryable' => true,
'show_ui'            => true,
'show_in_rest'       => true,
'show_in_menu'       => true,
'query_var'          => true,
'rewrite'            => array('with_front' => false, 'slug' => 'use-cases'),
'capability_type'    => 'post',
'has_archive'        => false
);
register_post_type('usecases', $args);
register_taxonomy(
'usecases-category',
array('usecases'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $use_case_category_str,
'menu_name'         => $use_case_category_str,
'singular_name'     => $use_case_category_str,
'search_items'      => 'Search Categories',
'all_items'         => 'All Categories',
'parent_item'       => $parent_category_str,
'parent_item_colon' => $parent_category_str . ':',
'edit_item'         => $edit_category_str,
'update_item'       => $update_category_str,
'add_new_item'      => $add_new_category_str,
'new_item_name'     => $new_category_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'usecase-category')
)
);
register_taxonomy(
'usecase-functionality',
array('usecases'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $use_case_functionality_str,
'menu_name'         => $use_case_functionality_str,
'singular_name'     => $use_case_functionality_str,
'search_items'      => 'Search Functionality',
'all_items'         => 'All Functionality',
'parent_item'       => 'Parent Functionality',
'parent_item_colon' => 'Parent Functionality:',
'edit_item'         => 'Edit Functionality',
'update_item'       => 'Update Functionality',
'add_new_item'      => 'Add New Functionality',
'new_item_name'     => 'New Functionality',
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'usecase-functionality')
)
);
register_taxonomy(
'usecases-industry',
array('usecases'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $use_case_industry_str,
'menu_name'         => $use_case_industry_str,
'singular_name'     => $use_case_industry_str,
'search_items'      => 'Search Industry',
'all_items'         => 'All Industry',
'parent_item'       => $parent_industry_str,
'parent_item_colon' => $parent_industry_str . ':',
'edit_item'         => $edit_industry_str,
'update_item'       => $update_industry_str,
'add_new_item'      => $add_new_industry_str,
'new_item_name'     => $new_industry_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'usecase-industry')
)
);
}
add_action('after_setup_theme', 'add_usecases_post_type');
}

if (!function_exists('add_integration_post_type')) {
function add_integration_post_type()
{
global $repeating_strings;
extract($repeating_strings);
$args = array(
'labels'             => array(
'name'               => 'Integrations',
'singular_name'      => 'Integration',
'add_new'            => $add_new,
'add_new_item'       => $add_new,
'edit'               => 'Edit',
'edit_item'          => 'Edit',
'new_item'           => 'New',
'view'               => 'View',
'view_item'          => 'View',
'search_items'       => 'Search',
'not_found'          => 'No Integration found',
'not_found_in_trash' => 'No Integration found in Trash'
),
'public'             => true,
'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields'),
'menu_icon'          => 'dashicons-admin-site',
'taxonomies'         => array(),
'publicly_queryable' => true,
'show_ui'            => true,
'show_in_rest'       => true,
'show_in_menu'       => true,
'query_var'          => true,
'rewrite'            => array('with_front' => false, 'slug' => 'integration'),
'capability_type'    => 'post',
'has_archive'        => false
);
register_post_type('integration', $args);
register_taxonomy(
'integration-type',
array('integration'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $integration_type_str,
'menu_name'         => $integration_type_str,
'singular_name'     => $integration_type_str,
'search_items'      => $search_type_str,
'all_items'         => $all_type_str,
'parent_item'       => $parent_type_str,
'parent_item_colon' => $parent_type_str . ':',
'edit_item'         => $edit_type_str,
'update_item'       => $update_type_str,
'add_new_item'      => $add_new_type_str,
'new_item_name'     => $new_type_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'integration-type')
)
);
}
add_action('after_setup_theme', 'add_integration_post_type');
}

if (!function_exists('add_marketplace_post_type')) {
function add_marketplace_post_type()
{
global $repeating_strings;
extract($repeating_strings);
$args = array(
'labels'             => array(
'name'               => 'Marketplace',
'singular_name'      => 'Marketplace',
'add_new'            => $add_new,
'add_new_item'       => $add_new,
'edit'               => 'Edit',
'edit_item'          => 'Edit',
'new_item'           => 'New',
'view'               => 'View',
'view_item'          => 'View',
'search_items'       => 'Search',
'not_found'          => 'No Marketplace found',
'not_found_in_trash' => 'No Marketplace found in Trash'
),
'public'             => true,
'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields'),
'menu_icon'          => 'dashicons-megaphone',
'taxonomies'         => array(),
'publicly_queryable' => true,
'show_ui'            => true,
'show_in_rest'       => true,
'show_in_menu'       => true,
'query_var'          => true,
'rewrite'            => array('with_front' => false, 'slug' => 'marketplace'),
'capability_type'    => 'post',
'has_archive'        => false
);
register_post_type('marketplace', $args);
register_taxonomy(
'marketplace-type',
array('marketplace'),
array(
'hierarchical'      => true,
'labels'            => array(
'name'              => $marketplace_type_str,
'menu_name'         => $marketplace_type_str,
'singular_name'     => $marketplace_type_str,
'search_items'      => $search_type_str,
'all_items'         => $all_type_str,
'parent_item'       => $parent_type_str,
'parent_item_colon' => $parent_type_str . ':',
'edit_item'         => $edit_type_str,
'update_item'       => $update_type_str,
'add_new_item'      => $add_new_type_str,
'new_item_name'     => $new_type_str,
),
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'query_var'         => true,
'rewrite'           => array('slug' => 'marketplace-type')
)
);
}
add_action('after_setup_theme', 'add_marketplace_post_type');
}
