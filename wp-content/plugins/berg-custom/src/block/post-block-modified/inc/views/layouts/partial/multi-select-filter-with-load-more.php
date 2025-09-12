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
	$_link_attributes = array(
		'href'  => "javascript:void(0);",
		'class' => "bs-posts__load-more-btn bs-posts__load-more-btn-$uniqidID",
		'label' => "$loadMoreLabel",
	);
	echo render_link('full', $_link_attributes);
	?>
	<input type="hidden" name="maxPage" id="maxPage_<?php echo $uniqidID; ?>" value="<?php echo $max_num_pages; ?>">
	<input type="hidden" name="paged" id="paged_<?php echo $uniqidID; ?>" value="2">
</div>

<script>
	(function($, window, document) {
		let resourceTypeFilterParentTermTax, resourceTypeFilterParentTermSlug;
		<?php
		if ($resourceTypeFilterParentTerm) {
			echo <<<EOQ
			resourceTypeFilterParentTermTax = '{$resourceTypeFilterParentTerm->slug}';
			resourceTypeFilterParentTermSlug = '{$resourceTypeFilterParentTerm->taxonomy}';
EOQ;
		}
		?>

		// Check load more [show/hide button]
		function checkLoadMoreButton_<?php echo $uniqidID; ?>() {
			var paged = parseInt($('#paged_<?php echo $uniqidID; ?>').val());
			var valMaxPage = parseInt($('#maxPage_<?php echo $uniqidID; ?>').val());
			if (paged > valMaxPage) {
				$('.bs-posts__load-more-btn-<?php echo $uniqidID; ?>').hide();
			} else {
				$('.bs-posts__load-more-btn-<?php echo $uniqidID; ?>').show();
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
			}
		}, );

		let searchQuery = '';
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

			if (mode === 'select' || mode === 'search') {
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

			if (search.length > 0) {
				filterLabels['search-query'] = search;
			}
			searchQuery = search;
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
			let existingParams = new URLSearchParams(new URL(window.location.href).search);
			for (let [key, value] of existingParams) {
				if (key.startsWith('_')) {
					searchParams.set(key, value);
				}
			}
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
					} else {
						$(".bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-posts__normal-row").html('<div class="bs-posts__not-found col-12"><h3><?php echo $noEntriesFoundText; ?></h3></div>');
						$('.bs-posts__load-more-btn-<?php echo $uniqidID; ?>').hide();
					}

					$('.bs-post-loading-icon').hide();
					$("#bs-posts__normal-grid-<?php echo $uniqidID; ?>").removeClass('loading');
				},
				error: function(errorThrown) {
					console.error(errorThrown);
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
			if (resourceTypeFilterParentTermTax && defaultFilters[resourceTypeFilterParentTermTax]) {
				delete defaultFilters[resourceTypeFilterParentTermTax];
			}
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

			let reflectSelectedFilterInPlaceholder = <?php echo isset($reflectSelectedFilterInPlaceholder) ? $reflectSelectedFilterInPlaceholder : 'false' ?>;
			if (reflectSelectedFilterInPlaceholder) {
				let defaultFilterLabel = $('.bs-posts__filters-<?php echo $uniqidID; ?> span.filter-label').text();
				let numberOfFiltersToShowInPlaceholder = <?php echo $numberOfFiltersToShowInPlaceholder ?>;
				let dynamicFilterLabel = defaultFilterLabel;
				let applicableFilters = Object.values(
					Object.fromEntries(
						Object.entries(filters).filter(([key]) => key != 'search-query')
					)
				);
				if (applicableFilters.length > 0 &&
					applicableFilters.length <= numberOfFiltersToShowInPlaceholder
				) {
					dynamicFilterLabel = applicableFilters.join(', ');
				} else if (applicableFilters.length > 0 &&
					applicableFilters.length > numberOfFiltersToShowInPlaceholder
				) {
					dynamicFilterLabel = `${applicableFilters.length} selected`;
				}
				let resourceTypeFilter = $('.bs-posts__filters-<?php echo $uniqidID; ?> select.multi-filter.multi-select');
				if (resourceTypeFilter) {
					resourceTypeFilter.next('.select2-container').find('.select2-selection__rendered .select2-search span:last-of-type').text(dynamicFilterLabel);
				}
			}
		}

		function stripQueryStringAndHashFromPath(url) {
			return url.split("?")[0].split("#")[0];
		}

		let filterApplyTimeouts = {};
		function throttle(key, callback, timeout = 800) {
			if (filterApplyTimeouts[key]) {
				clearTimeout(filterApplyTimeouts[key]);
			}
			filterApplyTimeouts[key] = setTimeout(callback, timeout);
		}

		/*filter remove link (x)- click*/
		$(document).on("click", '.choice-list .select2-selection__choice__remove', function(event) {
			if (['search-query', 'search-query-unselect'].indexOf(event.currentTarget.id) != -1) {
				var parentWrapperElement = event.currentTarget
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

			// Hide the Clear filter button without delay
			let parentDiv = $('.selected-filters-<?php echo $uniqidID; ?>');
			if(parentDiv.find('.choice-list').length === 0) {
				parentDiv.find('.clear-filters').hide();
			}
			let timeoutKey = `filter-unselect-<?php echo $uniqidID; ?>`;
			throttle(timeoutKey, () => {
				let search = $('.bs-posts__filters-<?php echo $uniqidID; ?> .search-box').val();

				getPostBlocksData_<?php echo $uniqidID; ?>('select', 'load', search);
				getPostBlocksData_<?php echo $uniqidID; ?>('search', 'maxPage', search);
			});
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

		$('.selected-filters-<?php echo $uniqidID; ?> .clear-filters').on('click', (event) => {
			event.preventDefault();
			let urlStr = window.location.href;
			let url = new URL(urlStr);
			let searchParams = new URLSearchParams(url.search);
			for (let [key, value] of searchParams) {
				if (!key.startsWith('_')) {
					searchParams.delete(key);
				}
			}
			// For some reason "search" doesn't comes into above loop
			if (searchParams.has('search')) {
				searchParams.delete('search');
			}
		  	$('.selected-filters-<?php echo $uniqidID; ?> .choice-list .select2-selection__choice__remove').click();
		});

		<?php if (isset($showFilterApplyButton) && !$showFilterApplyButton) : ?>
		$('.bs-posts__filters-<?php echo $uniqidID; ?> form input[type=text]')
			.on('keyup', (event) => {
				let element = event.target;
				let timeoutKey = `input-${element.name}-<?php echo $uniqidID; ?>`;
				throttle(timeoutKey, () => {
					let search = element.value;
					if (search != searchQuery) {
						getPostBlocksData_<?php echo $uniqidID; ?>('search', 'load', search);
						getPostBlocksData_<?php echo $uniqidID; ?>('select', 'maxPage', search);
					}
				});
			});
		$('.bs-posts__filters-<?php echo $uniqidID; ?> form select.multi-select')
			.each((index, selectElement) => {
				let observer = new MutationObserver((mutationList, observer) => {
					for (const mutation of mutationList) {
						if (mutation.attributeName === 'data-select2-id') {
							observer.disconnect();
							let select2Id = selectElement.dataset.select2Id;
							$(document).on(
								'click',
								`[data-select2-id^="select2-${select2Id}-result"]`,
								(event) => {
									let timeoutKey = `input-${selectElement.name}-<?php echo $uniqidID; ?>`;
									throttle(timeoutKey, () => {
										let search = $('.bs-posts__filters-<?php echo $uniqidID; ?> .search-box').val();
										getPostBlocksData_<?php echo $uniqidID; ?>('select', 'load', search);
										getPostBlocksData_<?php echo $uniqidID; ?>('search', 'maxPage', search);
									});
								}
							)
						}
					}
				});
				observer.observe(selectElement, {
					attributes: true,
					childList: false,
					subtree: false
				});
			});
		<?php endif; ?>
	})(jQuery, window, document); // or even jQuery.noConflict()
</script>
