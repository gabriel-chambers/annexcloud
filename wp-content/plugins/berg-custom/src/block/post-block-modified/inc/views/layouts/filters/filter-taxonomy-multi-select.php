<?php

/**
 * Created by PhpStorm.
 * User: sivanoly
 * Date: 2/13/19
 * Time: 3:08 PM
 */

$terms = get_terms(
	$_filter,
	array_merge(
		['hide_empty' => true],
		$resourceTypeFilterParentTerm
			? ['parent' => $resourceTypeFilterParentTerm->term_id]
			: []
	)
);
$taxonomy = get_taxonomy($_filter);
if (count($terms)) {
	$filter_label = $filterLabel
		? $filterLabel
		: ((isset($taxonomy->labels->filter_label)) ? $taxonomy->labels->filter_label : $taxonomy->label);

	$filter    = get_query_var($_filter);
	$filterArr = !empty($filter) ? explode(',', $filter) : [];
	if (!empty($filterArr)) {
		$currentSelection[$taxonomy->name] = $filterArr;
	}
	$filterArr = (!empty($currentSelection) && isset($currentSelection[$taxonomy->name]))
		? $currentSelection[$taxonomy->name]
		: $defaultFilters;

	$dynamicFilterLabel = $filter_label;
	if (isset($reflectSelectedFilterInPlaceholder)
		&& $reflectSelectedFilterInPlaceholder
	) {
		$selectedFilterLabels = [];
		foreach ($filterArr as $filterArrElement) {
			if ($resourceTypeFilterParentTerm
				&& $filterArrElement == $resourceTypeFilterParentTerm->slug
			) {
				continue;
			}
			$terms_filtered_by_slug = array_filter(
				$terms,
				function ($term) use ($filterArrElement) {
					return $term->slug == $filterArrElement;
				}
			);
			if (!empty($terms_filtered_by_slug)) {
				$selectedFilterLabels[] = array_shift($terms_filtered_by_slug)->name;
			}
		}
		if (!empty($selectedFilterLabels)
			&& sizeof($selectedFilterLabels) <= $numberOfFiltersToShowInPlaceholder
		) {
			$dynamicFilterLabel = implode(', ', $selectedFilterLabels);
		} else if (!empty($selectedFilterLabels)) {
			$dynamicFilterLabel = count($selectedFilterLabels) . ' selected';
		}
	}
?>
	<div class="form-group bs-posts__filter--<?php echo $taxonomy->name; ?>">
		<?php if (isset($reflectSelectedFilterInPlaceholder)
				&& $reflectSelectedFilterInPlaceholder
			) : ?>
			<span class="filter-label"><?php echo _e($filter_label); ?></span>
			<label class="dynamic-filter-label d-none" for="<?php echo $taxonomy->name; ?>">
				<?php echo _e($dynamicFilterLabel) ?>
			</label>
		<?php else : ?>
			<label class="filter-label" for="<?php echo $taxonomy->name; ?>"><?php echo _e($filter_label); ?></label>
		<?php endif; ?>
		<div class="select-wrapper">
			<select id="multi-filter-<?php echo $taxonomy->name; ?>" name="<?php echo $taxonomy->name; ?>[]" class="multi-filter multi-select form-control" multiple="multiple">
				<?php foreach ($terms as $term) { ?>
					<option value="<?php echo $term->slug; ?>" <?php if (in_array($term->slug, $filterArr)) { ?>selected="selected" <?php } ?>><?php echo $term->name; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
<?php } ?>
