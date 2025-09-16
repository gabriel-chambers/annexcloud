<?php
$meta = get_post_meta(get_the_ID(), str_replace('meta_', '', $display), true);
if (is_string($meta) && trim($meta)) { ?>
	<div class="bs-post__meta bs-post__<?php echo $display; ?>">
		<span><?php echo __($meta); ?></span>
	</div>
<?php } ?>
