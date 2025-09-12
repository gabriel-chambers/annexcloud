<?php

class CMTT_Post_Duplicates {

	public static $urlParameter = 'cmtt-is-duplicate';

	public static function init() {

		/*
		 * Admin part
		 */
		add_filter('wp_insert_post_empty_content', array(__CLASS__, 'on_submitted_post'), 10, 2);

		add_action('after_delete_post', array(__CLASS__, 'on_deleted_post'));
		add_action('deleted_post', array(__CLASS__, 'on_deleted_post'));
		add_action('trashed_post', array(__CLASS__, 'on_deleted_post'));

		add_filter('untrashed_post', array(__CLASS__, 'on_untrashed_post'));

		add_action('edit_form_top', array(__CLASS__, 'display_notice'));

		/*
		 * Frontend part
		 */
		add_filter('cmtt_glossary_index_tooltip_content', array(__CLASS__, 'add_duplicates_content'), 15, 2);
		add_filter('cmtt_term_tooltip_content', array(__CLASS__, 'add_duplicates_content'), 15, 2);
		add_filter('cmtt_footnotes_definition_content', array(__CLASS__, 'add_duplicates_content_footnote'), 15, 3);

		add_filter('cmtt_add_backlink_content', array(__CLASS__, 'display_duplicates_on_term_page'), 100, 2);
		add_filter('cmtt_footnotes_meaning_definition', array(__CLASS__, 'displayCategoriesInFootnotesDuplicate'), 10, 2);
	}

	/**
	 * Add the duplicates to the content of the tooltip
	 * @param type $content
	 * @param type $glossary_item
	 * @return type
	 */
	public static function display_duplicates_on_term_page($content, $glossary_item) {
		$enabled = \CM\CMTT_Settings::get('cmtt_alternativeMeaningsInGlossaryTermPage', '1');
		if (!$enabled) {
			return $content;
		}
		$duplicates = self::find_duplicates($glossary_item->post_title, $glossary_item->ID);

		$tag = 'div';
		if (is_array($duplicates) && count($duplicates) > 0) {
            $length = \CM\CMTT_Settings::get('cmtt_alternativeMeaningsInGlossaryTermPageLength', 2000);
			$content .= '<div class="cmtt_alternative_meanings_wrapper">';
			$content .= '<' . $tag . ' class="cmtt_related_title cmtt_related_terms_title">' . __(\CM\CMTT_Settings::get('cmtt_glossary_AlternativeMeaningLabel', 'Alternative Meanings:'), 'cm-tooltip-glossary') . ' </' . $tag . '>';
			$content .= '<ul class="cmtt_related">';
			foreach ($duplicates as $key => $duplicate) {
				/*
				 * We want to display the icon
				 */
				$title = get_the_title($duplicate);
				$content .= '<li class="cmtt_related_item">';
				$content .= '<a href="' . get_permalink($duplicate->ID) . '">' . $title . '</a>';
				$content .= '<div>' . cminds_truncate(do_shortcode(do_blocks($duplicate->post_content)), $length) . '</div>';
				$content .= '</li>';
			}
			$content .= '</ul>';
			$content .= '</div>';
		}


		return $content;
	}

	/**
	 * Add the duplicates to the content of the tooltip
	 * @param type $content
	 * @param type $glossary_item
	 * @return type
	 */
	public static function add_duplicates_content($content, $glossary_item) {
		$enabled = \CM\CMTT_Settings::get('cmtt_alternativeMeaningsInTooltips', '1');
		if (!$enabled || empty($glossary_item->post_title)) {
			return $content;
		}
		$duplicates = self::find_duplicates($glossary_item->post_title, $glossary_item->ID);
		if (!empty($duplicates)) {
			$count_start = 1;
			if (!empty($content)) {
				$count_start = 2;
				$content = '<div class="cmtt_meaning_label">1</div>' . $content;
			}
			foreach ($duplicates as $key => $duplicateId) {
				$duplicate = get_post($duplicateId);
				$post_content = (\CM\CMTT_Settings::get('cmtt_glossaryExcerptHover') && $duplicate->post_excerpt) ? $duplicate->post_excerpt : $duplicate->post_content;
				if (!empty($post_content)) {
					$content .= sprintf('<div><div class="cmtt_meaning_label">%d</div>', $count_start + $key);
					$content .= $post_content;
					$content .= '</div>';
				}
			}
		}
		return $content;
	}

	/*
	 * Add the duplicates to the content of the footnote
	 * @param type $content
	 * @param type $glossary_item
	 * @return type
	 */

	public static function add_duplicates_content_footnote($content, $term, $values) {
		$enabled = \CM\CMTT_Settings::get('cmtt_alternativeMeaningsInFootnotes', '1');
		$post_id = $values['postID'];
		if (!empty($post_id)) {
			$glossary_item = get_post($post_id);
		}
		if (!$enabled || empty($glossary_item->post_title)) {
			return $content;
		}
		$duplicates = self::find_duplicates($glossary_item->post_title, $glossary_item->ID);
		if (!empty($duplicates)) {
			$count_start = 1;
			if (!empty($content)) {
				$count_start = 2;
				$content = '<div><span class="cmtt_meaning_label">1 </span><span class="cmtt_meaning_definition">' . $content . '</span></div>';
			}
			foreach ($duplicates as $key => $duplicateId) {
				$duplicate = get_post($duplicateId);
				$post_content = (\CM\CMTT_Settings::get('cmtt_glossaryExcerptHover') && $duplicate->post_excerpt) ? $duplicate->post_excerpt : $duplicate->post_content;
				if (!empty($post_content)) {
					$post_content = strip_tags($post_content);
					$content .= '<div>' . sprintf('<span class="cmtt_meaning_label">%d </span>', $count_start + $key);
					$content .= '<span class="cmtt_meaning_definition">' . apply_filters('cmtt_footnotes_meaning_definition', $post_content, $duplicate) . '</span></div>';
				}
			}

			$content = '<div class="cmtt_meanings_wrapper">' . $content . '</div>';
		}
		return $content;
	}

	public static function displayCategoriesInFootnotesDuplicate($content, $term) {

		$showCategories = \CM\CMTT_Settings::get('cmtt_footnoteShowCategories', false);
		if ($showCategories && !empty($term->ID) && class_exists('CMTT_Glossary_Plus')) {
			$internalContent = CMTT_Glossary_Plus::displayTaxonomyTerms('glossary-categories', $term->ID);
			$content = $internalContent . $content;
		}
		return $content;
	}

	public static function display_notice($post) {
		$duplicateIds = get_post_meta($post->ID, self::$urlParameter, true);

		if (empty($duplicateIds)) {
			return;
		}

		if (!is_array($duplicateIds)) {
			$duplicateIds = array($duplicateIds);
		}

		$duplicatesArr = array();
		foreach ($duplicateIds as $postId) {
			$tooltipcontent = @get_the_excerpt($postId);
			$duplicate = sprintf('<a href="%s" target="_blank" class="cmtt_field_help" title="%s">%s</a>(<a href="%s" target="_blank">%s</a>)', get_permalink($postId), $tooltipcontent, $postId, get_edit_post_link($postId), 'edit');
			$duplicatesArr[] = $duplicate;
		}
		ob_start();
		?>
		<div id="warning" class="notice notice-warning"><p id="is-duplicate">This post is a duplicate of: <?php echo implode(', ', $duplicatesArr); ?></p></div>
		<?php
		$content = ob_get_clean();
		echo $content;
	}

	public static function find_duplicates($title, $id) {
		global $post;
		$args = array(
			'post_type'    => 'glossary',
			'post_status'  => 'publish',
			'numberposts'  => -1,
			'nopaging'     => true,
			'title'        => $title,
			'post__not_in' => array($id),
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		if (!empty($post)) {
			$customCats = CMTT_Glossary_Plus::getCurrentCustomCats($post->ID);
			$customCatsType = CMTT_Glossary_Plus::getCurrentCustomCatsType($post->ID);

			if (is_array($customCats)) {
				$tagsQuery = array(
					'taxonomy' => 'glossary-categories',
					'field'    => 'term_id',
					'terms'    => $customCats,
					'operator' => 'whitelist' === $customCatsType ? 'IN' : 'NOT IN'
				);

				if (!empty($args['tax_query'])) {
					$args['tax_query'][] = $tagsQuery;
					$args['tax_query']['relation'] = 'AND';
				} else {
					$args['tax_query'] = array(
						$tagsQuery
					);
				}
			}
		}

		if (class_exists('CMTT_Glossary_Ecommerce')) {
			add_filter('cmtt_duplicates_args', array('CMTT_Glossary_Ecommerce', 'addParserQueryArgs'), 10, 1);
		}
		$args = apply_filters('cmtt_duplicates_args', $args);
		$maybe_duplicates = get_posts($args);

		if (!empty($maybe_duplicates)) {

			/*
			 * Filter out private duplicates
			 */
			$maybe_duplicates = array_filter($maybe_duplicates, function ($duplicate) {
				$private = CMTT_Free::_get_meta('cmtt_private', $duplicate->ID);

				/*
				 * If Languages functionality is enabled we need to check if duplicate is of the right language
				 */
				if (class_exists('CMTT_Languages')) {
					$private = !CMTT_Languages::is_correct_tooltip_language($duplicate) || $private;
				}
				return !$private;
			});
		}

		return $maybe_duplicates;
	}

	public static function mark_duplicates($the_ID, $deleted = false) {
		$post_data = get_post($the_ID, ARRAY_A);
		if (!is_null($post_data) && $post_data['post_type'] == 'glossary' && $post_data['post_status'] !== 'auto-draft' && !empty($post_data['post_title'])) {
			$maybe_duplicates = self::find_duplicates($post_data['post_title'], $post_data['ID']);

			if (!empty($maybe_duplicates) && is_array($maybe_duplicates)) {
				$duplicateIds = array_map(function ($v) {
					return $v->ID;
				}, $maybe_duplicates);
				update_post_meta($the_ID, self::$urlParameter, $duplicateIds);

				foreach ($duplicateIds as $ID) {
					$duplicateIdsCopy = $duplicateIds;
					$duplicateIdsCopy = array_map(function ($v) use ($ID, $the_ID, $deleted) {
						if ($deleted) {
							return $v == $ID ? '' : $v;
						} else {
							return $v == $ID ? $the_ID : $v;
						}
					}, $duplicateIdsCopy);
					update_post_meta($ID, self::$urlParameter, array_filter($duplicateIdsCopy));
				}
			}
		}
		return;
	}

	/**
	 * Hook to the "wp_insert_post_empty_content" filter, since that is the only place
	 * we can intercept the creation of a new post and not let it be created
	 * @global type $usp_options
	 * @param type $maybe_empty
	 * @param type $post_data
	 * @return type
	 */
	public static function on_submitted_post($maybe_empty, $post_data) {
		$enabled = \CM\CMTT_Settings::get('cmtt_alternativeMeaningsAllow', '1');
		if (!$enabled) {

			if ($post_data['post_type'] == 'glossary' && $post_data['post_status'] !== 'auto-draft' && !empty($post_data['post_title'])) {
				$maybe_duplicates = self::find_duplicates($post_data['post_title'], $post_data['ID']);
				if (!empty($maybe_duplicates)) {

					/*
					 * Return true treats the post as empty and doesn't save
					 */
					return true;
				}
			}
		}

		self::mark_duplicates($post_data['ID']);
		return $maybe_empty;
	}

	/**
	 * Update post duplicates after post was untrashed
	 * @param type $the_ID
	 */
	public static function on_untrashed_post($the_ID) {
		self::mark_duplicates($the_ID);
	}

	/**
	 * Update post duplicates after post was deleted
	 * @param type $the_ID
	 */
	public static function on_deleted_post($the_ID) {
		self::mark_duplicates($the_ID, true);
	}

}
