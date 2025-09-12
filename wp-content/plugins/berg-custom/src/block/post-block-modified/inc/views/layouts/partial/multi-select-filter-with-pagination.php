<?php
if ($postsNumberFirstLoad) {
    $max_num_pages = ceil((($_the_query->found_posts - $postsNumberFirstLoad) / $postsPerPage) + 1);
} else {
    $max_num_pages = $_the_query->max_num_pages;
}
if ($max_num_pages >= 1) {
    ?>
	<div class="bs-posts__pagination">
		<?php bootstrap_pagination($max_num_pages, $paged, $prevLabel, $nextLabel);?>
	</div>
<?php }?>
<script>

var postBlockFilter =  (function ($, window, document) {
		function gotoURL() {
			var filters = {};
			var filterLabels = {};
			let search = $('.bs-posts__filters-<?php echo $uniqidID; ?> .search-box').val();
			$(".bs-posts__filters-<?php echo $uniqidID; ?> select").each(function (index) {
				var filter_value = $(this).val();
				if ($(this).has('option:selected') && filter_value != "" && filter_value) {
					var filterName = $(this).attr("name").replace("[]", "");
					filters['"' + filterName + '"'] = $(this).val();
				}
			});
			var permaLink = '<?php echo get_the_permalink() ?>';
			var permaLinkPath = permaLink.split('?').shift();
			var permaLinkParms = new URLSearchParams(new URL(permaLink).search);
			let bind = '';
			let urlParams = '';
			let i = 0;
			let searchTxt = '';
			$.each(filters, function (key, value) {
				// "resource-types"
				let val = value.toString();
				let filter = key.replace(/"|"/g, '');
				bind = (i == 0) ? '?' : '&'
				urlParams += bind + filter + '=' + val;
				i++;
			});
			if (search) {
				bind = (i == 0) ? '?' : '&';
				let searchTxt = bind + 'search=' + search;
				urlParams += searchTxt;
			}
			let searchParams = permaLinkParms;
			if (urlParams) {
				for (let [key, value] of new URLSearchParams(urlParams))
				{
					searchParams.set(key, value);
				}
			}
			$(location).attr('href', `${permaLinkPath}?${searchParams.toString()}`);
			return false;
		}

		return {gotoURL};


	})(jQuery, window, document);


	(function ($, window, document) {

		$(".bs-posts__filters-<?php echo $uniqidID; ?>").on("submit", "form#searchform", function () {
			event.preventDefault();
			postBlockFilter.gotoURL();
		});

		$(".bs-posts__filters-<?php echo $uniqidID; ?>").on('keyup', '.search-box', function (e) {
			if (e.keyCode === 13) {
				postBlockFilter.gotoURL();
			}
		},);

		if ($('.selected-filters ul li').length > 0) {
			$(".selected-filters-<?php echo $uniqidID; ?> span a.clear-filters").show();
		}

		$(".choice-list .select2-selection__choice__remove").on('click', function (e) {

			var updateUrl = false;
			var filterAttributes = $('#filterAttr').val();

			if (filterAttributes.indexOf("updateUrl") >= 0) {
				updateUrl = true;
			}

			if (e.currentTarget.id === 'search-query') {
				var parentWrapperElement = e.currentTarget
					.closest('.selected-filters-<?php echo $uniqidID; ?>');
				var filtersWrapper = parentWrapperElement.previousElementSibling;
				var filtersForm = filtersWrapper.querySelector('form');
				filtersForm.querySelector('#search').value = '';
			} else {
				var title = $(this).parent().attr("title");
				var removed_filter = $('.multi-select option[value="' + title + '"]');
				removed_filter.prop('selected', false);
			}

			$(this).parent().remove();

			if ($('.selected-filters ul li').length == 0) {
				$(".selected-filters-<?php echo $uniqidID; ?> span a.clear-filters").hide();
			}

			postBlockFilter.gotoURL();

		});

		/*filter expand - click*/
		let i = 0;
		$(".filter-expand").on('click', "a", function (e) {
			e.preventDefault();
			const $animateEle = $(this).parents(".filter-expand").next('.filter-wrapper'),
				$height = $animateEle[0].scrollHeight;
			$animateEle.animate({height:(++i % 2) ? $height+"px" : 0},200);
			$(this).parents(".filter-expand").toggleClass("active");
			i = i++;
		});

	})(jQuery, window, document); // or even jQuery.noConflict()
</script>
