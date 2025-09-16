<?php

/**
 *  Register e25m/slider-accordion
 *
 * @return void
 */
function slider_accordion_panels()
{
	$attributes = json_decode(
		file_get_contents(__DIR__ . '/attributes.json'),
		true
	);
	register_block_type(
		'e25m/slider-accordion',
		[
			'apiVersion' => 2,
			'render_callback' => 'slider_accordion_html',
			'attributes' => $attributes
		]
	);
}
add_action('init', 'slider_accordion_panels');

/**
 * Render Accordion save content
 *
 * @param  array $attributes
 * @param  string $content
 * @param  array $innerBlocks
 * @return string
 */

function slider_accordion_html($attributes, $content, $inner_blocks)
{
	if (!$attributes['visibility']) {
		return;
	}

	$accordion_blocks = $inner_blocks->parsed_block['innerBlocks'] ?? [];
	$content = [];
	$accordion_id = 'accordion_' . bin2hex(random_bytes(6));
	$accordion_class_names_array = $attributes['accordionClassNames']  ?? [];
	$accordion_classes = '';
	foreach ($accordion_class_names_array as $accordion_class_name) {
		$accordion_classes .= $accordion_class_name['value'] . ' ';
	}

	$progressbar_enabled = $attributes['sliderEnabled'] && isset($attributes['enableProgressBar']) && $attributes['enableProgressBar'];
	if ($progressbar_enabled) {
		$accordion_classes .= 'bs-slider-accordion--progress-bar';
	}

	foreach ($accordion_blocks as $accordion) {
		// Ignoring all the hidden cards
		if (array_key_exists("showCard", $accordion['attrs']) && $accordion['attrs']['showCard'] == false) {
			continue;
		}
		$data = [
			'id' => 'panel_' . bin2hex(random_bytes(6)),
			'heading' => $accordion['attrs']['colHeading'] ?? '',
			'headingIcon' => $accordion['attrs']['colImgURL'] ?? '',
			'headingIconAlt' => $accordion['attrs']['colImgAlt'] ?? '',
			'titleTag' => $accordion['attrs']['titleTag'] ?? 'span'
		];

		foreach ($accordion['innerBlocks'] as $inner_block) {
			if ($inner_block['blockName'] == 'e25m/slider-accordion-body-left') {
				$data['left'] = $inner_block['innerBlocks'];
			} else if ($inner_block['blockName'] == 'e25m/slider-accordion-body-right') {
				$data['right'] = $inner_block['innerBlocks'];
			}
		}

		$content[] = $data;
	}

	$accordion_blocks = $inner_blocks->parsed_block['innerBlocks'] ?? [];
	$fadeIn = ($attributes['fadeIn']) ? $attributes['fadeIn'] : false;

	$html_out = '<div id="' . $accordion_id . '_container" class="bs-slider-accordion ' . $accordion_classes . '"  data-attributes=\'' . json_encode($attributes, JSON_HEX_QUOT | JSON_HEX_TAG) . '\'>';

	//Setting translate3d css value if all slides are visible
	if ($attributes['sliderEnabled'] && count($content) == $attributes['slidesToShow']) {
		$html_out .= '<style>';
		$html_out .= '#' . $accordion_id . ' .slick-track {';
		$html_out .= '        transform: translate3d(0px, 0px, 0px) !important;';
		$html_out .= '    }';
		$html_out .= '</style>';
	}

	$slider_options = [
		'slidesToShow' => $attributes['slidesToShow'],
		'slidesToScroll' => $attributes['slidesToScroll'],
		'arrows' => $attributes['slideArrows'],
		'dots' => $attributes['slideDots'],
		'autoplay' => $attributes['autoplay'],
		'autoplaySpeed' => $attributes['autoplaySpeed'],
		'variableWidth' => $attributes['variableWidth'],
		'infinite' => $attributes['infinite'],
		'initialSlide' => $attributes['defaultOpenIndex'] ?? 0,
		'asNavFor' => "#{$accordion_id}_navigation_container",
		'focusOnSelect' =>  $attributes['focusOnSelect'],
	];

	$content_slider_options = [
		'arrows' => false,
		'autoplay' => $attributes['autoplay'],
		'autoplaySpeed' => $attributes['autoplaySpeed'],
		'infinite' => $attributes['infinite'],
		'initialSlide' => $attributes['defaultOpenIndex'] ?? 0,
		'asNavFor' => "#{$accordion_id}",
		'focusOnSelect' => true,
	];

	$responsive_array = [["breakpoint" => $attributes['breakpoint'], "settings" => "unslick"]];
	$slider_options["responsive"] = $responsive_array;
	$content_slider_options["responsive"] = $responsive_array;

	$slick_data_attr = $attributes['sliderEnabled'] ? 'data-slick=' . json_encode($slider_options) : '';

	$slick_parent_class = $attributes['sliderEnabled'] ? 'bs-slider-accordion-slick' : '';
	$html_out .= "<div class='$slick_parent_class' id='$accordion_id' data-fadein='$fadeIn' $slick_data_attr >";

	foreach ($content as $key => $content_block) {
		[$show, $expanded] = getSliderContentDisplayProperties(
			$key,
			$attributes['defaultOpenIndex'] ?? 0
		);

		$activeClass = ($expanded === 'true') ? 'bs-active' : '';
		$html_out .= '<div class="card ' . $activeClass . '">';
		$html_out .= '  <div class="card-header accordion__block__btn" id="' . $content_block['id'] . '_header"
		data-toggle="collapse"
        data-target="#' . $content_block['id'] . '"
        aria-expanded="' . $expanded . '"
        aria-controls="' . $content_block['id'] . '"
        >';

		$html_out .= $content_block['headingIcon'] ? '<img class="card-header__icon" src="' . $content_block['headingIcon'] . '" alt="' . $content_block['headingIconAlt'] . '">' : "";
		$html_out .= '<'.$content_block['titleTag'].'>' . $content_block['heading'] ?? '' . '</'.$content_block['titleTag'].'>';
		$html_out .= '</div>';
		$html_out .= '<div
                    id="' . $content_block['id'] . '"
                    class="card-panel collapse ' . $show . '"
                    aria-labelledby="' . $content_block['id'] . '_header"
                    data-parent="#' . $accordion_id . '"
                >';
		$html_out .= '<div class="card-body">';


		foreach ($content_block['right'] as $right_block) {
			$html_out .= render_block($right_block);
		}
		$html_out .= '</div></div>';
		$html_out .= '</div>';
	}
	$html_out .= '</div>';

	$content_slick_data_attr = $attributes['sliderEnabled'] ? 'data-slick=' . json_encode($content_slider_options) : '';

	$html_out .= '<div class="bs-slider-accordion__floating-panel-container">';
	foreach ($content as $key => $content_block) {
		[$show, $expanded] = getSliderContentDisplayProperties(
			$key,
			$attributes['defaultOpenIndex'] ?? 0
		);

		$html_out .= '<div class="bs-slider-accordion__floating-panel ' . $show . ' bs-visibility-hidden" data-parent="#' . $content_block['id'] . '">';

		foreach ($content_block['left'] as $left_block) {
			$html_out .= render_block($left_block);
		}

		$html_out .= '</div>';
	}
	$html_out .= '</div>';

	//This is used to create a sepearate navigation slider to the existing slider since we need the behaviour of the Tab Slider
	$html_out .= '<div id="' . $accordion_id . '_navigation_container" class="bs-slider-accordion__navigation-panel-container"' . $content_slick_data_attr . '>';
	foreach ($content as $key => $content_block) {
		$html_out .= '<div class="bs-slider-accordion__navigation-panel"></div>';
	}
	$html_out .= '</div>';

	$html_out .= '</div>';
	return $html_out;
}

/**
 *
 * @param   int  $key
 * @param   int  $defaultKey
 *
 * @return  array
 */
function getSliderContentDisplayProperties($key, $defaultKey)
{
	$show = '';
	$aria_expanded = 'false';

	if ($key == $defaultKey) {
		$show = 'show';
		$aria_expanded = 'true';
	}

	return [
		$show,
		$aria_expanded
	];
}
