<?php
/*
* To add CMS guide doc settings to theme option
*/

define('CMS_GUIDE_TEXT', 'CMS Guide');

function cms_guide_doc_theme_options($wp_customize)
{
$wp_customize->add_section('cms_guide_doc', [
'title'    => CMS_GUIDE_TEXT,
'priority' => 200,
]);

$wp_customize->add_setting('cms_guide_label_text', ['default'  => CMS_GUIDE_TEXT]);
$wp_customize->add_control('cms_guide_label_text', [
'type' => 'text',
'section' => 'cms_guide_doc',
'label' => 'CMS Guide Label Text',
'settings' => 'cms_guide_label_text'
]);

$wp_customize->add_setting('cms_guide_doc_url');
$wp_customize->add_control('cms_guide_doc_url', [
'type' => 'text',
'section' => 'cms_guide_doc',
'label' => 'CMS Guide Link',
'settings' => 'cms_guide_doc_url',
]);
}

add_action('customize_register', 'cms_guide_doc_theme_options');

/*
* To add admin dashboard top menu bar item to CMS Guide
*/
function custom_wp_toolbar_link($wp_admin_bar)
{
global $wp_admin_bar;
$cms_guide_link = get_theme_mod('cms_guide_doc_url');
$cms_guide_label_text = get_theme_mod('cms_guide_label_text', CMS_GUIDE_TEXT);
$args = array(
'id' => 'cms-guide',
'title' => '
<span class="ab-icon dashicons-before dashicons-book"></span>
<span class="ab-label">' . $cms_guide_label_text . '</span>',
'href' => $cms_guide_link,
'meta' => array(
'target' => '_blank',
'class' => 'cms-guide',
)
);
if ($cms_guide_link) {
$wp_admin_bar->add_menu($args);
}
}
add_action('admin_bar_menu', 'custom_wp_toolbar_link', 999);

/*
* To add an admin dashboard left panel menu item for the CMS Guide
*/
function admin_menu_add_cms_guide_link()
{
global $submenu;
$cms_guide_link = get_theme_mod('cms_guide_doc_url');
$cms_guide_label_text = get_theme_mod('cms_guide_label_text', CMS_GUIDE_TEXT);

$menu_pos = 999;

if ($cms_guide_link) {
add_menu_page('external_link', $cms_guide_label_text, 'read', $cms_guide_link, '', 'dashicons-book', $menu_pos);
}
}
add_action('admin_menu', 'admin_menu_add_cms_guide_link');

/*
* To pass the CMS guide meta values to the admin javascript file
*/
function localize_variables_to_admin_scripts()
{
wp_localize_script(
'child_admin_scripts',
'cms_guide_meta',
array(
'guideLink' => trim(get_theme_mod('cms_guide_doc_url'), '/'),
'guideLabelText' => get_theme_mod('cms_guide_label_text', CMS_GUIDE_TEXT)
)
);
}
add_action('admin_enqueue_scripts', 'localize_variables_to_admin_scripts');
