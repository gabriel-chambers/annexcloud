<?php

const TAB_NAVS_SLIDER_OPTIONS_ATTRIBUTES = [
    'slidesToShow' => 3,
    'slidesToScroll' => 1,
    'arrows' => true,
    'dots' => false,
    'infinite' => false,
    'speed' => 300,
    'autoplay' => false,
    'draggable' => true,
    'swipe' => true,
    'initialSlide' => 0,
    'autoplaySpeed' => 3000,
    'fade' => false,
    'variableWidth' => false,
    'adaptiveHeight' => false,
    'focusOnSelect' => false,
    'centerMode' => false,
    'centerPadding' => '50px',
    'lazyLoad' => 'progressive',
    'unslick' => false,
];

const TAB_CONTENTS_SLIDER_OPTIONS_ATTRIBUTES = [
    'slidesToShow' => 1,
    'slidesToScroll' => 1,
    'arrows' => false,
    'dots' => false,
    'infinite' => false,
    'autoplay' => false,
    'draggable' => true,
    'swipe' => true,
    'autoplaySpeed' => 3000,
    'initialSlide' => 0,
    'speed' => 300,
    'fade' => false,
    'variableWidth' => false,
    'adaptiveHeight' => false,
    'focusOnSelect' => false,
    'centerMode' => false,
    'centerPadding' => '50px',
    'lazyLoad' => 'progressive',
    'unslick' => false,
];

/**
 *  Register e25m/tab-slider-v2
 *
 * @return void
 */
function create_block_tab_slider_v2_block_init()
{
    register_block_type(
        'e25m/tab-slider-v2',
        [
            'render_callback' => 'render_block_tab_slider_v2_block',
            'attributes' => [
                'tabSliderVisibility' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
                'blockID' => [
                    'type' => 'string',
                    'default' => "",
                ],
                'tabSliderClassNames' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'value' => 'bs-tab-slider---default',
                            'label' => 'Default',
                        ],
                    ],
                ],
                'tabNavSliderClassNames' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'value' => 'bs-slider-tabs bs-slider---default',
                            'label' => 'Default',
                        ],
                    ],
                ],
                'tabContentSliderClassNames' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'value' => 'bs-slider-content bs-slider---default',
                            'label' => 'Default',
                        ],
                    ],
                ],
                'tabNavsSliderOptions' => [
                    'type' => 'object',
                    'default' => [
                        'desktop' => [
                            'settings' => TAB_NAVS_SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'mobile' => [
                            'breakpoint' => 576,
                            'settings' => TAB_NAVS_SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'tablet' => [
                            'breakpoint' => 992,
                            'settings' => TAB_NAVS_SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'tabletLandscape' => [
                            'breakpoint' => 1200,
                            'settings' => TAB_NAVS_SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                    ],
                ],
                'tabContentsSliderOptions' => [
                    'type' => 'object',
                    'default' => [
                        'desktop' => [
                            'settings' => TAB_CONTENTS_SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'mobile' => [
                            'breakpoint' => 576,
                            'settings' => TAB_CONTENTS_SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'tablet' => [
                            'breakpoint' => 992,
                            'settings' => TAB_CONTENTS_SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'tabletLandscape' => [
                            'breakpoint' => 1200,
                            'settings' => TAB_CONTENTS_SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                    ],
                ],
                'enableprogressBar' => [
                    'type' => 'boolean',
                    'default' => false,
                ]
            ],
        ]
    );
}
add_action('init', 'create_block_tab_slider_v2_block_init');

/**
 * Extract Nav and Content slider options for slick slider
 *
 * @param  array $attributes
 * @return array
 */
function get_slider_options($attributes, $blockId)
{
    $tabNavsSliderOptions = $attributes->tabNavsSliderOptions ?? [];
    $tabContentsSliderOptions = $attributes->tabContentsSliderOptions ?? [];

    $buildSettings
        = function ($settings, $isNav = false) use ($blockId) {
            $formatUnslicks = function ($settingsGroup)
            use ($isNav, $blockId) {
                if (is_array($settingsGroup)) {
                    if (
                        array_key_exists('settings', $settingsGroup)
                        && array_key_exists('unslick', $settingsGroup['settings'])
                        && $settingsGroup['settings']['unslick'] == true
                    ) {
                        $settingsGroup['settings'] = 'unslick';
                    }
                    if (is_array($settingsGroup['settings'])) {
                        $isKeySet = function ($arrayKey) use ($settingsGroup){
                            return ( isset($settingsGroup['settings'][$arrayKey]) && $settingsGroup['settings'][$arrayKey]);
                        };
                        $settingsGroup['settings']['asNavFor'] = $isNav
                            ? "#tab-slider-{$blockId}-content"
                            : (($isKeySet('dots') ||
                                $isKeySet('arrows') ||
                                $isKeySet('draggable') ||
                                $isKeySet('swipe')) ?
                                "#tab-slider-{$blockId}-nav" : "");

                        $updateArrows = function ($type) use (&$settingsGroup){
                        if (array_key_exists("{$type}Arrow", $settingsGroup['settings'])) {
                            if($settingsGroup['settings']["{$type}Arrow"] == "") {
                                unset($settingsGroup['settings']["{$type}Arrow"]);
                            }else {
                                $settingsGroup['settings']["{$type}Arrow"] = '<div class="slick-' . $type . ' custom-' . $type . '-arrow">'.$settingsGroup['settings']["{$type}Arrow"].'</div>';
                            }
                        }
                      };
                      $updateArrows('prev');
                      $updateArrows('next');
                    }
                }
                return $settingsGroup;
            };
            return [
                'responsive' => [
                    $formatUnslicks(
                        array_merge(['breakpoint' => 9999], $settings['desktop'])
                    ),
                    $formatUnslicks($settings['mobile']),
                    $formatUnslicks($settings['tablet']),
                    $formatUnslicks($settings['tabletLandscape']),
                ]
            ];
        };

    return [
        $buildSettings($tabNavsSliderOptions, true),
        $buildSettings($tabContentsSliderOptions),
    ];
}

/**
 * Render tab slider save content
 *
 * @param  array $attributes
 * @param  string $content
 * @param  array $innerBlocks
 * @return string
 */
function render_block_tab_slider_v2_block($attributes, $content, $innerBlocks)
{
    $attributes = (object) $attributes;
    $blockId = $attributes->blockID;
    $tabSliderClassNames = $attributes->tabSliderClassNames ?? [];

    $tabs = $innerBlocks->parsed_block['innerBlocks'];
    $navBlocks = $contentBlocks = [];

    foreach ($tabs as $tab) {
        if (
            array_key_exists('show', $tab['attrs'])
            && $tab['attrs']['show'] == false
        ) {
            continue;
        }

        foreach ($tab['innerBlocks'] as $innerBlockOfTab) {
            if ($innerBlockOfTab['blockName'] === 'e25m/tab-slider-v2-tab-nav') {
                array_push($navBlocks, $innerBlockOfTab);
            } elseif ($innerBlockOfTab['blockName'] === 'e25m/tab-slider-v2-tab-content') {
                array_push($contentBlocks, $innerBlockOfTab);
            }
        }
    }

    [$navSliderSettings, $contentSliderSettings]
        = get_slider_options($attributes, $blockId);

    $getClassNames = function ($classArray) {
        return implode(
            " ",
            array_map(
                function ($classData) {
                    return $classData['value'];
                },
                $classArray
            )
        );
    };

		$progressBarClass = ($attributes->enableprogressBar) ? ' bs-tab-slider--progress-bar': '';

    $html = '<div id="tab-slider-' . $blockId . '"
                class="bs-tab-slider ' . $getClassNames($attributes->tabSliderClassNames) . $progressBarClass. '">';
    $html .= '   <div id="tab-slider-' . $blockId . '-nav"
                    class="slick-slider ' . $getClassNames($attributes->tabNavSliderClassNames) . '"
                    data-slick=\'' . json_encode($navSliderSettings, JSON_HEX_QUOT | JSON_HEX_TAG) . '\'>';
    foreach ($navBlocks as $index => $navBlock) {
        $customString = ( isset($tabs[$index]['attrs']['enableCustomDots']) &&
                        $tabs[$index]['attrs']['enableCustomDots'] &&
                        isset($tabs[$index]['attrs']['customDotHtml']) &&
                        $tabs[$index]['attrs']['customDotHtml']) ?
        'data-customhtml=\'' . json_encode($tabs[$index]['attrs']['customDotHtml'], JSON_HEX_QUOT | JSON_HEX_TAG) . '\'': '';
        $html .= "<div class='slick-slide-wrapper text-center'
            data-slick-index='{$index}' ".$customString.">";
        $html .= render_block($navBlock);
        $html .= '</div>';
    }
    $html .= '   </div>';
    $html .= '   <div id="tab-slider-' . $blockId . '-content"
                    class="slick-slider ' . $getClassNames($attributes->tabContentSliderClassNames) . '"
                    data-slick=\'' . json_encode($contentSliderSettings, JSON_HEX_QUOT | JSON_HEX_TAG) . '\'>';
    foreach ($contentBlocks as $index => $contentBlock) {
         $customString = ( isset($tabs[$index]['attrs']['enableCustomDots']) &&
                        $tabs[$index]['attrs']['enableCustomDots'] &&
                        isset($tabs[$index]['attrs']['customDotHtml']) &&
                        $tabs[$index]['attrs']['customDotHtml']) ?
        'data-customhtml=\'' . json_encode($tabs[$index]['attrs']['customDotHtml'], JSON_HEX_QUOT | JSON_HEX_TAG) . '\'': '';
        $html .= "<div class='slick-slide-wrapper'
            data-slick-index='{$index}' ".$customString.">";
        $html .= render_block($contentBlock);
        $html .= '</div>';
    }
    $html .= '   </div>';
    $html .= '</div>';

    if ($attributes->tabSliderVisibility) {
        return $html;
    } else {
        return null;
    }
}
