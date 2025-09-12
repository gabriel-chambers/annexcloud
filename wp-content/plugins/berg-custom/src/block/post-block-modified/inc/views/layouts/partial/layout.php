<?php
$post_container_class = (get_post_thumbnail_id($_post_id)) ? 'bs-post__container ' : 'bs-post__container bs-post__container--no-image ';
$term_list            = wp_get_post_terms($_post_id, 'resource-types', array("fields" => "names"));
if (is_array($term_list)) {
	$post_container_class .= strtolower(str_replace(' ', '-', implode(' ', $term_list)));
}
if ($is_featured) {
	$_imageAppearance      = $featuredImageAppearance;
	$_backgroundImageField = $featuredBackgroundImageField;
} else {
	$_imageAppearance      = $imageAppearance;
	$_backgroundImageField = $backgroundImageField;
}
$bg_img_detail = get_background($_post_id, $_imageAppearance, $_backgroundImageField, false, $showPlaceholderImage, $placeholderImage);
if ($_imageAppearance == 'background') {
	$display_order = array_filter($display_order,
			function ( $order_field ) {
				if(!isset($order_field['image'])){
					return true;
				}
			}
	);
}
?>

<?php
	if ($anchorElementAppearance == 'full') {
		echo render_link('open', $_link_attributes);
	}
?>

<div class="<?php echo $post_container_class . $bg_img_detail['bg_img_class']; ?>"
    <?php echo $bg_img_detail['bg_img']; ?>>
    <div class="bs-post__inner">
    <?php
		foreach ($display_order as $order) {
			foreach ($order as $display => $type) {
				if ($type == 'print') {
					echo $display;
				} else {
					$_field_type = in_array($display, ['meta_custom_date'])  ? $display : explode('_', $display)[0];
					$_field_name = str_replace($_field_type . '_', '', $display);
					if ($_field_name == 'featured_image') {
						$_field_type = 'featured_image';
					}
					if ($_field_name == 'designation') {
						$_field_type = 'designation';
					}
					if ($_field_name == 'event_start_date') {
						$_field_type = 'event_start_date';
					}
					if ($_field_name == 'event_end_date') {
						$_field_type = 'event_end_date';
					}
					if ($_field_name == 'type_name') {
						$_field_type = 'post_type_name';
					}
					if ($_field_name == 'post_reading_time') {
						$_field_type = 'reading_time';
					}
					if ($_field_name == 'meta_custom_date') {
						$_field_type = 'custom_date';
					}
					include "fields/$_field_type.php";
				}
			}
		}
		?>
    </div>
</div>
   <?php
	if ($anchorElementAppearance == 'full') {
		echo render_link('close', $_link_attributes);
	} ?>
