<?php
$displayName = str_replace('meta_', '', $display);
$meta = get_post_meta(get_the_ID(), $displayName, true);

if($displayName == 'featured_event_location_logo' || $displayName == 'featured_event_date_logo'){
	 $image = wp_get_attachment_image($meta); //var_dump($image);
	?>
	<div class="bs-post__meta bs-post__<?php echo $displayName; ?>">
		<span><?php echo $image; ?></span>
	</div>
<?php
}else{
	if (is_string($meta) && trim($meta)) { ?>
		<div class="bs-post__meta bs-post__<?php echo $displayName; ?>">
			<span><?php echo __($meta); ?></span>
		</div>
	<?php } 
}