<?php
$post_reading_time = get_post_meta(get_the_ID(), 'post_reading_time', true);

if (!$post_reading_time) {
    $words_per_minute = get_option('words_per_minutes');
    $words_per_minute = ($words_per_minute) ? $words_per_minute : 300;
    $post_reading_time = calculate_reading_time(get_the_ID(), $words_per_minute);;
    update_post_meta(get_the_ID(), 'post_reading_time', $post_reading_time);
}

$reading_perfix_label = get_option('reading_time_prefix');
$reading_perfix_label = ($reading_perfix_label) ? $reading_perfix_label : 'Reading Time :';
$reading_postfix_label = get_option('reading_time_postfix');
$reading_postfix_label = ($reading_postfix_label) ? $reading_postfix_label : 'minutes';
?>

<div class="reading-time">
    <span class="reading-time__label prefix"><?php echo $reading_perfix_label ?></span>
    <span class="reading-time__time"><?php echo $post_reading_time ?></span>
    <span class="reading-time__label postfix"><?php echo $reading_postfix_label ?></span>
</div>
