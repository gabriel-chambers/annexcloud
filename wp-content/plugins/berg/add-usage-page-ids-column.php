<?php
add_filter('manage_wp_block_posts_columns', 'add_usage_page_ids');

function add_usage_page_ids($columns)
{
  $columns['usage_page'] = "<a><span>Usage (Post ids)</span></a>";
  return $columns;
}

add_action('manage_wp_block_posts_custom_column', 'add_usage_page_column_value', 10, 2);
function add_usage_page_column_value($column, $post_id)
{
  if ('usage_page' === $column) {
    global $wpdb;
			$tag = '<!-- wp:block {"ref":' . $post_id . '}';
			$search = '%' . $wpdb->esc_like($tag) . '%';
			$instances = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts WHERE post_content LIKE %s and post_type NOT IN ( %s, %s, %s )", $search, 'revision', 'attachment', 'nav_menu_item') );
			$count_instances = count( $instances );
			if ( $count_instances > 0 ) {
				echo '<p><strong>';
				echo sprintf( 
					_n( 'Used in %s post:', 'Used in %s posts:', $count_instances, 'berg-reusable-blocks' ),
					$count_instances
				);
				echo '</strong></p>';
			}
			if ( $instances ) {
				$count = 1;
				foreach( $instances as $instance ){
          $divider = $count_instances > 1 && $count < $count_instances ? ', ' : '';
          $title = esc_html($instance->post_title) ." [ ". get_post_type( $instance->ID ) ." ]";
          echo "<a title='".$title."'href='".get_edit_post_link($instance->ID)."'>" . $instance->ID . $divider . "</a>"; 
					$count++;
				}
			}
  }
}