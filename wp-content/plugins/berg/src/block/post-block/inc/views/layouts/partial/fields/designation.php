<?php
$designation = get_post_meta(get_the_ID(), 'designation', true);
if (!empty($designation)) { ?>
<div class="bs-post__meta bs-post__meta_designation">
    <span><?php echo $designation; ?></span>
</div>
<?php } ?>