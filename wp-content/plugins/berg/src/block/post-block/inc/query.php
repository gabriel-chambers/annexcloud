<?php
$_meta_key    = '';
$_tax_filters = array();
$_feaArgs     = null;
$curent_post_id = get_the_ID();

if (strpos($orderBy, 'meta_') !== false) {
    if ($orderBy === 'meta_value_num') {
        $_meta_key = 'wpb_post_views_count';
    } else {
        $_meta_key = str_replace('meta_', '', $orderBy);
        $orderBy   = 'meta_value';
    }
} else if ($orderBy === 'custom_date') {
    $orderBy   = 'custom_date_meta_clause';
}
// get the query vars
if ($filters) {
    foreach ($filters as $tax_filter) {
        if ($tax_filter == 'search') {
            if ((get_query_var($tax_filter) != 'search') && !$ajax) {
                $search_text = get_query_var($tax_filter);
            }
        } else {
            if (get_query_var($tax_filter) != "category") {
                if (get_query_var($tax_filter) != "") {
                    $_tax_filters[$tax_filter][] = get_query_var($tax_filter);
                }
            }
        }
    }
}

// Current datetime in UTC
$current_datetime_utc = date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ));

$meta_upcoming = array(
	'relation' => 'AND',
    array(
        'key'     => 'event_end_date', // Check the end date field
        'value'   => $current_datetime_utc, // Set today's date (note the similar format)
        'compare' => '>=', // Return the ones greater than today's date
        'type'    => 'DATETIME' // Let WordPress know we're working with date
    ),
    array(
        'key'     => 'event_date',
        'value'   => 1,
        'compare' => '=',
    )
);

$meta_past = array(
	'relation' => 'AND',
    array(
        'key'     => 'event_end_date', // Check the end date field
		'value'   => $current_datetime_utc, // Set today's date (note the similar format)
        'compare' => '<', // Return the ones greater than today's date
        'type'    => 'DATETIME' // Let WordPress know we're working with date
    ),
    array(
        'key'     => 'event_date',
        'value'   => 1,
        'compare' => '=',
    )
);

// Featured args
$feaArgs = array(
    'post_type'      => $postType,
    'post_status'    => array('publish'),
    'orderby'        => $orderBy,
    'order'          => $order,
    'posts_per_page' => $featuredNum
);

if ($_meta_key) {
    $feaArgs = array_slice($feaArgs, 0, 2, true) +
        array("meta_key" => $_meta_key) +
        array_slice($feaArgs, 2, count($feaArgs) - 2, true);
}

// Featured Query
if ($showFeatured) {
    $_feaArgs               = $feaArgs;
    $featured_q             = array(
        'relation' => 'AND',
        [
            'key'     => 'featured',
            'value'   => 1,
            'compare' => '=',
        ],
        [
            'relation' => 'OR',
            //check current page id in featured_page_list
            [
                'key' => 'featured_page_list',
                'value' => sprintf(';i:%d;', $curent_post_id),
                'compare' => 'LIKE'
            ],
            //select post which featured_page_list empty(a:0:{} is save for empty in database)
            [
                'key' => 'featured_page_list',
                'value' => 'a:0:{}',
                'compare' => '='
            ],
            //get old featured record(featured_page_list key not exist)
            [
                'key' => 'featured_page_list',
                'compare' => 'NOT EXISTS'
            ]
        ]
    );
    $_feaArgs['meta_query'] = $featured_q;

    if ($blockType == 'upcoming') {
        $_feaArgs['meta_query'] = array(
            'relation' => 'AND',
            $featured_q,
            $meta_upcoming
        );
    }
    if ($blockType == 'past') {
        $_feaArgs['meta_query'] = array(
            'relation' => 'AND',
            $featured_q,
            $meta_past
        );
    }

    //Filtering featured posts by default filter ('Filter by Type' option)
    $feaDefaultFilters = [];
    if (is_array($filterByType) && !empty($filterByType)) {
        if (count($filterByType) > 1) {
            $_feaArgs['tax_query']['relation'] = 'AND';
        }
        foreach ($filterByType as $feaTaxonomy) {
            if (is_array($feaTaxonomy) && array_key_exists("value", $feaTaxonomy)) {
                $feaDefaultTermId = $feaTaxonomy['value'];
                $feaDefaultTerm = get_term($feaDefaultTermId);
                $feaDefaultFilters[$feaDefaultTerm->taxonomy][] = $feaDefaultTerm->slug;
            }
        }
        foreach ($feaDefaultFilters as $key => $value) {
            $_feaArgs['tax_query'][] = array(
                'taxonomy' => $key,
                'field'    => 'slug',
                'terms'    => $value,
                'operator' => 'IN'
            );
        }
    }

    $_fea_the_query = new WP_Query($_feaArgs);

    if (!$_fea_the_query->post_count && $showDefaultFeatured) {
        if ($blockType == 'upcoming') {
            $feaArgs['meta_query'] = $meta_upcoming;
        }
        if ($blockType == 'past') {
            $feaArgs['meta_query'] = $meta_past;
        }
        //Appending featured posts taxonomy query when 'Show Default Featured Posts' is enabled
        $feaArgs['meta_query'] = $featured_q;
        $_fea_the_query = new WP_Query($feaArgs);
    }


    if ($_fea_the_query->have_posts() && filter_var($showFeatured, FILTER_VALIDATE_BOOLEAN)) {
        $featured_ids = array_column($_fea_the_query->posts, 'ID');
    }
} else {
    $_fea_the_query = new WP_Query();
}

// Default args
if ($postsNumberFirstLoad) {
    if ($paged == 1) {
        $limit  = $postsNumberFirstLoad;
        $offset = 0;
    } else {
        $limit  = $postsPerPage;
        $offset = ((((int) $paged - 2) * (int) $postsPerPage) + (int) $postsNumberFirstLoad);
    }
} else {
    $limit  = $postsPerPage;
    $offset = 0;
}
$args = array(
    'post_type'      => $postType,
    'post_status'    => array('publish'),
    'orderby'        => $orderBy,
    'order'          => $order,
    'paged'          => $paged,
    'posts_per_page' => $limit
);

if ($_meta_key) {
    $args = array_slice($args, 0, 2, true) +
        array("meta_key" => $_meta_key) +
        array_slice($args, 2, count($args) - 2, true);
}

if ($offset) {
    $args['offset'] = $offset;
}

if (is_array($tax_select_filters) && !empty($tax_select_filters)) {
    foreach ($tax_select_filters as $key => $value) {
        if ($multiselect) {
            $value                               = implode(',', $value);
            $_tax_filters[trim($key, '"')][] = $value;
        } else if ($value) {
            $_tax_filters[trim($key, '"')][] = $value;
        }
    }
}

//If default filters enabled by 'Filter by Type' option
if (isset($filterByType[0]) && is_array($filterByType[0]) && !empty($filterByType)) {
    $tax_query_array = $_tax_filters;
    if (count($filterByType) > 1) {
        $feaArgs['tax_query']['relation'] = 'OR';
    }
    $inc = 0;
    foreach ($filterByType as $_tax) {
        $term_id = $_tax['value'];
        $term = get_term($term_id);
        $term_taxonomy = $term->taxonomy;
        if (!in_array($term_taxonomy, $filters) || (in_array($term_taxonomy, $filters) && empty($tax_query_array))) {
            $_tax_filters[$term->taxonomy][] = $term->slug;
        }
    }
    foreach ($_tax_filters as $key => $value) {
        $feaArgs['tax_query'][$inc] = array(
            'taxonomy' => $key,
            'field'    => 'slug',
            'terms'    => $value,
            'operator' => 'IN'
        );
        $inc++;
    }
    unset($tax_query_array);
}

$inc = 0;
if (is_array($_tax_filters) && !empty($_tax_filters)) {
    foreach ($_tax_filters as $key => $value) {
        if ($multiselect) {
            $valueString = "";
            foreach ($value as $valueRow) {
                $valueString .= "," . $valueRow;
            }
            $valueString = substr($valueString, 1); // remove leading ","
            $value = explode(',', $valueString);
        }
        if ((in_array($loadType, ['load', 'infinite_scroll'])
                && $value
                && (empty($all_enabled)
                    || $all_enabled[$key] != "true"
                )
            )
            || ($loadType == 'pagination'
                && $value
                && get_query_var("all_" . $key) != "true"
            )
        ) {
            $args['tax_query'][$inc] = array(
                'taxonomy' => $key,
                'field'    => 'slug',
                'terms'    => array_unique($value),
                'operator' => 'IN'
            );
            $inc++;
        }
    }
}

if ($search_text != '') {
    $args['s'] = urldecode($search_text);
}

if (is_array($featured_ids) && !$displayFeaturedPostOnPostGrid) {
    $args['post__not_in'] = $featured_ids;
}

if ($blockType == 'upcoming') {
    $args['meta_query'] = $meta_upcoming;
}
if ($blockType == 'past') {
    $args['meta_query'] = $meta_past;
}
if ($orderBy === 'custom_date_meta_clause') { // when order by custom date
    if (!array_key_exists('meta_query', $args)) {
        $args['meta_query'] = [];
    }
    $args['meta_query'] = array_merge($args['meta_query'], [
        [
            'relation' => 'OR',
            'custom_date_meta_clause' => ['key' => 'custom_date', 'type' => 'DATE'],
            ['key' => 'custom_date', 'compare' => 'NOT EXISTS']
        ]
    ]);
}
$_the_query = new WP_Query($args);
