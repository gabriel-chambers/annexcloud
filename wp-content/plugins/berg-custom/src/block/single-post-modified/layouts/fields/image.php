<?php
$post_thumbnail_id = get_post_thumbnail_id($_post_id);
if ($post_thumbnail_id) {
	echo render_image($post_thumbnail_id);
}
