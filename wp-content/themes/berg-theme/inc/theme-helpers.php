<?php
//enable custom logo upload
add_theme_support('custom-logo');

if (!function_exists('berg_customizer_setting')) {
    function berg_customizer_setting($wp_customize)
    {
        // Add a setting
        $wp_customize->add_setting('secondary_logo');
        // Add a control to upload the secondary logo
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'secondary_logo', array(
            'label' => 'Secondary Logo',
            'section' => 'title_tagline', //this is the section where the custom-logo from WordPress is
            'settings' => 'secondary_logo',
            'priority' => 8 // show it just below the custom-logo
        )));

        $wp_customize->add_setting('footer_logo');
        // Add a control to upload the footer logo
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'footer_logo', array(
            'label' => 'Footer Logo',
            'section' => 'title_tagline', //this is the section where the custom-logo from WordPress is
            'settings' => 'footer_logo',
            'priority' => 8 // show it just below the custom-logo
        )));

        $wp_customize->add_setting('copyright');
        // Add a control to upload the footer logo
        $wp_customize->add_control('title_tagline', array(
            'type' => 'text',
            'section' => 'title_tagline', // Add a default or your own section
            'label' => __('Copyright'),
            'settings' => 'copyright',
            'description' => __('Add copyright text from here. *without &copy; year'),
        ));

        // 404 page Settings
        $wp_customize->add_section('404_page_settings', [
            'title' => __('404 Page Settings', 'blankslate'),
            'description' => '',
            'priority' => 121,
        ]);
        $wp_customize->add_setting("404_image");
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "404_image", [
            'label' => "404 Image",
            'section' => "404_page_settings",
            'settings' => "404_image"
        ]));
        $wp_customize->add_setting("404_title");
        $wp_customize->add_control("404_title", [
            'type' => "text",
            'section' => "404_page_settings",
            'label' => __("Title"),
            'settings' => "404_title",
            'description' => __("Add 404 page title here."),
        ]);
        $wp_customize->add_setting("404_sub_title");
        $wp_customize->add_control("404_sub_title", [
            'type' => "text",
            'section' => "404_page_settings",
            'label' => __("Sub Title"),
            'settings' => "404_sub_title",
            'description' => __("Add 404 page sub title here."),
        ]);
        $wp_customize->add_setting("404_button_text");
        $wp_customize->add_control("404_button_text", [
            'type' => "text",
            'section' => "404_page_settings",
            'label' => __("Button Text"),
            'settings' => "404_button_text",
            'description' => __("Add 404 page button text here."),
        ]);
        $wp_customize->add_setting("404_button_link");
        $wp_customize->add_control("404_button_link", [
            'type' => "text",
            'section' => "404_page_settings",
            'label' => __("Button Link"),
            'settings' => "404_button_link",
            'description' => __("Add 404 page button link here."),
        ]);

        // Berg breakpoint settings
        $wp_customize->add_section('breakpoint_settings', [
            'title' => __('Site Breakpoints', 'blankslate'),
            'description' => __("These will be the default desktop & mobile breakpoints for all the Berg blocks"),
            'priority' => 122,
        ]);
        $wp_customize->add_setting("desktop_breakpoint");
        $wp_customize->add_control("desktop_breakpoint", [
            'type' => "number",
            'section' => "breakpoint_settings",
            'label' => __("Desktop Breakpoint (in pixels)"),
            'settings' => "desktop_breakpoint",
            'description' => __("Default value: 1280 pixels"),
        ]);
        $wp_customize->add_setting("mobile_breakpoint");
        $wp_customize->add_control("mobile_breakpoint", [
            'type' => "number",
            'section' => "breakpoint_settings",
            'label' => __("Mobile Breakpoint (in pixels)"),
            'settings' => "mobile_breakpoint",
            'description' => __("Default value: 576 pixels"),
        ]);
    }
    add_action('customize_register', 'berg_customizer_setting');
}

// Asigning the Theme customizer values as global javascript variables for the block editor script
if (!function_exists('add_theme_settings_to_block_editor')) {

    function add_theme_settings_to_block_editor()
    {
        // 'berg-block-js' is the handle name used in Berg plugin to enqueue the block editor script
        wp_localize_script(
            'berg-block-js',
            'bergThemeData',
            array(
                'desktopBreakpoint' => get_theme_mod('desktop_breakpoint') > 0 ? get_theme_mod('desktop_breakpoint') : 1280,
                'mobileBreakpoint' => get_theme_mod('mobile_breakpoint') > 0 ? get_theme_mod('mobile_breakpoint') : 576,
            )
        );
    }
    add_action('enqueue_block_editor_assets', 'add_theme_settings_to_block_editor');
}
