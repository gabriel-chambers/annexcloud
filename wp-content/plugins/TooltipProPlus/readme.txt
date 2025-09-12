=== Plugin Name ===
Name: CM Tooltip Glossary Pro+
Contributors: CreativeMindsSolutions
Donate link: https://www.cminds.com/
Tags: glossary, pages, posts, definitions, tooltip, automatic, hints, hint, tip, tool-tip
Requires at least: 3.3
Tested up to: 6.8
Stable tag: 4.4.1
Archive name: TooltipProPlus.zip

PRO+ Version! Parses posts for defined glossary terms and adds links to the static glossary page containing the definition and a tooltip with the definition.

== Description ==

Parses posts for defined glossary terms and adds links to the static glossary page containing the definition.  The plugin also creates a tooltip containing the definition which is displayed when users mouseover the term.  Based on [automatic-glossary](http://wordpress.org/extend/plugins/automatic-glossary/) and on [TooltipGlossary] (http://wordpress.org/extend/plugins/tooltipglossary/).

The code has been bug fixed based on TooltipGlossary and many new features added. A new tag was introduced to avoid using the Tooltip [glossary_exclude] text [/glossary_exclude].

The tooltip is created with JavaScript based on the article written by [Michael Leigeber](http://www.leigeber.com/author/michael/) [here](http://sixrevisions.com/tutorials/javascript_tutorial/create_lightweight_javascript_tooltip/) and can be customized and styled through the tooltip.css and tooltip.js files.

Alphabetical index for glossary list is based on [jQuery ListNav Plugin](http://www.ihwy.com/labs/jquery-listnav-plugin.aspx)

**More About this Plug**

You can find more information about CM Tooltip Glossary Pro+ at [CreativeMinds Website](https://tooltip.cminds.com/).


**More Plugins by CreativeMinds**

* [CM Ad Changer]( http://wordpress.org/extend/plugins/cm-ad-changer/ ) - Helps you manage, track and provide reports of how your advertising campaigns are being run and turns your WordPress site into an ad server.
* [CM Download Manager]( http://wordpress.org/extend/plugins/cm-download-manager ) - Allows users to upload, manage, track and support documents or files in a download directory listing database for others to contribute, use and comment upon.
* [CM Answers]( http://wordpress.org/extend/plugins/cm-answers/ ) - Allows users to post questions and answers (Q&A) in a Stack-overflow style community forum which is easy to use, customize and install. Comes with Social integration Shortcodes.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Define your glossary terms under the glossary menu item in the administration interface.  The title of the page should be the term.  The body content should be the definition.
4. Create a main glossary page (example "Glossary") with no body content if you wish to.  If you do not create this page then your terms will still be highlighted but there will not be a central listing of all your terms.
5. In the plugin's dashboard preferences, enter the main glossary page's id (optional as above)
6. There are a handful of other optional preferences available in the dashboard.

Note: You must have a call to wp_head() in your template in order for the tooltip js and css to work properly.  If your theme does not support this you will need to link to these files manually in your theme (not recommended).

== Frequently Asked Questions ==

= Does my main glossary page need to be titled "Glossary"? =

No.  It can be called anything.  In fact you don't even need to have a main glossary page.

= Do I need to manually type in an unordered list of my glossary terms on the glossary page? =

No.  Just leave that page blank.  The plugin creates the unordered list of terms automatically.

= How do I add glossary terms? =

Simply add a term under the 'Glossary' section in the adminstration interface.  Title it the glossary term (ex. "WordPress") and put the term's definition into the body (ex. "A neato Blogging Platform").

= What if I need to add or change a glossary term? =

Just add it or change it.  The links for your glossary terms are added to your page and post content on the fly so your glossary links will always be up to date.

= How do I prevent the glossary from parsing a paragraph =

Just wrap the paragraph with [glossary_exclude] paragraph text [/glossary_exclude].

= How do I define the Glossary link style =

You can use glossaryLink. You can also define glossaryLinkMain if you wish to have a different style in the main glossary page

== Screenshots ==

1. List of terms in Glossary
2. Tooltip for one term inside glossary page
3. Tooltip for one term inside a post
4. Glossary terms page in Admin panel
5. Glossary setting page in Admin

== Changelog ==

= 4.4.1 =
* Feature: added "Optimize for Speed" option
* Feature: Added option to disable updating related terms/articles
* Feature: Added the "Accordion view"
* Feature: Added the "Alternative Meanings content length on Glossary Term Page" option
* Feature: Added the "Highlight terms on its alternative meaning pages" option
* Improvement: Expanded the "Add term page link to the end of the tooltip content?" option
* Improvement: Tooltip style fixes
* Improvement: Improved pre-caching feature
* Improvement: Added {term_title} placeholder for the "Read More" label on Index page
* Bug: Fixed highlighting only first term occurrence including synonyms and variations
* Bug: Fixed alphabetic index with client-side pagination
* Bug: Improved compatibility with WooCommerce
* Bug: Small bugfixes

= 4.4.0 =
* Feature: Added options to change styles for "Cards view"
* Feature: Added option to change tooltip title text alignment
* Feature: Added option to change the background color for footnotes
* Feature: Added option to disable numeric sorting
* Improvement: Added support for overwriting single term template on archive page
* Bug: Fixed the bug in Enfold compatibility
* Bug: Fixed [glossary_exclude] shortcode

= 4.3.9 =
* Feature: Added new "Cards view" style for the Glossary Index page
* Improvement: Separated option to highlight each variations or synonyms

= 4.3.8 =
* Feature: Added option to include glossary Index page to Yoast SEO breadcrumbs
* Feature: Added option to enable pre-caching
* Feature: Added option to set the cache expiration time
* Feature: Added option to disable loader animation on search
* Feature: Added option to change the categories/tags list separator for the term page
* Bug: Fixed manual rebuilding related articles index

= 4.3.7 =
* Feature: Added option to control the term link position in the tooltip
* Bug: Fixed XSS issue in glossary_tooltip shortcode
* Bug: Fixed bug with excluding hyphenated words

= 4.3.6 =
* Bug: Small bugfix

= 4.3.5 =
* New Feature: Alphabetical Archive Widget
* Improvement: Added new animations for tooltip
* Feature: Added new option to parse parts of hyphenated terms separately
* Bug: Fixed the "Hide term from Glossary Index" functionality
* License package update

= 4.3.4 =
* Improvement: Security improvement
* Feature: Added new options to change positions of blocks with additional info on the term page
* Feature: Added option to change the tooltip's title background color per category
* Bug: Fixed export feature
* Bug: Fixed displaying color picker on categories pages
* Bug: Fixed a bug with alphabetical index

= 4.3.3 =
* Improvement: Improved import glossary feature
* Improvement: Added options to control caching on the Glossary Index and other posts separately
* Feature: Added "Prefix for related articles that are not glossary terms" label
* Feature: Added options to define the width and height of tooltips per category
* Bug: fixed bug with highlighting first occurrence only

= 4.3.2 =
* Improvement: Added the option allowing to skip the duplicate terms while importing
* Improvement: Improved compatibility with Avia Builder
* Bug: Fixed the issue with the missing aria-label for tooltip
* Bug: Small bugfixes

= 4.3.1 =
* Improvement: Improved export glossary feature
* Bug: Fixed issue with schema
* Bug: Small bugfixes

= 4.3.0 =
* Feature: Added "Display abbreviations next to the term title in Glossary Index" option
* Feature: Added "Enable normalization for term synonyms?" option
* Feature: Added [glossary_terms_amount] shortcode to display the total number of published terms
* Improvement: Fixed compatibility with screen readers
* Improvement: Added the '@id' property to schema
* Bug: Fixed displaying metaboxes on parsed post types
* Bug: Fixed Avia Builder compatibility
* Bug: Fixed small bugs

= 4.2.9 =
* Bug: Fixed a bug with a new feature "Custom Index Page letter"
* Bug: Fixed the bug with selecting Google Translate for the Doubleclick service
* Bug: Fixed the bug with the [glossary_search] not redirecting to the single term page
* Bug: Fixed some PHP 8.1 compatibility issues

= 4.2.8 =
* Improvement: Added the option to set the custom starting letter for each term in the glossary
* Improvement: Updated the styling of the Term Properties
* Bug: Fixed parsing issues on the pages with Astra theme
* Bug: Fixed a bug with the accented letters not showing up correctly in the Glossary Index page
* Bug: Fixed a bug with the "Display Alphabetical Index" not working correctly
* Bug: Fixed a bug with the [cm_tooltip_link_to_term] shortcode not working correctly
* Bug: Fixed a bug with the Negative Terms not working correctly
* Improvement: Added support for parsing in the Directorist plugin pages

= 4.2.7 =
* Bug: Fixed the bug with releated articles not appearing in the tooltips
* Bug: Fixed some performance issues on the Glossary Index page
* Bug: Fixed the conflict between the plugin and the Easy Table of Contents
* Bug: Fixed the bug with the Glossary Index being stuck on the loading

= 4.2.4 =
* Fixed issues with the Glossary Index when the alphabetic index is missing
* Removed the legacy set_timeout code from the main file

= 4.2.3 =
* Improvement: Added the supprot for the DefinedTerm Structured Data in the Glossary Index
* Bug: Fixed the bug with Amazon Banners not saving correctly (Ecommerce only)
* Bug: Fixed the issue with breaking links on mobile devices in the Glossary Index page
* Bug: Fixed the bug that the plugin stopped searching in the descriptions on the Glossary Index page
* Bug: Fixed the bug that caused the negative terms to stop working

= 4.2.2 =
* Feature: Added the option to enable/disable the "Mobile support" on per-term basis
* Improvement: Improved the performance of the Glossary Index, rewritten the caching system to speed up the sorting
* Bug: Fixed a bug with the category colors in Pro+/Ecommerce
* Bug: Fixed a bug with the related articles not being reindexed properly
* Bug: Small bugfixes

= 4.2.1 =
* Bug: Fixed a few rare bugs that appeared in version 4.2.0

= 4.2.0 =
* (ONLY IN Ecommerce version!) New Feature: ChatGPT integration
* New Feature: Custom styles for terms belonging to specific category (background color, text color, font size)
* New Feature: If "Taxonomy Index link" is defined for category, it can be used in the links for terms in that category
* Improvement: New parameters for the shortcode [glossary_tooltip tcolor="#ccc" bgcolor="#111" tsize="16px"] that
allow to change the text color, background color and font size in the tooltip
* Improvement: Changed the way synonyms/variants/abbreviations are working internally
* Improvement: Performance related code tweaks (for Glossary Index mostly)
* Improvement: Changed the size of the thumbnail loaded in the tooltip
* Improvement: Editlink in toolitp now opens in the new tab
* Improvement: Fixed the tooltip positioning
* Improvement: Increased the performance of the Glossary Export/Backup functionality
* Improvement: Option to select the number of terms for Glossary Export/Backup functionality
* Bug: Fixed the bug that stopped the synonyms from being parsed in correct order
* Bug: Fixed the bug that caused synonyms to appear in the wrong letter (same as the base term)
* Bug: Fixed the warning about the "forceHighlight" variable not being defined
* Bug: Fixed the bug with the underline styles of the parsed terms
* Bug: Fixed the bug with the definitons not linking to the term in the "Flipbox" Glossary Index view
* Bug: Fixed the double editlink in the tooltip
* Bug: Fixed some deprecated warnings for PHP 8.2

= 4.1.5 =
* Improvement: Added the option "Highlight first term occurrence - exception tag"
* Improvement: Added the option "Highlight first term occurrence - exception class"
* Improvement: Renamed the option from "Treat variations/synonyms the same as base term?" to "Highlight each variations/synonyms of the term?"
* Improvement: Improved the performance of the parsing when the "Highlight first term occurrence" is enabled
* Improvement: Added the option to rename the [glossary] shortcode
* Bug: Fixed bug which stopped one of the setting tabs from collapsing
* CSS fix: Fixed the problem with pagination on one of the Glossary Index views
* Bug: Fixed the Wikipedia output for the terms in non-english language
* Change: Changed the option: "Only show items on search?" to also work on letter click

= 4.1.2 =
* Bug: Fixed the rare bug with calculation of the animation time in the glossary index page
* Bug: Fixed the bug with tooltips not closing properly on the mouse moving out
* Feature: Added the support for parsing the content of the BricksBuilder
* Feature: Added the option to disable the tooltips on the Glossary Index page

= 4.1.1 =
* Bug: Fixed Settings export
* Bug: Fixed Settings import
* Bug: Fixed "Remove all options" button functionality
* Improvement: Added the param "hide_categories" to the [glossary] shortcode
* Improvement: Added the option "Disable parsing on mobile devices?" in Tooltip section.
* Bug: Fixed the display bugs in the Glossary Index
* Bug: Fixed the bug in option allowing to show Synonyms before content
* Improvement: Fixed the way the before/after fields are displayed on the Glossary Term page
* Improvement: Added the option "Add rel="nofollow" to term links"
* Bug: Fixed the positioning of the tooltip on the keyboard focus

= 4.0.12 =
* Bug: Fixed the issue with retaining the categories for [glossary] shortcode on search
* Bug: Fixed the bug with sharing icons
* Bug: Fixed the bug with backlinks affecting the Recent Posts Widget
* Improvement: Improved the accessibility attributes on the Glossary Index page
* Improvement: New option: "Select the heading level for the new letter?" in Glossary Index tab
* Improvement: New shortcode: [cmtt_synonyms] allowing to show synonyms anywhere on the term page.
* Improvement: Improved some deprecated JS calls

= 4.0.11 =
* Improvement: Support for Languages and Alternative Meanings working together
* Improvement: Added option to enable/disable the All results count on the Glossary Index
* Bug: Fixed the issue with category parsing when list of categories is empty
* Bug: Fixed the wrong description of 'itemspage' parameter for [glossary] shortcode
* Improvement: New parameter 'perpage' allowing to select the number of items per page for [glossary] shortcode
* Improvement: Added option allowing to select to only search in title/description in Glossary Index
* Improvement: Sorting the related articles from newest to oldest when rebuilding index
* Change: "Roles allowed to add/edit terms" is now also applied to import/export

= 4.0.10 =
* Bug: Fixes the term highlighting bug introduced in 4.0.9
* Improvement: Added support for adding forced categories with Community Terms addon [added in 1.3.0]
* Improvement: Tooltip font size setting now also applies to mobile link.

= 4.0.9 =
* Improvement: Added support for adding "private" terms with Community Terms addon [added in 1.2.23]
* Bug: Fixed small bugs

= 4.0.8 =
* Feature: Added option to highlight only every nth occurrence of the term
* Bug: Fixed bugs with server-side pagination
* Bug: Fixed some small bugs 
* Improvement: Improved the detection and reporting of the duplicate synonyms
* Optimization: Small code optimizations

= 4.0.7 =
* Bug: Fixed the problem with the link hover color not working
* Bug: Fixed some small bugs 
* Bug: Fixed bug with server-side pagination

= 4.0.6 =
* Improvement: Added option allowing to enable/disable the Oxygen Builder parsing
* Bug: Fixed some small bugs 

= 4.0.5 =
* Feature: Added options to hide part of the footnote definitions
* Improvement: Improved code responsible for excluding the parsing of scripts
* Feature: Added option to whitelist/blacklist the tooltip terms on category level

= 4.0.4 =
* Bug: Fixed the bug with footnotes linking to terms page when it should be disabled
* Bug: Fixed the bug with the related articles refreshing 
* Bug: Fixed the bug with the [glossary_related] shortcode not working
* Bug: Restored the missing tooltip styling settings (position and animations)
* Bug: Fixed a bug which caused Elementor pages to appear broken when tooltips were not in footer
* Feature: Added support for gttranslate.io service
* Feature: Added the option to "Open related terms in new tab" 
* Feature/optimization: Added the option "Run QuickScan before parsing?"
* Bug: Fixed bug with related articles causing duplicate entries
* Bug: Fixed the rare bug with the alphabetical list on the glossary term pages
* Bug: Fixed the bug with plugin not working correctly on latest version of Oxygen builder

= 4.0.3 =
* Major change: Major improvement to the styles of Settings, added foldable sections and search
* Added support for tooltips in FullScreen mode
* Added option: Clicking on tooltip redirects to term?
* Added option: Only highlight white-listed and non-blacklisted terms? (Pro+/Ecommerce only)
* Added metabox option: Overwrite the "Display terms as a footnotes" setting on this page.
* Added debug option: (Debug) Move tooltips in DOM tree dynamically?
* Settings change: moved the "Is clickable" option from Tooltip - Styling to Tooltip - Content section
* Fixed some JS errors

= 4.0.2 =
* Bug: Fixed the bug with the flushing of the rewrite rules
* Bug: Re-added some missing settings from before 4.0.0
* Feature: Two new options for the Footnotes display
* Bug: Fixed few small bugs

= 4.0.1 =
* Critical Bug: Fixed the bug appearing when SharingBox was enabled

= 4.0.0 =
* Feature: added option 'Display tooltips as a footnotes' and main footnotes links style options
* Feature: added full footnotes functionality - direct links, backlinks to called link, adopted for synonyms and variations
* Feature: added option 'Simplify term permalink'
* Feature: added ability to show the tooltip by users roles
* Feature: Added Languages export/import
* Feature: added option 'Force load scripts'
* Feature: added option 'Add support for audio files in tooltips'
* Feature: added option 'Highlight terms in BuddyBoss activity content'
* Fixed scroll JS bug at Divi theme
* Fixed footnotes for early synonyms logic
* Fixed Footnote font size option added
* Fixed case sensitive notation logic
* Fixed multiple synonyms adding
* Fixed small bugs
* Accessibility improvements
* License package update

= 3.9.14 =
* Feature: Oxygen integration
* Feature: Added label for categories dropdown on the Index page
* Bug: Fixed warnings from few options
* Bug: Fixed small bugs

= 3.9.13 =
* Feature: Added option to change tooltip z-index
* Feature: Added option to limit word description in the "Word of the day" widget
* Feature: Added option to import terms with the same title
* Bug: Fixed small bugs

= 3.9.12 =
* Feature: Added option to show "Read More" link only for truncated term description on the Index page
* Bug: Fixed JS error
* Bug: Fixed the problem with not showing term definition if it's created using Themify Builder

= 3.9.11 =
* Fixed small bugs

= 3.9.10 =
* Feature: Added option to set default icon for all terms
* Feature: Added [glossary_related] shortcode
* Feature: Added options to style index search form
* Bug: Fixed the problem with including HTML tags in the content attribute of the [glossary_tooltip] shortcode
* Bug: Fixed the problem with displaying additional information multiple times on Avada builder generated pages
* Minor fixes

= 3.9.9 =
* Bug: Fixed an issue with [glossary_exclude] shortcode

= 3.9.8 =
* Feature: Added options to choose search button background-color and text color
* Feature: Added options to choose cube button color
* Feature: Added option to ignore preselected letter and show all results on glossary search
* Feature: Added option to exclude hyphenated words
* Feature: Added option to exclude words in quotation marks
* Feature: Added options to modify term font size and weight on the Index page
* Feature: Improved "Image + term + definition" view
* Feature: Added option to show synonyms before term content
* Feature: Added option to convert content to initial encoding
* Feature: Added option to highlight term in archive descriptions
* Feature: Added negative words
* Feature: Added ability to enable/disable search for glossary items on post/page
* Bug: Fixed an issue in pagination styling
* Bug: Fixed the bug in javascript file
* Bug: Fixed the bug in the Merriam-Webster API

= 3.9.7 =
* Feature: Added option to add structured data to the Term page
* Feature: Added option to enable tooltips on specific page
* Feature: Added option to disable tooltips for terms from specific category
* Updated the Licensing Package

= 3.9.6 =
* Bug: Fixed a bug on the term page

= 3.9.5 =
* Bug/Feature: Fixed conflict with WordPress SEO - ACF Content Analysis
* Feature: Added support for AMP and AMP for WP plugins
* Feature: Added new shortcode [cmtgend]
* Feature: Added new designs for Glossary index page
* Bug: Fixed the bug in Tooltip Preview

= 3.9.4 =
* Feature: Added option to wrap with <span> for ACF Fields
* Feature: Added option to disable tooltips on desktops
* Feature: Added option to show tooltips for related terms
* Feature: Added option to change Search Hint icon
* Feature: Added option to show tooltip close icon only on mobile devices
* Bug: Fixed the bug in "Backup Glossary Terms to File" functionality
* Bug: Fixed the bug in Alternative meanings functionality
* Bug: Fixed the bug in "Word of the day/week terms" widget

= 3.9.3 =
* Bug: Fixed the bug in javascript file

= 3.9.2 =
* Bug: Fixed the bug with the accented letters not being displayed for the base letter in Glossary Index
* Bug: Fixed the bug with multiple AJAX requests
* Bug: Fixed the bug with backlinks appearing more then once
* Bug: Fixed the bug in "Stretch the alphabetical index to 100%" option
* Bug: Fixed the bug in import/export functionality
* Feature: Added option to specify number of custom related articles on Term page
* Feature: Added option to specify number of custom related articles in tooltip
* Feature: Added option to paginate Related Articles on Term page

= 3.9.1 =
* Bug: Fixed the bug with some of the Glossary Index terms being missing
* Feature: Added the option to import/export the single meta values

= 3.9.0 =
* Feature: Added the option to stretch the alphabetical index to 100%
* Feature: Preview tooltip option in the Tooltip Settings area
* Improvement: Custom fields receive their own column upon export
* Feature: Added the option allowing to display only the first X terms beginning with each letter
* Feature: Added the support for Checkbox ACF field
* Bug: Fixed the bug with tooltips not opening on the first click
* Bug: Fixed a rare JS bug
* Bug: Fixed a bug with synonyms/Wikipiedia content appearing incorrectly
* Bug: Fixed a bug with the related terms not using the custom urls
* Bug: Fixed a bug with tooltip positioning

= 3.8.19 =
* Improvement: Improved the server-side pagination
* Feature: Added the option to select the delimeter for the Glossary Term Synonyms/Variations
* Feature: Redirect the single search result on Glossary Index page directly to the term page
* Bug: Fixed the bug in the [glossary_tooltip] shortcode

= 3.8.18 =
* Bug: Fixed the bug with tooltips overlapping the content beneath them even after closing
* Improvement: Improved the support for tooltips in AJAX loaded content
* Feature: Added the support for the "term_id" parameter for the [glossary_tooltip] shortcode
* Feature: Added the option allowing to change the parsing function priority (can solve some conflict with other themes/plugins)
* Feature: Added the option to auto-add parsed pages to the Related Articles Index

= 3.8.17 =
* Feature: Added the support for Woocommerce Attribute labels
* Feature: Only the non-filtered meanings will appear in the tooltips
* Bug: Fixed small bugs

= 3.8.16 =
* Feature: Added the option to exclude HTML tags
* Bug: Fixed bug in the Word of the day/week widget

= 3.8.15 =
* Feature: Added the option to disable the DOM parser
* Bug: Fixed the problem with not showing Categories on Edit glossary item page
* Bug: Fixed the problem with tooltip not closing in some browsers
* Bug: Fixed the Category widget dropdown view

= 3.8.14 =
* Feature: Added the option to the display the tooltips in the WP Image captions
* Feature: Added the option to change tooltip title's font size
* Feature: Added link attribute to the [glossary_tooltip] shortcode
* Feature: Added the option to export and import plugin settings
* Bug/change: Fixed the issue with the Customizer
* Bug: Fixed the typo
* Bug: Fixed PHP notices

= 3.8.13 =
* Feature: Added the option to link the Wikipedia term title to the Wikipedia page itself
* Bug/change: Fixed the CSS for the Modern Table view
* Feature: Added the option to disable the abbreviations in the title
* Feature: Added the option to disable the abbreviations on the Glossary Index page
* Feature: Added the option to overwrite the general setting for opening the glossary links on new tabs
* Updated the Licensing Package

= 3.8.12 =
* Feature: Added the support for Gutenberg for Glossary Term pages
* Feature: Added the way to display the Related Terms/Related Articles with widgets regardless of the meta options

= 3.8.11 =
* Updated the Licensing Package
* Feature: Added the setting allowing to display the excerpt instead of post content on Glossary Index page "Display excerpt instead of description?"
* Change: Redesigned the Glossary Index Settings Page
* Bug: Fixed the issue with the additional tooltip content not being added on Glossary Index page

= 3.8.10 =
* Small performance optimizations
* Added missing label for custom templates

= 3.8.9 =
* Feature: Added the support for tooltips in the WooCommerce Attributes section
* Feature: Fixed support for AntiSpambot
* Bug: Fixed the issue with audio/video players playing after tooltip being closed

= 3.8.8 =
* Bug: Security update

= 3.8.7 =
* Feature: Added the new view: Expand + description (two columns)
* Bug: Fixed the bug with the Google Fonts when default font was selected
* Bug: Fixed small performance related issues
* Bug: Fixed the compatibility issue with jQuery > 3.0.0

= 3.8.6 =
* Feature: Added the option to change the behavior of the "Highlight only the first occurrence" for variants/synonyms
* Feature: Added the class to the category name before the Term Name on the Glossary Index

= 3.8.5 =
* Feature: Added the option to display the category name before the Term Name on the Glossary Index
* Bug: Fixed the issue with the term links sometimes were unclickable because of the tooltip
* Bug: Fixed the display issues with tooltips on mobile devices
* Bug: Fixed the display issues with an image thumbnail on mobile devices

= 3.8.4 =
* Bug: Fixed the issue wit the hashes during AJAX parsing
* Bug: Fixed the Taxonomy Backlink not being displayed on the backend correctly
* Bug: Fixed the 404 errors in the CSS references
* Feature: Added the option to exclude the page/post from the Related Articles (through CM Tooltip Disables metabox)
* Feature/Change: Instead of disabling the related terms on post/page the checkbox now overrides the general settings (so it's easy now to only display them on few selected pages)
* Feature/Change: The option allowing to add "title" attribute link now works also on the Glossary Index page
* Bug: Fixed the bug with the abbreviations/synonyms cleanup

= 3.8.3 =
* Updated the Licensing Package

= 3.8.2 =
* Bug: Fixed the shortcode attached to the parsing button

= 3.8.1 =
* Bug: Fixed the bug in Enfold compatibility
* Feature: Added the new Word of Day Widget

= 3.8.0 =
* Feature: Added an option for tooltip display animation and tooltip hide animation along with animation time in seconds for Glossary Tooltip
* Bug: Fixed the additional linebreaks being added on export
* Update: Updated the Enfold compatibility (requires Enfold 4.4.1+)

= 3.7.10 =
* GDPR: Improved the description of the Tooltip font feature
* Feature: Added new options to set the paddings for the tooltip!

= 3.7.9 =
* Bug: Fixed the bug which caused the tooltips to be stuck open

= 3.7.8 =
* Bug: Fixed the bug which caused the plugin to stop parsing the terms

= 3.7.7 =
* Feature: Added the option to allow sorting of Glossary Index terms by the title
* Feature: Added the minification for the scripts and styles (disabled for Administrators)
* Feature: Added the support for the wpDatatables plugin
* Feature: Added the option to exclude parsing in the HTML elements with given class

= 3.7.6 =
* Bug: Fixed the bug with the missing function
* Feature: Added the option to add custom code before and after the Glossary Term Page content
* Feature: Added the option to add custom code before and after the Glossary Tooltip content

= 3.7.5 =
* Option: Added the option to disable the links in description of the Glossary Index Page
* Option: Added the option to link the Glossary Index Page thumbnails to the original sized images
* Option: Added the option to disable closing the tooltips on mouse moveout
* Bug: Fixed the typo

= 3.7.4 =
* Bug/Feature: Added the missing $additionalClass to links if tooltips are disabled
* Change: Added the support for the Luxembourgish language

= 3.7.3 =
* Feature: Added the option to close the tooltip on touch enabled devices by touching outside of the tooltip
* Bug/Change: Disabled the option "Move tooltip contents to footer?" by default - to stop the rare problems with the tooltips displaying random strings
* Bug: Fixed the bug with the Merriam-Webster duplicate contents in the tooltip

= 3.7.2 =
* Bug: Fixed the small compatibility issue with PHP >7.2
* Bug: Fixed the small compatibility with servers with "set_time_limit" disabled

= 3.7.1 =
* Feature: Added the new param "length=X" to the embed url allowing to limit the length of the description
* Feature: Removed the underline of the dashicons in the [glossary_tooltip] shortcode unless it's forced with the new underline="1" parameter
* Bug: Fixed the small compatibility issue with PHP <5.5
* Bug: Changed the default setting to move to scripts to footer to fix the problems with the tooltips displaying random strings

= 3.7.0 =
* Added the option to disable the comments per glossary page
* Added the option to add multiple terms with the same title (Alternative Meanings)
* Added the option to set the dashicon for each term
* Added the option to embed the tooltips (add in an iframe)
* Added the option to move the tooltip content to the footer (improved the compatibility with the builders)
* Added the option to close the tooltips on mobile only on the close icon click.
* Added the option to close with "ESC" button
* Improved the accessibility of the tooltips (displaying the tooltips on focus)
* Updated the licensing package
* Fixed the bug with the padding of tooltip not being recognized
* Fixed the bug with selecting the tooltip font

= 3.6.1 =
* Added the support for "author_id" attribute for the [glossary] shortcode allowing to display terms from single author
* Added the debug code for Related Articles Indexing
* Fixed the option allowing to disable stem
* Fixed the problem with the linebreaks after list items
* Updated the licensing package

= 3.6.0 =
* Added the support for importing featured images
* Fixed the error reporting during imports
* Fixed the issue with displaying image onclick (added the term link to the bottom)
* Added the live tester for the tooltip definition length
* Added the Related Terms Widget
* Added the Related Articles Widget
* Fixed the problem with the listnav on Glossary Term Pages

= 3.5.14 =
* Added the support for the parsing in the Goodlayers theme builder
* Added the option to disable the "All Categories" selection
* Fix for stem

= 3.5.13 =
* Now showing the term link automatically on the bottom when displaying tooltip on click

= 3.5.12 =
* Added the clickable thumbnail for Images Tiles View
* Fixed the Custom Link in the ReadMore link in the tooltip

= 3.5.11 =
* Fix for the Themify
* Added the limitation for the Related Terms to appear just once for page
* Added the option to change the parentheses around Abbreviations in the Glossary Index Page
* Added the option to change the tooltip to display RTL text
* Added the new Glossary Index View focused on displaying the images - Images Tiles View
* Added the new Glossary Term template allowing to display the featured image in the custom sidebar

= 3.5.10 =
* Further fixes for the Enfold theme
* Added option to set the Index Backling per category
* Added new filters

= 3.5.9 =
* Fixed the bug with the non-linked terms not having the right styles applied
* Fixed the bug with the terms not openin on the new tab properly on the index page
* Fixed the bug with the Featured Image in Pro+
* Added the option to show the term link in tooltip in new tab
* Added the option to disable the related article excerpts
* Fixes for the Enfold theme

= 3.5.8 =
* Added the option to select the Tooltip Close Icon Symbol
* Fixed the problem with the fontsize for the tooltips
* Fixed the disable_listnav attribute for Client Side Pagination
* Added the option to select the stem color
* Added the options to select the featured image size and placement per term

= 3.5.7 =
* Fixed the problem with Categories
* Added the option to search only the exact terms

= 3.5.6 =
* Fixed the problem with the tooltip closing on mobile
* Fixed the XSS vulnerability of the Glossary Index

= 3.5.5 =
* Added the label to "Back to top" in "Expand + Description" Glossary Index type
* Added the option to disable the filtering of the tooltip content completely
* Added the option to display the related articles in new tab
* Added the new shortcode [cm_tooltip_link_to_term] (see the Shortcodes menu item for details)

= 3.5.4 =
* Fixed the bug with the Tooltip title color settings not working

= 3.5.3 =
* Added the translation wrappers to few missing places on the Glossary Term Page

= 3.5.2 =
* Furtner improved the PHP 7.0 compatibility
* Added the option to close the tooltip with the ESC key (if it's clickable)

= 3.5.1 =
* Added the option to show only relevant categories on the Glossary Index
* Added the option to show only relevant tags on the Glossary Index
* Added the option to set the search placeholder
* Fixed the bug with the "disable_listnav" and "show_search" attributes not being preserved on Glossary Index navigation
* Added the HTML classes to the Search label in the Glossary Index Page

= 3.5.0 =
* Added the option to keep the HTML tags in the Glossary Index definitions
* Added the search in the abbreviations
* Added the option to disable the search in abbreviations for widget
* Added the option to disable the search in synonyms for widget
* Improved the Letter Bar on Term Page
* Improved the PHP 7.0 compatibility
* Added the option to overwrite the "Highlight only first term occurance" for post
* Added the option to show a term link after the truncated definition on the Glossary Index Page
* Added the label to the part indicating that the definition was truncated
* Fixed a rare warning about in the wp-includes/cache.php
* Added the option to use the dashicons in the [glossary_tooltip] shortcode
* Fixed the bug in the "Only show items on search?"
* Added option to exclude the parsed ACF fields by ID
* Added the option to disable ACF parsing on page/post
* Added the options to set the size and color of the tooltip close icon

= 3.4.4 =
* Fixed the problem with the capital I in the Glossary Index
* Add background color option to the tooltip title

= 3.4.3 =
* Fixed the bug with the Glossary Index alphabetical index not being hidden with the setting properly
* Fixed the warning about undefined internal content
* Improved accessibility of the Glossary Index letter navigation

= 3.4.2 =
* Add the option to search the index page by title or description or both
* Fixed the visiblity of categories and tags according to settings
* Added the style options for glossary page: Expand + description
* Added the style options for glossary page: Grid + terms
* Added the style options for glossary page: Cube
* Added the option to remove the term higlightining on BuddyPress pages
* Fixed the term highlighting in the image captions of the ACF fields
* Fixed the tooltip explanations to some of the settings

= 3.4.1 =
* Added the option to display the categories on glossary term page
* Added the option to display the tags on glossary term page
* Fixed the Fast-Live-Filter vs Nothing found label
* Added the option to remove the parsing from the excerpts in Performance&Debug section enabled by default
* Added the new Tooltip Categories Widget
* Removed the tooltip reappearing after clicking the close button

= 3.4.0 =
* Added the option to select which types of Advanced Custom Fields should be parsed
* Added the option to select which types of Advanced Custom Fields should have the WP filters removed (wpautop)
* Added the option to disable the Alphabetic Index
* Added the option to enable the Fast-Live-Filter of the Glossary Index
* Added the option to change the width of the tiles
* Added the support for search in Sidebar+term view
* Fixed the bug with the close modal icon not being displayed correctly in Pro+
* Fixed the bug in parsing of the ACF fields
* Fixed the bug with the close icon for tooltip sometimes missing when not logged in

= 3.3.13 =
* Fixed the problem with the align of the letters index in the Glossary Term pages

= 3.3.12 =
* Fixed the error in the Custom Related Article functionality

= 3.3.11 =
* Fixed the problem with the styling of the Index Page
* Fixed the typo in SYNONYMS
* Fixed the problem with the tooltips appearing on Glossary Index Page with server-side pagination
* Fixed the bug with styles missing on the Glossary Term pages when no related terms have been found and scripts placed in footer
* Fixed the bug with "ALL" option not being properly pre-selected in some cases (you can now type "all" or "ALL" in pre selected option to force that)

= 3.3.10 =
* Fixed the notice in related articles
* Changed the styling of the letter count in Glosssary Index Alphabetical list
* Fixed the bug in the Category Whitelisting
* Added the option to Hide the tags on Glossary Index Page
* Fixed the problem with the Google API testing
* Added the option to parse the tooltips in WordPress Text Widget
* Added the option to show only "Draft" elements on Terms List in Admin Dashboard
* Fixed the bug in the WooCommerce product descriptions
* Added translation wrappers for two missing string

= 3.3.9 =
* Added the option to change the ordering of the tags to alphabetical
* Removed the redundant Server Information tab from the Settings
* Moved the shortcodes info to separate tab
* Fixed small bugs

= 3.3.8 =
* Fixed the rare 'randomness' of the random terms widget bug in some installations
* Fixed the bug not allowing to unset all of the post types in the related articles list
* Added the option to show only the custom related articles
* Fixed the bug in the related articles parsing

= 3.3.7 =
* Fixed the rare bug with initial the pagination
* Fixed the rare bug with undefined excludedTagsCount
* Fixed the bug with "related" attribute used together with "glossary_index_style" in [glossary] shortcode
* Added option to add the term page link to the tooltip
* CHANGE: Removed automated adding the term page link when the content is limited - now selecting the "Add term page link to the end of the tooltip content?" option is required
* CHANGE: Disabled the "Enable Caching Mechanisms" by default

= 3.3.6 =
* Added the option to split the Glossary Index letters navigation into multiple lines with | character
* Added the option to disable the element count in Glossary Index letters navigation
* Fixed the Glossary Index letters styling
* Added the option to disable the additional Visual Editor buttons
* Added the option allowing to open the custom related articles in same tab
* Added the new attribute to [glossary] shortcode "glossary_index_style" allowing to override the general setting

= 3.3.5 =
* General bugfixing

= 3.3.4 =
* General bugfixing

= 3.3.3 =
* Fixed the problem with Custom Related Articles
* Fixed conflict in BuddyPress avatar cropping
* Added the option to hide the "empty" letters in the Glossary Index Page
* Big performance optimization (reduced memory amount used, number of quersies, and loading times)

= 3.3.1 =
* Added the Latest Terms widget
* Replaced "data-tooltip" attribute with "data-cmtooltip" to avoid collisions
* Added the new label for the prefix before the "title" attribute

= 3.3.0 =
* Fixed the problem with set_time_limit
* Fixed the gettext calls
* Added the options to set colors for links in the tooltip
* Added the option to display tooltips on click not on hover
* Added the new display mode for Glossary Index: "Sidebar + term page"
* Added the new API for Glosbe Dictionary a free alternative for Merriam-Webster

= 3.2.9 =
* Updated the "ALL" count in Glossary Index Page client side pagination

= 3.2.8 =
* Small fix in the licensing API

= 3.2.7 =
* Added the several new strings to be translatable
* Removed the deprecated options
* Added the option to Get the Suggested Synonyms from the external Service
* Fixed the styling of the tiles
* Added many custom classes to the elements on the Glossary Term page to allow easier customisation

= 3.2.6 =
* Ensured the WordPress 4.4 compatibility
* Small fix to the licensing package

= 3.2.3 =
* Fixed the sorting of numbers when 'intl' library is present from 1,10,11,2 to 1,2,10,11
* Fixed the problem with the role management

= 3.2.2 =
* Fixed the problem with the double alphabetical list if the search was disabled
* Fixed the problem with searching for terms with no synonym
* Fixed the problems with the CM Tooltip - Filter Cats
* Fixed the bug with synonyms not being properly deleted for the deleted terms (messaged as duplicates)
* Removed the parsing of the tooltips in the textareas
* IMPORTANT! Adding/deleting/editing the glossary terms now require the "manage_glossary" capability (added to admin/editor on activation)
* Added the option to control the role(s) which can add/edit/delete glossary terms

= 3.2.1 =
* Raised the priority of the main hooks over to 20000 to avoid conflicts
* Added the functionality which remembers the last selected filters on the Glossary Index Page
* Improved the design of the tooltips
* Added the option to disable the tooltips for the user in the session (new argument "session" to the [glossary-toogle-tooltips] shortcode
* Added the option to set the delay before the tooltip appears and before it hides
* Added the option to show the shadow for the tooltips and set it's color
* Redesigned the synonyms feature to improve the WPML compatibility and fix some bugs
* Fixed the way how the "Disable Tooltips on this page" works
* Fixed the bug with the icons indicating new terms in Glossary Index

= 3.2.0 =
* Fixed the missing arguments to "current_user_can" calls
* Added the autocreation of cache table, when one of the columns is missing
* Fixed the code responsible for creating the cache table
* Moved all of the Labels to separate table
* Added the support for multiple categories in the shortcode
* Added the option to whitelist/blacklist multiple categories on the post/page
* Added the option to add the image on the Glossary Index for "Classic+excerpt" and "Classic+description" views

= 3.1.9 =
* Fixed the bug with terms consisting of just numbers being incorrectly highlighted
* Added the support for highlighting the terms in the Woocommerce short description
* Fixed the support for the hypenated terms and terms containing commas (those need to be enclosed in quotes eg. "some, shiny term" in the Whitelist/Blacklist functionality
* Specified the taxonomy names (added the "Tooltip" prefix) to help users to distinguish the taxonomies

= 3.1.8 =
* Added the hook allowing to change/remove the built-in tooltip styling: add_filter('cmtt_dynamic_css',$content)
* Fixed the bug with the styling of the title in the tooltip
* Fixed the bug with the ACF not working under some circumstances
* Fixed the gtags shortcode attriubute to properly support id/name/slug

= 3.1.7 =
* Removed the <h4> tags used in the plugin to comply with the accessibility standards
* Adedd the support for touch+mouse devices
* Added the options to control the Related Articles Index - now it's done in chunks to solve the performance problems

= 3.1.6 =
* Fixed the bug with the "checked" on the Tooltip - Disables
* Added the option to disable the creation of RSS feeds for the glossary term pages
* Small improvements and bugfixes
* Fixed the rare problem with the shortcode detection

= 3.1.5 =
* Added the option to keep the images in the Tooltip content
* Added some security updates
* Fixed the bug which stopped the terms from being highlighted on glossary pages
* Added the option to pick the pagination position in the Server-side pagination
* Updated the CSS for the category links on the Glossary Index Page

= 3.1.4 =
* Fixed the XSS vulnerability in Wordpress add_query_arg() and remove_query_arg() functions
* Added the option to filter search the abbreviations and synonyms
* Added the option to hide abbreviations, synonyms, terms from Glossary Index with shortcode arguments.
* Added the option to parse the ACF fields using the simple parser (solves some html issues)
* Added the shortcode [glossary-toogle-tooltips] which displays a link allowing to show/hide tooltips on given page
* Added the shortcode [glossary-toggle-theme label="Test theme" class="test"] which displays a link allowing to change the body class.

= 3.1.2 =
* Fixed the support for the apostrophes in the Glossary Terms
* Added the option to disable the functionality to "Hide the terms from the index" which causes issues on some installations
* Added the "Categories" column in the Admin Dashboard
* Added the option to filter the Glossary Terms by "Category" and by "Tags"
* Added the option to highlight terms in bbPress replies
* Added the wp-color-picker to color selections

= 3.1.1 =
* Added the option to pick the template for the Glossary Term from the Settings
* Changed all of the __() and _e() calls to CMTT_Pro::__() and CMTT_Pro::_e() respectively
* Added the options to set the tooltips max-width and min-width
* Added the option to remove the Related Articles for given term
* Organized the General Settings screen in more sections
* Updated the Advanced Custom Fields parsing function

= 3.1.0 =
* Fixed the bug with Abbreviations not being highlighted correctly
* Fixed the problems with blacklist/whitelist
* Fixed the option removing comments from term pages (worked inverse)
* Plugin is now adding the HTML classes for glossary links per each category
* When the category is passed by the shortcode - the category selection is disabled and all search results are limited to the category
* Added the explanation to the Custom Term Link functionality
* Fixed the Custom Term Link functionality on Glossary Index Page
* Added the option to turn ON/OFF the caching mechanisms
* Added the option to choose the Custom Post Types where the Tooltips should be highlighted
* Added the option to mark the terms in the Glossary Index Page as new (with an icon)
* Added the option to highlight the terms in Advanced Custom Fields (ACF)
* Added the option to show an alphabetical list on Glossary Term pages
* Added the option to choose the way categories are displayed
* Added the shortcode [glossary-listnav] displaying the alphabetical list
* Added the shortcode [glossary-term term="term" length="X"] displaying the single glossary term (must be first added to the Index)
* Added the new attribute (related="X") to the [glossary] shortcode allowing to show "up to X" related articles on Glossary Index List
* Added the new attribute (no_desc="0") to the [glossary] shortcode allowing to disable the descriptions on Glossary Index List

= 3.0.6 =
* Fixed the bug with doubled Merriam-Webster content in the tooltip
* Fixed the bug with the "Flush Cache" button not working in some cases
* Fixed the bug with term titles not being properly trimmed on API calls
* Fixed small bug in js on mobile devices, subsequent term clicks close previous tooltips

= 3.0.5 =
* Improved i18n by wrapping some missing texts in __()
* Fixed bug on Test Google API
* Fixed notice when tags are empty

= 3.0.4 =
* Settings stay on the last tab after save
* Fixed the jQuery bug with switching categories when search is disabled
* Fixed the bug with the terms not being highlighted for some users

= 3.0.3 =
* Fixed the bugs in the Categories
* Fixed the bug with [glossary_exclude] shortcode sometimes appearing around the Glossary Index
* Improved the support for mobile devices
* Added the support for passing the attributes to the [glossary] shortcode by $_GET and $_POST
* Removed the unused "title_prefix", "title_show" and "title_category" attributes from the [glossary] shortcode
* Updated the shortcode attributes of the [glossary] shortcode
* Fixed the bug on import when category didn't exist
* Fixed the bug on export
* Fixed the bug with accented characters in Modern Table and Classic Table

= 3.0.2 =
* Added the support for DataTables(https://datatables.net)
* Improved the way tooltip position is calculated - the tooltips should take into account the boundaries of the viewport
* Fixed the Search Widget
* Added the option to choose the locale used for the sorting on Glossary Index page
* Added the option to add the translation to the help item for Search on Glosary Index page

= 3.0.1 =
* Added the support for sorting using "intl" library
* Fixed the typo in "Cleanup Database" button
* Added the autoinstaller for the synonyms, abbreviations and related posts (fixes the problems with failed saving)

= 3.0.0 =
* Improved the performance of the Glossary Index page
* Added the support for [glossary] shortcode
* Added the automatic addition of [glossary] shortcode to the Glossary Index page
* Added over 50 new filters
* Added over 20 new actions
* Fixed many bugs/potential bugs
* Improved i18n by adding some missing labels
* Improved security by fixing potential XSS vulnerabilities
* Fixed the functionality of Custom Replacements tool
* Added option to update the single Custom Replacement
* Improved the server-side pagination letter counts

= 2.8.6 =
* Fixed the numbers of terms overlapping the letters in Glossary Index
* Added the option to manually check for the plugin updates
* Fixed the functions attaching the scripts and styles on the backend
* Fixed the conflict with the Fusion Page Builder
* Added the option to Flush the API cache for single term
* Added the option to Hide the term from Glossary Index Page
* Added the option to test the APIs on the backend
* Added the option to display the "X" to close the tooltip
* Fixed a rare bug with terms not being highlighted only once

= 2.8.5 =
* Fixed the frequency the plugin checks for the update

= 2.8.4 =
* Fixed the bug with the tooltips not being highlighted after changing the page in the Modern Table Glossary Index view
* Fixed the compatibility bug in the abbreviations in WP 4.0+
* Added the option to switch between "Whitelist" and "Blacklist" on the post/page to limit the terms that can be highlighted
* Added the option to turn off the term highlighting on the Glossary Term pages in the Settings

= 2.8.3 =
* Fixed the bug "indexOf not working in InternetExplorer 8"

= 2.8.2 =
* Added the new options for sorting of the Related Articles
* Added the option to add a custom Wikipedia URl for a term
* Fixed some small JS issues

= 2.8.1 =
* Fixed the bug causing the Glossary Index Page to disregard the chosen template
* Fixed the links to the social media libraries
* Added the option to select the size of the Glossary Index letters
* Fixed the bug in [cm_tooltip_parse] shortcode which was adding the <p> tag
* Fixed the option regarding the way non-latin characters behave in the Glossary Index Page for client-side pagination
* Limited the scope of the admin styles to the plugin's Settings only
* Fixed the problems with Posts/Pages with with statuses other than "published" being shown in "Related Posts"
* Added the option to choose the color of the term link in the tooltip

= 2.8.0 =
* Merged the items on the Glossary Index Page regardless of the case
* Improved the support to [glossary_parse] shortcode by adding new qTags and tinyMCE and ckeditor buttons
* Added the option to change the way non-latin characters behave in the Glossary Index Page
* Added the option allowing to display the edit-link inside the Tooltip for logged-in users with "edit_posts" capability
* Added the button on the Settings page allowing to Cleanup the database (erase all the data plugin has saved in the DB)

= 2.7.8 =
* Added the example import file on the Import/Export page
* Fixed the bugs on the Import/Export page

= 2.7.7 =
* Combined the scripts to optimize loading times
* Optimized the loading of external vendor scripts required by ShareThis box
* Added the option to turn off the tooltips on mobile devices
* Fixed the bug with Custom Related Articles
* Fixed the Import/Export format example
* Changed the way how the Google Translate API is called (to curl)
* WARNING! Due to the performance issues on big glossaries, we've decided to disable for the terms to have parent

= 2.7.6 =
* Changed some of the CSS rules to be more strict
* Fixed the problem with some UTF-8 characters with server-side pagination and letter list
* Improved the tooltip display on narrow screens on low resolutions and near the edges

= 2.7.5 =
* Added the support for the CM Tooltip Glossary Remote Import
* Fixed the paths of the scripts on SSL installations
* Added the filter "cmtt_allowed_terms_metabox_posttypes" which allows to display "CM Tooltip - Allowed Terms" metabox on custom post types
* Fixed the TinyMCE editor integration (glossary_exclude button)
* Added the settings allowing to display the metaboxes on all post types
* Added the Call to action box on Settings screen

= 2.7.4 =
* Fixed the deprecated calls in widgets.php
* Fixed the problem with licensing
* Started the WPML compatibility integration
* Added the filter "cmtt_disable_metabox_posttypes" which allows to display disable metabox on custom post types

= 2.7.3 =
* Added the "title_prefix", "title_show" and "title_category" attributes to the [glossary] shortcode
* Fixed the js bug
* Fixed the support for [0-9] option with server-side pagination
* Added the backup support and options

= 2.7.2 =
* Added the options to change the interval and hour when the Related Articles Index is rebuilt
* Added the option to change the label for [ALL] option in alphabetical index of Glossary Index Page
* Removed the meta information caching from the query of the parser (bug with long queries)
* Fixed the bug with "glossary_container" element being indented on AJAX requests
* Improved the tooltips appearance on the mobile devices
* Added the option to Highlight the terms in the comments
* Added the option to turn off the comments on the Glossary Term pages (by turning off the comments support)
* Added the option to create a list of possible terms to be parsed on post/page
* Added the options to better support mobile devices (Tooltip - Mobile Support)

= 2.7.1 =
* Added the option to change the order of the "Related Articles"
* Added the options to only show the MW output when the term's content is empty
* Improved the performance of the abbreviations
* Improved the performance of the synonyms
* Fixed the bug with widgets
* Added the option to disable "Related Terms" on Glossary Term pages
* Implemented the Alphabetical list for Server-side pagination
* Added the single space after prefixes

= 2.7.0 =
* Fixed the count of the alphabetical list on the Classic Table view (it counted the letters)
* Updated the link to the User Guide
* Showing "Related Terms" on the term pages
* Dropped the "Licensed Item Name" from the "Licensing" screen
* Added the explanation to the "Search" on the Glossary Index Page

= 2.6.9 =
* Fixed the "parse_error" bug in PHP versions <5.3
* Added Licensing tab and support
* Fixed notices
* Fixed "Share This" bug with search
* Added the option to show the synonyms in the Glossary Index Page
* Fixed the sorting of the Abbreviations and Synonyms in Glossary Index Page

= 2.6.7 =
* Adedd "header" to the list of protected tags
* Changed "Share This" box structure to table
* Fixed the bug with default "Display style" of Glossary Index Page
* Added setting to disable highlighting only on the "main" WP_Query
* The [cm_tooltip_parse] shortcode now forces the parse regardless of the General settings
* Remove the filter displaying the Glossary Index Page after it's outputted once
* Fixed the CSS/JS enqueue for [glossary cat="cat_name"] shortcode
* Fixed the i18n for [glossary cat="cat_name"] shortcode header
* Fixed bug with synonyms

= 2.6.6 =
* Fixed the bug with Merriam-Webster
* Fixed the top margin of the pagination on the Glossary Index
* Fixed the title of the Glossary Index Page when there's permalink conflict
* Displaying "Glossary Index Page ID" settings in separate line
* Added the option to prefix the Glossary Term title on Glossary Term page
* Fixed a bugs with pagination
* Added the options to display the "Share This" box on the Glossary Index Page and Glossary Term Pages
* Small changes of CSS spacing
* Redesigned the settings regarding the Glossary Index Page display
* Fixed the setting "Avoid parsing protected tags?"

= 2.6.5 =
* Changed the "Glossary Index Page ID" input from textbox to select
* Updated the descriptions of "Glossary Index Page ID" and "Glossary Index Page Permalink"
* Added option to generate the "Glossary Index Page"
* Added new column in "System Information" with information if the setting is OK

= 2.6.4 =
* Moved the "Use custom template" setting to the "Glossary Term" tab
* Fixed the bug with permalink conflict of page and archive
* Fixed the first listnav letter bug when [All] and [0-9] were disabled

= 2.6.3 =
* Fixed a very rare bug with filenames conflict

= 2.6.1 =
* Fixed bugs
* Added warning message about missing "mbstring" library

= 2.6.0 =
* Fixed some problems with saving the settings
* Added the redirect from the terms archive to the glossary index page

= 2.5.2 =
* Added the "mbstring" check to the System Information tab
* Fixed the conflict with "NextGen Gallery"
* Fixed the bug with "&amp;" character in synonyms
* Redesigned the settings for better readability and functionality
* Fixed some notices
* Added option to limit the length of the term description on the glossary index page
* Added option to show the big tiles on the glossary index page
* Added option to choose the preselected letter from the navigation bar

= 2.5.1 =
* Tooltips are now displayed by default
* Added the Term title to the Tooltip content
* Removed the confusing condition for the Term properties metabox
* Added the note about using the Excerpts

= 2.5.0 =
* Fixed the bug with 0-9 color on glossary index letters nav bar
* Fixed the bug with disabling the tooltips in the Glossary Random Terms widget
* Fixed the bug with missing letters nav bar on the glossary index
* Added the localization to the "Clear" link on the glossary index page
* Added the "Server Information" settings tab
* Fixed the conditions in "the_content" filters

= 2.4.10 =
* Separated "Variations" metabox from "Synonyms"
* Fixed wording in the variations/synonyms additional informations
* Fixed a bug in tooltip recognition pattern
* Added the support for BuddyPress (custom type for filter: "bp_blogs_record_comment_post_types")
* Added "Edit Glossary Item" to the admin bar
* Added the support for shortcode [glossarytooltip content="text"]term[/glossarytooltip]
* Added the code changing the default charset for the synonyms/variations table to UTF-8
* Added the link to the "Trash" (for trashed glossary terms)

= 2.4.9 =
* Added the charset (UTF-8) and collate information of synonyms/variations table
* Added support for the shortcodes in the term pages
* Fixed a bug with contentHash
* Added better support for Unicode characters in tooltip recognition pattern (especially for French support)
* Added clarification informations about abbreviations, synonyms and variants (toggle by clicking on "More info")

= 2.4.8 =
* Fixed the rare bug with case-sensitivity
* Added the option allowing to turn off the plugin parsing completely
* Fixed related articles per page setting
* Added the option to add Glossary Items from the admin bar
* Changed the database structure regarding the synonyms
* Added the error message if the same synonym/variation is being used more than once
* Fixed the deprecated "attribute_escape()" calls

= 2.4.7 =
* Added the option to turn off related terms and synonyms per page
* Updated the User Guide link

= 2.4.6 =
* Fixed notifications appearing on some plugin installations
* Fixed the bug with parsing with huge amounts of glossary items ( over 3000)
* Fixed the warnings from abbreviations
* Fixed showing the excerpts on glossary index page
* Added the support for RSS feeds for glossary pages

= 2.4.5 =
* Added the option to set the abbreviation to the glossary terms
* Added the option to disable the glossary term links on given page
* Added the option to disable the tooltips on given page
* Clarified the option which stopped the plugin to parse the given page
* Added the option to strip the shortcodes from glossary page content when displaying it in the tooltip content

= 2.4.4 =
* Fixed listnav js problems
* Added the option to disable the parsing of the glossary pages
* Fixed the conflict with Cminds AdsChanger plugin
* Fixed the rare installation bug which caused main glossary page not being created

= 2.4.3 =
* Fixed a bug which caused the alphabetical list to not appear

= 2.4.2 =
* Added the option to put the tooltip.js in footer
* Switched default tooltip.js loading place to header (due to many themes lacking wp_footer() hook)
* Changed the way how jQueryUI is loaded in admin

= 2.4.1 =
* Fixed the issue with tooltip transparency
* Fixed the issue with tooltip fadeIn
* Added the option to switch the tooltip between clickable and unclickable
* Added the custom filter "cm_tooltip_parse" which can be used to enable tooltip functionality outside "the_content"
* Added the shortcode "[cm_tooltip_parse]text[/cm_tooltip_parse]" which applies the above filter to its content

= 2.4.0 =
* Fixed the bug with tooltip link in glossary index page

= 2.3.11 =
* Fixed the bug with definitions on the glossary list not appearing
* Fixed the bug with wrong tooltip appearing for two terms having the same part
* Fixed the PHP error for widget
* Added the affiliate programme
* Added the "Search Widget"

= 2.3.10 =
* Fixed a couple of PHP warnings

= 2.3.9 =
* Fixed tooltip.js conflict with Modernizr
* Fixed the bug with the synonyms
* Added the option to remove the search button from the main index page
* Fixed the rare conflict that caused the main index page not showing up

= 2.3.8 =
* Fixed the issue where admin tabs weren't working in rare cases

= 2.3.7 =
* Fixed the bug with htmlentities being decoded

= 2.3.6 =
* IMPORTANT! Minimal Wordpress version supported: 3.3
* Fixed the way of loading dynamic CSS
* Related terms now show once for each link
* Added the option to change the "Term details" link
* Removed old files
* Added the explanation for how to remove the tooltips on the main index page

= 2.3.5 =
* Upgraded the main glossary index paging mechanism

= 2.3.4 =
* Fixed the bug which was throwing Warnings if the content html was incorrect
* Fixed the bug with tooltip not appearing for some users
* Added the option to search the terms in non-separated texts (e.g. japanese) (default: OFF)

= 2.3.3 =
* Fixed the bug which was breaking the layout for the pages without content

= 2.3.2 =
* Fixed the bug which appeared when activating plugin with PHP < 5.3.6
* Removed the admin menu link for subscribers
* Fixed "More info" link
* Fixed "remove links" option
* Localized search button label on glossary index page
* Search now searches also in the term content

= 2.3.1 =
* Added the shortcode for glossary dictionary [glossary_dictionary term="term name"]
* Added the shortcode for glossary thesaurus [glossary_thesaurus term="term name"]
* Added the shortcode for glossary translate [glossary_translate term="term to translate" source="english" target="spanish"]
* Fixed the bug with the search not being trimmed
* Added the ability to remove the [All] option from the glossary index page
* Added the ability not to run API calls from the glossary index page
* Added the ability to show excerpt on the glossary index page
* Fixed the bug which broke the layout if the either term or synonym was the same as the HTML tag name
* Optimized the parsing speed
* Fixed bug with additional <body> tags being added
* Fixed bug with title and prefix of related term list

= 2.3 =
* First Release of Pro+ Version
