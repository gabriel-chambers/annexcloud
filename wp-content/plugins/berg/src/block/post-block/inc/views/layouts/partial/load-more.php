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

		$(".bs-posts__load-more-btn-<?php echo $uniqidID; ?>").unbind('click').on("click", function() {
			$("#bs-posts__normal-grid-<?php echo $uniqidID; ?>").addClass('loading');
			getPostBlocksData_<?php echo $uniqidID; ?>('click', 'load');
		});
		$(".bs-posts__filters-<?php echo $uniqidID; ?>").on("change", "select", function() {
			$("#bs-posts__normal-grid-<?php echo $uniqidID; ?>").addClass('loading');
			getPostBlocksData_<?php echo $uniqidID; ?>('select', 'load');
			getPostBlocksData_<?php echo $uniqidID; ?>('select', 'maxPage');
		});

		$(".bs-posts__filters-<?php echo $uniqidID; ?>").on("keyup", ".search-box", function(e) {
			if (e.keyCode === 13) {
				getPostBlocksData_<?php echo $uniqidID; ?>('search', 'load', $(this).val());
				getPostBlocksData_<?php echo $uniqidID; ?>('search', 'maxPage', $(this).val());
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
				if ($(this).has('option:selected')) {
					filters['"' + $(this).attr("name") + '"'] = $(this).val();
					if ($(this).prop('selectedIndex') == 0) {
						all_enabled[$(this).attr("name")] = "true";
					} else {
						all_enabled[$(this).attr("name")] = "false";
					}
				}
			});

			var defaultLoader = (atts.custom_load_more_button.image.url != null) ?
				atts.custom_load_more_button.image.url : '<?php echo plugin_dir_url(__DIR__) ?>loader.gif';

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
					'all_enabled': all_enabled,
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
				const currentUrl = document.location.href;
				const prevUrl = document.referrer;
				const currentUrlHash = document.location.hash;
				// Prevent re-rendering in hash navigation
				if (prevUrl && currentUrl.replace(prevUrl, '') === currentUrlHash) {
					return;
				}
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
					$('.bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-posts__normal-row > div')
						.slice(-<?php echo $postsPerPage; ?>)
						.remove();
					$('#paged_<?php echo $uniqidID; ?>').val(paged - 1);
					checkLoadMoreButton_<?php echo $uniqidID; ?>();
				} else {
					// history fwd
					if (url_paged_val === 0) {
						$('.bs-posts__normal-grid-<?php echo $uniqidID; ?> .bs-posts__normal-row > div')
							.slice(-<?php echo $postsPerPage; ?>)
							.remove();
					} else {
						getPostBlocksData_<?php echo $uniqidID; ?>('click', 'load');
					}
				}
			};
		}
	})(jQuery, window, document); // or even jQuery.noConflict()
</script>
