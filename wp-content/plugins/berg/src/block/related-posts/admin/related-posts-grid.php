<?php

if (!class_exists('WP_List_Table')) {
	require(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

require(__DIR__ . '/link-related-posts.php');

class BRP_List_Table extends WP_List_Table
{

	function __construct()
	{
		global $status, $page;

		//Set parent defaults
		parent::__construct(array(
			'singular'  => 'related-post',     //singular name of the listed records
			'plural'    => 'related-posts',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		));
	}

	function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'post_type':
			case 'post_date':
				return $item[$column_name];
			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_post_title($item)
	{

		//Build row actions
		$actions = array(
			'link' => sprintf(
				'<a href="?page=%s&amp;brp_parent=%s&amp;brp_create_link=%s">' . __('Link Post', 'berg-related-posts') . '</a>',
				$_REQUEST['page'],
				$_GET['brp_parent'],
				$item['ID']
			),
			'view' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				get_permalink($item['ID']),
				__('View Post')
			)
		);

		//Return the title contents
		return sprintf(
			'%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			/*$1%s*/
			$item['post_title'],
			/*$2%s*/
			$item['ID'],
			/*$3%s*/
			$this->row_actions($actions)
		);
	}

	function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="brp_bulk[]" value="%s" />',
			$item['ID']
		);
	}

	function get_columns()
	{
		$columns = array(
			'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'post_title'     => 'Title',
			'post_type'    => 'Post Type',
			'post_date'  => 'Post Date'
		);
		return $columns;
	}

	function get_sortable_columns()
	{
		$sortable_columns = array();
		return $sortable_columns;
	}

	function get_bulk_actions()
	{
		$actions = array(
			'bulklink' => __('Link Posts', 'berg-related-posts')
		);
		return $actions;
	}

	function process_bulk_action()
	{
		//Link all selected posts as a bulk
		if ('bulklink' === $this->current_action()) {
			create_link_bulk_from_admin();
		}
	}

	function prepare_items()
	{
		global $wpdb; //This is used only if making any database queries

		$parent_id = $_GET['brp_parent'];
		$current = (isset($_GET['brp_view']) ? $_GET['brp_view'] : 'related');

		if ($current == 'related') {
			$args = array(
				"currentPostId" => $parent_id,
				"orderBy" => "date",
				"order" => "DESC",
				"postTaxonomies" => get_taxonomy_terms_by_post_id($parent_id),
			);
			$data = get_related_posts($args);
		} else {
			$parent_post_type = get_post_type($parent_id);
			$child_ids = get_linked_post_ids($parent_id);
			$exclude = array_values($child_ids);
			$exclude[] = $parent_id;
			$args = array(
				"post_type" => $parent_post_type,
				'posts_per_page' => -1,
				"orderby" => "date",
				"order" => "DESC",
				"exclude" => $exclude
			);
			$all_posts = get_posts($args);
			$data = json_decode(json_encode($all_posts), true);
		}

		$per_page = 10;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$current_page = $this->get_pagenum();

		$total_items = count($data);

		$data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args(array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
		));
	}
}

function brp_add_menu_items()
{
	add_menu_page(NULL, NULL, 'activate_plugins', 'berg_related_posts_list', 'brp_render_list_page');
	remove_menu_page('berg_related_posts_list');
}
add_action('admin_menu', 'brp_add_menu_items');


function generate_page_tabs($current)
{
	$page_tabs = array(
		'related' => __('Related Posts', 'berg-related-posts'),
		'all'     => __('All Posts', 'related-posts-for-wp'),
	);

	$page_tabs_with_link = array();
	foreach ($page_tabs as $key => $val) {
		$page_tabs_with_link[$key] = "<a href='" . esc_url(add_query_arg(array(
			'brp_view' => $key,
			'paged'      => 1
		))) . "'" . (($current == $key) ? " class='current'" : "") . ">{$val}</a>";
	}
	return $page_tabs_with_link;
}

function brp_render_list_page()
{
	//Create an instance of our package class...
	$postListTable = new BRP_List_Table();
	//Fetch, prepare, sort, and filter our data...
	$postListTable->prepare_items();
	$current = (isset($_GET['brp_view']) ? $_GET['brp_view'] : 'related');
	// Parent
	$parent = $_GET['brp_parent'];
	// Setup cancel URL
	$cancel_url = get_admin_url() . "post.php?post={$parent}&action=edit";
	$page_tabs = generate_page_tabs($current);
?>
	<div class="wrap">
		<h2>
			<?php _e('Berg Related Posts', 'related-posts-for-wp'); ?>
			<a href="<?php echo $cancel_url; ?>" class="add-new-h2"><?php _e('Cancel linking', 'related-posts-for-wp'); ?></a>
		</h2>
		<form id="related-posts-filter" method="post">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<ul class="subsubsub">
				<?php foreach ($page_tabs as $key => $link) {
					echo "<li class='" . $key . "'>" . $link . " | </li>";
				}
				?>
			</ul>
			<!-- Now we can render the completed list table -->
			<?php $postListTable->display() ?>
		</form>

	</div>
<?php
}
