<?php

function register_my_menus_custom()
{
    register_nav_menus(
        [
            'main-menu' => __('Main Menu'),
           // 'top-menu' => __('Top Menu'),
            'request-demo-menu' => __('Request a Demo Menu'),
            'footer-menu-one' => __('Footer Menu One'),
            'footer-menu-two' => __('Footer Menu Two'),
            'footer-menu-three' => __('Footer Menu three'),
            'bottom-menu' => __('Bottom Menu'),
            'login-menu' => __('Login Menu'),
           // 'marketplace-inner-menu' => __('Marketplace Inner Menu'),
        ]
    );
}

add_action('init', 'register_my_menus_custom');
