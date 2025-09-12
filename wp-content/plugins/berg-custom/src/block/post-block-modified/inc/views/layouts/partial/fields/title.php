<?php
$_title = get_the_title();
if (trim($_title)) :
	if (is_numeric($titleCharLimit) && $titleCharLimit > 0) {
		$_title = charLimit($_title, $titleCharLimit, false);
	}
?>
	<div class="bs-post__title">
		<?php echo '<' . $attributes['titleTag'] . '>' . $_title . '</' . $attributes['titleTag'] . '>'; ?>
	</div>
<?php endif; ?>
