<?php

class CMTT_Related {

	const TABLENAME = 'glossary_related';
	const SHORTCODE_RELATED = 'glossary_related';
	const TRANSIENT_NAME = '_cmtt_related_terms_for_post_';

	protected static $filePath = '';
	protected static $jsPath = '';
	public static $tableExists = false;

	public static function init() {
		self::$filePath = plugin_dir_url(__FILE__);
		self::$jsPath = self::$filePath . 'assets/js/';

		add_action('save_post', array(__CLASS__, 'triggerOnSave'), 11);
		add_action('cmtt_do_cleanup', array(__CLASS__, 'doCleanup'));
		add_action('cmtt_do_activate', array(__CLASS__, 'install'));
		add_filter('cron_schedules', array(__CLASS__, 'cronAddIntervals'));
		add_filter('strip_shortcodes_tagnames', array(__CLASS__, 'excludeRelatedTermsShortcode'), 20, 2);
		add_filter('cmtt_add_backlink_content', array(__CLASS__, 'maybeAddRelatedArticles'), 10 + (int)\CM\CMTT_Settings::get( 'cmtt_glossary_RelatedArticles_order', 1 ), 2);
		add_action('admin_init', array(__CLASS__, 'reschedule'));
		add_action('cmtt_after_parsed_content', array(__CLASS__, 'triggerOnView'), 10, 2);

		/*
		 * AJAX
		 */
		add_action('wp_ajax_related_articles_pagination', array(__CLASS__, 'ajaxRelatedArticlesPagination'));
		add_action('wp_ajax_nopriv_related_articles_pagination', array(__CLASS__, 'ajaxRelatedArticlesPagination'));
		add_action('wp_ajax_related_articles', array(__CLASS__, 'ajaxRelatedArticles'));
		add_action('wp_ajax_nopriv_related_articles', array(__CLASS__, 'ajaxRelatedArticles'));

		/*
		 * Shortcode
		 */
		add_shortcode(CMTT_Related::SHORTCODE_RELATED, array(__CLASS__, 'show_related'));
	}

	public static function show_related($atts = array()) {
		global $post;

		$atts = shortcode_atts(array(
			'terms'    => 1,
			'articles' => 1
		), $atts, self::SHORTCODE_RELATED);

		if (!isset($post)) {
			return '';
		}
		$id = $post->ID;

		/*
		 * Load scripts conditionally
		 */
		add_action('wp_enqueue_scripts', array(__CLASS__, 'addScripts'));
		add_action('wp_footer', array(__CLASS__, 'addScripts'));

		$content = '<div class="cmtt-related-shortcode-wrapper" postid="' . $id . '"'
		           . ' showterms="' . $atts['terms'] . '" showarticles="' . $atts['articles'] . '"></div>';
		return $content;
	}

	public static function install() {
		global $wpdb;
		$sql = "CREATE TABLE {$wpdb->prefix}" . self::TABLENAME . " (
            glossaryId INTEGER UNSIGNED NOT NULL,
            articleId VARCHAR(145) NOT NULL,
            PRIMARY KEY  (articleId,glossaryId),
            KEY glossaryId (glossaryId)
          );";

		include_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		wp_schedule_event(current_time('timestamp'), 'daily', 'glossary_daily_event');
	}

	public static function checkIfTableExists() {
		global $wpdb;

		if (!empty(self::$tableExists)) {
			return self::$tableExists;
		}

		if (!$wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}" . self::TABLENAME . "'") == $wpdb->prefix . self::TABLENAME) {
			self::install();
		}
		self::$tableExists = true;
		return self::$tableExists;
	}

	public static function doCleanup() {
		self::flushDb();
	}

	public static function flushDb() {
		global $wpdb;
		$wpdb->query('DELETE FROM ' . $wpdb->prefix . self::TABLENAME);
	}

	public static function cronAddIntervals($schedules) {
		// add a 'weekly' interval
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __('Once Weekly', 'cm-tooltip-glossary')
		);
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display'  => __('Once Monthly', 'cm-tooltip-glossary')
		);
		return $schedules;
	}

	public static function excludeRelatedTermsShortcode($tags_to_remove, $content) {
		foreach ($tags_to_remove as $k => $tag) {
			if ($tag == 'glossary_related')
				unset($tags_to_remove[$k]);
		}

		return $tags_to_remove;
	}

	public static function reschedule() {
		$possibleIntervals = array_keys(wp_get_schedules());

		$newScheduleHour = filter_input(INPUT_POST, 'cmtt_glossary_relatedCronHour');
		$newScheduleInterval = filter_input(INPUT_POST, 'cmtt_glossary_relatedCronInterval');

		if ($newScheduleHour !== NULL && $newScheduleInterval !== NULL) {
			wp_clear_scheduled_hook('glossary_daily_event');

			if ($newScheduleInterval == 'none') {
				return;
			}

			if (!in_array($newScheduleInterval, $possibleIntervals)) {
				$newScheduleInterval = 'daily';
			}

			$time = strtotime($newScheduleHour);
			if ($time === FALSE) {
				$time = current_time('timestamp');
			}

			wp_schedule_event($time, $newScheduleInterval, 'glossary_daily_event');
		}
	}

	public static function updateArticleTerms($id, $content, $runParser = true) {
		global $templatesArr, $wpdb;

		$post = get_post($id);
		$postTypes = \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesPostTypesArr', array());
		if (is_array($postTypes) && !in_array($post->post_type, $postTypes)) {
			return 0;
		}

		$excludeFromRelated = get_post_meta($id, '_glossary_not_include_in_related_articles', true);
		if ($excludeFromRelated) {
			/*
			 * Remove the post/page from all related articles lists
			 */
			$wpdb->query("DELETE FROM " . $wpdb->prefix . self::TABLENAME . " WHERE articleId=" . $id);
		} else {

			if ($runParser) {
				$templatesArr = array();
				CMTT_Free::cmtt_glossary_parse($content, true);
			}

			if (!empty($templatesArr)) {
				$table_name = $wpdb->prefix . self::TABLENAME;
				$glossaryIds = array_keys($templatesArr);

				foreach ($glossaryIds as $glossaryId) {
					$record_exists = false;
					if (\CM\CMTT_Settings::get('cmtt_additional_check_related_articles', 1)) {
						$record = $wpdb->get_row(
							"SELECT * FROM $table_name WHERE articleId=" . $id . " AND glossaryId=" . $glossaryId,
							ARRAY_A
						);
						$record_exists = (!empty($record) && count($record) > 0 ) ? true : false;
					}

					if ($glossaryId != $id && !$record_exists) {
						$result = $wpdb->insert($wpdb->prefix . self::TABLENAME, array('articleId' => $id, 'glossaryId' => $glossaryId), array('%d', '%d'));
						if (!$result) {
							/*
							 * Indicate the problem with inserting the terms
							 */
							\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesIndexingInsertError', '1');
						}
					}
				}

				/*
				 * Add to the indexed Ids list if the indexing is in progress
				 */
				self::addToIndexedIds($id);
			}
		}

		return 1;
	}

	public static function getRemainingArticlesCount($justCount = false) {
		$indexedIds = \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesIndexedIds', array());
		if (!is_array($indexedIds)) {
			$indexedIds = array();
		}
		$allArticles = (int) \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesCrawlItems', 0);

		/*
		 * Count the remaining items
		 */
		$remainingItemsToCrawl = max(0, $allArticles - count($indexedIds));
		$result = sprintf(__('Remaining articles to crawl: %d/%d', 'cm-tooltip-glossary'), $remainingItemsToCrawl, $allArticles);
		if ($justCount) {
			return $remainingItemsToCrawl;
		}
		return $result;
	}

	public static function getParsingProblems() {
		$result = '';
		if (\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesIndexingStarted')) {
			$result .= 'Error: Chunk processing timed-out. Please lower the chunk size and retry.';
		}
		if (\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesIndexingInsertError')) {
			$result .= 'Error: There was an error during saving the related term in the database.';
		}
		return $result;
	}

	public static function showContinueButton() {
		$remainingItemsToCrawl = self::getRemainingArticlesCount(true);
		return $remainingItemsToCrawl > 0;
	}

	public static function crawlArticles($restart = false) {
		global $wpdb, $post;

		if (function_exists('wp_suspend_cache_addition')) {
			wp_suspend_cache_addition(true);
		}

		$lastSave = microtime(true);

		/*
		 * Clear error
		 */
		\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesIndexingInsertError', '0');
		\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesIndexingStarted', '1');

		if ($restart) {
			\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesIndexedIds', array());
			self::doCleanup();
		}

		$chunkSize = (int) \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesCrawlChunkSize', 500);
		$indexedIds = \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesIndexedIds', array());

		/*
		 * Types of the posts to crawl
		 */
		$types = \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesPostTypesArr', array());
		if (!is_array($types)) {
			$types = array();
			$allArticleIds = array();
		} else {
			if (!is_array($indexedIds)) {
				$indexedIds = array();
			}

			if ($chunkSize <= 0 || $chunkSize > 1000) {
				$chunkSize = 1000;
			}

			self::checkIfTableExists();

			$allArticlesArgs = array(
				'post_type'              => $types,
				'post_status'            => 'publish',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'suppress_filters'       => false,
				'nopaging'               => true,
				'numberposts'            => -1,
				'fields'                 => 'ids',
				'orderby'                => 'modified',
				'order'                  => 'ASC'
			);
			$q = new WP_Query($allArticlesArgs);
			$allArticleIds = $q->get_posts();
		}

		if ($restart) {
			/*
			 * Count the articles to crawl
			 */
			$articleCount = count($allArticleIds);
			/*
			 * Remove indexed articles
			 */
			$wpdb->query("TRUNCATE " . $wpdb->prefix . self::TABLENAME);
			/*
			 * Update the items to crawl
			 */
			\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesCrawlItems', $articleCount);
		}

		/*
		 * Get the $currentChunk for parsing
		 */
		$remainingArticles = array_diff($allArticleIds, $indexedIds);
		$currentChunk = array_slice($remainingArticles, 0, $chunkSize);
		$currentChunkIndexed = array();

		if (!empty($allArticleIds) && !empty($currentChunk)) {
			/*
			 * IDEA: Count all posts, divide into chunks, parse one chunk at a time.
			 *
			 * IEAD2: If start - count posts and parse first chunk, save the IDS
			 * If continue - count posts and diff with the saved IDS, parse the first chunk of the rest
			 */
			$articlesChunkArgs = array(
				'post__in'               => $currentChunk,
				'post_type'              => $types,
				'post_status'            => 'publish',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'suppress_filters'       => false,
				'nopaging'               => true,
				'orderby'                => 'modified',
				'order'                  => 'ASC'
			);
			$q = new WP_Query($articlesChunkArgs);
			$articles = $q->get_posts();

			foreach ($articles as $article) {
				$post = $article;
				self::updateArticleTerms($article->ID, $article->post_content);
				$currentChunkIndexed[] = $article->ID;

				/*
				 * Save every 30s
				 */
				if (abs(microtime(true) - $lastSave) > 30) {
					/*
					 * Update the indexed ids array
					 */
					$indexedIdsUpdated = array_merge($indexedIds, $currentChunkIndexed);
					/*
					 * Update the chunk
					 */
					\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesIndexedIds', $indexedIdsUpdated);
					$lastSave = microtime(true);
				}
			}
		}

		if (function_exists('wp_suspend_cache_addition')) {
			wp_suspend_cache_addition(true);
		}

		/*
		 * Update the indexed ids array
		 */
		$indexedIdsUpdated = array_merge($indexedIds, $currentChunkIndexed);

		/*
		 * Indicate the finish
		 */
		\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesIndexingStarted', '0');

		/*
		 * Update the chunk
		 */
		\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesIndexedIds', $indexedIdsUpdated);
	}

	/**
	 * Allows to add single ID to the indexed array
	 * @param type $currentChunkIndexed
	 * @return 0 if not running 1 if successfull
	 */
	public static function addToIndexedIds($currentChunkIndexed) {
		$idsToCrawl = self::getRemainingArticlesCount(true);
		if (!$idsToCrawl) {
			return 0;
		}
		$indexedIds = \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesIndexedIds', array());
		if (!is_array($currentChunkIndexed)) {
			if (is_numeric($currentChunkIndexed)) {
				$currentChunkIndexed = [$currentChunkIndexed];
			} else {
				$currentChunkIndexed = array();
			}
		}
		$indexedIdsUpdated = array_unique(array_merge($indexedIds, $currentChunkIndexed));
		\CM\CMTT_Settings::set('cmtt_glossary_relatedArticlesIndexedIds', $indexedIdsUpdated);
		return 1;
	}

	/**
	 * Run after the content was already parsed (page is viewed)
	 * @param type $post_id
	 */
	public static function triggerOnView($post_id, $content) {
		global $replacedTerms;

		self::checkIfTableExists();
		$triggerOnView = \CM\CMTT_Settings::get('cmtt_glossary_relatedFillAfterParsing', false);
        $post = get_post($post_id);
        if ($triggerOnView && $post->post_status == 'publish') {
			self::updateArticleTerms($post_id, $content, false);
		}

		/*
		 * Save the replaced terms in meta, so it can be used by [glossary_related] shortcode
		 * only if Pro+/Ecommerce, as it's not used in Pro, and causes a big performance impact
		 */

		if (class_exists('CMTT_Glossary_Plus') && !\CM\CMTT_Settings::get('cmtt_disableRelatedTermsUpdate',0) && is_array($replacedTerms)) {
            $meta_value = [];
		    foreach($replacedTerms as $key => $value){
                $meta_value[$key] = ['postID' => $value['postID']];
            }
			$result = update_post_meta($post_id, self::TRANSIENT_NAME, $meta_value);
		}
	}

	public static function triggerOnSave($post_id) {
		self::checkIfTableExists();
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		$post = get_post($post_id);
		if ($post->post_status == 'publish') {
			self::updateArticleTerms($post_id, $post->post_content);
		} else {
			global $wpdb;
			/*
			 * Clear the related terms
			 */
			$wpdb->query("DELETE FROM " . $wpdb->prefix . self::TABLENAME . " WHERE articleId=" . $post_id);
		}
	}

	public static function getRelatedArticles($glossaryId, $limit = 5, $type = 'all', $offset = 0) {
		global $wpdb;
		$where = '';

		if ($type == 'glossary') {
			$where = 'WHERE p.post_type=\'glossary\'';
		} elseif ($type == 'others') {
			$where = 'WHERE p.post_type<>\'glossary\'';
		}
		$order = \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesOrder', 'menu_order');
		$sql = $wpdb->prepare("SELECT p.ID, p.post_title, p.post_excerpt, p.post_type 
                               FROM {$wpdb->prefix}" . self::TABLENAME . " g 
                               JOIN {$wpdb->posts} p ON g.articleId=p.ID 
                                AND g.glossaryId=%d " . $where . " 
                               ORDER BY " . $order . " 
                               LIMIT %d, %d", $glossaryId, $offset, $limit);
		$results = $wpdb->get_results($sql);

		foreach ($results as &$result) {
			$result->url = get_permalink($result->ID);
		}
		return $results;
	}

	public static function getCustomRelatedArticles($glossaryId, $limit = 0, $offset = 0) {
		$results = array();
		$glossary_cra = get_post_meta($glossaryId, '_glossary_related_article', true);
		if (!empty($glossary_cra) && is_array($glossary_cra)) {
			foreach ($glossary_cra as $gc) {
				if (empty($gc) || !is_array($gc) || empty($gc['name']) || empty($gc['url'])) {
					continue;
				}
				$current_row = new stdClass;
				$current_row->ID = 1;
				$current_row->post_title = $gc['name'];
				$current_row->post_type = 'custom_related_article';
				$current_row->url = $gc['url'];
				$results[] = $current_row;
			}
		}

		if ($offset == count($results))
			return array();
		if ($limit > 0) {
			$results = array_slice($results, $offset, $limit);
		}

		return $results;
	}

	public static function renderRelatedArticles($glossaryId, $limitArticles = 5, $limitGlossary = 5, $force = false, $showPagination = false, $limitCustomArticles = 2) {
		$html = '';
		$basicArticlesType = 'all';

		$disableRelatedArticlesForThisTerm = (bool) get_post_meta($glossaryId, '_cmtt_disable_related_articles_for_term', true);
		$showRelatedArticles = (bool) \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticles');
		$showRelatedCustomArticles = (bool) \CM\CMTT_Settings::get('cmtt_glossary_showCustomRelatedArticles');

		/*
		 * If terms are disabled for this item specifically, or neither the custom related terms nor automated ones are enabled
		 */
		if (!$force && ($disableRelatedArticlesForThisTerm || (!$showRelatedArticles && !$showRelatedCustomArticles))) {
			return '';
		}

		$basic_articles = array();
		$glossaryArticles = array();
		$custom_related_articles = array();
		$all_related_articles = array();
		$all_custom_related_articles = array();

		if ($showRelatedArticles) {
			/*
			 * Note: The option name is wrong, but the variable and labels are right
			 */
			$showRelatedArticlesAndGlossarySeparately = \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesMerged');

			if ($showRelatedArticlesAndGlossarySeparately == 1) {
				$basicArticlesType = 'others';
				$glossaryArticles = self::getRelatedArticles($glossaryId, $limitGlossary, 'glossary');
			}

			$limit = !$showPagination ? $limitArticles : \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesLimit', 100);
			$all_related_articles = self::getRelatedArticles($glossaryId, $limit, $basicArticlesType);
			$basic_articles = !$showPagination ? $all_related_articles : array_slice($all_related_articles, 0, $limitArticles);
		}

		if ($showRelatedCustomArticles) {
			$limitCustom = !$showPagination ? $limitCustomArticles : 0;
			$all_custom_related_articles = self::getCustomRelatedArticles($glossaryId, $limitCustom, 0);
			$custom_related_articles = !$showPagination ? $all_custom_related_articles : array_slice($all_custom_related_articles, 0, $limitCustomArticles);
		}

		// Retrieve custom related articles and merge them in from of auto-generated ones
		$articles = array_merge($custom_related_articles, $basic_articles);

		if ($showPagination && \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesMerged') != 1) {
			$all_related_articles_count = count($all_related_articles);
			$all_custom_related_articles_count = count($all_custom_related_articles);
			$total_related_pages = $limitArticles > 0 ? ceil($all_related_articles_count / $limitArticles) : 0;
			$total_custom_related_pages = $limitCustomArticles > 0 ? ceil($all_custom_related_articles_count / $limitCustomArticles) : 0;
			$total_pages = ($total_related_pages > $total_custom_related_pages) ? $total_related_pages : $total_custom_related_pages;
		}

		/*
		 * Changed from 'h4' to 'div' to comply with accessibility standards
		 */
		$tag = 'div';
		if (count($articles) > 0) {
			$html .= '<div id="cmtt_related_articles" class="cmtt_related_articles_wrapper">'; // div.cmtt_related_articles_wrapper
			$html .= '<' . $tag . ' class="cmtt_related_title cmtt_related_articles_title">' . __(\CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesTitle'), 'cm-tooltip-glossary') . ' </' . $tag . '>';
			$html .= '<ul class="cmtt_related">';
			foreach ($articles as $article) {
				/*
				 * Added filter the_title in 4.0.13
				 */
				$title = apply_filters('the_title', $article->post_title, $article->ID);
				if (\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefix') && $article->post_type == 'glossary') {
					$title = \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefix') . ' ' . $title;
                } elseif(\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefixNotGlossary') && $article->post_type !== 'glossary'){
                    $title = \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefixNotGlossary') . ' ' . $title;
                }
				$target = ($article->post_type == 'custom_related_article') ? (\CM\CMTT_Settings::get('cmtt_glossary_customRelatedArticlesNewTab', '1') ? 'target="_blank"' : '') : (\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesNewTab', '1') ? 'target="_blank"' : '');
				$html .= '<li class="cmtt_related_item">';
				$html .= '<a href="' . $article->url . '"' . $target . '>' . $title . '</a>';
				if (\CM\CMTT_Settings::get('cmtt_glossary_relatedShowExcerpt') && !empty($article->post_excerpt)) {
					$html .= '<div>' . strip_shortcodes($article->post_excerpt) . '</div>';
				}
				$html .= '</li>';
			}
			$html .= '</ul>';
			if ($showPagination && ($total_related_pages > 1 || $total_custom_related_pages > 0) && \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesMerged') != 1) {
				$html .= '<div id="cmtt-pagination-container"'
				         . ' data-total-pages="' . $total_pages . '"'
				         . ' data-relart-per-page="' . $limitArticles . '"'
				         . ' data-custom-per-page="' . $limitCustomArticles . '"'
				         . ' data-glossary-id="' . $glossaryId . '"'
				         . '>'; // div#cmtt-pagination-container starts

				$html .= '<ul class="pageNumbers"><li class="prev disabled numeric" data-page-number="1"'
				         . '><<</li>';

				for ($i = 1; $i <= $total_pages; ++$i) {
					$html .= '<li class="numeric' . ($i != 1 ? '' : ' disabled') . '" data-page-number="' . $i
					         . '">'
					         . $i . '</li>';
				}

				$html .= '<li class="next numeric" data-page-number="' . $total_pages . '" '
				         . '>>></li></ul>';
				$html .= '</div>'; // div#cmtt-pagination-container ends
			}
			$html .= '</div>'; // div.cmtt_related_articles_wrapper ends

			/*
			 * Load scripts conditionally
			 */
			add_action('wp_enqueue_scripts', array(__CLASS__, 'addScripts'));
		}

		if (count($glossaryArticles) > 0) {
			$html .= '<div class="cmtt_related_terms_wrapper">';
			$html .= '<' . $tag . ' class="cmtt_related_title cmtt_related_terms_title">' . __(\CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesGlossaryTitle'), 'cm-tooltip-glossary') . ' </' . $tag . '>';
			$html .= '<ul class="cmtt_related">';
			foreach ($glossaryArticles as $article) {
				$title = $article->post_title;
				$html .= '<li class="cmtt_related_item">';
				$html .= '<a href="' . $article->url . '">' . $title . '</a>';
				if (\CM\CMTT_Settings::get('cmtt_glossary_relatedShowExcerpt') && !empty($article->post_excerpt)) {
					$html .= '<div>' . strip_shortcodes($article->post_excerpt) . '</div>';
				}
				$html .= '</li>';
			}
			$html .= '</ul>';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Show the list of related terms under page/post
	 * @param array $terms
	 * @return string
	 * @since 2.3.1
	 */
	public static function renderRelatedTerms($terms) {
		$html = '';
		$permalinks = array();

		if (is_array($terms) && count($terms) > 0) {
			$html .= '<div class="cmtt_related_linked_terms_wrapper">';
			$html .= '<div class="cmtt_related_title cmtt_related_linked_terms_title">' . __(\CM\CMTT_Settings::get('cmtt_glossary_showRelatedTermsTitle'), 'cm-tooltip-glossary') . '</div>';
			$html .= '<ul class="cmtt_related cmtt_related_terms">';

			foreach ($terms as $term) {
				$permalink = CMTT_Free::get_term_link($term['post']->ID);
				/*
				 * Don't show the same link multiple times for terms with different case
				 */
				if (in_array($permalink, $permalinks)) {
					continue;
				}
				$permalinks[] = $permalink;

				$title = $term['post']->post_title;

				if (\CM\CMTT_Settings::get('cmtt_showRelatedTermTooltip', 0)) {
					$tooltipContent = CMTT_Free::getTooltipContent($term['post']);
					$title = '<span aria-describedby="tt" class="glossaryLink" data-cmtooltip="' . $tooltipContent . '" >' . $title . '</span>';
				}
				if (\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefix')) {
					$title = __(\CM\CMTT_Settings::get('cmtt_glossary_relatedTermsPrefix'), 'cm-tooltip-glossary') . ' ' . $title;
				}
				$newTab = \CM\CMTT_Settings::get('cmtt_showRelatedTermNewTab', 0) ? 'target="_blank"' : '';
				$html .= '<li><a href="' . $permalink . '" ' . $newTab . '>' . $title . '</a></li>';
			}
			$html .= '</ul>';
			$html .= '</div>';
		}

		return $html;
	}

	public static function maybeAddRelatedArticles($content, $post) {
		$showPagination = (bool) \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPagination');
		$limitCustomArticles = \CM\CMTT_Settings::get('cmtt_glossary_showCustomRelatedArticlesCount');
		$relatedSnippet = CMTT_Related::renderRelatedArticles($post->ID, \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesCount'), \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesGlossaryCount'), false, $showPagination, $limitCustomArticles);
		$newContent = $content . $relatedSnippet;
		return $newContent;
	}

	/**
	 * AJAX call for the Related Articles pagination
	 *
	 * @since 3.9.2
	 */
	public static function ajaxRelatedArticlesPagination() {
		if (!wp_verify_nonce($_POST['nonce'], 'cmtt-related-terms-action')) {
			return;
		}
		$html = '';

		$relates_count = filter_input(INPUT_POST, 'related_count');
		$custom_count = filter_input(INPUT_POST, 'custom_count');
		$glossary_id = filter_input(INPUT_POST, 'glossary_id');
		$current_page = filter_input(INPUT_POST, 'current_page');

		$showRelatedArticles = (bool) \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticles');
		$showRelatedCustomArticles = (bool) \CM\CMTT_Settings::get('cmtt_glossary_showCustomRelatedArticles');

		$basic_articles = array();
		$custom_related_articles = array();

		if ($showRelatedArticles) {
			$related_articles_offset = ($current_page - 1) * $relates_count;
			$basic_articles = self::getRelatedArticles($glossary_id, $relates_count, 'all', $related_articles_offset);
		}

		if ($showRelatedCustomArticles) {
			$custom_articles_offset = ($current_page - 1) * $custom_count;
			$custom_related_articles = self::getCustomRelatedArticles($glossary_id, $custom_count, $custom_articles_offset);
		}

		$articles = array_merge($custom_related_articles, $basic_articles);

		if (count($articles) > 0) {
			foreach ($articles as $article) {
				$title = $article->post_title;
				if (\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefix') && $article->post_type == 'glossary') {
					$title = \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefix') . ' ' . $title;
                } elseif(\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefixNotGlossary') && $article->post_type !== 'glossary'){
                    $title = \CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesPrefixNotGlossary') . ' ' . $title;
                }
				$target = ($article->post_type == 'custom_related_article') ? (\CM\CMTT_Settings::get('cmtt_glossary_customRelatedArticlesNewTab', '1') ? 'target="_blank"' : '') : (\CM\CMTT_Settings::get('cmtt_glossary_relatedArticlesNewTab', '1') ? 'target="_blank"' : '');
				$html .= '<li class="cmtt_related_item">';
				$html .= '<a href="' . $article->url . '"' . $target . '>' . $title . '</a>';
				if (\CM\CMTT_Settings::get('cmtt_glossary_relatedShowExcerpt') && !empty($article->post_excerpt)) {
					$html .= '<div>' . strip_shortcodes($article->post_excerpt) . '</div>';
				}
				$html .= '</li>';
			}
		}

		wp_send_json($html);

		wp_die();
	}

	/**
	 * AJAX call for the Related Terms when using [glossary_related] shortcode
	 *
	 * @since 4.0.0
	 */
	public static function ajaxRelatedArticles() {
		if (!wp_verify_nonce($_POST['nonce'], 'cmtt-related-terms-action')) {
			return;
		}

		$post_id = filter_input(INPUT_POST, 'post_id');
		if (empty($post_id)) {
			return;
		}

		/*
		 * Get the replaced terms from meta
		 */
		$relatedTerms = CMTT_Free::_get_meta(self::TRANSIENT_NAME, $post_id);
        foreach ($relatedTerms as $key => $value) {
            if(!isset($relatedTerms[$key]['post'])) {
                $relatedTerms[$key]['post'] = get_post($value['postID']);
            }
        }

		$html = self::renderRelatedTerms($relatedTerms);
		wp_send_json($html);

		wp_die();
	}

	/**
	 * Adds script for the Related articles
	 *
	 * @since 3.9.2
	 */
	public static function addScripts() {
		if (cminds_is_amp_endpoint()) {
			return;
		}

		$inFooter = \CM\CMTT_Settings::get('cmtt_script_in_footer', true);

		// Fix for Oxygen builder (don't apply jquery-ui-core on the Oxygen edit page)
		if (!isset($_GET["ct_builder"])) {
			wp_enqueue_script(
				'cmtt-related-articles',
				self::$jsPath . 'cm-related-articles.js',
				array('jquery', 'jquery-ui-core'),
				false,
				$inFooter);
		}

		wp_localize_script('cmtt-related-articles', 'cmtt_relart_data', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => wp_create_nonce('cmtt-related-terms-action')
		));
	}

}

add_action('glossary_daily_event', array('CMTT_Related', 'crawlArticles'));
