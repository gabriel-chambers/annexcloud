<?php if ($showFilters) : ?>
	<div class="bs-posts__filters bs-posts__filters-<?php echo $uniqidID; ?>">
		<?php if ($filterLabel && !empty($filters)) : ?>
			<div class="bs-posts__filters--title">
				<h3><?php echo $filterLabel; ?></h3>
			</div>
		<?php endif; ?>
		<div class="bs-posts__filters--fields">
			<?php
			global $wp;
			$page_slug = $wp->request;
			//To be used to display selected filters as tags
			$filterTags = $filters;
			if ($showFilters && count($filters)) :
			?>
				<form class="form-inline mt-2 mt-md-0 resource-search" method="post" role="search" id="searchform" enctype="multipart/form-data" action="<?php echo esc_url(home_url($page_slug)); ?>">
					<?php
					if (current($filters) == 'search') :
						unset($filters[0]);
						include 'filter-search.php';
					endif;
					?>
					<div class="form-group bs-posts__filter--tax">
						<div class="filter-expand"><span><?php echo $filterLabel; ?></span>
							<div class="expand"><a href="javascript:void(0)"></a></div>
						</div>
						<div class="filter-wrapper">
							<?php
							foreach ($filters as $filterIndex => $_filter) :
								if ($_filter != 'search') :
									unset($filters[$filterIndex]);
									include 'filter-taxonomy-multi-select.php';
								endif;
							endforeach;
							?>
							<input type="hidden" name="page" id="page" value="<?php echo get_query_var('paged'); ?>" />
							<input type="hidden" name="filterAttr" id="filterAttr" value="<?php echo 'updateUrl, showSelected' ?>" />
							<?php if (isset($showFilterApplyButton) && $showFilterApplyButton) : ?>
								<div class="filter-submit">
									<input type="submit" name="Apply" class="btn btn-outline-success my-2 my-sm-0" id="searchsubmit" value="<?php echo __('Apply', 'blankslate'); ?>" />
								</div>
							<?php endif; ?>
						</div>
					</div>
					<?php
					if (end($filters) == 'search') :
						include 'filter-search.php';
					endif;
					?>
				</form>
			<?php
			endif;
			?>
		</div>
	</div>
	<div class="selected-filters selected-filters-<?php echo $uniqidID; ?>">
		<ul>
			<?php
			$tags_count = 0;
			foreach ($filterTags as $_filter) {
				$taxonomy = get_taxonomy($_filter);
				$selected_values = get_query_var($_filter);
				$selected_filters_arr =  !empty($selected_values) ? explode(',', $selected_values) : [];
				if (!empty($selected_filters_arr) && is_object($taxonomy)) {
					$currentSelection[$taxonomy->name] = $selected_filters_arr;
				}
				$selected_filters_arr = (!empty($currentSelection)
					&& is_object($taxonomy)
					&& isset($currentSelection[$taxonomy->name])
					&& $_filter != 'search')
					? $currentSelection[$taxonomy->name]
					: $defaultFilters;
				if (!empty($selected_filters_arr) && $_filter != 'search') {
					foreach ($selected_filters_arr as $selected_filter) {
						if ($resourceTypeFilterParentTerm
							&& $selected_filter == $resourceTypeFilterParentTerm->slug
						) {
							continue;
						}
						$term = get_term_by('slug', $selected_filter, $_filter);
						if ($term) {
							$tags_count++;
			?>				<li class="choice-list" title="<?php echo $selected_filter; ?>">
								<span id="<?php echo $selected_filter; ?>" class="unselect select2-selection__choice__remove" role="presentation">×</span><?php echo $term->name; ?>
							</li>
			<?php 		}
					}
				} else if (($search_query = get_query_var($_filter)) && $_filter == 'search') {
			?>
				<li class="choice-list" title="Search Query">
					<span id="search-query"
						role="presentation"
						class="unselect select2-selection__choice__remove"
					>×</span><?php echo $search_query ?>
				</li>
			<?php
				}
			}
			?>
		</ul>
		<span><a class="clear-filters" style="<?php echo ($tags_count == 0) ? 'display:none' : '' ?>" href="javascript:void(0);"><?php echo __('Clear Filters', 'blankslate'); ?></a></span>
	</div>
<?php endif; ?>
