<?php

use CM\CMTT_Settings;

class CMTT_Pro {

	protected static $messages = '';

	public static function init() {
		global $cmtt_isLicenseOk;

		self::includeFiles();

		self::initFiles();

		self::setup_constants();

		if ( $cmtt_isLicenseOk ) {

			remove_filter( 'cmtt_settings_tooltip_tab_content_after', array(
				'CMTT_Free',
				'cmtt_settings_tooltip_tab_content_after'
			) );
			add_filter( 'cmtt_settings_tooltip_tab_content_after', array(
				__CLASS__,
				'cmtt_settings_tooltip_tab_content_after'
			), 1000 );

			if ( \CM\CMTT_Settings::get( 'cmtt_withoutGlossaryForTermLink' ) ) {
				add_filter( 'cmtt_term_tooltip_permalink', array(
					__CLASS__,
					'cmtt_remove_glossary_from_link'
				), 999, 2 );
				add_action( 'pre_get_posts', array( __CLASS__, 'cmtt_update_rule_for_term_links' ) );
			}
			/*
			 * Footnotes
			 */
			add_filter( 'cmtt_show_tooltips', array( __CLASS__, 'maybeDisplayFootnotes' ), 200, 3 );
			add_filter( 'cmtt_parsed_content', array( __CLASS__, 'onFootnotesDefs' ), 200 );
			add_action( 'cmtt_add_disables_metabox', array( __CLASS__, 'addDisablesFields' ) );
			add_action( 'cmtt_on_glossary_item_save_before', array( __CLASS__, 'saveDisableRelatedPosts' ), 14, 2 );

			add_filter( 'cmtt_glossary_index_style', array( __CLASS__, 'selectTheGlossaryIndexStyle' ), 10, 4 );
			add_filter( 'cmtt_glossary_index_item_desc', array( __CLASS__, 'outputGlossaryIndexItemDesc' ), 10, 4 );
			add_filter( 'cmtt_glossary_index_item_desc', array( __CLASS__, 'wrapGlossaryIndexItemDesc' ), 100, 4 );

			if ( \CM\CMTT_Settings::get( 'cmtt_glossaryShowShareBoxTermPage' ) == 1 ) {
				add_filter( 'cmtt_glossary_term_after_content', array( __CLASS__, 'cmtt_glossaryAddShareBox', ) );
			}

            if ( \CM\CMTT_Settings::get( 'cmtt_glossaryAddTermPagelinkAfterDescription' ) == 0 ) {
                add_filter( 'cmtt_tooltip_content_add', array( __CLASS__, 'addTermPageLinkToTooltip' ), 100, 2 );
            } else{
                add_filter( 'cmtt_tooltip_content_add', array( __CLASS__, 'addTermPageLinkToTooltip' ), 3, 2 );
            }

            add_filter( 'cmtt_tooltip_content_add', array( __CLASS__, 'addRelatedLinkToTooltip' ), 15, 2 );

			add_action( 'cmtt_save_options_before', array( __CLASS__, 'flushCaps' ), 10, 2 );

			add_action( 'cmtt_replace_template_after_synonyms', array( __CLASS__, 'checkPrivateTerms' ), 32, 3 );

			add_filter( 'cmtt_glossary_index_item_additions', array( __CLASS__, 'addSynonymsAndVariations' ), 10, 4 );
			/*
			 * Tooltips in Ninja tables
			 */
			if ( \CM\CMTT_Settings::get( 'cmtt_glossaryParseNinjaTables', 0 ) == 1 ) {
				add_filter(
					'ninja_tables_get_public_data',
					array(
						__CLASS__,
						'cmtt_parse_ninja_tables',
					),
					10,
					2
				);
			}

			/*
			 * Tooltips in BuddyBoss Activity Content
			 */
			if ( \CM\CMTT_Settings::get( 'cmtt_parseBuddyBossActivityContent' ) ) {
				add_filter(
					'bp_get_activity_content_body',
					array(
						__CLASS__,
						'cmtt_bp_parse',
					),
					\CM\CMTT_Settings::get( 'cmtt_tooltipParsingPriority', 20000 ),
					2
				);
			}

			/**
			 * Add support for Avia Builder for glossary terms
			 *
			 * @since 4.0.12
			 */
			add_filter(
				'avf_alb_supported_post_types',
				function ( array $supported_post_types ) {
					$supported_post_types[] = 'glossary';

					return $supported_post_types;
				},
				10,
				1
			);

			/*
			 * Add support for Bricks Builder
			 */
			add_filter( 'bricks/frontend/render_data', array( __CLASS__, 'addTooltipsToBricksBuilder' ), 10, 3 );

			/*
			 * Mobile support
			 */
			add_filter( 'cmtt_tooltip_script_data', array( __CLASS__, 'addTooltipScriptData' ) );
		}
	}

	/**
	 * Include the files
	 */
	public static function includeFiles() {
		include_once CMTT_PLUGIN_DIR . 'synonyms.php';
		include_once CMTT_PLUGIN_DIR . 'import_export.php';
		include_once CMTT_PLUGIN_DIR . 'related.php';
		include_once CMTT_PLUGIN_DIR . 'widgets.php';
		include_once CMTT_PLUGIN_DIR . 'package/cminds-pro.php';
		include_once CMTT_PLUGIN_DIR . 'customTemplates.php';
		include_once CMTT_PLUGIN_DIR . 'schema.php';
		include_once CMTT_PLUGIN_DIR . 'cache.php';
	}

	/**
	 * Initialize the files
	 */
	public static function initFiles() {
		CMTT_RandomTerms_Widget::init();
		CMTT_Import_Export::init();
		CMTT_Search_Widget::init();
		CMTT_LatestTerms_Widget::init();
		CMTT_RelatedTerms_Widget::init();
		CMTT_RelatedArticles_Widget::init();
		CMTT_Categories_Widget::init();
		CMTT_Wordofday_Widget::init();
		CMTT_Synonyms::init();
		CMTT_Related::init();
		CMTT_Custom_Templates::init();
		CMTT_Schema::init();
        CMTT_Cache::init();
	}

	/**
	 * Setup plugin constants
	 */
	public static function setup_constants() {

	}

	public static function addTooltipsToBricksBuilder( $content, $post, $area ) {
		if ( 'content' === $area ) {
			$content = CMTT_Free::cmtt_glossary_parse( $content );
		}

		return $content;
	}

	/**
	 * Don't show private terms for other users
	 *
	 * @param string $titleIndex
	 * @param string $title
	 *
	 * @return type
	 */
	public static function checkPrivateTerms( $currentItem, $titleIndex, $title ) {
        $private = $currentItem ? CMTT_Free::_get_meta( 'cmtt_private', $currentItem->ID ) : false;

		if ( $private ) {
			$current_user = get_current_user_id();
			$author       = $currentItem->post_author;
			if ( $current_user != $author ) {
				throw new GlossaryTooltipException( $title );
			}
		}
	}

	public static function cmtt_settings_tooltip_tab_content_after( $content ) {
		ob_start();
		?>
		<div class="block">
			<h3 class="section-title">
				<span>Tooltip - Styling</span>
				<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
				     fill="#6BC07F">
					<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
				</svg>
			</h3>
			<table class="floated-form-table form-table">
				<tr>
					<th scope="row">Show "Close" icon</th>
					<td>
						<input type="hidden" name="cmtt_tooltipShowCloseIcon" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltipShowCloseIcon" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipShowCloseIcon', 1 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">With this option you can choose:<br/>
						<strong>TRUE</strong> - the close icon will be displayed<br/>
						<strong>FALSE</strong> - there won't be the close icon<br/>
					</td>
				</tr>
				<tr>
					<th scope="row">Show "Close" icon only on mobile devices</th>
					<td>
						<input type="hidden" name="cmtt_tooltipShowCloseIconMobile" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltipShowCloseIconMobile" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipShowCloseIconMobile', 0 ) ); ?>
						       value="1"/>
					</td>
					<td colspan="2" class="cm_field_help_container">With this option you can choose:<br/>
						<strong>TRUE</strong> - the close icon will be displayed only on mobile devices<br/>
						<strong>FALSE</strong> - the close icon will be displayed on all devices<br/>
						<strong>Note:</strong> to use this option you need to enable "Show Close icon"
					</td>
				</tr>
				<tr>
					<th scope="row">Close icon color</th>
					<td><input type="text" class="colorpicker" name="cmtt_tooltipCloseColor"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipCloseColor', '#222' ); ?>"/></td>
					<td colspan="2" class="cm_field_help_container">Set color of tooltip close icon</td>
				</tr>
				<tr>
					<th scope="row">Close icon size</th>
					<td><input type="number" name="cmtt_tooltipCloseSize"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipCloseSize', 20 ); ?>" step="1"
					           min="0" max="50"/>px
					</td>
					<td colspan="2" class="cm_field_help_container">Set the size of the tooltip close icon</td>
				</tr>
				<tr>
					<th scope="row">Tooltip background color</th>
					<td><input type="text" class="colorpicker" name="cmtt_tooltipBackground"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipBackground' ); ?>"/></td>
					<td colspan="2" class="cm_field_help_container">Set color of tooltip background</td>
				</tr>
				<tr>
					<th scope="row">Tooltip text color</th>
					<td><input type="text" class="colorpicker" name="cmtt_tooltipForeground"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipForeground' ); ?>"/></td>
					<td colspan="2" class="cm_field_help_container">Set color of tooltip text color</td>
				</tr>
				<tr>
					<th scope="row">Tooltip title's font size</th>
					<td><input type="number" name="cmtt_tooltipTitleFontSize"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipTitleFontSize' ); ?>"
					           step="1" min="0" max="50"/>px
					</td>
					<td colspan="2" class="cm_field_help_container">Set font-size of term title in the tooltip. (Works
						only if the option "Add term
						title to the tooltip content?" is set)
					</td>
				</tr>
				<tr>
					<th scope="row">Tooltip title's text color</th>
					<td><input type="text" class="colorpicker" name="cmtt_tooltipTitleColor_text"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipTitleColor_text', '#000000' ); ?>"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Set color of term title in the tooltip. (Works only
						if the option "Add term title to the tooltip content?" is set)
					</td>
				</tr>
				<tr>
					<th scope="row">Tooltip title's background color</th>
					<td><input type="text" class="colorpicker" name="cmtt_tooltipTitleColor_background"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipTitleColor_background', 'transparent' ); ?>"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Set color of the title's background in the tooltip.
						(Works only if the option "Add term title to the tooltip content?" is set)
					</td>
				</tr>
                <tr>
                    <th scope="row">Tooltip title align</th>
                    <td class="field-select">
                        <?php
                        $positionsArray = array(
                            'initial' => 'Auto',
                            'left'   => 'Left',
                            'right' => 'Right',
                            'center' => 'Center',
                        );
                        ?>
                        <select name="cmtt_tooltipTitleTextAlign">
                            <?php foreach ( $positionsArray as $position => $positionLabel ) : ?>
                                <option
                                        value="<?php echo $position ?>" <?php selected( $position, \CM\CMTT_Settings::get( 'cmtt_tooltipTitleTextAlign', 'initial' ) ); ?>>
                                    <?php echo $positionLabel ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td colspan="2" class="cm_field_help_container">Choose the text-align of the tooltip title.</td>
                </tr>
				<tr class="whole-line">
					<th scope="row">Tooltip border</th>
					<td>Style: <select name="cmtt_tooltipBorderStyle" style="width: 150px;">
							<option
								value="none" <?php selected( 'none', \CM\CMTT_Settings::get( 'cmtt_tooltipBorderStyle' ) ); ?>>
								None
							</option>
							<option
								value="solid" <?php selected( 'solid', \CM\CMTT_Settings::get( 'cmtt_tooltipBorderStyle' ) ); ?>>
								Solid
							</option>
							<option
								value="dotted" <?php selected( 'dotted', \CM\CMTT_Settings::get( 'cmtt_tooltipBorderStyle' ) ); ?>>
								Dotted
							</option>
							<option
								value="dashed" <?php selected( 'dashed', \CM\CMTT_Settings::get( 'cmtt_tooltipBorderStyle' ) ); ?>>
								Dashed
							</option>
						</select><br/>
						Width: <input type="number" name="cmtt_tooltipBorderWidth"
						              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipBorderWidth' ); ?>"
						              step="1" min="0" max="10"/>px<br/>
						Color: <input type="text" class="colorpicker" name="cmtt_tooltipBorderColor"
						              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipBorderColor' ); ?>"/>
					</td>

					<td colspan="2" class="cm_field_help_container">Set border styling (style, width, color)</td>
				</tr>
				<tr>
					<th scope="row">Tooltip rounded corners radius</th>
					<td><input type="number" name="cmtt_tooltipBorderRadius"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipBorderRadius' ); ?>" step="1"
					           min="0" max="50"/>px
					</td>
					<td colspan="2" class="cm_field_help_container">Set rounded corners radius</td>
				</tr>
				<tr>
					<th scope="row">Tooltip opacity</th>
					<td><input type="number" name="cmtt_tooltipOpacity"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipOpacity', 90 ); ?>" step="1"
					           min="1" max="100"/></td>
					<td colspan="2" class="cm_field_help_container">Set opacity of tooltip (100=fully opaque,
						0=transparent)
					</td>
				</tr>
				<tr>
					<th scope="row">Tooltip z-index</th>
					<td><input type="number" name="cmtt_tooltipZIndex"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipZIndex', 1500 ); ?>" step="1"
					           min="1"/></td>
					<td colspan="2" class="cm_field_help_container">Set tooltip z-index</td>
				</tr>
				<tr class="whole-line">
					<th scope="row">Tooltip sizing</th>
					<td>Min. width: <input type="number" style="width:50px" name="cmtt_tooltipWidthMin"
					                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipWidthMin', 200 ); ?>"
					                       step="1"/>px<br/>
						Max. width: <input type="number" style="width:50px" name="cmtt_tooltipWidthMax"
						                   value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipWidthMax', 400 ); ?>"
						                   step="1"/>px
					</td>
					<td colspan="2" class="cm_field_help_container">Set the minimal size of the tooltip in pixels.</td>
				</tr>
				<tr>
					<th scope="row">Tooltip placement</th>
					<td>
						<?php
						$positionsArray = array(
							'horizontal' => 'Left/right',
							'vertical'   => 'Top/bottom',
						);
						?>
						<select name="cmtt_tooltipPlacement">
							<?php foreach ( $positionsArray as $position => $positionLabel ) : ?>
								<option
									value="<?php echo $position; ?>" <?php selected( $position, \CM\CMTT_Settings::get( 'cmtt_tooltipPlacement', 'horizontal' ) ); ?>>
									<?php echo $positionLabel; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
					<td colspan="2" class="cm_field_help_container">Choose the location of the tooltip.</td>
				</tr>
				<tr class="whole-line">
					<th scope="row">Tooltip positioning</th>
					<td>Vertical: <input type="number" style="width:50px" name="cmtt_tooltipPositionTop"
					                     value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipPositionTop' ); ?>"
					                     step="1"/>px<br/>
						Horizontal: <input type="number" style="width:50px" name="cmtt_tooltipPositionLeft"
						                   value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipPositionLeft' ); ?>"
						                   step="1"/>px
					</td>

					<td colspan="2" class="cm_field_help_container">Set distance of tooltip's bottom left corner from
						cursor pointer
					</td>
				</tr>
				<tr>
					<th scope="row">Tooltip font size</th>
					<td><input type="number" style="width:50px" name="cmtt_tooltipFontSize"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipFontSize' ); ?>" step="1"/>px
					</td>

					<td colspan="2" class="cm_field_help_container">Set size of font inside tooltip</td>
				</tr>
				<tr>
					<th scope="row">Tooltip padding</th>
					<td><input type="text" name="cmtt_tooltipPadding"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipPadding' ); ?>"/>
					</td>

					<td colspan="2" class="cm_field_help_container">Set internal padding: top, right, bottom, left</td>
				</tr>
				<tr>
					<th scope="row">Tooltip shadow</th>
					<td>
						<input type="hidden" name="cmtt_tooltipShadow" value="0"/>
						<input type="checkbox"
						       name="cmtt_tooltipShadow" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipShadow', 1 ) ); ?>
						       value="1"/>
					</td>

					<td colspan="2" class="cm_field_help_container">Select this option if you like to show the shadow
						for the tooltip
					</td>
				</tr>
				<tr>
					<th scope="row">Tooltip shadow color</th>
					<td>
						<input type="text" class="colorpicker" name="cmtt_tooltipShadowColor"
						       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipShadowColor', '#666666' ); ?>"/>
					</td>

					<td colspan="2" class="cm_field_help_container">Set the color of the shadow of the tooltip</td>
				</tr>
				<tr>
					<th scope="row">Tooltip internal link color</th>
					<td><input type="text" class="colorpicker" name="cmtt_tooltipInternalLinkColor"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipInternalLinkColor' ); ?>"/></td>
					<td colspan="2" class="cm_field_help_container">Set the color of the links inside the tooltip.</td>
				</tr>
				<tr>
					<th scope="row">Tooltip edit link color</th>
					<td><input type="text" class="colorpicker" name="cmtt_tooltipInternalEditLinkColor"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipInternalEditLinkColor' ); ?>"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Set the color of the edit links in the tooltip.
						(Added only when the "Add term editlink to the tooltip content?" is enabled)
					</td>
				</tr>
				<tr>
					<th scope="row">Tooltip mobile link color</th>
					<td><input type="text" class="colorpicker" name="cmtt_tooltipInternalMobileLinkColor"
					           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipInternalMobileLinkColor' ); ?>"/>
					</td>
					<td colspan="2" class="cm_field_help_container">Set color of link to the term page in the tooltip.
						(Added only when the mobile support is enabled and on mobile device)
					</td>
				</tr>

			</table>
		</div>
		<div class="block">
			<h3 class="section-title">
				<span>Tooltip - Animation</span>
				<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
				     fill="#6BC07F">
					<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
				</svg>
			</h3>
			<table class="floated-form-table form-table">
				<!-- Tooltip Animation Time -->
				<tr>
					<th scope="row">Tooltip animation appearance time</th>
					<td>
						<input type="text" style="width:50px" name="cmtt_tooltipDisplayDelay"
						       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayDelay', '0.5' ); ?>"/>s
					</td>

					<td colspan="2" class="cm_field_help_container">Set the animation time for tooltip appearance</td>
				</tr>
				<tr>
					<th scope="row">Tooltip animation disappearance time</th>
					<td>
						<input type="text" style="width:50px" name="cmtt_tooltipHideDelay"
						       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipHideDelay', '0.5' ); ?>"/>s
					</td>

					<td colspan="2" class="cm_field_help_container">Set the animation time for tooltip disappearance
					</td>
				</tr>
				<!-- Tooltip Display Animation -->
				<tr style="clear: left;">
					<th scope="row">Tooltip display animation</th>
					<td>
						<select name="cmtt_tooltipDisplayanimation">
							<option
								value="no_animation" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'no_animation' ) ? 'selected' : ''; ?>>
								No animation
							</option>
							<option
								value="fade_in" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'fade_in' ) ? 'selected' : ''; ?> >
								Fade in
							</option>
							<option
								value="grow" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'grow' ) ? 'selected' : ''; ?> >
								Grow
							</option>
							<option
								value="horizontal_flip" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'horizontal_flip' ) ? 'selected' : ''; ?> >
								Horizontal Flip
							</option>
                            <option
                                    value="vertical_flip" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'vertical_flip' ) ? 'selected' : ''; ?> >
                                Vertical Flip
                            </option>
							<option
								value="center_flip" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'center_flip' ) ? 'selected' : ''; ?> >
								Center Flip
							</option>
                            <option
                                    value="rotation" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'rotation' ) ? 'selected' : ''; ?> >
                                Rotation
                            </option>
                            <option
                                    value="horizontal_rotation" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'horizontal_rotation' ) ? 'selected' : ''; ?> >
                                Horizontal Rotation
                            </option>
                            <option
                                    value="vertical_rotation" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'vertical_rotation' ) ? 'selected' : ''; ?> >
                                Vertical Rotation
                            </option>
						</select>
					</td>

					<td colspan="2" class="cm_field_help_container">Set an animation for when the tooltip appears</td>
				</tr>
				<!-- Tooltip hide animation -->
				<tr>
					<th scope="row">Tooltip hide animation</th>
					<td>
						<select name="cmtt_tooltipHideanimation">
							<option
								value="no_animation" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'no_animation' ) ? 'selected' : ''; ?>>
								No animation
							</option>
							<option
								value="fade_out" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'fade_out' ) ? 'selected' : ''; ?> >
								Fade out
							</option>
							<option
								value="shrink" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'shrink' ) ? 'selected' : ''; ?> >
								Shrink
							</option>
							<option
								value="horizontal_flip" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'horizontal_flip' ) ? 'selected' : ''; ?> >
								Horizontal Flip
							</option>
                            <option
                                    value="vertical_flip" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'vertical_flip' ) ? 'selected' : ''; ?> >
                                Vertical Flip
                            </option>
							<option
								value="center_flip" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'center_flip' ) ? 'selected' : ''; ?> >
								Center Flip
							</option>
                            <option
                                    value="rotation" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'rotation' ) ? 'selected' : ''; ?> >
                                Rotation
                            </option>
                            <option
                                    value="horizontal_rotation" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'horizontal_rotation' ) ? 'selected' : ''; ?> >
                                Horizontal Rotation
                            </option>
                            <option
                                    value="vertical_rotation" <?php echo ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'vertical_rotation' ) ? 'selected' : ''; ?> >
                                Vertical Rotation
                            </option>
						</select>
					</td>

					<td colspan="2" class="cm_field_help_container">Set an animation for when the tooltip disappears
					</td>
				</tr>
			</table>
		</div>
		<?php
		$content = ob_get_clean();

		return $content;
	}


	public static function cmtt_bp_parse( $content, $activity ) {
		global $post;
		$post->ID = $activity->id;

		$content = CMTT_Free::cmtt_glossary_parse( $content, true );

		return $content;
	}

	/*
	 * removing the "glossary" slug from term link
	 *
	 */

	public static function cmtt_remove_glossary_from_link( $url, $id ) {
		global $wp_post_types;

		$post_type = $wp_post_types['glossary'];
		$url       = str_replace( $post_type->rewrite['slug'] . DIRECTORY_SEPARATOR, '', $url );

		return $url;
	}

	public static function cmtt_update_rule_for_term_links( $query ) {
		if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) || empty( $query->query['name'] ) ) {
			return;
		}

		$query->set( 'post_type', array( 'post', 'page', 'glossary' ) );
	}

	public static function maybeDisplayFootnotes( $showTooltips, $glossary_item, $post ) {
		$globalFootnotesEnabled  = \CM\CMTT_Settings::get( 'cmtt_displayTermsAsFootnotes', 0 );
		$footnotesEnabledForPost = CMTT_Free::_get_meta( '_glossary_display_terms_as_footnotes', $post->ID );

		$showFootnotes = $globalFootnotesEnabled || $footnotesEnabledForPost;

		/*
		 * If footnotes are enabled we disable tooltips
		 */
		if ( $showFootnotes ) {
			$showTooltips = false;
			add_filter( 'cmtt_link_replace', array( __CLASS__, 'footnotesDisplaySymbol' ), 100, 6 );
		}

		return $showTooltips;
	}

	public static function footnotesDisplaySymbol( $link_replace, $titleAttr, $glossary_item, $additionalClass, $titlePlaceholder, $title = '' ) {
		/*
		 * Make sure this is one time - otherwise one footnote link can affect other links
		 */
		remove_filter( 'cmtt_link_replace', array( __CLASS__, 'footnotesDisplaySymbol' ), 100, 6 );
		$id                 = $glossary_item->ID;
		$footnoteNumberSpan = self::footnotesPart( $title, $id );
		$link_replace       .= $footnoteNumberSpan;

		return $link_replace;
	}

	public static function footnotesPart( $title, $id ) {
		global $footnotesIndexes, $footnotesSynonyms, $replacedTerms;
		/*
		 * Add synonyms items to the global array so it can be used to generate footnotes definitions  list
		 */
		if ( ! is_array( $footnotesSynonyms ) ) {
			$footnotesSynonyms = array();
		}
		/*
		 *  Check for duplicated synonyms
		 */
		if ( ! empty( $footnotesSynonyms[ $id ]['synonyms'] ) ) {
			$isSynonymInArray = in_array( $title, $footnotesSynonyms[ $id ]['synonyms'] );
		} else {
			$isSynonymInArray = false;
		}

		if ( ! is_array( $footnotesIndexes ) ) {
			$footnotesIndexes = array();
		}
		$synonym_fired = false;

		/*
		 *  Add new synonym to array
		 */
		if ( strtolower( $title ) !== strtolower( $replacedTerms[ $title ]['post']->post_title ) && ! $isSynonymInArray ) {
			$footnotesSynonyms[ $id ]['synonyms'][] = $title;

			if ( ! empty( $footnotesIndexes ) ) {
				$postid_is_in_array = false;
				foreach ( $footnotesIndexes as $key => $value ) {
					if ( $id === $value['postID'] ) {
						$postid_is_in_array = true;
					}
				}
			}
			if ( empty( $footnotesIndexes ) || ! $postid_is_in_array ) {

				$post_title                                  = get_the_title( $id );
				$footnotesIndexes[ $post_title ]['footnote'] = ( count( $footnotesIndexes ) + 1 );
				$footnotesIndexes[ $post_title ]['postID']   = $id;
				$footnotesSynonyms[ $id ]['title'][]         = $post_title;
				$synonym_fired                               = true;
			}
		}

		/*
		 *  Create footnotes  terms numeration array
		 *  Check for used footnotes
		 */
		$postid_is_in_array = false;
		foreach ( $footnotesIndexes as $key => $value ) {
			if ( $id === $value['postID'] ) {
				$postid_is_in_array = true;
			}
		}
		if ( ! array_key_exists( $title, $footnotesIndexes ) && strtolower( $title ) === strtolower( $replacedTerms[ $title ]['post']->post_title ) && ! $postid_is_in_array ) {
			$footnotesIndexes[ $title ]['footnote'] = ( count( $footnotesIndexes ) + 1 );
			$footnotesIndexes[ $title ]['postID']   = $id;
		}

		if ( $synonym_fired ) {
			$footnote_number = $footnotesIndexes[ $footnotesSynonyms[ $id ]['title'][0] ]['footnote'];
		} else {
			$footnote_number = $footnotesIndexes[ $title ]['footnote'];
		}

		if ( \CM\CMTT_Settings::get( 'cmtt_footnoteAestheticsType', 'type1' ) === 'type1' ) {
			$footnoteNumber = '[' . $footnote_number . ']';
		} else {
			$footnoteNumber = '{' . $footnote_number . '}';
		}
		$footnoteLinkStyle  = 'style="font-size: ' . \CM\CMTT_Settings::get( 'cmtt_footnoteSymbolSize', '14px' ) . '; color: ' . \CM\CMTT_Settings::get( 'cmtt_footnoteSymbolColor', '#ff9fbc' ) . '; font-style : ' . \CM\CMTT_Settings::get( 'cmtt_footnoteFormat', 'none' ) . ' ;"';
		$footnoteNumberSpan = '<span id="cmttFootnoteLink' . $footnote_number . '-0" class="cmtt-footnote"><sup><a class="et_smooth_scroll_disabled cmtt_footnote_link cmtt-footnote-deflink" href="#cmttFootnoteLink' . $footnote_number . '" ' . $footnoteLinkStyle . '>' . $footnoteNumber . '</a></sup></span>';

		return $footnoteNumberSpan;
	}

	public static function onFootnotesDefs( $content ) {
		global $post, $footnotesIndexes, $footnotesSynonyms;

		// Prepare footnotes definitions output
		$definitions = '';
		if ( ! empty( $footnotesIndexes ) ) {
			$definitions   .= '<div class="cmtt-footnotes-block">';
			$definitions   .= '<div class="cmtt-footnote-header">' . \CM\CMTT_Settings::get( 'cmtt_footnoteDefTitle', 'Terms definitions' ) . '</div>';
			$definitions   .= '<div class="cmtt-footnote-header-border"></div>';
			$backLinkStyle = 'style="font-size: ' . \CM\CMTT_Settings::get( 'cmtt_footnoteSymbolSize', '14px' ) . '; color: ' . \CM\CMTT_Settings::get( 'cmtt_footnoteSymbolColor', '#ff9fbc' ) . '; font-style : ' . \CM\CMTT_Settings::get( 'cmtt_footnoteFormat', 'none' ) . ' ;"';

			$max      = \CM\CMTT_Settings::get( 'cmtt_footnoteDefMax', 5 );
			$maxLabel = \CM\CMTT_Settings::get( 'cmtt_footnoteDefMaxButtonLabel' );

			foreach ( $footnotesIndexes as $term => $values ) {
				$post_id = $values['postID'];
				if ( ! empty( $footnotesSynonyms[ $post_id ] ) ) {
					$synonyms = ' ( ' . implode( ',', $footnotesSynonyms[ $post_id ]['synonyms'] ) . ' ) ';
				} else {
					$synonyms = '. ';
				}
				$showExcerpt = \CM\CMTT_Settings::get( 'cmtt_footnoteShowExcerpt', false );
				if ( $showExcerpt ) {
					$definitionContent = get_the_excerpt( $post_id );
				} else {
					$definitionContent = get_the_content( null, false, $post_id );
				}
				$stripHTML = \CM\CMTT_Settings::get( 'cmtt_footnoteStripHTML', false );
				if ( $stripHTML ) {
					$definitionContent = strip_tags( $definitionContent );
				}
				/*
				 * Check if the links to term page shouldn't be removed
				 */
				$removeLinksToTerms = CMTT_Free::maybeRemoveLinkToGlossaryTerm( $post );
				if ( ! $removeLinksToTerms ) {
					$termHtml = '<a aria-describedby="tt" href="' . get_the_permalink( $post_id ) . '" class="glossaryLink" target="_blank">' . $term . '</a>';
				} else {
					$termHtml = $term;
				}

				$definitions .= ( 0 === $max ) ? '<button class="cmtt-footnote-showmore-btn">' . $maxLabel . '</button>' : '';
				$hideClass   = ( -- $max < 0 ) ? 'hidden' : '';

				$definitions .= '<div class="cmtt-footnote-def ' . $hideClass . '" id="cmttFootnoteLink' . $values['footnote'] . '">';
				$definitions .= '<span class="cmtt-footnote-def-number">' . $values['footnote'] . '. </span>';
				$definitions .= '<span class="cmtt-footnote-def-back"><a class="cmtt_footnote_link cmtt-footnote-backlink" href="#cmttFootnoteLink' . $values['footnote'] . '-0" ' . $backLinkStyle . '> &#8593; </a></span>';
				$definitions .= '<span class="cmtt-footnote-def-key"> ' . $termHtml . $synonyms . '</span>';
				$definitions .= '<span class="cmtt-footnote-def-content"> ' . apply_filters( 'cmtt_footnotes_definition_content', $definitionContent, $term, $values ) . ' </span>';
				$definitions .= '</div>';
			}
			$definitions .= '</div>';
			$definitions .= '<div class="cmtt-footnote-bottom-border"></div>';
		}
		$content .= $definitions;

		return $content;
	}

	/**
	 * Adds the disables metabox fields
	 *
	 * @param array $metaboxFields
	 *
	 * @return type
	 */
	public static function addDisablesFields( $post ) {
		$termsAsFootnotes        = CMTT_Free::_get_meta( '_glossary_display_terms_as_footnotes', $post->ID );
		$displayTermsAsFootnotes = (int) ( ! empty( $termsAsFootnotes ) && $termsAsFootnotes == 1 );

		echo '<div class="cmtt_disable_for_page_field cmtt-metabox-field">';
		echo '<label for="glossary_display_terms_as_footnotes" class="blocklabel">';
		echo '<input type="checkbox" name="glossary_display_terms_as_footnotes" id="glossary_display_terms_as_footnotes" value="1" ' . checked( 1, $displayTermsAsFootnotes, false ) . '>';
		echo '&nbsp;&nbsp;&nbsp;Overwrite the "Display terms as a footnotes" setting on this page.</label>';
		echo '</div>';
	}

	/**
	 * Saves additional post data
	 *
	 * @param array $content
	 *
	 * @return type
	 */
	public static function saveDisableRelatedPosts( $post_id, $post ) {
		$postType            = isset( $post['post_type'] ) ? $post['post_type'] : '';
		$disableBoxPostTypes = apply_filters( 'cmtt_disable_metabox_posttypes', array( 'glossary', 'post', 'page' ) );
		if ( in_array( $postType, $disableBoxPostTypes ) ) {
			/*
			 * Disables the parsing of the given page
			 */
			$displayTermsAsFootnotes = 0;
			if ( isset( $post['glossary_display_terms_as_footnotes'] ) && $post['glossary_display_terms_as_footnotes'] == 1 ) {
				$displayTermsAsFootnotes = 1;
			}
			update_post_meta( $post_id, '_glossary_display_terms_as_footnotes', $displayTermsAsFootnotes );
		}
	}


	/**
	 * Function adds the page term link at the bottom of the tooltip
	 *
	 * @return string
	 */
	public static function addTermPageLinkToTooltip( $glossaryItemContent, $glossary_item ) {
		$tooltipOnClick          = \CM\CMTT_Settings::get( 'cmtt_glossaryShowTooltipOnClick', '0' );
		$addLink                 = \CM\CMTT_Settings::get( 'cmtt_glossaryAddTermPagelink' );
		$createGlossaryTermPages = (bool) \CM\CMTT_Settings::get( 'cmtt_createGlossaryTermPages', true );
        $truncateSymbol = \CM\CMTT_Settings::get( 'cmtt_glossaryLimitTooltipSymbol', '(...)' );
        $shouldBeAdded = ($addLink == 2) && (strpos($glossaryItemContent,$truncateSymbol) !== false);
		if ( $createGlossaryTermPages && ( $addLink == 1 || $tooltipOnClick == 1 || $shouldBeAdded)) {
			$target = \CM\CMTT_Settings::get( 'cmtt_glossaryTermPageLinkTargetBlank', false ) ? ' target=_blank' : '';
			/*
					 * @since 4.0.13 Add rel="nofollow" to glossary links.
					 */
			$rel              = ( \CM\CMTT_Settings::get( 'cmtt_addNofollowToTermLink' ) == 1 ) ? ' rel="nofollow" ' : '';
			$text             = __( \CM\CMTT_Settings::get( 'cmtt_glossaryTermDetailsLink' ), 'cm-tooltip-glossary' );
			$permalink        = CMTT_Free::get_term_link( $glossary_item->ID );
			$link             = '<a class=glossaryTooltipMoreLink href=' . $permalink . ' ' . $target . $rel . '>' . $text . '</a>';
            if(\CM\CMTT_Settings::get( 'cmtt_glossaryAddTermPagelinkAfterDescription') == 1){
                $glossaryPageLink = ' ' . $link;
            } else {
                $glossaryPageLink = '<div class=glossaryTooltipMoreLinkWrapper>' . $link . '</div>';
            }
			/*
			 * Add the link
			 */
			$glossaryItemContent = $glossaryItemContent . $glossaryPageLink;
		}

		return $glossaryItemContent;
	}

	/**
	 * Adds the related links to the tooltip
	 * @param $glossaryItemContent
	 * @param $glossary_item
	 *
	 * @return mixed|string
	 */
	public static function addRelatedLinkToTooltip( $glossaryItemContent, $glossary_item ) {
		$showLink            = \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipShowLink' );
		$limitArticles       = \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipAmountLinks' );
		$limitCustomArticles = \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipAmountCustomLinks' );
		if ( $showLink == 1 ) {
			if ( ! $limitArticles ) {
				$limitArticles = 5;
			}
			$relatedSnippet      = CMTT_Related::renderRelatedArticles( $glossary_item->ID, $limitArticles, 5, false, false, $limitCustomArticles );
			$glossaryItemContent = $glossaryItemContent . $relatedSnippet;
		}

		return $glossaryItemContent;
	}

	/**
	 * Add the social share buttons
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public static function cmtt_glossaryAddShareBox( $content = '' ) {
		if ( ! defined( 'DOING_AJAX' ) ) {
			ob_start();
			require CMTT_PLUGIN_DIR . 'views/frontend/social_share.phtml';
			$preContent = ob_get_clean();

			$content = $preContent . $content;
		}

		return $content;
	}

	public static function flushCaps( $post, $messages ) {
		$oldRoles = \CM\CMTT_Settings::get( 'cmtt_glossaryRoles', array( 'administrator', 'editor' ) );
		$newRoles = $post['cmtt_glossaryRoles'];
		if ( $oldRoles != $newRoles ) {
			CMTT_Free::_add_caps( $newRoles );
			self::$messages = __( 'New Role assignment has been saved!', 'cm-tooltip-glossary' );
		}
	}

	public static function addSynonymsAndVariations( $additions, $glossary_item, $context, $shortcodeAtts ) {
		if ( ! is_array( $additions ) ) {
			$additions = array();
		}

		if ( 'index' === $context ) {
			$addSynonymsToTheGlossaryIndex = \CM\CMTT_Settings::get( 'cmtt_glossarySynonymsInIndex', 1 );
			$hideSynonyms                  = ! empty( $shortcodeAtts['hide_synonyms'] );
			if ( ! $addSynonymsToTheGlossaryIndex || $hideSynonyms ) {
				return $additions;
			}
			$synonyms = CMTT_Synonyms::getSynonymsArr( $glossary_item->ID, true );
		} else {
			$synonymsArr   = CMTT_Synonyms::getSynonymsArr( $glossary_item->ID, true );
			$variationsArr = CMTT_Synonyms::getSynonymsArr( $glossary_item->ID, false );
			$synonyms      = array_merge( $synonymsArr, $variationsArr );
		}
		$synonymsNormalized = array();

		if ( ! empty( $synonyms ) && count( $synonyms ) > 0 ) {
			foreach ( $synonyms as $val ) {
			    if(\CM\CMTT_Settings::get( 'cmtt_normalizationSynonymsEnabled', 1 )) {
                    $val = CMTT_Free::normalizeTitle($val, true, true);
                } else {
                    $val = htmlspecialchars( trim( $val ), ENT_QUOTES, 'UTF-8' );
                }
				if ( ! empty( $val ) ) {
					$synonymsNormalized[] = $val;
				}
			}
			if ( ! empty( $synonymsNormalized ) ) {
				$additions = array_merge( $additions, $synonymsNormalized );
			}
		}

		return $additions;
	}

	/**
	 * Add tooltip script data
	 *
	 * @param array $tooltipData
	 *
	 * @return type
	 */
	public static function addTooltipScriptData( $tooltipData ) {
		$tooltipData['mobile_support'] = (bool) \CM\CMTT_Settings::get( 'cmtt_glossaryMobileSupport' );

		return $tooltipData;
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
            'cards-view',
			'language-table',
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
	 * Outputs the glossary item description
	 *
	 * @param type $glossaryItemDesc
	 * @param type $glossary_item
	 * @param type $glossaryIndexStyle
	 *
	 * @return type
	 */
	public static function wrapGlossaryIndexItemDesc( $glossaryItemDesc, $glossary_item, $glossaryIndexStyle, $shortcodeAtts ) {
		if ( empty( $glossaryItemDesc ) ) {
			return $glossaryItemDesc;
		}

		$glossaryItemDescBaseAttributes = '';
		$structuredData                 = \CM\CMTT_Settings::get( 'cmtt_add_structured_data_term_page', 1 ) ? ' itemprop="description" ' : '';
		$glossaryItemDescBaseAttributes .= $structuredData;

		$glossaryItemDesc = '<dfn class="glossary_itemdesc" role="definition" ' . $glossaryItemDescBaseAttributes . '>' . do_shortcode( $glossaryItemDesc ) . '</dfn>';

		return $glossaryItemDesc;
	}

    public static function countGlossaryTerms($atts = array()){
        global $wpdb;
        $res = '';
        $sql = "SELECT Count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'glossary'";
        $res = $wpdb->get_var( $sql );
        return $res;
    }

	public static function selectTheGlossaryIndexStyle($indexStyle){
		if('classic' === $indexStyle){
			$withDefinition = \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipDesc', 0);
			if($withDefinition){
				$indexStyle = 'classic-definition';
			}
		}
		return $indexStyle;
	}

    public static function setUpOptimization(){
	    $optimized_options = [
            'cmtt_glossaryEnableQuickScan' => 1,
            'cmtt_forceLoadScripts' => 0,
            'cmtt_glossaryOnMainQuery' => 1,
            'cmtt_removeGlossaryCreateListFilter' => 1,
            'cmtt_glossaryEnableCaching' => 1,
            'cmtt_glossaryEnableCachingIndex' => 1,
            'cmtt_glossaryEnableCachingPosts' => 1,
            'cmtt_glossaryEnablePreCaching' => 1,
            'cmtt_glossaryCachingExpiration' => 30,
//          'cmtt_glossaryCompressCaches' => 0, /* Ecommerce only */
            'cmtt_glossaryClearCaches' => 0,
            'cmtt_glossaryRemoveExcerptParsing' => 1,
            'cmtt_glossaryTooltipHashContent' => 1,
            'cmtt_disableDOMParser' => 0,
            'cmtt_tooltipParsingPriority' => 20000,
            'cmtt_disableMinifiedTooltip' => 0,
            'cmtt_glossaryTurnOnAmp' => 0,
            'cmtt_glossaryParseOnGlossarySearch' => 0,
            'cmtt_glossaryEnableAjaxComplete' => 0,
            'cmtt_script_in_footer' => 1,
            'cmtt_glossaryTermsInComments' => 0,
            'cmtt_glossaryParseTextWidget' => 0,
            'cmtt_glossaryRunApiCalls' => 0,
            'cmtt_disableLoaderAnimation' => 1,
            'cmtt_indexFastFilter' => 1,
            'cmtt_perPage' => 100,
            'cmtt_glossaryServerSidePagination' => 1,
            'cmtt_tooltipHideanimation' => 'no_animation',
            'cmtt_tooltipDisplayanimation' => 'no_animation',
        ];
        $previous_values = get_option('cmtt_pre_optimized_options') ?? [];
        if(!empty($previous_values)) {
            foreach ($previous_values as $name => $value) {
                \CM\CMTT_Settings::set($name, $value);
            }
            delete_option('cmtt_pre_optimized_options');
        } else {
            foreach ($optimized_options as $name => $value) {
                $previous_values[$name] = \CM\CMTT_Settings::get($name);
                \CM\CMTT_Settings::set($name, $value);
            }
            update_option('cmtt_pre_optimized_options', $previous_values);
        }
    }
}
