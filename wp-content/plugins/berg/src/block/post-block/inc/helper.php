<?php

/* Including common helper functions from lib folder */
require_once WP_PLUGIN_DIR  . "/berg/lib/helper/post-helper.php";

/* Post block helper functions */
if (!function_exists('get_post_block_data')) {
    function get_post_block_data()
    {
        $attributes =  json_decode(stripslashes($_POST['atts']), true);
        if ($attributes) {
            include "variables.php";
            $search_text = (isset($_POST['search'])) ? urlencode($_POST['search']) : '';
            $tax_select_filters = (isset($_POST['filters'])) ? $_POST['filters'] : [];
            $featured_ids = (isset($_POST['featured_ids'])) ? $_POST['featured_ids'] : [];
            $ajax = (isset($_POST['ajax'])) ? $_POST['ajax'] : false;
            $paged = (isset($_POST['paged'])) ? $_POST['paged'] : 1;
            $dataType = (isset($_POST['dataType'])) ? $_POST['dataType'] : '';
            $all_enabled = (isset($_POST['all_enabled'])) ? $_POST['all_enabled'] : [];
            include "query.php";

            if ($postsNumberFirstLoad) {
                $max_num_pages = ceil((($_the_query->found_posts - $postsNumberFirstLoad) / $postsPerPage) + 1);
            } else {
                $max_num_pages = $_the_query->max_num_pages;
            }
            if ($dataType == 'maxPage') {
                echo $max_num_pages;
            } else {
                include 'views/layouts/partial/grid.php';
            }
        }
        die();
    }
    add_action("wp_ajax_get_post_block_data", "get_post_block_data");
    add_action("wp_ajax_nopriv_get_post_block_data", "get_post_block_data");
}

if (!function_exists('add_custom_query_vars_filter')) {
    function add_custom_query_vars_filter($vars)
    {
        //Registering new parameter keys for 'All' filter option
        $taxonomies = get_taxonomies();
        foreach ($taxonomies as $key => $value) {
            $vars[] = "all_" . $value;
        }
        return $vars;
    }
    add_filter('query_vars', 'add_custom_query_vars_filter');
}

if (!function_exists('rename_featured_image_field_labels')) {
	function rename_featured_image_field_labels() {
		$args = array(
			'public' => true,
	);
		$post_types = get_post_types($args);
		foreach($post_types as $post_type){
			$get_post_type = get_post_type_object($post_type);
			$labels = $get_post_type->labels;
				$labels->featured_image = __( 'Image', 'textdomain' );
				$labels->set_featured_image = __( 'Set Image', 'textdomain' );
				$labels->remove_featured_image = __( 'Remove', 'textdomain' );
				$labels->replace_featured_image = __( 'Replace', 'textdomain' );
				$labels->use_featured_image = __( 'Use as the default image', 'textdomain' );
		}
	}
	add_action( 'init', 'rename_featured_image_field_labels' );
}
