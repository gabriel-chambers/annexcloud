<?php
$post_thumbnail_id = get_post_meta(get_the_ID(), str_replace('meta_', '', $display), true);
if (!$post_thumbnail_id && $showPlaceholderImage) {
    $post_thumbnail_id = $placeholderImage;
}
echo render_image($post_thumbnail_id, true);
