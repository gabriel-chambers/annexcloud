<?php

if (!function_exists('register_my_menus')) {
	function register_my_menus()
	{
		register_nav_menus(
			array(
				'main-menu' => __('Main Menu'),
			)
		);
	}
	add_action('init', 'register_my_menus');
}
