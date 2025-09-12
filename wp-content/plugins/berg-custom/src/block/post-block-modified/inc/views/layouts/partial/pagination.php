<?php
if ($postsNumberFirstLoad) {
	$max_num_pages = ceil((($_the_query->found_posts - $postsNumberFirstLoad) / $postsPerPage) + 1);
} else {
	$max_num_pages = $_the_query->max_num_pages;
}
if ($max_num_pages >= 1) {
?>
	<div class="bs-posts__pagination">
		<?php bootstrap_pagination($max_num_pages, $paged, $prevLabel, $nextLabel); ?>
	</div>
<?php } ?>
<script>
	var postBlockFilter = (function($, window, document) {
		function gotoURL() {
			var params = '';
			var search = '';
			<?php foreach ($filters as $tax_filter) : ?>
				<?php
				$add = false;
				if ($tax_filter == 'search') {
					$defaultValue = 'search';
					$add = true;
				} else {
					$defaultValue = 'category';
					$__terms = get_terms($tax_filter, array('hide_empty' => true));
					if (count($__terms)) {
						$add = true;
					}
				}
				?>
				<?php if ($add) : ?>
					var inputVal = $('#<?php echo $tax_filter; ?>').val();
					var selectedIndex = $('#<?php echo $tax_filter; ?>').prop('selectedIndex');
					var taxFilter = '<?php echo $tax_filter; ?>';
					inputVal = encodeURIComponent(inputVal);
					<?php if ($tax_filter != 'search') : ?>
						if (selectedIndex == 0) {
							params += "all_" + taxFilter + "=true&";
						} else if (inputVal) {
							params += '<?php echo $tax_filter . '='; ?>' + inputVal + '&';
						}
					<?php else : ?>
						if (inputVal) {
							search = 'search=' + inputVal;
						}
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach ?>
			params += search;
			var permaLink = '<?php echo get_the_permalink() ?>';
			var permaLinkPath = permaLink.split('?').shift();
			var permaLinkParms = new URLSearchParams(new URL(permaLink).search);
			var queryParams = permaLinkParms;
			if (params) {
				for (let [key, value] of new URLSearchParams(params)) {
					queryParams.set(key, value);
				}
			}
			$(location).attr('href', `${permaLinkPath}?${queryParams.toString()}`);
			return false;
		}

		return {
			gotoURL
		};

	})(jQuery, window, document);


	(function($, window, document) {
		$('.bs-posts__filters-<?php echo $uniqidID; ?>').on(
			'change',
			'select',
			postBlockFilter.gotoURL
		);

		$('.bs-posts__filters-<?php echo $uniqidID; ?>').on(
			'keyup',
			'.search-box',
			function(e) {
				if (e.keyCode === 13) postBlockFilter.gotoURL();
			}
		);

	})(jQuery, window, document); // or even jQuery.noConflict()
</script>