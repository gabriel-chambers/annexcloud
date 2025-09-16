<?php

/**
 *  Register e25m/advance-accordion
 *
 * @return void
 */
function advance_accordion_panels()
{
	$attributes = json_decode(
		file_get_contents(__DIR__ . '/attributes.json'),
		true
	);
	register_block_type(
		'e25m/advance-accordion',
		[
			'apiVersion' => 2,
			'render_callback' => 'advance_accordion_panels_html',
			'attributes' => $attributes
		]
	);
}
add_action('init', 'advance_accordion_panels');

/**
 * Render Accordion save content
 *
 * @param  array $attributes
 * @param  string $content
 * @param  array $innerBlocks
 * @return string
 */

function advance_accordion_panels_html($attributes, $content, $inner_blocks)
{
	$accordion_blocks = $inner_blocks->parsed_block['innerBlocks'] ?? [];
	$content = [];
	$accordion_id = 'accordion_' . bin2hex(random_bytes(6));

	$acSide = $attributes['acSideChange'] ?? '';
	$accordion_layout = $acSide == 'right-float'
		? 'bs-advance-accordion--content-right'
		: ($acSide == 'bottom'
			? 'bs-advance-accordion--content-bottom'
			: '');

	$accordion_class_names_array = $attributes['accordionClassNames']  ?? [];
	$accordion_classes = '';

	//Checking if the 'alwaysExpanded' attribute is set to ensure the backward comatibility
	$alwaysExpanded = isset($attributes['alwaysExpanded']) ?  $attributes['alwaysExpanded'] : false;

	foreach ($accordion_class_names_array as $accordion_class_name) {
		$accordion_classes .= $accordion_class_name['value'] . ' ';
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
			'isPanelTop' => true,
			'titleTag' => $accordion['attrs']['titleTag'] ?? 'span'
		];
		foreach ($accordion['innerBlocks'] as $inner_block) {
			if ($inner_block['blockName'] == 'e25m/advance-accordion-body-left') {
				$data['isPanelTop'] = false;
				$data['left'] = $inner_block['innerBlocks'];
			} else if ($inner_block['blockName'] == 'e25m/advance-accordion-body-right') {
				$data['isPanelTop'] = false;
				$data['right'] = $inner_block['innerBlocks'];
			} else {
				$data['left'] = [];
				$data['right'][] = $inner_block;
			}
		}
		$content[] = $data;
	}

	$accordion_blocks = $inner_blocks->parsed_block['innerBlocks'] ?? [];

	$html_out = '<div id="' . $accordion_id . '-container" class="bs-advance-accordion ' . $accordion_layout . ' ' . $accordion_classes . '" data-attributes=\'' . json_encode($attributes, JSON_HEX_QUOT | JSON_HEX_TAG) . '\'>';
	$html_out .= '<div class="bs-advance-accordion__left-container">';

	foreach ($content as $key => $content_block) {
		[$show] = getContentDisplayProperties(
			$key,
			$attributes['defaultOpenIndex'] ?? null,
		);

		$html_out .= '<div class="bs-advance-accordion__left-content-panel ' . $show . '" data-parent="#' . $content_block['id'] . '">';

		if (isset($content_block['left'])) {
			foreach ($content_block['left'] as $left_block) {
				$html_out .= render_block($left_block);
			}
		}
		$html_out .= '</div>';
	}

	$html_out .= '</div>';
	$html_out .= ' <div class="bs-advance-accordion__right-container">';

	foreach ($content as $key => $content_block) {
		if (isset($content_block['right']) && $content_block['isPanelTop'] && $key == 0) {
			foreach ($content_block['right'] as $right_block) {
				$html_out .= render_block($right_block);
			}
		}
	}

	$html_out .= '  <div id="' . $accordion_id . '">';

	foreach ($content as $key => $content_block) {
		if (isset($content_block['right']) && $content_block['isPanelTop']) {
			if ($key > 0) {
				foreach ($content_block['right'] as $right_block) {
					$html_out .= render_block($right_block);
				}
			}
		} else {
			[$show, $expanded] = getContentDisplayProperties(
				$key,
				$attributes['defaultOpenIndex'] ?? 0, //Asigning default index as 0
			);

			$activeClass = $expanded === 'true' ? 'active' : '';
			$alwaysExpandedClass = $alwaysExpanded ? 'expand-fixed' : '';
			$html_out .= '<div class="card ' . $activeClass . '">';
			$html_out .= '  <div class="card-header accordion__block__btn ' . $alwaysExpandedClass . '" id="' . $content_block['id'] . '_header"
        data-toggle="collapse"
        data-target="#' . $content_block['id'] . '"
        aria-expanded="' . $expanded . '"
        aria-controls="' . $content_block['id'] . '">';

			$html_out .= $content_block['headingIcon'] ? '<img class="card-header__icon" src="' . $content_block['headingIcon'] . '" alt="' . $content_block['headingIconAlt'] . '">' : "";
			$html_out .= $content_block['heading'] ? '<' . $content_block['titleTag'] . '>' . $content_block['heading'] . '</' . $content_block['titleTag'] . '>' : null;
			$html_out .= '</div>';
			$html_out .= '<div
                    id="' . $content_block['id'] . '"
                    class="collapse ' . $show . ' ' . $alwaysExpandedClass . '"
                    aria-labelledby="' . $content_block['id'] . '_header"
                    data-parent="#' . $accordion_id . '"
                >';
			$html_out .= '<div class="card-body">';

			if(isset($content_block['right'])) {
				foreach ($content_block['right'] as $right_block) {
					$html_out .= render_block($right_block);
				}
			}
			
			$html_out .= '      </div>';
			$html_out .= '  </div>';
			$html_out .= '</div>';
		}
	}

	$html_out .= '      </div>';
	$html_out .= '  </div>';
	$html_out .= '</div>';

	return $html_out;
}

/**
 *
 * @param   int  $key
 * @param   int  $defaultKey
 * @param   bool  $alwaysExpanded
 *
 * @return  array
 */
function getContentDisplayProperties($key, $defaultKey)
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
