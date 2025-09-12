<?php

use CM\CMTT_Settings;

class CMTT_Glossary_Plus {

	/**
	 *
	 */
	const CATEGORY_TAXONOMY = 'glossary-categories';

	/**
	 * @var string
	 */
	protected static $filePath = '';
	/**
	 * @var string
	 */
	protected static $cssPath = '';
	/**
	 * @var string
	 */
	protected static $jsPath = '';

	/**
	 * Removes the hooks
	 */
	public static function after() {
		remove_filter( 'cmtt_glossary_index_listnav_content', array( 'CMTT_Glossary_Index', 'removeListnav' ) );
	}

	/**
	 * Adds the hooks
	 */
	public static function init() {
		global $cmtt_isLicenseOk;

		self::$filePath = plugin_dir_url( __FILE__ );
		self::$cssPath  = self::$filePath . 'assets/css/';
		self::$jsPath   = self::$filePath . 'assets/js/';

		self::includeFiles();
		self::initFiles();

		/*
		 * ACTIONS
		 */
		add_action( 'init', array( __CLASS__, 'createTaxonomies' ) );
		add_action( 'cmtt_disable_parsing', array( __CLASS__, 'disableParsing' ) );
		add_action( 'cmtt_reenable_parsing', array( __CLASS__, 'reenableParsing' ) );

		add_action( 'cmtt_save_options_after_on_save', array( __CLASS__, 'flushMWCache' ), 10, 2 );
		add_action( 'cmtt_on_glossary_item_save', array( __CLASS__, 'saveAdditionalPostData' ), 10, 2 );
		add_action( 'cmtt_on_glossary_item_save_before', array( __CLASS__, 'flushSingleMWCache' ), 10, 2 );
		add_action( 'cmtt_on_glossary_item_save_before', array( __CLASS__, 'saveSelectedTermsForPage' ), 11, 2 );
		add_action( 'cmtt_on_glossary_item_save_before', array( __CLASS__, 'saveDisableRelatedPosts' ), 12, 2 );
		add_action( 'save_post', array( __CLASS__, 'cmtt_save_negative_words_for_item' ), 13, 2 );

		add_filter( 'cmtt_post_type_args', array( __CLASS__, 'addPostTypeSupport' ) );
		add_filter( 'cmtt_edit_properties_metabox_array', array( __CLASS__, 'renderIconSelectButton' ), 10, 2 );
		add_filter( 'cmtt_edit_properties_metabox_array', array( __CLASS__, 'renderFlushButton' ), 10, 2 );
		add_action( 'cmtt_quick_edit_content', array( __CLASS__, 'addQuickEdit' ) );
		add_action( 'cmtt_meta_column_content', array( __CLASS__, 'addMetaColumn' ) );

		add_filter( 'cmtt-settings-tabs-array', array( __CLASS__, 'addSettingsTabs' ) );
		add_filter( 'cmtt-custom-settings-tab-content-5', array( __CLASS__, 'addAPITabContent' ) );
		add_filter( 'cmtt-custom-settings-tab-content-8', array( __CLASS__, 'addGlossaryReplacementTabContent' ) );

		add_filter( 'cmtt_add_properties_metabox', array( __CLASS__, 'addMetaboxFields' ) );
		add_action( 'cmtt_add_disables_metabox', array( __CLASS__, 'addDisablesFields' ) );
		add_action( 'cmtt_register_boxes', array( __CLASS__, 'registerMetaboxes' ) );

		add_filter( 'cmtt_disable_metabox_posttypes', array( __CLASS__, 'filterDisableMetaboxPosttypes' ) );

		add_filter( 'cmtt_add_admin_menu_after_new', array( __CLASS__, 'addAdminMenuItems' ) );
		add_filter( 'cmtt_enqueueFlushRules', array( __CLASS__, 'enqueueFlushRules' ), 10, 2 );

		add_action( 'cmtt_flush_rewrite_rules', array( __CLASS__, 'createTaxonomies' ) );
		add_action( 'parent_file', array( __CLASS__, 'setCurrentMenu' ) );

		if ( ! $cmtt_isLicenseOk ) {
			return;
		}

		if ( CMTT_Settings::get( 'cmtt_glossaryRTL' ) == 1 ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, '_rtl_support' ), 11 );
		}

		add_filter( 'the_content', array( __CLASS__, 'addRelatedTerms' ), 21500 );

		/**
		 * Avada (fusion) Builder Fix
		 */
		add_action( 'fusion_pause_live_editor_filter', array( __CLASS__, 'fusionBuilderFix' ) );

		if ( ! is_admin() ) {
			add_filter( 'the_title', array( __CLASS__, 'addAbbreviation' ), 22000, 2 );
			add_filter( 'the_title', array( __CLASS__, 'addDashiconForTitle' ), 23000, 2 );
		}

		add_filter( 'cmtt_glossaryItemTitle', array( __CLASS__, 'addDashicon' ), 23000, 3 );
		add_filter( 'cmtt_glossaryItemTitle', array( __CLASS__, 'addCategory' ), 22000, 3 );

		if ( CMTT_Settings::get( 'cmtt_glossaryTermsInComments', false ) ) {
			add_filter( 'comment_text', array( 'CMTT_Free', 'cmtt_glossary_parse' ), 20000 );
		}

		/* Filter the single_template with our custom function */
		add_filter( 'single_template', array( __CLASS__, 'glossaryTermCustomTemplate' ), 200 );
		add_action( 'widgets_init', array( __CLASS__, 'cmtt_custom_sidebar' ) );

		add_filter( 'cmtt_glossary_index_shortcode_default_atts', array( __CLASS__, 'addGlossaryIndexDefaultAtts' ) );
		add_filter( 'cmtt_glossary_index_atts', array( __CLASS__, 'addGlossaryIndexPostAtts' ), 20 );
		add_filter( 'cmtt_glossary_index_atts', array( __CLASS__, 'processGlossaryIndexShortcodeAtts' ), 30 );
		add_filter( 'cmtt_glossary_index_style', array( __CLASS__, 'changeGlossaryIndexStyle' ) );

		add_filter( 'cmtt_glossary_index_query_args', array( __CLASS__, 'addSearchFilter' ), 10, 2 );
		add_filter( 'cmtt_glossary_index_query_args', array( __CLASS__, 'addServerSidePaginationFilter' ), 10, 2 );

		add_filter( 'cmtt_glossary_index_query_args', array( __CLASS__, 'addGlossaryIndexQueryArgs' ), 10, 2 );

		add_filter( 'cmtt_parser_query_args', array( __CLASS__, 'addParserQueryArgs' ), 10, 2 );

		add_filter( 'cmtt_glossary_index_before_listnav_content', array( __CLASS__, 'outputBeforeListnav' ), 10, 3 );

		add_filter( 'cmtt_glossary_index_style_classes', array( __CLASS__, 'addGlossaryIndexStyles' ) );
		add_filter( 'cmtt_glossary_index_item_desc', array( __CLASS__, 'outputGlossaryIndexItemDesc' ), 10, 4 );
		add_filter( 'cmtt_glossary_index_item_desc', array( __CLASS__, 'stripDescriptionShortcode' ), 20, 4 );
		add_filter( 'cmtt_glossary_index_item_desc', array( __CLASS__, 'addGlossaryIndexDescRelated' ), 50, 4 );
		add_filter( 'cmtt_glossary_index_item_desc', array( __CLASS__, 'outputExpandStyleWrapper' ), 150, 4 );

		add_filter( 'cmtt_glossary_index_tooltip_content', array(
			__CLASS__,
			'outputGlossaryIndexGoogleTermOnly'
		), 5, 2 );
		add_filter( 'cmtt_glossary_index_tooltip_content', array(
			__CLASS__,
			'outputGlossaryIndexGoogleTranslation'
		), 25, 2 );

		add_filter( 'cmtt_listnav_js_args', array( __CLASS__, 'addListnavArgs' ) );
		add_filter( 'cmtt_add_backlink_content', array( __CLASS__, 'addBacklinkContent' ), 5, 2 );

		add_action( 'cmtt_import_glossary_item', array( __CLASS__, 'importAdditionalInfo' ), 10, 2 );

		add_filter( 'cmtt_export_data_row', array( __CLASS__, 'addExportDataRowFields' ), 10, 2 );

		add_action( 'cmtt_replace_template_before_synonyms', array( __CLASS__, 'applyParseCustomTermList' ), 10, 2 );
		add_action( 'cmtt_replace_template_after_synonyms', array( __CLASS__, 'applyCategoryFiltering' ), 10, 3 );
		add_action( 'cmtt_replace_template_after_synonyms', array( __CLASS__, 'excludeNegativeWords' ), 12, 4 );

		/*
		 * @since 4.2.8 - needed to fix the temporary markup used for omitting the negative terms
		 */
		add_action( 'cmtt_parsed_content_with_exceptions', array( __CLASS__, 'cleanup_negative_term_markup' ) );

		add_action( 'cmtt_3rdparty_tooltip_content', array( __CLASS__, 'addMWToTooltipContent' ), 10, 3 );
		add_action( 'cmtt_3rdparty_tooltip_content', array( __CLASS__, 'addGlosbeToTooltipContent' ), 10, 3 );
		add_filter( 'cmtt_term_tooltip_content', array( __CLASS__, 'outputGlossaryTermTranslation' ), 25, 2 );

		add_filter( 'cmtt_term_tooltip_additional_class', array( __CLASS__, 'addCategoryClass' ), 10, 2 );
		add_filter( 'cmtt_term_tooltip_additional_class', array( __CLASS__, 'addTermAdditionalClass' ), 10, 2 );
		add_filter( 'cmtt_term_tooltip_permalink', array( __CLASS__, 'changeTermPermalink' ), 10, 2 );

		add_filter( 'cmtt_glossaryPreItemTitleContent_add', array( __CLASS__, 'addNewIconGlossaryIndex' ), 10, 2 );

		add_filter( 'cmtt_glossary_index_item_additions', array( __CLASS__, 'addAbbreviationsToAdditions' ), 10, 4 );
		add_filter( 'cmtt_glossary_index_item_additions', array( __CLASS__, 'addNegativeWordsToAdditions' ), 5, 4 );

		add_filter( 'cmtt_glossary_term_after_content', array( __CLASS__, 'termAddListnav' ), 20 );

		add_filter( 'cmtt_tooltip_script_data', array( __CLASS__, 'glossaryTempDisableTooltips' ), PHP_INT_MAX );

		add_action( 'cmtt_glossary_doing_search', array( __CLASS__, 'addSearchFilters' ), 10, 2 );

		add_filter( 'cmtt_runParser', array( __CLASS__, 'maybeDisableTooltips' ), 10, 4 );

		add_filter( 'cmtt_add_backlink_content', array( __CLASS__, 'displayCategoriesOnSinglePage' ) , 10 + (int)\CM\CMTT_Settings::get( 'cmtt_glossary_taxonomies_order', 1 ));
		add_filter( 'cmtt_add_backlink_content', array( __CLASS__, 'displayTagsOnSinglePage' ) , 10 + (int)\CM\CMTT_Settings::get( 'cmtt_glossary_taxonomies_order', 1 ));

		add_filter( 'cmtt_3rdparty_tooltip_content', array(
			__CLASS__,
			'cmtt_add_featured_image_to_tooltip_content'
		), 10, 3 );

		add_filter( 'cmtt_glossary_index_before_terms_list', array(
			__CLASS__,
			'cmtt_glossary_index_before_terms_list'
		), 50, 3 );

		add_filter( 'cmtt_term_disable_tooltip_by_category', array(
			__CLASS__,
			'cmtt_glossary_disable_tooltips_by_category'
		), 10, 2 );

		add_filter( 'cmtt_footnotes_definition_content', array( __CLASS__, 'displayCategoriesInFootnotes' ), 10, 3 );
		add_filter( 'cmtt_liAdditionalClass', array( __CLASS__, 'addLetterToGlossaryIndexClasses' ), 10, 3 );
		add_filter( 'cmtt_term_custom_attribute', array( __CLASS__, 'add_custom_attributes' ), 10, 2 );

		add_filter( 'cmtt_term_tooltip_additional_attibutes', array(
			__CLASS__,
			'add_custom_tooltip_attributes'
		), 10, 2 );
		/*
		 * Custom  Index Link
		 */
		// Add/Edit
		add_action( 'glossary-categories_add_form_fields', array( __CLASS__, 'taxonomy_additional_fields' ), 10, 2 );
		add_action( 'glossary-categories_edit_form_fields', array( __CLASS__, 'taxonomy_additional_fields' ), 10, 2 );
		// Save
		add_action( 'edited_glossary-categories', array( __CLASS__, 'save_additional_fields' ), 10, 2 );
		add_action( 'created_glossary-categories', array( __CLASS__, 'save_additional_fields' ), 10, 2 );
		// Use
		add_action( 'cmtt_glossary_backlink_href', array( __CLASS__, 'customBackLink' ), 10, 2 );

		/*
		 * Whitelist/Blacklist of categories
		 */
		// Add/Edit
		add_action( 'category_add_form_fields', array( __CLASS__, 'categoryWhitelistBlacklistField' ), 10, 2 );
		add_action( 'category_edit_form_fields', array( __CLASS__, 'categoryWhitelistBlacklistField' ), 10, 2 );
		add_action( 'glossary-categories_add_form_fields', array(
			__CLASS__,
			'categoryWhitelistBlacklistField'
		), 10, 2 );
		add_action( 'glossary-categories_edit_form_fields', array(
			__CLASS__,
			'categoryWhitelistBlacklistField'
		), 10, 2 );
		// Save
		add_action( 'edited_category', array( __CLASS__, 'saveWhitelistBlacklistField' ), 10, 2 );
		add_action( 'created_category', array( __CLASS__, 'saveWhitelistBlacklistField' ), 10, 2 );
		add_action( 'edited_glossary-categories', array( __CLASS__, 'saveWhitelistBlacklistField' ), 10, 2 );
		add_action( 'created_glossary-categories', array( __CLASS__, 'saveWhitelistBlacklistField' ), 10, 2 );

		add_action( 'cmtt_settings_labels_content_end', array( __CLASS__, 'addLabels' ) );
		add_filter( 'cmtt_glossary_index_after_content', array( __CLASS__, 'outputAdditionalHTML' ), 5, 3 );
		/*
		 * SHORTCODES
		 */
		add_shortcode( 'glossary-term', array( __CLASS__, 'glossaryTermShortcode' ) );
		add_shortcode( 'glossary-listnav', array( __CLASS__, 'glossaryListnavShortcode' ) );
		add_shortcode( 'glossary-toogle-tooltips', array( __CLASS__, 'glossaryToggleTooltips' ) );
		add_shortcode( 'glossary-toggle-theme', array( __CLASS__, 'glossaryToggleTheme' ) );
		add_shortcode( 'glossary_search', array( __CLASS__, 'glossarySearchShortcode' ) );
        add_shortcode( 'glossary_terms_amount', array( 'CMTT_Pro', 'countGlossaryTerms' ) );

		/*
		 * AJAX
		 */
		add_filter( 'cmtt_glossaryNoAjax', '__return_false' );

		add_action( 'wp_ajax_glossary_search', array( __CLASS__, 'ajaxGlossary' ) );
		add_action( 'wp_ajax_nopriv_glossary_search', array( __CLASS__, 'ajaxGlossary' ) );
	}

	/**
	 * Include new files
	 */
	public static function includeFiles() {
		include_once CMTT_PLUGIN_DIR . 'glossaryReplacement.php';
		include_once CMTT_PLUGIN_DIR . 'abbreviations.php';
		include_once CMTT_PLUGIN_DIR . 'vendor/api_cache.php';
		include_once CMTT_PLUGIN_DIR . 'thirdparty.php';
		include_once CMTT_PLUGIN_DIR . 'glosbe.php';
		include_once CMTT_PLUGIN_DIR . 'postDuplicates.php';
		include_once CMTT_PLUGIN_DIR . 'postEmbed.php';
        include_once CMTT_PLUGIN_DIR . 'archive.php';
	}

	/**
	 * Init new files
	 */
	public static function initFiles() {
		CMTT_Glossary_Replacement::init();
		CMTT_Abbreviations::init();
		CMTT_Post_Duplicates::init();
		CMTT_Post_Embed::init();
		CMTT_Mw_API::addShortcodes();
		CMTT_Google_API::addShortcodes();
		CMTT_Glosbe_API::addShortcodes();
        CMTT_Archive::init();
        CMTT_AlphabeticalIndexArchive_Widget::init();
	}

	/**
	 * Wrap Glossary Index in styling container
	 *
	 * @param $content
	 * @param $glossary_query
	 * @param $shortcodeAtts
	 *
	 * @return mixed|string
	 */
	public static function outputAdditionalHTML( $content, $glossary_query, $shortcodeAtts ) {
		if ( ! defined( 'DOING_AJAX' ) ) {
			$glossaryIndexStyle = $shortcodeAtts['glossary_index_style'];
			if ( 'sidebar-termpage' === $glossaryIndexStyle ) {
				if ( isset( $shortcodeAtts['term'] ) ) {
					$internalContent = '[glossary-term term="' . $shortcodeAtts['term'] . '" run_filter="1"]';
				} else {
					$internalContent = __( \CM\CMTT_Settings::get( 'cmtt_index_sidebar_default', 'Select the term to display its content.' ), 'cm-tooltip-glossary' );
				}
				$content .= '<div class="glossary-term-content">' . do_shortcode( apply_filters( 'cmtt_single_glossary_term_definition', $internalContent, $glossary_query, $shortcodeAtts ) ) . '</div>';
			}
		}

		return $content;
	}

	/**
	 * Function added in 4.2.2 allows to enable the mobile support on per-post basis
	 *
	 * @param $attributes
	 * @param $glossaryItem
	 *
	 * @return mixed
	 * @since 4.2.2
	 */
	public static function add_custom_tooltip_attributes( $attributes, $glossaryItem ) {
		$mobile_support = CMTT_Free::_get_meta( '_cmtt_toggle_mobile_support', $glossaryItem->ID );
		if ( empty( $mobile_support ) ) {
			$mobile_support = '0';
		}
		$attributes['data-mobile-support'] = $mobile_support;

		return $attributes;
	}

	public static function add_custom_attributes( $content, $glossaryItem ) {

		$tag              = self::get_term_category( $glossaryItem );
		$category_bgcolor = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_bg_color', true ) : '';
		$category_tbgcolor = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_title_bg_color', true ) : '';
		if ( $category_bgcolor ) {
			$content .= 'data-bgcolor="' . esc_attr( $category_bgcolor ) . '"';
		}
		if ( $category_tbgcolor ) {
			$content .= 'data-tbgcolor="' . esc_attr( $category_tbgcolor ) . '"';
		}

		$category_tcolor = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_t_color', true ) : '';
		if ( $category_tcolor ) {
			$content .= 'data-tcolor="' . esc_attr( $category_tcolor ) . '"';
		}

		$category_tsize = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_t_size', true ) : '';
		if ( $category_tsize ) {
			$content .= 'data-tsize="' . esc_attr( $category_tsize ) . '"';
		}

        $category_twidth  = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_t_width', true ) : '';
        if ( $category_twidth ) {
            $content .= 'data-twidth="' . esc_attr( $category_twidth ) . '"';
        }

        $category_theight  = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_t_height', true ) : '';
        if ( $category_theight ) {
            $content .= 'data-theight="' . esc_attr( $category_theight ) . '"';
        }

		return $content;
	}

	public static function get_term_category( $glossary_item ) {
		$id    = is_object( $glossary_item ) ? $glossary_item->ID : $glossary_item;
		$terms = get_the_terms( $id, CMTT_Glossary_Plus::CATEGORY_TAXONOMY );
		if ( ! $terms || ! is_array( $terms ) ) {
			return '';
		}

		return reset( $terms );
	}

	/**
	 * @param $liAdditionalClass
	 * @param $glossaryItem
	 * @param $letters
	 *
	 * @return string
	 */
	public static function addLetterToGlossaryIndexClasses( $liAdditionalClass, $glossaryItem, $letters ) {
		$first_letter = substr( strtolower( $glossaryItem->post_title ), 0, 1 );
		if ( ! is_numeric( $first_letter ) ) {
			$first_letter_count = array_search( $first_letter, $letters );
			$first_letter_count = $first_letter_count >= 0 ? $first_letter_count : '-';
		} else {
			$first_letter_count = 'num';
		}
		$first_letter_class = 'ln-' . $first_letter_count;
		$liAdditionalClass  .= ' ' . trim( $first_letter_class );

		return $liAdditionalClass;
	}

	/**
	 * @return void
	 */
	public static function fusionBuilderFix() {
		remove_filter( 'the_content', array( 'CMTT_Glossary_Plus', 'addRelatedTerms' ), 21500 );
		add_filter( 'fusion_component_fusion_tb_content_content', array( __CLASS__, 'addRelatedTerms' ), 21500 );
	}

	/*
	 * Taxonomy Whitelist/Blacklist featrue
	 * @since 4.0.5
	 */

	/**
	 * @param $link
	 * @param $post
	 *
	 * @return mixed|string
	 */
	public static function customBackLink( $link, $post ) {
		global $post;
		if ( empty( $post ) ) {
			return $link;
		}
		$terms = wp_get_object_terms( $post->ID, 'glossary-categories', array( 'hide_empty' => false ) );
		if ( ! empty( $terms ) ) {
			$firstTerm  = array_shift( $terms );
			$customLink = get_term_meta( $firstTerm->term_id, '_cmtt_category_index_link', true );
			if ( ! empty( $customLink ) ) {
				$link = self::_addhttp( $customLink );
			}
		}

		return $link;
	}

	/**
	 * @param $url
	 *
	 * @return mixed|string
	 */
	public static function _addhttp( $url ) {
		if ( ! preg_match( '~^(?:f|ht)tps?://~i', $url ) ) {
			$url = 'https://' . $url;
		}

		return $url;
	}

	/**
	 * @param $tag
	 * @param $taxonomy
	 *
	 * @return void
	 */
	public static function categoryWhitelistBlacklistField( $tag, $taxonomy = 'glossary-categories' ) {
		$currentCustomCats     = isset( $tag->term_id ) && is_array( self::getTaxonomyCustomCats( $tag->term_id ) ) ? self::getTaxonomyCustomCats( $tag->term_id ) : array( 'all' );
		$currentCustomCatsType = isset( $tag->term_id ) ? self::getTaxomomyCustomCatsType( $tag->term_id ) : '';
		$catSelectOutput       = '';
		$cat_args              = array(
			'taxonomy'   => 'glossary-categories',
			'hide_empty' => false,
			'orderby'    => 'name',
			'number'     => 0,
		);
		$cats                  = get_terms( $cat_args );
		?>
		<tr class="form-field term-name-wrap">
			<th scope="row">
				<label
					for="cmtt_category_index_link"><?php _e( 'Whitelist/Blacklist Glossary Categories', CMTT_SLUG_NAME ); ?></label>
			</th>
			<td>
				<div>
					Hold CTRL before clicking to select multiple categories, or to unselect the category.<br/>
					<div class="cm-showhide">
						<h5 class="cm-showhide-handle">More info &rArr;</h5>
						<div class="cm-showhide-content">
							<i>
								Remember: if you decide to fill this field, and select the "Whitelist" then for this
								category - then only the terms from cats which are on this list<strong> and </strong>
								are defined in the Glossary will be highlighted in the content if found. If you choose
								"Blacklist" instead, terms from selected cats will not be highlighted on the posts/pages
								belonging to this category.
							</i>
						</div>
					</div>
				</div>
				<?php
				if ( ! empty( $cats ) ) {
					$catSelectOutput .= '<input type="hidden" name="cmtt_category_custom_cats[]" value="" />';
					$catSelectOutput .= '<select class="cmtt_select2" name="cmtt_category_custom_cats[]" multiple>';
					foreach ( $cats as $cat ) {
						$selected        = in_array( $cat->term_id, $currentCustomCats ) ? 'selected="selected"' : '';
						$catSelectOutput .= '<option ' . $selected . ' value="' . $cat->term_id . '">' . $cat->name . '</option>';
					}
					$catSelectOutput .= '</select>';
				}
				echo $catSelectOutput;
				?>
				<select name="cmtt_category_custom_cats_type">
					<option value="" <?php selected( '', $currentCustomCatsType ); ?> >-none-</option>
					<option value="whitelist" <?php selected( 'whitelist', $currentCustomCatsType ); ?> >Whitelist
					</option>
					<option value="blacklist"<?php selected( 'blacklist', $currentCustomCatsType ); ?> >Blacklist
					</option>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getTaxonomyCustomCats( $id ) {
		$customTerms = get_term_meta( $id, '_cmtt_category_custom_cats', true );
		if ( is_array( $customTerms ) ) {
			$customTerms = array_filter( $customTerms );
		}

		return $customTerms;
	}

	/**
	 * @param $id
	 *
	 * @return mixed|string
	 */
	public static function getTaxomomyCustomCatsType( $id ) {
		$customTermsType = get_term_meta( $id, '_cmtt_category_custom_cats_type', true );
		if ( ! in_array( $customTermsType, array( 'blacklist', 'whitelist', '' ) ) ) {
			$customTermsType = 'whitelist';
		}

		return $customTermsType;
	}

	/**
	 * @param $term_id
	 * @param $tt_id
	 *
	 * @return void
	 */
	public static function saveWhitelistBlacklistField( $term_id, $tt_id ) {
		if ( isset( $_POST['cmtt_category_custom_cats'] ) ) {
			$cats = $_POST['cmtt_category_custom_cats'];
			update_term_meta( $term_id, '_cmtt_category_custom_cats', $cats );
		}
		if ( isset( $_POST['cmtt_category_custom_cats_type'] ) ) {
			$type = $_POST['cmtt_category_custom_cats_type'];
			update_term_meta( $term_id, '_cmtt_category_custom_cats_type', $type );
		}
	}

	/**
	 * @param $tag
	 * @param $taxonomy
	 *
	 * @return void
	 */
	public static function taxonomy_additional_fields( $tag, $taxonomy = 'glossary-categories' ) {
		$link            = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_index_link', true ) : '';
		$disable_tt      = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_disable_tooltips', true ) : '';
		$custom_bg_color = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_bg_color', true ) : '';
        $custom_title_bg_color = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_title_bg_color', true ) : '';
		$custom_t_color  = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_t_color', true ) : '';
		$custom_t_size   = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_t_size', true ) : '';
        $custom_t_width  = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_t_width', true ) : '';
        $custom_t_height  = ( is_object( $tag ) ) ? get_term_meta( $tag->term_id, '_cmtt_category_custom_t_height', true ) : '';
		?>
		<tr class="form-field term-name-wrap">
			<th scope="row">
				<label
					for="cmtt_category_index_link"><?php _e( 'Taxonomy Index Link', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input name="cmtt_category_index_link"
				       id="cmtt_category_index_link"
				       value="<?php echo esc_attr( $link ); ?>"
				       type="text" size="40"/>
				<p class="description">
					<?php _e( 'Enter the exact url of the page where the "Back to Glossary Index" button should link to.', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field term-name-wrap">
			<th scope="row">
				<label
					for="cmtt_category_disable_tooltips"><?php _e( 'Disable tooltips', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input type="hidden" name="cmtt_category_disable_tooltips" value="0"/>
				<input type="checkbox"
				       name="cmtt_category_disable_tooltips"
					<?php checked( true, $disable_tt ); ?>
					   value="1"/>
				<p class="description">
					<?php _e( 'Enable this option if you want to don\'t show tooltips for terms from this category', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field term-name-wrap">
			<th scope="row">
				<label for="cmtt_category_custom_bg_color">
					<?php _e( 'Tooltip background color', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input type="text" name="cmtt_category_custom_bg_color" class="color-picker"
				       value="<?php echo esc_attr( $custom_bg_color ); ?>"/>
				<p class="description">
					<?php _e( 'Set the custom background color for the terms belonging to this category.', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
        <tr class="form-field term-name-wrap">
			<th scope="row">
				<label for="cmtt_category_custom_bg_color">
					<?php _e( 'Tooltip title background color', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input type="text" name="cmtt_category_custom_title_bg_color" class="color-picker"
				       value="<?php echo esc_attr( $custom_title_bg_color ); ?>"/>
				<p class="description">
					<?php _e( 'Set the custom title background color for the terms belonging to this category.', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field term-name-wrap">
			<th scope="row">
				<label for="cmtt_category_custom_t_color">
					<?php _e( 'Tooltip text color', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input type="text" name="cmtt_category_custom_t_color" class="color-picker"
				       value="<?php echo esc_attr( $custom_t_color ); ?>"/>
				<p class="description">
					<?php _e( 'Set the custom text color for the terms belonging to this category.', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field term-name-wrap">
			<th scope="row">
				<label for="cmtt_category_custom_t_width">
					<?php _e( 'Tooltip width', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input type="text" name="cmtt_category_custom_t_width"
				       value="<?php echo esc_attr( $custom_t_width ); ?>"/>
				<p class="description">
					<?php _e( 'Set the custom tooltip width for the terms belonging to this category.', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
        <tr class="form-field term-name-wrap">
			<th scope="row">
				<label for="cmtt_category_custom_t_height">
					<?php _e( 'Tooltip height', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input type="text" name="cmtt_category_custom_t_height"
				       value="<?php echo esc_attr( $custom_t_height ); ?>"/>
				<p class="description">
					<?php _e( 'Set the custom tooltip height for the terms belonging to this category.', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field term-name-wrap">
			<th scope="row">
				<label for="cmtt_category_custom_t_size">
					<?php _e( 'Tooltip text font-size', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input type="text" name="cmtt_category_custom_t_size"
				       value="<?php echo esc_attr( $custom_t_size ); ?>"/>
				<p class="description">
					<?php _e( 'Set the custom font-size for the terms belonging to this category. Number + unit e.g. 12pt, 30px, 2rem etc.', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field term-name-wrap">
			<th scope="row">
				<label for="cmtt_category_custom_reset">
					<?php _e( 'Tooltip reset custom styles to default', 'cm-tooltip-glossary' ); ?></label>
			</th>
			<td>
				<input type="checkbox" name="cmtt_category_custom_reset" id="cmtt_category_custom_reset"
				       value="1"/>
				<p class="description">
					<?php _e( 'Check this checkbox to reset the custom settings to default values (disable customization).', 'cm-tooltip-glossary' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * @param $term_id
	 * @param $tt_id
	 *
	 * @return void
	 */
	public static function save_additional_fields( $term_id, $tt_id ) {
		if ( isset( $_POST['cmtt_category_index_link'] ) ) {
			$link = sanitize_url( $_POST['cmtt_category_index_link'] );
			update_term_meta( $term_id, '_cmtt_category_index_link', $link );
			self::add_category_rewrite_rule( $term_id );
			flush_rewrite_rules( false );
		}
		if ( isset( $_POST['cmtt_category_custom_reset'] ) ) {
			delete_term_meta( $term_id, '_cmtt_category_custom_bg_color' );
			delete_term_meta( $term_id, '_cmtt_category_custom_title_bg_color' );
			delete_term_meta( $term_id, '_cmtt_category_custom_t_color' );
			delete_term_meta( $term_id, '_cmtt_category_custom_t_size' );
			delete_term_meta( $term_id, '_cmtt_category_custom_t_width' );
			delete_term_meta( $term_id, '_cmtt_category_custom_t_height' );

			return;
		}
		if ( isset( $_POST['cmtt_category_disable_tooltips'] ) ) {
			$disable_tt = $_POST['cmtt_category_disable_tooltips'];
			update_term_meta( $term_id, '_cmtt_category_disable_tooltips', $disable_tt );
		}
		if ( isset( $_POST['cmtt_category_custom_bg_color'] ) ) {
			update_term_meta( $term_id, '_cmtt_category_custom_bg_color', sanitize_hex_color( $_POST['cmtt_category_custom_bg_color'] ) );
		}
		if ( isset( $_POST['cmtt_category_custom_title_bg_color'] ) ) {
			update_term_meta( $term_id, '_cmtt_category_custom_title_bg_color', sanitize_hex_color( $_POST['cmtt_category_custom_title_bg_color'] ) );
		}
		if ( isset( $_POST['cmtt_category_custom_t_color'] ) ) {
			update_term_meta( $term_id, '_cmtt_category_custom_t_color', sanitize_hex_color( $_POST['cmtt_category_custom_t_color'] ) );
		}
        if ( isset( $_POST['cmtt_category_custom_t_width'] ) ) {
            $twidth = sanitize_text_field( $_POST['cmtt_category_custom_t_width'] );
            if ( preg_match( '/^[\d]+$/', $twidth ) ) {
                $twidth .= 'px';
            }
            update_term_meta( $term_id, '_cmtt_category_custom_t_width', $twidth );
        }
        if ( isset( $_POST['cmtt_category_custom_t_height'] ) ) {
            $theight = sanitize_text_field( $_POST['cmtt_category_custom_t_height'] );
            if ( preg_match( '/^[\d]+$/', $theight ) ) {
                $theight .= 'px';
            }
            update_term_meta( $term_id, '_cmtt_category_custom_t_height', $theight );
        }
		if ( isset( $_POST['cmtt_category_custom_t_size'] ) ) {
			$tsize = sanitize_text_field( $_POST['cmtt_category_custom_t_size'] );
			if ( preg_match( '/^[\d]+$/', $tsize ) ) {
				$tsize .= 'px';
			}
			update_term_meta( $term_id, '_cmtt_category_custom_t_size', $tsize );
		}
	}

	public static function add_category_rewrite_rule( $link ) {
		if ( ! empty( $link ) ) {
			$parsed_link = parse_url( $link );
            /*
            * @since 4.2.9 - Added check to combat Deprecated: str_replace(): Passing null to parameter #1 ($search) of type array|string is deprecated
            */
            $home_path   = parse_url( home_url(), PHP_URL_PATH ) ?? '';

            $path  = trailingslashit( substr( str_replace( $home_path, '', $parsed_link['path'] ?? '' ), 1 ) );
            $regex = "$path([^/]+)/?$";
			add_rewrite_rule( $regex, 'index.php?glossary=$matches[1]', 'top' );
		}
	}

	/**
	 * Method allows to add the featured image to the tooltip's content
	 *
	 * @param type $glossaryItemContent
	 * @param type $post
	 *
	 * @return type
	 * @noinspection HttpUrlsUsage
	 * @noinspection HttpUrlsUsage
	 */
	public static function cmtt_add_featured_image_to_tooltip_content( $glossaryItemContent, $post, $is_index ) {
		/*
		 ML - get the location of the image from settings
		 */
		$disabledForTerm              = CMTT_Free::_get_meta( '_cmtt_disable_featured_image', $post->ID );
		$featuredImageLocationGeneric = CMTT_Settings::get( 'cmtt_glossary_tooltip_featuredImageDisplay', 'above_content' );
		$featuredImageLocationMeta    = CMTT_Free::_get_meta( '_cmtt_featured_image_position', $post->ID );

		$featuredImageLocation = ! empty( $featuredImageLocationMeta ) ? $featuredImageLocationMeta : $featuredImageLocationGeneric;

		if ( $featuredImageLocation === 'no' || $disabledForTerm ) {
			return $glossaryItemContent;
		}
		/*
		 ML - get the  width of the image from settings
		 */
		$widthOption = CMTT_Settings::get( 'cmtt_glossary_tooltip_imageWidth', '100px' );
		$widthMeta   = $post ? CMTT_Free::_get_meta( '_cmtt_featured_image_size', $post->ID ) : '';
		if ( ! is_array( $widthMeta ) ) {
			$widthMeta = trim( $widthMeta );
		} else {
            $widthMeta = '';
        }
		$imageWidth = ! empty( $widthMeta ) ? $widthMeta : $widthOption;

		$additionalStyle = '';
		switch ( $featuredImageLocation ) {
			case 'left_aligned':
				/*
				 * If left aligned and no width set - set to 100px;
				 */
				$imageWidth      = ( '100%' == $imageWidth || empty( $imageWidth ) ) ? '100px' : $imageWidth;
				$additionalStyle = 'float: left; margin-right: 2%;';
				break;
			case 'right_aligned':
				/*
				 * If right aligned and no width set - set to 100px;
				 */
				$imageWidth      = ( '100%' == $imageWidth || empty( $imageWidth ) ) ? '100px' : $imageWidth;
				$additionalStyle = 'float: right; margin-left: 2%;';
				break;
		}

		/*
		 * Select the thumbnail size based on the options
		 */
		$widthAsNumber = intval( $imageWidth );
		if ( false !== strpos( $imageWidth, '%' ) ) {
			//Max width of the tooltip
			$maxw = (int) CMTT_Settings::get( 'cmtt_tooltipWidthMax', 400 );
			//Calculate the image size using the percentage and the tooltip width
			$widthAsNumber = $maxw * $widthAsNumber / 100;
		}

		$imageSize = array( $widthAsNumber, 1200 );
		$thumbnail = get_the_post_thumbnail(
			$post->ID,
			$imageSize,
			array(
				'class' => 'cmtt-tooltip-featured-image',
				'style' => 'width: ' . $imageWidth . '; margin-top: 2%;' . $additionalStyle,
				'sizes' => '(max-width: 9999px) ' . $imageWidth . ',auto',
			)
		);

		if ( ! empty( $thumbnail ) ) {
			$protocol        = is_ssl() ? 'https://' : 'http://';
			$thumbnail       = str_replace( 'http://', $protocol, $thumbnail );
			$thumbnail_image = str_replace( '"', '\'', $thumbnail );
			/*
			 ML
			 * Change the location of the image inside the tooltip
			 */
			switch ( $featuredImageLocation ) {
				case 'no':
					break;
				case 'above_content':
				case 'left_aligned':
				case 'right_aligned':
				default:
					$glossaryItemContent = $thumbnail_image . $glossaryItemContent;
					break;
				case 'below_content':
					$glossaryItemContent .= $thumbnail_image;
					break;
			}
		}

		return $glossaryItemContent;
	}

	/**
	 * @param $content
	 * @param $term
	 * @param $values
	 *
	 * @return mixed|string
	 */
	public static function displayCategoriesInFootnotes( $content, $term, $values ) {

		$showCategories = CMTT_Settings::get( 'cmtt_footnoteShowCategories', false );
		if ( $showCategories && ! empty( $values['postID'] ) ) {
			$internalContent = self::displayTaxonomyTerms( 'glossary-categories', $values['postID'] );
			$content         = $internalContent . $content;
		}

		return $content;
	}

	/**
	 * Custom taxonomy display added
	 *
	 * @param type $content
	 *
	 * @return type
	 * @global type $wp_query
	 * @global type $post
	 */
	public static function displayTaxonomyTerms( $taxonomySlug = 'glossary-categories', $postID = false ) {
		global $wp_query;

		$taxonomyContentArr = array();

		if ( false === $postID ) {
			$post = $wp_query->post;
			$id   = $post->ID;
		} else {
			$id = $postID;
		}
		$defaultLabel = 'Categories:';

		if ( ! in_array( $taxonomySlug, array( 'glossary-categories', 'glossary-tags' ) ) ) {
			return '';
		}
		switch ( $taxonomySlug ) {
			case 'glossary-categories':
				$taxonomyName = 'cat';
				break;
			case 'glossary-tags':
				$taxonomyName = 'gtags';
				$defaultLabel = 'Tags:';
				break;
			default:
				$taxonomyName = $taxonomySlug;
				break;
		}

		$internalContent = '';
		$glossaryPageUrl = get_permalink( CMTT_Glossary_Index::getGlossaryIndexPageId() );
		$taxonomyTerms   = wp_get_post_terms( $id, $taxonomySlug );
		$label           = CMTT_Settings::get( 'cmtt_term_taxonomy_label_' . $taxonomySlug, $defaultLabel );
		if ( ! empty( $taxonomyTerms ) ) {

			$internalContent .= '<div class="cmtt-taxonomy-single" data-glossary-url="' . $glossaryPageUrl . '">' . apply_filters( 'cmtt_term_taxonomy_label_single_page', __( $label, 'cm-tooltip-glossary' ), $taxonomySlug ) . ' ';
			foreach ( $taxonomyTerms as $taxonomyTerm ) {
				$tagId                = $taxonomyTerm->term_id;
				$taxonomyContentArr[] = '<a data-tagid="' . $tagId . '" data-taxonomy-name="' . $taxonomyName . '" title="' . __( $label, 'cm-tooltip-glossary' ) . $taxonomyTerm->name . '">' . $taxonomyTerm->name . '</a>';
			}
            $separator = CMTT_Settings::get('cmtt_term_separator_taxonomy_' . $taxonomySlug,', ');
            $internalContent .= implode( $separator, $taxonomyContentArr );
			$internalContent .= '</div>';
		}

		return $internalContent;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public static function displayCategoriesOnSinglePage( $content = '' ) {
		/* ML  - check if categories needed to be shown */
		$internalContent = '';
		$showCategories  = CMTT_Settings::get( 'cmtt_term_show_taxonomy_glossary-categories', false );
		if ( $showCategories == 1 ) {
			$internalContent = self::displayTaxonomyOnSinglePage();
		}
		$position = CMTT_Settings::get( 'cmtt_term_position_taxonomy_glossary-categories', 'top' );

		if ( ! is_string( $internalContent ) ) {
			$internalContent = '';
		}

		return ( $position !== 'bottom' ) ? $internalContent . $content : $content . $internalContent;
	}

	/**
	 * Custom taxonomy display added
	 *
	 * @param type $content
	 *
	 * @return type
	 * @global type $wp_query
	 * @global type $post
	 */
	public static function displayTaxonomyOnSinglePage( $taxonomySlug = 'glossary-categories', $postID = false ) {
		global $wp_query;

		if ( false === $postID ) {
			$post = $wp_query->post;
			$id   = $post->ID;
		} else {
			$id = $postID;
		}
		$showTaxonomyGlobal = CMTT_Settings::get( 'cmtt_term_show_taxonomy_' . $taxonomySlug, false );
		$showTaxonomyLocal  = CMTT_Free::_get_meta( 'cmtt_term_show_taxonomy_' . $taxonomySlug, $id );

		/*
		 * The taxonomy is shown when the global setting is different from single setting.
		 * This allows to have an exception in both ways e.g.
		 * - Feature is enabled on all terms but disabled on single page
		 * - Feature is disabled on all terms but enabled on single page
		 */
		$internalContent = '';
		if ( $showTaxonomyGlobal != $showTaxonomyLocal ) {
			$internalContent = self::displayTaxonomyTerms( $taxonomySlug, $id );
		}

		return $internalContent;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public static function displayTagsOnSinglePage( $content = '' ) {
		/* ML - check if tags needed to be shown */
		$internalContent = '';
		$showTags        = CMTT_Settings::get( 'cmtt_term_show_taxonomy_glossary-tags', false );
		if ( $showTags == 1 ) {
			$internalContent = self::displayTaxonomyOnSinglePage( 'glossary-tags' );
		}
		$position = CMTT_Settings::get( 'cmtt_term_position_taxonomy_glossary-tags', 'top' );

		if ( ! is_string( $internalContent ) ) {
			$internalContent = '';
		}

		return ( $position !== 'bottom' ) ? $internalContent . $content : $content . $internalContent;
	}

	/**
	 * @param $result
	 * @param $post
	 * @param $content
	 * @param $force
	 *
	 * @return bool|mixed
	 */
	public static function maybeDisableTooltips( $result, $post, $content, $force ) {
		$frontendTooltipDisableMode = CMTT_Settings::get( 'cmtt_frontendTooltipDisableMode', 'cmtooltip' );
		if ( ( 'parsing' === $frontendTooltipDisableMode ) && self::tooltipsTempDisabled() ) {
			return false;
		}
		/*
		 * @since 4.0.3
		 * If this option is enabled we only enable parsing for posts/pages which has:
		 * a) whitelisted/blacklisted terms
		 * b) whitelisted/blacklisted categories
		 * @since 4.0.5
		 * c) belong to whitelisted/blacklisted category
		 */
		$post_id               = $post ? $post->ID : false;
		$listingTooltipDisable = CMTT_Settings::get( 'cmtt_glossaryOnlyListed', 0 );
		if ( $listingTooltipDisable && false !== $post_id ) {
			$belongsToCustomCats = self::belongsToCustomCats( $post_id );
			$customCats          = self::getCurrentCustomCats( $post_id );
			$customTerms         = self::getCurrentCustomTerms( $post_id );
			$result              = ! empty( $belongsToCustomCats ) || ! empty( $customCats ) || ! empty( $customTerms );
		}

		return $result;
	}

	/**
	 * Function allowing to disable the tooltips if it's in the query.
	 *
	 * @return boolean
	 */
	public static function tooltipsTempDisabled() {
		$disableTooltip        = filter_input( INPUT_GET, 'disable_tooltips' );
		$sessionDisableTooltip = false;

		return ( $disableTooltip || $sessionDisableTooltip );
	}

	/**
	 * @param type $id
	 *
	 * @return array|false
	 * @since 4.0.5
	 * This function should return non-empty value if current post id
	 * belongs to a category, which has blacklisted/whitelisted terms,
	 * if it does, return them
	 */
	public static function belongsToCustomCats( $id ) {
		$terms_regular  = get_the_terms( $id, 'category' );
		$terms_regular  = is_array( $terms_regular ) ? $terms_regular : array();
		$terms_glossary = get_the_terms( $id, 'glossary-categories' );
		$terms_glossary = is_array( $terms_glossary ) ? $terms_glossary : array();
		$terms          = array_merge( $terms_regular, $terms_glossary );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$customCats     = self::getTaxonomyCustomCats( $term->term_id );
				$customCatsType = self::getTaxomomyCustomCatsType( $term->term_id );
				if ( ! empty( $customCats ) ) {
					return array(
						'cats' => $customCats,
						'type' => $customCatsType,
					);
				}
			}
		} else {
			return false;
		}

		return false;
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getCurrentCustomCats( $id ) {
		return get_post_meta( $id, 'glossary_post_page_custom_cats', true );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getCurrentCustomTerms( $id ) {
		return get_post_meta( $id, 'glossary_post_page_custom_terms', true );
	}

	/**
	 * Modifies the Glossary query
	 *
	 * @param type $args
	 * @param type $shortcodeAtts
	 */
	public static function addSearchFilters( $args, $shortcodeAtts ) {
		add_filter( 'posts_groupby', array( __CLASS__, 'addGroupbyFilter' ), 10, 2 );
	}

	/**
	 * Adds the GROUP By statement to glossary queries
	 *
	 * @param type $groupby
	 * @param type $wp_query_object
	 *
	 * @return string
	 */
	public static function addGroupbyFilter( $groupby, $wp_query_object ) {
		global $wpdb;

		return "{$wpdb->posts}.ID";
	}

	/**
	 * Display the listnav od the Term page
	 *
	 * @param type $content
	 *
	 * @return string
	 */
	public static function termAddListnav( $content ) {
		$addListnav = CMTT_Settings::get( 'cmtt_glossaryTermShowListnav' );
		if ( $addListnav ) {
			wp_enqueue_style( 'jquery-listnav-style', self::$cssPath . 'jquery.listnav.css' );
			$atts    = array( 'glossary-container-additional-class' => 'glossary-term-listnav' );
			$listnav = self::glossaryListnavShortcode( $atts );
			$content = $listnav . $content;
		}

		return $content;
	}

	/**
	 * Function displaying the listnav
	 */
	public static function glossaryListnavShortcode( $atts = array() ) {
		extract( shortcode_atts( array(), $atts ) );

		$letters          = (array) CMTT_Settings::get( 'cmtt_index_letters' );
		$letters          = apply_filters( 'cmtt_index_letters', $letters, $atts );
		$includeAll       = (bool) CMTT_Settings::get( 'cmtt_index_includeAll' );
		$includeNum       = (bool) CMTT_Settings::get( 'cmtt_index_includeNum' );
		$round            = (bool) CMTT_Settings::get( 'cmtt_index_showRound' );
		$allLabel         = __( CMTT_Settings::get( 'cmtt_index_allLabel', 'ALL' ), 'cm-tooltip-glossary' );
		$letterSize       = CMTT_Settings::get( 'cmtt_indexLettersSize' );
		$glossaryPageLink = get_permalink( CMTT_Glossary_Index::getGlossaryIndexPageId() );
		$additionalClass  = ! empty( $atts['glossary-container-additional-class'] ) ? $atts['glossary-container-additional-class'] : '';

		$listNavInsideContent = '<div class="glossary-container ' . $letterSize . ' ' . $additionalClass
		                        . '"><div class="ln-letters' . ( $round ? ' round' : '' ) . '">';

		$postCounts = CMTT_Free::getListnavCounts( array(), CMTT_Free::getGlossaryItems( array(), 'index' ) );

		if ( $includeAll ) {
			$postsCount = isset( $postCounts['all'] ) ? $postCounts['all'] : 0;

			$selectedClass        = '';
			$listNavInsideContent .= '<a class="ln-all ln-serv-letter' . $selectedClass . '" href="' . $glossaryPageLink . '" data-letter-count="' . $postsCount . '">' . $allLabel . '</a>';
		}

		if ( $includeNum ) {
			$postsCount           = isset( $postCounts['al-num'] ) ? $postCounts['al-num'] : 0;
			$disabledClass        = $postsCount == 0 ? ' ln-disabled' : '';
			$selectedClass        = '';
			$link                 = add_query_arg( array( 'letter' => 'al-num' ), $glossaryPageLink );
			$listNavInsideContent .= '<a class="ln-_ ln-serv-letter' . $disabledClass . $selectedClass . '" href="' . $link . '">0-9</a>';
		}

		foreach ( $letters as $key => $letter ) {
			$postsCount           = isset( $postCounts[ $letter ] ) ? $postCounts[ $letter ] : 0;
			$isLast               = ( $key == count( $letters ) - 1 );
			$lastClass            = $isLast ? ' ln-last' : '';
			$disabledClass        = $postsCount == 0 ? ' ln-disabled' : '';
			$selectedClass        = '';
			$link                 = add_query_arg( array( 'letter' => $letter ), $glossaryPageLink );
			$listNavInsideContent .= '<a class="lnletter-' . $letter . ' ln-serv-letter' . $lastClass . $disabledClass . $selectedClass
			                         . '" data-letter-count="' . $postsCount
			                         . '" data-letter="' . $letter
			                         . '" href="' . $link . '">'
			                         . mb_strtoupper( str_replace( 'ı', 'İ', $letter ) ) . '</a>';
		}

		if ( CMTT_Settings::get( 'cmtt_index_showResultsCount', '1' ) ) {
			$listNavInsideContent .= '<div class="ln-letter-count" style="position:absolute;top:0;left:88px;width:20px;display:none;"></div>';
		}

		$listNavInsideContent .= '</div></div>';
		CMTT_Glossary_Index::addScriptParams( $atts );

		return $listNavInsideContent;
	}

	/**
	 * Function allowing to disable/enable the tooltips
	 *
	 * @param type $atts
	 */
	public static function glossaryToggleTheme( $atts = array() ) {
		static $id = 0;

		$args = shortcode_atts(
			array(
				'class' => '',
				'label' => '',
			),
			$atts
		);

		$label        = $args['label'];
		$bodyClass    = $args['class'];
		$elementClass = 'cmtt-glossary-theme-toggle';
		$elementId    = 'cmtt-glossary-theme-toggle-' . ( ++ $id );

		return '<a id="' . $elementId . '" class="' . $elementClass . '" data-bodyclass="' . $bodyClass . '">' . $label . '</a>';
	}

	/**
	 * Function serves the shortcode: [glossary_search]
	 * Moved to CMTT_Glossary_Plus from CMTT_Glossary_Index in v.4.0.0
	 *
	 * @param array $atts
	 *
	 * @return string $output
	 */
	public static function glossarySearchShortcode( $atts = array() ) {

		if ( ! is_array( $atts ) ) {
			$atts = array();
		}

		$default_atts = apply_filters(
			'cmtt_glossary_search_shortcode_default_atts',
			array(
				'glossary_page_link' => get_permalink( CMTT_Glossary_Index::getGlossaryIndexPageId() ),
			)
		);

		$shortcode_atts = apply_filters( 'cmtt_glossary_index_atts', array_merge( $default_atts, $atts ) );
		do_action( 'cmtt_glossary_search_shortcode_before', $shortcode_atts );
		$output = self::outputSearch( $shortcode_atts );
		do_action( 'cmtt_glossary_search_shortcode_after', $atts );

		return $output;
	}

	/**
	 * Displays the main glossary index
	 *
	 * @param array $shortcodeAtts
	 *
	 * @return string $content
	 */
	public static function outputSearch( $shortcodeAtts ) {
		global $post;

		$content = '';

		if ( $post === null && $shortcodeAtts['post_id'] ) {
			$post = get_post( $shortcodeAtts['post_id'] );
		}

		$content .= apply_filters( 'cmtt_glossary_search_before_content', '', $shortcodeAtts );
		$content .= '<form method="post" action="' . esc_attr( $shortcodeAtts['glossary_page_link'] ) . '">';

		$additionalClass = ( ! empty( $shortcodeAtts['search_term'] ) ) ? 'search' : '';

		$searchLabel       = __( CMTT_Settings::get( 'cmtt_glossary_SearchLabel', 'Search:' ), 'cm-tooltip-glossary' );
		$searchPlaceholder = __( CMTT_Settings::get( 'cmtt_glossary_SearchPlaceholder', '' ), 'cm-tooltip-glossary' );
		$searchButtonLabel = __( CMTT_Settings::get( 'cmtt_glossary_SearchButtonLabel', 'Search' ), 'cm-tooltip-glossary' );
		$searchTerm        = isset( $shortcodeAtts['search_term'] ) ? $shortcodeAtts['search_term'] : '';
		$searchHelp        = __( CMTT_Settings::get( 'cmtt_glossarySearchHelp', 'The search returns the partial search for the given query from both the term title and description. So it will return the results even if the given query is part of the word in the description.' ), 'cm-tooltip-glossary' );
		ob_start();
		?>
		<?php if ( ! empty( $searchHelp ) ) : ?>
			<div class="cmtt_help glossary-search-helpitem" data-cmtooltip="<?php echo $searchHelp; ?>"></div>
		<?php endif; ?>
		<span class="glossary-search-label"><?php echo $searchLabel; ?></span>
		<input type="search" value="<?php echo esc_attr( $searchTerm ); ?>"
		       placeholder="<?php echo esc_attr( $searchPlaceholder ); ?>"
		       class="glossary-search-term <?php echo esc_attr( $additionalClass ); ?>" name="search_term"
		       id="glossary-search-term"/>
		<button type="submit" id="glossary-search"
		        class="glossary-search button"><?php echo esc_attr( $searchButtonLabel ); ?></button>
		<?php
		$content .= ob_get_clean();
		$content .= '</form>';
		$content = apply_filters( 'cmtt_glossary_search_after_content', $content, $shortcodeAtts );

		do_action( 'cmtt_after_glossary_search' );

		return $content;
	}

	/**
	 * Function allowing to disable/enable the tooltips
	 *
	 * @param array $atts
	 */
	public static function glossaryToggleTooltips( $atts = array() ) {
		$atts = shortcode_atts(
			array(),
			$atts
		);

		$disableTooltip = filter_input( INPUT_GET, 'disable_tooltips' );

		if ( '1' === $disableTooltip ) {
			$tooltipToggleLink = esc_url( add_query_arg( array( 'disable_tooltips' => 0 ), remove_query_arg( 'disable_tooltips' ) ) );
			$label             = __( apply_filters( 'cmtt_tooltip_on_off_widget_enable_text', 'Enable Tooltips' ), 'cm-tooltip-glossary' );
		} else {
			$tooltipToggleLink = esc_url( add_query_arg( array( 'disable_tooltips' => 1 ) ) );
			$label             = __( apply_filters( 'cmtt_tooltip_on_off_widget_disable_text', 'Disable Tooltips' ), 'cm-tooltip-glossary' );
		}

		$labelHtml   = '';
		$widgetLabel = apply_filters( 'cmtt_tooltip_on_off_widget_label', CMTT_Settings::get( 'cmtt_tooltipOnOffWidgetLabel', '' ) );
		if ( ! empty( $label ) ) {
			$labelHtml = '<div class="cmtt-glossary-tooltip-toggle-label-wrapper"><label class="cmtt-glossary-tooltip-toggle-label">' . $widgetLabel . '</label></div>';
		}

		return '<div class="cmtt-glossary-tooltip-toggle-wrapper">' . $labelHtml . '<a href="' . $tooltipToggleLink . '" class="cmtt-glossary-tooltip-toggle">' . $label . '</a></div>';
	}

	/**
	 * Function allowing to disable the tooltips if it's in the query.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function glossaryTempDisableTooltips( $args ) {

		$frontendTooltipDisableMode = CMTT_Settings::get( 'cmtt_frontendTooltipDisableMode', 'cmtooltip' );
		if ( 'cmtooltip' !== $frontendTooltipDisableMode ) {
			return $args;
		}

		if ( self::tooltipsTempDisabled() ) {
			unset( $args['cmtooltip'] );
		}

		return $args;
	}

	/**
	 * Function displaying the single term shortcode
	 * [glossary-term term="term" length="100"]
	 */
	public static function glossaryTermShortcode( $atts ) {
		global $post, $wp_query;

		$atts = shortcode_atts(
			array(
				'term'       => '',
				'run_filter' => '0',
				'length'     => '',
				'show_title' => '1',
			),
			$atts
		);

		if ( empty( $atts['term'] ) ) {
			return false;
		}

		$args = array(
			'post_type'   => 'glossary',
			'post_status' => 'publish',
			'name'        => mb_strtolower( trim( $atts['term'] ) ),
		);

		$query = new WP_Query( $args );
		$posts = $query->get_posts();
		if ( ! empty( $posts ) ) {
			$glossaryItem    = reset( $posts );
			$termTitle       = $glossaryItem->post_title;
			$termDescription = $glossaryItem->post_content;
			if ( ! empty( $atts['length'] ) && is_numeric( $atts['length'] ) ) {
				$termDescription = cminds_truncate( $termDescription, $atts['length'] );
			}
		} else {
			return false;
		}

		if ( $atts['run_filter'] ) {
			$oldPost    = $post;
			$oldWpQuery = $wp_query;

			$post     = $glossaryItem;
			$wp_query = $query;

			$termDescription = apply_filters( 'the_content', $termDescription );

			$post     = $oldPost;
			$wp_query = $oldWpQuery;
		}

		ob_start();
		?>
		<div class="glossary_term">
			<?php if ( $atts['show_title'] ) : ?>
				<div class="glossary_term_title"><?php echo $termTitle; ?></div>
			<?php endif; ?>
			<div class="glossary_term_description"><?php echo $termDescription; ?></div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Whether to enqueue flush rules or not
	 */
	public static function enqueueFlushRules( $enqueeFlushRules, $post ) {
		if ( isset( $post['cmtt_glossaryCategoriesPermalink'] ) && $post['cmtt_glossaryCategoriesPermalink'] !== CMTT_Settings::get( 'cmtt_glossaryCategoriesPermalink' ) ) {
			CMTT_Settings::set( 'cmtt_glossaryCategoriesPermalink', $post['cmtt_glossaryCategoriesPermalink'] );
			$enqueeFlushRules = true;
		}
		if ( isset( $post['cmtt_glossaryTagsPermalink'] ) && $post['cmtt_glossaryTagsPermalink'] !== CMTT_Settings::get( 'cmtt_glossaryTagsPermalink' ) ) {
			CMTT_Settings::set( 'cmtt_glossaryTagsPermalink', $post['cmtt_glossaryTagsPermalink'] );
			$enqueeFlushRules = true;
		}

		return $enqueeFlushRules;
	}

	/**
	 * Add edit menu for tags and categories
	 *
	 * @return void
	 */
	public static function addAdminMenuItems() {
		add_submenu_page( CMTT_MENU_OPTION, 'Categories', 'Categories', 'manage_categories', 'edit-tags.php?taxonomy=glossary-categories&post_type=glossary' );
		add_submenu_page( CMTT_MENU_OPTION, 'Tags', 'Tags', 'manage_categories', 'edit-tags.php?taxonomy=glossary-tags&post_type=glossary' );
	}

	/**
	 * Add the icon for new items
	 *
	 * @param string $postItemTitleContent
	 * @param type $glossary_item
	 *
	 * @return string
	 */
	public static function addNewIconGlossaryIndex( $postItemTitleContent, $glossary_item ) {
		if ( self::isNewGlossaryItem( $glossary_item ) ) {
			$newItemTitle         = apply_filters( 'cmtt_new_item_mark_title', CMTT_Settings::get( 'cmtt_glossaryNewItemMarkTitle', __( 'New!', 'cm-tooltip-glossary' ) ) );
			$postItemTitleContent .= '<label class = "cmtt-post-format-icon cmtt-post-format-new" title = "' . $newItemTitle . '"></label>';
		}

		return $postItemTitleContent;
	}

	/**
	 * Decides whether the glossaryItem can be treated as 'new' or not
	 *
	 * @param type $glossaryItem
	 *
	 * @return boolean
	 */
	public static function isNewGlossaryItem( $glossaryItem ) {
		$maxDaysDiff = CMTT_Settings::get( 'cmtt_glossaryNewItemMaxDays' );
		if ( ! $maxDaysDiff || ! is_object( $glossaryItem ) || empty( $glossaryItem->post_date ) ) {
			return false;
		}

		/*
		 * Required for non-logged in users
		 */
		wp_enqueue_style( 'dashicons' );

		$now      = time();
		$postDate = strtotime( $glossaryItem->post_date );
		$dateDiff = $now - $postDate;

		$daysDiff = floor( $dateDiff / ( 60 * 60 * 24 ) );

		return $daysDiff <= $maxDaysDiff;
	}

	/**
	 * Adds abbreviations to additions array (glossary_item->additions)
	 *
	 * @param $additions
	 * @param type $glossary_item
	 * @param $context
	 * @param $shortcodeAtts
	 *
	 * @return array
	 */
	public static function addAbbreviationsToAdditions( $additions, $glossary_item, $context, $shortcodeAtts ) {
		if ( ! is_array( $additions ) ) {
			$additions = array();
		}

		if ( 'index' === $context ) {
			$addAbbreviationsToTheGlossaryIndex = CMTT_Settings::get( 'cmtt_glossaryAbbreviationsInIndex', 1 );
			$hideAbbreviations                  = ! empty( $shortcodeAtts['hide_abbrevs'] );

			if ( $hideAbbreviations || ! $addAbbreviationsToTheGlossaryIndex ) {
				return $additions;
			}
		}

		$abbreviation = CMTT_Abbreviations::getAbbreviation( $glossary_item->ID );
		if ( ! empty( $abbreviation ) ) {
			$additions = array_merge( $additions, array( CMTT_Free::normalizeTitle( $abbreviation, true, true ) ) );
		}

		return $additions;
	}

	/**
	 * Adds support for terms custom link to all links
	 *
	 * @param type $permalink
	 * @param $post
	 * @param $leavename
	 *
	 * @return type
	 */
	public static function changeTermPermalinks( $permalink, $post, $leavename ) {
		if ( 'glossary' === $post->post_type ) {
			$permalink = self::changeTermPermalink( $permalink, $post->ID );
		}

		return $permalink;
	}

	/**
	 * Adds support for terms custom link
	 *
	 * @param string $permalink
	 * @param $id
	 *
	 * @return string
	 */
	public static function changeTermPermalink( $permalink, $id ) {
		$custom_link = CMTT_Free::_get_meta( '_glossary_custom_link', $id );
		if ( ! empty( $custom_link ) ) {
			$permalink = $custom_link;
		}

		$category_index_link_enabled = CMTT_Settings::get( 'cmtt_category_index_link_enabled', false );
		$category                    = self::get_term_category( $id );
		if ( $category ) {
			$custom_index_url = get_term_meta( $category->term_id, '_cmtt_category_index_link', true );
			if ( ! empty( $custom_index_url ) && filter_var( $custom_index_url, FILTER_VALIDATE_URL ) ) {
				global $wp_post_types;
				$post_type = $wp_post_types['glossary'];
				$permalink = preg_replace( '#^.*' . $post_type->rewrite['slug'] . '/' . '#', trailingslashit( $custom_index_url ), $permalink );
			}
		}

		return $permalink;
	}

	/**
	 * Adds the class for each category term belongs to
	 *
	 * @param type $additionalClass
	 * @param type $glossary_item
	 *
	 * @return type
	 */
	public static function addCategoryClass( $additionalClass, $glossary_item ) {
		$terms = CMTT_Free::_get_term( 'glossary-categories', $glossary_item->ID );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$additionalClass .= ' cmtt_' . $term;
			}
		}

		return $additionalClass;
	}

	/**
	 * Add support for tooltip transparency
	 *
	 * @param type $additionalClass
	 * @param type $glossary_item
	 *
	 * @return string
	 */
	public static function addTermAdditionalClass( $additionalClass, $glossary_item ) {
		$tooltipTransparency = CMTT_Free::_get_meta( '_cmtt_disable_tooltip_background', $glossary_item->ID );
		$additionalClass     .= $tooltipTransparency ? ' transparent' : '';

		return $additionalClass;
	}

	/**
	 * Add Glossary Translate output to Glossary Term tooltip
	 *
	 * @param string $tooltipContent
	 * @param type $glossary_item
	 *
	 * @return string
	 */
	public static function outputGlossaryTermTranslation( $tooltipContent, $glossary_item ) {
		$excludeGoogleApi = CMTT_Free::_get_meta( '_cmtt_exclude_google_api', $glossary_item->ID );

		if ( CMTT_Google_API::enabled() && ! $excludeGoogleApi ) {
			if ( CMTT_Google_API::term() ) {
				$tooltipContent = CMTT_Google_API::translate( $glossary_item->post_title, $glossary_item->post_title );
			} else {
				$translatedContent = CMTT_Google_API::translate( $tooltipContent, $glossary_item->post_title, CMTT_Google_API::COLUMN_CONTENT );
				if ( CMTT_Google_API::together() ) {
					$tooltipContent = $tooltipContent . '<br/><br/>' . $translatedContent;
				}
			}
		}

		return $tooltipContent;
	}

	/**
	 * Add Merriam-Webster output to Glossary Term tooltip
	 *
	 * @param type $tooltipContent
	 * @param type $glossary_item
	 *
	 * @return type
	 */
	public static function addMWToTooltipContent( $tooltipContent, $glossary_item, $onGlossaryIndex = false ) {
		$excludeMerriamDictionaryApi = CMTT_Free::_get_meta( '_cmtt_exclude_merriam_api', $glossary_item->ID );

		if ( CMTT_Mw_API::dictionary_enabled() && CMTT_Mw_API::dictionary_show_in_tooltip() && ! $excludeMerriamDictionaryApi ) {
			$onlyOnEmpty = CMTT_Mw_API::dictionary_only_on_empty_content();
			if ( ( $onlyOnEmpty && empty( $glossary_item->post_content ) ) || ! $onlyOnEmpty ) {
				$tooltipContent .= CMTT_Mw_API::get_dictionary( $glossary_item->post_title, $onGlossaryIndex );
			}
		}

		$excludeMerriamThesaurusApi = CMTT_Free::_get_meta( '_cmtt_exclude_merriam_thesaurus_api', $glossary_item->ID );
		if ( CMTT_Mw_API::thesaurus_enabled() && CMTT_Mw_API::thesaurus_show_in_tooltip() && ! $excludeMerriamThesaurusApi ) {
			$onlyOnEmpty = CMTT_Mw_API::thesaurus_only_on_empty_content();
			if ( ( $onlyOnEmpty && empty( $glossary_item->post_content ) ) || ! $onlyOnEmpty ) {
				$tooltipContent .= CMTT_Mw_API::get_thesaurus( $glossary_item->post_title, $onGlossaryIndex );
			}
		}

		return $tooltipContent;
	}

	/**
	 * Add Glosbe output to Glossary Term tooltip
	 *
	 * @param type $tooltipContent
	 * @param type $glossary_item
	 *
	 * @return type
	 */
	public static function addGlosbeToTooltipContent( $tooltipContent, $glossary_item, $onGlossaryIndex ) {
		$excludeMerriamDictionaryApi = false; // TODO: Add support for exclude
		if ( CMTT_Glosbe_API::dictionary_enabled() && CMTT_Glosbe_API::dictionary_show_in_tooltip() ) {
			$onlyOnEmpty = CMTT_Glosbe_API::dictionary_only_on_empty_content();
			if ( ( $onlyOnEmpty && empty( $glossary_item->post_content ) ) || ! $onlyOnEmpty ) {
				$tooltipContent .= CMTT_Glosbe_API::get_dictionary( $glossary_item->post_title, $onGlossaryIndex, $glossary_item, ! $onGlossaryIndex );
			}
		}

		return $tooltipContent;
	}

	/**
	 * Apply whitelist/blackist of terms to parsing
	 *
	 * @param type $titleIndex
	 * @param type $title
	 *
	 * @return void
	 */
	public static function applyParseCustomTermList( $titleIndex, $title ) {
		global $post;
		static $normalizedCustomSelectedTermsList = null;

		if ( null === $normalizedCustomSelectedTermsList ) {

			$customSelectedTermsList = self::getCurrentCustomTerms( $post->ID );
			if ( ! is_array( $customSelectedTermsList ) ) {
				return;
			}

			function normalizeArrayItems( $title ) {
				global $caseSensitive;

				return CMTT_Free::normalizeTitle( $title, $caseSensitive );
			}

			$normalizedCustomSelectedTermsList = array_map( 'normalizeArrayItems', $customSelectedTermsList );
		}

		$customSelectedTermsListType = self::getCurrentCustomTermsType( $post->ID );

		/*
		 * Whitelist means we only parse for the terms being on the list
		 * Blacklist means we don't parse for the terms being on the list
		 */
		if ( ! empty( $normalizedCustomSelectedTermsList ) && is_array( $normalizedCustomSelectedTermsList ) ) {
			$inArray = in_array( $titleIndex, $normalizedCustomSelectedTermsList );
			if ( ( $customSelectedTermsListType == 'whitelist' && ! $inArray ) || ( $customSelectedTermsListType == 'blacklist' && $inArray ) ) {
				throw new GlossaryTooltipException( $title );
			}
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed|string
	 */
	public static function getCurrentCustomTermsType( $id ) {
		$customTermsType = get_post_meta( $id, 'glossary_post_page_custom_terms_type', true );
		if ( ! in_array( $customTermsType, array( 'blacklist', 'whitelist' ) ) ) {
			$customTermsType = 'whitelist';
		}

		return $customTermsType;
	}

	/**
	 * Apply whitelist/blackist of terms to parsing
	 *
	 * @param type $titleIndex
	 * @param type $title
	 *
	 * @return void
	 *
	 * @global type $post
	 */
	public static function applyCategoryFiltering( $currentItem, $titleIndex, $title ) {
		global $post;

		$category = CMTT_Free::_get_meta( '_cmcrpr_selected_category', $post->ID );

		/*
		 * Whitelist means we only parse for the terms being on the list
		 * Blacklist means we don't parse for the terms being on the list
		 */
		if ( ! empty( $category ) ) {
			$postHasTerm           = false;
			$currentItemCategories = wp_get_post_terms( $currentItem->ID, CMTT_Glossary_Plus::CATEGORY_TAXONOMY );
			foreach ( $currentItemCategories as $taxonomyTerm ) {
				if ( $taxonomyTerm->term_id == $category ) {
					$postHasTerm = true;
					break;
				}
			}

			if ( ! $postHasTerm ) {
				throw new GlossaryTooltipException( $title );
			}
		}
	}

	/**
	 * Exclude negative words
	 *
	 * @param string $titleIndex
	 * @param string $title
	 *
	 * @return void
	 */
	public static function excludeNegativeWords( $currentItem, $titleIndex, $title, $node = null ) {
		$negative_term_class = 'cmtt-negative-term';
		$negative_words      = self::get_negative_words( $currentItem );

		if ( ! empty( $negative_words ) ) {
			$parentNode      = $node ? $node->parentNode : null;
			$parentClass     = null !== $parentNode ? $parentNode->getAttribute( 'class' ) : '';
			$title_sanitized = CMTT_Free::normalizeTitle( $title );
			if ( in_array( $title_sanitized, $negative_words ) || in_array( $titleIndex, $negative_words ) ) {
				throw new GlossaryTooltipException( '<span class=' . $negative_term_class . '>' . $title . '</span>' );
			}
			if ( $negative_term_class === $parentClass && str_contains( $node->wholeText, $title ) ) {
				throw new GlossaryTooltipException( $title );
			}
		}
	}

	/**
	 * @param $glossary_item
	 *
	 * @return array
	 */
	static function get_negative_words( $glossary_item ) {
		$negative_words = array();
		if ( ! empty( $glossary_item ) ) {
			$negative_words_str = CMTT_Free::_get_meta( 'cmtt_negative_words', $glossary_item->ID );

			if(is_array($negative_words_str)){
                $negative_words_str = '';
            }

			if ( ! empty( $negative_words_str ) ) {
                $negative_words_arr = is_array($negative_words_str) ? $negative_words_str : str_getcsv( $negative_words_str );

				foreach ( $negative_words_arr as $word ) {
					if ( ! empty( $word ) ) {
						$negative_words[] = CMTT_Free::normalizeTitle( $word, true, true );
					}
				}
			}
		}

		return $negative_words;
	}

	public static function cleanup_negative_term_markup( $html ) {
		global $cmWrapItUp;

		if ( ! empty( $html ) && is_string( $html ) ) {

			$dom = new DOMDocument();
			/*
			 * loadXml needs properly formatted documents, so it's better to use loadHtml, but it needs a hack to properly handle UTF-8 encoding
			 */
			libxml_use_internal_errors( true );
			if ( ! $dom->loadHTML( '<?xml encoding="UTF-8">' . $html ) ) {
				libxml_clear_errors();
			}
// dirty fix
			foreach ( $dom->childNodes as $item ) {
				if ( $item->nodeType == XML_PI_NODE ) {
					$dom->removeChild( $item ); // remove hack
					break;
				}
			}

			$dom->encoding = 'UTF-8'; // insert proper
			$xpath         = new DOMXPath( $dom );

			$nodeList = $xpath->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' cmtt-negative-term ')]" );
			if ( ! empty( $nodeList ) && $nodeList->length > 0 ) {
				foreach ( $nodeList as $node ) {
					/* @var $node DOMText */
					// Create a document fragment to hold the content to be moved
					$fragment = $dom->createDocumentFragment();
					// Move the children of the node to the fragment
					while ( $node->childNodes->length > 0 ) {
						$fragment->appendChild( $node->childNodes->item( 0 ) );
					}
					// Replace the node with its fragment (which contains the original inner HTML)
					$node->parentNode->replaceChild( $fragment, $node );
				}

				/*
				 *  get only the body tag with its contents, then trim the body tag itself to get only the original content
				 */
				$bodyNode = $xpath->query( '//body' )->item( 0 );
				if ( $bodyNode !== null ) {
					$newDom = new DOMDocument();
					$newDom->appendChild( $newDom->importNode( $bodyNode, true ) );

					$intermalHtml = $newDom->saveHTML();
					$html         = mb_substr( trim( $intermalHtml ), 6, ( mb_strlen( $intermalHtml ) - 14 ) );
					/*
					 * Fixing the self-closing which is lost due to a bug in DOMDocument->saveHtml() (caused a conflict with NextGen)
					 */
					$html = preg_replace( '#(<img[^>]*[^/])>#Ui', '$1/>', $html );
					if ( \CM\CMTT_Settings::get( 'cmtt_convert_to_initial_encoding', 0 ) ) {
						$html = htmlentities( $html, ENT_COMPAT, 'UTF-8' );
					}
				}
			}
		}

		return $html;
	}

	/**
	 * Export additional data
	 *
	 * @param array $exportDataRow , object $term
	 *
	 * @return array
	 */
	public static function addExportDataRowFields( $exportDataRow, $term ) {
		$cats       = array();
		$categories = get_the_terms( $term->ID, 'glossary-categories' );
		if ( ! empty( $categories ) && is_array( $categories ) ) {
			foreach ( $categories as $category ) {
				$cats[] = $category->name;
			}
		}

		/*
		 * cats_lang
		 */
		$cats_lang       = array();
		$categories_lang = get_the_terms( $term->ID, 'glossary-languages' );
		if ( ! empty( $categories_lang ) && is_array( $categories_lang ) ) {
			foreach ( $categories_lang as $category_lang ) {
				$cats_lang[] = $category_lang->name;
			}
		}
		/*
		 * Tags
		 */
		$tags         = array();
		$glossaryTags = get_the_terms( $term->ID, 'glossary-tags' );
		if ( ! empty( $glossaryTags ) && is_array( $glossaryTags ) ) {
			foreach ( $glossaryTags as $tagegory ) {
				$tags[] = $tagegory->name;
			}
		}

		/*
		 * Abbreviation
		 */
		$abbreviation = (string) CMTT_Abbreviations::getAbbreviation( $term->ID );

		$exportDataRow[6]  = ( isset( $cats ) && ! empty( $cats ) ) ? implode( ',', $cats ) : '';
		$exportDataRow[7]  = is_array( $abbreviation ) ? implode( ',', $abbreviation ) : $abbreviation;
		$exportDataRow[8]  = ( isset( $tags ) && ! empty( $tags ) ) ? implode( ',', $tags ) : '';
		$exportDataRow[10] = ( isset( $cats_lang ) && ! empty( $cats_lang ) ) ? implode( ',', $cats_lang ) : '';

		return $exportDataRow;
	}

	/**
	 * Adds additional fields to import
	 */
	public static function importAdditionalInfo( $item, $update ) {
		/*
		 * Categories
		 */
		if ( $update > 0 && ! empty( $item[6] ) ) {
			$categories = explode( ',', $item[6] );
			if ( ! empty( $categories ) && is_array( $categories ) ) {
				$categoriesArr = array();
				foreach ( $categories as $category ) {
					if ( is_numeric( $category ) ) {
						$categoriesArr[] = (int)$category;
					} else {
						$term = get_term_by( 'name', $category, 'glossary-categories' );
						if ( $term ) {
							/*
							 * Add the category
							 */
							$categoriesArr[] = $term->term_id;
						} else {
							/*
							 * Create the category
							 */
							$result = wp_insert_term( $category, 'glossary-categories' );
							if ( ! is_a( $result, 'WP_Error' ) ) {
								$categoriesArr[] = $result['term_id'];
							}
						}
					}
				}
				wp_set_object_terms( $update, $categoriesArr, 'glossary-categories' );
			}
			$categoriesArr = null;
		}

		/*
		 * Categories_lang
		 */

		if ( $update > 0 && ! empty( $item[10] ) ) {
			$categories_lang = explode( ',', $item[10] );
			if ( ! empty( $categories_lang ) && is_array( $categories_lang ) ) {
				$categoriesArr_lang = array();
				foreach ( $categories_lang as $category_lang ) {
					if ( is_numeric( $category_lang ) ) {
						$categoriesArr_lang[] = $category_lang;
					} else {
						$term = get_term_by( 'name', $category_lang, 'glossary-languages' );
						if ( $term ) {
							/*
							 * Add the category_lang
							 */
							$categoriesArr_lang[] = $term->term_id;
						} else {
							/*
							 * Create the category
							 */
							$result = wp_insert_term( $category_lang, 'glossary-languages' );
							if ( ! is_a( $result, 'WP_Error' ) ) {
								$categoriesArr_lang[] = $result['term_id'];
							}
						}
					}
				}
				wp_set_object_terms( $update, $categoriesArr_lang, 'glossary-languages' );
			}
			$categoriesArr_lang = null;
		}

		/*
		 * Abbreviation
		 */
		if ( $update > 0 && ! empty( $item[7] ) ) {
			CMTT_Abbreviations::setAbbreviation( $update, $item[7] );
		}

		/*
		 * Tags
		 */
		if ( $update > 0 && ! empty( $item[8] ) ) {
			$tags = explode( ',', $item[8] );
			if ( ! empty( $tags ) && is_array( $tags ) ) {
				$tagsArr = array();
				foreach ( $tags as $tag ) {
					if ( is_numeric( $tag ) ) {
						$tagsArr[] = $tag;
					} else {
						$term = get_term_by( 'name', $tag, 'glossary-tags' );
						if ( $term ) {
							/*
							 * Add the tag
							 */
							$tagsArr[] = $term->term_id;
						} else {
							/*
							 * Create the tag
							 */
							$result = wp_insert_term( $tag, 'glossary-tags' );
							if ( ! is_a( $result, 'WP_Error' ) ) {
								$tagsArr[] = $result['term_id'];
							}
						}
					}
				}
				wp_set_object_terms( $update, $tagsArr, 'glossary-tags' );
			}
			$tagsArr = null;
		}
	}

	/**
	 * Adds the backlink content
	 *
	 * @param string $backlinkContent
	 *
	 * @return string
	 */
	public static function addBacklinkContent( $backlinkContent, $post ) {
		$onlyOnEmptyDictionary = CMTT_Mw_API::dictionary_only_on_empty_content();
		if ( ( $onlyOnEmptyDictionary && empty( $post->post_content ) ) || ! $onlyOnEmptyDictionary ) {
			$excludeMerriamDictionaryApi = get_post_meta( $post->ID, '_cmtt_exclude_merriam_api', true );
			/*
			 * MW Dictionary
			 */
			$MWdictionary    = ( CMTT_Mw_API::dictionary_enabled() && CMTT_Mw_API::dictionary_show_in_term() && ! $excludeMerriamDictionaryApi ) ? CMTT_Mw_API::get_dictionary( $post->post_title ) : '';
			$backlinkContent .= $MWdictionary;
		}

		$onlyOnEmptyThesaurus = CMTT_Mw_API::thesaurus_only_on_empty_content();
		if ( ( $onlyOnEmptyThesaurus && empty( $post->post_content ) ) || ! $onlyOnEmptyThesaurus ) {
			$excludeMerriamThesaurusApi = get_post_meta( $post->ID, '_cmtt_exclude_merriam_thesaurus_api', true );
			/*
			 * MW Thesaurus
			 */
			$MWthesaurus     = ( CMTT_Mw_API::thesaurus_enabled() && CMTT_Mw_API::thesaurus_show_in_term() && ! $excludeMerriamThesaurusApi ) ? CMTT_Mw_API::get_thesaurus( $post->post_title ) : '';
			$backlinkContent .= $MWthesaurus;
		}

		$onlyOnEmptyGlosbeDictionary = CMTT_Glosbe_API::dictionary_only_on_empty_content();
		if ( ( $onlyOnEmptyGlosbeDictionary && empty( $post->post_content ) ) || ! $onlyOnEmptyGlosbeDictionary ) {
			$excludeGlosbeDictionaryApi = get_post_meta( $post->ID, '_cmtt_exclude_glosbe_dictionary_api', true );
			/*
			 * Glosbe dictionary
			 */
			$Glosbedictionary = ( CMTT_Glosbe_API::dictionary_enabled() && CMTT_Glosbe_API::dictionary_show_in_term() && ! $excludeGlosbeDictionaryApi ) ? CMTT_Glosbe_API::get_dictionary( $post->post_title ) : '';
			$backlinkContent  .= $Glosbedictionary;
		}

		return $backlinkContent;
	}

	/**
	 * Returns the list of posttypes for which we show the "disable metabox"
	 *
	 * @param type $postTypes
	 *
	 * @return type
	 */
	public static function filterDisableMetaboxPosttypes( $postTypes ) {
		if ( CMTT_Settings::get( 'cmtt_disable_metabox_all_post_types' ) ) {
			$postTypes = get_post_types();
		}

		return $postTypes;
	}

	/**
	 * @param $post
	 *
	 * @return void
	 */
	public static function _renderCustomRelatedArticlesMetabox( $post ) {
		// VKost - function to edit custom related pairs
		?>
		<div id='glossary-related-article-header'>
			<span id='glossary-related-article-header-name' style="width: 40%; font-weight: bold">Name => URL</span>
		</div>
		<div id='glossary-related-article-list'>
			<?php
			$customRelatedArticles = get_post_meta( $post->ID, '_glossary_related_article', true );

			if ( ! is_array( $customRelatedArticles ) ) {
				$customRelatedArticles = array();
			}
			if ( ! empty( $customRelatedArticles ) && is_array( $customRelatedArticles ) ) {
				foreach ( $customRelatedArticles as $key => $relatedArticle ) {
					if ( isset( $relatedArticle['name'] ) && isset( $relatedArticle['url'] ) ) {
						echo '<div id="custom-related-article-' . $key . '" class="custom-related-article">';
						echo '<input type="text" name="cmtt_related_article_name[]" style="width: 40%" id="cmtt_related_article_name" value="' . $relatedArticle['name'] . '">';
						echo '<input type="text" name="cmtt_related_article_url[]" style="width: 50%" id="cmtt_related_article_url" value="' . $relatedArticle['url'] . '">';
						echo '<a href="#" class="cmtt_related_article_remove">Remove</a>';
						echo '</div>';
					}
				}
			}

			echo '<div id="custom-related-article-' . count( $customRelatedArticles ) . '" class="custom-related-article">';
			echo '<input type="text" name="cmtt_related_article_name[]" style="width: 40%" id="cmtt_related_article_name" value="" placeholder="Name">';
			echo '<input type="text" name="cmtt_related_article_url[]" style="width: 50%" id="cmtt_related_article_url" value="" placeholder="https://">';
			echo '<a href="#" class="cmtt_related_article_remove">Remove</a>';
			echo '</div>';
			?>
		</div>
		<a id="red-add-more-rows" href="#" onclick="jQuery.fn.add_new_replacement_row(); return false;">Add more
			rows</a>
		<p style="clear: left;">
			<span class="howto">
				<?php _e( 'Insert Name-URL pairs that will be shown before auto-generated Related articles. Both Name and URL must be supplied.', 'cm-tooltip-glossary' ); ?><br/>
				<strong><?php _e( 'Once you finish, save the post/page to store changes', 'cm-tooltip-glossary' ); ?></strong>
			</span>
		</p>
		<?php
	}

	/**
	 * @param $post
	 *
	 * @return void
	 */
	public static function _renderCustomTermLinkMetabox( $post ) {
		$customLink = get_post_meta( $post->ID, '_glossary_custom_link', true );
		if ( empty( $customLink ) ) {
			$customLink = '';
		}
		echo '<input type="text" name="glossary_custom_link" style="width: 100%" id="glossary_custom_link" value="' . $customLink . '">';
		?>
		<p style="clear: left;">
			<span class="howto">
				<?php _e( 'Insert the custom URL of the Glossary Term. This URL will replace the link to the Glossary Term Page on the Glossary Index and whenever the term is highlighted.', 'cm-tooltip-glossary' ); ?><br/>
				<strong><?php _e( 'Once you finish, "Publish" or "Update" to store changes', 'cm-tooltip-glossary' ); ?></strong>
			</span>
		</p>
		<?php
	}

	/**
	 * @param $post
	 *
	 * @return void
	 */
	public static function _renderSelectedTermsForPageMetabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'glossary_customterms_noncename' );
		$currentCustomTerms = is_array( self::getCurrentCustomTerms( $post->ID ) ) ? self::getCurrentCustomTerms( $post->ID ) : array();
		$customTermsList    = implode( ', ', $currentCustomTerms );

		$currentCustomTermsType = self::getCurrentCustomTermsType( $post->ID );
		?>
		Separate custom terms for this post/page by comma e.g. term1,term2,term3<br/>
		<div class="cm-showhide">
			<h5 class="cm-showhide-handle">More info &rArr;</h5>
			<div class="cm-showhide-content">
				<i>
					Remember: if you decide to fill this field, and select the "Whitelist" then for this page only the
					terms which are on this list<strong> and </strong>
					are defined in the Glossary will be highlightened in the content if found. If you choose "Blacklist"
					instead, selected terms will not be highlighted on the page.
				</i>
			</div>
		</div>
		<textarea name="glossary_custom_terms" style='width:100%'><?php echo $customTermsList; ?></textarea>
		<select name="glossary_custom_terms_type">
			<option value="whitelist" <?php selected( 'whitelist', $currentCustomTermsType ); ?> >Whitelist</option>
			<option value="blacklist"<?php selected( 'blacklist', $currentCustomTermsType ); ?> >Blacklist</option>
		</select>
		<?php
	}

	/**
	 * @param $post
	 *
	 * @return void
	 */
	public static function _renderSelectedCatsForPageMetabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'glossary_customcats_noncename' );
		$currentCustomCats = is_array( self::getCurrentCustomCats( $post->ID ) ) ? self::getCurrentCustomCats( $post->ID ) : array( 'all' );

		$currentCustomCatsType = self::getCurrentCustomCatsType( $post->ID );
		?>
		Hold CTRL before clicking to select multiple categories, or to unselect the category.<br/>
		<div class="cm-showhide">
			<h5 class="cm-showhide-handle">More info &rArr;</h5>
			<div class="cm-showhide-content">
				<i>
					Remember: if you decide to fill this field, and select the "Whitelist" then for this page only the
					terms from cats which are on this list<strong> and </strong>
					are defined in the Glossary will be highlightened in the content if found. If you choose "Blacklist"
					instead, terms from selected cats will not be highlighted on the page.
				</i>
			</div>
		</div>
		<?php
		$catSelectOutput = '';
		$cat_args        = array(
			'taxonomy'   => 'glossary-categories',
			'hide_empty' => false,
			'orderby'    => 'name',
			'number'     => 0,
		);
		$cats            = get_terms( $cat_args );

		if ( ! empty( $cats ) ) {
			$catSelectOutput .= '<select name="glossary_custom_cats[]" multiple>';
			foreach ( $cats as $cat ) {
				$selected        = in_array( $cat->term_id, $currentCustomCats ) ? 'selected="selected"' : '';
				$catSelectOutput .= '<option ' . $selected . ' value="' . $cat->term_id . '">' . $cat->name . '</option>';
			}
			$catSelectOutput .= '</select>';
		}
		echo $catSelectOutput;
		?>
		<select name="glossary_custom_cats_type">
			<option value="whitelist" <?php selected( 'whitelist', $currentCustomCatsType ); ?> >Whitelist</option>
			<option value="blacklist"<?php selected( 'blacklist', $currentCustomCatsType ); ?> >Blacklist</option>
		</select>
		<?php
	}

	/**
	 * @param $id
	 *
	 * @return mixed|string
	 */
	public static function getCurrentCustomCatsType( $id ) {
		$customTermsType = get_post_meta( $id, 'glossary_post_page_custom_cats_type', true );
		if ( ! in_array( $customTermsType, array( 'blacklist', 'whitelist' ) ) ) {
			$customTermsType = 'whitelist';
		}

		return $customTermsType;
	}

	/**
	 * @param $post
	 *
	 * @return void
	 */
	public static function _renderNegativeWordsMetabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'negativewords_noncename' );
        $negative_words = get_post_meta( $post->ID, 'cmtt_negative_words', true );
		if(is_array($negative_words)){
            $negative_words = '';
        }
		?>
		<label>
			<textarea name='negative-words' style='width:100%'><?php echo $negative_words; ?></textarea>
			<span class="howto">
				Separate words and phrases by a comma.<br/>If there is a phrase with a comma, wrap the phrase in double-quotes.
			</span>

		</label>
		<?php
	}

	/**
	 * Registers new metaboxes
	 */
	public static function registerMetaboxes() {
		add_meta_box( 'glossary-custom-related-articles', 'Custom Related Articles', array(
			__CLASS__,
			'_renderCustomRelatedArticlesMetabox'
		), 'glossary', 'normal', 'high' );
		add_meta_box( 'glossary-link-box', 'Custom term link', array(
			__CLASS__,
			'_renderCustomTermLinkMetabox'
		), 'glossary', 'normal', 'high' );
		add_meta_box( 'glossary-negative-words', 'Negative Words', array(
			__CLASS__,
			'_renderNegativeWordsMetabox'
		), 'glossary', 'side' );

		$defaultPostTypes         = CMTT_Settings::get( 'cmtt_allowed_terms_metabox_all_post_types' ) ? get_post_types() : array(
			'post',
			'page'
		);
		$allowedTermsBoxPostTypes = apply_filters( 'cmtt_allowed_terms_metabox_posttypes', $defaultPostTypes );
        if(CMTT_Settings::get( 'cmtt_disable_all_metabox_everywhere' )){
            $allowedTermsBoxPostTypes = [];
        }
		foreach ( $allowedTermsBoxPostTypes as $postType ) {
			add_meta_box( 'glossary-selected-terms-box', 'CM Tooltip - Filter Terms', array(
				__CLASS__,
				'_renderSelectedTermsForPageMetabox'
			), $postType, 'side', 'high' );
			add_meta_box( 'glossary-selected-cats-box', 'CM Tooltip - Filter Cats', array(
				__CLASS__,
				'_renderSelectedCatsForPageMetabox'
			), $postType, 'side', 'high' );
		}
	}

	/**
	 * @param $post_id
	 *
	 * @return void
	 */
	public static function cmtt_save_negative_words_for_item( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['negativewords_noncename'] ) || ! wp_verify_nonce( $_POST['negativewords_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$words_string = filter_input( INPUT_POST, 'negative-words' );
		if ( ! is_null( $words_string ) ) {
			update_post_meta( $post_id, 'cmtt_negative_words', $words_string );
		}
	}

	/**
	 * Adds the disables metabox fields
	 *
	 * @param $post
	 *
	 * @return void
	 */
	public static function addDisablesFields( $post ) {
		$drpage                     = get_post_meta( $post->ID, '_glossary_disable_related_terms_for_page', true );
		$disableRelatedTermsForPage = (int) ( ! empty( $drpage ) && $drpage == 1 );

		echo '<div class="cmtt_disables_fields_metabox cmtt-metabox-field">';
		echo '<label class="blocklabel">';
		echo '<input type="checkbox" name="glossary_disable_related_terms_for_page" id="glossary_disable_related_terms_for_page" value="1" ' . ( $disableRelatedTermsForPage != 0 ? ' checked ' : '' ) . '>';
		echo '&nbsp;&nbsp;&nbsp;Overwrite the default related terms setting for this post/page</label>';
		echo '</div>';

		$excludeFromRelated        = get_post_meta( $post->ID, '_glossary_not_include_in_related_articles', true );
		$excludeFromRelatedForPage = (int) ( ! empty( $excludeFromRelated ) && $excludeFromRelated == 1 );

		echo '<div class="cmtt_disables_fields_metabox cmtt-metabox-field">';
		echo '<label class="blocklabel">';
		echo '<input type="checkbox" name="glossary_not_include_in_related_articles" id="glossary_not_include_in_related_articles" value="1" ' . ( $excludeFromRelatedForPage != 0 ? ' checked ' : '' ) . '>';
		echo '&nbsp;&nbsp;&nbsp;Don\'t include this post/page in Related Articles</label>';
		echo '</div>';
	}

	/**
	 * Adds the metabox fields
	 *
	 * @param array $metaboxFields
	 *
	 * @return array
	 */
	public static function addMetaboxFields( $metaboxFields ) {
		$imageWidthOption        = CMTT_Settings::get( 'cmtt_glossary_tooltip_imageWidth', '100px' );
		$mobileSupportGlobal     = CMTT_Settings::get( 'cmtt_glossaryMobileSupport', '0' );
		$mobileSupportGlobalText = $mobileSupportGlobal ? __( 'Enabled', 'cm-tooltip-glossary' ) : __( 'Disabled', 'cm-tooltip-glossary' );

		return array_merge(
			$metaboxFields,
			array(
				'cmtt_exclude_parsing'                   => __( 'Don\'t parse this term', 'cm-tooltip-glossary' ),
				'cmtt_exclude_tooltip'                   => __( 'Hide tooltip for this term', 'cm-tooltip-glossary' ),
				'cmtt_toggle_mobile_support'             => array(
					'label'   => __( 'Toggle mobile support', 'cm-tooltip-glossary' ),
					'options' => array(
						'0' => 'Use global settings (' . $mobileSupportGlobalText . ')',
						'1' => 'Enabled',
						'2' => 'Disabled',
					),
				),
				'cmtt_disable_related_articles_for_term' => __( 'Disable related articles for this term', 'cm-tooltip-glossary' ),
				'cmtt_hide_from_index'                   => __( 'Hide term from Glossary Index', 'cm-tooltip-glossary' ),
				'cmtt_custom_index_letter'               => array(
					'label'       => __( 'Custom Glossary Index letter', 'cm-tooltip-glossary' ),
					'placeholder' => 'Single letter e.g.: x',
				),
				'cmtt_exclude_google_api'                => __( 'Disable Google API for this term', 'cm-tooltip-glossary' ),
				'cmtt_exclude_merriam_api'               => __( 'Disable Merriam-Webster Dictionary API for this term', 'cm-tooltip-glossary' ),
				'cmtt_exclude_merriam_thesaurus_api'     => __( 'Disable Merriam-Webster Thesaurus API for this term', 'cm-tooltip-glossary' ),
				'cmtt_exclude_glosbe_dictionary_api'     => __( 'Disable Glosbe Dictionary API for this term', 'cm-tooltip-glossary' ),
				'cmtt_featured_image_position'           => array(
					'label'   => __( 'Featured Image position', 'cm-tooltip-glossary' ),
					'options' => array(
						''              => '-Default-',
						'no'            => 'No',
						'above_content' => 'Above content',
						'below_content' => 'Below Content',
						'left_aligned'  => 'Left Aligned',
						'right_aligned' => 'Right Aligned',
					),
				),
				'cmtt_featured_image_size'               => array(
					'label'       => __( 'Featured Image width', 'cm-tooltip-glossary' ),
					'placeholder' => ! empty( $imageWidthOption ) ? $imageWidthOption : '100px',
				),
				'cmtt_term_icon'                         => true, // only for save
				'cmtt_term_icon_color'                   => true, // only for save
				'cmtt_term_icon_position'                => true, // only for save
			)
		);
	}

	/**
	 * Saves additional post data
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public static function saveSelectedTermsForPage( $post_id, $post ) {
		/*
		 * Add the call to function saving the selected custom terms for post/page
		 */
		$defaultPostTypes         = CMTT_Settings::get( 'cmtt_allowed_terms_metabox_all_post_types' ) ? get_post_types() : array(
			'post',
			'page'
		);
		$allowedTermsBoxPostTypes = apply_filters( 'cmtt_allowed_terms_metabox_posttypes', $defaultPostTypes );
		if ( isset( $post['post_type'] ) && in_array( $post['post_type'], $allowedTermsBoxPostTypes ) ) {
			self::cmtt_save_selected_terms_for_page( $post_id );
		}
	}

	/**
	 * @param $post_id
	 *
	 * @return void
	 */
	public static function cmtt_save_selected_terms_for_page( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['glossary_customterms_noncename'] ) || ! wp_verify_nonce( $_POST['glossary_customterms_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$customTerms = filter_input( INPUT_POST, 'glossary_custom_terms' );
		if ( is_string( $customTerms ) ) {
			/*
			 * Needed for str_getcsv to work properly
			 */
			$customTerms     = str_replace( '\\"', '"\"', $customTerms );
			$dataCustomTerms = is_array( str_getcsv( $customTerms ) ) ? array_map( 'trim', array_filter( str_getcsv( $customTerms ) ) ) : array();
			update_post_meta( $post_id, 'glossary_post_page_custom_terms', $dataCustomTerms );
		}

		$customTermsType = filter_input( INPUT_POST, 'glossary_custom_terms_type' );
		update_post_meta( $post_id, 'glossary_post_page_custom_terms_type', $customTermsType );

		/*
		 * Categories filter
		 */
		$customCats = filter_input( INPUT_POST, 'glossary_custom_cats', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		update_post_meta( $post_id, 'glossary_post_page_custom_cats', $customCats );

		$customCatsType = filter_input( INPUT_POST, 'glossary_custom_cats_type' );
		update_post_meta( $post_id, 'glossary_post_page_custom_cats_type', $customCatsType );
	}

	/**
	 * Saves additional post data
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public static function saveDisableRelatedPosts( $post_id, $post ) {
		$postType            = isset( $post['post_type'] ) ? $post['post_type'] : '';
		$disableBoxPostTypes = apply_filters( 'cmtt_disable_metabox_posttypes', array( 'glossary', 'post', 'page' ) );
		if ( in_array( $postType, $disableBoxPostTypes ) ) {
			/*
			 * Disables the parsing of the given page
			 */
			$disableRelatedForPage = 0;
			if ( isset( $post['glossary_disable_related_terms_for_page'] ) && $post['glossary_disable_related_terms_for_page'] == 1 ) {
				$disableRelatedForPage = 1;
			}
			update_post_meta( $post_id, '_glossary_disable_related_terms_for_page', $disableRelatedForPage );
			/*
			 * Excludes the page from the Related Articles
			 */
			$excludeFromRelatedArticles = 0;
			if ( isset( $post['glossary_not_include_in_related_articles'] ) && $post['glossary_not_include_in_related_articles'] == 1 ) {
				$excludeFromRelatedArticles = 1;
			}
			update_post_meta( $post_id, '_glossary_not_include_in_related_articles', $excludeFromRelatedArticles );
		}
	}

	/**
	 * Saves additional post data
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public static function flushSingleMWCache( $post_id, $post ) {
		if ( isset( $post['post_type'] ) && 'glossary' === $post['post_type'] && ! empty( $post['cmtt_flush_thirdparty'] ) ) {
			$slug = basename( get_permalink( $post_id ) );
			CMTT_Mw_API::flushTermCache( $slug );
		}
	}

	/**
	 * Saves additional post data
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public static function saveAdditionalPostData( $post_id, $post ) {
		if ( isset( $post['glossary_custom_link'] ) ) {
			update_post_meta( $post_id, '_glossary_custom_link', $post['glossary_custom_link'] );
		}
		/*
		 * Writing custom related articles in post
		 */
		if ( isset( $post['cmtt_related_article_name'] ) && is_array( $post['cmtt_related_article_name'] ) ) {
			$relatedArticleNames = $post['cmtt_related_article_name'];
			$relatedArticles     = array();
			delete_post_meta( $post_id, '_glossary_related_article' );
			foreach ( $relatedArticleNames as $key => $ra_name ) {
				if ( $ra_name != '' && $post['cmtt_related_article_url'][ $key ] != '' ) {
					$relatedArticles[] = array(
						'name' => $ra_name,
						'url'  => $post['cmtt_related_article_url'][ $key ],
					);
				}
			}
			add_post_meta( $post_id, '_glossary_related_article', $relatedArticles );
		}
	}

	/**
	 * Add the "API" tab content
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public static function addAPITabContent( $content ) {
		ob_start();
		?>
		<div class="block">
			<h3 class="section-title">
				<span>API - Google Translate</span>
				<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
				     fill="#6BC07F">
					<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
				</svg>
			</h3>
			<a class="cmtt-documentation-link"
			   href="https://creativeminds.helpscoutdocs.com/article/145-cmtg-api-google-translate"
			   target="_blank">
				Documentation <span class="dashicons dashicons-external"></span>
			</a>
			<table class="floated-form-table form-table">
				<tr>
					<th scope="row">Enabled?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RDGoogleEnabled" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RDGoogleEnabled" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RDGoogleEnabled', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Enabling Google Translate API</td>
				</tr>
				<tr>
					<th scope="row">Display translation and original text together?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RDGoogleTogether" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RDGoogleTogether" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RDGoogleTogether', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Will display both translation and original test in
						tooltip, if not checked will only display translation
					</td>
				</tr>
				<tr>
					<th scope="row">Display translated term name in tooltip?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RDGoogleTerm" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RDGoogleTerm" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RDGoogleTerm', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Will display on tooltip translated term name only
						and will not display term content
					</td>
				</tr>

				<tr>
					<th scope="row">API Key:</th>
					<td><input type="text" name="cmtt_tooltip3RDGoogleApiKey"
					           value="<?php echo CMTT_Settings::get( 'cmtt_tooltip3RDGoogleApiKey', '' ); ?>"/></td>
					<td colspan="2" class="cm_field_help_container">You need <a
							href="https://developers.google.com/translate/v2/pricing" target="_blank">Google API Key</a>
					</td>
				</tr>
				<tr>
					<th scope="row">Source Language:</th>
					<td>
						<select name="cmtt_tooltip3RDGoogleSource">
							<option value="-1">Select language</option>
							<?php foreach ( CMTT_Google_API::getLanguages() as $lang_num => $lang ) : ?>
								<option
									value="<?php echo $lang_num; ?>" <?php selected( $lang_num, CMTT_Settings::get( 'cmtt_tooltip3RDGoogleSource' ) ); ?> ><?php echo $lang['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td colspan="2" class="cm_field_help_container">
						Language of the original text
					</td>
				</tr>
				<tr>
					<th scope="row">Target Language:</th>
					<td>
						<select name="cmtt_tooltip3RDGoogleTarget">
							<option value="-1">Select language</option>
							<?php foreach ( CMTT_Google_API::getLanguages() as $lang_num => $lang ) : ?>
								<option
									value="<?php echo $lang_num; ?>" <?php selected( $lang_num, CMTT_Settings::get( 'cmtt_tooltip3RDGoogleTarget' ) ); ?> ><?php echo $lang['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td colspan="2" class="cm_field_help_container">
						Language the text is translated to
					</td>
				</tr>
				<tr>
					<th scope="row">Test Google API</th>
					<td><input type="button" value="Test Google API" id="cmtt-test-google-api" class="button"/></td>
					<td colspan="2" class="cm_field_help_container">Test the Google API - result will be displayed in a
						popup.
					</td>
				</tr>
			</table>
		</div>
		<div class="block">
			<h3 class="section-title">
				<span>API - Merriam - Webster Dictionary &amp; Thesaurus</span>
				<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
				     fill="#6BC07F">
					<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
				</svg>
			</h3>
			<a class="cmtt-documentation-link"
			   href="https://creativeminds.helpscoutdocs.com/article/144-cmtg-api-merriam-webster"
			   target="_blank">
				Documentation <span class="dashicons dashicons-external"></span>
			</a>
			<table class="floated-form-table form-table">
				<tr>
					<th scope="row">Enable Dictionary?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_MWDictionaryEnabled" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_MWDictionaryEnabled" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_MWDictionaryEnabled', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show definitions from MW Dictionary</td>
				</tr>
				<tr>
					<th scope="row">Only show Dictionary when content is empty?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_MWDictionaryAutoContent" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_MWDictionaryAutoContent" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_MWDictionaryAutoContent', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">If you select this option and the Dictionary is
						enabled then the Dictionary will only be shown when the content of the term is empty.
					</td>
				</tr>
				<tr>
					<th scope="row">Dictionary API Key:</th>
					<td><input size="32" type="text" name="cmtt_tooltip3RD_MWDictionaryApiKey"
					           value="<?php echo CMTT_Settings::get( 'cmtt_tooltip3RD_MWDictionaryApiKey', '' ); ?>"/>
					</td>
					<td colspan="2" class="cm_field_help_container">You need <a
							href="https://dictionaryapi.com/products/index.htm" target="_blank">Merriam-Webster
							Dicitonary API Key</a></td>
				</tr>
				<tr>
					<th scope="row">Show Dictionary data in Tooltip?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_MWDictionaryTooltip" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_MWDictionaryTooltip" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_MWDictionaryTooltip', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show Dictionary in tooltip</td>
				</tr>
				<tr>
					<th scope="row">Show Dictionary data in Glossary term display?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_MWDictionaryTerm" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_MWDictionaryTerm" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_MWDictionaryTerm', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show Dictionary in Glossary term display (will
						remove all other content which currently exist for tooltip)
					</td>
				</tr>
				<tr>
					<th scope="row">Test Dictionary API</th>
					<td><input type="button" value="Test Dictionary API" id="cmtt-test-dictionary-api" class="button"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Test the Thesaurus API - result will be displayed in
						a popup.
					</td>
				</tr>
				<tr>
					<th scope="row">Enable Thesaurus?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_MWThesaurusEnabled" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_MWThesaurusEnabled" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_MWThesaurusEnabled', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show definitions from MW Thesaurus</td>
				</tr>
				<tr>
					<th scope="row">Only show Thesaurus when content is empty?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_MWThesaurusAutoContent" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_MWThesaurusAutoContent" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_MWThesaurusAutoContent', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">If you select this option and the Thesaurus is
						enabled then the Thesaurus output will only be shown when the content of the term is empty.
					</td>
				</tr>
				<tr>
					<th scope="row">Thesaurus API Key:</th>
					<td><input size="32" type="text" name="cmtt_tooltip3RD_MWThesaurusApiKey"
					           value="<?php echo CMTT_Settings::get( 'cmtt_tooltip3RD_MWThesaurusApiKey', '' ); ?>"/>
					</td>
					<td colspan="2" class="cm_field_help_container">You need <a
							href="https://dictionaryapi.com/products/index.htm" target="_blank">Merriam-Webster
							Thesaurus API Key</a></td>
				</tr>
				<tr>
					<th scope="row">Show Thesaurus data in Tooltip?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_MWThesaurusTooltip" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_MWThesaurusTooltip" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_MWThesaurusTooltip', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show Thesaurus in tooltip</td>
				</tr>
				<tr>
					<th scope="row">Show Thesaurus data in Glossary term display?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_MWThesaurusTerm" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_MWThesaurusTerm" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_MWThesaurusTerm', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show Thesaurus in Glossary term display</td>
				</tr>
				<tr>
					<th scope="row">Test Thesaurus API</th>
					<td><input type="button" value="Test Thesaurus API" id="cmtt-test-thesaurus-api" class="button"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Test the Thesaurus API - result will be displayed in
						a popup.
					</td>
				</tr>
				<tr>
					<th scope="row">Flush the Cache?</th>
					<td><input type="submit" name="cmtt_tooltip3RD_MWFlushcache" value="Flush cache" class="button"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Flush the database of the 3rd party definitions.
						<strong>Warning! The glossary will load significantly slower until the cache is filled
							again.</strong></td>
				</tr>
			</table>
		</div>
		<div class="block">
			<h3 class="section-title">
				<span>API - Glosbe Dictionary</span>
				<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
				     fill="#6BC07F">
					<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
				</svg>
			</h3>
			<a class="cmtt-documentation-link"
			   href="https://creativeminds.helpscoutdocs.com/article/1314-cm-tooltip-cmtg-api-glosbe-dictionary"
			   target="_blank">
				Documentation <span class="dashicons dashicons-external"></span>
			</a>
			<table class="floated-form-table form-table">
				<tr>
					<th scope="row">Enable Dictionary?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_GlosbeDictionaryEnabled" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_GlosbeDictionaryEnabled" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_GlosbeDictionaryEnabled', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show definitions from Glosbe Dictionary</td>
				</tr>
				<tr>
					<th scope="row">Only show Dictionary when content is empty?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_GlosbeDictionaryAutoContent" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_GlosbeDictionaryAutoContent" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_GlosbeDictionaryAutoContent', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">If you select this option and the Dictionary is
						enabled then the Dictionary will only be shown when the content of the term is empty.
					</td>
				</tr>
				<tr>
					<th scope="row">Show Dictionary data in Tooltip?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_GlosbeDictionaryTooltip" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_GlosbeDictionaryTooltip" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_GlosbeDictionaryTooltip', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show Dictionary in tooltip</td>
				</tr>
				<tr>
					<th scope="row">Show Dictionary data in Glossary term display?</th>
					<td>
						<input type="hidden" name="cmtt_tooltip3RD_GlosbeDictionaryTerm" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltip3RD_GlosbeDictionaryTerm" <?php checked( true, CMTT_Settings::get( 'cmtt_tooltip3RD_GlosbeDictionaryTerm', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Show Dictionary in Glossary term display (will
						remove all other content which currently exist for tooltip)
					</td>
				</tr>
				<tr>
					<th scope="row">Test Dictionary API</th>
					<td><input type="button" value="Test Dictionary API" id="cmtt_test_glosbe_dictionary_api"
					           class="button"/></td>
					<td colspan="2" class="cm_field_help_container">Test the Thesaurus API - result will be displayed in
						a popup.
					</td>
				</tr>
			</table>
		</div>
		<?php
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Add the "Glossary Replacement" tab content
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public static function addGlossaryReplacementTabContent( $content ) {
		ob_start();
		?>
		<div class="block">
			<h3 class="section-title">
				<span>Replacements Settings</span>
				<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
				     fill="#6BC07F">
					<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
				</svg>
			</h3>
			<a class="cmtt-documentation-link"
			   href="https://creativeminds.helpscoutdocs.com/article/159-cmtg-extras-replacment-tool"
			   target="_blank">
				Documentation <span class="dashicons dashicons-external"></span>
			</a>
			<p>This section of the settings allows you to replace content on your pages.
				You can set the string which should be found and the replaced string that should be placed instead.
				This is only working once page is displayed and does not change the content on the database.
				It is a valuable tool once you want to replace html code or term found in pages with different terms</p>
			<?php
			$repl = CMTT_Settings::get( 'cmtt_glossary_replacements', array() );
			CMTT_Glossary_Replacement::_outputReplacements( $repl, true );
			?>
		</div>
		<?php
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Add the new settings tabs
	 *
	 * @param array $settingsTabs
	 *
	 * @return array
	 */
	public static function addSettingsTabs( $settingsTabs ) {
		$settingsTabs['5'] = 'API';
		$settingsTabs['8'] = 'Glossary Replacement';

		return $settingsTabs;
	}

	/**
	 * Flush the Merriam-Webster cache
	 */
	public static function flushMWCache( $post, $messages ) {
		if ( isset( $post['cmtt_tooltip3RD_MWFlushcache'] ) ) {
			CMTT_Mw_API::flushDatabase();
			$messages = '3rd party definitions cache database has been flushed';
			do_action( 'cmtt_api_flush_cache', $post, $messages );
		}
	}

	/**
	 * Add the new listnav arguments
	 *
	 * @param array $listnavArgs
	 *
	 * @return array
	 */
	public static function addListnavArgs( $listnavArgs ) {
		$listnavArgs['nonLatinSeparate'] = CMTT_Settings::get( 'cmtt_index_nonLatinLetters' );

		return $listnavArgs;
	}

	/**
	 * Outputs the top filter
	 *
	 * @param string $content
	 * @param array $shortcodeAtts
	 * @param array $glossary_query
	 *
	 * @return string
	 */
	public static function outputBeforeListnav( $content, $shortcodeAtts, $glossary_query ) {
		$clearLink         = '';
		$additionalClass   = ( ! empty( $shortcodeAtts['search_term'] ) ) ? 'search' : '';
		$showSearchButton  = ( ! empty( $shortcodeAtts['show_search'] ) ) ? $shortcodeAtts['show_search'] : 0;
		$exactSearch       = ( ! empty( $shortcodeAtts['exact_search'] ) ) ? $shortcodeAtts['exact_search'] : 0;
		$searchLabel       = __( CMTT_Settings::get( 'cmtt_glossary_SearchLabel', 'Search:' ), 'cm-tooltip-glossary' );
		$searchPlaceholder = __( CMTT_Settings::get( 'cmtt_glossary_SearchPlaceholder', '' ), 'cm-tooltip-glossary' );
		$searchButtonLabel = __( CMTT_Settings::get( 'cmtt_glossary_SearchButtonLabel', 'Search' ), 'cm-tooltip-glossary' );
		$clearLabel        = __( CMTT_Settings::get( 'cmtt_glossary_ClearLabel', '(clear)' ), 'cm-tooltip-glossary' );
		$searchTerm        = isset( $shortcodeAtts['search_term'] ) ? $shortcodeAtts['search_term'] : '';
		$cb_search         = isset( $shortcodeAtts['cb_search'] ) ? $shortcodeAtts['cb_search'] : '';

		ob_start();
		?>
        <div class="progress-indicator" style="display:none">
            <?php if ( !\CM\CMTT_Settings::get('cmtt_disableLoaderAnimation', 0) ) : ?>
                <img src="<?php echo self::$cssPath; ?>images/ajax-loader.gif" alt="AJAX progress indicator"/>
            <?php endif; ?>
        </div>
		<div class="glossary_top_filter">
			<div class="left">
				<?php
				if ( $showSearchButton ) :
					ob_start();
					$searchHelp = __( CMTT_Settings::get( 'cmtt_glossarySearchHelp', 'The search returns the partial search for the given query from both the term title and description. So it will return the results even if the given query is part of the word in the description.' ), 'cm-tooltip-glossary' );
					if ( ! empty( $searchHelp ) ) : ?>
						<div class="cmtt_help glossary-search-helpitem"
						     data-cmtooltip="<?php echo $searchHelp; ?>"></div>
					<?php endif; ?>
					<span class="glossary-search-label"><?php echo $searchLabel; ?></span>
					<div class="glossary-search-wrapper">
						<?php
						$check_options         = CMTT_Settings::get( 'cmtt_glossarySearchFromOptions', array() );
						$allow_frontend_select = CMTT_Settings::get( 'cmtt_glossarySearchFromOptionsFrontend', 0 );
						if ( ! is_array( $check_options ) ) {
							$check_options = array();
						}
						if ( $allow_frontend_select && in_array( '0', $check_options ) && in_array( '1', $check_options ) ) :
							?>
							<select name="cb_search" id="cb_search"
							        style="float: left; padding: 13px; margin: 0 5px 5px 0; background-color: white; border-radius: 10px; border: 1px solid;">
								<option value="title" <?php selected( 'title', $cb_search ); ?> >Title</option>
								<option value="description" <?php selected( 'description', $cb_search ); ?>>
									Description
								</option>
							</select>
						<?php
						endif;
						?>
						<input value="<?php echo esc_attr( $searchTerm ); ?>"
						       placeholder="<?php echo esc_attr( $searchPlaceholder ); ?>"
						       class="glossary-search-term <?php echo $additionalClass; ?>" name="glossary-search-term"
						       id="glossary-search-term"
                               aria-label="<?php echo esc_attr($searchButtonLabel); ?>"/>
						<button type="submit" id="glossary-search"
						        class="glossary-search button"><?php echo esc_attr( $searchButtonLabel ); ?></button>
					</div>
					<a class="glossary-search-clear" title="<?php _e( 'Clear the input', 'cm-tooltip-glossary' ); ?>"
					   href="<?php echo $clearLink; ?>">
						<?php echo $clearLabel; ?>
					</a>
					<?php
					$search_html = ob_get_clean();
					echo apply_filters( 'cmtt_glossary_index_search_html', $search_html, $shortcodeAtts );
				endif;
				echo self::outputCategories( $shortcodeAtts, $glossary_query ); ?>
			</div>
			<?php echo apply_filters( 'cmtt_glossary_index_additional_filters_html', '', $shortcodeAtts ); ?>
		</div>

		<input type="hidden" class="cmtt-attribute-field" name="disable_listnav"
		       value="<?php echo (int) ( isset( $shortcodeAtts['disable_listnav'] ) && $shortcodeAtts['disable_listnav'] ); ?>"/>
		<input type="hidden" class="cmtt-attribute-field" name="exact_search"
		       value="<?php echo (int) $exactSearch; ?>"/>
		<input type="hidden" class="cmtt-attribute-field" name="show_search"
		       value="<?php echo (int) $showSearchButton; ?>"/>
		<input type="hidden" class="glossary-hide-terms" name="glossary-hide-terms"
		       value="<?php echo (int) ( isset( $shortcodeAtts['hide_terms'] ) && $shortcodeAtts['hide_terms'] ); ?>"/>
		<input type="hidden" class="glossary-hide-categories" name="glossary-hide-categories"
		       value="<?php echo (int) ( isset( $shortcodeAtts['hide_categories'] ) && $shortcodeAtts['hide_categories'] ); ?>"/>
		<input type="hidden" class="glossary-hide-abbrevs" name="glossary-hide-abbrevs"
		       value="<?php echo (int) ( isset( $shortcodeAtts['hide_abbrevs'] ) && $shortcodeAtts['hide_abbrevs'] ); ?>"/>
		<input type="hidden" class="glossary-hide-synonyms" name="glossary-hide-synonyms"
		       value="<?php echo (int) ( isset( $shortcodeAtts['hide_synonyms'] ) && $shortcodeAtts['hide_synonyms'] ); ?>"/>
		<input type="hidden" class="glossary-perpage" name="glossary-perpage"
		       value="<?php echo (int) ( isset( $shortcodeAtts['perpage'] ) ? $shortcodeAtts['perpage'] : CMTT_Settings::get( 'cmtt_perPage' ) ); ?>"/>
		<input type="hidden" name="tooltip_language"
		       value="<?php echo isset( $shortcodeAtts['tooltip_language'] ) ? $shortcodeAtts['tooltip_language'] : ''; ?>"/>
		<input type="hidden" name="language_dropdown"
		       value="<?php echo isset( $shortcodeAtts['language_dropdown'] ) ? $shortcodeAtts['language_dropdown'] : ''; ?>"/>
		<input type="hidden" name="languages_for_table"
		       value="<?php echo isset( $shortcodeAtts['languages_for_table'] ) ? $shortcodeAtts['languages_for_table'] : ''; ?>"/>
		<?php
		$content .= ob_get_clean();

		return apply_filters( 'cmtt_before_listnav', $content );
	}

	/**
	 * Function outputs the categories control
	 *
	 * @param $shortcodeAtts
	 * @param $glossary_query
	 *
	 * @return string
	 */
	public static function outputCategories( $shortcodeAtts, $glossary_query ) {
		$catSelectOutput               = '';
		$selectAdditionalAttr          = '';
		$currentCategory               = ( ! empty( $shortcodeAtts['cat'] ) ) ? $shortcodeAtts['cat'] : array( 'all' );
		$glossaryCategoriesAllDisabled = CMTT_Settings::get( 'cmtt_glossary_disableAllCats', '0' );

		if ( empty( $currentCategory ) ) {
			$currentCategory = array( 'all' );
		}
		if ( ! is_array( $currentCategory ) ) {
			$currentCategory = explode( ',', $currentCategory );
		}
		if ( is_array( $currentCategory ) ) {
			$currentCategoryString = implode( ',', $currentCategory );
		} else {
			$currentCategoryString = '';
		}

		if ( ! empty( $shortcodeAtts['freeze_cat'] ) && $currentCategory !== array( 'all' ) && ! empty( $shortcodeAtts['hide_categories'] ) ) {
			$catSelectOutput .= '<input type="hidden" class="glossary-categories" name="cat" value="' . $currentCategoryString . '">';
			$catSelectOutput .= '<input type="hidden" class="glossary-freeze-categories" name="freeze_cat" value="1">';
		} else {

			if ( ! empty( $shortcodeAtts['hide_categories'] ) ) {
				/*
				 * @since 4.0.13 - allow to disable the display of categories completely
				 */
				$catSelectOutput .= '';
			} else {
				/*
				* Code allows to display only the relevant categories meaning that only the categories of the currently displayed items will be displayed.
				*/
				$showOnlyRelevant = isset( $shortcodeAtts['only_relevant_cats'] ) && $shortcodeAtts['only_relevant_cats'];
				if ( $showOnlyRelevant ) {
					$args      = isset( CMTT_Free::$lastQueryDetails['nopaging_args'] ) ? CMTT_Free::$lastQueryDetails['nopaging_args'] : array();
					$posts_ids = array();

					if ( ! empty( $args ) && is_array( $args ) ) {
                        $args['return_just_ids'] = 1;
                        $posts_ids = CMTT_Free::getGlossaryItems( $args );
						$cats = wp_get_object_terms(
							$posts_ids,
							'glossary-categories',
							array(
								'hide_empty' => 1,
								'orderby'    => 'name',
								'order'      => 'ASC',
							)
						);
					}
				} else {
					// Getting Glossary categories
					$cats = get_terms(
						'glossary-categories',
						array(
							'hide_empty' => false,
						)
					);
				}

				$glossaryCategoriesDisplayMethod = CMTT_Settings::get( 'cmtt_glossaryCategoriesDisplayType', '1' );
				$allCategoriesLabel              = __( CMTT_Settings::get( 'cmtt_glossary_AllCategoriesLabel', 'All categories' ), 'cm-tooltip-glossary' );
				if ( cminds_is_amp_endpoint() ) {
					$glossaryCategoriesDisplayMethod = '0';
					$selectAdditionalAttr            = 'name="cat" on="change:cmttForm.submit"';
				}

				if ( ! empty( $cats ) ) {
					switch ( $glossaryCategoriesDisplayMethod ) {
						case '0':
						{
							if ( $showOnlyRelevant ) {
								if ( ! empty( CMTT_Settings::get( 'cmtt_glossaryCategoriesLabelForDropdown' ) ) ) {
									$catSelectOutput .= '<div class="glossary-category-wrapper"><label>' . CMTT_Settings::get( 'cmtt_glossaryCategoriesLabelForDropdown' ) . '</label>';
								}
								$catSelectOutput .= '<select id="glossary-categories" class="glossary-categories" ' . $selectAdditionalAttr . '>';
								if ( ! $glossaryCategoriesAllDisabled ) {
									$catSelectOutput .= '<option value="all">' . $allCategoriesLabel . '</option>';
								}
								foreach ( $cats as $cat ) {
									$selected        = is_array( $currentCategory ) && in_array( $cat->term_id, $currentCategory ) ? 'selected="selected"' : '';
									$catSelectOutput .= '<option ' . $selected . ' value="' . $cat->term_id . '">' . $cat->name . '</option>';
								}
								$catSelectOutput .= '</select>';
								if ( ! empty( CMTT_Settings::get( 'cmtt_glossaryCategoriesLabelForDropdown' ) ) ) {
									$catSelectOutput .= '</div>';
								}
							} else {
								$selected        = isset( $currentCategory[0] ) ? $currentCategory[0] : 'all';
								$cat_args        = array(
									'orderby'           => 'name',
									'taxonomy'          => 'glossary-categories',
									'show_count'        => 0,
									'hierarchical'      => 1,
									'hide_empty'        => false,
									'show_option_none'  => $allCategoriesLabel,
									'option_none_value' => 'all',
									'id'                => 'glossary-categories',
									'class'             => 'glossary-categories',
									'selected'          => $selected,
									'echo'              => 0,
								);
								$catSelectOutput = wp_dropdown_categories( $cat_args );
							}
							break;
						}
						default:
						case '1':
						{
							$categoriesLabel = __( CMTT_Settings::get( 'cmtt_glossaryCategoriesLabel' ), 'cm-tooltip-glossary' );
							$allClass        = ( $currentCategoryString == 'all' ) ? 'selected' : '';

							$catSelectOutput .= '<input type="hidden" class="glossary-categories" name="cat" value="' . $currentCategoryString . '">';
							$catSelectOutput .= '<div class="cmtt-categories-filter">' . $categoriesLabel;
							$catSelectOutput .= ' <a class="cmtt-glossary-category ' . $allClass . '">' . str_replace( ' ', '&nbsp;', $allCategoriesLabel ) . '</a>';

                            if ( !$glossaryCategoriesAllDisabled ) {
                                $catSelectOutput .= ' <a class="cmtt-glossary-category ' . $allClass . '">' . str_replace(' ', '&nbsp;', $allCategoriesLabel) . '</a>';
                            }
                            $currentCategory = $glossaryCategoriesAllDisabled && $currentCategoryString == 'all' ? [$cats[0]->term_id ]: $currentCategory;

                            foreach ( $cats as $cat ) {
								$isCurrentCategory = is_array( $currentCategory ) && in_array( $cat->term_id, $currentCategory );
								$selectedCateClass = $isCurrentCategory ? 'selected' : '';
								$catSelectOutput   .= ' <a class="cmtt-glossary-category ' . $selectedCateClass . '" data-category-name="' . $cat->term_id . '">' . str_replace( ' ', '&nbsp;', $cat->name ) . '</a>';
							}
							$catSelectOutput .= '</div>';

							break;
						}
					}
				}
			}
		}

		return $catSelectOutput;
	}

	/**
	 * Outputs only the Google Translate term in Glossary Index page
	 *
	 * @param type $glossaryItemContent
	 * @param type $glossaryItem
	 *
	 * @return type
	 */
	public static function outputGlossaryIndexGoogleTranslation( $glossaryItemContent, $glossaryItem ) {
		if ( CMTT_Google_API::enabled() ) {
			$glossaryItemContentTranslated = CMTT_Google_API::translate( $glossaryItemContent, $glossaryItem->post_title, CMTT_Google_API::COLUMN_CONTENT, true );
			if ( CMTT_Google_API::together() ) {
				$glossaryItemContent = $glossaryItemContent . '<br/><br/>' . $glossaryItemContentTranslated;
			}
		}

		return $glossaryItemContent;
	}

	/**
	 * Outputs only the Google Translate term in Glossary Index page
	 *
	 * @param type $glossaryItemContent
	 * @param type $glossaryItem
	 *
	 * @return type
	 */
	public static function outputGlossaryIndexGoogleTermOnly( $glossaryItemContent, $glossaryItem ) {
		if ( CMTT_Google_API::enabled() && CMTT_Google_API::term() ) {
			$glossaryItemContent = CMTT_Google_API::translate( $glossaryItem->post_title, $glossaryItem->post_title, CMTT_Google_API::COLUMN_TITLE, true );
			remove_filter( 'cmtt_glossary_index_tooltip_content', array(
				'CMTT_Glossary_Index',
				'getTheTooltipContentBase'
			), 10 );
			remove_filter( 'cmtt_glossary_index_tooltip_content', array(
				'CMTT_Free',
				'cmtt_glossary_parse_strip_shortcodes'
			), 20 );
			remove_filter( 'cmtt_glossary_index_tooltip_content', array(
				'CMTT_Free',
				'cmtt_glossary_filterTooltipContent'
			), 30 );
		}

		return $glossaryItemContent;
	}

	/**
	 * Function strips the shortcodes if the option is set
	 *
	 * @param $glossaryItemDesc
	 * @param $glossary_item
	 * @param $glossaryIndexStyle
	 * @param $shortcodeAtts
	 *
	 * @return type
	 */
	public static function stripDescriptionShortcode( $glossaryItemDesc, $glossary_item, $glossaryIndexStyle, $shortcodeAtts ) {
		if ( CMTT_Settings::get( 'cmtt_glossaryIndexDescStripShortcode' ) == 1 ) {
			$glossaryItemDesc = strip_shortcodes( $glossaryItemDesc );
		}

		return $glossaryItemDesc;
	}

	/**
	 * Outputs the glossary item description
	 *
	 * @param string $glossaryItemDesc
	 * @param object $glossary_item
	 * @param string $glossaryIndexStyle
	 *
	 * @return string
	 */
	public static function outputGlossaryIndexItemDesc( $glossaryItemDesc, $glossary_item, $glossaryIndexStyle, $shortcodeAtts ) {
		$styles_with_desc = array(
			'modern-table',
			'classic-definition',
			'expand-style',
			'expand2-style',
			'classic-excerpt',
			'term-definition',
			'img-term-definition',
			'term-carousel',
			'tiles-with-definition',
			'flipboxes-with-definition',
			'accordion-view',
			'language-table',
             'cards-view',
		);

		if ( ! empty( $shortcodeAtts['no_desc'] ) || ! in_array( $glossaryIndexStyle, $styles_with_desc ) ) {
			remove_all_filters( 'cmtt_glossary_index_item_desc' );

			return '';
		}

		$descOrExcerpt = (int) CMTT_Settings::get( 'cmtt_glossaryShowExcerpt', 0 );

		if ( $descOrExcerpt || in_array( $glossaryIndexStyle, array( 'classic-excerpt' ) ) ) {
			$glossaryItemDesc = $glossary_item->post_excerpt;
		} else {
			$glossaryItemDesc = do_blocks( $glossary_item->post_content );

			if ( class_exists( 'Themify_Builder' ) ) {
				$themify_json = CMTT_Free::_get_meta( '_themify_builder_settings_json', $glossary_item->ID );
				if ( ! empty( $themify_json ) ) {
					global $ThemifyBuilder;
					$builder_data     = $ThemifyBuilder->get_builder_output( $glossary_item->ID, $glossary_item->post_content );
					$glossaryItemDesc .= $builder_data;
				}
			}
		}
		if ( empty( $glossaryItemDesc ) ) {
			$glossaryItemDesc = '&nbsp;';
		} else {
			$stripTags = (int) CMTT_Settings::get( 'cmtt_glossaryTooltipDescStripTags', 1 );
			if ( $stripTags ) {
				$glossaryItemDesc = strip_tags( $glossaryItemDesc );
			}
			$glossaryDescLength = (int) CMTT_Settings::get( 'cmtt_glossaryTooltipDescLength' );
			$permalink          = get_permalink( $glossary_item->ID );
			if ( $glossaryDescLength && $glossaryDescLength >= 30 ) {
				$endingHtml = CMTT_Settings::get( 'cmtt_glossaryIndexTruncateLabel', __( '(...)' ) );

				$lengthBeforeTruncate = strlen( $glossaryItemDesc );
				$glossaryItemDesc     = cminds_truncate( html_entity_decode( $glossaryItemDesc ), $glossaryDescLength, $endingHtml );
				$lengthAfterTruncate  = strlen( $glossaryItemDesc );

				$showReadMoreLink      = (int) CMTT_Settings::get( 'cmtt_glossaryIndexDescReadMore', 0 );
				$showReadMoreLinkLabel = CMTT_Settings::get( 'cmtt_glossaryIndexDescReadMoreLabel', __( 'Read More' ) );
                if(strpos($showReadMoreLinkLabel,'{term_title}') !== false){
                    $showReadMoreLinkLabel = str_replace('{term_title}',$glossary_item->post_title,$showReadMoreLinkLabel);
                }
				$styleAttr             = ' style="' . apply_filters( 'cmtt_term_style_attribute', '', $glossary_item ) . '" ';
				$glossaryItemDesc      = apply_filters( 'cmtt_item_description_before_read_more_link', $glossaryItemDesc, $glossary_item );

				if ( $showReadMoreLink == 1 ) {
					$linkHtml         = ' <a class="glossary-read-more-link" href="' . $permalink . '" ' . $styleAttr . '>' . $showReadMoreLinkLabel . '</a>';
					$glossaryItemDesc .= $linkHtml;
				} elseif ( $showReadMoreLink == 2 && $lengthBeforeTruncate > $lengthAfterTruncate ) {
					$linkHtml         = ' <a class="glossary-read-more-link" href="' . $permalink . '" ' . $styleAttr . '>' . $showReadMoreLinkLabel . '</a>';
					$glossaryItemDesc .= $linkHtml;
				}
			}
		}


		return $glossaryItemDesc;
	}

	/**
	 * Adds the Related Items to the glossary description
	 *
	 * @param type $glossaryItemDesc
	 * @param type $glossary_item
	 * @param type $glossaryIndexStyle
	 *
	 * @return type
	 */
	public static function addGlossaryIndexDescRelated( $glossaryItemDesc, $glossary_item, $glossaryIndexStyle, $shortcodeAtts ) {
		if ( isset( $shortcodeAtts['related'] ) && $shortcodeAtts['related'] && in_array( $glossaryIndexStyle, array(
				'modern-table',
				'classic-definition',
				'classic-excerpt'
			) ) ) {
			$relatedArticlesGlossaryCount = CMTT_Settings::get( 'cmtt_glossary_showRelatedArticlesGlossaryCount' );
			$relatedArticlesCount         = min( CMTT_Settings::get( 'cmtt_glossary_showRelatedArticlesCount' ), $shortcodeAtts['related'] );

			$relatedSnippet = CMTT_Related::renderRelatedArticles( $glossary_item->ID, $relatedArticlesCount, $relatedArticlesGlossaryCount );

			if ( ! empty( $relatedSnippet ) ) {
				$glossaryItemDesc .= $relatedSnippet;
			}
		}

		return $glossaryItemDesc;
	}

	/**
	 * Outputs the glossary item description
	 *
	 * @param type $glossaryItemDesc
	 * @param type $glossary_item
	 * @param type $glossaryIndexStyle
	 *
	 * @return type
	 */
	public static function outputExpandStyleWrapper( $glossaryItemDesc, $glossary_item, $glossaryIndexStyle, $shortcodeAtts ) {
		if ( empty( $glossaryItemDesc ) ) {
			return $glossaryItemDesc;
		}

		if ( in_array( $glossaryIndexStyle, array( 'expand-style' ) ) ) {
			$label            = apply_filters( 'cmtt_tooltip_back_to_top_label', CMTT_Settings::get( 'cmtt_tooltipExpandBackToTopLabel', 'Back to Top' ) );
			$glossaryItemDesc .= '<div class="expand-back-to-top"><a href="#top" style="box-shadow: none;">' . $label . '</a></div>';
			$glossaryItemDesc .= '<p class="expand-space"></p>';
		}

		return $glossaryItemDesc;
	}

	/**
	 * Adds new display styles
	 *
	 * @param array $styles
	 *
	 * @return array
	 */
	public static function addGlossaryIndexStyles( $styles ) {
		$styles['big-tiles']                 = 'tiles big';
		$styles['classic-table']             = 'table classic';
		$styles['modern-table']              = 'table modern';
		$styles['sidebar-termpage']          = 'sidebar-termpage';
		$styles['expand-style']              = 'expand';
		$styles['expand2-style']             = 'expand2';
		$styles['grid-style']                = 'grid';
		$styles['cube-style']                = 'cube';
		$styles['image-tiles-view']          = 'image-tiles';
		$styles['classic-excerpt']           = 'classic-excerpt';
		$styles['classic-definition']        = 'classic-definition';
		$styles['term-definition']           = 'term-definition';
		$styles['img-term-definition']       = 'img-term-definition';
		$styles['term-carousel']             = 'term-carousel';
		$styles['tiles-with-definition']     = 'tiles-with-definition';
		$styles['flipboxes-with-definition'] = 'flipboxes-with-definition';
		$styles['accordion-view']			 = 'accordion-view';
        $styles['cards-view']                = 'cards-view';

		return $styles;
	}

	/**
	 * Adds the server-side pagination filter and query modification args
	 *
	 * @param type $args
	 * @param type $shortcodeAtts
	 *
	 * @return type
	 */
	public static function addServerSidePaginationFilter( $args, $shortcodeAtts ) {
		if ( CMTT_Glossary_Index::isServerSide() ) {
			$initLetter              = CMTT_Settings::get( 'cmtt_index_initLetter', '' );
			$nonLatinLetters         = (bool) CMTT_Settings::get( 'cmtt_index_nonLatinLetters' );
			$currentlySelectedLetter = ( ! empty( $shortcodeAtts['letter'] ) ) ? $shortcodeAtts['letter'] : ( ! empty( $initLetter ) ? $initLetter : 'all' );
			$currentlySelectedLetter = ( ! empty( $shortcodeAtts['search_term'] ) && CMTT_Settings::get( 'cmtt_glossary_ignore_letter_on_search', 0 ) ) ? 'all' : $currentlySelectedLetter;

			if ( $currentlySelectedLetter !== 'all' ) {
				$args['first_letter']     = $currentlySelectedLetter;
				$args['nonlatin_letters'] = $nonLatinLetters;
			}
		}

		return $args;
	}

	/**
	 * Adds the default parameters for Glossary Index shortcode
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	public static function changeGlossaryIndexStyle( $style ) {
		$newStyle = CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' );
		if ( empty( $newStyle ) ) {
			$newStyle = $style;
		}

		return $newStyle;
	}

	/**
	 * Adds the default parameters for Glossary Index shortcode
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	public static function addGlossaryIndexDefaultAtts( $atts ) {
		$atts['cat']             = 'all';
		$atts['disable_listnav'] = ! apply_filters( 'cmtt_index_enabled', CMTT_Settings::get( 'cmtt_index_enabled', 0 ) );
		$atts['title_prefix']    = __( 'Glossary for:', 'cm-tooltip-glossary' );
		$atts['title_category']  = 1;
		$atts['title_show']      = 1;
		$atts['search_term']     = (string) filter_input( INPUT_POST, 'search_term' );
		$atts['hide_categories'] = CMTT_Settings::get( 'cmtt_index_hideCategories', 0 );

		return $atts;
	}

	/**
	 * Search the Glossary Terms
	 *
	 * @param string $where
	 *
	 * @return string
	 * @global object $wpdb
	 */
	public static function searchTermsWhere( $where, $wp_query ) {
		global $wpdb;

		$searchTerm = $wp_query->get( 'search_term' );
		$exact      = $wp_query->get( 'exact' );
		$exactAdd   = $exact ? '' : '%';

		$term     = esc_sql( $wpdb->esc_like( trim( $searchTerm ) ) );
		$whereArr = array();
		$term     = str_replace( array( '’', "'", '&#39;', '&#96;', '&#146;', '&rsquo;' ), '_', $term );

		$fields_to_search_in = CMTT_Glossary_Index::get_fields_to_search_in();

		foreach ( $fields_to_search_in as $element ) {
			if ( $element == '0' ) {
				$whereArr[] = $wpdb->posts . '.post_title LIKE "' . $exactAdd . $term . $exactAdd . '"';
			} elseif ( $element == '1' ) {
				$whereArr[] = $wpdb->posts . '.post_content LIKE "' . $exactAdd . $term . $exactAdd . '"';
			}
		}

		$whereArr = apply_filters( 'cmtt_search_where_arr', $whereArr, $term, $wp_query, $fields_to_search_in );
		$where    .= ' AND ( ' . implode( ' OR ', $whereArr ) . ' )';

		return $where;
	}

	/**
	 * Add the search filter to the Glossary Index query
	 *
	 * @param array $args
	 * @param array $shortcodeAtts
	 *
	 * @return array
	 */
	public static function addSearchFilter( $args, $shortcodeAtts ) {
		if ( ! empty( $shortcodeAtts['search_term'] ) ) {
			$args['search_term'] = $shortcodeAtts['search_term'] ?? '';
			$args['cb_search']   = $shortcodeAtts['cb_search'] ?? '';
			add_filter( 'posts_where', array( __CLASS__, 'searchTermsWhere' ), 10, 2 );

			do_action( 'cmtt_glossary_doing_search', $args, $shortcodeAtts );

			/*
			 * Don't add the share box on search
			 */
			remove_filter( 'cmtt_glossary_index_after_content', array( 'CMTT_Pro', 'cmtt_glossaryAddShareBox' ) );
		}

		return $args;
	}

	/**
	 * Remove the search filter after the Glossary Index query
	 */
	public static function removeSearchFilter() {
		remove_filter( 'posts_where', array( __CLASS__, 'searchTermsWhere' ), 10 );
	}

	/**
	 * Adds the Parsing query args
	 *
	 * @param type $args
	 *
	 * @return array|type
	 * @global type $post
	 */
	public static function addParserQueryArgs( $args ) {
		global $post;

		if ( ! empty( $post ) ) {
			$belongsToCats  = self::belongsToCustomCats( $post->ID );
			$customCats     = self::getCurrentCustomCats( $post->ID );
			$customCatsType = self::getCurrentCustomCatsType( $post->ID );

			/*
			 * Glossary category whitelisted/blacklisted through regular category
			 * and there's no overwrite with direct category
			 */
			if ( ! empty( $belongsToCats ) && isset( $belongsToCats['cats'] ) && ! is_array( $customCats ) ) {
				$customCats     = $belongsToCats['cats'];
				$customCatsType = $belongsToCats['type'];
			}

			if ( is_array( $customCats ) ) {
				$tagsQuery = array(
					'taxonomy' => 'glossary-categories',
					'field'    => 'term_id',
					'terms'    => $customCats,
					'operator' => 'whitelist' === $customCatsType ? 'IN' : 'NOT IN',
				);

				if ( ! empty( $args['tax_query'] ) ) {
					$args['tax_query'][]           = $tagsQuery;
					$args['tax_query']['relation'] = 'AND';
				} else {
					$args['tax_query'] = array(
						$tagsQuery,
					);
				}
			}
		}

		return $args;
	}

	/**
	 * Adds the Glossary Index query args
	 */
	public static function addGlossaryIndexQueryArgs( $args, $shortcodeAtts ) {
		global $post;

		$hideFromIdexEnabled = CMTT_Settings::get( 'cmtt_enableHidingFromIndex', false );

		if ( ! $hideFromIdexEnabled ) {
			$metaQueryArgs = array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key'   => '_cmtt_hide_from_index',
						'value' => '0',
					),
					array(
						'key'     => '_cmtt_hide_from_index',
						'compare' => 'NOT EXISTS',
					),
                    array(
                        'key'     => '_cmtt_hide_from_index',
                        'value' => '',
                    ),
				),
			);
			if ( isset( $args['meta_query'] ) ) {
				$args['meta_query'][] = $metaQueryArgs;
			} else {
				$args['meta_query'] = $metaQueryArgs;
			}
		}

		if ( ! empty( $post ) ) {
			$customCats     = self::getCurrentCustomCats( $post->ID );
			$customCatsType = self::getCurrentCustomCatsType( $post->ID );

			if ( is_array( $customCats ) ) {
				$tagsQuery = array(
					'taxonomy' => 'glossary-categories',
					'field'    => 'term_id',
					'terms'    => $customCats,
					'operator' => 'whitelist' === $customCatsType ? 'IN' : 'NOT IN',
				);

				if ( ! empty( $args['tax_query'] ) ) {
					$args['tax_query'][]           = $tagsQuery;
					$args['tax_query']['relation'] = 'AND';
				} else {
					$args['tax_query'] = array(
						$tagsQuery,
					);
				}
			}
		}

		if ( isset( $shortcodeAtts['cat'] ) && is_array( $shortcodeAtts['cat'] ) ) {
			$tagsQuery = array(
				'taxonomy' => 'glossary-categories',
				'field'    => 'term_id',
				'terms'    => $shortcodeAtts['cat'],
				'operator' => 'IN',
			);

			if ( ! empty( $args['tax_query'] ) ) {
				$args['tax_query'][]           = $tagsQuery;
				$args['tax_query']['relation'] = 'AND';
			} else {
				$args['tax_query'] = array(
					$tagsQuery,
				);
			}
		}

		if ( isset( $shortcodeAtts['gtags'] ) && is_array( $shortcodeAtts['gtags'] ) ) {

			$exactMatch = CMTT_Settings::get( 'cmtt_glossary_tagsExactMatch', '0' );
			if ( ! $exactMatch ) {
				$tagsQuery = array(
					'taxonomy' => 'glossary-tags',
					'field'    => 'term_id',
					'terms'    => $shortcodeAtts['gtags'],
					'operator' => 'IN',
				);

				if ( ! empty( $args['tax_query'] ) ) {
					$args['tax_query'][]           = $tagsQuery;
					$args['tax_query']['relation'] = 'AND';
				} else {
					$args['tax_query'] = array(
						$tagsQuery,
					);
				}
			} else {
				foreach ( $shortcodeAtts['gtags'] as $tag ) {
					$tagQuery = array(
						'taxonomy' => 'glossary-tags',
						'field'    => 'term_id',
						'terms'    => array( $tag ),
						'operator' => 'IN',
					);
					if ( ! empty( $args['tax_query'] ) ) {
						$args['tax_query'][]           = $tagQuery;
						$args['tax_query']['relation'] = 'AND';
					} else {
						$args['tax_query'] = array(
							$tagQuery,
						);
					}
				}
			}
		}

		return $args;
	}

	/**
	 * AJAX call for the glossary
	 * - takes into account all the shortcode params
	 *
	 * @global type $post
	 */
	public static function ajaxGlossary() {
		$content = CMTT_Glossary_Index::glossaryShortcode();
		if ( CMTT_Settings::get( 'cmtt_glossaryParseOnGlossarySearch', 0 ) ) {
			$content = CMTT_Free::cmtt_glossary_parse( $content, true );
		}
		echo trim( $content );
		exit();
	}

	/**
	 * Filter the shortcode atts with the $_GET
	 *
	 * @param type $baseAtts
	 *
	 * @return array
	 */
	public static function processGlossaryIndexShortcodeAtts( $baseAtts ) {
		$processAtts = array();
		if ( isset( $baseAtts['cat'] ) && 'all' !== $baseAtts['cat'] ) {
			$processAtts['freeze_cat']      = 1;
			$processAtts['hide_categories'] = isset( $baseAtts['hide_categories'] ) ? $baseAtts['hide_categories'] : 0;
		}
		$glossaryCategoriesAllDisabled = CMTT_Settings::get( 'cmtt_glossary_disableAllCats', '0' );
		if ( $glossaryCategoriesAllDisabled && 'all' === $baseAtts['cat'] ) {
			/*
			 * Code allows to display only the relevant categories meaning that only the categories of the currently displayed items will be displayed.
			 */
			$showOnlyRelevant = isset( $baseAtts['only_relevant_cats'] ) && $baseAtts['only_relevant_cats'];
			if ( $showOnlyRelevant ) {
				$args      = isset( CMTT_Free::$lastQueryDetails['nopaging_args'] ) ? CMTT_Free::$lastQueryDetails['nopaging_args'] : array();
				$posts_ids = array();

				if ( ! empty( $args ) && is_array( $args ) ) {
                    $args['return_just_ids'] = 1;
                    $posts_ids = CMTT_Free::getGlossaryItems( $args );
					$cats = wp_get_object_terms(
						$posts_ids,
						'glossary-categories',
						array(
							'hide_empty' => 1,
							'orderby'    => 'name',
							'order'      => 'ASC',
						)
					);
				}
			} else {
				// Getting Glossary categories
				$cats = get_terms( 'glossary-categories' );
			}
			if ( ! empty( $cats ) ) {
				$firstCat           = reset( $cats );
				$processAtts['cat'] = $firstCat->term_id;
			}
		}

		return array_merge( $baseAtts, $processAtts );
	}

	/**
	 * Filter the shortcode atts with the $_POST
	 *
	 * @param type $baseAtts
	 *
	 * @return array
	 */
	public static function addGlossaryIndexPostAtts( $baseAtts ) {
		$postAtts = (array) filter_input_array( INPUT_POST );
		$atts     = array_merge( $baseAtts, $postAtts );

		unset( $atts['action'] );

		if ( ! empty( $atts['search_changed'] ) ) {
			$atts['itemspage'] = 1;
		}

		$atts = self::normalizeTaxonomyTermParameter( $atts, 'gtags', 'glossary-tags' );
		if ( isset( $atts['cat'] ) && 'all' != $atts['cat'] ) {
			$atts = self::normalizeTaxonomyTermParameter( $atts, 'cat', 'glossary-categories' );
		}

		return $atts;
	}

	/**
	 * @param $atts
	 * @param $parameterName
	 * @param $taxonomyName
	 *
	 * @return array
	 */
	public static function normalizeTaxonomyTermParameter( $atts, $parameterName, $taxonomyName ) {
		if ( ! empty( $atts[ $parameterName ] ) && ! is_array( $atts[ $parameterName ] ) ) {
			$atts[ $parameterName ] = explode( ',', $atts[ $parameterName ] );
		}

		if ( ! empty( $atts[ $parameterName ] ) && is_array( $atts[ $parameterName ] ) ) {
			array_map( 'trim', $atts[ $parameterName ] );
			foreach ( $atts[ $parameterName ] as $key => $tag ) {
				if ( ! is_numeric( $tag ) ) {
					$tagObj = get_term_by( 'slug', esc_attr( $tag ), $taxonomyName );
					if ( false === $tagObj ) {
						$tagObj = get_term_by( 'name', esc_attr( $tag ), $taxonomyName );
					}

					if ( is_object( $tagObj ) ) {
						$atts[ $parameterName ][ $key ] = $tagObj->term_id;
					}
				}
			}
			$atts[ $parameterName ] = array_filter( $atts[ $parameterName ], 'is_numeric' );
		}

		return $atts;
	}

	/**
	 * Support for custom templates
	 *
	 * @param type $single
	 *
	 * @return string
	 * @global type $post
	 */
	public static function glossaryTermCustomTemplate( $single ) {
		global $post;
		/* Checks for single template by post type */
		if ( $single !== 'single-glossary.php' ) {
			if ( CMTT_Settings::get( 'cmtt_glossaryUseTemplate' ) && $post->post_type == 'glossary' ) {

				$glossary_path    = plugin_dir_path( __FILE__ );
				$theme_path       = get_stylesheet_directory();
				$default_template = CMTT_Settings::get( 'cmtt_glossaryPageTermTemplate' );
				if ( $default_template == '1' ) {
					if ( file_exists( $theme_path . '/Tooltip/single-sidebar-glossary.php' ) && file_exists( $theme_path . '/Tooltip/sidebar-glossary.php' ) ) {
						return $theme_path . '/Tooltip/single-sidebar-glossary.php';
					} elseif ( file_exists( $glossary_path . 'theme/Tooltip/single-sidebar-glossary.php' ) && file_exists( $glossary_path . 'theme/Tooltip/sidebar-glossary.php' ) ) {
						return $glossary_path . 'theme/Tooltip/single-sidebar-glossary.php';
					}
				} else {
					if ( file_exists( $theme_path . '/Tooltip/single-glossary.php' ) ) {
						return $theme_path . '/Tooltip/single-glossary.php';
					} elseif ( file_exists( $glossary_path . 'theme/Tooltip/single-glossary.php' ) ) {
						return $glossary_path . 'theme/Tooltip/single-glossary.php';
					}
				}
			}
		}

		return $single;
	}

	/**
	 * @return void
	 */
	public static function cmtt_custom_sidebar() {
		register_sidebar(
			array(
				'name'          => 'CM Tooltip Glossary Sidebar',
				'id'            => 'CM Tooltip Glossary Sidebar',
				'description'   => 'The sidebar for "CM Tooltip Glossary Pro+ Sidebar Image" term template',
				'before_widget' => '<div class="widget topbar-widget-container">',
				'after_widget'  => '</div>',
				'before_title'  => '<span class="widget topbar-widget-title">',
				'after_title'   => '</span>',
			)
		);
	}

	/**
	 * Add the abbreviation in the square brackets to the post's title
	 *
	 * @param string $title
	 * @param int $id
	 *
	 * @return string
	 */
	public static function addAbbreviation( $title = '', $id = null ) {
		$addAbbreviationsToTitle = CMTT_Settings::get( 'cmtt_glossaryAbbreviationsInTitle', 1 );

		if ( $id && $addAbbreviationsToTitle ) {
			$glossaryItem = get_post( $id );
			if ( $glossaryItem && 'glossary' == $glossaryItem->post_type ) {
				$abbreviation = CMTT_Abbreviations::getAbbreviation( $id );
				if ( $abbreviation &&  $abbreviation !== $title) {
					if ( CMTT_Settings::get( 'cmtt_roundBracketsAbbr' ) == 'round' ) {
						$title .= ' (' . $abbreviation . ')';
					} else {
						$title .= ' [' . $abbreviation . ']';
					}
				}
			}
		}

		return $title;
	}

	/**
	 * Add the abbreviation in the square brackets to the post's title
	 *
	 * @return void
	 */
	public static function addQuickEdit() {
		ob_start();
		?>
		<div class="inline-edit-col">
			<label class="alignleft">
				<span class="title">Term Icon</span>
				<span class="input-text-wrap"><input type="text" id="cmtt_term_icon" name="cmtt_term_icon"
				                                     class="inline-edit-cmtt_term_icon" value=""></span>
			</label>
		</div>
		<?php
		$content = ob_get_clean();
		echo $content;
	}

	/**
	 * Add the abbreviation in the square brackets to the post's title
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public static function addMetaColumn( $id ) {
		$icon = self::getDashicon( $id );
		ob_start();
		if ( ! empty( $icon ) ) :
			?>
			<div id="cmtt_meta_icon_<?php echo esc_attr( $id ); ?>">
				Custom Icon: <?php echo $icon; ?>
			</div>
		<?php
		endif;
		$content = ob_get_clean();
		echo $content;
	}

	/**
	 * @param $id
	 * @param $glossary_page
	 *
	 * @return string
	 */
	public static function getDashicon( $id = null, $glossary_page = 0 ) {
		$dashicon = '';
		if ( empty( $id ) ) {
			return $dashicon;
		}
		$glossaryItem = get_post( $id );
		if ( $glossaryItem && 'glossary' == $glossaryItem->post_type ) {
			$icon  = CMTT_Free::_get_meta( '_cmtt_term_icon', $glossaryItem->ID );
			$color = CMTT_Free::_get_meta( '_cmtt_term_icon_color', $glossaryItem->ID );
			if ( empty( $icon ) && empty( $color ) ) {
				$icon  = CMTT_Settings::get( 'cmtt_tooltipLinkIconDefault', '' );
				$color = CMTT_Settings::get( 'cmtt_tooltipLinkIconDefaultColor', '#000' );
			}

			/*
			 * If there's no icon, nor default icon - don't show it at all
			 */
			if ( empty( $icon ) ) {
				return $dashicon;
			}

			$style    = ! empty( $color ) ? 'color:' . $color . ';' : '';
			$dashicon = '<span class="dashicons ' . esc_attr( $icon )
			            . '" data-icon="' . esc_attr( $icon )
			            . '" style="' . $style . 'display:inline;vertical-align:baseline;"></span>';

			if ( CMTT_Settings::get( 'cmtt_tooltipLinkIconToTerm', 0 ) && CMTT_Settings::get( 'cmtt_glossaryTermLink', 0 ) && ! $glossary_page ) {
				$permalink = get_permalink( $id );
				$dashicon  = '<a href="' . $permalink . '">' . $dashicon . '</a>';
			}
		}

		return $dashicon;
	}

	/**
	 * @param $title
	 * @param $id
	 *
	 * @return mixed|string
	 */
	public static function addDashiconForTitle( $title = '', $id = null ) {
	    global $post;
		if ( CMTT_Settings::get( 'cmtt_tooltipLinkIconOnTermPage', 1 ) && $post && $post->post_type == 'glossary' && $post->ID == $id && is_main_query()) {
			$title = self::addDashicon( $title, $id );
		}

		return $title;
	}

	/**
	 * Add the dashicon to the post's title
	 *
	 * @param string $title
	 * @param int $id
	 *
	 * @return string
	 */
	public static function addDashicon( $title = '', $id = null, $glossary_page = null ) {
		if ( $glossary_page == 1 && empty( CMTT_Settings::get( 'cmtt_tooltipLinkIconOnGlossaryPage', 0 ) ) ) {
			return $title;
		}
		if ( $id ) {
			if ( is_object( $id ) ) {
				$id = $id->ID;
			}
			$position = CMTT_Free::_get_meta( '_cmtt_term_icon_position', $id );

			if ( empty( $position ) ) {
				$position = CMTT_Settings::get( 'cmtt_tooltipLinkIconDefaultPosition', 'right' );
			}

			$dashicon = self::getDashicon( $id, $glossary_page );

			if ( $position == 'right' ) {
				$title = $title . $dashicon;
			} else {
				$title = $dashicon . $title;
			}
		}

		return $title;
	}

	/**
	 * Add the category name before the post title
	 *
	 * @param string $title
	 * @param null $glossaryItem
	 * @param null $glossary_page
	 *
	 * @return string
	 */
	public static function addCategory( $title = '', $glossaryItem = null, $glossary_page = null ) {
		if ( CMTT_Settings::get( 'cmtt_showCategoryBeforeTitleOnIndex', 0 ) && $glossaryItem ) {
			$categories  = wp_get_post_terms( $glossaryItem->ID, 'glossary-categories' );
			$categoryArr = array();
			$category    = '';
			if ( ! empty( $categories ) ) {
				foreach ( $categories as $category ) {
					$categoryArr[] = $category->name;
				}
				$category = apply_filters( 'cmtt_index_title_category_name', '<span class=glossaryLinkCategory>' . implode( ', ', $categoryArr ) . '</span> ', $glossaryItem );
			}
			$title = $category . $title;
		}

		return $title;
	}

	/**
	 * Disable the parsing
	 */
	public static function disableParsing() {
		remove_filter( 'the_content', array( __CLASS__, 'addRelatedTerms' ), 21500 );
	}

	/**
	 * Reenable the parsing
	 */
	public static function reenableParsing() {
		add_filter( 'the_content', array( __CLASS__, 'addRelatedTerms' ), 21500 );
	}

	/**
	 * Add related terms to posts and pages
	 *
	 * @param type $content
	 *
	 * @return type
	 * @global type $wp_query
	 * @global type $replacedTerms
	 */
	public static function addRelatedTerms( $content = '' ) {
		static $doTermsOnce = true;

		if ( ! $doTermsOnce && CMTT_Settings::get( 'cmtt_addBacklinksOnce', 0 ) ) {
			return $content;
		}

		$doTermsOnce = false;

		global $wp_query, $replacedTerms;

		// static $added = array();

		if ( ! isset( $wp_query->post ) ) {
			return $content;
		}
		$post = $wp_query->post;
		$id   = $post->ID;

		$disableRelatedTermsForPage        = (bool) get_post_meta( $id, '_glossary_disable_related_terms_for_page', true );
		$disableRelatedTermsOnTermPages    = ( $post->post_type == 'glossary' ) ? CMTT_Settings::get( 'cmtt_glossaryDisableRelatedTermsForTerms', 0 ) : 0;
		$disableRelatedTermsGeneralSetting = (bool) CMTT_Settings::get( 'cmtt_showRelatedTermsList' );
        $relatedTermsLimit = \CM\CMTT_Settings::get('cmtt_glossary_showRelatedArticlesGlossaryCount');
		/*
		 * updated function of the meta to "override" the general setting
		 * this allows to disable the functionality globally but still enable it on a few selected pages
		 */
		$disableRelatedTermsForThisPage = $disableRelatedTermsGeneralSetting == $disableRelatedTermsForPage;

		/*
		 * Fixed the issue when the main query being parsed two times, and displayed only the second time (the related terms would not appear)
		 */
		// if ( !in_array( $id, $added ) && is_singular() && $wp_query->is_main_query() && (\CM\CMTT_Settings::get( 'cmtt_showRelatedTermsList' ) == 1) && !$disableRelatedTermsForPage && !$disableRelatedTermsOnTermPages ) {
        if ( is_singular() && $wp_query->is_main_query() && !$disableRelatedTermsForThisPage && !$disableRelatedTermsOnTermPages ) {
            if ( !empty($replacedTerms) && is_array($replacedTerms) && $relatedTermsLimit > count( $replacedTerms )) {
                $relatedTerms = array_slice( $replacedTerms, 0, $relatedTermsLimit );
            } else {
                $relatedTerms = $replacedTerms;
            }
            $relatedSnippet = CMTT_Related::renderRelatedTerms( $relatedTerms );
            $content .= $relatedSnippet;
        }

		return $content;
	}

	/**
	 * Adds the "Select Icon" button and Preview
	 *
	 * @param array $content
	 *
	 * @return array
	 */
	public static function renderIconSelectButton( $content, $post ) {
		$selectedIcon      = get_post_meta( $post->ID, '_cmtt_term_icon', true );
		$selectedIconColor = get_post_meta( $post->ID, '_cmtt_term_icon_color', true );
		$selectedIconPos   = get_post_meta( $post->ID, '_cmtt_term_icon_position', true );
		$positionsArr      = array( 'left', 'right' );

		// Choose Icon
		$additionalContent = '<div class="cmtt-term-property"><label for="cmtt_term_icon"><input id="cmtt_term_icon" type="text" name="cmtt_term_icon" value="' . esc_attr( $selectedIcon ) . '" />
<input class="button dashicons-picker" type="button" value="Choose Icon" data-target="#cmtt_term_icon" data-preview="#cmtt_term_icon_preview" />
<span style="font-size: 15px;padding: 4px;display: inline-block;">
' . __( 'Preview:', 'cm-tooltip-glossary' ) . ' <span id="cmtt_term_icon_preview" class="dashicons ' . esc_attr( $selectedIcon ) . '"></span></span></label></div>';

		// Choose Icon Color
		$additionalContent .= '<div class="cmtt-term-property"><label for="cmtt_term_icon_color">Select icon color</label>
<input type="text" class="colorpicker" id="cmtt_term_icon_color" name="cmtt_term_icon_color" value="' . esc_attr( $selectedIconColor ) . '" /></div>';

		// Choose Icon position
		$additionalContent .= '<div class="cmtt-term-property"><label for="cmtt_term_icon_position">Choose Icon position</label><select name="cmtt_term_icon_position" id="cmtt_term_icon_position">';
		$defaultSelected   = empty( $selectedIconPos ) ? 'selected' : '';
		$additionalContent .= '<option value="0" ' . $defaultSelected . '>Default</option>';
		foreach ( $positionsArr as $pos ) {
			$selected          = $selectedIconPos == $pos ? 'selected' : '';
			$additionalContent .= '<option value="' . $pos . '" ' . $selected . '>' . ucfirst( $pos ) . '</option>';
		}
		$additionalContent .= '</select></div>';

		$content[] = $additionalContent;

		return $content;
	}

	/**
	 * Adds the "Flush API Cache" button
	 *
	 * @param array $content
	 *
	 * @return array
	 */
	public static function renderFlushButton( $content ) {

		// Choose Icon Color
		$additionalContent = '<div>
<input type="checkbox" id="cmtt_flush_thirdparty" name="cmtt_flush_thirdparty" value="1" />
<label for="cmtt_flush_thirdparty">' . __( 'Flush all API Caches for this term', 'cm-tooltip-glossary' ) . '</label>
</div>';

		$content[] = $additionalContent;

		return $content;
	}

	/**
	 * Fix the highlighting of the element in the Admin menu
	 *
	 * @param type $parent_file
	 *
	 * @return type
	 * @global type $pagenow
	 * @global string $submenu_file
	 * @global type $current_screen
	 */
	public static function setCurrentMenu( $parent_file ) {
		global $submenu_file, $current_screen, $pagenow;
		// Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
		if ( $current_screen->post_type == 'glossary' ) {
			if ( $pagenow == 'edit-tags.php' ) {
				$submenu_file = 'edit-tags.php?taxonomy=' . $current_screen->taxonomy . '&post_type=' . $current_screen->post_type;
			}
			$parent_file = CMTT_MENU_OPTION;
		}

		return $parent_file;
	}

	/**
	 * Create taxonomies
	 */
	public static function createTaxonomies() {
		$glossaryCategoriesPermalink = CMTT_Settings::get( 'cmtt_glossaryCategoriesPermalink', 'glossary-categories' );
		$glossaryCategoriesArgs      = array(
			'label'             => __( 'Tooltip Categories', 'cm-tooltip-glossary' ),
			'rewrite'           => array(
				'slug'       => $glossaryCategoriesPermalink,
				'with_front' => false,
			),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
		);
		register_taxonomy(
			'glossary-categories',
			'glossary',
			apply_filters( 'cmtt_taxonomy_categories_args', $glossaryCategoriesArgs )
		);

		self::add_categories_rewrite_rules();

		$glossaryTagsPermalink = CMTT_Settings::get( 'cmtt_glossaryTagsPermalink', 'glossary-tags' );
		$glossaryTagsArgs      = array(
			'label'             => __( 'Tooltip Tags', 'cm-tooltip-glossary' ),
			'rewrite'           => array(
				'slug'       => $glossaryTagsPermalink,
				'with_front' => false,
			),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
		);
		register_taxonomy(
			'glossary-tags',
			'glossary',
			apply_filters( 'cmtt_taxonomy_tags_args', $glossaryTagsArgs )
		);
	}

	public static function add_categories_rewrite_rules() {
		global $wpdb;

		$sql          = "SELECT DISTINCT meta_value FROM {$wpdb->prefix}termmeta WHERE `meta_key` LIKE '_cmtt_category_index_link' AND meta_value <> ''";
		$custom_links = $wpdb->get_col( $sql );
		if ( ! empty( $custom_links ) ) {
			foreach ( $custom_links as $link ) {
				self::add_category_rewrite_rule( $link );
			}
			flush_rewrite_rules( false );
		}
	}

	/**
	 * Adds the support for tags to "glossary" post item
	 *
	 * @param type $args
	 *
	 * @return type
	 */
	public static function addPostTypeSupport( $args ) {
		if ( ! isset( $args['taxonomies'] ) || ! is_array( $args['taxonomies'] ) ) {
			$args['taxonomies'] = array( 'glossary-tags' );
		} else {
			$args['taxonomies'][] = 'glossary-tags';
		}

		return $args;
	}

	/**
	 * RTL
	 */
	public static function _rtl_support() {
		wp_add_inline_style(
			'cmtooltip',
			'
			#tt {
				direction: rtl !important;
}
			.ln-letters a {
				float: right !important;
			}
		'
		);
	}

	/**
	 *  Tiles View Plus
	 */
	public static function _image_tiles_view( $id = null ) {
		$result       = array();
		$permalink    = get_permalink( $id );
		$thumbnail    = get_the_post_thumbnail( $id, array( 250, 250 ) );
		$windowTarget = ( CMTT_Settings::get( 'cmtt_glossaryInNewPage' ) == 1 ) ? ' target="_blank" ' : '';
		if ( ! empty( $thumbnail ) ) {
            if(is_ssl()){
                $thumbnail = str_replace('http://','https://',$thumbnail);
            }
			$result['thumbnail'] = '<a class="cmtt-thumbnail" ' . $windowTarget . ' href="' . $permalink . '">' . $thumbnail . '</a>';
		} else {
			if ( CMTT_Settings::get( 'cmtt_glossary_no_thumb' ) != '' ) {
				$src                 = wp_get_attachment_image_src( CMTT_Settings::get( 'cmtt_glossary_no_thumb' ), array(
					250,
					250
				) );
				$result['thumbnail'] = '<a href="' . $permalink . '"><img src="' . $src[0] . '" width="250" height="250"></a>';
			} else {
				$result['thumbnail'] = '<img src="' . plugin_dir_url( __FILE__ ) . 'assets/no_image.jpg" width="250" height="250">';
			}
		}
		$result['liAdditionalClass'] = 'cmtt-has-thumbnail';

		return $result;
	}

	/**
	 *  Image + Term + Definition View Plus
	 */
	public static function _image_term_definition_view( $id = null ) {
		$result           = array();
		$permalink        = get_permalink( $id );
		$full_img         = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );
		$full_img_url     = ! empty( $full_img ) ? ' data-full-img="' . $full_img[0] . '"' : '';
		$windowTarget     = ( CMTT_Settings::get( 'cmtt_glossaryInNewPage' ) == 1 ) ? ' target="_blank" ' : '';
		$link_to_original = CMTT_Settings::get( 'cmtt_linkThumbnailToOriginal', false );

		if ( is_array( $full_img ) ) {
			$thumbnail = get_the_post_thumbnail( $id, array( 250, 250 ), array( 'srcset' => $full_img[0] ) );
		}
        if(is_ssl()){
            $thumbnail = str_replace('http://','https://',$thumbnail);
        }
		if ( ! empty( $thumbnail ) ) {
			$result['thumbnail'] = '<a class="cmtt-thumbnail" ' . $full_img_url . $windowTarget . ' href="' . $permalink . '" data-link="' . $link_to_original . '">';
			$result['thumbnail'] .= $thumbnail . '</a>';
		} else {
			if ( CMTT_Settings::get( 'cmtt_glossary_no_thumb' ) != '' ) {
				$src                 = wp_get_attachment_image_src( CMTT_Settings::get( 'cmtt_glossary_no_thumb' ), array(
					250,
					250
				) );
				$result['thumbnail'] = '<a href="' . $permalink . '"' . $windowTarget . '><img src="' . $src[0] . '" width="250" height="250"></a>';
			} else {
				$result['thumbnail'] = '<img src="' . plugin_dir_url( __FILE__ ) . 'assets/no_image.jpg" width="250" height="250">';
			}
		}
		$result['liAdditionalClass'] = 'cmtt-has-thumbnail';

		return $result;
	}

	/**
	 *  Media Uploader
	 */
	public static function _image_uploader( $name ) {
		wp_enqueue_media();
		$src = wp_get_attachment_image_src( CMTT_Settings::get( $name ), array( 250, 250 ) );
		( CMTT_Settings::get( $name ) != '' ) ? $hasThumb = 'cmtt_hasThumb' : $hasThumb = '';
		$image = ! empty( $src ) ? $src[0] : '';

		return '
			<div id="' . $name . '-preview" class="cmtt_Media_Image ' . $hasThumb . '" style="background-image:url(' . '"' . $image . '"' . ')"></div>
			<input id="' . $name . '" class="' . $name . ' cmtt_Media_Storage" name="' . $name . '" type="hidden" value="' . CMTT_Settings::get( $name ) . '" />
			<input class="upload_image_button cminds_link" id="upload_btn" type="button" value="Upload" />
			<input class="remove_image_button cminds_link" id="remove_btn" type="button" value="Remove" data-input="' . $name . '" />
		';
	}

	/**
	 * @param $content
	 * @param $glossaryIndexStyle
	 * @param $shortcodeAtts
	 *
	 * @return mixed|string
	 */
	public static function cmtt_glossary_index_before_terms_list( $content, $glossaryIndexStyle, $shortcodeAtts ) {
		$term_label = CMTT_Settings::get( 'cmtt_term_defition_term_label', 'Term' );
		$desc_label = CMTT_Settings::get( 'cmtt_term_defition_definition_label', 'Definition' );
		if ( $glossaryIndexStyle == 'term-definition' && ! empty( $term_label ) && ! empty( $desc_label ) ) {
			$content .= '<li class="cmtg-term-definition_header">
                            <div class="glossary_itemtitle">' . $term_label . '</div>
                            <div class="glossary_itemdesc">' . $desc_label . '</div>
                        </li>';
		}

		return $content;
	}

	/**
	 * @param $disable
	 * @param $glossaryTerm
	 *
	 * @return bool
	 */
	public static function cmtt_glossary_disable_tooltips_by_category( $disable, $glossaryTerm ) {
		$categories  = get_the_terms( $glossaryTerm->ID, 'glossary-categories' );
		$is_disabled = false;
		if ( ! empty( $categories ) && count( $categories ) > 0 ) {
			foreach ( $categories as $category ) {
				$is_disabled = get_term_meta( $category->term_id, '_cmtt_category_disable_tooltips', true );
				if ( $is_disabled ) {
					break;
				}
			}
		}

		return $disable || $is_disabled;
	}

	/**
	 * @param $additions
	 * @param $glossary_item
	 *
	 * @return array|mixed
	 */
	public static function addNegativeWordsToAdditions( $additions, $glossary_item, $context, $shortcodeAtts ) {
		if ( 'parsing' !== $context ) {
			return $additions;
		}
		$negative_words = self::get_negative_words( $glossary_item );

		if ( ! empty( $negative_words ) && is_array( $negative_words ) ) {
			$additions = array_merge( $additions, $negative_words );
		}

		return $additions;
	}

	/**
	 * @param $name
	 * @param $from_get
	 * @param $additional
	 *
	 * @return string
	 */
	public static function outputCategoriesSelect( $name, $from_get = false, $additional = array() ) {
		$categories = get_terms(
			array(
				'taxonomy'   => array( self::CATEGORY_TAXONOMY ),
				'hide_empty' => false,
			)
		);
		if ( ! empty( $categories ) ) {
			$values = array();
			if ( $from_get ) {
				$userCats = array();
				if ( is_user_logged_in() ) {
					$userCats = get_user_meta( get_current_user_id(), 'cmtt_tooltip_categories_override', true );
					if ( ! is_array( $userCats ) ) {
						$userCats = array();
					}
				}
				$get                 = filter_input_array( INPUT_GET );
				$selected_categories = $get[ $name ] ?? $userCats;
			} else {
				$selected_categories = CMTT_Settings::get( $name, array() );
			}
			if ( ! is_array( $additional ) ) {
				$additional = array();
			}
			$selected_categories = is_array( $selected_categories ) ? $selected_categories : array();
			$selected_categories = array_unique( array_merge( $selected_categories, $additional ) );
			foreach ( $categories as $language ) {
				$values[ $language->term_id ] = $language->name;
			}
			$content = CMTT_Free::_outputMultipleValues( $name, $values, $selected_categories, 1 );
		} else {
			return "<strong>You didn't define any categories yet.</strong>";
		}

		return $content;
	}

	public static function addLabels() {
		?>
		<tr valign="top">
			<th scope="row">Default label for the "Sidebar + term page" Glossary Index view</th>
			<td>
				<input type="text"
				       name="cmtt_index_sidebar_default"
				       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_index_sidebar_default', 'Select the term to display its content.' ); ?>"/>
			</td>
		</tr>
		<?php
	}

}