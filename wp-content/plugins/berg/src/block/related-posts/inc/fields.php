<?php

require(__DIR__ . '/attributes.php');

function brp_text_field_html($args)
{
	$field_name = 'brp_' . $args['id'];
	$field_value = get_option($field_name);
	if (!$field_value) {
		$field_value = $args['default'];
		update_option($field_name, $args['default']);
	}
	printf(
		'<input type="text" id="%s" name="%s" value="%s"/>',
		esc_attr($field_name),
		esc_attr($field_name),
		esc_attr($field_value)
	);
	echo '<p class="description">' . $args['desc'] . '</p>';
}

function brp_textarea_field_html($args)
{
	$field_name = 'brp_' . $args['id'];
	$field_value = get_option($field_name);
	if (!$field_value) {
		$field_value = $args['default'];
		update_option($field_name, $args['default']);
	}
	printf(
		'<textarea id="%s" name="%s" rows="4" cols="50">%s</textarea>',
		esc_attr($field_name),
		esc_attr($field_name),
		esc_attr($field_value)
	);
	echo '<p class="description">' . $args['desc'] . '</p>';
}

function brp_number_field_html($args)
{
	$field_name = 'brp_' . $args['id'];
	$field_value = get_option($field_name);
	if (!$field_value) {
		$field_value = $args['default'];
		update_option($field_name, $args['default']);
	}
	printf(
		'<input type="number" id="%s" name="%s" value="%u"/>',
		esc_attr($field_name),
		esc_attr($field_name),
		esc_attr($field_value)
	);
	echo '<p class="description">' . $args['desc'] . '</p>';
}

function brp_checkbox_field_html($args)
{
	$field_name = 'brp_' . $args['id'];
	$field_value = get_option($field_name);
	if (!$field_value) {
		$field_value = $args['default'];
		update_option($field_name, $args['default']);
	}
	$checked = ($field_value) ? "checked" : "";
	printf(
		'<input type="checkbox" id="%s" name="%s" value="1" %s/>',
		esc_attr($field_name),
		esc_attr($field_name),
		esc_attr($checked)
	);
	echo '<p class="description">' . $args['desc'] . '</p>';
}


function brp_select_field_html($args)
{
	$field_name = 'brp_' . $args['id'];
	$field_value = get_option($field_name) ? get_option($field_name) : $args['default'];

	printf(
		'<select id="%s" name="%s">',
		esc_attr($field_name),
		esc_attr($field_name)
	);

	foreach ($args['options'] as $key => $value) {
		$selected = ($key == $field_value) ? "selected" : "";
		printf(
			'<option value="%s" %s >%s</option>',
			esc_attr($key),
			esc_attr($selected),
			esc_attr($value)
		);
	}
	echo '</select>';
	echo '<p class="description">' . $args['desc'] . '</p>';
}

function brp_post_type_field_html($args)
{
	$field_name = 'brp_' . $args['id'];
	// Assigning default values for the first time
	if (!get_option($field_name)) {
		update_option($field_name, serialize($args['default']));
	}
	$post_types = get_post_types_by_support(array('editor', 'title', 'thumbnail'));
	foreach ($post_types as $post_type) {
		$pt = get_post_type_object($post_type);
		printf(
			'<div><input type="checkbox" name="%s[]" value="%s" />%s</div>',
			esc_attr($field_name),
			esc_attr($post_type),
			esc_attr($pt->labels->name),
		);
	}
	echo '<p class="description">' . $args['desc'] . '</p>';
}

function update_brp_post_installer()
{
	$selected_post_types =  (isset($_POST['brp_post_installer'])) ? $_POST['brp_post_installer'] : [];
	if (count($selected_post_types)) {
		foreach ($selected_post_types as $post_type) {
			delete_links_by_post_type($post_type);
		}
	}
	update_option('brp_post_installer', serialize($selected_post_types));
}
add_action('update_option_brp_post_installer', 'update_brp_post_installer', 10, 2);

function brp_displayorder_field_html($args)
{
	$field_name = 'brp_' . $args['id'];
	// Assigning default values for the first time
	if (!get_option($field_name)) {
		update_option($field_name, serialize($args['default']));
	}
	$field_values = unserialize(get_option($field_name));
	$selected_values_array = [];
	if (!empty($field_values)) {
		foreach ($field_values as $item) {
			$selected_values_array[] = array("value" => $item, "label" =>  str_replace("_", " ", ucwords($item)));
		}
	}
	$ordered_array = array_merge($selected_values_array, DISPLAY_ORDER);
	foreach ($ordered_array as $key => $line) {
		if (!in_array($line['value'], $selected_values_array)) {
			$selected_values_array[] = $line['value'];
			$final_dropdown[$key] = $line;
		}
	}
	printf(
		'<select id="%s" class="multi-select" name="%s[]" multiple>',
		esc_attr($field_name),
		esc_attr($field_name)
	);

	foreach ($final_dropdown as $row) {
		$selected = (in_array($row['value'], $field_values)) ? "selected" : "";
		printf(
			'<option value="%s" %s >%s</option>',
			esc_attr($row['value']),
			esc_attr($selected),
			esc_attr($row['label'])
		);
	}
	echo '</select>';

	echo '<p class="description">' . $args['desc'] . '</p>';
}

function update_brp_popup_display_order()
{
	$brp_popup_display_order =  (isset($_POST['brp_popup_display_order'])) ? $_POST['brp_popup_display_order'] : [];
	update_option('brp_popup_display_order', serialize($brp_popup_display_order));
}
add_action('update_option_brp_popup_display_order', 'update_brp_popup_display_order', 11, 2);

function update_brp_post_display_order()
{
	$brp_post_display_order =  (isset($_POST['brp_post_display_order'])) ? $_POST['brp_post_display_order'] : [];
	update_option('brp_post_display_order', serialize($brp_post_display_order));
}
add_action('update_option_brp_post_display_order', 'update_brp_post_display_order', 10, 2);
