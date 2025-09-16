<?php
const SETTINGS = [
  "animateViewPort" => 50,
  "direction" => 1,
  "delay" => 0,
  "loop" => false,
  "fileName" => "",
  "mouseOutAction" => "none",
  "numberOfLoops" => 0,
  "playSpeed" => 1,
  "path" => "",
  "scrollRelativeTo" => "withinSection",
  "trigger" => false,
  "triggerMethod" => "pageHover",
  "oneTimePlay" => false,
	"segmentFrames" => []
];

add_filter('wp_check_filetype_and_ext', 'lottiefiles_filetypes', 10, 5);

function lottiefiles_filetypes($data, $file, $filename, $mimes, $real_mime)
{
  if (!empty($data['ext']) && !empty($data['type'])) {
    return $data;
  }
  $wp_file_type = wp_check_filetype($filename, $mimes);

  if ('json' === $wp_file_type['ext']) {
    $data['ext']  = 'json';
    $data['type'] = 'text/plain';
  }
  return $data;
}

add_action('init', 'register_lottie_animator_block');

function register_lottie_animator_block()
{
  $attributes = [
    'animationSettings' => [
      'type' => 'object',
      'default' => [
        'desktop' => [
          'settings' => SETTINGS,
        ],
        'mobile' => [
          'breakpoint' => 576,
          'settings' => SETTINGS,
        ],
      ]
    ],
    "classNames" => [
      "type" => "array",
      'default' => [
        [
          'value' => 'bs-lottie-animator---default',
          'label' => 'Default',
        ],
      ],
    ],
    "visibility" => [
      "type" => "boolean",
      "default" => true,
    ],
    "uniqueId" => [
      "type" => "string",
      "default" => '',
    ],
    "excludeInit" => [
      "type" => "boolean",
      "default" => false,
    ],
  ];

  register_block_type(
    'e25m/lottie-animator',
    [
      'apiVersion' => 2,
      'render_callback' => 'render_lottie_content',
      'attributes' => $attributes
    ]
  );
}

if (!function_exists("getClassNames")) {
  function getClassNames($classArray)
  {
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
}

function render_lottie_content($attributes)
{
  extract($attributes);

  $classes = $excludeInit ? "bs-lottie-animator bs-exclude-init" : "bs-lottie-animator";
  $moduleClassNames = getClassNames($classNames);

  $html = '<div class="' . $classes . ' ' . $moduleClassNames . '"
            id=' . $uniqueId . '
            data-settings=' . json_encode($animationSettings, JSON_HEX_QUOT | JSON_HEX_TAG) . '
          ></div>';
  if ($visibility && $animationSettings['desktop']['settings']['path']) {
    return $html;
  } else {
    return null;
  }
}
