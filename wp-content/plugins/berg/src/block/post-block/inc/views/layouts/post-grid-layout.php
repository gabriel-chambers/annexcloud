<!--Normal posts grid-->
<div class='bs-posts__normal'>
	<?php if (trim($postText)) :
		$postTextTag = ($postTextTag) ? $postTextTag : "h2";
	?>
		<div class='bs-posts__normal-title'>
			<?php echo '<' . $postTextTag . '>' . $postText . '</' . $postTextTag . '>'; ?></div>
	<?php endif; ?>
	<div id="bs-posts__normal-grid-<?php echo $uniqidID; ?>" class='bs-posts__normal-grid bs-posts__normal-grid-<?php echo $uniqidID; ?>'>
		<div class='bs-posts__normal-row row'>
			<?php include 'partial/grid.php'; ?>
		</div>
		<div class="bs-post-loading"></div>
	</div>
</div>

<!--Pagination-->
<?php include 'partial/pagination-load.php'; ?>
