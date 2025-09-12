<?php
$event_start_date = get_post_meta(get_the_ID(), 'event_start_date', true);
$event_end_date = get_post_meta(get_the_ID(), 'event_end_date', true);
if (trim($event_start_date) || trim($event_end_date)) { ?>
    <div class="bs-post__meta bs-post-<?php echo $display; ?>">
        <span><?php echo humanDateRanges($event_start_date, $event_end_date); ?></span>
    </div>
<?php } ?>
