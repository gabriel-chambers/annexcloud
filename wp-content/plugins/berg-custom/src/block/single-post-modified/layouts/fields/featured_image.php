<?php
$post_thumbnail_id = get_post_meta(get_the_ID(), str_replace('meta_', '', $display), true);
if ($post_thumbnail_id) {
	echo render_image($post_thumbnail_id, true);
}
