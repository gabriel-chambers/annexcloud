<?php //Getting default filter option
$defaultFilters = [];
$currentSelection = [];
if (!empty($filterByType) && is_array($filterByType)) {
	foreach ($filterByType as $tax) {
		if(isset($tax['value'])){
			$defaultTermId = $tax['value'];
			$defaultTerm = get_term($defaultTermId);
			$defaultFilters[] = $defaultTerm->slug;
		}
	}
};
if ($multiselect) {
	// Filtration block with multi select
	include 'filters/filter-multiple.php';
} else {
	// Filtration block
	include 'filters/filter.php';
}
