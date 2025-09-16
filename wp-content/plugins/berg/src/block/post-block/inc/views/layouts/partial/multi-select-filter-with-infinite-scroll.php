<?php
// Creating an array of default filters to be used as a javascript object
$defaultFiltersArray = [];
if (is_array($attributes['filterByType']) && !empty($attributes['filterByType'])) {
	foreach ($attributes['filterByType'] as $tax) {
		$termLabel = $tax['label'];
		$term = get_term($tax['value']);
		$termSlug = $term->slug;
		$defaultFiltersArray[$termSlug] = $termLabel;
	}
}
?>
<div class="bs-posts__load-more">
	<?php
	if ($postsNumberFirstLoad) {
		$max_num_pages = ceil((($_the_query->found_posts - $postsNumberFirstLoad) / $postsPerPage) + 1);
	} else {
		$max_num_pages = $_the_query->max_num_pages;
	}
	?>
	<input type="hidden" name="maxPage" id="maxPage_<?php echo $uniqidID; ?>" value="<?php echo $max_num_pages; ?>">
	<input type="hidden" name="paged" id="paged_<?php echo $uniqidID; ?>" value="2">
	<input type="hidden" name="search_term" id="search_term_<?php echo $uniqidID; ?>" value="">
</div>

<script>
	(function($, window, document) {
		var enableRequest = true;
		// Check more records available for infinite scroll
		function checkLoadMoreButton_<?php echo $uniqidID; ?>() {
			var paged = parseInt($('#paged_<?php echo $uniqidID; ?>').val());
			var valMaxPage = parseInt($('#maxPage_<?php echo $uniqidID; ?>').val());
			if (paged > valMaxPage) {
				return false;
			} else {
				return true;
			}
		}

		checkLoadMoreButton_<?php echo $uniqidID; ?>();

		$(".bs-posts__filters-<?php echo $uniqidID; ?>").on("submit", "form#searchform", function() {
			event.preventDefault();
			let search = $('.bs-posts__filters-<?php echo $uniqidID; ?> .search-box').val();

			getPostBlocksData_<?php echo $uniqidID; ?>('select', 'load', search);
			getPostBlocksData_<?php echo $uniqidID; ?>('search', 'maxPage', search);
		});

		$(".bs-posts__load-more-btn-<?php echo $uniqidID; ?>").on("click", function() {
			$("#bs-posts__normal-grid-<?php echo $uniqidID; ?>").addClass('loading');
			getPostBlocksData_<?php echo $uniqidID; ?>('click', 'load');
		});

		$(".bs-posts__filters-<?php echo $uniqidID; ?>").on("keyup", ".search-box", function(e) {
			if (e.keyCode === 13) {
				$("#bs-posts__normal-grid-<?php echo $uniqidID; ?>").addClass('loading');
				getPostBlocksData_<?php echo $uniqidID; ?>('search', 'load', $(this).val());
				getPostBlocksData_<?php echo $uniqidID; ?>('search', 'maxPage', $(this).val());
				$("#search_term_<?php echo $uniqidID; ?>").val($(this).val());
			}
		}, );


		function getPostBlocksData_<?php echo $uniqidID; ?>(mode, maxPage, search = '') {
			// We'll pass this variable to the PHP function example_ajax_request
			var atts = <?php echo json_encode($attributes); ?>;
			var paged = parseInt($('#paged_<?php echo $uniqidID; ?>').val());
			//
			var valMaxPage = parseInt($('#maxPage_<?php echo $uniqidID; ?>').val());
			var filters = {};
			var filter_mode = false;
			var filterLabels = {};
			var all_enabled = {}; // to check if 'All' option is applied

			if (mode === 'select') {
				paged = 1;
				filter_mode = true;
			}

			if (mode === 'search') {
				paged = 1;
				filter_mode = true;
			}

			$(".bs-posts__filters-<?php echo $uniqidID; ?> select").each(function(index) {
				var filter_value = $(this).val();
				if ($(this).has('option:selected') && filter_value != "" && filter_value) {
					var filterName = $(this).attr("name").replace("[]", "");
					filters['"' + filterName + '"'] = $(this).val();
				}
			});

			$(".bs-posts__filters-<?php echo $uniqidID; ?> select option:selected").each(function() {
				var filter_value = $(this).val();
				var $this = $(this);
				if ($this.length && filter_value != "" && filter_value) {
					filterLabels[$(this).val()] = $(this).text();
				}
			});

			var permaLink = '<?php echo get_the_permalink() ?>';
			var permaLinkPath = permaLink.split('?').shift();
			var permaLinkParms = new URLSearchParams(new URL(permaLink).search);
			let bind = '';
			let urlParams = '';
			let i = 0;
			let searchTxt = '';
			$.each(filters, function(key, value) {
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

			let searchParams = permaLinkParms
			if (urlParams) {
				for (let [key, value] of new URLSearchParams(urlParams)) {
					searchParams.set(key, value);
				}
			}

			let url = `${permaLinkPath}?${searchParams.toString()}`
			window.history.pushState({
				path: url
			}, '', url);

			var defaultLoader = (atts.custom_load_more_button.image.url != null) ?
				atts.custom_load_more_button.image.url : '<?php echo plugin_dir_url(__DIR__) ?>loader.gif';

			showSelectedFilters(filterLabels);
			// This does the ajax request
			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>', // or example_ajax_obj.ajaxurl if using on frontend
				type: 'post',
				data: {
					'action': 'get_post_block_data',
					'atts': JSON.stringify(atts),
					'featured_ids': <?php echo json_encode($featured_ids); ?>,
					'ajax': true,
					'paged': paged,
					'filters': filters,
					'dataType': maxPage,
					'search': search,
				},
				beforeSend: function() {
					enableRequest = false;
					let loadMoreHtml = '<img src="' + defaultLoader + '" class="bs-post-loading-icon" />';
					$(".bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-post-loading").html(loadMoreHtml);
				},
				success: function(data) {
					if (data != 0) {
						if (maxPage === 'maxPage') {
							$("#paged_<?php echo $uniqidID; ?>").val(2);
							$("#maxPage_<?php echo $uniqidID; ?>").val(data);
							checkLoadMoreButton_<?php echo $uniqidID; ?>();
						} else {
							if (filter_mode) {
								$(".bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-posts__normal-row").html(data);
							} else {
								$(".bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-posts__normal-row").append(data);
								new_paged = parseInt(paged) + 1;
								$('#paged_<?php echo $uniqidID; ?>').val(new_paged);
								// history.pushState("", "", '<?php the_permalink(); ?>page/' + paged);
								checkLoadMoreButton_<?php echo $uniqidID; ?>();
							}
						}
						enableRequest = true;
					} else {
						$(".bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-posts__normal-row").html('<div class="bs-posts__not-found col-12"><h3><?php echo $noEntriesFoundText; ?></h3></div>');
						$('.bs-posts__load-more-btn-<?php echo $uniqidID; ?>').hide();
						enableRequest = false;
					}

					$('.bs-post-loading-icon').hide();
					$("#bs-posts__normal-grid-<?php echo $uniqidID; ?>").removeClass('loading');

				},
				error: function(errorThrown) {
					console.log(errorThrown);
					enableRequest = true;
				}
			});

			window.onpopstate = function(event) {
				var url = document.location.pathname;
				var url_paged = url.split("/").slice(-1)[0];
				var paged = parseInt($('#paged_<?php echo $uniqidID; ?>').val());
				var url_paged_val;
				if (url_paged === "") {
					url_paged_val = 0;
				} else {
					url_paged_val = parseInt(url_paged);
				}
				if (url_paged_val < paged) {
					// history back
					$('.bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-posts__normal-row > div').slice(-<?php echo $postsPerPage; ?>).remove();
					$('#paged_<?php echo $uniqidID; ?>').val(paged - 1);
					checkLoadMoreButton_<?php echo $uniqidID; ?>();
				} else {
					// history fwd
					if (url_paged_val === 0) {
						$('.bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-posts__normal-row > div').slice(-<?php echo $postsPerPage; ?>).remove();
					} else {
						getPostBlocksData_<?php echo $uniqidID; ?>('click', 'load');
					}
				}
			};
		}

		function showSelectedFilters(filters) {
			var defaultFilters = JSON.parse('<?php echo json_encode($defaultFiltersArray) ?>');
			filters = (Object.keys(filters).length == 0 && Object.keys(defaultFilters).length > 0) ? defaultFilters : filters;
			$(".selected-filters-<?php echo $uniqidID; ?> span a.clear-filters").hide();
			var selectedFiltersHtml = "";
			$("div.selected-filters ul").html(selectedFiltersHtml);
			$.each(filters, function(index, value) {
				selectedFiltersHtml = selectedFiltersHtml + '<li class="choice-list" title=' + index + '>';
				selectedFiltersHtml = selectedFiltersHtml + '<span id="' + index + '-unselect" class="products-unselect select2-selection__choice__remove" role="presentation">×</span>';
				selectedFiltersHtml = selectedFiltersHtml + value;
				selectedFiltersHtml = selectedFiltersHtml + '</li>';
			});

			if (selectedFiltersHtml != "") {
				$("div.selected-filters ul").html(selectedFiltersHtml);
				$(".selected-filters-<?php echo $uniqidID; ?> span a.clear-filters").show();
			}
		}

		function stripQueryStringAndHashFromPath(url) {
			return url.split("?")[0].split("#")[0];
		}

		/*filter remove link (x)- click*/
		$(document).on("click", '.choice-list .select2-selection__choice__remove', function(event) {
			if ($('.selected-filters ul li').length == 1) {
				var url = window.location.href;
				var cleanedUrl = stripQueryStringAndHashFromPath(url);
				window.location.href = cleanedUrl;
				//$(".selected-filters-<?php echo $uniqidID; ?> span a.clear-filters").hide();
			} else {
				$(this).parent().remove();
				var title = $(this).parent().attr("title");
				var removed_filter = $('.multi-select option[value="' + title + '"]');
				removed_filter.prop('selected', false);

				let search = $('.bs-posts__filters-<?php echo $uniqidID; ?> .search-box').val();

				getPostBlocksData_<?php echo $uniqidID; ?>('select', 'load', search);
				getPostBlocksData_<?php echo $uniqidID; ?>('search', 'maxPage', search);
			}
		});

		/*filter expand - click*/
		let i = 0;
		$(".filter-expand").on('click', "a", function(e) {
			e.preventDefault();
			const $animateEle = $(this).parents(".filter-expand").next('.filter-wrapper'),
				$height = $animateEle[0].scrollHeight;
			$animateEle.animate({
				height: (++i % 2) ? $height + "px" : 0
			}, 200);
			$(this).parents(".filter-expand").toggleClass("active");
			i = i++;
		});

		// infinite srolling call
		let loadMoreParentDiv = document.getElementById("maxPage_<?php echo $uniqidID; ?>").parentNode;

		const handleIntersect = (entries, observer) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					let search = $("#search_term_<?php echo $uniqidID; ?>").val();
					if (checkLoadMoreButton_<?php echo $uniqidID; ?>() && enableRequest) {
						$("#bs-posts__normal-grid-<?php echo $uniqidID; ?>").addClass('loading');
						getPostBlocksData_<?php echo $uniqidID; ?>('click', 'load', search);
					}
				}
			});
		}

		let observer = new IntersectionObserver(handleIntersect, {
			rootMargin: "0px"
		});
		observer.observe(loadMoreParentDiv);

	})(jQuery, window, document); // or even jQuery.noConflict()
</script>
