<?php

class CMTT_Import_Export {

	public static function init() {

		add_action( 'wp_ajax_cmtt_get_glossary_backup', array( __CLASS__, 'cmtt_glossary_get_backup' ) );
		add_action(
			'wp_ajax_nopriv_cmtt_get_glossary_backup',
			array(
				__CLASS__,
				'cmtt_glossary_get_backup',
			)
		);

		add_action( 'admin_init', array( __CLASS__, '_cmtt_rescheduleBackup' ) );
		add_action( 'cmtt_glossary_backup_event', array( __CLASS__, '_cmtt_doBackup' ) );
		add_action( 'cmtt_add_submenu_pages', array( __CLASS__, 'add_submenu_pages' ) );
		add_action( 'admin_init', array( __CLASS__, 'cmtt_glossary_handleexport' ) );
		add_action( 'wp_ajax_cmtt_export_terms', array( __CLASS__, 'cmtt_glossary_ajaxExport' ) );
        add_action('wp_ajax_cmtt_import_terms', array( __CLASS__,'cmtt_glossary_ajaxImport'));
	}

	public static function add_submenu_pages() {
		add_submenu_page(
			CMTT_MENU_OPTION,
			'TooltipGlossary Import/Export',
			'Import/Export',
			'manage_glossary',
			'cmtt_importexport',
			array(
				__CLASS__,
				'cmtt_importExport',
			)
		);
	}

	public static function cmtt_importExport() {
		$showCredentialsForm    = self::_cmtt_backupGlossary();
		$showBackupDownloadLink = self::_cmtt_getBackupGlossary( false, $showCredentialsForm );
		$backup_button_label    = self::get_backup_button_label();

		ob_start();
		include 'views/backend/admin_importexport.php';
		$content = ob_get_clean();
		include 'views/backend/admin_template.php';
	}

	/**
	 * Backups the glossary
	 */
	public static function _cmtt_backupGlossary() {
		if ( empty( $_POST ) ) {
			return false;
		}

		if ( isset( $_POST['cmtt_doBackup'] ) ) {
			check_admin_referer( 'cmtt_do_backup' );
			$url               = wp_nonce_url( 'admin.php?page=cmtt_importexport' );
			$export_chunk_size = filter_input( INPUT_POST, 'cmtt_process_chunk_size', FILTER_SANITIZE_NUMBER_INT );
			$filename          = self::_cmtt_doBackup( $url, $export_chunk_size );

			return $filename;
		}

		return false;
	}

	public static function _cmtt_doBackup( $url = null, $export_chunk_size = 1000 ) {
		$export_data = self::_cmtt_prepareExportGlossary( $export_chunk_size );

		$filename  = self::_get_backup_file_path( $export_data['new'] );
		$outstream = fopen( $filename, 'a' );
		chmod( $filename, 0775 );

		if ( $export_data['new'] ) {
			$header_map = $export_data['header_map'];
			fputcsv( $outstream, $export_data['header_map'], ',', '"' );
		}

		foreach ( $export_data['data'] as $line_number => $line_array ) {
			$line      = $line_array;
			$line_keys = array_keys( $line );
			$diff      = array_diff( $export_data['header_map'], $line_keys );

			foreach ( $diff as $key => $value ) {
				if ( ! isset( $line[ $key ] ) && ! isset( $line[ $value ] ) ) {
					$line[ $value ] = '';
				}
			}

			fputcsv( $outstream, $line, ',', '"' );
			unset( $export_data['data'][ $line_number ] );
		}

		fclose( $outstream );
		chmod( $filename, 0644 );

		\CM\CMTT_Settings::set( 'cmtt_glossary_last_backup_filename', $filename );
		$backup_lines_saved = \CM\CMTT_Settings::get( 'cmtt_glossary_backup_lines_saved', 0 );
		\CM\CMTT_Settings::set( 'cmtt_glossary_backup_lines_saved', $backup_lines_saved + $export_data['count'] );

		return $filename;
	}

	public static function _cmtt_prepareExportGlossary( $export_chunk_size = 1000 ) {
		$backup_lines_saved = \CM\CMTT_Settings::get( 'cmtt_glossary_backup_lines_saved', 0 );

		$export_columns_header = array(
			'Id',
			'Title',
			'Excerpt',
			'Description',
			'Synonyms',
			'Variations',
			'Categories',
			'Abbreviation',
			'Tags',
			'Image',
			'Languages',
		);
		$export_columns_header = apply_filters( 'cmtt_export_header_row', $export_columns_header );

		$args = array(
			'post_type'              => 'glossary',
			'post_status'            => 'publish',
			'nopaging'               => true,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids'
		);

		$q          = new WP_Query( $args );
		$exportData = array();
		$postsArray = $q->get_posts();
		$q          = null;

		$ignored_meta_keys = array(
			'cmtt_synonyms',
			CMTT_Related::TRANSIENT_NAME,
			'cmtt_variations',
			'cmtt_abbreviations'
		);

		$export_data_count = count( $postsArray );
		$new               = $backup_lines_saved == 0 || $backup_lines_saved >= $export_data_count;

		if ( $new ) {
			$backup_lines_saved = 0;
			\CM\CMTT_Settings::set( 'cmtt_glossary_backup_lines_saved', 0 );
			\CM\CMTT_Settings::set( 'cmtt_glossary_backup_items', $export_data_count );
		}
		$postsArray = array_slice( $postsArray, $backup_lines_saved, $export_chunk_size );

		foreach ( $postsArray as $glossary_term ) {

			$glossary_term = get_post( $glossary_term );

			$exportDataRow = array(
				$glossary_term->ID,
                html_entity_decode($glossary_term->post_title, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($glossary_term->post_excerpt, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($glossary_term->post_content, ENT_QUOTES, 'UTF-8'),
				CMTT_Synonyms::getSynonyms( $glossary_term->ID, true ),
				CMTT_Synonyms::getSynonyms( $glossary_term->ID, false ),
				'',
				'',
				'',
				get_the_post_thumbnail_url( $glossary_term->ID, 'full' ),
				'',
			);

			$meta = get_post_meta( $glossary_term->ID );
			foreach ( $meta as $item => $value ) {

				$is_own_meta    = ( strpos( $item, 'cmtt' ) ) !== false || ( strpos( $item, '_glossary_related_article' ) ) !== false;
				$is_not_ignored = ! in_array( $item, $ignored_meta_keys );
				$is_unique      = ! in_array( $item, $export_columns_header );

				if ( $is_own_meta && $is_not_ignored ) {
					if ( $is_unique ) {
						$export_columns_header[] = $item;
					}

					if ( $item === '_glossary_related_article' ) {
						/*
						 * For the sake of export each custom related article is being exported
						 * as a separate column in the CSV file for better readibility
						 */
						if ( is_array( $value ) ) {
							$related_articles = unserialize( $value[0] );
							if ( is_array( $related_articles ) ) {
								foreach ( $related_articles as $related_article ) {
									$related_article_metakey = $item . '_' . $related_article['name'];
									if ( ! in_array( $related_article_metakey, $export_columns_header ) ) {
										$export_columns_header[] = $related_article_metakey;
									}
									$exportDataRow[ $related_article_metakey ] = $related_article['url'];
								}
							}
						}
					} else {
						$exportDataRow[ $item ] = is_array( $value ) ? $value[0] : $value;
					}
				}
			}

			$exportDataRowWithMeta = apply_filters( 'cmtt_export_data_row', $exportDataRow, $glossary_term );
			$exportData[]          = $exportDataRowWithMeta;
		}

		return array(
			'new'        => $new,
			'header_map' => $export_columns_header,
			'data'       => $exportData,
			'count'      => min( $export_data_count, $export_chunk_size ),
		);
	}

	private static function _get_backup_file_path( $new = false ) {

		$backup_filepath = self::get_backup_filename( $new );
		if ( $new ) {
			// get the upload directory
			$upload_dir = wp_upload_dir();
			$path       = trailingslashit( $upload_dir['basedir'] ) . 'cmtt/';

			if ( ! file_exists( $path ) ) {
				wp_mkdir_p( $path );
			}
			chmod( $path, 0775 );
			$backup_filepath = $path . $backup_filepath;
			\CM\CMTT_Settings::set( 'cmtt_glossary_backup_lines_saved', 0 );
		}

		return $backup_filepath;
	}

	private static function get_backup_filename( $new = false ) {
		$filename = '';
		if ( ! $new ) {
			$filename = \CM\CMTT_Settings::get( 'cmtt_glossary_last_backup_filename', '' );
		}
		if ( $new || empty( $filename ) ) {
			$filename = 'glossary_backup_' . date( 'Ymd_His', current_time( 'timestamp' ) ) . '.csv';
		}

		return $filename;
	}

	/**
	 * Outputs the backup glossary AJAX link
	 */
	public static function _cmtt_getBackupGlossary( $protect = true, $filepath = false ) {
		$is_backup_started = self::is_backup_started();

		if ( false === $filepath ) {
			$filepath = self::_get_backup_file_path( false );
		}

		if ( file_exists( $filepath ) && ! $is_backup_started ) {
			$url = admin_url( 'admin-ajax.php?action=cmtt_get_glossary_backup' );

			if ( ! $protect ) {
				$pinOption = \CM\CMTT_Settings::get( 'cmtt_glossary_backup_pinprotect' );
				$url       .= $pinOption ? '&pin=' . $pinOption : '';
			}

			return $url;
		}

		return false;
	}

	public static function is_backup_started() {
		$backup_lines_saved  = \CM\CMTT_Settings::get( 'cmtt_glossary_backup_lines_saved', 0 );
		$backup_lines_needed = \CM\CMTT_Settings::get( 'cmtt_glossary_backup_items', 0 );

		return $backup_lines_saved && $backup_lines_saved < $backup_lines_needed;
	}

	public static function get_backup_button_label() {
		$is_started = self::is_backup_started();

		return $is_started ? __( 'Backup to CSV (continue)', 'cm-tooltip-glossary' ) : __( 'Backup to CSV', 'cm-tooltip-glossary' );
	}

	public static function cmtt_glossary_handleexport() {
		if ( ! empty( $_POST['cmtt_doExportSettings'] ) ) {
			if ( ! wp_verify_nonce( $_POST['cmtt_nonce'], 'cmtt_export_settings' ) ) {
				wp_die( 'Invalid request' );
			}
			self::_cmtt_exportGlossarySettings();
		} elseif ( ! empty( $_POST['cmtt_doImportSettings'] ) && ! empty( $_FILES['importCSV'] ) && is_uploaded_file( $_FILES['importCSV']['tmp_name'] ) ) {

			if ( ! wp_verify_nonce( $_POST['cmtt_nonce'], 'cmtt_import_settings' ) ) {
				wp_die( 'Invalid request' );
			}
			self::_cmtt_importGlossarySettings( $_FILES['importCSV'] );
		}
	}

    /**
     * Exports the glossary
     */

    public static function cmtt_glossary_ajaxExport() {
        global $wpdb;

        if ( ! wp_verify_nonce( $_GET['cmtt_nonce'], 'cmtt_export' ) ) {
            wp_die( 'Invalid request' );
        }

        $export_chunk_size = $_GET['chunk_size'] ?? 1000;
        $export_page_num = $_GET['export_page'] ?? 1;
        $basedir = wp_upload_dir()['basedir'];
        $filename = $basedir . '/glossary_export.csv';

        $exported_persent = $export_page_num * $export_chunk_size * 100 / CMTT_Pro::countGlossaryTerms();

        if($export_page_num == 1){
            if(file_exists($filename)) {
                unlink($filename);
            }
        }

        $fp = fopen( $filename, 'a+b');
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        $export_columns_header = array(
            'Id',
            'Title',
            'Excerpt',
            'Description',
            'Synonyms',
            'Variations',
            'Categories',
            'Abbreviation',
            'Tags',
            'Image',
            'Languages',
        );
        $export_columns_header = apply_filters( 'cmtt_export_header_row', $export_columns_header );

        $data = static::exportGetRows($export_page_num, $export_chunk_size, $export_columns_header);
        $records = $data[0];
        $export_columns_header = $data[1];

        if(empty($records)){
            fputcsv($fp, array());
            fclose($fp);
            $site_url = site_url();
            if (strpos($filename, ABSPATH) === 0) {
                $relative_file_path = substr($filename, strlen(ABSPATH));
                $file_url = trailingslashit($site_url) . $relative_file_path;
            } else {
                $file_url = $filename;
            }
            wp_send_json(['progress_status' => 'finished','file_link'=>esc_url($file_url)]);
            return;
        }

        if ( $export_page_num == 1 ) {
            fputcsv($fp, $export_columns_header);
        }

        foreach ($records as $key => $record) {
            $record_keys = array_keys( $record );
            $diff      = array_diff( $export_columns_header, $record_keys );

            foreach ( $diff as $key => $value ) {
                if ( ! isset( $record[ $key ] ) && ! isset( $record[ $value ] ) ) {
                    $record[ $value ] = '';
                }
            }
            fputcsv( $fp, $record, ',', '"' );
            unset( $records[ $key ] );
        }
        wp_send_json(['progress_status' => 'in_progress', 'ready_on' => (int)$exported_persent]);
        fputcsv($fp, array());

    }

    public static function exportGetRows($page, $chunk_size, $columns_header)
    {
        global $wpdb;

        $args = array(
            'post_type' => 'glossary',
            'post_status' => 'publish',
            'orderby' => 'ID',
            'order' => 'ASC',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'fields' => 'ids',
            'posts_per_page' => $chunk_size,
            'offset' => ($page - 1) * $chunk_size,
        );

        $q = new WP_Query($args);
        $exportData = array();
        $postsArray = $q->get_posts();
        $q = null;

        $ignored_meta_keys = array(
            'cmtt_synonyms',
            CMTT_Related::TRANSIENT_NAME,
            'cmtt_variations',
            'cmtt_abbreviations'
        );

        foreach ($postsArray as $glossary_term) {

            $glossary_term = get_post($glossary_term);

            $exportDataRow = array(
                $glossary_term->ID,
                html_entity_decode($glossary_term->post_title, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($glossary_term->post_excerpt, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($glossary_term->post_content, ENT_QUOTES, 'UTF-8'),
                CMTT_Synonyms::getSynonyms($glossary_term->ID, true),
                CMTT_Synonyms::getSynonyms($glossary_term->ID, false),
                '',
                '',
                '',
                get_the_post_thumbnail_url($glossary_term->ID, 'full'),
                '',
            );

            $meta = get_post_meta($glossary_term->ID);
            foreach ($meta as $item => $value) {

                $is_own_meta = (strpos($item, 'cmtt')) !== false || (strpos($item, '_glossary_related_article')) !== false;
                $is_not_ignored = !in_array($item, $ignored_meta_keys);
                $is_unique = !in_array($item, $columns_header);

                if ( $is_own_meta && $is_not_ignored ) {
                    if ( $is_unique ) {
                        $columns_header[] = $item;
                    }

                    if ( $item === '_glossary_related_article' ) {
                        /*
                         * For the sake of export each custom related article is being exported
                         * as a separate column in the CSV file for better readibility
                         */
                        if ( is_array($value) ) {
                            $related_articles = unserialize($value[0]);
                            if ( is_array($related_articles) ) {
                                foreach ($related_articles as $related_article) {
                                    $related_article_metakey = $item . '_' . $related_article['name'];
                                    if ( !in_array($related_article_metakey, $columns_header) ) {
                                        $columns_header[] = $related_article_metakey;
                                    }
                                    $exportDataRow[$related_article_metakey] = $related_article['url'];
                                }
                            }
                        }
                    } else {
                        $exportDataRow[$item] = is_array($value) ? $value[0] : $value;
                    }
                }
            }

            $exportDataRowWithMeta = apply_filters('cmtt_export_data_row', $exportDataRow, $glossary_term);
            $exportData[] = $exportDataRowWithMeta;
        }

        return [$exportData,$columns_header];
    }

    public static function cmtt_glossary_ajaxImport() {
        if ( ! wp_verify_nonce( $_POST['cmtt_nonce'], 'cmtt_import' ) ) {
            wp_die( 'Invalid request' );
        }

        $import_chunk_size = intval($_POST['import_chunk_size'] ?? 100);
        $import_chunk_number = intval($_POST['import_chunk_num'] ?? 0 );
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['path'];
        $target_file = $target_dir . '/glossary_import_'.get_current_user_id().'.csv';

        if($import_chunk_number){
            $i = 0;
            $import_chunk_number--;
            $start =  $import_chunk_number * $import_chunk_size + 1;
            $end = $start + $import_chunk_size;
            if(file_exists($target_file)) {
                $fp      = fopen( $target_file, 'r' );
                $tab     = array();
                while ( ! feof( $fp ) && $i < $end) {
                    $item  = fgetcsv( $fp, 0, ',', '"' );
                    $title_set = isset($item[1]) && !empty(trim($item[1]));
                    $description_set = isset($item[3]) && !empty(trim($item[3]));
                    if ($title_set || $description_set) {
                        if($i == 0 || $i >= $start){
                            $tab[] = $item;
                        }
                    }
                    $i++;
                }
                $unlink = feof( $fp );
                fclose( $fp );
                if($unlink){
                    unlink($target_file);
                }
                $errors            = [];
                $allElements      = 0;
                $importedElements = 0;

                remove_action( 'save_post', array( 'CMTT_Related', 'triggerOnSave' ) );
                remove_action( 'save_post', array( 'CMTTW_Related', 'triggerOnSave' ) );

                $header_map      = array();
                $cmtt_meta_start = 10;

                foreach ( $tab[0] as $key => $header_item ) {
                    if ( $key < $cmtt_meta_start ) {
                        $header_map[ $key ] = $key;
                    } else {
                        $header_map[ $key ] = $header_item;
                    }
                }

                $total = count( $tab );
                for ( $i = 1; $i < $total; $i ++ ) {
                    if ( is_array( $tab[ $i ] ) ) {
                        $allElements ++;
                        $result = self::importGlossaryItem( $tab[ $i ], $header_map );
                        if ( $result > 0 ) {
                            $importedElements ++;
                        } else {
                            $errors[] = abs( $result );
                        }
                        unset( $tab[ $i ] );
                    }
                }
                if(!empty($errors)){
                    foreach( $errors as $error) {
                        switch ($error) {
                            case '1':
                                $error_msg[] = 'Empty title column. Title column (second) cannot be empty!';
                                break;
                            case '3':
                                $error_msg[] = 'Empty description column. Description column (4th) cannot be empty!';
                                break;
                            case '5':
                                $error_msg[] = 'Too few columns! Minimal row: "","Title","","Description"';
                                break;
                            case '6':
                                $error_msg[] = 'First column have to be an ID, text given';
                                break;
                            default:
                                $error_msg[] = 'Error during import.';
                                break;
                        }
                    }
                }
                wp_send_json(['message' =>"CSV file uploaded and processed in $import_chunk_number chunks.", 'imported' => $importedElements, 'errors' => $error_msg ?? []]);
            }
        }

        if(isset($_FILES['importCSV']) && !$import_chunk_number){
            if(empty($_FILES['importCSV']['tmp_name'])){
                wp_send_json_error("No file uploaded.");
            }

            if(file_exists($target_file)){
                unlink($target_file);
            }
            if ( !empty($_FILES['importCSV']['tmp_name']) && move_uploaded_file($_FILES['importCSV']['tmp_name'], $target_file)){
                $line_count = self::count_lines_in_file($target_file);
                wp_send_json_success(['message' =>"File uploaded, start importing!", 'total' => $line_count - 1]);
            } else {
                wp_send_json_error(['message' =>"Error uploading CSV file!"]);
            }
        }

    }

	/**
	 * Imports the single glossary item
	 *
	 * @param type $item
	 * @param type $override
	 *
	 * @return boolean
	 * @global type $wpdb
	 */
	public static function importGlossaryItem( $item, $header_map, $override = true ) {
		if ( ! empty( $item ) && is_array( $item ) ) {
			/*
			 * Too few columns
			 */
			if ( count( $item ) < 4 ) {
				return - 5;
			}
			/*
			 * If first column is not empty it have to be a number
			 */
			if ( ! empty( $item[0] ) && intval( $item[0] ) == 0 ) {
				return - 6;
			}
			/*
			 * Empty title
			 */
			if ( empty( $item[1] ) ) {
				return - 1;
			}
			/*
			 * Empty description
			 */
			if ( empty( $item[3] ) ) {
				return - 3;
			}

			global $wpdb;
			$data = array(
				'post_title'   => $item[1],
				'post_type'    => 'glossary',
				'post_excerpt' => $item[2],
				'post_content' => $item[3],
				'post_status'  => 'publish',
			);

			if ( \CM\CMTT_Settings::get( 'cmtt_importSameTitle', 0 ) ) {
				$sql           = $wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type=%s AND post_title=%s AND post_status='publish'", 'glossary', $item[1] );
				$existingPosts = $wpdb->get_results( $sql );
			}

			$existingId = 0;
			if ( ! empty( $existingPosts ) && is_array( $existingPosts ) ) {
				foreach ( $existingPosts as $glossaryPost ) {
					if ( $glossaryPost->post_title == $item[1] ) {
						$existingId = $glossaryPost->ID;
						break;
					}
				}
			}

            /*
             * @since 4.3.2 Option to skip the duplicate items
             */
            if ( \CM\CMTT_Settings::get( 'cmtt_skipSameTitle', 0 ) && ! empty( $existingId ) ) {
                $item = null;
                return 999;
            }

			if ( ! empty( $existingId ) ) {
				// update
				$data['ID'] = $existingId;

				// cmtt processing
				$sql                         = $wpdb->prepare( "SELECT meta_key FROM {$wpdb->postmeta} WHERE post_id=%s AND meta_key LIKE '%cmtt%'", $existingId );
				$post_meta_keys_             = $wpdb->get_results( $sql, ARRAY_N );
				$post_meta_keys              = array();
				$new_custom_related_articles = array();
				$_glossary_related_article   = get_post_meta( $existingId, '_glossary_related_article', true );

				foreach ( $post_meta_keys_ as $value ) {
					$post_meta_keys[] = $value[0];
				}
				unset( $post_meta_keys_ );

				$cmtt_data        = array();
				$cmtt_data_insert = array();
				foreach ( $header_map as $key => $value ) {
					/*
					 * Allows the meta fields to include cmtt or glossary (@since 4.1.5)
					 */
					if ( false !== strpos( $value, 'cmtt' ) || false !== strpos( $value, 'glossary' ) ) {
						if ( trim( $item[ $key ] ) == '' || is_array( $item[ $key ] ) ) {
							continue;
						} else {
							// if field exists
							if ( in_array( $value, $post_meta_keys ) ) {
								$cmtt_data[ $value ] = $item[ $key ];
							} else {
								$cmtt_data_insert[ $value ] = $item[ $key ];
							}
						}
					}
					// If it's a custom related post field and it's not empty
					if ( strpos( $value, '_glossary_related_article' ) !== false && ! empty( $item[ $key ] ) ) {
						if ( is_array( $_glossary_related_article ) && count( $_glossary_related_article ) > 0 ) {
							foreach ( $_glossary_related_article as &$glossary_related_article_item ) {
								if ( isset( $glossary_related_article_item['name'] ) && $value == '_glossary_related_article_' . $glossary_related_article_item['name'] ) {
									$glossary_related_article_item['url'] = $item[ $key ];
								}
							}
						} else {
							$article_name                  = str_replace( '_glossary_related_article_', '', $value );
							$new_custom_related_articles[] = array(
								'name' => $article_name,
								'url'  => $item[ $key ],
							);
						}
					}
				}

				if ( $override ) {
					// Insert cmtt data
					if ( ! empty( $cmtt_data_insert ) ) {
						foreach ( $cmtt_data_insert as $meta_key => $meta_value ) {
							$wpdb->insert(
								$wpdb->postmeta,
								array(
									'meta_value' => $meta_value,
									'post_id'    => $existingId,
									'meta_key'   => $meta_key,
								)
							);
						}
					}
					// Update cmtt data
					if ( ! empty( $cmtt_data ) ) {
						try {
							foreach ( $cmtt_data as $meta_key => $meta_value ) {
								update_post_meta( $existingId, $meta_key, $meta_value );
							}
						} catch ( Exception $e ) {
							error_log( "\n " . 'Exception:  ' . print_r( $e->getMessage(), true ), 3, 'error.log' );
						}
					}

					// Update cmtt _glossary_related_article
					if ( is_array( $_glossary_related_article ) && ( ! empty( $_glossary_related_article ) || ! empty( $new_custom_related_articles ) ) ) {
						$_glossary_related_article = array_merge( $_glossary_related_article, $new_custom_related_articles );
						try {
							update_post_meta( $existingId, '_glossary_related_article', serialize( $_glossary_related_article ) );
						} catch ( Exception $e ) {
							error_log( "\n " . 'Exception:  ' . print_r( $e->getMessage(), true ), 3, 'error.log' );
						}
					}

					$update = wp_update_post( $data );
				} else {
					$update = false;
				}
			} else {
				// Insert new glossary item
				$update = wp_insert_post( $data, true );

				if ( $update > 0 ) {
					foreach ( $header_map as $key => $value ) {
						/*
						 * Allows the meta fields to include cmtt or glossary (@since 4.1.5)
						 */
						if ( false !== strpos( $value, 'cmtt' ) || false !== strpos( $value, 'glossary' ) ) {
							$cmtt_data_insert[ $value ] = $item[ $key ];
						}
						if ( strpos( $value, '_glossary_related_article' ) !== false && ! empty( $item[ $key ] ) ) {
							$custom_related_article         = array();
							$custom_related_article['name'] = str_replace( '_glossary_related_article_', '', $value );
							$custom_related_article['url']  = $item[ $key ];
							$custom_related_articles[]      = $custom_related_article;
						}
					}
					if ( ! empty( $custom_related_articles ) ) {
						$cmtt_data_insert['_glossary_related_article'] = serialize( $custom_related_articles );
					}

					if ( ! empty( $cmtt_data_insert ) ) {
						foreach ( $cmtt_data_insert as $meta_key => $meta_value ) {
							try {
								$wpdb->insert(
									$wpdb->postmeta,
									array(
										'meta_value' => $meta_value,
										'post_id'    => $update,
										'meta_key'   => $meta_key,
									)
								);
							} catch ( Exception $e ) {
								error_log( "\n " . 'Exception:  ' . print_r( $e->getMessage(), true ), 3, 'error.log' );
							}
						}
					}
				}
			}

			if ( $update > 0 && isset( $item[4] ) && isset( $item[5] ) ) {
				CMTT_Synonyms::setSynonyms( $update, $item[4], true );
				CMTT_Synonyms::setSynonyms( $update, $item[5], false );
			}

			/*
			 * Image
			 */
			$image_column = 9;
			if ( $update > 0 && ! empty( $item[ $image_column ] ) ) {
				self::uploadImage( $item[ $image_column ], $update, '' );
			}

			do_action( 'cmtt_import_glossary_item', $item, $update );

			/*
			 * Return with no error
			 */
			/*
			 * Release memory
			 */
			$item = null;

			return $update;
		}

		return - 4;
	}

	public static function uploadImage( $url, $parent_post, $img_title ) {
		$image_data = file_get_contents( $url );
		$upload_dir = wp_upload_dir();
		$filename   = basename( $url );
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = trailingslashit( $upload_dir['path'] ) . $filename;
		} else {
			$file = trailingslashit( $upload_dir['basedir'] ) . $filename;
		}
		file_put_contents( $file, $image_data );
		$guid = trailingslashit( $upload_dir['path'] ) . $filename;

		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );
		$atache_path = ltrim( trailingslashit( $upload_dir['subdir'] ) . $filename, DIRECTORY_SEPARATOR );
		$attachment  = array(
			'guid'           => trailingslashit( $upload_dir['url'] ) . $filename,
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => $filename,
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		require_once ABSPATH . 'wp-admin/includes/image.php';
		// Generate the metadata for the attachment, and update the database record.
		$attach_id   = wp_insert_attachment( $attachment, $atache_path, $parent_post );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $guid );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		set_post_thumbnail( $parent_post, $attach_id );

		return $attach_id;
	}

	/**
	 * Exports the glossary settings
	 */
	public static function _cmtt_exportGlossarySettings() {
		$exportData = self::_cmtt_prepareExportGlossarySettings();

		$outstream = fopen( 'php://temp', 'r+' );

		foreach ( $exportData as $line ) {
			fputcsv( $outstream, $line, ',', '"' );
		}
		rewind( $outstream );

		header( 'Content-Encoding: UTF-8' );
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename=glossary_export_settings_' . date( 'Ymd_His', current_time( 'timestamp' ) ) . '.csv' );
		/*
		 * Why including the BOM? - Marcin
		 */
		echo "\xEF\xBB\xBF"; // UTF-8 BOM
		while ( ! feof( $outstream ) ) {
			echo fgets( $outstream );
		}
		fclose( $outstream );
		exit;
	}

	public static function _cmtt_prepareExportGlossarySettings() {
		global $wpdb;
		$export_data[] = array( 'Title', 'Value' );

		$optionNames = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'cmtt%'" );

		$options_names = array();
		foreach ( (array) $optionNames as $o ) {
			$options_names[ $o->option_name ] = $o->option_value;
		}

		foreach ( $options_names as $k => $v ) {
			$export_data[] = array( $k, $v );
		}

		return $export_data;
	}

	public static function _cmtt_importGlossarySettings( $file ) {
		$filesrc = $file['tmp_name'];

		$fp  = fopen( $filesrc, 'r' );
		$tab = array();
		while ( ! feof( $fp ) ) {
			$item  = fgetcsv( $fp, 0, ',', '"' );
			$tab[] = $item;
		}
		$error            = '';
		$allElements      = 0;
		$importedElements = 0;

		for ( $i = 1; $i < count( $tab ); ++ $i ) {
			if ( is_array( $tab[ $i ] ) ) {
				$allElements ++;
				$result = self::importGlossaryOption( $tab[ $i ] );
				if ( $result > 0 ) {
					$importedElements ++;
				} else {
					$error = abs( $result );
				}
			}
		}
		$glossaryPostID = \CM\CMTT_Settings::get( 'cmtt_glossaryID' );

		if ( $glossaryPostID > 0 ) {
			/*
			 * Update glossary post permalink
			 */
			$glossaryPost = array(
				'ID'        => $glossaryPostID,
				'post_name' => \CM\CMTT_Settings::get( 'cmtt_glossaryPermalink' ),
			);
			wp_update_post( $glossaryPost );
			\CM\CMTT_Settings::_flush_rewrite_rules();
		}

		$queryArgs = array(
			'settingsMsg'      => 'imported',
			'settingstotal'    => $allElements,
			'settingsImported' => $importedElements,
			'settings_error'   => $error,
		);
		wp_safe_redirect( add_query_arg( $queryArgs, $_SERVER['REQUEST_URI'] ), 303 );
		exit;
	}

	/**
	 * Imports the single glossary option
	 *
	 * @param array $option
	 *
	 * @return boolean
	 */
	public static function importGlossaryOption( $option ) {
		if ( ! empty( $option ) && is_array( $option ) && count( $option ) == 2 ) {
			if ( 'cmtt_options' === $option[0] ) {
				return \CM\CMTT_Settings::setAll( maybe_unserialize( $option[1] ) );
			} else {
				return true;
			}
			// return \CM\CMTT_Settings::set($option[0], maybe_unserialize($option[1]));
		}

		return - 1;
	}


	/**
	 * Outputs the backup file
	 */
	public static function cmtt_glossary_get_backup() {
		$pinOption = \CM\CMTT_Settings::get( 'cmtt_glossary_backup_pinprotect', false );

		if ( ! empty( $pinOption ) ) {
			$passedPin = filter_input( INPUT_GET, 'pin' );
			if ( $passedPin != $pinOption ) {
				echo 'Incorrect PIN!';
				die();
			}
		}

		$filepath = self::get_backup_filename( false );

		if ( $filepath ) {

			$outstream = fopen( $filepath, 'r' );
			if ( ! $outstream ) {
				echo 'Error while reading the backup file.';
				die();
			}
			$path     = pathinfo( $filepath );
			$filename = $path['basename'];
			rewind( $outstream );

			header( 'Content-Encoding: UTF-8' );
			header( 'Content-Type: text/csv; charset=UTF-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			/*
			 * Why including the BOM? - Marcin
			 */
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
			while ( ! feof( $outstream ) ) {
				echo fgets( $outstream );
			}
			fclose( $outstream );
		} else {
			echo 'No backup file found.';
		}
		die();
	}

	/**
	 * Reschedule the backup event
	 *
	 * @return type
	 */
	public static function _cmtt_rescheduleBackup() {
		$possibleIntervals = array_keys( wp_get_schedules() );

		$newScheduleHour     = filter_input( INPUT_POST, 'cmtt_glossary_backupCronHour' );
		$newScheduleInterval = filter_input( INPUT_POST, 'cmtt_glossary_backupCronInterval' );

		if ( $newScheduleHour !== null && $newScheduleInterval !== null ) {
			wp_clear_scheduled_hook( 'cmtt_glossary_backup_event' );

			if ( $newScheduleInterval == 'none' ) {
				return;
			}

			if ( ! in_array( $newScheduleInterval, $possibleIntervals ) ) {
				$newScheduleInterval = 'daily';
			}

			$time = strtotime( $newScheduleHour );
			if ( $time === false ) {
				$time = current_time( 'timestamp' );
			}

			wp_schedule_event( $time, $newScheduleInterval, 'cmtt_glossary_backup_event' );
		}
	}

    public static function count_lines_in_file($file_path) {
        $line_count = 0;
        $handle = fopen($file_path, "r");

        // Check if the file handle is valid
        if ($handle) {
            // Loop through each line in the file
            while (!feof($handle)) {
                // Increment the line count
                $line = fgetcsv($handle);
                $title_set = isset($line[1]) && !empty(trim($line[1]));
                $description_set = isset($line[3]) && !empty(trim($line[3]));
                if ($title_set || $description_set) {
                    $line_count++;
                }
            }
            fclose($handle);
        } else {
            // Handle error if the file cannot be opened
            echo "Error: Unable to open file.";
        }

        return $line_count;
    }
}