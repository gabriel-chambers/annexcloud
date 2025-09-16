<?php
$meta = get_post_meta(get_the_ID(), str_replace('meta_', '', $display), true);
if ( is_string($meta) && trim($meta)) { ?>
	<div class="bs-post__meta bs-post-<?php echo $display; ?>">
		<?php if ($display == 'meta_custom_date') : ?>
			<span><?php echo date($dateFormat, strtotime($meta));; ?></span>
		<?php else : ?>
			<span><?php echo __($meta); ?></span>
		<?php endif; ?>
	</div>
<?php } ?>
