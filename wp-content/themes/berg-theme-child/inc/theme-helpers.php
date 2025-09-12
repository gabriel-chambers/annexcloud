<?php
//enable custom logo upload
add_theme_support('custom-logo');

function berg_customizer_setting_custom_header_script($wp_customize, $strings)
{
  extract($strings);
  
  $wp_customize->add_setting('preconnect');
  $wp_customize->add_control('preconnect', array(
      'type' => 'textarea',
      'section' => 'header_script',
      'label' => __('Front page pre-connects'),
      'settings' => 'preconnect',
      'description' => __('Add links from here.'),
  ));

  $wp_customize->add_section('header_script', array(
    'title'    => __('Header Script', 'blankslate'),
    'description' => '',
    'priority' => 110,
  ));

  $wp_customize->add_setting('header_script_one');

  $wp_customize->add_control('header_script_one', array(
    'type' => 'textarea',
    'section' => 'header_script',
    'label' => __('Header Script One'),
    'settings' => 'header_script_one',
    'description' => $add_script_str,
  ));

  $wp_customize->add_setting('header_script_two');

  $wp_customize->add_control('header_script_two', array(
    'type' => 'textarea',
    'section' => 'header_script',
    'label' => __('Header Script Two'),
    'settings' => 'header_script_two',
    'description' => $add_script_str,
  ));

  $wp_customize->add_setting('header_script_three');

  $wp_customize->add_control('header_script_three', array(
    'type' => 'textarea',
    'section' => 'header_script',
    'label' => __('Header Script Three'),
    'settings' => 'header_script_three',
    'description' => $add_script_str,
  ));

  $wp_customize->add_setting('body_script');

  $wp_customize->add_control('body_script', array(
    'type' => 'textarea',
    'section' => 'header_script',
    'label' => __('Body Script (GTM iframe code snippets)'),
    'settings' => 'body_script',
    'description' => __('Add body script from here.'),
  ));

  $wp_customize->add_section('site_footer', array(
    'title'    => __('Footer', 'blankslate'),
    'description' => '',
    'priority' => 120,
  ));

  $wp_customize->add_setting('footer_logo');
  // Add a control to upload the footer logo
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'footer_logo', array(
    'label' => 'Footer Logo',
    'section' => 'site_footer', //this is the section where the custom-logo from WordPress is
    'settings' => 'footer_logo',
  )));

  $wp_customize->add_setting('footer_logo_text');

  $wp_customize->add_control('footer_logo_text', array(
    'type' => 'textarea',
    'section' => 'site_footer',
    'label' => __('Footer Logo Text'),
    'settings' => 'footer_logo_text',
    'description' => __('Add footer logo text from here.'),
  ));

  $wp_customize->add_setting('footer_subcribe_text');

  $wp_customize->add_control('footer_subcribe_text', array(
    'type' => 'text',
    'section' => 'site_footer',
    'label' => __('Footer Subcribe Text'),
    'settings' => 'footer_subcribe_text',
    'description' => __('Add footer Subcribe text from here.'),
  ));

  $wp_customize->add_setting('footer_form_code');

  $wp_customize->add_control('footer_form_code', array(
    'type' => 'textarea',
    'section' => 'site_footer',
    'label' => __('Footer Form Code'),
    'settings' => 'footer_form_code',
    'description' => __('Add footer form code from here.'),
  ));

  $wp_customize->add_setting('copyright');
  // Add a control to upload the footer logo
  $wp_customize->add_control('title_tagline', array(
    'type' => 'text',
    'section' => 'site_footer', // Add a default or your own section
    'label' => __('Copyright'),
    'settings' => 'copyright',
    'description' => __('Add copyright text from here. *without &copy; year'),
  ));

  $wp_customize->add_setting('footer_social_pc');

  $wp_customize->add_control('footer_social_pc', array(
    'type' => 'text',
    'section' => 'site_footer',
    'label' => __('Footer Social Podcast URL'),
    'settings' => 'footer_social_pc',
    'description' => __('Add footer social podcast url from here.'),
  ));
}

function berg_customizer_setting_custom_header_footer_logo($wp_customize, $strings)
{
  extract($strings);
  $wp_customize->add_setting('footer_social_pc_logo');

  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'footer_social_pc_logo', array(
    'label' => 'Footer Social Podcast Logo',
    'section' => 'site_footer',
    'settings' => 'footer_social_pc_logo'
  )));

  $wp_customize->add_setting('footer_social_in');

  $wp_customize->add_control('footer_social_in', array(
    'type' => 'text',
    'section' => 'site_footer',
    'label' => __('Footer Social LinkedIn URL'),
    'settings' => 'footer_social_in',
    'description' => __('Add footer social LinkedIn url from here.'),
  ));

  $wp_customize->add_setting('footer_social_in_logo');

  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'footer_social_in_logo', array(
    'label' => 'Footer Social LinkedIn Logo',
    'section' => 'site_footer',
    'settings' => 'footer_social_in_logo'
  )));

  $wp_customize->add_setting('footer_social_yt');

  $wp_customize->add_control('footer_social_yt', array(
    'type' => 'text',
    'section' => 'site_footer',
    'label' => __('Footer Social Youtube URL'),
    'settings' => 'footer_social_yt',
    'description' => __('Add footer social youtube url from here.'),
  ));

  $wp_customize->add_setting('footer_social_yt_logo');

  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'footer_social_yt_logo', array(
    'label' => 'Footer Social Youtube Logo',
    'section' => 'site_footer',
    'settings' => 'footer_social_yt_logo'
  )));

  $wp_customize->add_section('footer_script', array(
    'title'    => __('Footer Script', 'blankslate'),
    'description' => '',
    'priority' => 111,
  ));

  $wp_customize->add_setting('footer_script_one');

  $wp_customize->add_control('footer_script_one', array(
    'type' => 'textarea',
    'section' => 'footer_script',
    'label' => __('Footer Script One'),
    'settings' => 'footer_script_one',
    'description' => $add_script_str,
  ));

  // 404 page Settings
  $wp_customize->add_section($section_404_settings, [
    'title' => __('404 Page Settings', 'blankslate'),
    'description' => '',
    'priority' => 121,
  ]);
  $wp_customize->add_setting($image_404);
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "404_image", [
    'label' => $image_404,
    'section' => $section_404_settings,
    'settings' => $image_404
  ]));
  $wp_customize->add_setting($background_image_404);
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $background_image_404, [
    'label' => "404 Background Image",
    'section' => $section_404_settings,
    'settings' => $background_image_404
  ]));
  $wp_customize->add_setting($title_404);
  $wp_customize->add_control($title_404, [
    'type' => "text",
    'section' => $section_404_settings,
    'label' => __("Title"),
    'settings' => $title_404,
    'description' => __("Add 404 page title here."),
  ]);
  $wp_customize->add_setting($sub_title_404);
  $wp_customize->add_control($sub_title_404, [
    'type' => "text",
    'section' => $section_404_settings,
    'label' => __("Sub Title"),
    'settings' => $sub_title_404,
    'description' => __("Add 404 page sub title here."),
  ]);
  $wp_customize->add_setting($description_404);
  $wp_customize->add_control($description_404, [
    'type' => "textarea",
    'section' => $section_404_settings,
    'label' => __("Page Not Found Descriptions"),
    'settings' => $description_404,
    'description' => __("Add page not found descriptions from here."),
  ]);
  $wp_customize->add_setting($button_text_404);
  $wp_customize->add_control($button_text_404, [
    'type' => "text",
    'section' => $section_404_settings,
    'label' => __("Button Text"),
    'settings' => $button_text_404,
    'description' => __("Add 404 page button text here."),
  ]);
  $wp_customize->add_setting($button_link_404);
  $wp_customize->add_control($button_link_404, [
    'type' => "text",
    'section' => $section_404_settings,
    'label' => __("Button Link"),
    'settings' => $button_link_404,
    'description' => __("Add 404 page button link here."),
  ]);
}

function berg_customizer_setting_custom($wp_customize)
{
  $strings = [
    'add_script_str' => __('Add script from here.'),
    'section_404_settings' => '404_page_settings',
    'image_404' => '404_image',
    'background_image_404' => '404_background_image',
    'title_404' => '404_title',
    'sub_title_404' => '404_sub_title',
    'description_404' => '404_descriptions',
    'button_text_404' => '404_button_text',
    'button_link_404' => '404_button_link',
  ];
  berg_customizer_setting_custom_header_script($wp_customize, $strings);
  berg_customizer_setting_custom_header_footer_logo($wp_customize, $strings);
}
add_action('customize_register', 'berg_customizer_setting_custom', 11);
