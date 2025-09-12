<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_xxp_report_grid extends d4p_grid {
	public $_sanitize_orderby_fields = array( 'l.id', 'l.reported', 'l.ip' );
	public $_search_through_fields = array( 'ip', 'request_url', 'request_body' );
	public $_checkbox_field = 'id';
	public $_table_class_name = 'gdsih-grid-xxp';

	public $current_ip = '';
	public $server_ip = '';

	public $geoip = false;
	public $network = true;
	public $site = false;

	public $ids = array();
	public $ips = array();

	public $geo = null;

	function __construct( $args = array() ) {
		$this->current_ip = gdsih()->ip();
		$this->server_ip  = d4p_server_ip();

		parent::__construct( array(
			'singular' => 'xxp',
			'plural'   => 'xxps',
			'ajax'     => false,
		) );
	}

	private function _self( $args, $getback = false ) {
		$base_url = 'admin.php?page=gd-security-headers-xxp-reports';
		$url      = $base_url . '&' . $args;

		if ( $getback ) {
			$url .= '&_wpnonce=' . wp_create_nonce( 'gdsih-admin-panel' );
			$url .= '&gdsih_handler=getback';
			$url .= '&_wp_http_referer=' . wp_unslash( self_admin_url( $base_url ) );
		}

		return self_admin_url( $url );
	}

	protected function extra_tablenav( $which ) {
		if ( $which == 'top' ) {
			$all_periods = array_merge( array(
				'all'   => __( 'All Time', 'gd-security-headers' ),
				'hr-01' => __( 'Last hour', 'gd-security-headers' ),
				'hr-04' => __( 'Last 4 hours', 'gd-security-headers' ),
				'hr-08' => __( 'Last 8 hours', 'gd-security-headers' ),
				'hr-12' => __( 'Last 12 hours', 'gd-security-headers' ),
				'dy-01' => __( 'Last day', 'gd-security-headers' ),
				'dy-02' => __( 'Last 2 days', 'gd-security-headers' ),
				'dy-03' => __( 'Last 3 day', 'gd-security-headers' ),
				'dy-05' => __( 'Last 5 day', 'gd-security-headers' ),
				'dy-07' => __( 'Last 7 day', 'gd-security-headers' ),
				'dy-30' => __( 'Last 30 days', 'gd-security-headers' ),
			), $this->list_all_months_dropdown() );

			$_sel_period = ! empty( $_GET['filter-period'] ) ? d4p_sanitize_slug( $_GET['filter-period'] ) : 'all';

			echo '<div class="alignleft actions">';
			d4p_render_select( $all_periods, array( 'selected' => $_sel_period, 'name' => 'filter-period' ) );
			submit_button( __( 'Filter', 'gd-security-headers' ), 'button', false, false, array( 'id' => 'gdsih-csp-reports-submit' ) );
			echo '</div>';
		}
	}

	public function list_all_months_dropdown() {
		global $wp_locale;

		$sql    = "SELECT DISTINCT YEAR(reported) AS year, MONTH(reported) AS month FROM " . gdsih_db()->csp_reports . " ORDER BY reported DESC";
		$months = gdsih_db()->run( $sql );

		$list = array();

		foreach ( $months as $row ) {
			if ( $row->month > 0 && $row->year > 0 ) {
				$month = zeroise( $row->month, 2 );
				$year  = $row->year;

				$list[ $year . '-' . $month ] = sprintf( __( '%s %s', 'gd-security-headers' ), $wp_locale->get_month( $month ), $year );
			}
		}

		return $list;
	}

	public function rows_per_page() {
		$key = 'gdsih_rows_per_page_xxp_reports';

		$user     = get_current_user_id();
		$per_page = get_user_meta( $user, $key, true );

		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = 25;
		}

		return $per_page;
	}

	public function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'id'          => __( 'ID', 'gd-security-headers' ),
			'ip'          => __( 'IP', 'gd-security-headers' ),
			'request_url' => __( 'Requested URL', 'gd-security-headers' ),
			'data'        => __( 'Data', 'gd-security-headers' ),
			'reported'    => __( 'Reported', 'gd-security-headers' ),
		);

		if ( ! gdsih_scope()->is_network_admin() ) {
			unset( $columns['blog'] );
		}

		return $columns;
	}

	public function get_row_classes( $item ) {
		static $row_class = '';
		$row_class = $row_class == '' ? ' alternate' : '';

		$classes = array();

		if ( $row_class != '' ) {
			$classes[] = $row_class;
		}

		return $classes;
	}

	protected function get_sortable_columns() {
		$columns = array(
			'id'       => array( 'l.id', false ),
			'ip'       => array( 'l.ip', false ),
			'reported' => array( 'l.reported', false ),
		);

		if ( ! gdsih_scope()->is_network_admin() ) {
			unset( $columns['blog'] );
		}

		return $columns;
	}

	protected function get_bulk_actions() {
		$bulk = array(
			'delete' => __( 'Delete', 'gd-security-headers' ),
		);

		return $bulk;
	}

	protected function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->id );
	}

	protected function column_data( $item ) {
		$show = array();
		$keys = array( 'user_agent', 'request_body' );

		if ( isset( $item->user_agent ) && $item->user_agent != '' ) {
			$show[] = '<abbr title="' . $item->user_agent . '">' . __( 'User Agent', 'gd-security-headers' ) . '</abbr>';
		}

		$content = empty( $show ) ? '' : '<br/>';
		$buttons = array();

		$content .= '<a class="gdsih-log-view-event-data" href="#' . $item->id . '">' . __( 'View All Data', 'gd-security-headers' ) . '</a>';
		$content .= '<div id="gdsih-event-content-' . $item->id . '" style="display: none;">';
		$content .= '<table class="widefat"><thead><tr>';
		$content .= '<th scope="col" class="gdsih-popup-meta-key">' . __( 'Data', 'gd-security-headers' ) . '</th>';
		$content .= '<th scope="col" class="gdsih-popup-meta-value">' . __( 'Value', 'gd-security-headers' ) . '</th>';
		$content .= '</tr></thead><tbody>';

		foreach ( $keys as $key ) {
			if ( isset( $item->$key ) ) {
				$content .= '<tr><td>' . $key . '</td><td>' . $item->$key . '</td></tr>';
			}
		}

		$content .= '</tbody></table>';

		if ( ! empty( $buttons ) ) {
			$content .= '<div style="margin-top: 15px;">' . join( " ", $buttons ) . '</div>';
		}

		$content .= '</div>';

		return join( ', ', $show ) . $content;
	}

	protected function column_ip( $item ) {
		if ( empty( $item->ip ) ) {
			return __( 'No IP logged', 'gd-security-headers' );
		}

		$actions = array(
			'log' => '<a href="' . $this->_self( 's=' . $item->ip ) . '">' . __( 'Reports', 'gd-security-headers' ) . '</a>',
		);

		$title  = '';
		$status = '';

		if ( $item->ip == $this->current_ip ) {
			$status = 'current';
			$title  .= ' ' . __( 'Your current IP.', 'gd-security-headers' );
		} else if ( $item->ip == $this->server_ip ) {
			$status = 'server';
			$title  .= ' ' . __( 'Your server IP.', 'gd-security-headers' );
		}

		$content = sprintf( '<span title="%s" class="gdsih-ip-%s">%s</span>', trim( $title ), $status, $item->ip );

		return $content . $this->row_actions( $actions );
	}

	protected function column_reported( $item ) {
		$timestamp = gdsih()->datetime->timestamp_gmt_to_local( strtotime( $item->reported ) );

		return date( 'Y-m-d', $timestamp ) . '<br/>@ ' . date( 'H:i:s', $timestamp );
	}

	protected function column_default( $item, $column_name ) {
		return $item->$column_name;
	}

	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		$per_page = $this->rows_per_page();

		$table = gdsih_db()->xxp_reports;

		$query = "SELECT SQL_CALC_FOUND_ROWS l.*";
		$from  = " FROM $table l";

		$where = array();

		$last   = isset( $_GET['filter-period'] ) && ! empty( $_GET['filter-period'] ) ? d4p_sanitize_slug( $_GET['filter-period'] ) : '';
		$search = isset( $_GET['s'] ) && $_GET['s'] != '' ? sanitize_text_field( $_GET['s'] ) : '';

		if ( ! empty( $search ) ) {
			$_search = array();

			foreach ( $this->_search_through_fields as $field ) {
				$_search[] = "`" . $field . "` LIKE '%" . $search . "%'";
			}

			$where[] = '(' . join( ' OR ', $_search ) . ')';
		}

		if ( $last != '' && $last != 'all' ) {
			if ( strlen( $last ) == 7 ) {
				$date = explode( '-', $last );

				if ( count( $date ) == 2 ) {
					$where[] = "YEAR(l.`reported`) = " . intval( $date[0] );
					$where[] = "MONTH(l.`reported`) = " . intval( $date[1] );
				}
			} else {
				$date = explode( '-', $last );

				if ( $date[0] == 'dy' ) {
					$last = $date[1] * 24;
				}

				if ( $last > 0 ) {
					$where[] = "l.`reported` > DATE_SUB(NOW(), interval " . $last . " hour)";
				}
			}
		}

		$query .= $from;

		if ( ! empty( $where ) ) {
			$query .= ' WHERE ' . join( ' AND ', $where );
		}

		$orderby = ! empty( $_GET['orderby'] ) ? $this->sanitize_field( 'orderby', $_GET['orderby'], 'reported' ) : 'l.`reported` DESC, l.`id`';
		$order   = ! empty( $_GET['order'] ) ? $this->sanitize_field( 'order', $_GET['order'], 'DESC' ) : 'DESC';

		$query .= " ORDER BY $orderby $order";

		$paged = ! empty( $_GET['paged'] ) ? esc_sql( $_GET['paged'] ) : '';
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}

		$offset = intval( ( $paged - 1 ) * $per_page );
		$query  .= " LIMIT $offset, $per_page";

		$this->items = gdsih_db()->get_results( $query );

		$total_rows = gdsih_db()->get_found_rows();

		if ( ! empty( $this->items ) ) {
			$this->ids = wp_list_pluck( $this->items, 'id' );
			$this->ips = array_unique( wp_list_pluck( $this->items, 'ip' ) );
		}

		$this->set_pagination_args( array(
			'total_items' => $total_rows,
			'total_pages' => ceil( $total_rows / $per_page ),
			'per_page'    => $per_page,
		) );
	}
}
