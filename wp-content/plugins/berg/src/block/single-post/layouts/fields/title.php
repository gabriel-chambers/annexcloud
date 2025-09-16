<?php
$_title = get_the_title();
if (trim($_title)) :
  if (is_numeric($titleCharLimit) && $titleCharLimit > 0) {
    $_title = charLimit($_title, $titleCharLimit, false);
  }
    ?>
    <div class="bs-post__title">
        <<?php echo $title_tag; ?>><?php echo $_title; ?></<?php echo $title_tag; ?>>
    </div>
<?php endif; ?>
