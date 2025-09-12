<?php

/**
 * Created by PhpStorm.
 * User: sivanoly
 * Date: 2/13/19
 * Time: 3:08 PM
 */
$terms = get_terms($_filter, array('hide_empty' => true));
uasort($terms, function($a, $b) {
	return strcmp($a->name, $b->name);
});
$taxonomy = get_taxonomy($_filter);
$firstOptionSelected = false;
if (count($terms)) {
?>
    <div class="form-group bs-posts__filter--<?php echo $taxonomy->name; ?>">
        <label for="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></label>
        <div class="select-wrapper">
            <select id="<?php echo $taxonomy->name; ?>" name="<?php echo $taxonomy->name; ?>" class="form-control">
                <option value=""> All <?php echo $taxonomy->label; ?></option>
                <?php foreach ($terms as $term) { ?>
                    <option value="<?php echo $term->slug; ?>" <?php if (
                                                                    get_query_var($_filter) == $term->slug ||
                                                                    ((in_array($term->slug, $defaultFilters) && !($firstOptionSelected)) && get_query_var("all_" . $_filter) != "true")
                                                                ) {
                                                                ?>selected="selected" <?php
                                                                                    } ?>><?php echo $term->name; ?></option>
                    <?php if (in_array($term->slug, $defaultFilters)) {
                        $firstOptionSelected = true;
                    } ?>
                <?php } ?>
            </select>
        </div>
    </div>
<?php } ?>
