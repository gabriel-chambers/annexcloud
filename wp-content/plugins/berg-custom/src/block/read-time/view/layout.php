<?php
$post_reading_time = get_post_meta(get_the_ID(), 'post_reading_time', true);

if(!$post_reading_time){
    $words_per_minute = get_option('words_per_minutes');
    $post_reading_time = calculate_reading_time(get_the_ID(), $words_per_minute);;
    update_post_meta(get_the_ID(), 'post_reading_time', $post_reading_time);
}
?>

<div class="reading-time">
    <span class="reading-time__time"><?php echo $post_reading_time; ?> </span> minute read
</div>