<?php

/**
 * Created by PhpStorm.
 * User: sivanoly
 * Date: 2/13/19
 * Time: 3:08 PM
 */
$terms = get_terms($_filter, array('hide_empty' => true));
$taxonomy = get_taxonomy($_filter);
if (count($terms)) {
	$filter_label = (isset($taxonomy->labels->filter_label)) ? $taxonomy->labels->filter_label : $taxonomy->label;
	$filter    = get_query_var($_filter);
	$filterArr = !empty($filter) ? explode(',', $filter) : [];
	if (!empty($filterArr)) {
		$currentSelection[$taxonomy->name] = $filterArr;
	}
	$filterArr = (!empty($currentSelection) && isset($currentSelection[$taxonomy->name]))
		? $currentSelection[$taxonomy->name]
		: $defaultFilters;
?>
	<div class="form-group bs-posts__filter--<?php echo $taxonomy->name; ?>">
		<label class="filter-label" for="<?php echo $taxonomy->name; ?>"><?php echo __($filter_label); ?></label>
		<div class="select-wrapper">
			<select id="multi-filter-<?php echo $taxonomy->name; ?>" name="<?php echo $taxonomy->name; ?>[]" class="multi-filter multi-select form-control" multiple="multiple">
				<?php foreach ($terms as $term) { ?>
					<option value="<?php echo $term->slug; ?>" <?php if (in_array($term->slug, $filterArr)) { ?>selected="selected" <?php } ?>><?php echo $term->name; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
<?php } ?>
