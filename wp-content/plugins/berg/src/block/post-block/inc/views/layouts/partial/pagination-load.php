<?php
wp_reset_query();
wp_reset_postdata();

if ( $loadType == 'pagination' && $multiselect) { // Default Pagination
	include( "multi-select-filter-with-pagination.php" );
} elseif ($multiselect && $loadType == 'load') {
	include( "multi-select-filter-with-load-more.php" );
} elseif ($multiselect && $loadType == 'infinite_scroll') {
  include("multi-select-filter-with-infinite-scroll.php");
} elseif ($loadType == 'pagination'){
	include( "pagination.php" );
} elseif ($loadType == 'load') {
	include( "load-more.php" );
} elseif ($loadType == 'infinite_scroll') {
  include("infinite-scroll.php");
}


