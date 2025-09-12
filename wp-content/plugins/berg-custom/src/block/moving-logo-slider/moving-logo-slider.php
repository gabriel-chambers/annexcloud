<?php
function register_annex_moving_logo_slider()
{
	register_block_type('e25m-custom/moving-logo-slider', [
		'editor_script' => 'berg-block-js-vendor',
		'editor_style' => 'berg-block-editor-css',
		'style' => 'e25m-style-css',
		'render_callback' => 'moving_annex_logo_slider_render_callback',
		'attributes' => [
			'images' => [
				'type' => 'array',
				'default' => [],
			],
			'direction' => [
				'type' => 'string',
				'default' => "left",
			],
			'speed' => [
				'type' => 'string',
				'default' => "4000",
			],
		]
	]);
}

add_action('init', 'register_annex_moving_logo_slider');

function moving_annex_logo_slider_render_callback($block_attributes)
{
	$images = $block_attributes['images'];
	$direction = $block_attributes['direction'];
	$speed = $block_attributes['speed'];

	ob_start();

	include 'view/layout.php';

	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
