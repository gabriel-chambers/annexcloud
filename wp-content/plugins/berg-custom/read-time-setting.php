<?php

if (
  file_exists(plugin_dir_path(__FILE__) . 'src/block/post-block/init.php') ||
  file_exists(plugin_dir_path(__FILE__) . 'src/block/single-post/single-post.php')
) {

  function reading_time_setting_init()
  {
    // Create settings section
    add_settings_section('reading_time_section', 'Post Reading Time Settings', '', 'reading');

    // Register Settings
    register_setting('reading', 'reading_time_prefix', '');
    register_setting('reading', 'words_per_minutes', 'sanitize_word_count_per_minute');
    register_setting('reading', 'reading_time_postfix', '');


    // Create settings fields
    add_settings_field('reading_time_prefix', 'Reading time prefix', 'add_reading_time_label', 'reading', 'reading_time_section');
    add_settings_field('words_per_minutes', 'Words per minute', 'add_words_per_minutes', 'reading', 'reading_time_section');
    add_settings_field('reading_time_postfix', 'Reading time postfix', 'add_reading_time_postfix', 'reading', 'reading_time_section');
  }
  add_action('admin_init', 'reading_time_setting_init');

  function sanitize_word_count_per_minute($input)
  {
    $number = filter_var($input, FILTER_VALIDATE_INT);
    if (!$number) {
      return (!empty(get_option('words_per_minutes')) ? get_option('words_per_minutes') : 300);
    }
    return $input;
  }

  /* Settings Field Callback */
  function add_reading_time_label()
  {
    $reading_time_prefix = (!empty(get_option('reading_time_prefix')) ? get_option('reading_time_prefix') : 'Reading Time :');
?>
    <label for="reading_time_prefix">
      <input id="reading_time_prefix" type="text" value="<?php echo $reading_time_prefix ?>" name="reading_time_prefix">
    </label>
  <?php
  }

  function add_words_per_minutes()
  {
    $words_per_minutes = (!empty(get_option('words_per_minutes')) ? get_option('words_per_minutes') : 300);
  ?>
    <label for="words_per_minutes">
      <input id="words_per_minutes" type="text" value="<?php echo $words_per_minutes ?>" name="words_per_minutes">
    </label>
  <?php
  }

  function add_reading_time_postfix()
  {
    $reading_time_postfix = (!empty(get_option('reading_time_postfix')) ? get_option('reading_time_postfix') : ' minutes');
  ?>
    <label for="reading_time_postfix">
      <input id="reading_time_postfix" type="text" value="<?php echo $reading_time_postfix ?>" name="reading_time_postfix">
    </label>
<?php
  }


  function update_post_read_time_meta($old_value, $new_value)
  {
    global $wpdb;
    $wpdb->update(
      $wpdb->postmeta,
      array(
        'meta_value' => null
      ),
      array('meta_key' => 'post_reading_time')
    );
  }
  add_action('add_option_words_per_minutes', 'update_post_read_time_meta', 10, 2);
  add_action('update_option_words_per_minutes', 'update_post_read_time_meta', 10, 2);


  function calculate_reading_time($post_id, $number_of_word_per_minute)
  {
    if (!$number_of_word_per_minute && !is_numeric($number_of_word_per_minute)) {
      return null;
    }

    $post_content = get_post_field('post_content', $post_id);
    $post_content = wp_strip_all_tags($post_content);
    $word_count = count(preg_split('/\s+/', $post_content));
    $reading_time = $word_count / $number_of_word_per_minute;
    if (1 > $reading_time) {
      $reading_time = '< 1';
    } else {
      $reading_time = ceil($reading_time);
    }
    return $reading_time;
  }
}

//update post reading time when post update
add_action('save_post', 'set_post_reading_time', 10, 2);

function set_post_reading_time($post_id, $post)
{
  $words_per_minute = get_option('words_per_minutes');
  $post_reading_time = calculate_reading_time($post_id, $words_per_minute);;
  update_post_meta(get_the_ID(), 'post_reading_time', $post_reading_time);
}