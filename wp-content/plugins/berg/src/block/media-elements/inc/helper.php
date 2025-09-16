<?php
if (!function_exists('render_media_image')) {
	/**
	 * @param $attributes
	 * @param string $return_type
	 * @return string|array
	 */
	function render_media_image($attributes, $return_type = 'html')
	{
		$image_data = getAttachmentData($attributes);
		if ($return_type == 'data') {
			$image_data['caption'] = ($attributes['image_caption']) ? $attributes['image_caption'] : $image_data['caption'];
			return $image_data;
		}
		if ($image_data) {
			$image_url = $image_data['url'];
			$image_caption = ($attributes['image_caption']) ? $attributes['image_caption'] : $image_data['caption'];
			$image_show_caption = $attributes['image_show_caption'];
			$image_title = ($attributes['image_title'])
				? ($attributes['image_title'])
				: null;
			$image_mask = $image_data['mask_url'];
			$image_type = $image_data['type'];
			$align_class = $attributes['media_alignment'] . " d-flex";
			$image_lazy_load =  $attributes['isImgLazyLoad'] ? 'loading="lazy"' : '';
			$img_attr = "src='$image_url' class='$image_type'  $image_lazy_load alt='".$image_data['alt']."' title='$image_title'";

			$common_prefix = 'bs-common';
			$mask_prefix = $common_prefix . '-mask';

			// extension catch
			$ext = pathinfo($image_url, PATHINFO_EXTENSION);
			$data_gif = ($ext == "gif") ? 'data-' . $ext . '= "' . $image_url . '"' : '';

			$element = "";
			$_figure_caption_data = "";

			if ($image_show_caption) {
				$_figure_caption_data = "<figcaption class='figure-caption'>$image_caption</figcaption>";
			}

			if (!empty($image_mask)) {
				$element .= "<div class='" . $mask_prefix . "'>
                <div class='" . $mask_prefix . "__wrap'>";
			}

			$desktop_breakpoint = get_theme_mod('desktop_breakpoint') > 0 ? get_theme_mod('desktop_breakpoint') : 1280;
			$mobile_breakpoint = get_theme_mod('mobile_breakpoint') > 0 ? get_theme_mod('mobile_breakpoint') : 576;

			//Adding 2x image url to srcset
			$source_media_mobile_2x = $attributes['image_2x_url_mobile'] ? ", " . $attributes['image_2x_url_mobile'] . " 2x" : "";
			$source_media_desktop_2x = $attributes['image_2x_url_desktop'] ? ", " . $attributes['image_2x_url_desktop'] . " 2x" : "";

			$source_media_mobile = $attributes['image_url'] && $attributes['image_url_mobile'] ? '<source srcset="' . $attributes['image_url_mobile'] . $source_media_mobile_2x . '" media="(max-width:'.($mobile_breakpoint - 1).'px)">' : '';
			$source_media_desktop =  $attributes['image_url'] && $attributes['image_url_desktop'] ? '<source srcset="' . $attributes['image_url_desktop'] . $source_media_desktop_2x . '" media="(max-width:'.$desktop_breakpoint.'px)">' : '';
			$element .= "<div class='" . $common_prefix . "-image'>
                            <figure class='figure " . $align_class . "'>
                            <picture>
                            " . $source_media_mobile  . $source_media_desktop . "
                            <img $img_attr $data_gif />
                            </picture>
                                $_figure_caption_data
                            </figure>
                        </div>";

			if (!empty($image_mask)) {
				/**/
				$element .= "<div class='" . $mask_prefix . "__layer' style=\"background-image: url('$image_mask');\">
                        </div>
                    </div>
                    </div>";
			}

			return $element; //html element
		}
		return 'No Media selected';
	}
}

if (!function_exists('getAttachmentData')) {
	/**
	 * @param array $attributes
	 * @return array
	 */
	function getAttachmentData(array $attributes)
	{
		$response = array(
			'url' => (array_key_exists('image_url', $attributes) && filter_var($attributes['image_url'], FILTER_VALIDATE_URL))
				? $attributes['image_url']
				: 'https://via.placeholder.com/300.png',
			'mask_url' => (array_key_exists('mask_image_url', $attributes) && filter_var($attributes['mask_image_url'], FILTER_VALIDATE_URL))
				? $attributes['mask_image_url']
				: false,
			'alt' => getAltTag($attributes) ? getAltTag($attributes) : "placeholder",
			'title' => getTitleTag($attributes) ? getTitleTag($attributes) : "placeholder",
			'caption' =>  (array_key_exists('image_url', $attributes) && filter_var($attributes['image_url'], FILTER_VALIDATE_URL))
			? ''
			: 'placeholder',
			'type' => $attributes['image_behaviour'],
			'media_alignment' => $attributes['media_alignment'],
			'image' => false
		);

		if ($attributes['image']) {
			$attachment = get_post($attributes['image']);
			$mask_attachment = get_post($attributes['mask_image']);

			if ($attachment && $attachment->post_type && $attachment->post_type === 'attachment') {
				$response = array(
					'mask_url' => wp_get_attachment_url($mask_attachment->ID),
					'title' => getTitleTag($attributes),
					'url' => wp_get_attachment_url($attachment->ID),
					'alt' => getAltTag($attributes),
					'description' => $attachment->post_content,
					'caption' => $attachment->post_excerpt,
					'type' => $attributes['image_behaviour'],
					'media_alignment' => $attributes['media_alignment'],
					'image' => true,
				);
			}
		}

		return $response;
	}
}


if (!function_exists('getAltTag')) {
	/**
	 * @param array $attributes
	 * @return string
	 */
	function getAltTag(array $attributes)
	{
		if ($attributes['image_alt']) {
			return $attributes['image_alt'];
		}
		$mediaAltTag = get_post_meta($attributes['image'], '_wp_attachment_image_alt', true);
		return $mediaAltTag ? $mediaAltTag : "";
	}
}

if (!function_exists('getTitleTag')) {
	/**
	 * @param array $attributes
	 * @return string
	 */
	function getTitleTag(array $attributes)
	{
		if ($attributes['image_title']) {
			return $attributes['image_title'];
		}
		$mediaTitle = get_the_title($attributes['image']);

		return $mediaTitle ? $mediaTitle : "";
	}
}

if (!function_exists('getBrowserName')) {
	function getBrowserName($userAgent)
	{
		$t = strtolower($userAgent);
		$t = " " . $t;
		if (strpos($t, 'opera') || strpos($t, 'opr/')) return 'Opera';
		elseif (strpos($t, 'edge')) return 'Edge';
		elseif (strpos($t, 'chrome')) return 'Chrome';
		elseif (strpos($t, 'safari')) return 'Safari';
		elseif (strpos($t, 'firefox')) return 'Firefox';
		elseif (strpos($t, 'msie') || strpos($t, 'trident/7')) return 'Internet Explorer';
		return 'Unkown';
	}
}
