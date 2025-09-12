<?php

/**
 * To exclude featured posts from all the templates
 */
//upcoming events
add_filter('facetwp_query_args', 'custom_facetwp_query_args', 11, 2);

function custom_facetwp_query_args($query_args, $class) {
    $filtered = is_filtered($class->ajax_params["facets"]);

    $event_key = 'event_end_date';

    if ($class->template['name'] == "upcoming_events") {
        if (!$filtered && ($class->template["name"] != "archive")) {
            $query_args['meta_query'] = get_upcoming_events_meta_query();
        }

        $query_args['meta_key'] = $event_key;
        add_filter('posts_orderby', 'order_by_event_dates', 10, 2);
    } else {
        if (!$filtered && ($class->template["name"] != "archive")) {
            $query_args['meta_query'] = get_general_meta_query();
        }
    }

    return $query_args;
}

function is_filtered($facets) {
    foreach ($facets as $facet) {
        if ($facet["facet_name"] != "pagination") {
            if (is_array($facet["selected_values"])) {
                if (count($facet["selected_values"]) > 0) { // filters
                    return true;
                }
            } else {
                if (!empty($facet["selected_values"])) { // search
                    return true;
                }
            }
        }
    }
    return false;
}

function get_upcoming_events_meta_query() {
    $event_key = 'event_end_date';
    $event_compare = '>=';

    return array(
        array(
            'relation' => 'OR',
            array(
                'key' => 'featured',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key' => 'featured',
                'value' => "",
                'compare' => '=',
            ),
        ),
        array(
            'relation' => 'AND',
            array(
                'key' => $event_key,
                'value' => date("Y-m-d"),
                'compare' => $event_compare,
                'type'    => 'DATE'
            ),
        )
    );
}

function get_general_meta_query() {
    return array(
        'relation' => 'OR',
        array(
            'key' => 'featured',
            'compare' => 'NOT EXISTS',
        ),
        array(
            'key' => 'featured',
            'value' => "",
            'compare' => '=',
        ),
    );
}

function order_by_event_dates($order_by, $wp_query)
{
    if (isset($wp_query->query["event_list_query"])
        && $wp_query->query["event_list_query"] === true
    ) {
        global $wpdb;
        $order_by = <<<EOQ
            CASE
                WHEN DATEDIFF({$wpdb->prefix}postmeta.meta_value, CURDATE()) >= 0 THEN 0
                ELSE 1
            END,
            ABS(DATEDIFF({$wpdb->prefix}postmeta.meta_value, CURDATE())) ASC
        EOQ;
    }
    return $order_by;
}


add_filter('facetwp_facet_where', 'modify_fselect_where_clause_to_include_all_types', 10, 2);

function modify_fselect_where_clause_to_include_all_types($where_clause, $facet_obj)
{
    if ('fselect' === $facet_obj['type']) {
        $facet = FWP()->facet;
        $listing_query_args = $facet->query_args;
        $filtered = is_filtered($facet->ajax_params["facets"]);
        $template_name = $facet->template["name"];
        $featured_meta_query = get_general_meta_query();

        if (!$filtered
            && 'archive' !== $template_name
            && isset($listing_query_args['meta_query'])
            && (
                json_encode($featured_meta_query)
                === json_encode($listing_query_args['meta_query'])
            )
        ) {
            $featured_resources_query_args = $listing_query_args;

            // Remove unnecessary args
            unset($featured_resources_query_args['paged']); // paging

            $featured_resources_query_args = array_merge(
                $featured_resources_query_args,
                [
                    'fields' => 'ids',                  // get only ids
                    'nopaging' => true,
                    'posts_per_page' => -1,             // get all posts
                    'meta_query' => [
                        // get only featured
                        [
                            'key' => 'featured',
                            'value' => 1,
                            'operator' => '='
                        ]
                    ]
                ]
            );

            $featured_resource_ids_query = new WP_Query($featured_resources_query_args);
            if ($featured_resource_ids_query->have_posts()) {
                $featured_resource_ids = $featured_resource_ids_query->posts;

                preg_match_all('/\d+/', $where_clause, $where_clause_post_id_matches);
                $all_resource_ids = array_merge(
                    !empty($where_clause_post_id_matches)
                    ? $where_clause_post_id_matches[0]
                    : [],
                    $featured_resource_ids
                );
                $all_resource_ids = array_unique($all_resource_ids);

                $where_clause = ' AND post_id IN (' . implode(',', $all_resource_ids) . ')';
            }
        }
    }
    return $where_clause;
}


/**
 * Facetwp clear filters shortcode
 * @param $atts
 * @return string
 */
function create_facetwp_reset_button_shortcode($atts)
{
    $attributes = shortcode_atts(array(
        'label' => 'CLEAR FILTERS',
        'class' => 'facetwp-reset-button',
        'autohide' => 'true',
    ), $atts);

    $autohide_class = ($attributes['autohide'] == 'true') ? ' reset-selection' : '';

    $attributes['class'] .= $autohide_class;

    return "<button class='" . $attributes['class'] . " reset-filters'
    onclick='FWP.reset()'>" . $attributes['label'] . "</button>";
}

add_shortcode('facetwp_reset', 'create_facetwp_reset_button_shortcode');

/**
 * Add 'All' checkbox for all checkbox facets type
 * @param $output
 * @param $atts
 * @return mixed|string
 */
function add_select_all_checkbox($output, $atts)
{
    if (isset($atts['facet'])) {
        $facet = FWP()->helper->get_facet_by_name($atts['facet']);

        if ($facet && $facet['type'] == 'checkboxes') {
            $facet_name = $facet['name'];
            $class = 'facetwp-all-' . $facet_name;
            $original_output = $output;
            $output = '<div class="facetwp-checkbox checked type-select-all facet-checkbox-select-all ' . $class . '"
                data-name="' . $facet_name . '"
                onclick="FWP.reset(' . "'$facet_name'" . ')">All</div>' . $original_output;
        }
    }
    return $output;
}

add_filter('facetwp_shortcode_html', 'add_select_all_checkbox', 10, 2);

/**
 * Ignore facetwp from using as the main query
 * @return boolean
 */
add_filter('facetwp_is_main_query', function () {
    //This will prevent using facetwp query as the default query
    return false;
}, 10, 1);
