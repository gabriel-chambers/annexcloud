<?php
$meta = get_post_meta(get_the_ID(), str_replace('meta_', '', $display), true);
$_date = date($dateFormat, strtotime($meta));
if (trim($meta)) { ?>
    <div class="bs-post__meta bs-post__<?php echo $display; ?>">
        <span><?php echo $_date; ?></span>
    </div>
<?php } ?>
