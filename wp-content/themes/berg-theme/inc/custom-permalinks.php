<?php
if (!function_exists('berg_query_vars')) {

	function berg_query_vars( $qvars ) {
		$args = array(
			'public'   => true,
			'_builtin' => true
		);
		$output = 'names'; // or objects
		$operator = 'and'; // 'and' or 'or'
		$taxonomies = get_taxonomies( $args, $output, $operator );

		if(!empty($taxonomies)){
			foreach ($taxonomies AS $taxonomy){
				$qvars[] = $taxonomy;
			}
		}
		return $qvars;
	}
	add_filter( 'query_vars', 'berg_query_vars' );
}

