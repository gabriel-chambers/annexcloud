<div class="bs-posts <?php
	echo implode(' ',array_column($postBlockClassNames,'value'));
	echo ($attributes['postVisibility'] == true ? " enable" : " disable");
	?>"
>
	<?php
		if (isset($hideSectionWhenEmpty)
			&& $hideSectionWhenEmpty
			&& !$_the_query->have_posts()
		) :
			$has_filtered_applied = false;
			if ($showFilters && is_array($filters) && !empty($filters)) {
				foreach ($filters as $_filter) {
					if (get_query_var($_filter)) {
						$has_filtered_applied = true;
						break;
					}
				}
			}
			if (!$has_filtered_applied) :
	?>
			<div data-hide-bs-section-wrapper="<?php echo $uniqidID; ?>"></div>
			<script>
				(function($, window, document) {
					$('[data-hide-bs-section-wrapper="<?php echo $uniqidID; ?>"]')
						.parents('section.bs-section')
						.remove();
				})(jQuery, window, document);
			</script>
	<?php
			endif;
	 	endif;
	?>
	<div class="bs-posts__container">
		<?php
		if(!empty($layoutDisplayOrders) && is_array($layoutDisplayOrders)):
			$layoutDisplayOrders = array_column($layoutDisplayOrders,'value');
			foreach ($layoutDisplayOrders AS $layout):
				 include ($layout.'-layout.php');
			endforeach;
		endif;
		?>
    </div>
</div>
