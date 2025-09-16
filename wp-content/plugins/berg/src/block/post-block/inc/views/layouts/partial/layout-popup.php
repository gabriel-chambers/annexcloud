<?php
$_learn_more_type = get_post_meta($_post_id, 'learn_more_type', true);
if ($_learn_more_type == "po_link") :
	$bg_img_detail = get_background($_post_id, $popupImageAppearance, $popupBackgroundImageField, true, $showPlaceholderImage, $placeholderImage);
	if ($popupImageAppearance == 'background') {
		$popup_display_order = array_filter($popup_display_order, function ($k) use ($popupBackgroundImageField) {
			return $k != $popupBackgroundImageField;
		}, ARRAY_FILTER_USE_KEY);
	}
?>
<div<?php echo $bg_img_detail['bg_img']; ?>
    class="bs-post__target bs-post__target--popup-<?php echo $postType; ?> <?php echo $bg_img_detail['bg_img_class']; ?>"
    id="bs-post__popup--<?php echo $_post_id; ?>" data-post-id="<?php echo $_post_id; ?>" style="display: none;">
    <div
        class="bs-post-<?php echo $_post_id; ?> <?php echo $posts_blocks_class; ?> bs-post <?php echo implode(" ", $term_list_slug); ?>">
        <?php
			foreach ($popup_display_order as $order) {
				foreach ($order as $display => $type) {
					if ($type == 'print') {
						echo $display;
					} else {
						$_field_type = explode('_', $display)[0];
						$_field_name = str_replace($_field_type . '_', '', $display);
						if ($_field_name == 'featured_image') {
							$_field_type = 'featured_image';
						}
						if ($_field_name == 'designation') {
							$_field_type = 'designation';
						}
            if ($_field_name == 'post_reading_time') {
              $_field_type = 'reading_time';
            }
						include "fields/$_field_type.php";
					}
				}
			}
			?>
    </div>
    </div>
    <?php endif; ?>
