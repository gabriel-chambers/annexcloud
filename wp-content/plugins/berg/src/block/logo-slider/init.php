<?php

const LOGO_SLIDER_OPTION = [
		'slidesToShow' => 3,
		'slidesToScroll' => 1,
		'arrows' => true,
		'dots' => true,
		'infinite' => false,
		'speed' => 300,
		'autoplay' => false,
		'draggable' => true,
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

const MOBILE_BREAKPOINT = 768;

/**
 *  Register e25m/slider-v2
 *
 * @return void
 */
function logo_slider_rendering()
{
		$attributes = json_decode(
			file_get_contents(__DIR__ . '/attributes.json'),
			true
		);
    register_block_type(
        'e25m/logo-slider',
        [
						'apiVersion' => 2,
            'render_callback' => 'logo_slider_rendering_html',
            'attributes' => $attributes
        ]
    );
}
add_action('init', 'logo_slider_rendering');

function get_slick_slider_options( $options ){
  $logo_slider = [
    'variableWidth' => $options['variableWidth'],
      'dots' =>  $options['slideDots'],
      'autoplay' =>  $options['autoPlay'],
      'arrows' =>  $options['slideArrows'],
    'responsive' =>  [ 
      [
        'breakpoint'  => 9999,
        'settings'    => [  
          'centerPadding' =>  "0px",
          'touchThreshold' =>  500,
          'infinite' =>  true,
          'slidesToShow' =>  $options['colCount'],
          'slidesToScroll' =>  $options['slidesToScroll'],
        ]
      ],
      [
        'breakpoint'  => MOBILE_BREAKPOINT,
        'settings'    => [  
          'slidesToShow' => $options['mobileItems'],
          'slidesToScroll' =>  $options['mobileSlidesToScroll'],
        ]
      ]
    ]
  ];

  return $logo_slider;
}



/**
 * Render tab slider save content
 *
 * @param  array $attributes
 * @param  string $content
 * @param  array $innerBlocks
 * @return string
 */

function logo_slider_rendering_html($attributes, $content, $innerBlocks)
{ 

  $slider_options   = get_slick_slider_options( $attributes );  
  $html = '';

  $slick_data_attr = $attributes['sliderEnabled']
    ? 'data-slick='.json_encode($slider_options) 
    : '';

  $variable_width_cls = $attributes['variableWidth'] ? 'bs-logo-slider__inner--variable-width' : '';
  $logo_set_id = 'logo-set-' . bin2hex(random_bytes(6));

  $logo_slider_class_names_array = $attributes['logoSliderClassNames']  ?? [] ; 
  $slider_classes = '';   
  foreach ($logo_slider_class_names_array as $slider_class_name ) {
      $slider_classes.=$slider_class_name['value'].' ';
  }

  if ( $attributes['images'] ) {
    $html .=  '<div class="bs-logo-slider '.$slider_classes.' "    id="'.$logo_set_id.'">';
    $html .= (!$attributes['sliderEnabled'] && !$attributes['variableWidth'])
      ? '<style>
          #'.$logo_set_id.' .bs-logo-slider__item {
                width: calc(100% / '.$attributes['colCount'].');
          }
          @media (max-width: '.MOBILE_BREAKPOINT.'px) {
            #'.$logo_set_id.' .bs-logo-slider__item {
                width: calc(100% / '.$attributes['mobileItems'].');
              }
          }
        </style>' 
      : '';
    $html .=  '<div class="bs-logo-slider__inner '.$variable_width_cls.'" '.$slick_data_attr.'>';
      foreach ($attributes['images'] as $key => $image ) {
        $html   .=  '<div class="bs-logo-slider__item" id="'.$image['mediaID'].'">';
        $html   .=  '<div class="bs-logo-slider__item-inner">';
        $html   .=  '<img src="'.$image['mediaURL'].'" alt="'.$image['alt'].'" title="'.$image['title'].'" class="bs-logo-slider__image">';
        $html   .=  $attributes['showCaption'] ? '<div class="bs-logo-slider__caption">'.$image['title'].'</div>' : '';
        $html   .=  '</div>';
        $html   .=  '</div>';
      }
    $html .=  '</div></div>'; 
  }
  return $html;
}
