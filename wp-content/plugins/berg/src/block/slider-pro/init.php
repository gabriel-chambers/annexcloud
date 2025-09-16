<?php

const SLIDER_OPTIONS_ATTRIBUTES = [
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
    'unslick' => false
];

/**
 *  Register e25m/slider-v2
 *
 * @return void
 */
function create_block_slider_v2_block_init()
{
    register_block_type(
        'e25m/slider-v2',
        [
            'render_callback' => 'render_block_slider_v2_block',
            'attributes' => [
                'sliderVisibility' => [
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
                            'value' => 'bs-slider---default',
                            'label' => 'Default',
                        ],
                    ],
                ],
                'tabNavSliderClassNames' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'value' => 'bs-slider bs-slider---default',
                            'label' => 'Default'
                        ],
                    ],
                ],
                'tabContentSliderClassNames' => [
                    'type' => 'array',
                    'default' => [
                        [
                            'value' => 'bs-slider bs-slider---default',
                            'label' => 'Default',
                        ],
                    ],
                ],
                'tabNavsSliderOptions' => [
                    'type' => 'object',
                    'default' => [
                        'desktop' => [
                            'settings' => SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'mobile' => [
                            'breakpoint' => 576,
                            'settings' => SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'tablet' => [
                            'breakpoint' => 992,
                            'settings' => SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                        'tabletLandscape' => [
                            'breakpoint' => 1200,
                            'settings' => SLIDER_OPTIONS_ATTRIBUTES,
                        ],
                    ],
                ],
            ]
        ]
    );
}

add_action('init', 'create_block_slider_v2_block_init');

/**
 * Extract Nav and Content slider options for slick slider
 *
 * @param  array $attributes
 * @return array
 */

function get_slider_v2_options($attributes)
{
    $tabNavsSliderOptions = $attributes->tabNavsSliderOptions ?? [];

    $buildSettings = function ($settings) {
        $formatUnslicks = function ($settingsGroup) {
            if (is_array($settingsGroup) && array_key_exists('settings', $settingsGroup)) {
                    if (is_array($settingsGroup['settings']) &&  array_key_exists('unslick', $settingsGroup['settings']) && $settingsGroup['settings']['unslick'] == true) {
                        $settingsGroup['settings'] = 'unslick';
                    }
                    if(is_array($settingsGroup['settings'])){
                      $updateArrows = function ($type) use (&$settingsGroup){
                        if (array_key_exists("{$type}Arrow", $settingsGroup['settings'])) {
                          if($settingsGroup['settings']["{$type}Arrow"]== "") {
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
        $buildSettings($tabNavsSliderOptions),
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

function render_block_slider_v2_block($attributes, $content, $innerBlocks)
{
    $attributes = (object) $attributes;
    $blockId = $attributes->blockID;
    $tabSliderClassNames = $attributes->tabSliderClassNames ?? [];

    [$navSliderSettings] = get_slider_v2_options($attributes);

    $tabs = $innerBlocks->parsed_block['innerBlocks'];

    $navBlocks = $contentBlocks = [];

    $showSlide = function ($attributes) {
        return !array_key_exists('show', $attributes)
            || (array_key_exists('show', $attributes) && $attributes['show']);
    };

    foreach ($tabs as $tab) {
        if ($tab['blockName'] === 'e25m/slider-v2-tab' && $showSlide($tab['attrs'])) {
            array_push($navBlocks, $tab);
        }
    }

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

    $html = '<div id="slider-' . $blockId . '"
                class="bs-slider ' . $getClassNames($attributes->tabSliderClassNames) . '">';
    $html .= '   <div id="slider-' . $blockId . '-content" class="slick-slider ' . '"
                     data-slick=\'' . json_encode($navSliderSettings, JSON_HEX_QUOT | JSON_HEX_TAG) . '\'>';
    foreach ($navBlocks as $navBlock) {
        $customString = ( isset($navBlock['attrs']['enableCustomDots']) && 
                        $navBlock['attrs']['enableCustomDots'] && 
                        isset($navBlock['attrs']['customDotHtml']) && 
                        $navBlock['attrs']['customDotHtml']) ? 
        'data-customhtml=\'' . json_encode($navBlock['attrs']['customDotHtml'], JSON_HEX_QUOT | JSON_HEX_TAG) . '\'': '';
        $html .= '<div class="slick-slide-wrapper" '.$customString.'>';
        $html .=    render_block($navBlock);
        $html .= '</div>';
    }
    $html .= '   </div>';
    $html .= '</div>';

    if ($attributes->sliderVisibility) {
        return $html;
    } 
}
