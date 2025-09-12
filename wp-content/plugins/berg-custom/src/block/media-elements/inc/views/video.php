<?php
$_mobile_image_url = wp_get_attachment_url($attributes['video_mobile_img']);
$div_class = '';
if (!empty($_mobile_image_url)) {
	$div_class = 'd-none d-lg-block d-md-block ';
}

if ($attributes['video_behaviour'] === "thumbnail") {
	$align_class = (isset($attributes['media_alignment']) ?? $attributes['media_alignment']) . ' d-flex';
	$div_class .= $align_class;
	$video_class = 'mw-100';
} else {
	$video_class = 'w-100';
}

$_settings = (!empty($attributes['video_settings'])) ? implode(" ", array_column($attributes['video_settings'], 'value')) : "";
$poster_image = array_key_exists('video_poster', $attributes)
	&& array_key_exists('url', $attributes['video_poster'])
	&& $attributes['video_poster']['url'] != null
	&& strpos($_settings, 'autoplay') === false
	? $attributes['video_poster']['url']
	: null;
$custom_play_button_image = array_key_exists('custom_play_button', $attributes)
	&& array_key_exists('image', $attributes['custom_play_button'])
	&& array_key_exists('url', $attributes['custom_play_button']['image'])
	#&& strpos($_settings, 'autoplay') === false
	&& $attributes['custom_play_button']['image']['url'] != null
	? $attributes['custom_play_button']['image']['url']
	: null;

	$hide_class = "";
	if(strpos($_settings, 'autoplay')) {
		$hide_class = "hide";
	}

$custom_play_button_height = array_key_exists('custom_play_button', $attributes)
	&& array_key_exists('size', $attributes['custom_play_button'])
	&& array_key_exists('height', $attributes['custom_play_button']['size'])
	&& $attributes['custom_play_button']['size']['height'] != null
	? $attributes['custom_play_button']['size']['height']
	: 150;
$custom_play_button_width = array_key_exists('custom_play_button', $attributes)
	&& array_key_exists('size', $attributes['custom_play_button'])
	&& array_key_exists('width', $attributes['custom_play_button']['size'])
	&& $attributes['custom_play_button']['size']['width'] != null
	? $attributes['custom_play_button']['size']['width']
	: 150;
$custom_play_button_size_unit = array_key_exists('custom_play_button', $attributes)
	&& array_key_exists('size', $attributes['custom_play_button'])
	&& array_key_exists('unit', $attributes['custom_play_button']['size'])
	&& $attributes['custom_play_button']['size']['unit'] != null
	? $attributes['custom_play_button']['size']['unit']
	: 'px';
$custom_pause_button_image = array_key_exists('custom_pause_button', $attributes)
	&& array_key_exists('image', $attributes['custom_pause_button'])
	&& array_key_exists('url', $attributes['custom_pause_button']['image'])
	&& $attributes['custom_pause_button']['image']['url'] != null
	? $attributes['custom_pause_button']['image']['url']
	: null;
$custom_pause_button_height = array_key_exists('custom_pause_button', $attributes)
	&& array_key_exists('size', $attributes['custom_pause_button'])
	&& array_key_exists('height', $attributes['custom_pause_button']['size'])
	&& $attributes['custom_pause_button']['size']['height'] != null
	? $attributes['custom_pause_button']['size']['height']
	: 150;
$custom_pause_button_width = array_key_exists('custom_pause_button', $attributes)
	&& array_key_exists('size', $attributes['custom_pause_button'])
	&& array_key_exists('width', $attributes['custom_pause_button']['size'])
	&& $attributes['custom_pause_button']['size']['width'] != null
	? $attributes['custom_pause_button']['size']['width']
	: 150;
$custom_pause_button_size_unit = array_key_exists('custom_pause_button', $attributes)
	&& array_key_exists('size', $attributes['custom_pause_button'])
	&& array_key_exists('unit', $attributes['custom_pause_button']['size'])
	&& $attributes['custom_pause_button']['size']['unit'] != null
	? $attributes['custom_pause_button']['size']['unit']
	: 'px';

if ($attributes['video_source'] === "upload" && !empty($attributes['video_url'])) :
	$video_data_arr = [];
	$_regular_vid_url = isset($attributes['video']) && wp_get_attachment_url($attributes['video']) && wp_attachment_is($attributes['video'], 'video') ?  wp_get_attachment_url($attributes['video']) : (filter_var($attributes['video_url'], FILTER_VALIDATE_URL) ? $attributes['video_url'] : null);
	$_regular_vid_ext = pathinfo($_regular_vid_url, PATHINFO_EXTENSION) ==  'mov' ? 'mp4' : pathinfo($_regular_vid_url, PATHINFO_EXTENSION);
	$video_data_arr[$_regular_vid_ext] = $_regular_vid_url;
	$fallback_video_ext = '';

	// Setting fallback video url as a source for the video tag
	if (
		isset($attributes['enableFallbackVideo']) && $attributes['enableFallbackVideo'] &&
		isset($attributes['fallbackVideoOptions']['videoUrl']) && $attributes['fallbackVideoOptions']['videoUrl']
	) {
		$fallback_video_url = wp_get_attachment_url($attributes['fallbackVideoOptions']['videoId']) &&
			wp_attachment_is($attributes['fallbackVideoOptions']['videoId'], 'video') ?
			wp_get_attachment_url($attributes['fallbackVideoOptions']['videoId']) : (filter_var($attributes['fallbackVideoOptions']['videoUrl'], FILTER_VALIDATE_URL) ? $attributes['fallbackVideoOptions']['videoUrl'] : null);

		if (!empty($fallback_video_url)) {
			$fallback_video_ext = pathinfo($fallback_video_url, PATHINFO_EXTENSION) ==  'mov' ? 'mp4' : pathinfo($fallback_video_url, PATHINFO_EXTENSION);
			$video_data_arr[$fallback_video_ext] = $fallback_video_url;
		}
	}

	if ($attributes['isVideoTransparent']) {
		$videoOrder = ['mp4', 'webm'];
		$videoExtensions = array_keys($video_data_arr);
		$commonExtensions = array_intersect($videoOrder, $videoExtensions);
		$diffrentExtensions = array_diff($videoExtensions, $videoOrder);
		$video_data_arr = array_replace(array_flip(array_merge($commonExtensions, $diffrentExtensions)), $video_data_arr);
	}
?>
	<div class="<?php echo $div_class; ?> video-wrapper">
		<video <?php echo $_settings; ?> class="<?php echo $video_class; ?>" preload="<?php echo $poster_image ? 'none' : 'metadata' ?>" poster="<?php echo $poster_image ?>" playsinline>
			<?php foreach ($video_data_arr as $key => $url) :
				$type = ($attributes['isVideoTransparent'] && $key === $fallback_video_ext) ? "video/" . $key . "; codecs=&quot;hvc1&quot;" : "video/" . $key;
			?>
				<source src="<?php echo $url; ?>#t=0.01" type="<?php echo $type; ?>">
			<?php endforeach; ?>
			<?php echo __('Your browser does not support HTML5 video.', 'blankslate'); ?>
		</video>
		<?php if (!is_null($custom_play_button_image)) : ?>
			<span class="play-button <?php echo $hide_class; ?>" style="width: <?php echo "{$custom_play_button_width}{$custom_play_button_size_unit}" ?>;
                    height: <?php echo "{$custom_play_button_height}{$custom_play_button_size_unit}" ?>;
                    background-image: url(<?php echo $custom_play_button_image ?>);"></span>
		<?php endif; ?>
		<?php if (!is_null($custom_pause_button_image)) : ?>
			<span class="pause-button hide" style="width: <?php echo "{$custom_pause_button_width}{$custom_pause_button_size_unit}" ?>;
                    height: <?php echo "{$custom_pause_button_height}{$custom_pause_button_size_unit}" ?>;
                    background-image: url(<?php echo $custom_pause_button_image ?>);"></span>
		<?php endif; ?>
	</div>
<?php elseif ($attributes['video_source'] === "url" && $attributes['video_url'] !== "") : ?>
	<div class="video-wrapper video-wrapper--iframe <?php echo $div_class; ?>">
		<?php if (!is_null($poster_image)) : ?>
			<img class="video-wrapper__poster-image" src="<?php echo $poster_image; ?>">
		<?php endif; ?>
		<div class="embed-responsive <?php echo $attributes['aspect_ratios']; ?>">
			<iframe class="embed-responsive-item" src="<?php echo $attributes['video_url']; ?>"></iframe>
		</div>
		<?php if (!is_null($custom_play_button_image)) : ?>
			<span class="play-button <?php echo $hide_class; ?>" style="width: <?php echo "{$custom_play_button_width}{$custom_play_button_size_unit}" ?>;
                    height: <?php echo "{$custom_play_button_height}{$custom_play_button_size_unit}" ?>;
                    background-image: url(<?php echo $custom_play_button_image ?>);"></span>
		<?php endif; ?>
		<?php if (!is_null($custom_pause_button_image)) : ?>
			<span class="pause-button hide" style="width: <?php echo "{$custom_pause_button_width}{$custom_pause_button_size_unit}" ?>;
                    height: <?php echo "{$custom_pause_button_height}{$custom_pause_button_size_unit}" ?>;
                    background-image: url(<?php echo $custom_pause_button_image ?>);"></span>
		<?php endif; ?>
	</div>
<?php elseif ($attributes['video_source'] === "html" && (isset($attributes['vpopupHtml']) && $attributes['vpopupHtml'] !== "")) :
	echo '<div>' . base64_decode($attributes['vpopupHtml']) . '</div>';
endif; ?>

<?php if (!empty($_mobile_image_url)) : ?>
	<div class="d-md-none d-lg-none">
		<img src="<?php echo $_mobile_image_url; ?>" />
	</div>
<?php endif; ?>
