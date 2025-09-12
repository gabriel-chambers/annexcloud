<?php

if ( ! class_exists( 'CM_API_Cache' ) ) {

	/**
	 *  Generic Database Cache Class
	 */
	final class CM_API_Cache {

		/**
		 * @var bool
		 */
		protected $table_exists = false;
		/**
		 * @var string
		 */
		protected $table_name = '';

		/**
		 * @param string $table_name
		 */
		public function __construct( string $table_name ) {
			$this->table_name = $table_name;
		}

		/**
		 * @param $term string
		 * @param $column_name string
		 *
		 * @return false
		 */
		public function get_result( $term, $column_name ) {
			static $_cache = array();
			global $wpdb;

			$this->check_if_table_exists();

			$this->check_if_column_exists( $column_name );

			$table_name = $this->get_table_name();

			$term = $this->prepare_term_for_db( $term );

			if ( isset( $_cache[ $term ] ) && isset( $_cache[ $term ]->$column_name ) ) {
				return $_cache[ $term ]->$column_name;
			}

			$sql    = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE term LIKE %s", $term );
			$result = $wpdb->get_row( $sql );

			if ( ! empty( $wpdb->last_error ) || $result === null || $result->$column_name === null ) {
				return false;
			}

			$_cache[ $term ] = $result;

			return $result->$column_name;
		}

		/**
		 * @return bool
		 */
		protected function check_if_table_exists() {
			global $wpdb;

			if ( ! empty( $this->table_exists ) ) {
				return $this->table_exists;
			}

			$table_name = $this->get_table_name();

			if ( ! $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) == $table_name ) {
				$this->create_table();
				$this->flush_table();
			}
			$this->table_exists = true;

			return $this->table_exists;
		}

		/**
		 * @return string
		 */
		protected function get_table_name() {
			global $wpdb;

			return $wpdb->prefix . $this->table_name;
		}

		/**
		 * @return void
		 */
		protected function create_table() {
			global $wpdb;
			$table_name = $this->get_table_name();

			$sql = "CREATE TABLE {$table_name} (
                id INT(11) NOT NULL AUTO_INCREMENT,
                term VARCHAR(128) NOT NULL,
                thesaurus TEXT NULL,
                dictionary TEXT NULL,
                wikipedia TEXT NULL,
                translate_title TEXT NULL,
                translate_content TEXT NULL,
                PRIMARY KEY  (id),
                KEY term_id (id),
                KEY term_name (term)
              )
          CHARACTER SET utf8 COLLATE utf8_general_ci;";
			include_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			$wpdb->query( "ALTER TABLE {$table_name} DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;" );
		}

		/**
		 * @return void
		 */
		public function flush_table() {
			global $wpdb;
			$table_name = $this->get_table_name();
			$sql        = $wpdb->prepare( "TRUNCATE TABLE {$table_name}" );
			$wpdb->query( $sql );
		}

		/**
		 * @param string $column_name
		 *
		 * @return void
		 */
		protected function check_if_column_exists( string $column_name ) {
			static $_cache = array();
			global $wpdb;
			$table_name    = $this->get_table_name();
			$column_name   = $this->sanitize_column_name( $column_name );
			$column_exists = $_cache[ $column_name ] ?? $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table_name} LIKE %s", $column_name ) ) == $column_name;
			if ( ! $column_exists ) {
				$this->alter_table( $column_name );
			}
			$_cache[ $column_name ] = $column_exists;
		}

		/**
		 * @param string $column_name
		 *
		 * @return string
		 */
		protected function sanitize_column_name( string $column_name ) {
			return substr( $column_name, 0, 64 );
		}

		/**
		 * @param $column_name
		 *
		 * @return void
		 */
		protected function alter_table( $column_name ) {
			global $wpdb;
			$table_name = $this->get_table_name();
			$sql        = $wpdb->prepare( "ALTER TABLE {$table_name} ADD COLUMN {$column_name} TEXT NULL;" );
			include_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			$wpdb->query( $sql );
		}

		/**
		 * @param $term
		 * @param $column_name
		 * @param $value
		 *
		 * @return bool|int|mysqli_result|resource|null
		 */
		public function cache_result( $term, $column_name, $value ) {
			global $wpdb;

			$this->check_if_table_exists();

			$this->check_if_column_exists( $column_name );

			$table_name = $this->get_table_name();

			$term = $this->prepare_term_for_db( $term );

			$sql    = $wpdb->prepare( "SELECT term FROM {$table_name} WHERE term LIKE %s", $term );
			$result = $wpdb->get_row( $sql );
			if ( empty( $result ) ) {
				$result = $wpdb->insert( $table_name, array( 'term' => $term, $column_name => $value ) );
			} else {
				$result = $wpdb->update( $table_name, array( $column_name => $value ), array( 'term' => $term ), array( '%s' ), array( '%s' ) );
			}

			return $result;
		}

		/**
		 * @param string $term
		 * @param string $column_name
		 *
		 * @return bool|int|mysqli_result|resource|null
		 */
		public function flush_result( $term, $column_name = '' ) {
			global $wpdb;

			$this->check_if_table_exists();

			$table_name = $this->get_table_name();

			$term = $this->prepare_term_for_db( $term );

			if ( ! $column_name ) {
				$result = $wpdb->delete( $table_name, array( 'term' => $term ) );
			} else {
				$result = $wpdb->update( $table_name, array( $column_name => false ), array( 'term' => $term ), array( '%s' ), array( '%s' ) );
			}

			return $result;
		}

		protected function prepare_term_for_db( $term ) {
			$term = mb_strtolower( $term );

			return $term;
		}

	}
}