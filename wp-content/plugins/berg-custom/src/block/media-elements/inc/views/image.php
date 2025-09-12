<?php if ($attributes['choice_link']) { ?>
    <?php  $image_link = $attributes['image_link']; ?>
    <a class="" href="<?php echo $image_link; ?>" target="<?php echo $attributes['open_new_tab']; ?>"
       role='link' aria-label='image link' title='<?php echo __('Image Link', 'blankslate'); ?>'>
        <div class="mask_image_link">
            <?php echo render_media_image($attributes); ?>
        </div>
    </a>
<?php } else { ?>
    <?php echo render_media_image($attributes); ?>
<?php } ?>
