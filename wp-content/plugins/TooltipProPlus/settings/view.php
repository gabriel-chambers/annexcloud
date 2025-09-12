<?php echo do_shortcode( '[cminds_pro_ads id="cmtt"]' ); ?>

<br>
<?php if ( ! empty( $messages ) ) : ?>
	<div class="updated" style="clear:both"><p><?php echo $messages; ?></p></div>
<?php endif; ?>
<br>

<div id="cminds_settings_container">
	<div class="cminds_settings_description">
		<p>
			<?php
			global $cmindsPluginPackage;
			$shortcodesPage = $cmindsPluginPackage['cmtt']->licensingApi->getPageSlug( 'shortcodes' );
			?>
			<strong>Supported Shortcodes:</strong> <a
				href="<?php echo get_admin_url( '', 'admin.php?page=' . esc_attr( $shortcodesPage ) ); ?>">See the
				list</a>
		</p>

		<p>
			<?php
			$glossaryId                = CMTT_Glossary_Index::getGlossaryIndexPageId();
			if ( $glossaryId > 0 && get_post( $glossaryId ) ) :

				$glossaryIndexPageEditLink = admin_url( 'post.php?post=' . $glossaryId . '&action=edit' );
				$glossaryIndexPageLink = get_page_link( $glossaryId );
				?>
				<strong>Link to the Glossary Index Page:</strong> <a href="<?php echo $glossaryIndexPageLink; ?>"
				                                                     target="_blank"><?php echo $glossaryIndexPageLink; ?></a> (
				<a title="Edit the Glossary Index Page" href="<?php echo $glossaryIndexPageEditLink; ?>">edit</a>)
			<?php
			endif;
			?>
		</p>
		<p>
			<strong>Example of Glossary Term
				link:</strong> <?php echo trailingslashit( home_url( \CM\CMTT_Settings::get( 'cmtt_glossaryPermalink' ) ) ) . 'sample-term'; ?>
		</p>
		<form method="post">
			<div>
				<div class="cm_field_help_container">Warning! This option will completely erase all of the data stored
					by the CM Tooltip Glossary in the database: terms, options, synonyms etc. <br/> It will also remove
					the Glossary Index Page. <br/> It cannot be reverted.
				</div>
                <?php wp_nonce_field( 'remove-options-items' ); ?>
				<input
					onclick="return confirm('All options of CM Tooltip Glossary will be erased. This cannot be reverted.')"
					type="submit" name="cmtt_removeAllOptions" value="Remove all options"
					class="button cmtt-cleanup-button"/>
				<input
					onclick="return confirm('All terms of CM Tooltip Glossary will be erased. This cannot be reverted.')"
					type="submit" name="cmtt_removeAllItems" value="Remove all items"
					class="button cmtt-cleanup-button"/>
				<span style="display: inline-block;position: relative;"></span>
			</div>
		</form>

		<?php
		// check permalink settings
		if ( \CM\CMTT_Settings::get( 'permalink_structure' ) == '' ) {
			echo '<span style="color:red">Your WordPress Permalinks needs to be set to allow plugin to work correctly. Please Go to <a href="' . admin_url() . 'options-permalink.php" target="new">Settings->Permalinks</a> to set Permalinks to Post Name.</span><br><br>';
		}
		?>

	</div>

	<br/>
	<div class="clear"></div>

	<form method="post" id="cminds_settings_form">

		<div id="cminds_settings_search--container">
			<input id="cminds_settings_search" placeholder="Search in settings..."><span
				id="cminds_settings_search_clear">&times;</span>
		</div>

		<?php wp_nonce_field( 'update-options' ); ?>
		<input type="hidden" name="action" value="update"/>

		<div id="cm_settings_tabs" class="glossarySettingsTabs">
			<div class="glossary_loading"></div>

			<?php
			\CM\CMTT_Settings::renderSettingsTabsControls();

			\CM\CMTT_Settings::renderSettingsTabs();
			?>
			<div id="tabs-1" class="settings-tab">
				<div class="cminds_settings_toggle_tabs cminds_settings_toggle-opened">Toggle All</div>
				<div class="block">
					<h3 class="section-title">
						<span>General Settings</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr class="whole-line">
							<th scope="row">Glossary Index Page ID</th>
							<td>
								<?php
								wp_dropdown_pages(
									array(
										'name'              => 'cmtt_glossaryID',
										'selected'          => (int) \CM\CMTT_Settings::get( 'cmtt_glossaryID', - 1 ),
										'show_option_none'  => '-None-',
										'option_none_value' => '0',
									)
								);
								?>
								<br/><input type="checkbox" name="cmtt_glossaryID" value="-1"/> Generate page for
								Glossary Index
							</td>
							<td colspan="2" class="cm_field_help_container">Select the page ID of the page you would
								like to use as the Glossary Index Page. If you select "-None-" terms will still be
								highlighted in relevant posts/pages but there won't be a central list of terms (Glossary
								Index Page). If you check the checkbox a new page would be generated automatically.
								WARNING! You have to manually remove old pages!
							</td>
						</tr>
                        <tr class="whole-line">
                            <th scope="row">Glossary Archive Page ID</th>
                            <td class="field-select">
                                <?php
                                wp_dropdown_pages(
                                    array(
                                        'name'              => 'cmtt_glossaryArchiveID',
                                        'selected'          => (int) \CM\CMTT_Settings::get( 'cmtt_glossaryArchiveID' ),
                                        'show_option_none'  => '-None-',
                                        'option_none_value' => '0',
                                    )
                                );
                                ?>
                                <br/><input type="checkbox" name="cmtt_glossaryArchiveID" value="-1"/> Generate page for
                                Glossary Archive
                            </td>
                            <td colspan="2" class="cm_field_help_container">Select the page ID of the page you would
                                like to use as the Glossary Archive Page. This page is used by the Alphabetical Archive Glossary Widget. If you check the checkbox a new page would be generated automatically.
                                WARNING! You have to manually remove old pages!
                            </td>
                        </tr>
						<tr>
							<th scope="row">Roles allowed to add/edit terms:</th>
							<td class="field-multiselect">
								<input type="hidden" name="cmtt_glossaryRoles" value="0"/>
								<?php
								echo CMTT_Free::outputRolesList( 'cmtt_glossaryRoles', array(
									'administrator',
									'editor'
								), false, 1 );
								?>
							</td>
							<td colspan="2" class="cm_field_help_container">Select the custom post types where you'd
								like the Glossary Terms to be highlighted.
							</td>
						</tr>
						<tr>
							<th scope="row">Create Glossary Term Pages:</th>
							<td>
								<input type="hidden" name="cmtt_createGlossaryTermPages" value="0"/>
								<input type="checkbox"
								       name="cmtt_createGlossaryTermPages" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_createGlossaryTermPages', true ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Uncheck this if you don't want the Glossary
								Term pages to be created. <strong>After disabling this all of the links to the Glossary
									Term pages will be removed.</strong></td>
						</tr>
						<tr>
							<th scope="row">Exclude Glossary Term Pages from search:</th>
							<td>
								<input type="hidden" name="cmtt_excludeGlossaryTermPagesFromSearch" value="0"/>
								<input type="checkbox"
								       name="cmtt_excludeGlossaryTermPagesFromSearch" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_excludeGlossaryTermPagesFromSearch', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Check this if you don't want the Glossary
								Term pages to be displayed in the search results.
							</td>
						</tr>
						<tr>
							<th scope="row">Glossary Terms Permalink</th>
							<td><input type="text" name="cmtt_glossaryPermalink"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryPermalink', 'glossary' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enter the name you would like to use for the
								permalink to the Glossary Terms.
								By default this is "glossary", however you can update this if you wish.
								If you are using a parent please indicate this in path eg. "/path/glossary", otherwise
								just leave glossary or the name you have chosen.
								<br/><br/>
								The permalink of the Glossary Index Page will change automatically, but you can change
								it manually (if you like) using the "edit" link near the "Link to the Glossary Index
								Page" above.
								<br/><br/><strong>WARNING! If you already use this permalink the plugin's behavior may
									be unpredictable.</strong>
								<br/><strong>This option cannot be empty.</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Glossary Categories Permalink</th>
							<td><input type="text" name="cmtt_glossaryCategoriesPermalink"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryCategoriesPermalink', 'glossary-categories' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enter the name you would like to use for the
								permalink for the Glossary Categories.
								By default this is "glossary-categories", however you can update this if you wish.
								If you are using a parent please indicate this in path eg. "/path/glossary-categories",
								otherwise just leave glossary-categories or the name you have chosen.
							</td>
						</tr>
						<tr>
							<th scope="row">Glossary Tags Permalink</th>
							<td><input type="text" name="cmtt_glossaryTagsPermalink"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryTagsPermalink', 'glossary-tags' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enter the name you would like to use for the
								permalink to the Glossary Tags.
								By default this is "glossary-tags", however you can update this if you wish.
								If you are using a parent please indicate this in path eg. "/path/glossary-tags",
								otherwise
								just leave glossary-tags or the name you have chosen.
							</td>
						</tr>
						<tr>
							<th scope="row">Enable RTL Support</th>
							<td>
								<input type="hidden" name="cmtt_glossaryRTL" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryRTL" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryRTL', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enable right to left text for CM Tooltip
							</td>
						</tr>
						<tr>
							<th scope="row">Glossary Breadcrumbs Title</th>
							<td><input type="text" name="cmtt_glossaryBreadcrumbs"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryBreadcrumbs', CMTT_NAME ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enter the name for the plugin's post type,
								which is usually displayed in the breadcrumbs.
								By default this is the post type name, however you can change it here to anything you
								want.
							</td>
						</tr>
						<tr>
							<th scope="row">Limit number of characters for the description column text in the Glossary
								List Table
							</th>
							<td>
								<input type="hidden" name="cmtt_show_desc_inlist_table" value="0"/>
								<input type="number" min="0" step="1"
								       name="cmtt_show_desc_inlist_table"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_show_desc_inlist_table', 0 ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show a
								limited
								number of characters in the description column. If the value set to 0, the description
								column will be hidden.
							</td>
						</tr>
                        <tr>
                            <th scope="row">Optimize for Speed</th>
                            <td>
                                <input type="submit" name="cmtt_glossaryOptimizeForSpeed" value="<?php echo !empty(get_option('cmtt_pre_optimized_options')) ? "Cancel optimization" : "Optimize" ; ?>"
                                       class="button"
                                       style="background: #6bc07f; border-color: #6bc07f; font-weight: bold; color:#fff"/>
                                <br/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Click this button if you would like to optimize the plugin.
                            </td>
                        </tr>
					</table>
					<div class="clear"></div>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Abbreviations Settings</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Abbreviations brackets</th>
							<td>
								<select name="cmtt_roundBracketsAbbr">
									<option
										value="square" <?php selected( 'square', \CM\CMTT_Settings::get( 'cmtt_roundBracketsAbbr' ) ); ?>>
										Square[]
									</option>
									<option
										value="round" <?php selected( 'round', \CM\CMTT_Settings::get( 'cmtt_roundBracketsAbbr' ) ); ?>>
										Round()
									</option>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">Change abbreviation square brackets to round
							</td>
						</tr>
						<tr>
							<th scope="row">Display abbreviations after term title</th>
							<td>
								<input type="hidden" name="cmtt_glossaryAbbreviationsInTitle" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryAbbreviationsInTitle" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryAbbreviationsInTitle', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Disable showing abbreviations in the
								Glossary
							</td>
						</tr>
                        <tr>
                            <th scope="row">Display abbreviations next to the term title in Glossary Index</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryAbbreviationsInTitleOnIndex" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_glossaryAbbreviationsInTitleOnIndex" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryAbbreviationsInTitleOnIndex', '0' ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Enable this option if you want to display the abbreviation after the term title on the Glossary Index
                            </td>
                        </tr>
						<tr>
							<th scope="row">Display abbreviations in Glossary Index</th>
							<td>
								<input type="hidden" name="cmtt_glossaryAbbreviationsInIndex" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryAbbreviationsInIndex" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryAbbreviationsInIndex', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enable this option to show abbreviations in the Glossary Index
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Alternative Meanings Settings</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Allow for Alternative Meanings</th>
							<td>
								<input type="hidden" name="cmtt_alternativeMeaningsAllow" value="0"/>
								<input type="checkbox"
								       name="cmtt_alternativeMeaningsAllow" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_alternativeMeaningsAllow', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is unchecked it will be
								impossible to add more than one Glossary Term with the same title.
							</td>
						</tr>
						<tr>
							<th scope="row">Display Alternative Meanings in Tooltips</th>
							<td>
								<input type="hidden" name="cmtt_alternativeMeaningsInTooltips" value="0"/>
								<input type="checkbox"
								       name="cmtt_alternativeMeaningsInTooltips" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_alternativeMeaningsInTooltips', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then the
								Alternative
								Meaning descriptions will be displayed in the tooltips.
							</td>
						</tr>
						<tr>
							<th scope="row">Display Alternative Meanings in Footnotes</th>
							<td>
								<input type="hidden" name="cmtt_alternativeMeaningsInFootnotes" value="0"/>
								<input type="checkbox"
								       name="cmtt_alternativeMeaningsInFootnotes" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_alternativeMeaningsInFootnotes', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then the
								Alternative
								Meaning descriptions will be displayed in the footnotes.
							</td>
						</tr>
						<tr>
							<th scope="row">Display Alternative Meanings on Glossary Term Page</th>
							<td>
								<input type="hidden" name="cmtt_alternativeMeaningsInGlossaryTermPage" value="0"/>
								<input type="checkbox"
								       name="cmtt_alternativeMeaningsInGlossaryTermPage" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_alternativeMeaningsInGlossaryTermPage', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled the Alternative
								Meanings will be displayed on the bottom of the Glossary Term page.
							</td>
						</tr>
						<tr>
							<th scope="row">Alternative Meanings content length on Glossary Term Page</th>
							<td>
								<input type="hidden" name="cmtt_alternativeMeaningsInGlossaryTermPageLength" value="0"/>
								<input type="number"
								       name="cmtt_alternativeMeaningsInGlossaryTermPageLength"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_alternativeMeaningsInGlossaryTermPageLength', 2000 ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select a number of characters of the Alternative
								Meanings content will be displayed on the Glossary Term page.
							</td>
						</tr>
					</table>
					<div class="clear"></div>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Advanced Custom Fields Settings</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Highlight terms in ACF fields?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryParseACFFields" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryParseACFFields" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseACFFields' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container"> Select this option if you wish to highlight
								Glossary Terms in ALL of the "Advanced Custom Fields" fields.
							</td>
						</tr>
						<tr>
							<th scope="row">Types of fields to highlight:</th>
							<td class="field-multiselect">
								<input type="hidden" name="cmtt_acf_parsed_field_types" value="0"/>
								<?php
								echo CMTT_Free::outputACFTypesList( 'cmtt_acf_parsed_field_types', array(), 1 );
								?>
							</td>
							<td colspan="2" class="cm_field_help_container">Select the types of ACF fields in which
								you'd like the Glossary Terms to be highlighted.
							</td>
						</tr>
						<tr>
							<th scope="row">Types of fields to remove the WP functions:</th>
							<td class="field-multiselect">
								<input type="hidden" name="cmtt_acf_remove_filters_for_type" value="0"/>
								<?php
								echo CMTT_Free::outputACFTypesList( 'cmtt_acf_remove_filters_for_type', array( 'text' ), 1 );
								?>
							</td>
							<td colspan="2" class="cm_field_help_container">Select the types of ACF fields for which the
								built in WP filters adding paragraphs and newlines should be removed.
							</td>
						</tr>
						<tr>
							<th scope="row">Types of fields to wrap with &lt;span&gt; tag:</th>
							<td class="field-multiselect">
								<input type="hidden" name="cmtt_acf_wrap_in_span_for_type" value="0"/>
								<?php
								echo CMTT_Free::outputACFTypesList( 'cmtt_acf_wrap_in_span_for_type', array(
									'text',
									'checkbox'
								), 1 );
								?>
							</td>
							<td colspan="2" class="cm_field_help_container">Select the types of ACF fields which should
								be wrapped
								with &lt;span&gt; tag.
							</td>
						</tr>
						<tr>
							<th scope="row">Don't use the DOM parser for ACF fields?</th>
							<td>
								<input type="hidden" name="cmtt_disableDOMParserForACF" value="0"/>
								<input type="checkbox"
								       name="cmtt_disableDOMParserForACF" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_disableDOMParserForACF', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to parse the
								ACF fields using the simple parser (preg_replace) instead of DOM parser. Warning! May
								break content.
							</td>
						</tr>
						<tr>
							<th scope="row" valign="middle"
							    align="left"><?php _e( 'Excluded ACF Field IDs', 'cm-tooltip-glossary' ); ?>:
							</th>
							<td>
								<input type="text" name="cmtt_disableACFfields"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_disableACFfields' ); ?>"
								       placeholder="<?php _e( 'field_id,field_2_id', 'cm-tooltip-glossary' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">You can put here the comma separated list of
								IDs of the ACF fields you would like to exclude from being parsed.
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Term highlighting</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Highlight terms on given post types:</th>
							<td class="field-multiselect">
								<input type="hidden" name="cmtt_glossaryOnPosttypes" value="0"/>
								<?php
								echo CMTT_Free::outputCustomPostTypesList( 'cmtt_glossaryOnPosttypes', 1 );
								?>
							</td>
							<td colspan="2" class="cm_field_help_container">Select the custom post types where you'd
								like the Glossary Terms to be highlighted.
							</td>
						</tr>
						<tr>
							<th scope="row">Only show terms on single posts/pages (not Homepage, authors etc.)?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryOnlySingle" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryOnlySingle" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryOnlySingle' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish to only
								highlight glossary terms when viewing a single page/post.
								This can be used so terms aren't highlighted on your homepage, or author pages and other
								taxonomy related pages.
							</td>
						</tr>
						<tr>
							<th scope="row">Only highlight white-listed and non-blacklisted terms?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryOnlyListed" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryOnlyListed" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryOnlyListed', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish to only
								highlight glossary terms on posts/pages, which have: white-listed terms/categories or
								black-listed terms/categories.
								This means adding the terms to the glossary won't be enough to highlight.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight terms in bbPress replies?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryParseBBPressFields" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryParseBBPressFields" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseBBPressFields' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container"> Select this option if you wish to highlight
								Glossary Terms in ALL of the "bbPress" replies.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight terms on BuddyPress pages?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryParseBuddyPressPages" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryParseBuddyPressPages" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseBuddyPressPages', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container"> Select this option if you wish to highlight
								Glossary Terms in ALL of the "bbPress" replies.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight terms in BuddyBoss activity content?</th>
							<td>
								<input type="hidden" name="cmtt_parseBuddyBossActivityContent" value="0"/>
								<input type="checkbox"
								       name="cmtt_parseBuddyBossActivityContent" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_parseBuddyBossActivityContent', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container"> Select this option if you wish to highlight
								Glossary Terms in the BuddyBoss activity content.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight first term occurrence only?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryFirstOnly" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryFirstOnly" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryFirstOnly' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to only highlight the first occurrence of each term on a
								page/post.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight first term occurrence - exception tag</th>
							<td>
								<input type="text" name="cmtt_firstOnlyExceptionTag" placeholder="strong"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_firstOnlyExceptionTag', false ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Use this option if you would like to have an exception tag for the <cite>"Highlight
									first term occurrence only?"</cite> option.<br>
								Value should be a single HTML tag name eg. 'strong'. Every occurrence of terms directly
								wrapped with the selected tag, will be highlighted.<br>
								<strong>WARNING: Works with <cite>"Highlight first term occurrence only?" and "Highlight
										every nth occurrence only?"</cite>
									enabled.</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight first term occurrence - exception class</th>
							<td>
								<input type="text" name="cmtt_firstOnlyExceptionClass" placeholder="strong"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_firstOnlyExceptionClass', false ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Use this option if you would like to have an exception class for the <cite>"Highlight
									first term occurrence only?"</cite> option.<br>
								Value should be a single HTML class name eg. 'schema-faq-answer'. Every occurrence of
								terms directly wrapped with the selected tag, will be highlighted.<br>
								<strong>WARNING: Works with <cite>"Highlight first term occurrence only?" and "Highlight
										every nth occurrence only?"</cite>
								</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight each variations of the term?</th>
							<td>
								<input type="hidden" name="cmtt_firstOnlyIncludingVariations" value="0"/>
								<input type="checkbox"
								       name="cmtt_firstOnlyIncludingVariations" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_firstOnlyIncludingVariations', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to only
								highlight the first occurrence of each of the terms variantion.
								eg. if the term is "HTML" and it appears in the content as both "HTML" and "html", both
								will be highlighted. <br>
								Disable if you want to highlight the first occurrence of any of the variants.
							</td>
						</tr>
                        <tr>
							<th scope="row">Highlight each synonyms of the term?</th>
							<td>
								<input type="hidden" name="cmtt_firstOnlyIncludingSynonyms" value="0"/>
								<input type="checkbox"
								       name="cmtt_firstOnlyIncludingSynonyms" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_firstOnlyIncludingSynonyms', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to only
								highlight the first occurrence of each of the terms synonym.
								eg. if the term is "HTML" and it appears in the content as both "HTML" and "html", both
								will be highlighted. <br>
								Disable if you want to highlight the first occurrence of any of the synonyms.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight every nth occurrence only?</th>
							<td>
								<input type="number" name="cmtt_tooltipReplaceEveryNth"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipReplaceEveryNth', 1 ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to only highlight every nth occurrence of a term(or it's
								synonyms/variations) on a page/post. <br>
								Setting it to 3 will mean that only every third occurrence will be highlighted. Set to 1
								to highlight all occurrences.<br>
								<strong>WARNING: Doesn't work with <cite>"Highlight first term occurrence only?"</cite>
									enabled.</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight only space-separated terms?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryOnlySpaceSeparated" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryOnlySpaceSeparated" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryOnlySpaceSeparated' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to only
								search for the terms separated from other words (usually by space).
							</td>
						</tr>
                        <tr>
                            <th scope="row">Highlight hyphenated terms separately?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryParseHyphenSeparated" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_glossaryParseHyphenSeparated" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseHyphenSeparated' ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Select this option if you want to search
                                for terms that are parts of hyphenated words.
                            </td>
                        </tr>
						<tr>
							<th scope="row">Highlight the terms in comments</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTermsInComments" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTermsInComments" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTermsInComments' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to highlight
								the glossary terms in the comments.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight the terms in Text Widget</th>
							<td>
								<input type="hidden" name="cmtt_glossaryParseTextWidget" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryParseTextWidget" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseTextWidget', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to highlight
								the glossary terms in the Text Widget built in WordPress.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight the terms in WPBakery</th>
							<td>
								<input type="hidden" name="cmtt_glossaryParseWPBakery" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryParseWPBakery" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseWPBakery', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to highlight
								the glossary terms in the WPBakery shortcodes.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight the terms in Oxygen Builder</th>
							<td>
								<input type="hidden" name="cmtt_glossaryParseOxygenBuilder" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryParseOxygenBuilder" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseOxygenBuilder', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to highlight
								the glossary terms in the Oxygen Builder templates. <strong>WARNING: this include the
									header/footer templates, use with caution</strong>.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight the terms in Ninja Tables</th>
							<td>
								<input type="hidden" name="cmtt_glossaryParseNinjaTables" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryParseNinjaTables" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseNinjaTables', 0 ) ); ?>
								       value="1"/>

							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to highlight
								the glossary terms in the Ninja Tables.
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight the terms on category/tag pages</th>
							<td>
								<input type="hidden" name="cmtt_glossaryHighlightInArchive" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryHighlightInArchive"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryHighlightInArchive', 1 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to highlight the glossary terms on category/tag pages.
							</td>
						</tr>
						<tr>
							<th scope="row">Exclude HTML tags from parsing:</th>
							<td class="field-multiselect">
								<?php
								$excluded_tags = \CM\CMTT_Settings::get( 'cmtt_glossaryProtectedTags', array(
									'all_h',
									'h1',
									'a',
									'other'
								) );
								if ( $excluded_tags == 1 ) {
									$excludedTags = array( 'all_h', 'h1', 'a', 'other' );
								}
								if ( empty( $excluded_tags ) ) {
									$excluded_tags = array();
								}
								if ( ! is_array( $excluded_tags ) ) {
									$excluded_tags = array( $excluded_tags );
								}
								$options = array(
									'all_h' => 'All heading tags (h1-h6)',
									'h1'    => '&lt;h1&gt;',
									'a'     => '&lt;a&gt;',
									'other' => 'Other (header, pre, object, textarea)',
								);
								echo CMTT_Free::_outputMultipleValues( 'cmtt_glossaryProtectedTags', $options, $excluded_tags, 1 );
								?>
							</td>
							<td colspan="2" class="cm_field_help_container">Select which tags you don't need to parse.
								Uncheck all if you need to parse all tags
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Excluded HTML Classes', 'cm-tooltip-glossary' ); ?>:</th>
							<td>
								<input type="text" name="cmtt_glossaryParseExcludedClasses"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryParseExcludedClasses' ); ?>"
								       placeholder="<?php _e( 'class_1,class_2', 'cm-tooltip-glossary' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">You can put here the comma separated list of
								IDs of the HTML classes you would like to exclude from being parsed.
							</td>
						</tr>
						<tr id="exclude-html-tags-row">
							<th scope="row"><?php _e( 'Excluded HTML tags', 'cm-tooltip-glossary' ); ?>:</th>
							<td>
								<input type="text" name="cmtt_glossaryParseExcludedTags"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryParseExcludedTags' ); ?>"
								       placeholder="<?php _e( 'h1,h2,h3', 'cm-tooltip-glossary' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">You can put here the comma separated list of
								tags you would like to exclude from being parsed.
							</td>
						</tr>
						<tr>
							<th scope="row">Exclude hyphenated words</th>
							<td>
								<input type="hidden" name="cmtt_glossaryExcludeHyphenatedWords" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryExcludeHyphenatedWords"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryExcludeHyphenatedWords', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you wish to exclude hyphenated words, like
								<i>camera-ready</i>, <i>up-to-date</i> <i>and well-known</i>
							</td>
						</tr>
						<tr>
							<th scope="row">Exclude words in double quotes</th>
							<td>
								<input type="hidden" name="cmtt_glossaryInDoubleQuotes" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryInDoubleQuotes"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryInDoubleQuotes', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you wish to exclude words in double quotes
							</td>
						</tr>
						<tr>
							<th scope="row">Highlight terms on it's own page</th>
							<td>
								<input type="hidden" name="cmtt_highlightTermOnItsOwnPage" value="0"/>
								<input type="checkbox"
								       name="cmtt_highlightTermOnItsOwnPage"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_highlightTermOnItsOwnPage', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you wish to highlight term on it's own page
							</td>
						</tr>
                        <tr>
                            <th scope="row">Highlight terms on its alternative meaning pages</th>
                            <td>
                                <input type="hidden" name="cmtt_highlightTermOnItsAlternativeMeaningsPage" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_highlightTermOnItsAlternativeMeaningsPage" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_highlightTermOnItsAlternativeMeaningsPage', 1 ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Select this option if you wish to highlight term on its alternative
                                meanings pages.
                            </td>
                        </tr>
					</table>
					<div class="clear"></div>
				</div>

				<div class="block">
					<h3 class="section-title">
						<span>Footnotes display settings</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row" valign="middle" align="left">Display terms as a footnotes:</th>
							<td>
								<input type="hidden" name="cmtt_displayTermsAsFootnotes" value="0"/>
								<input type="checkbox"
								       name="cmtt_displayTermsAsFootnotes" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_displayTermsAsFootnotes' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enable show terms not as links with tooltips
								but as footnotes with definitions below the main post content</td>
						</tr>
						<tr>
							<th scope="row" valign="middle" align="left">Display style :</th>
							<td>
								<label for="cmtt_footnoteAestheticsType"></label>
								<select name="cmtt_footnoteAestheticsType">
									<option
										value="type1" <?php selected( 'type1', \CM\CMTT_Settings::get( 'cmtt_footnoteAestheticsType' ) ); ?>>
										Square brackets
									</option>
									<option
										value="type2" <?php selected( 'type2', \CM\CMTT_Settings::get( 'cmtt_footnoteAestheticsType' ) ); ?>>
										Curly brackets
									</option>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">How the reference link is displayed in the
								Front-End</td>
						</tr>
						<tr class="whole-line">
							<th scope="row" valign="middle" align="left">Footnote link styles</th>
							<td>
								<label for="cmtt_footnoteSymbolSize">Font size
									<input type="text" name="cmtt_footnoteSymbolSize"
									       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_footnoteSymbolSize' ); ?>"
									       style="width:60px;"/>
								</label>
								<label for="cmtt_footnoteSymbolColor">
									Color
									<input type="color" name="cmtt_footnoteSymbolColor"
									       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_footnoteSymbolColor' ); ?>"/>
								</label>
								<label for="cmtt_footnoteFormat">
									Style
									<select name="cmtt_footnoteFormat" style="width: 100px;">
										<option
											value="none" <?php selected( 'none', \CM\CMTT_Settings::get( 'cmtt_footnoteFormat' ) ); ?>>
											None
										</option>
										<option
											value="bold" <?php selected( 'bold', \CM\CMTT_Settings::get( 'cmtt_footnoteFormat' ) ); ?>>
											Bold
										</option>
										<option
											value="italic" <?php selected( 'italic', \CM\CMTT_Settings::get( 'cmtt_footnoteFormat' ) ); ?>>
											Italic
										</option>
									</select>
								</label>
                                <label for="cmtt_footnoteBackgroundColor">
                                    Background
                                    <input type="color" name="cmtt_footnoteBackgroundColor"
                                           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_footnoteBackgroundColor', '#eaf3ff' ); ?>"/>
                                </label>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose the styles for the link from where
								the term is found.
							</td>
						</tr>
						<tr>
							<th scope="row" valign="middle" align="left">Display categories in footnotes</th>
							<td>
								<input type="hidden" name="cmtt_footnoteShowCategories" value="0"/>
								<input type="checkbox"
								       name="cmtt_footnoteShowCategories" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_footnoteShowCategories', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If enabled, the bottom footnote definition
                                will display a category that the term belongs to</td>
						</tr>
						<tr>
							<th scope="row" valign="middle" align="left">Use excerpt for bottom definition</th>
							<td>
								<input type="hidden" name="cmtt_footnoteShowExcerpt" value="0"/>
								<input type="checkbox"
								       name="cmtt_footnoteShowExcerpt" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_footnoteShowExcerpt' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">When enabled bottom footnote definition will
								display terms excerpt instead of full definition</td>
						</tr>
						<tr>
							<th scope="row" valign="middle" align="left">Strip HTML from the bottom footnote
								definition
							</th>
							<td>
								<input type="hidden" name="cmtt_footnoteStripHTML" value="0"/>
								<input type="checkbox"
								       name="cmtt_footnoteStripHTML" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_footnoteStripHTML' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">When enabled bottom footnote definition will
								have all of it's HTML removed, leaving just the plain text.</td>
						</tr>
						<tr>
							<th scope="row">Footnotes definitions
								title
							</th>
							<td>
								<label for="cmtt_footnoteDefTitle"></label>
								<input type="text" name="cmtt_footnoteDefTitle"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_footnoteDefTitle', 'Terms defenitions' ); ?>"
								       style="width:260px;"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose the title for footnote (terms)
								definitions bottom block</td>
						</tr>
                        <tr>
                            <th scope="row">Maximum displayed footnotes</th>
                            <td>
                                <input type="number" name="cmtt_footnoteDefMax"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_footnoteDefMax', 5 ); ?>"
                                       style="width:260px;"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Define how many footnote definitions should
                                be displayed at the bottom of the article by default. The user will be able to see all
                                definitions by clicking the button "Show more".
                            </td>
                        </tr>
						<tr>
							<th scope="row" >Show more button label
							</th>
							<td>
								<label for="cmtt_footnoteDefMaxButtonLabel"></label>
								<input type="text" name="cmtt_footnoteDefMaxButtonLabel"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_footnoteDefMaxButtonLabel', 'Show more' ); ?>"
								       style="width:260px;"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Define the label for the button "Show more".
							</td>
						</tr>
					</table>
				</div>

				<div class="block">
					<h3 class="section-title">
						<span>Performance &amp; Debug</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Run QuickScan before parsing?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryEnableQuickScan" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryEnableQuickScan" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryEnableQuickScan', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you have a very big glossaries (thousands of terms) and long
								pages.
								This may improve the performance of parsing.
							</td>
						</tr>
						<tr>
							<th scope="row">Add RSS feeds?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryAddFeeds" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryAddFeeds" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryAddFeeds', true ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you want to have the RSS feeds for your glossary terms.
							</td>
						</tr>
						<tr>
							<th scope="row">Load the scripts in footer?</th>
							<td>
								<input type="hidden" name="cmtt_script_in_footer" value="0"/>
								<input type="checkbox"
								       name="cmtt_script_in_footer" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_script_in_footer', true ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you want to load the plugin's js files in the footer.
							</td>
						</tr>
						<tr>
							<th scope="row">Force loading of scripts?</th>
							<td>
								<input type="hidden" name="cmtt_forceLoadScripts" value="0"/>
								<input type="checkbox"
								       name="cmtt_forceLoadScripts" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_forceLoadScripts', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you tooltips are not showing for AJAX loaded content.
							</td>
						</tr>
						<tr>
							<th scope="row">Only highlight on "main" WP query?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryOnMainQuery" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryOnMainQuery" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_glossaryOnMainQuery' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you wish to only highlight glossary terms on main glossary query.
								Unchecking this box may fix problems with highlighting terms on some themes which
								manipulate the WP_Query.
							</td>
						</tr>
						<tr>
							<th scope="row">Run the function outputting the Glossary Index Page only once</th>
							<td>
								<input type="hidden" name="cmtt_removeGlossaryCreateListFilter" value="0"/>
								<input type="checkbox"
								       name="cmtt_removeGlossaryCreateListFilter" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_removeGlossaryCreateListFilter' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you wish to remove the filter responsible for outputting the
								Glossary Index. <br/>
								When this option is selected the function responsible for rendering the Glossary Index
								page (hooked to "the_content" filter) <br/>
								will run only once and then it will be removed. It's known that this conflicts with some
								translation plugins (e.g. qTranslate, Jetpack, PageBuilder).
							</td>
						</tr>
						<tr>
							<th scope="row">Run backlink generating function only once</th>
							<td>
								<input type="hidden" name="cmtt_addBacklinksOnce" value="0"/>
								<input type="checkbox"
								       name="cmtt_addBacklinksOnce" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_addBacklinksOnce', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show a
								backlink to Glossary Index on glossary term page only once
							</td>
						</tr>
						<tr>
							<th scope="row">Enable the caching mechanisms</th>
							<td>
								<input type="hidden" name="cmtt_glossaryEnableCaching" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryEnableCaching" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryEnableCaching', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to use the
								internal caching mechanisms.
							</td>
						</tr>
                        <tr>
                            <th scope="row">Enable the caching on Glossary Index</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryEnableCachingIndex" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_glossaryEnableCachingIndex" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryEnableCachingIndex', true ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Select this option if you want to use the
                                internal caching mechanisms on the Glossary Index page.
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Enable the caching on posts/pages</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryEnableCachingPosts" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_glossaryEnableCachingPosts" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryEnableCachingPosts', true ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Select this option if you want to use the
                                internal caching mechanisms on posts/pages.
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Enable the pre-caching</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryEnablePreCaching" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_glossaryEnablePreCaching" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryEnablePreCaching', false ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Select this option if you want to enable
                                the caching right after post is saved/updated.
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Cache expiration</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryCachingExpiration" value="0"/>
                                <input type="number" min="0" step="1"
                                       name="cmtt_glossaryCachingExpiration"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryCachingExpiration', 30 ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Set how many days the cache will expire.
                                Set 0 to unlimit.
                            </td>
                        </tr>
						<tr>
							<th scope="row">Clear caches actively</th>
							<td>
								<input type="hidden" name="cmtt_glossaryClearCaches" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryClearCaches" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryClearCaches', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to actively
								clear the internal caching mechanisms. <strong>Only works if the mechanisms are already
									disabled, also increases the database usage, so it's best to deactivate after a
									while (week).</strong></td>
						</tr>
						<tr>
							<th scope="row">Disable the "Hide term from Glossary Index" functionality</th>
							<td>
								<input type="hidden" name="cmtt_enableHidingFromIndex" value="0"/>
								<input type="checkbox"
								       name="cmtt_enableHidingFromIndex" <?php checked( 0, \CM\CMTT_Settings::get( 'cmtt_enableHidingFromIndex', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to disable
								the
								functionality. Doing this solves the performance problems with long query on some
								hostings.
							</td>
						</tr>
						<tr>
							<th scope="row">Enable embedded mode?</th>
							<td>
								<input type="hidden" name="cmtt_enableEmbeddedMode" value="0"/>
								<input type="checkbox"
								       name="cmtt_enableEmbeddedMode" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_enableEmbeddedMode', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you want to embedd the WordPress pages on other platform - eg.
								using
								Magento FishPig (it changes the way JS files are loaded)
							</td>
						</tr>
						<tr>
							<th scope="row">Remove the parsing of the excerpts?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryRemoveExcerptParsing" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryRemoveExcerptParsing" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryRemoveExcerptParsing', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Uncheck this option if you'd like to parse the excerpts in search for the glossary
								terms.
							</td>
						</tr>
						<tr>
							<th scope="row">Move tooltip contents to footer?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTooltipHashContent" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTooltipHashContent" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipHashContent', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								If this option is enabled, the tooltip content will not be passed directly to JS with
								the HTML attribute.
							</td>
						</tr>
						<tr>
							<th scope="row">Don't use the DOM parser for the content</th>
							<td>
								<input type="hidden" name="cmtt_disableDOMParser" value="0"/>
								<input type="checkbox" id="cmtt_disableDOMParser"
								       name="cmtt_disableDOMParser" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_disableDOMParser', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you want to parse the content using the simple parser
								(preg_replace) instead of DOM parser.
							</td>
						</tr>
						<tr>
							<th scope="row" valign="middle"
							    align="left"><?php _e( 'Tooltip Parsing Priority', 'cm-tooltip-glossary' ); ?>:
							</th>
							<td>
								<input type="text" name="cmtt_tooltipParsingPriority"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipParsingPriority', 20000 ); ?>"
								       placeholder="20000"/>
							</td>
							<td colspan="2" class="cm_field_help_container"><strong>Warning: Don't change this setting
									unless you know what you're doing</strong><br/>
								Changes the priority of the "glossary_parse" function firing. Can solve some problems
								with builders.
							</td>
						</tr>
						<tr>
							<th scope="row" valign="middle"
							    align="left"><?php _e( 'Tooltip Variants/Synonyms Separator', 'cm-tooltip-glossary' ); ?>
								:
							</th>
							<td>
								<input type="text" name="cmtt_tooltipVariantsSynonymsSeparator"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipVariantsSynonymsSeparator', ',' ); ?>"
								       placeholder=","/>
							</td>
							<td colspan="2" class="cm_field_help_container"><strong>Warning: Don't change this setting
									unless you know what you're doing</strong><br/>
								Changes the separator for the Glossary Term Synonyms/Variants, can be used if you plan
								to use commas in the terms. For example you can use semicolon ";", hash "#" etc.
							</td>
						</tr>
						<tr>
							<th scope="row">Use a non-minified version of tooltip script</th>
							<td>
								<input type="hidden" name="cmtt_disableMinifiedTooltip" value="0"/>
								<input type="checkbox" id="cmtt_disableMinifiedTooltip"
								       name="cmtt_disableMinifiedTooltip" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_disableMinifiedTooltip', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you want use non-minified version of the tooltip.js file.<br/>
							</td>
						</tr>
						<tr>
							<th scope="row">Turn on AMP</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTurnOnAmp" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTurnOnAmp" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTurnOnAmp', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to show tooltips in AMP pages.<br/>
								<a href="https://creativeminds.helpscoutdocs.com/article/2648-cm-tooltip-cmtg-extras-amp-support-accelerated-mobile-pages"
								   target="_blank">
									<i>See documentation</i>
								</a>
							</td>
						</tr>
						<tr>
							<th scope="row">Add structured data to the Term Page</th>
							<td>
								<input type="hidden" name="cmtt_add_structured_data_term_page" value="0"/>
								<input type="checkbox"
								       name="cmtt_add_structured_data_term_page" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_add_structured_data_term_page', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to add <a
									href="https://developers.google.com/search/docs/guides/intro-structured-data">
									structured data</a> to the Term Page.
							</td>
						</tr>
						<tr>
							<th scope="row">Convert content to initial encoding</th>
							<td>
								<input type="hidden" name="cmtt_convert_to_initial_encoding" value="0"/>
								<input type="checkbox"
								       name="cmtt_convert_to_initial_encoding"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_convert_to_initial_encoding', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you want to convert page content from "HTML Entities" to "UTF-8"
								after the CM Tooltip parser has processed the content.
							</td>
						</tr>
						<tr>
							<th scope="row">Add additional DB checks for related articles</th>
							<td>
								<input type="hidden" name="cmtt_additional_check_related_articles" value="0"/>
								<input type="checkbox"
								       name="cmtt_additional_check_related_articles"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_additional_check_related_articles', 1 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Debugging function - only for support and/or advanced users
							</td>
						</tr>
						<tr>
							<th scope="row">Parse content which is returned on glossary search</th>
							<td>
								<input type="hidden" name="cmtt_glossaryParseOnGlossarySearch" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryParseOnGlossarySearch"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryParseOnGlossarySearch', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Enable this option if you want to parse the content which is returned on glossary search
							</td>
						</tr>
						<tr>
							<th scope="row">Enable glossary filter for the content which is loaded using ajax</th>
							<td>
								<input type="hidden" name="cmtt_glossaryEnableAjaxComplete" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryEnableAjaxComplete"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryEnableAjaxComplete', true ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Select this option if you want to fire glossary filter on ajax complete.
							</td>
						</tr>
                        <tr>
                            <th scope="row">Allow importing terms with the same title (update/overwrite)</th>
                            <td>
                                <input type="hidden" name="cmtt_importSameTitle" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_importSameTitle"
                                    <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_importSameTitle', 0 ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                <strong>Warning: Don't change this setting unless you know what you're
                                    doing</strong><br/>
                                Enable this option if you want to import terms with the same title. The data from import
                                will overwrite/update the existing term data.
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Skip the imported terms with the same title</th>
                            <td>
                                <input type="hidden" name="cmtt_skipSameTitle" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_skipSameTitle"
                                    <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_skipSameTitle', 0 ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                <strong>Warning: Don't change this setting unless you know what you're
                                    doing. Overwrites the "Allow importing terms with the same title"</strong><br/>
                                Enable this option if you want to skip if one of the terms in import already exists in
                                the database.
						</tr>
						<tr>
							<th scope="row">Enable Audio player support?</th>
							<td>
								<input type="hidden" name="cmtt_audioPlayerEnabled" value="0"/>
								<input type="checkbox"
								       name="cmtt_audioPlayerEnabled"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_audioPlayerEnabled', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								<strong>Warning: Don't change this setting unless you know what you're
									doing</strong><br/>
								Enable this option if you want to use Audio Player in tooltips.
							</td>
						</tr>
						<tr>
							<th scope="row">Shortcode for Glossary Index
							</th>
							<td><input type="text" name="cmtt_glossaryShortcode"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryShortcode', 'glossary' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container"><strong>Warning!</strong>Only change this
								option
								if there is a problem with displaying the Glossary Index page.!
							</td>
						</tr>
                        <tr>
                            <th scope="row">Enable normalization for term synonyms?</th>
                            <td>
                                <input type="hidden" name="cmtt_normalizationSynonymsEnabled" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_normalizationSynonymsEnabled"
                                    <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_normalizationSynonymsEnabled', 1 ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                <strong>Warning: Don't change this setting unless you know what you're
                                    doing</strong><br/>
                                Disable this option to prevent replacing apostrophes with periods in term titles.
                            </td>
                        </tr>
					</table>
					<div class="clear"></div>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Backup</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<p>Easily backup your glossary to the file. You can create/download a backup on the <a
							href="<?php echo admin_url( 'admin.php?page=cmtt_importexport' ); ?>">Import/Export</a>
						page.</p>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row" valign="middle" align="left">PIN Protect</th>
							<td>
								<input type="text" name="cmtt_glossary_backup_pinprotect"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_backup_pinprotect' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Fill this field with a PIN code which will
								be required to get the backup. Leave empty to disable PIN Protection.
							</td>
						</tr>
						<tr>
							<th scope="row" valign="middle" align="left">Secure Backup</th>
							<td>
								<input type="hidden" name="cmtt_glossary_backup_secure" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_backup_secure" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_backup_secure', true ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this field if you want to use the
								secure WP Filesystem API for the file saves. Note: This may require the FTP/SSH
								credentials.
							</td>
						</tr>
						<tr>
							<th scope="row">Backup rebuild interval:</th>
							<td>
								<select name="cmtt_glossary_backupCronInterval">
									<?php
									$types            = wp_get_schedules();
									$selectedInterval = \CM\CMTT_Settings::get( 'cmtt_glossary_backupCronInterval', 'none' );
									?>
									<option
										value="none" <?php selected( 'none', $selectedInterval ); ?>><?php _e( 'Never', 'cm-tooltip-glossary' ); ?></option>
									<?php foreach ( $types as $typeName => $type ) : ?>
										<option
											value="<?php echo $typeName; ?>" <?php selected( $typeName, $selectedInterval ); ?>><?php echo $type['display']; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose how often the backup of the glossary
								is saved. Choose 'none' to disable automatic saves.
							</td>
						</tr>
						<tr>
							<th scope="row">Backup rebuild hour:</th>
							<td><input type="time" placeholder="00:00" size="5" name="cmtt_glossary_backupCronHour"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_backupCronHour' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose the hour when the Glossary Index
								Backup save should take place. The hour should be properly formatted string eg. 23:00 or
								1 AM
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Edit Screen Elements</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row" valign="middle" align="left">&quot;CM Tooltip - Disables&quot; metabox</th>
							<td>
								<input type="hidden" name="cmtt_disable_metabox_all_post_types" value="0"/>
								<input type="checkbox"
								       name="cmtt_disable_metabox_all_post_types" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_disable_metabox_all_post_types' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to display
								the
								metabox allowing to disable tooltips on all post types.
							</td>
						</tr>
						<tr>
							<th scope="row" valign="middle" align="left">&quot;CM Tooltip - Filter Terms&quot;
								metabox
							</th>
							<td>
								<input type="hidden" name="cmtt_allowed_terms_metabox_all_post_types" value="0"/>
								<input type="checkbox"
								       name="cmtt_allowed_terms_metabox_all_post_types" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_allowed_terms_metabox_all_post_types' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to display
								the
								metabox allowing to set allowed terms list on all post types.
							</td>
						</tr>
                        <tr>
                            <th scope="row" valign="middle" align="left">Disable all CM Tooltip metaboxes</th>
                            <td>
                                <input type="hidden" name="cmtt_disable_all_metabox_everywhere" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_disable_all_metabox_everywhere" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_disable_all_metabox_everywhere' ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Select this option if you want to hide
                                all CM Tooltip
                                metaboxes for all post types, including those parsing is enabled for.
                            </td>
                        </tr>
						<tr>
							<th scope="row" valign="middle" align="left">Show Visual Editor additional buttons?</th>
							<td>
								<input type="hidden" name="cmtt_add_richedit_buttons" value="0"/>
								<input type="checkbox"
								       name="cmtt_add_richedit_buttons" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_add_richedit_buttons', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to display
								plugin's additional buttons in Visual Editor.
							</td>
						</tr>
						<tr>
							<th scope="row" valign="middle"
							    align="left"><?php _e( 'Synonym Suggestions API', 'cm-tooltip-glossary' ); ?>:
							</th>
							<td>
								<input type="text" name="cmtt_glossarySynonymSuggestionsAPI"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossarySynonymSuggestionsAPI' ); ?>"
								       placeholder="<?php _e( 'API key', 'cm-tooltip-glossary' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">To get the API Key please go to <a
									href="https://words.bighugelabs.com/getkey.php" target="_blank">Big Huge
									Thesaurus</a></td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Referrals</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<p>Refer new users to any of the CM Plugins and you'll receive a minimum of <strong>15%</strong> of
						their purchase! For more information please visit CM Plugins <a
							href="http://www.cminds.com/referral-program/" target="new">Affiliate page</a></p>
					<table>
						<tr>
							<th scope="row" valign="middle" align="left">Enable referrals:</th>
							<td>
								<input type="hidden" name="cmtt_glossaryReferral" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryReferral" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_glossaryReferral' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enable referrals link at the bottom of the
								question and the answer page<br><br></td>
						</tr>
						<tr>
							<th scope="row" valign="middle"
							    align="left"><?php _e( 'Affiliate Code', 'cm-tooltip-glossary' ); ?>:
							</th>
							<td>
								<input type="text" name="cmtt_glossaryAffiliateCode"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryAffiliateCode' ); ?>"
								       placeholder="<?php _e( 'Affiliate Code', 'cm-tooltip-glossary' ); ?>"/>
							</td>
							<td colspan="2"
							    class="cm_field_help_container"><?php _e( 'Please add your affiliate code in here.', 'cm-tooltip-glossary' ); ?></td>
						</tr>
					</table>
				</div>
			</div>
			<div id="tabs-2" class="settings-tab">
				<div class="cminds_settings_toggle_tabs cminds_settings_toggle-opened">Toggle All</div>
				<div class="block">
					<h3 class="section-title">
						<span>Glossary Index Page Settings</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Display style:</th>
							<td>
								<select name="cmtt_glossaryDisplayStyle">
									<optgroup label="Without definition">
										<option
											value="classic" <?php selected( 'classic', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Classic
										</option>
										<option
											value="small-tiles" <?php selected( 'small-tiles', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Small Tiles
										</option>
										<option
											value="big-tiles" <?php selected( 'big-tiles', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Big Tiles
										</option>
										<option
											value="classic-table" <?php selected( 'classic-table', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Classic table
										</option>
										<option
											value="sidebar-termpage" <?php selected( 'sidebar-termpage', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Sidebar + term page
										</option>
										<option
											value="grid-style" <?php selected( 'grid-style', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Grid + terms
										</option>
										<option
											value="cube-style" <?php selected( 'cube-style', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Cube
										</option>
										<option
											value="image-tiles-view" <?php selected( 'image-tiles-view', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Image Tiles
										</option>
									</optgroup>
									<optgroup label="With definition">
										<option
											value="classic-definition" <?php selected( 'classic-definition', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Classic + definition
										</option>
										<option
											value="classic-excerpt" <?php selected( 'classic-excerpt', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Classic + excerpt
										</option>
										<option
											value="modern-table" <?php selected( 'modern-table', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Modern table
										</option>
										<option
											value="expand-style" <?php selected( 'expand-style', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Expand + description
										</option>
										<option
											value="expand2-style" <?php selected( 'expand2-style', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Expand + description v2
										</option>
										<option
											value="term-definition" <?php selected( 'term-definition', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Term + definition
										</option>
										<option
											value="img-term-definition" <?php selected( 'img-term-definition', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Image + term + definition
										</option>
										<option
											value="term-carousel" <?php selected( 'term-carousel', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Term Carousel
										</option>
										<option
											value="tiles-with-definition" <?php selected( 'tiles-with-definition', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Term tiles with definition
										</option>
										<option
											value="flipboxes-with-definition" <?php selected( 'flipboxes-with-definition', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
											Term flipboxes with definition
										</option>
										<option
											value="accordion-view" <?php selected( 'accordion-view', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
                                            Accordion view
										</option>
                                        <option
                                            value="cards-view" <?php selected( 'cards-view', \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle' ) ); ?>>
                                            Cards view
                                        </option>
									</optgroup>
								</select><br/>
							<td colspan="2" class="cm_field_help_container">Set display style of the Glossary Index
								page.
								By default the "Classic" style is selected.
							</td>
						</tr>
						<tr>
							<th scope="row">Show featured image thumbnail?</th>
							<td>
								<input type="hidden" name="cmtt_showFeaturedImageThumbnail" value="0"/>
								<input type="checkbox"
								       name="cmtt_showFeaturedImageThumbnail" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_showFeaturedImageThumbnail' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to display the thumbnails of the featured image on the
								Glossary Index (when available).
								<br/><i>Works only on "Classic + definition", "Classic + excerpt"</i>
							</td>
						</tr>
						<tr>
							<th scope="row">Link the thumbnail to the original image?</th>
							<td>
								<input type="hidden" name="cmtt_linkThumbnailToOriginal" value="0"/>
								<input type="checkbox"
								       name="cmtt_linkThumbnailToOriginal" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_linkThumbnailToOriginal' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to link the thumbnails of the featured image displayed on
								the
								Glossary Index (when available) to the original images.
								<br/><i>Works only on "Classic + definition", "Classic + excerpt", "Image + term +
									definition"</i>
							</td>
						</tr>
						<tr>
							<?php wp_enqueue_media(); ?>
							<th scope="row">Choose an image for posts without thumbnail</th>
							<td class="CM_Media_Uploader">
								<?php
								if ( class_exists( 'CMTT_Glossary_Plus' ) ) {
									echo CMTT_Glossary_Plus::_image_uploader( 'cmtt_glossary_no_thumb' );
								}
								?>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Upload the image if you want to replace the default one showing when no image thumbnail
								is
								set. Click on the uploaded image to remove it.<br/><br/>
								Preview the defaut image
								<br/><img src="<?php echo plugin_dir_url( CMTT_PLUGIN_FILE ); ?>/assets/no_image.jpg"
								          width="100" height="100">
							</td>
						</tr>
						<tr>
							<th scope="row">Run the API calls on the Glossary Index page?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryRunApiCalls" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryRunApiCalls" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryRunApiCalls' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to call the
								APIs on the Glossary Index page. <br/>
								<strong>Warning!</strong> Enabling this option can slow the loading time of the Glossary
								Index page drastically.
							</td>
						</tr>
						<tr>
							<th scope="row">Remove the tooltips on the Glossary Index Page?</th>
							<td>&nbsp;</td>
							<?php
							$link = admin_url( 'post.php?post=' . \CM\CMTT_Settings::get( 'cmtt_glossaryID' ) . '&action=edit' );
							?>
							<td colspan="2" class="cm_field_help_container">If you want to remove the tooltip from the
								Glossary Index page, you should edit the page using WordPress's Pages menu (or clicking
								<a href="<?php echo $link; ?>" target="_blank">this link</a>)<br/>
								And in the <strong>"Tooltip Plugin"</strong> tab select the option <strong>"Exclude this
									page from Tooltip plugin"</strong></td>
						</tr>
						<tr>
							<th scope="row">Mark terms not older than X days as "New"</th>
							<td><input type="text" name="cmtt_glossaryNewItemMaxDays"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryNewItemMaxDays', '0' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								If this setting contains a positive number then Glossary Terms not older than this
								number will be marked as "New". 0 turns off the feature.
							</td>
						</tr>
						<tr>
							<th scope="row">Title for the mark indicating "New" terms</th>
							<td><input type="text" name="cmtt_glossaryNewItemMarkTitle"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryNewItemMarkTitle', 'New!' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								You can select the title which will appear as a title on hover over the star indicating
								that the term is "New".
							</td>
						</tr>
						<tr>
							<th scope="row">Show the category names before the term name?</th>
							<td>
								<input type="hidden" name="cmtt_showCategoryBeforeTitleOnIndex" value="0"/>
								<input type="checkbox"
								       name="cmtt_showCategoryBeforeTitleOnIndex" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_showCategoryBeforeTitleOnIndex', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to display the category names before the term name on the
								Glossary Index
							</td>
						</tr>
						<tr>
							<th scope="row">Select the heading level for the new letter?</th>
							<td>
								<select name="cmtt_index_letter_tag">
									<option
										value="h1" <?php selected( 'h1', \CM\CMTT_Settings::get( 'cmtt_index_letter_tag', 'h3' ) ); ?>>
										h1
									</option>
									<option
										value="h2" <?php selected( 'h2', \CM\CMTT_Settings::get( 'cmtt_index_letter_tag', 'h3' ) ); ?>>
										h2
									</option>
									<option
										value="h3" <?php selected( 'h3', \CM\CMTT_Settings::get( 'cmtt_index_letter_tag', 'h3' ) ); ?>>
										h3
									</option>
									<option
										value="h4" <?php selected( 'h4', \CM\CMTT_Settings::get( 'cmtt_index_letter_tag', 'h3' ) ); ?>>
										h4
									</option>
									<option
										value="h5" <?php selected( 'h5', \CM\CMTT_Settings::get( 'cmtt_index_letter_tag', 'h3' ) ); ?>>
										h5
									</option>
									<option
										value="h6" <?php selected( 'h6', \CM\CMTT_Settings::get( 'cmtt_index_letter_tag', 'h3' ) ); ?>>
										h6
									</option>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select which heading tag should be used to display the letter indicating the start of
								new section of the Glossary Terms.
								<br/><i>Works only on "Modern View" and other views that display the letters in the
									list.</i>
							</td>
						</tr>
                        <tr>
                            <th scope="row">Disable loader animation on search</th>
                            <td>
                                <input type="hidden" name="cmtt_disableLoaderAnimation" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_disableLoaderAnimation" <?php checked( 1, \CM\CMTT_Settings::get( 'cmtt_disableLoaderAnimation', 0 ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select this option if you want to disable showing loader animation on search on the
                                Glossary Index
                            </td>
                        </tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Styling</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Column width</th>
							<td>
								<input type="text"
								       name="cmtt_glossaryGridColumnWidth"
								       class="cmtt-input-sm"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryGridColumnWidth', '200px' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the width of the columns in the "Grid + terms" view
							</td>
						</tr>
						<tr>
							<th scope="row">Small tiles tile width</th>
							<td>
								<input type="text"
								       name="cmtt_glossarySmallTileWidth"
								       class="cmtt-input-sm"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossarySmallTileWidth', '85px' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the width of the single tile in the "Small tiles" view
							</td>
						</tr>
						<tr>
							<th scope="row">Big tiles tile width</th>
							<td>
								<input type="text"
								       name="cmtt_glossaryBigTileWidth"
								       class="cmtt-input-sm"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryBigTileWidth', '179px' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the width of the single tile in the "Big tiles" view
							</td>
						</tr>
						<tr>
							<th scope="row">Term item height</th>
							<td>
								<input type="text"
								       name="cmtt_carousel_slide_height"
								       class="cmtt-input-sm"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_carousel_slide_height', '245px' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the height of the items in the "Term Carousel" and "Term tiles with definition"
								views
							</td>
						</tr>
						<tr>
							<th scope="row">Term item width</th>
							<td>
								<input type="text"
								       name="cmtt_term_tiles_width"
								       class="cmtt-input-sm"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_term_tiles_width', '220px' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the height of the items in the "Term tiles with definition" view
							</td>
						</tr>
						<tr>
							<th scope="row">Image column width in the "Image + term + definition" view</th>
							<td>
								<input type="text"
								       name="cmtt_img_term_def_imgsize"
								       class="cmtt-input-sm"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_img_term_def_imgsize', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the width of the image column in the "Image + term + definition" view
							</td>
						</tr>
						<tr>
							<th scope="row">Cube button color (selected buttons)</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_cubeBtnColorSelected"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_cubeBtnColorSelected', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Choose the color of cube buttons for the "Cube" view
							</td>
						</tr>
						<tr>
							<th scope="row">Cube button color (disabled buttons)</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_cubeBtnColor"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_cubeBtnColor', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Choose the color of cube buttons for the "Cube" view
							</td>
						</tr>
						<tr>
							<th scope="row">Flipbox item height</th>
							<td>
								<input type="text"
								       name="cmtt_flipbox_item_height"
								       class="cmtt-input-sm"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_flipbox_item_height', '160px' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the height of flipboxes in the "Term flipboxes with definition" view
							</td>
						</tr>
						<tr>
							<th scope="row">Number of flipboxes in a row</th>
							<td>
								<input type="number"
								       name="cmtt_number_of_flipboxes"
								       step="1"
								       class="cmtt-input-sm"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_number_of_flipboxes', 6 ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the number of flipboxes in a row
							</td>
						</tr>
						<tr>
							<th scope="row">Flipbox background color</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_flipbox_background_color"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_flipbox_background_color', '#cecece' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select flipboxes background color
							</td>
						</tr>
						<tr>
                            <th scope="row">Alphabet color in Accordion view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_alphapet_color_accordion_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_alphapet_color_accordion_view', '#a60a3d' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select alphabet color in Accordion view
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Letter separator background color in Accordion view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_letter_background_color_accordion_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_letter_background_color_accordion_view', '#8e2c85' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select index letter separator background color in Accordion view
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Letter separator color in Accordion view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_letter_color_accordion_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_letter_color_accordion_view', '#fff' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select index letter separator color in Accordion view
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Active term title color in Accordion view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_active_item_color_accordion_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_active_item_color_accordion_view', '#8e2c85' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select active term title color in Accordion view
                            </td>
                        </tr>
                        <tr>
                            <?php $selectedIcon = CM\CMTT_Settings::get( 'cmtt_open_icon_accordion_view', 'dashicons-arrow-down-alt2' ); ?>
                            <th scope="row">Open icon in Accordion view</th>
                            <td>
                                <label for="cmtt_term_icon">
                                    <input id="cmtt_open_icon_accordion_view"
                                           type="text"
                                           name="cmtt_open_icon_accordion_view" value="<?php echo $selectedIcon; ?>"/>
                                    <input class="button dashicons-picker"
                                           type="button"
                                           value="Choose Icon"
                                           data-target="#cmtt_open_icon_accordion_view"
                                           data-preview="#cmtt_open_icon_accordion_view_preview"/>
                                    <span style="font-size: 15px;padding: 4px;display:block;">
										<?php echo __( 'Preview:', 'cm-tooltip-glossary' ); ?>
										<span id="cmtt_open_icon_accordion_view_preview"
                                              class="dashicons <?php echo $selectedIcon; ?> "></span>
									</span>
                                </label>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select icon for closed view in Accordion view
                            </td>
                        </tr>
                        <tr>
                            <?php $selectedIcon = CM\CMTT_Settings::get( 'cmtt_close_icon_accordion_view', 'dashicons-arrow-up-alt2' ); ?>

                            <th scope="row">Close icon in Accordion view</th>
                            <td>
                                <label for="cmtt_term_icon">
                                    <input id="cmtt_close_icon_accordion_view"
                                           type="text"
                                           name="cmtt_close_icon_accordion_view" value="<?php echo $selectedIcon; ?>"/>
                                    <input class="button dashicons-picker"
                                           type="button"
                                           value="Choose Icon"
                                           data-target="#cmtt_close_icon_accordion_view"
                                           data-preview="#cmtt_close_icon_accordion_view_preview"/>
                                    <span style="font-size: 15px;padding: 4px;display:block;">
										<?php echo __( 'Preview:', 'cm-tooltip-glossary' ); ?>
										<span id="cmtt_close_icon_accordion_view_preview"
                                              class="dashicons <?php echo $selectedIcon; ?> "></span>
									</span>
                                </label>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select icon for open view in Accordion view
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Open/close icon color in Accordion view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_icon_color_accordion_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_icon_color_accordion_view', '#8e2c85' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select icon color in Accordion view
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Default term title color in Cards view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_default_item_color_card_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_default_item_color_card_view', '#00000' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select default term title color in Cards view
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Active term title color in Cards view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_active_item_color_card_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_active_item_color_card_view', '#6bc07f' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select active term title color in Cards view
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Term definition color in Cards view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_term_definition_color_card_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_term_definition_color_card_view', '#000' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select term definition color in Cards view
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Alphabetical Index color in Cards view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_alphabetical_index_color_card_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_alphabetical_index_color_card_view', '#000' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select alphabetical index color in Cards view
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Alphabetical separator color in Cards view</th>
                            <td>
                                <input type="text" class="colorpicker" name="cmtt_alphabetical_separator_color_card_view"
                                       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_alphabetical_separator_color_card_view', '#000' ); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">
                                Select letter separator color in Cards view
                            </td>
                        </tr>
						<tr>
							<th scope="row">Search button background-color</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_searchBtnBgColor"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_searchBtnBgColor', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select button background-color
							</td>
						</tr>
						<tr>
							<th scope="row">Search button background-color on hover</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_searchBtnBgColorOnHover"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_searchBtnBgColorOnHover', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select button background-color on hover
							</td>
						</tr>
						<tr>
							<th scope="row">Search button text color</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_searchBtnTextColor"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_searchBtnTextColor', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select button text color
							</td>
						</tr>
						<tr>
							<th scope="row">Search button text color on hover</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_searchBtnTextColorOnHover"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_searchBtnTextColorOnHover', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select button text color on hover
							</td>
						</tr>
						<tr>
							<th scope="row">Search form width</th>
							<td>
								<input type="number"
								       name="cmtt_glossarySearchFormWidth"
								       class="cmtt-input-sm"
								       step="1"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossarySearchFormWidth', '' ); ?>"/>px
							</td>
							<td colspan="2" class="cm_field_help_container">
								Set the width of the search form "input + button"
							</td>
						</tr>
						<tr class="whole-line">
							<th scope="row">Search button border</th>
							<td>
								Width: <input type="number"
								              class="cmtt-input-sm"
								              name="cmtt_glossaryButtonBorderWidth"
								              step="1"
								              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderWidth' ); ?>"/>px
								<br>
								Style: <select name="cmtt_glossaryButtonBorderStyle" style="width: 150px;">
									<option
										value="none" <?php echo selected( 'none', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										None
									</option>
									<option
										value="solid" <?php echo selected( 'solid', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										Solid
									</option>
									<option
										value="dotted" <?php echo selected( 'dotted', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										Dotted
									</option>
									<option
										value="dashed" <?php echo selected( 'dashed', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										Dashed
									</option>
									<option
										value="double" <?php echo selected( 'double', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										Double
									</option>
									<option
										value="groove" <?php echo selected( 'groove', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										Groove
									</option>
									<option
										value="ridge" <?php echo selected( 'ridge', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										Ridge
									</option>
									<option
										value="inset" <?php echo selected( 'inset', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										Inset
									</option>
									<option
										value="outset" <?php echo selected( 'outset', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										Outset
									</option>
								</select> <br>
								Color: <input type="text" class="colorpicker" name="cmtt_glossaryButtonBorderColor"
								              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderColor', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Set wanted border options for the search
								button
							</td>
						</tr>
						<tr class="whole-line">
							<th scope="row">Search input border</th>
							<td>
								Width: <input type="number"
								              class="cmtt-input-sm"
								              name="cmtt_glossaryInputBorderWidth"
								              step="1"
								              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderWidth' ); ?>"/>px
								<br>
								Style: <select name="cmtt_glossaryInputBorderStyle"  style="width: 150px;">
									<option
										value="none" <?php echo selected( 'none', \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle' ) ); ?>>
										None
									</option>
									<option
										value="solid" <?php echo selected( 'solid', \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle' ) ); ?>>
										Solid
									</option>
									<option
										value="dotted" <?php echo selected( 'dotted', \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle' ) ); ?>>
										Dotted
									</option>
									<option
										value="dashed" <?php echo selected( 'dashed', \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle' ) ); ?>>
										Dashed
									</option>
									<option
										value="double" <?php echo selected( 'double', \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle' ) ); ?>>
										Double
									</option>
									<option
										value="groove" <?php echo selected( 'groove', \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle' ) ); ?>>
										Groove
									</option>
									<option
										value="ridge" <?php echo selected( 'ridge', \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle' ) ); ?>>
										Ridge
									</option>
									<option
										value="inset" <?php echo selected( 'inset', \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle' ) ); ?>>
										Inset
									</option>
									<option
										value="outset" <?php echo selected( 'outset', \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle' ) ); ?>>
										Outset
									</option>
								</select> <br>
								Color: <input type="text" class="colorpicker" name="cmtt_glossaryInputBorderColor"
								              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderColor', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Set wanted border options for the search
								input
							</td>
						</tr>
						<tr>
							<th scope="row">Search button font size</th>
							<td>
								<input type="number"
								       class="cmtt-input-sm"
								       name="cmtt_glossaryButtonFontSize"
								       step="1"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryButtonFontSize' ); ?>"/>px
							</td>
							<td colspan="2" class="cm_field_help_container">Set text font size for search form button
							</td>
						</tr>
						<tr>
							<th scope="row">Search input font size</th>
							<td>
								<input type="number"
								       class="cmtt-input-sm"
								       name="cmtt_glossaryInputFontSize"
								       step="1"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryInputFontSize' ); ?>"/>px
							</td>
							<td colspan="2" class="cm_field_help_container">Set text font size for search form input
							</td>
						</tr>
						<tr>
							<th scope="row">Search input background-color</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_glossaryInputBackgroundColor"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryInputBackgroundColor', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Chooset background-color for search form
								input
							</td>
						</tr>
						<tr>
							<th scope="row">Search input text color</th>
							<td>
								<input type="text" class="colorpicker" name="cmtt_glossaryInputTextColor"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryInputTextColor', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose text color for search form input
							</td>
						</tr>
						<tr>
							<th scope="row">Search form border-radius</th>
							<td>
								<input type="number"
								       class="cmtt-input-sm"
								       name="cmtt_glossaryFormBorderRadius"
								       step="1"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryFormBorderRadius' ); ?>"/>px
							</td>
							<td colspan="2" class="cm_field_help_container">Use for set border-radius for input and
								button.
								Also, it use for combine input and button if that option is selected
							</td>
						</tr>
						<tr>
							<th scope="row">Combine search input and button</th>
							<td>
								<input type="hidden" name="cmtt_glossaryCombineForm" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryCombineForm" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryCombineForm' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select if you want merge search input and button
							</td>
						</tr>
						<tr>
							<th scope="row">Term font size</th>
							<td>
								<input type="number"
								       class="cmtt-input-sm"
								       name="cmtt_indexTermFontSize"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_indexTermFontSize' ); ?>"
								       step="1"/>px
							</td>
							<td colspan="2" class="cm_field_help_container">
								Set size of the font of the term title on the Index page. Leave empty to ignore this
								option.
							</td>
						</tr>
						<tr>
							<th scope="row">Term font weight</th>
							<td>
								<select name="cmtt_indexTermFontWeight">
									<option
										value="normal" <?php selected( 'normal', \CM\CMTT_Settings::get( 'cmtt_indexTermFontWeight' ), true ); ?>>
										Regular
									</option>
									<option
										value="bold" <?php selected( 'bold', \CM\CMTT_Settings::get( 'cmtt_indexTermFontWeight' ), true ); ?>>
										Bold
									</option>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">Set weight of the term title on the Index
								page
							</td>
						</tr>
					</table>
				</div>
				<!--    end: STYLING    -->
				<div class="block">
					<h3 class="section-title">
						<span>Definition</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<p><i>All of these settings work only on the <strong>Display styles</strong> from the group "With
							definition"</i></p>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Display excerpt instead of description?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryShowExcerpt" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryShowExcerpt" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryShowExcerpt', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to display exceprt field as definition instead of the
								content
								of glossary term.
								<br/><i>"Classic + excerpt" will <strong>always</strong> show the excerpt.</i>
							</td>
						</tr>
						<tr>
							<th scope="row">Remove the HTML tags from definition?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTooltipDescStripTags" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTooltipDescStripTags" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipDescStripTags', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to remove the html characters from the definition.
								<br/><i>Works only on "Classic + definition", "Classic + excerpt" and "Modern table"</i>
							</td>
						</tr>
						<tr class="whole-line">
							<th scope="row">Limit the definition length</th>
							<td>
								<input type="number"
								       class="cmtt-input-sm"
								       name="cmtt_glossaryTooltipDescLength"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipDescLength', 300 ); ?>"/>
								<div class="button toggleLengthTester">Toggle Length Tester</div>
								<textarea type="text"
								          placeholder="You can test length visually by typing or pasting here."
								          id="cmtt_definitionLengthTester"
								          maxlength="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipDescLength', 300 ); ?>"></textarea>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to show only a limited number of chars of the description
								and
								add "(...)" at the end. Minimum is 30 chars.
								<br/><i>Works on all of the modes which display the description/excerpt</i>
							</td>
						</tr>
						<tr>
							<th scope="row">Show "Read More" link on the index page</th>
							<td>
								<input type="hidden" name="cmtt_glossaryIndexDescReadMore" value="0"/>
								<?php
								$show_read_more = \CM\CMTT_Settings::get( 'cmtt_glossaryIndexDescReadMore', 0 );
								?>
								<select name="cmtt_glossaryIndexDescReadMore">
									<option value="0" <?php echo selected( '0', $show_read_more ); ?>>Don't show
									</option>
									<option value="2" <?php echo selected( '2', $show_read_more ); ?>>Show only if text
										is truncated
									</option>
									<option value="1" <?php echo selected( '1', $show_read_more ); ?>>Show always
									</option>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select when to show Read more link for glossary terms definition on the Index page.
								You can change the "Read More" label in the Labels tab.
								<br/><i>Works on all of the modes which display the description/excerpt</i>
							</td>
						</tr>
						<tr>
							<th scope="row">Strip the shortcodes from definition?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryIndexDescStripShortcode" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryIndexDescStripShortcode" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryIndexDescStripShortcode' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to strip the shortcodes from the definition displayed on
								the
								Glossary Index page.
								<br/><i>Works on all of the modes which display the description/excerpt</i>
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Links</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Remove the link from Glossary Index to the Glossary Term pages?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryListTermLink" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryListTermLink" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryListTermLink' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you do not want to
								show
								links to the glossary term pages on the Glossary Index page.
								<br/>Keep in mind that the plugin use a <strong>&lt;span&gt;</strong> tag instead of a
								link
								tag and if you are using a custom CSS you should take this into account
							</td>
						</tr>
						<tr>
							<th scope="row">Remove the tooltip from Glossary Index term title?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryListTermDisableTooltip" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryListTermDisableTooltip" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryListTermDisableTooltip', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you do not want to
								show
								the tooltips on the term title of the Glossary Index page.
								<br/>Keep in mind that it won't work on the definitions.
							</td>
						</tr>
						<tr>
							<th scope="row">Style links differently?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryDiffLinkClass" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryDiffLinkClass" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryDiffLinkClass' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish for the links
								in the Glossary Index page to be styled differently than the regular way glossary terms
								links are styled.
								<br/>By selecting this option you will be able to use the class 'glossaryLinkMain' to
								style
								only the links on the Glossary Index page otherwise they will retain the class
								'glossaryLink' and will be identical to the linked terms on all other pages.
							</td>
						</tr>
						<tr>
							<th scope="row">Only title links to Glossary Term pages?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryOnlyTitleLinksToTerm" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryOnlyTitleLinksToTerm" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryOnlyTitleLinksToTerm', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish By selecting
								this option you wish that only the term title links to the Glossary Term pages, while
								the
								terms in definitions only display the tooltips.
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Sharing box</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Show the sharing box on the Glossary Index Page?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryShowShareBox" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryShowShareBox" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryShowShareBox' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish to show the
								"Share This" box on the Glossary Index Page with links to Facebook, Twitter, Google+ and
								LinkedIn.
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Search, Categories &amp; Tags</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<p>
						<a class="cmtt-documentation-link"
						   href="https://creativeminds.helpscoutdocs.com/article/1337-cm-tooltip-cmtg-index-fast-live-filter"
						   target="_blank">
							Documentation<span class="dashicons dashicons-external"></span>
						</a>
					</p>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Show the search on the Glossary Index page</th>
							<td>
								<input type="hidden" name="cmtt_glossary_showSearch" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_showSearch" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_showSearch' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you like the "search"
								functionality to appear on the Glossary Index page.
							</td>
						</tr>
						<tr>
							<th scope="row">Only show items on search?</th>
							<td>
								<input type="hidden" name="cmtt_showOnlyOnSearch" value="0"/>
								<input type="checkbox"
								       name="cmtt_showOnlyOnSearch" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_showOnlyOnSearch', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to display the glossary items only after search is used.
							</td>
						</tr>
						<tr>
							<th scope="row">Use Fast-live-Filter?</th>
							<td>
								<input type="hidden" name="cmtt_indexFastFilter" value="0"/>
								<input type="checkbox"
								       name="cmtt_indexFastFilter" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_indexFastFilter', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to use the JS based fast filtering on the Glossary Index.
							</td>
						</tr>
						<tr>
							<th scope="row">Hide the Categories on the Glossary Index page?</th>
							<td>
								<input type="hidden" name="cmtt_index_hideCategories" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_hideCategories" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_hideCategories', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected the categories
								are will not be displayed on the Glossary Index Pages. <br><em>Hint: If you just want to
									hide them on one Index
									you can use the <strong>hide_categories="1"</strong> attribute.</em></td>
						</tr>
						<tr>
							<th scope="row">Category selection method:</th>
							<td><select name="cmtt_glossaryCategoriesDisplayType">
									<option
										value="0" <?php echo selected( '0', \CM\CMTT_Settings::get( 'cmtt_glossaryCategoriesDisplayType' ) ); ?>>
										Dropdown
									</option>
									<option
										value="1" <?php echo selected( '1', \CM\CMTT_Settings::get( 'cmtt_glossaryCategoriesDisplayType' ) ); ?>>
										Links
									</option>
								</select></td>
							<td colspan="2" class="cm_field_help_container">Select the way how categories are displayed
								on the Glossary Index Page
							</td>
						</tr>
						<tr>
							<th scope="row">Show only relevant categories?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_onlyRelevantCats" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_onlyRelevantCats" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_onlyRelevantCats', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected only the
								categories matching the currently displayed elements will be shown.
							</td>
						</tr>
						<tr>
							<th scope="row">Disable all categories?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_disableAllCats" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_disableAllCats" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_disableAllCats', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected the "All
								Categories" option will be disabled.
							</td>
						</tr>
						<tr>
							<th scope="row">Save the users last selection in the session?</th>
							<td>
								<input type="hidden" name="cmtt_index_sessionSave" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_sessionSave" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_sessionSave', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you like to remember
								the last user search/letter selection in the session.
							</td>
						</tr>
						<tr>
							<th scope="row">Show what elements to search selection on Glossary Index?</th>
							<td>
								<input type="hidden" name="cmtt_glossarySearchFromOptionsFrontend" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossarySearchFromOptionsFrontend" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossarySearchFromOptionsFrontend', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show the
								selection for users on Glossary Index page.
							</td>
						</tr>
						<tr>
							<th scope="row">Search only from:</th>
							<td>
								<input type="hidden" name="cmtt_glossarySearchFromOptions" value="0"/>
								<?php
								$search_options = array(
									0 => 'Title',
									1 => 'Description',
								);

								$search_options   = apply_filters( 'cmtt_search_form_options', $search_options );
								$selected_options = \CM\CMTT_Settings::get( 'cmtt_glossarySearchFromOptions' );

								if ( ! is_array( $selected_options ) ) {
									$selected_options = $selected_options == '2' ? array(
										'0',
										'1'
									) : array( $selected_options );
								}
								?>

								<?php foreach ( $search_options as $k => $v ) : ?>
									<input type="checkbox"
									       name="cmtt_glossarySearchFromOptions[]" <?php checked( true, in_array( $k, $selected_options ), true ); ?>
									       value="<?php echo $k; ?>"/>
									<label><?php echo $v; ?></label> <br>
								<?php endforeach; ?>
							</td>
							<td colspan="2" class="cm_field_help_container">Select rather to search only in the titles,
								only in the descriptions, or in both
							</td>
						</tr>
						<tr>
							<th scope="row">Search only for the exact term/phrase?</th>
							<td>
								<input type="hidden" name="cmtt_index_searchExact" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_searchExact" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_searchExact', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected the search will
								only look for exact term/phrase. And will not return the phrases containing it.
							</td>
						</tr>
						<tr>
							<th scope="row">Optimize the search results</th>
							<td>
								<input type="hidden" name="cmtt_fixSearch" value="0"/>
								<input type="checkbox"
								       name="cmtt_fixSearch" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_fixSearch', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cmtt_field_help_container">This option optimizes the search results
								by removing irrelevant posts
							</td>
						</tr>
						<tr>
							<th scope="row">Hide the tags on the Glossary Index page?</th>
							<td>
								<input type="hidden" name="cmtt_index_hideTags" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_hideTags" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_hideTags', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected the tags are will
								not be displayed on the Glossary Index Pages. <br><em>Hint: If you just want to hide
									them on one Index
									you can use the <strong>hide_tags="1"</strong> attribute.</em></td>
						</tr>
						<tr>
							<th scope="row">Order tags by items count?</th>
							<td>
								<input type="hidden" name="cmtt_orderTagsByCount" value="0"/>
								<input type="checkbox"
								       name="cmtt_orderTagsByCount" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_orderTagsByCount', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected the tags are
								ordered in by the number of times given tag was assigned. Otherwise it displays the tags
								alphabetically.
							</td>
						</tr>
						<tr>
							<th scope="row">Show only relevant tags?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_onlyRelevantTags" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_onlyRelevantTags" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_onlyRelevantTags', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected only the tags
								matching the currently displayed elements will be shown.
							</td>
						</tr>
						<tr>
							<th scope="row">Show terms which exact match selected tags (on search)</th>
							<td>
								<input type="hidden" name="cmtt_glossary_tagsExactMatch" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_tagsExactMatch"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_tagsExactMatch', '0' ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected only the tags
								matching the currently displayed elements will be shown.
							</td>
						</tr>
						<tr>
							<th scope="row">Redirect single search result to term page?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_directToTermPage" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_directToTermPage" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_directToTermPage', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is selected if search result
								yields one result it will automatically redirect to the term page.
							</td>
						</tr>
						<tr>
							<th scope="row">Ignore preselected letter and show all results on glossary search?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_ignore_letter_on_search" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_ignore_letter_on_search"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_ignore_letter_on_search', '0' ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								When this option is enabled the preselected letter (eg. passed to a shortcode as
								attribute) will be ignored when user searches.
							</td>
						</tr>
						<tr>
							<?php wp_enqueue_media(); ?>
							<th scope="row">Choose an icon for Search Hint</th>
							<td class="CM_Media_Uploader">
								<?php
								if ( class_exists( 'CMTT_Glossary_Plus' ) ) {
									echo CMTT_Glossary_Plus::_image_uploader( 'cmtt_glossary_search_hint' );
								}
								?>

							</td>
							<td colspan="2" class="cm_field_help_container">
								Upload the icon if you want to replace the default Search Hint icon
								<br/><img
									src="<?php echo plugin_dir_url( CMTT_PLUGIN_FILE ); ?>/assets/css/images/help.png"
									width="15" height="15">
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Pagination</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Paginate Glossary Index page (items per page)</th>
							<td><input type="text" name="cmtt_perPage"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_perPage' ); ?>"/></td>
							<td colspan="2" class="cm_field_help_container">How many elements per page should be
								displayed (0 to disable pagination)
							</td>
						</tr>
						<tr>
							<th scope="row">Round pagination elements</th>
							<td>
								<input type="hidden" name="cmtt_glossaryPaginationRound" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryPaginationRound" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryPaginationRound', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cmtt_field_help_container">Select this option if you would like the
								pagination elements to be round instead of rectangular
							</td>
						</tr>
						<tr>
							<th scope="row">Pagination type</th>
							<td><select name="cmtt_glossaryServerSidePagination">
									<option
										value="0" <?php echo selected( 0, \CM\CMTT_Settings::get( 'cmtt_glossaryServerSidePagination' ) ); ?>>
										Client-side
									</option>
									<option
										value="1" <?php echo selected( 1, \CM\CMTT_Settings::get( 'cmtt_glossaryServerSidePagination' ) ); ?>>
										Server-side
									</option>
								</select></td>
							<td colspan="2" class="cm_field_help_container">Client-side: longer loading, fast page
								switch (with additional alphabetical index)<br/>
								Server-side: faster loading, slower page switch <br/>
								<strong>Note: The Alphabetical Index only works in Server-side pagination in
									Pro+/Ecommerce</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Pagination position (Server-side only)</th>
							<td><select name="cmtt_glossaryPaginationPosition">
									<option
										value="bottom" <?php echo selected( 'bottom', \CM\CMTT_Settings::get( 'cmtt_glossaryPaginationPosition' ) ); ?>>
										Bottom
									</option>
									<option
										value="top" <?php echo selected( 'top', \CM\CMTT_Settings::get( 'cmtt_glossaryPaginationPosition' ) ); ?>>
										Top
									</option>
									<option
										value="both" <?php echo selected( 'both', \CM\CMTT_Settings::get( 'cmtt_glossaryPaginationPosition' ) ); ?>>
										Both
									</option>
								</select></td>
							<td colspan="2" class="cm_field_help_container">Choose where you would like the pagination
								to appear on the Index Page (only for the Server-side pagination). For the client side
								the pagination is always on the bottom.
							</td>
						</tr>
						<tr>
							<th scope="row">Scroll to the top</th>
							<td>
								<input type="hidden" name="cmtt_glossaryScrollTop" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryScrollTop" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryScrollTop', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you would like automatically scroll to the top after the content
								has loaded
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Alphabetic index</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Display Alphabetical Index</th>
							<td>
								<input type="hidden" name="cmtt_index_enabled" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_enabled" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_enabled', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If you uncheck this option the alphabetical
								index will not be displayed on the Glossary Index Page
							</td>
						</tr>
						<tr>
							<th scope="row">Stretch the alphabetical index to 100%</th>
							<td>
								<input type="hidden" name="cmtt_letter_width" value="0"/>
								<input type="checkbox"
								       name="cmtt_letter_width" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_letter_width', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If you check this option the alphabetical
								index will be stretched to 100% width
							</td>
						</tr>
						<tr>
							<th scope="row">Letters in alphabetic index</th>
							<td><input type="text" class="cmtt_longtext" name="cmtt_index_letters"
							           value="<?php echo esc_attr( implode( ',', \CM\CMTT_Settings::get( 'cmtt_index_letters', array() ) ) ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Which letters should be shown in alphabetic
								index (separate by commas)
							</td>
						</tr>
						<tr>
							<th scope="row">Size of the letters in alphabetic index</th>
							<td>
								<select name="cmtt_indexLettersSize">
									<option
										value="small" <?php selected( 'small', \CM\CMTT_Settings::get( 'cmtt_indexLettersSize' ) ); ?>>
										Small
									</option>
									<option
										value="medium" <?php selected( 'medium', \CM\CMTT_Settings::get( 'cmtt_indexLettersSize' ) ); ?>>
										Medium
									</option>
									<option
										value="large" <?php selected( 'large', \CM\CMTT_Settings::get( 'cmtt_indexLettersSize' ) ); ?>>
										Large
									</option>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">Select the size of the letters in the
								alphabetic index: small(7pt), medium(10pt), large(14pt)
							</td>
						</tr>
						<tr>
							<th scope="row">Show numeric [0-9] in alphabetic index?</th>
							<td>
								<input type="hidden" name="cmtt_index_includeNum" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_includeNum" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_includeNum' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish to show [0-9]
								option in alphabetical index.
							</td>
						</tr>
						<tr>
							<th scope="row">Show all [ALL] in alphabetic index?</th>
							<td>
								<input type="hidden" name="cmtt_index_includeAll" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_includeAll" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_includeAll' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish to show [All]
								option in alphabetical index.
							</td>
						</tr>
						<tr>
							<th scope="row">Show matching elements counts in alphabetic index?</th>
							<td>
								<input type="hidden" name="cmtt_index_showCounts" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_showCounts" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_showCounts', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show the
								number of elements matching each letter on hover.
							</td>
						</tr>
						<tr>
							<th scope="row">Show found matching results count below alphabetical index?</th>
							<td>
								<input type="hidden" name="cmtt_index_showResultsCount" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_showResultsCount" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_showResultsCount', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enable this option if you want to show the
                                number of found terms below the alphabetical index.
							</td>
						</tr>
						<tr>
							<th scope="row">Show alphabetic index as round elements</th>
							<td>
								<input type="hidden" name="cmtt_index_showRound" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_showRound" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_showRound', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show the
								alphabetical index as round items instead of rectangular.
							</td>
						</tr>
						<tr>
							<th scope="row">Show empty letters in alphabetic index?</th>
							<td>
								<input type="hidden" name="cmtt_index_showEmpty" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_showEmpty" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_showEmpty' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish to display
								empty letters (they will be grayed out). Uncheck to hide.
							</td>
						</tr>
						<tr>
							<th scope="row">What letter should be preselected in alphabetic index?</th>
							<td><input type="text" size="1" name="cmtt_index_initLetter"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_index_initLetter', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">You can choose which letter should be
								preselected. e.g. &quot;b&quot;(without quotes) would mean "B" will be preselected each
								time
								user visits Glossary Index page. If you leave this field empty the leftmost item on the
								alphabetic index is selected.
							</td>
						</tr>
						<tr>
							<th scope="row">Consider non-latin letters separate from their latin base?</th>
							<td>
								<input type="hidden" name="cmtt_index_nonLatinLetters" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_nonLatinLetters" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_nonLatinLetters', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">With this setting you can control how the
								non-latin letters used in many national character sets should be displayed on the
								Glossary
								Index alphabetical list. When this setting is unchecked the terms starting with: "A" and
								"Á"
								will be displayed for "A".
							</td>
						</tr>
						<tr>
							<th scope="row">Use titles for sorting instead of permalinks?</th>
							<td>
								<input type="hidden" name="cmtt_index_sortby_title" value="0"/>
								<input type="checkbox"
								       name="cmtt_index_sortby_title" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_index_sortby_title', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">By default the terms in the Glossary Index
								are sorted by their slug(permalink part), which allows to differentiate terms with the
								same title (multiple meanings). You can switch to sorting by title if that better suits
								your needs.
							<td>
						</tr>
                        <tr>
                            <th scope="row">Enable Numeric Sorting</th>
                            <td>
                                <input type="hidden" name="cmtt_enable_numeric_sorting" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_enable_numeric_sorting" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_enable_numeric_sorting', 1 ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Check this option to enable numeric sorting
                                for terms with numbers in the title. If enabled, numeric values within titles will be
                                compared as numbers instead of lexicographically.
                            <td>
                        </tr>
						<tr>
							<th scope="row">What locale should be used for sorting?</th>
							<td><input type="text" size="4" name="cmtt_index_locale"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_index_locale', get_locale() ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container"> You can specify the locale which should be
								used for sorting the items on Glossary Index eg. 'de_DE', 'it_IT'. If left empty the
								locale of the WordPress installation will be used.
								<br/><i>Works only if the "intl" library is installed (see "Server Information"
									tab).</i></td>
						</tr>
						<tr>
							<th scope="row">Limit items in the glossary index page</th>
							<td><input type="text" name="cmtt_limitNum"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_limitNum', 0 ); ?>"/></td>
							<td colspan="2" class="cm_field_help_container">How many items in the glossary index page
								should be displayed
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="tabs-3" class="settings-tab">
				<div class="cminds_settings_toggle_tabs cminds_settings_toggle-opened">Toggle All</div>
				<div class="block">
					<h3 class="section-title">
						<span>Glossary Term - Display</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Use custom template for terms?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryUseTemplate" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryUseTemplate" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryUseTemplate' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If you select this option then the plugin
								will
								search for the custom template for the glossary term page. <br/>
								If you want to customize it, you can copy the file from: <br/>
								<strong><?php echo CMTT_PLUGIN_DIR; ?>theme/Tooltip/single-glossary.php</strong> to
								<br/>
								<strong><?php echo get_stylesheet_directory(); ?>/Tooltip/single-glossary.php</strong>
								<br/>
								(If the plugin doesn't find the template in your theme's folder it will use the default
								one)
							</td>
						</tr>
						<tr>
							<th scope="row">Choose the template for glossary term?</th>
							<td>
								<select name="cmtt_glossaryPageTermTemplate">
									<?php
									$selectedTemplate = \CM\CMTT_Settings::get( 'cmtt_glossaryPageTermTemplate', 0 );
									$templates        = CMTT_Custom_Templates::getPageTemplatesOptions();
									?>
									<?php foreach ( $templates as $templateKey => $template ) : ?>
										<option
											value="<?php echo $templateKey; ?>" <?php selected( $templateKey, $selectedTemplate ); ?>><?php echo $template; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose the page template of the current
								theme or set default.
							</td>
						</tr>
						<tr>
							<th scope="row">Show the sharing box on the Glossary Term Page?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryShowShareBoxTermPage" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryShowShareBoxTermPage" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryShowShareBoxTermPage' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish to show the
								"Share This" box on the Glossary Index Page with links to Facebook, Twitter, Google+ and
								LinkedIn.
							</td>
						</tr>
						<tr>
							<th scope="row">Show back link on the top</th>
							<td>
								<input type="hidden" name="cmtt_glossary_addBackLink" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_addBackLink" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_addBackLink' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show link
								back to Glossary Index from glossary term page
							</td>
						</tr>
						<tr>
							<th scope="row">Show back link on the bottom</th>
							<td>
								<input type="hidden" name="cmtt_glossary_addBackLinkBottom" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_addBackLinkBottom" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_addBackLinkBottom' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show link
								back to Glossary Index from glossary term page
							</td>
						</tr>
						<tr>
							<th scope="row">Remove comments from term page</th>
							<td>
								<input type="hidden" name="cmtt_glossaryRemoveCommentsTermPage" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryRemoveCommentsTermPage" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryRemoveCommentsTermPage' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to remove the
								comments support form the term pages.
							</td>
						</tr>
						<tr>
							<th scope="row">Display alphabetical list on top of Term Page?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTermShowListnav" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTermShowListnav" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTermShowListnav' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to display
								the
								alphabetical list on top of Glossary Term Page.
							</td>
						</tr>
						<tr>
							<th scope="row">Display embed button on top of the Term Page?</th>
							<td>
								<input type="hidden" name="cmtt_embed_enabled" value="0"/>
								<input type="checkbox"
								       name="cmtt_embed_enabled" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_embed_enabled', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to display
								the
								button allowing to embed the term on top of Glossary Term Page.
							</td>
						</tr>
						<tr>
							<th scope="row">Content to be displayed before the Glossary Term description</th>
							<td>
								<textarea cols="30" rows="4" style="resize: both"
								          placeholder="You can put anything here, including HTML and shortcodes"
								          name="cmtt_glossaryContentBefore"><?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryContentBefore', '' ); ?></textarea>
							</td>
							<td colspan="2" class="cm_field_help_container">You can put anything here, including HTML,
								shortcodes. It will be displayed right before the description.
							</td>
						</tr>
						<tr>
							<th scope="row">Content to be displayed after the Glossary Term description</th>
							<td>
								<textarea cols="30" rows="4" style="resize: both"
								          placeholder="You can put anything here, including HTML and shortcodes"
								          name="cmtt_glossaryContentAfter"><?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryContentAfter', '' ); ?></textarea>
							</td>
							<td colspan="2" class="cm_field_help_container">You can put anything here, including HTML,
								shortcodes. It will be displayed right after the description.
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Glossary Term - Links</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Remove link to the glossary term page?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTermLink" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTermLink" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTermLink' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you do not want to
								show
								links from posts or pages to the glossary term pages. This will only apply to Post /
								Pages
								and not to the Glossary Index page, for Glossary Index page please visit index page tab
								in
								settings. Keep in mind that the plugin use a <strong>&lt;span&gt;</strong> tag instead
								of a
								link tag and if you are using a custom CSS you should take this into account
							</td>
						</tr>
						<tr>
							<th scope="row">Simplify term permalink</th>
							<td>
								<input type="hidden" name="cmtt_withoutGlossaryForTermLink" value="0"/>
								<input type="checkbox"
								       name="cmtt_withoutGlossaryForTermLink" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_withoutGlossaryForTermLink', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enable this option if you want to remove
								"glossary" slug from term permalink
								<br>Example: www.example.com/term
								<br> instead of www.example.com/glossary/term
								<br> <strong>Warning! Using the same slug for term and post/page will make the latter
									inaccessible.</strong></td>
						</tr>
						<tr>
							<th scope="row">Add rel="nofollow" to term links</th>
							<td>
								<input type="hidden" name="cmtt_addNofollowToTermLink" value="0"/>
								<input type="checkbox"
								       name="cmtt_addNofollowToTermLink" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_addNofollowToTermLink', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Enable this option if you want to add
								attribute rel="nofollow" to all term links.
							</td>
						</tr>
						<tr>
							<th scope="row">Open glossary term page in a new windows/tab?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryInNewPage" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryInNewPage" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryInNewPage' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want glossary term
								page to open in a new window/tab.
							</td>
						</tr>
						<tr>
							<th scope="row">Show HTML "title" attribute for glossary links</th>
							<td>
								<input type="hidden" name="cmtt_showTitleAttribute" value="0"/>
								<input type="checkbox"
								       name="cmtt_showTitleAttribute" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_showTitleAttribute' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to use
								glossary
								name as HTML "title" for link
							</td>
						</tr>
						<tr class="whole-line">
							<th scope="row">Link underline</th>
							<td>Style: <select name="cmtt_tooltipLinkUnderlineStyle" style="width: 150px;">
									<option
										value="none" <?php selected( 'none', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineStyle' ) ); ?>>
										None
									</option>
									<option
										value="solid" <?php selected( 'solid', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineStyle' ) ); ?>>
										Solid
									</option>
									<option
										value="dotted" <?php selected( 'dotted', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineStyle' ) ); ?>>
										Dotted
									</option>
									<option
										value="dashed" <?php selected( 'dashed', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineStyle' ) ); ?>>
										Dashed
									</option>
								</select><br/>
								Width: <input type="number" name="cmtt_tooltipLinkUnderlineWidth"
								              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineWidth' ); ?>"
								              step="1"
								              min="0" max="10"/>px<br/>
								Color: <input type="text" class="colorpicker" name="cmtt_tooltipLinkUnderlineColor"
								              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineColor' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Set style of glossary link underline</td>
						</tr>
						<tr class="whole-line">
							<th scope="row">Link underline (hover)</th>
							<td>Style: <select name="cmtt_tooltipLinkHoverUnderlineStyle" style="width: 150px;">
									<option
										value="none" <?php selected( 'none', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineStyle' ) ); ?>>
										None
									</option>
									<option
										value="solid" <?php selected( 'solid', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineStyle' ) ); ?>>
										Solid
									</option>
									<option
										value="dotted" <?php selected( 'dotted', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineStyle' ) ); ?>>
										Dotted
									</option>
									<option
										value="dashed" <?php selected( 'dashed', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineStyle' ) ); ?>>
										Dashed
									</option>
								</select><br/>
								Width: <input type="number" name="cmtt_tooltipLinkHoverUnderlineWidth"
								              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineWidth' ); ?>"
								              step="1" min="0" max="10"/>px<br/>
								Color: <input type="text" class="colorpicker" name="cmtt_tooltipLinkHoverUnderlineColor"
								              value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineColor' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Set style of glossary link underline on
								mouse
								hover
							</td>
						</tr>
						<tr>
							<th scope="row">Link text color</th>
							<td><input type="text" class="colorpicker" name="cmtt_tooltipLinkColor"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipLinkColor' ); ?>"/></td>
							<td colspan="2" class="cm_field_help_container">Set color of glossary link text color</td>
						</tr>
						<tr>
							<th scope="row">Link text color (hover)</th>
							<td><input type="text" class="colorpicker" name="cmtt_tooltipLinkHoverColor"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverColor' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Set color of glossary link text color on
								mouse
								hover
							</td>
						</tr>
						<tr>
							<th scope="row">Display term icon on the Glossary Index page?</th>
							<td>
								<input type="hidden" name="cmtt_tooltipLinkIconOnGlossaryPage" value="0"/>
								<input type="checkbox"
								       name="cmtt_tooltipLinkIconOnGlossaryPage" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipLinkIconOnGlossaryPage' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to display
								glossary term icon on Glossary Index page.
							</td>
						</tr>
						<tr>
							<th scope="row">Display term icon when term found in the content?</th>
							<td>
								<input type="hidden" name="cmtt_tooltipLinkIconInContent" value="0"/>
								<input type="checkbox"
								       name="cmtt_tooltipLinkIconInContent" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipLinkIconInContent' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to display
								glossary term icon when term found in the content.
							</td>
						</tr>
						<tr>
							<th scope="row">Display term icon on the Term page?</th>
							<td>
								<input type="hidden" name="cmtt_tooltipLinkIconOnTermPage" value="0"/>
								<input type="checkbox"
								       name="cmtt_tooltipLinkIconOnTermPage"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipLinkIconOnTermPage', 1 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to display glossary term icon on the Term page.
							</td>
						</tr>
						<tr>
							<?php $selectedIcon = \CM\CMTT_Settings::get( 'cmtt_tooltipLinkIconDefault', '' ); ?>
							<th scope="row">Default icon</th>
							<td>
								<label for="cmtt_term_icon">
									<input id="cmtt_tooltipLinkIconDefault"
									       type="text"
									       name="cmtt_tooltipLinkIconDefault" value="<?php echo $selectedIcon; ?>"/>
									<input class="button dashicons-picker"
									       type="button"
									       value="Choose Icon"
									       data-target="#cmtt_tooltipLinkIconDefault"
									       data-preview="#cmtt_term_icon_preview"/>
									<span style="font-size: 15px;padding: 4px;display:block;">
										<?php echo __( 'Preview:', 'cm-tooltip-glossary' ); ?>
										<span id="cmtt_term_icon_preview"
										      class="dashicons <?php echo $selectedIcon; ?> "></span>
									</span>
								</label>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the default icon for all terms. You can define an icon for a term on the term
								edit page.
							</td>
						</tr>
						<tr>
							<th scope="row">Default icon color</th>
							<td>
								<input type="text"
								       class="colorpicker"
								       name="cmtt_tooltipLinkIconDefaultColor"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_tooltipLinkIconDefaultColor', '' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the default icon color
							</td>
						</tr>
						<tr>
							<?php
							$selectedIconPos = \CM\CMTT_Settings::get( 'cmtt_tooltipLinkIconDefaultPosition', 'right' );
							$positionsArr    = array( 'left', 'right' );
							?>
							<th scope="row">Default icon position</th>
							<td>
								<select name="cmtt_tooltipLinkIconDefaultPosition"
								        id="cmtt_tooltipLinkIconDefaultPosition">
									<?php
									foreach ( $positionsArr as $pos ) {
										$selected = $selectedIconPos == $pos ? 'selected' : '';
										echo '<option value="' . $pos . '" ' . $selected . '>' . ucfirst( $pos ) . '</option>';
									}
									?>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select the default icon position
							</td>
						</tr>
						<tr>
							<th scope="row">Link icon to the Term page</th>
							<td>
								<input type="hidden" name="cmtt_tooltipLinkIconToTerm" value="0"/>
								<input type="checkbox"
								       name="cmtt_tooltipLinkIconToTerm"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipLinkIconToTerm', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want the term icon links to the Term page.<br/>
								<strong>Note:</strong> you can use this option only if the option "Remove link to the
								glossary term page?"
								is enabled.
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Glossary Term - Related Articles</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>

					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Show related articles</th>
							<td>
								<input type="hidden" name="cmtt_glossary_showRelatedArticles" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_showRelatedArticles" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_showRelatedArticles' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show list
								of
								related articles (posts, pages) on glossary term description page
							</td>
						</tr>
                        <tr>
                            <th scope="row">Display order on the term page</th>
                            <td>
                                <input type="hidden" name="cmtt_glossary_RelatedArticles_order" value="0"/>
                                <input type="number" min="0" step="1"
                                       name="cmtt_glossary_RelatedArticles_order"  value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_RelatedArticles_order' , 1); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Choose the display order of the related articles block on the term page
                            </td>
                        </tr>
						<tr>
							<th scope="row">Order of the related articles by:</th>
							<td>
								<select name="cmtt_glossary_relatedArticlesOrder">
									<option
										value="menu_order" <?php selected( 'menu_order', \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesOrder' ) ); ?>>
										Menu Order
									</option>
									<option
										value="post_title" <?php selected( 'post_title', \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesOrder' ) ); ?>>
										Post Title
									</option>
									<option
										value="post_date DESC" <?php selected( 'post_date DESC', \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesOrder' ) ); ?>>
										Publising Date DESC
									</option>
									<option
										value="post_date ASC" <?php selected( 'post_date ASC', \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesOrder' ) ); ?>>
										Publising Date ASC
									</option>
									<option
										value="post_modified DESC" <?php selected( 'post_modified DESC', \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesOrder' ) ); ?>>
										Last Modified DESC
									</option>
									<option
										value="post_modified ASC" <?php selected( 'post_modified ASC', \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesOrder' ) ); ?>>
										Last Modified ASC
									</option>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">How the related articles should be ordered?
							</td>
						</tr>
						<tr>
							<th scope="row">Number of related articles:</th>
							<td><input type="number" name="cmtt_glossary_showRelatedArticlesCount"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_showRelatedArticlesCount' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">How many related articles should be shown?
							</td>
						</tr>
						<tr>
							<th scope="row">Open related articles in new tab?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_relatedArticlesNewTab" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_relatedArticlesNewTab" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesNewTab', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to open
								related
								articles in new tab.
							</td>
						</tr>
						<tr>
							<th scope="row">Index rebuild interval:</th>
							<td>
								<select name="cmtt_glossary_relatedCronInterval">
									<?php
									$types            = wp_get_schedules();
									$selectedInterval = \CM\CMTT_Settings::get( 'cmtt_glossary_relatedCronInterval', 'daily' );
									?>
									<option
										value="none" <?php selected( 'none', $selectedInterval ); ?>><?php _e( 'Never', 'cm-tooltip-glossary' ); ?></option>
									<?php foreach ( $types as $typeName => $type ) : ?>
										<option
											value="<?php echo $typeName; ?>" <?php selected( $typeName, $selectedInterval ); ?>><?php echo $type['display']; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose how often the related articles index
								is
								being rebuilt.
							</td>
						</tr>
						<tr>
							<th scope="row">Index rebuild hour:</th>
							<td><input type="time" placeholder="00:00" size="5" name="cmtt_glossary_relatedCronHour"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_relatedCronHour' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose the hour when the Related Articles
								Rebuild should take place. The hour should be properly formatted string eg. 23:00 or 1
								AM
							</td>
						</tr>
						<tr>
							<th scope="row">Post types to index:</th>
							<td>
								<input type="hidden" name="cmtt_glossary_showRelatedArticlesPostTypesArr" value=""/>
								<select multiple name="cmtt_glossary_showRelatedArticlesPostTypesArr[]">
									<?php
									$types = \CM\CMTT_Settings::get( 'cmtt_glossary_showRelatedArticlesPostTypesArr' );
									foreach ( get_post_types() as $type ) :
										?>
										<option value="<?php echo $type; ?>"
											<?php
											if ( is_array( $types ) && in_array( $type, $types ) ) {
												echo 'selected';
											}
											?>
										><?php echo $type; ?></option>
									<?php endforeach; ?>
								</select></td>
							<td colspan="2" class="cm_field_help_container">Which post types should be indexed? (select
								more by holding down ctrl key)
							</td>
						</tr>
						<tr>
							<th scope="row">Related articles index rebuild chunk size:</th>
							<td>
								<input type="text" name="cmtt_glossary_relatedArticlesCrawlChunkSize"
								       value="<?php echo esc_attr( \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesCrawlChunkSize', 500 ) ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Since rebuilding the Glossary Index requires
								a
								lot of resources, both memory and time.
								It has to be done in chunks. The optimal size of the chunk depends on your server.
								If after clicking the button page goes blank, try to make this value much smaller and
								try to
								rebuild it again.
							</td>
						</tr>
						<tr class="whole-line">
							<th scope="row">Refresh related articles index:</th>
							<td>
								<input type="submit" name="cmtt_glossaryRelatedRefresh" value="Rebuild Index!"
								       class="button"/>
								<br/>
								<?php if ( CMTT_Related::showContinueButton() ) : ?>
									<input type="submit" name="cmtt_glossaryRelatedRefreshContinue"
									       value="Continue indexing" class="button"/>
									<br/>
								<?php endif; ?>
								<span><?php echo CMTT_Related::getRemainingArticlesCount(); ?></span>
								<span
									style="color:red;display:inline-block;"><?php echo CMTT_Related::getParsingProblems(); ?></span>
							</td>
							<td colspan="2" class="cm_field_help_container">The index for relations between articles
								(posts, pages) and glossary terms is being rebuilt on daily basis. Click this button if
								you
								want to do it manually (it may take a while)
							</td>
						</tr>
						<tr>
							<th scope="row">Auto-add parsed pages to related articles index?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_relatedFillAfterParsing" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_relatedFillAfterParsing" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_relatedFillAfterParsing', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to
								automatically add the parsed pages to the glossary index when they're parsed.
							</td>
						</tr>
						<tr>
							<th scope="row">Display the related article's excerpt?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_relatedShowExcerpt" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_relatedShowExcerpt" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_relatedShowExcerpt', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to display
								the
								excerpts of the related articles.
							</td>
						</tr>
						<tr>
							<th scope="row">Paginate Related articles</th>
							<td>
								<input type="hidden" name="cmtt_glossary_relatedArticlesPagination" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_relatedArticlesPagination" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesPagination', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to paginate
								Related articles
							</td>
						</tr>
						<tr>
							<th scope="row">Maximum number of related articles:</th>
							<td>
								<input type="number" name="cmtt_glossary_relatedArticlesLimit" min="1"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_relatedArticlesLimit' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Maximum number of related articles for
								pagination
							</td>
						</tr>
					</table>
				</div> <!-- Related Articles block end -->
				<div class="block">
					<h3 class="section-title">
						<span>Glossary Term - Custom Related Articles</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Show custom related articles</th>
							<td>
								<input type="hidden" name="cmtt_glossary_showCustomRelatedArticles" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_showCustomRelatedArticles" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_showCustomRelatedArticles', true ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show list
								of
								custom related articles
							</td>
						</tr>
						<tr>
							<th scope="row">Number of custom related articles:</th>
							<td><input type="number" name="cmtt_glossary_showCustomRelatedArticlesCount"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_showCustomRelatedArticlesCount' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">How many custom related articles should be
								shown?
							</td>
						</tr>
						<tr>
							<th scope="row">Open custom related articles in new tab?</th>
							<td>
								<input type="hidden" name="cmtt_glossary_customRelatedArticlesNewTab" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_customRelatedArticlesNewTab" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_customRelatedArticlesNewTab', '1' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to open the
								custom related articles in new tab.
							</td>
						</tr>
					</table>
				</div> <!-- Custom Related Articles block end -->
				<div class="block">
					<h3 class="section-title">
						<span>Glossary Term - Related Terms</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24" fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Disable related terms on glossary term pages:</th>
							<td>
								<input type="hidden" name="cmtt_glossaryDisableRelatedTermsForTerms" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryDisableRelatedTermsForTerms" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryDisableRelatedTermsForTerms' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you don't want to show
								list of related terms on glossary term pages
							</td>
						</tr>
						<tr>
							<th scope="row">Show related glossary terms in a separate list</th>
							<td>
								<input type="hidden" name="cmtt_glossary_showRelatedArticlesMerged" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_showRelatedArticlesMerged" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_showRelatedArticlesMerged' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show list
								of
								related glossary terms in the separate list.
								If this option is not checked, the list of related articles and glossary terms will be
								the
								same list.
							</td>
						</tr>
						<tr>
							<th scope="row">Number of related glossary terms:</th>
							<td><input type="number" name="cmtt_glossary_showRelatedArticlesGlossaryCount"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_showRelatedArticlesGlossaryCount' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">How many related glossary terms should be
								shown? Works only if "Show related articles and glossary terms together" is enabled
							</td>
						</tr>
						<tr>
							<th scope="row">Show linked glossary terms list under post/page?</th>
							<td>
								<input type="hidden" name="cmtt_showRelatedTermsList" value="0"/>
								<input type="checkbox"
								       name="cmtt_showRelatedTermsList" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_showRelatedTermsList' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show the
								widget containing a list of all glossary items found in the post/page
							</td>
						</tr>
						<tr>
							<th scope="row">Show tooltip for related terms?</th>
							<td>
								<input type="hidden" name="cmtt_showRelatedTermTooltip" value="0"/>
								<input type="checkbox"
								       name="cmtt_showRelatedTermTooltip" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_showRelatedTermTooltip', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show
								tooltips for related terms
							</td>
						</tr>
						<tr>
							<th scope="row">Open related terms in new tab?</th>
							<td>
								<input type="hidden" name="cmtt_showRelatedTermNewTab" value="0"/>
								<input type="checkbox"
								       name="cmtt_showRelatedTermNewTab" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_showRelatedTermNewTab', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to open the
								related term links in new tab
							</td>
						</tr>
						<tr>
							<th scope="row">Disable related terms update?</th>
							<td>
								<input type="hidden" name="cmtt_disableRelatedTermsUpdate" value="0"/>
								<input type="checkbox"
								       name="cmtt_disableRelatedTermsUpdate" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_disableRelatedTermsUpdate', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to disable the
								related terms updating while parsing. Related terms wouldn't be updated if enabled.
							</td>
						</tr>
					</table>
				</div> <!-- Related Terms block end -->
				<div class="block">
					<h3 class="section-title">
						<span>Glossary Term - Synonyms</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24"
						     fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Show synonyms list</th>
							<td>
								<input type="hidden" name="cmtt_glossary_addSynonyms" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_addSynonyms" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_addSynonyms' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show list
								of
								synonyms of the term on glossary term description page
							</td>
						</tr>
                        <tr>
							<th scope="row">Display order on the term page</th>
							<td>
								<input type="hidden" name="cmtt_glossary_synonyms_order" value="0"/>
								<input type="number" min="0" step="1"
								       name="cmtt_glossary_synonyms_order"  value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_synonyms_order' , 1); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Choose the display order of the synonyms block on the term page
							</td>
						</tr>
						<tr>
							<th scope="row">Show synonyms list in tooltip</th>
							<td>
								<input type="hidden" name="cmtt_glossary_addSynonymsTooltip" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossary_addSynonymsTooltip" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossary_addSynonymsTooltip' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show the
								list of synonyms of the term tooltip
							</td>
						</tr>
						<tr>
							<th scope="row">Show synonyms in Glossary Index Page</th>
							<td>
								<input type="hidden" name="cmtt_glossarySynonymsInIndex" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossarySynonymsInIndex" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossarySynonymsInIndex' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show
								synonyms as terms in Glossary Index Page
							</td>
						</tr>
						<tr>
							<th scope="row">Show synonyms before term description</th>
							<td>
								<input type="hidden" name="cmtt_glossarySynonymsBeforeContent" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossarySynonymsBeforeContent" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossarySynonymsBeforeContent', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show
								synonyms before term description on the term page
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Glossary Term - Taxonomies</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24"
						     fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Show categories on Glossary Term page?</th>
							<td>
								<input type="hidden" name="cmtt_term_show_taxonomy_glossary-categories" value="0"/>
								<input type="checkbox"
								       name="cmtt_term_show_taxonomy_glossary-categories" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_term_show_taxonomy_glossary-categories', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show list
								of
								categories of the term on glossary term description page
							</td>
						</tr>
						<tr>
							<th scope="row">Position of categories on Glossary Term page?</th>
							<td>
								<select name="cmtt_term_position_taxonomy_glossary-categories">
									<option
										value="top" <?php selected( 'top', \CM\CMTT_Settings::get( 'cmtt_term_position_taxonomy_glossary-categories', 'top' ) ); ?>>
										Top
									</option>
									<option
										value="bottom" <?php selected( 'bottom', \CM\CMTT_Settings::get( 'cmtt_term_position_taxonomy_glossary-categories', 'top' ) ); ?>>
										Bottom
									</option>
								</select>
								<br/>
							</td>
							<td colspan="2" class="cm_field_help_container">Set the position of the Categories displayed
								on the Glossary Term page.
							</td>
						</tr>
                        <tr>
                            <th scope="row">Separator for categories on the term page</th>
                            <td>
                                <input type="text"
                                       name="cmtt_term_separator_taxonomy_glossary-categories"
                                       value="<?php echo esc_attr(\CM\CMTT_Settings::get( 'cmtt_term_separator_taxonomy_glossary-categories', ', ' )); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Set separator that will be used for categories list on the term page
                            </td>
                        </tr>
						<tr>
							<th scope="row">Show tags on Glossary Term page?</th>
							<td>
								<input type="hidden" name="cmtt_term_show_taxonomy_glossary-tags" value="0"/>
								<input type="checkbox"
								       name="cmtt_term_show_taxonomy_glossary-tags" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_term_show_taxonomy_glossary-tags', \CM\CMTT_Settings::get( 'cmtt_glossaryTermShowTags', false ) ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to show list
								of
								tags of the term on glossary term description page
							</td>
						</tr>
						<tr>
							<th scope="row">Position of tags on Glossary Term page?</th>
							<td>
								<select name="cmtt_term_position_taxonomy_glossary-tags">
									<option
										value="top" <?php selected( 'top', \CM\CMTT_Settings::get( 'cmtt_term_position_taxonomy_glossary-tags', 'top' ) ); ?>>
										Top
									</option>
									<option
										value="bottom" <?php selected( 'bottom', \CM\CMTT_Settings::get( 'cmtt_term_position_taxonomy_glossary-tags', 'top' ) ); ?>>
										Bottom
									</option>
								</select>
								<br/>
							</td>
							<td colspan="2" class="cm_field_help_container">Set the position of the Categories displayed
								on the Glossary Term page.
							</td>
						</tr>
                        <tr>
                            <th scope="row">Separator for tags on the term page</th>
                            <td>
                                <input type="text"
                                       name="cmtt_term_separator_taxonomy_glossary-tags"
                                       value="<?php echo esc_attr(\CM\CMTT_Settings::get( 'cmtt_term_separator_taxonomy_glossary-tags', ', ' )); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Set separator that will be used for tags list on the term page
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Display order on the term page</th>
                            <td>
                                <input type="hidden" name="cmtt_glossary_taxonomies_order" value="0"/>
                                <input type="number" min="0" step="1"
                                       name="cmtt_glossary_taxonomies_order"  value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_taxonomies_order' , 1); ?>"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Choose the display order of the taxonomies block on the term page
                            </td>
                        </tr>
					</table>
				</div>
			</div>
			<div id="tabs-4" class="settings-tab">
				<div class="cminds_settings_toggle_tabs cminds_settings_toggle-opened">Toggle All</div>
				<div class="block">
					<h3 class="section-title">
						<span>Tooltip - Content</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24"
						     fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<?php do_action( 'cminds_cmtt_admin_tooltip_preview' ); ?>
					<table class="floated-form-table form-table  tt-table  ">
						<tr>
							<th scope="row">Show tooltip?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTooltip" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTooltip" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTooltip' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you wish to show a
								tooltip.
								By default the tooltip will appear on hover, to show tooltips on click also enable
								option
								"Display tooltips on click". The tooltip can be styled differently using the tooltip.css
								and tooltip.js files in the plugin folder.
							</td>
						</tr>
						<tr>
							<th scope="row">Is clickable?</th>
							<td>
								<input type="hidden" name="cmtt_tooltipIsClickable" value="0"/>
								<input type="checkbox"
								       name="cmtt_tooltipIsClickable" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipIsClickable', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">With this option you can choose:<br/>
								<strong>TRUE</strong> - the tooltip should be stationary and clickable<br/>
								<strong>FALSE</strong> - the tooltip should be floating and unclickable(like in Tooltip
								Free)<br/>
							</td>
						</tr>
						<tr>
							<th scope="row">Clicking on tooltip redirects to term?</th>
							<td>
								<input type="hidden" name="cmtt_tooltipLinkWholeTooltip" value="0"/>
								<input type="checkbox"
								       name="cmtt_tooltipLinkWholeTooltip" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipLinkWholeTooltip', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">When this option is enabled by clicking
								anywhere
								in the tooltip user will be redirected to term as if they clicked the term link.<br/>
								<strong>Warning</strong> Only works if "Is clickable?" option is enabled and there's a
								link
								to the term page.<br/>
							</td>
						</tr>
						<tr>
							<th scope="row">Show tooltips for all users</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTooltipForAll" value="0"/>
								<input id="allowed_roles_checkbox" type="checkbox"
								       name="cmtt_glossaryTooltipForAll" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipForAll', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container"> Enable this option if you want to show
								tooltips
								for all users.<br>Disable if you want to show tooltips only for specific roles.
							</td>
						</tr>
						<tr>
							<th scope="row">Add term title to the tooltip content?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryAddTermTitle" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryAddTermTitle" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryAddTermTitle' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want the term
								title
								to appear in the tooltip content.
							</td>
						</tr>
						<?php $class = \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipForAll', 1 ) ? 'invisible_option' : 'visible_option'; ?>
						<tr id="allowed_roles_list" class="<?php echo $class; ?>">
							<th scope="row">Roles allowed to see tooltips:</th>
							<td class="field-multiselect">
								<input type="hidden" name="cmtt_glossaryRolesShowTooltip" value="0"/>
								<?php
								echo CMTT_Free::outputRolesList( 'cmtt_glossaryRolesShowTooltip', array(), true, 1 );
								?>
							</td>
							<td colspan="2" class="cm_field_help_container">Select user roles for which you want to show
								tooltips.
							</td>
						</tr>
						<tr>
							<th scope="row">Add term editlink to the tooltip content?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryAddTermEditlink" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryAddTermEditlink" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryAddTermEditlink' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want the term
								editlink to appear in the tooltip content (only for logged in users with "edit_posts"
								capability).
							</td>
						</tr>
						<tr>
							<th scope="row">Show links to related articles?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTooltipShowLink" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTooltipShowLink" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipShowLink' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you'd like to show the
								related articles list in the tooltip content.
							</td>
						</tr>
						<tr>
							<th scope="row">How many related articles should be shown?</th>
							<td>
								<input type="number" name="cmtt_glossaryTooltipAmountLinks"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipAmountLinks' ); ?>"
								       min="0"/>
							</td>
							<td colspan="2" class="cm_field_help_container">How many related articles should be shown in
								the
								tooltip?
							</td>
						</tr>
						<tr>
							<th scope="row">How many custom related articles should be shown?</th>
							<td>
								<input type="number" name="cmtt_glossaryTooltipAmountCustomLinks"
								       value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipAmountCustomLinks' ); ?>"
								       min="0"/>
							</td>
							<td colspan="2" class="cm_field_help_container">How many custom related articles should be
								shown
								in the tooltip?
							</td>
						</tr>
						<tr>
							<th scope="row">Strip the shortcodes?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTooltipStripShortcode" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTooltipStripShortcode" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipStripShortcode' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to strip the
								shortcodes from the glossary page description/excerpt before showing the tooltip.
							</td>
						</tr>
						<tr>
							<th scope="row">Limit tooltip length?</th>
							<td><input type="text" name="cmtt_glossaryLimitTooltip"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryLimitTooltip' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								Select this option if you want to show only a limited number of characters (minimum is
								30)
								and add "<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryTermDetailsLink' ); ?>" at the
								end
								of the
								tooltip text.<br/>
								<strong>The tooltip has to be clickable for users to be able to click this
									link.</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Add term page link to the end of the tooltip content?</th>
							<td>
                                <input type="hidden" name="cmtt_glossaryAddTermPagelink" value="0"/>
                                <?php
                                $addTermPagelink = \CM\CMTT_Settings::get( 'cmtt_glossaryAddTermPagelink', 0 );
                                ?>
                                <select name="cmtt_glossaryAddTermPagelink">
                                    <option value="0" <?php echo selected( '0', $addTermPagelink ); ?>>Don't add
                                    </option>
                                    <option value="2" <?php echo selected( '2', $addTermPagelink ); ?>>Add only if text
                                        is truncated
                                    </option>
                                    <option value="1" <?php echo selected( '1', $addTermPagelink ); ?>>Add always
                                    </option>
                                </select>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want the term page
								link to appear in the tooltip content.
							</td>
						</tr>
						<tr>
							<th scope="row">Open term page link in new tab?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryTermPageLinkTargetBlank" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryTermPageLinkTargetBlank" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryTermPageLinkTargetBlank', false ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want the term page
								link to be opened in new tab.
							</td>
						</tr>
						<tr>
                            <th scope="row">Symbol indicating the tooltip content has been limited</th>
							<td><input type="text" name="cmtt_glossaryLimitTooltipSymbol"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryLimitTooltipSymbol', '(...)' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">
								This option allows you to change the symbol which will be displayed in place where the
								tooltip content has been cut when it reaches the tooltip length limit.
							</td>
						</tr>
						<tr>
							<th scope="row">Remove all tooltip filters</th>
							<td>
								<input type="hidden" name="cmtt_glossaryNoFilters" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryNoFilters" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryNoFilters', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to remove all
								tooltip content filters. Warning: This overrides the options below.
							</td>
						</tr>
                        <tr>
                            <th scope="row">Add term page link to the end of the term description?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryAddTermPagelinkAfterDescription" value="0"/>
                                <input type="checkbox"
                                       name="cmtt_glossaryAddTermPagelinkAfterDescription" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryAddTermPagelinkAfterDescription', false ) ); ?>
                                       value="1"/>
                            </td>
                            <td colspan="2" class="cm_field_help_container">Select this option if you want the term page link to appear after the term description instead of in the end of the tooltip. Work only if the 'Add term page link to the end of the tooltip content?' option is enabled.
                            </td>
                        </tr>
						<tr>
							<th scope="row">Clean tooltip text?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryFilterTooltip" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryFilterTooltip" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryFilterTooltip' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to remove
								extra
								spaces and special characters from tooltip text.
							</td>
						</tr>
						<tr>
							<th scope="row">Leave the &lt;a&gt; tags?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryFilterTooltipA" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryFilterTooltipA" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryFilterTooltipA' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to preserve
								the
								html anchor tags in tooltip text.
							</td>
						</tr>
						<tr>
							<th scope="row">Leave the &lt;img&gt; tags?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryFilterTooltipImg" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryFilterTooltipImg" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryFilterTooltipImg' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to preserve
								the
								images in tooltip text.
							</td>
						</tr>
						<tr>
							<th scope="row">Use term excerpt for hover?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryExcerptHover" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryExcerptHover" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryExcerptHover' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want to use the
								term
								excerpt (if it exists) as hover text.
								<br/>NOTE: You have to manually create the excerpts for term pages using the "Excerpt"
								field.
							</td>
						</tr>
						<tr>
							<th scope="row">Terms case-sensitive?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryCaseSensitive" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryCaseSensitive" <?php checked( '1', \CM\CMTT_Settings::get( 'cmtt_glossaryCaseSensitive' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">Select this option if you want glossary
								terms
								to be case-sensitive.
							</td>
						</tr>
						<tr>
							<th scope="row">Content to be displayed before the Tooltip content</th>
							<td>
								<textarea cols="30" rows="4" style="resize: both"
								          placeholder="You can put anything here, including HTML and shortcodes"
								          name="cmtt_glossaryTooltipContentBefore"><?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipContentBefore', '' ); ?></textarea>
							</td>
							<td colspan="2" class="cm_field_help_container">You can put anything here, including HTML,
								shortcodes. It will be displayed right before the description.
							</td>
						</tr>
						<tr>
							<th scope="row">Content to be displayed after the Tooltip content</th>
							<td>
								<textarea cols="30" rows="4" style="resize: both"
								          placeholder="You can put anything here, including HTML and shortcodes"
								          name="cmtt_glossaryTooltipContentAfter"><?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipContentAfter', '' ); ?></textarea>
							</td>
							<td colspan="2" class="cm_field_help_container">You can put anything here, including HTML,
								shortcodes. It will be displayed right after the description.
							</td>
						</tr>
					</table>
				</div>
				<div class="block">
					<h3 class="section-title">
						<span>Tooltip - Mobile Support & Activation</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24"
						     fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row">Enable the mobile support?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryMobileSupport" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryMobileSupport" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryMobileSupport' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then on the mobile
								devices a link to the term page will appear on the bottom of the tooltip.
							</td>
						</tr>
						<tr>
							<th scope="row">Close tooltips only on button click?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryCloseOnlyOnButton" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryCloseOnlyOnButton" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryCloseOnlyOnButton', 0 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then the only way
								to
								close the tooltip on mobile devices will be by clicking the "Close icon".
								<br/><strong>Make sure that the "Show close icon" option is enabled, otherwise it won't
									be
									possible to close the tooltips!</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Close tooltips on mouse moveout?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryCloseOnMoveout" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryCloseOnMoveout" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryCloseOnMoveout', 1 ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then the tooltips
								will close when moving the cursor out of their bounds.
								<br/><strong>Make sure that the "Show close icon" option is enabled, otherwise it won't
									be
									possible to close the tooltips!</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Close tooltips on touch outside?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryCloseOnTouchAnywhere" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryCloseOnTouchAnywhere"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryCloseOnTouchAnywhere', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then it would be
								possible to close the tooltip on mobile devices will be by touching anywhere outside the
								tooltip.
								<br/><strong>You will be able to touch inside the tooltip to scroll the long
									tooltips!</strong>
							</td>
						</tr>
						<tr>
							<th scope="row">Disable parsing on mobile devices?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryMobileDisableParsing" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryMobileDisableParsing"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryMobileDisableParsing', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then plugin will
								not run parsing on the mobile devices.
							</td>
						</tr>
						<tr>
							<th scope="row">Disable tooltips on mobile devices?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryMobileDisableTooltips" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryMobileDisableTooltips"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryMobileDisableTooltips' ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then on the mobile
								devices the tooltips will not appear.
							</td>
						</tr>
						<tr>
							<th scope="row">Disable tooltips on desktops?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryDesktopDisableTooltips" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryDesktopDisableTooltips"
									<?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryDesktopDisableTooltips', 0 ) ); ?>
									   value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then on desktops
								the tooltips will not appear.
							</td>
						</tr>
						<tr>
							<th scope="row">Display tooltips on click?</th>
							<td>
								<input type="hidden" name="cmtt_glossaryShowTooltipOnClick" value="0"/>
								<input type="checkbox"
								       name="cmtt_glossaryShowTooltipOnClick" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_glossaryShowTooltipOnClick', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then on the
								tooltips
								will be displayed only when term is clicked not on hover (default).
							</td>
						</tr>
						<tr>
							<th scope="row">(Debug) Move tooltips in DOM tree dynamically?</th>
							<td>
								<input type="hidden" name="cmtt_tooltipMoveTooltipInDOM" value="0"/>
								<input type="checkbox"
								       name="cmtt_tooltipMoveTooltipInDOM" <?php checked( true, \CM\CMTT_Settings::get( 'cmtt_tooltipMoveTooltipInDOM', '0' ) ); ?>
								       value="1"/>
							</td>
							<td colspan="2" class="cm_field_help_container">If this option is enabled then on the
								tooltip
								HTML element
								will move in DOM tree when tooltip is displayed. <strong>Warning: don't change this
									option
									unless you know what you're doing.</strong>
							</td>
						</tr>
					</table>
				</div>

				<div class="block">
					<h3 class="section-title">
						<span>Tooltip - Featured Images</span>
						<svg class="tab-arrow" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
						     viewBox="0 0 24 24"
						     fill="#6BC07F">
							<path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"></path>
						</svg>
					</h3>
					<table class="floated-form-table form-table">
						<tr>
							<th scope="row"> Show featured image in tooltip?</th>
							<td>
								<select name="cmtt_glossary_tooltip_featuredImageDisplay">
									<option
										value="no" <?php selected( 'no', \CM\CMTT_Settings::get( 'cmtt_glossary_tooltip_featuredImageDisplay' ) ); ?>>
										No
									</option>
									<option
										value="above_content" <?php selected( 'above_content', \CM\CMTT_Settings::get( 'cmtt_glossary_tooltip_featuredImageDisplay' ) ); ?>>
										Above content
									</option>
									<option
										value="below_content" <?php selected( 'below_content', \CM\CMTT_Settings::get( 'cmtt_glossary_tooltip_featuredImageDisplay' ) ); ?>>
										Below Content
									</option>
									<option
										value="left_aligned" <?php selected( 'left_aligned', \CM\CMTT_Settings::get( 'cmtt_glossary_tooltip_featuredImageDisplay' ) ); ?>>
										Left Aligned
									</option>
									<option
										value="right_aligned" <?php selected( 'right_aligned', \CM\CMTT_Settings::get( 'cmtt_glossary_tooltip_featuredImageDisplay' ) ); ?>>
										Right Aligned
									</option>
								</select>
							</td>
							<td colspan="2" class="cm_field_help_container">Select the way you want the image to be
								displayed in the tooltip
							</td>
						</tr>
						<tr>
							<th scope="row">Image width:</th>
							<td><input type="text" name="cmtt_glossary_tooltip_imageWidth"
							           value="<?php echo \CM\CMTT_Settings::get( 'cmtt_glossary_tooltip_imageWidth', '100px' ); ?>"/>
							</td>
							<td colspan="2" class="cm_field_help_container">The image's width in the tooltip</td>
						</tr>
					</table>
				</div>
				<?php
				$additionalTooltipTabContent = apply_filters( 'cmtt_settings_tooltip_tab_content_after', '' );
				echo $additionalTooltipTabContent;
				?>
			</div>

		</div>
		<p class="submit" style="clear:left">
			<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'cm-tooltip-glossary' ); ?>"
			       name="cmtt_saveSettings"/>
		</p>
	</form>
</div>
