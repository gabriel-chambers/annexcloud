<?php
$post_thumbnail_id = get_post_thumbnail_id($_post_id);
if (!$post_thumbnail_id && $showPlaceholderImage) {
    $post_thumbnail_id = $placeholderImage;
}
echo render_image($post_thumbnail_id);
