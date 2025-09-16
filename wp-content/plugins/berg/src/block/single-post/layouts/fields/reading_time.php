<?php
  $post_reading_time = get_post_meta(get_the_ID(), 'post_reading_time', true);

  if(!$post_reading_time){
    $words_per_minute = get_option('words_per_minutes');
    $post_reading_time = calculate_reading_time(get_the_ID(), $words_per_minute);;
    update_post_meta(get_the_ID(), 'post_reading_time', $post_reading_time);
  }

  $reading_perfix_label = get_option('reading_time_prefix');
  $reading_postfix_label = get_option('reading_time_postfix');
?>

<div class="reading-time">
  <?php if(!empty($reading_perfix_label)){ ?>
    <span class="reading-time__label prefix"><?php echo $reading_perfix_label ?></span> 
  <?php } ?>
  <span class="reading-time__time"><?php echo $post_reading_time?></span>
  <?php if(!empty($reading_postfix_label)){ ?>
    <span class="reading-time__label postfix"><?php echo $reading_postfix_label ?></span>
  <?php } ?>
</div>