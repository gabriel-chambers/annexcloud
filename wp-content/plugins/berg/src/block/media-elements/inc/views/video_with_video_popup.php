<?php
$uniqueID = uniqid();
$videoPreviewId = "video-popup-preview-" . $uniqueID;
$previewStartTime = isset($attributes['previewStartTime']) ? floatval($attributes['previewStartTime']) / 1000 : 0;
$previewEndTime = isset($attributes['previewEndTime']) ? floatval($attributes['previewEndTime']) / 1000 : 0;

$div_class = '';
$preview_video_data_arr = [];
$preview_video_url = isset($attributes['previewVideoOptions']['videoId']) &&
	wp_get_attachment_url($attributes['previewVideoOptions']['videoId']) &&
	wp_attachment_is($attributes['previewVideoOptions']['videoId'], 'video') ?
	wp_get_attachment_url($attributes['previewVideoOptions']['videoId']) :
	(filter_var($attributes['previewVideoOptions']['videoUrl'], FILTER_VALIDATE_URL) ? $attributes['previewVideoOptions']['videoUrl'] : null);

$preview_video_ext = pathinfo($preview_video_url, PATHINFO_EXTENSION) == 'mov' ? 'mp4' : pathinfo($preview_video_url, PATHINFO_EXTENSION);
$preview_video_data_arr[$preview_video_ext] = $preview_video_url;

$poster_image = array_key_exists('video_poster', $attributes)
	&& array_key_exists('url', $attributes['video_poster'])
	&& $attributes['video_poster']['url'] != null
	? $attributes['video_poster']['url']
	: null;
	
/**
 * Placeholder settings
 */
$placeholder_data = render_media_image($attributes, 'data');

$image_url = $placeholder_data['url'];
$image_alt = $placeholder_data['alt'];
$image_caption = $placeholder_data['caption'];
$image_title = $placeholder_data['title'];
$image_mask = $placeholder_data['mask_url'];
$image_type = $placeholder_data['type'];
if ($attributes['video_behaviour'] === "thumbnail") {
	$align_class = (isset($attributes['media_alignment']) ?? $attributes['media_alignment']) . ' d-flex';
	$div_class .= $align_class;
	$video_class = 'mw-100';
} else {
	$video_class = 'w-100';
}
$img_attr = "src=$image_url";
$img_attr .= " class='$image_type' alt='$image_alt' title='$image_title'";

$common_prefix = 'bs-common';
$mask_prefix = $common_prefix . '-mask';

/**
 * Popup video settings
 */
$popup_type = $attributes['video_source']; //fw_print($popup_type);
$target_el = '';
$encoded_html_attributes = '';
$video_data = [];
$video_data_json = '';
$isVideoTransparent = ($attributes['isVideoTransparent']) ? $attributes['isVideoTransparent'] : '';

if ($popup_type === 'url' && isset($attributes['video_url'])) {
	$_data = $attributes['video_url'];
}

if ($popup_type === 'upload') {

	if (isset($attributes['video_url']) && !empty($attributes['video_url'])) {
		$_data = $video_url = isset($attributes['video']) && wp_get_attachment_url($attributes['video']) && wp_attachment_is($attributes['video'], 'video') ? wp_get_attachment_url($attributes['video']) : (filter_var($attributes['video_url'], FILTER_VALIDATE_URL) ? $attributes['video_url'] : null);
		$video_ext = pathinfo($video_url, PATHINFO_EXTENSION) == 'mov' ? 'mp4' : pathinfo($video_url, PATHINFO_EXTENSION);
		$video_data[$video_ext] = ['src' => $video_url, 'format' => 'video/' . $video_ext];
	}
	if (
		isset($attributes['enableFallbackVideo']) && $attributes['enableFallbackVideo'] &&
		isset($attributes['fallbackVideoOptions']['videoUrl']) && $attributes['fallbackVideoOptions']['videoUrl']
	) {
		$fallback_video_url = wp_get_attachment_url($attributes['fallbackVideoOptions']['videoId']) &&
			wp_attachment_is($attributes['fallbackVideoOptions']['videoId'], 'video') ?
			wp_get_attachment_url($attributes['fallbackVideoOptions']['videoId']) : (filter_var($attributes['fallbackVideoOptions']['videoUrl'], FILTER_VALIDATE_URL) ? $attributes['fallbackVideoOptions']['videoUrl'] : null);

		if (!empty($fallback_video_url)) {
			$fallback_video_ext = pathinfo($fallback_video_url, PATHINFO_EXTENSION) == 'mov' ? 'mp4' : pathinfo($fallback_video_url, PATHINFO_EXTENSION);
			$video_data[$fallback_video_ext] = ['src' => $fallback_video_url, 'format' => "video/" . $fallback_video_ext, 'isVideoTransparent' => $isVideoTransparent];
		}
	}

	//get mp4 vedio extention to first element in the array
	if ($isVideoTransparent) {
		$videoOrder = ['mp4', 'webm'];
		//get vedio extention from $video_data
		$videoExtensions = array_keys($video_data);
		//get common extenstions(if mp4 extension array then it became first element)
		$commonExtensions = array_intersect($videoOrder, $videoExtensions);
		//get other extension(except mp4 and webm)
		$diffrentExtensions = array_diff($videoExtensions, $videoOrder);
		//reorder $video_data array extenstion(always mp4 first)
		$video_data = array_replace(array_flip(array_merge($commonExtensions, $diffrentExtensions)), $video_data);
	}

	$video_data_json = json_encode($video_data, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS);
}

if ($popup_type === 'html' && isset($attributes['vpopupHtml']) && !empty($attributes['vpopupHtml'])) {
	$_data = "data:text/html, " . htmlentities(base64_decode($attributes['vpopupHtml']));
	$class_names = isset($attributes['vpopupFancyBoxOptions']['classNames']) ? $attributes['vpopupFancyBoxOptions']['classNames'] : "";
	$click_outside = isset($attributes['vpopupFancyBoxOptions']['dismissOnClickOutside']) ? $attributes['vpopupFancyBoxOptions']['dismissOnClickOutside'] : "close";
	$encoded_html_attributes = "data-main-class='" . $class_names . "'
                                data-click='" . $click_outside . "'
                                data-click-slide='" . $click_outside . "'
								data-type='iframe'";
}

if (isset($_data)) {
	$play = isset($attributes['play_icon']) &&
		wp_get_attachment_url($attributes['play_icon']) &&
		wp_attachment_is($attributes['play_icon'], 'image') ?
		wp_get_attachment_url($attributes['play_icon']) : (filter_var($attributes['play_icon_url'], FILTER_VALIDATE_URL) ?
			$attributes['play_icon_url'] : null);
	$style = 'background-image: url("' . $play . '");background-position: center;background-repeat: no-repeat;';
	$target_el = "<a href='$_data' data-fancybox rel='fancybox-thumb' tabindex='0' data-video_data='$video_data_json'" . $encoded_html_attributes . ">";
	$target_el .= "<i role='img' aria-label='icon' style='" . $style . "' class='play-icon'></i>";
	$target_el .= "</a>";
}

?>

<?php if (!empty($image_mask)): ?>
	<div class="<?php echo $mask_prefix; ?>">
		<div class="<?php echo $mask_prefix; ?>__wrap">
		<?php endif; ?>

		<div class="<?php echo $common_prefix; ?>-video common-video-popup">

			<div class="<?php echo $div_class; ?> preview-video-wrapper">
				<video id="<?php echo $videoPreviewId; ?>" muted autoplay loop class="<?php echo $video_class; ?>"
					data-preview-start="<?php echo $previewStartTime; ?>"
					data-preview-end="<?php echo $previewEndTime; ?>" preload="metadata"
					poster="<?php echo $poster_image ?>" playsinline>
					<?php foreach ($preview_video_data_arr as $key => $url): ?>
						<source src="<?php echo $url; ?>" type="<?php echo "video/" . $key; ?>">
					<?php endforeach; ?>
					<?php echo __('Your browser does not support HTML5 video.', 'blankslate'); ?>
				</video>
				<script>
					if (document.getElementById("<?php echo $videoPreviewId; ?>") !== null) {
						const videoPreview<?php echo $uniqueID; ?> = document.getElementById("<?php echo $videoPreviewId; ?>");
						videoPreview<?php echo $uniqueID; ?>.currentTime = videoPreview<?php echo $uniqueID; ?>.dataset.previewStart;

						videoPreview<?php echo $uniqueID; ?>.addEventListener('timeupdate', function () {
							if (this.currentTime > videoPreview<?php echo $uniqueID; ?>.dataset.previewEnd) {
								this.pause();
								if (videoPreview<?php echo $uniqueID; ?>.dataset.previewStart !== videoPreview<?php echo $uniqueID; ?>.dataset.previewEnd) {
									this.currentTime = videoPreview<?php echo $uniqueID; ?>.dataset.previewStart;
									this.play();
								}
							}
						});
					}
				</script>
			</div>

			<?php echo $target_el; ?>
		</div>
		<?php if (!empty($image_mask)): ?>
			<div class="<?php echo $mask_prefix; ?>__layer" style="background-image: url('<?php echo $image_mask; ?>');">
			</div>
		</div>
	</div>
<?php endif; ?>