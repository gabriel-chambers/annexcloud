<?php
$cminds_plugin_config = array(
    'plugin-is-pro'             => TRUE,
    'plugin-has-addons'         => TRUE,
    'plugin-show-shortcodes'    => TRUE,
    'plugin-shortcodes'         => '<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary]</h4>
			<span>Show Glossary Index</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>cat</strong> - The comma separated list of categories.</li>
				<li><strong>gtags</strong> - The comma separated list of tags</li>
				<li><strong>search_term</strong> - The preselected search term.</li>
				<li><strong>itemspage</strong> - The number of items on the page. This attribute is for Server-side pagination only.</li>
				<li><strong>letter</strong> - The preselected letter on the alphabetical index.</li>
				<li><strong>related</strong> - Whether the related terms should be displayed.</li>
				<li><strong>no_desc</strong> - Whether the descriptions of terms should be hidden.</li>
				<li><strong>hide_terms</strong> - Allows to remove the regular terms from the Glossary Index.</li>
				<li><strong>hide_abbrevs</strong> - Allows to remove the abbreviations from the Glossary Index.</li>
				<li><strong>hide_synonyms</strong> - Allows to remove the synonyms from the Glossary Index.</li>
				<li><strong>glossary_index_style</strong> - The style of the Glossary Index.
					Possible values are (use the value in quotes): Classic "classic", Classic + definition "classic-definition",
					 Classic + excerpt "classic-excerpt", Small Tiles "small-tiles", Big Tiles "big-tiles", Classic table "classic-table",
					 Modern table "modern-table", Sidebar + term page "sidebar-termpage", Expand style "expand-style", Grid "grid", Cube "cube",
					 Term + definition "term-definition", Image + term + definition "img-term-definition", Term Carousel "term-carousel",
					 Term tiles with definition "tiles-with-definition"</li>
				<li><strong>disable_listnav</strong> - Allows to disable the alphabetical navigation bar.</li>
			</ul>
			<h5>Example</h5>
			<p><kbd>[glossary cat="cat1,cat2" gtags="tag1,tag2" search_term="term" itemspage="1" letter="all" related="0" no_desc="0" hide_terms="0" hide_abbrevs="0" hide_synonyms="0" glossary_index_style="tiles" disable_listnav="1" ]</kbd></p>
			<p>The shows a custom glossary tooltip.</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary_tooltip]</h4>
			<span>Custom glossary tooltip</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>content</strong> - The content of the tooltip.</li>
				<li><strong>dashicon</strong> - Dashicon to show the tooltip instead of the term/phrase.<a href="https://developer.wordpress.org/resource/dashicons/#chart-bar">List of dashicons</a></li>
				<li><strong>color</strong> - The color of the dashicon.</li>
				<li><strong>size</strong> - The size of the dashicon.</li>
				<li><strong>underline</strong> - The dashicon tooltips will not be underlined by default. If you want to force underline you need to set this parameter to 1.</li>
			</ul>
			<h5>Shortcode content:</h5>
			<p>The term/phrase which should display the custom tooltip.</p>
			<h5>Example</h5>
			<p><kbd>[glossary_tooltip content="text" dashicon="dashicon="dashicons-editor-help" color="#c0c0c0" size="16px" underline="0"] term [/glossary_tooltip]</kbd></p>
			<p>The shows a custom glossary tooltip.</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary_exclude]</h4>
			<span>Exclude from parsing</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Example</h5>
			<p><kbd>[glossary_exclude] text [/glossary_exclude]</kbd></p>
			<p>The content inside the shortcode will be excluded from the parsing.</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[cm_tooltip_parse]</h4>
			<span>Apply tooltip</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Example</h5>
			<p><kbd>[cm_tooltip_parse] text [/cm_tooltip_parse]</kbd></p>
			<p>
				Apply the tooltip to the text inside. Useful to force parsing in places where "the_content" filter is not being used.
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[cm_tooltip_link_to_term]</h4>
			<span>Link word/phrase to the term</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>term</strong> - The title of the term which should be linked.</li>
			</ul>
			<h5>Example</h5>
			<p><kbd>[cm_tooltip_link_to_term term="Term Title"]Linked Word[/cm_tooltip_link_to_term]</kbd></p>
			<p>
				Display the tooltip for a word/phrase as if it was a terms own synonym/variation.
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary_search]</h4>
			<span>Show Glossary Search Form</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Example</h5>
			<p><kbd>[glossary_search]</kbd></p>
			<p>
				Display the form which allows to search for the glossary terms.
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary_dictionary]</h4>
			<span>Show Merriam-Webster Dictionary</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>term</strong> - The term for which the Dictionary definition should be displayed.</li>
			</ul>
			<h5>Example</h5>
			<p><kbd>[glossary_dictionary term="term name"]</kbd></p>
			<p>
				Display the Show Merriam-Webster Dictionary definition. [Ecommerce only]
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary_thesaurus]</h4>
			<span>Show Merriam-Webster Thesaurus</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>term</strong> - The term for which the Dictionary definition should be displayed.</li>
			</ul>
			<h5>Example</h5>
			<p><kbd>[glossary_thesaurus term="term name"]</kbd></p>
			<p>
				Display the Show Merriam-Webster Thesaurus definition. [Ecommerce only]
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary_translate]</h4>
			<span>Translate</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>term</strong> - The term for which the Dictionary definition should be displayed.</li>
				<li><strong>source</strong> - The term for which the Dictionary definition should be displayed.</li>
				<li><strong>target</strong> - The term for which the Dictionary definition should be displayed.</li>
			</ul>
			<h5>Example</h5>
			<p><kbd>[glossary_translate term="text-to-translate" source="english" target="spanish"]</kbd></p>
			<p>
				Display the Google Translated definition of the term. [Ecommerce only]
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary-toogle-tooltips]</h4>
			<span>Toggle Tooltips</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>session</strong> - Whether the result of the shortcode should be persisted in the session. Defaults to 0 (not persisted).</li>
			</ul>
			<h5>Example</h5>
			<p><kbd>[glossary-toogle-tooltips session="0"]</kbd></p>
			<p>
				Display the button allowing to temporarily disable the tooltips on given page.
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary-toggle-theme]</h4>
			<span>Toggle Theme</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>label</strong> - Choose the label for the theme.</li>
				<li><strong>class</strong> - Choose the class for the theme.</li>
			</ul>
			<h5>Example</h5>
			<p><kbd>[glossary-toggle-theme label="Test theme" class="test"]</kbd></p>
			<p>
				Displays a selection allowing to change the class of the tooltips on given page.
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[cmtgend]</h4>
			<span>Select tooltip content</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Example</h5>
			<p><kbd>[cmtgend] text [/cmtgend]</kbd></p>
			<p>The content inside the shortcode will be presented in the tooltip instead of the whole description.</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary_wikipedia]</h4>
			<span>Wikipedia</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Parameters:</h5>
			<ul>
				<li><strong>term</strong> - The term for which the Wikipedia term should be displayed.</li>
			</ul>
			<h5>Example</h5>
			<p><kbd>[glossary_wikipedia term="term name"]</kbd></p>
			<p>
				Displays the Wikipedia definition of the term. [Ecommerce only]
			</p>
		</div>
	</article>
	<article class="cm-shortcode-desc">
		<header>
			<h4>[glossary_terms_amount]</h4>
			<span>Total amount of glossary terms</span>
		</header>
		<div class="cm-shortcode-desc-inner">
			<h5>Example</h5>
			<p><kbd>[glossary_terms_amount]</kbd></p>
			<p>
				Displays the total amount of published glossary terms.
			</p>
		</div>
	</article>',
    'plugin-shortcodes-action'  => 'cmtt_glossary_supported_shortcodes',
    'plugin-version'            => CMTT_VERSION,
    'plugin-abbrev'             => 'cmtt',
    'plugin-short-slug'         => 'cmtooltip',
    'plugin-parent-short-slug'  => '',
    'plugin-settings-url'       => admin_url( 'admin.php?page=cmtt_settings' ),
    'plugin-show-guide'         => FALSE,
    'plugin-guide-text'         => '<p>
										The description of the plugin goes here
									</p>',
    'plugin-guide-video-height' => 180,
    'plugin-guide-videos'       => array(
        array( 'title' => 'Free Version Installation Tutorial', 'video_id' => '157868636' ),
    ),
    'plugin-file'               => CMTT_PLUGIN_FILE,
    'plugin-dir-path'           => plugin_dir_path( CMTT_PLUGIN_FILE ),
    'plugin-dir-url'            => plugin_dir_url( CMTT_PLUGIN_FILE ),
    'plugin-basename'           => plugin_basename( CMTT_PLUGIN_FILE ),
    'plugin-icon'               => '',
    'plugin-name'               => CMTT_NAME,
    'plugin-license-name'       => CMTT_CANONICAL_NAME,
    'plugin-slug'               => '',
    'plugin-menu-item'          => CMTT_MENU_OPTION,
    'plugin-textdomain'         => CMTT_SLUG_NAME,
    'plugin-userguide-key'      => '6-cm-tooltip',
    'plugin-store-url'          => 'https://www.cminds.com/store/tooltipglossary/',
    'plugin-review-url'         => 'https://wordpress.org/support/view/plugin-reviews/enhanced-tooltipglossary',
    'plugin-changelog-url'      => CMTT_RELEASE_NOTES,
    'plugin-licensing-aliases'  => array( CMTT_LICENSE_NAME ),
	'plugin-addons'        => array(
		array(
			'title' => 'Ultimate Tooltip Glossary',
			'description' => 'The Tooltip Glossary Ultimate Plan includes the base plugin and 8 add-ons',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/tooltipglossary/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/tooltip.png',
		),
		array(
			'title' => 'Glossary Community Terms Add-on',
			'description' => 'Allow your site visitors to suggest terms for your Glossary. Each new term can be moderated and categorized accordingly.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/cm-tooltip-glossary-community-terms-cm-plugins-store/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPGlossaryTootlipGlossaryCommunityTermsS.png',
		),
		array(
			'title' => 'Glossary Log & Statistics Add-on',
			'description' => 'Tracks and reports tooltip usage statistics and improve your glossary performance.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/cm-tooltip-glossary-log-cm-plugins-store/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPGlossaryTootlipGlossaryLogStatisticsS.png',
		),
		array(
			'title' => 'Glossary Remote Import Add-on',
			'description' => 'Provides an easy way to import, replicate and create an up-to-date copy of your CM Glossary across several WordPress sites.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/cm-tooltip-glossary-remote-import-cm-plugins-store/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPGlossaryTootlipGlossaryRemoteImportS.png',
		),
		array(
			'title' => 'Glossary Custom Taxonomies Add-on',
			'description' => 'Add support for multiple taxonomies and filtering to your Tooltip Glossary terms.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/tooltip-glossary-custom-taxonomies-add-on-for-wordpress-by-creativeminds/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPGlossaryTootlipGlossaryCustomTaxonomiesS.png',
		),
		array(
			'title' => 'Glossary Search Console Add-on',
			'description' => 'Make your glossary more accessible by adding a search widget on the bottom of your website.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/tooltip-glossary-search-console-widget-add-on-for-wordpress-by-creativeminds/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPGlossaryTootlipGlossarySearchConsoleS.png',
		),
		array(
			'title' => 'Glossary Editor Tooltip Add-on',
			'description' => 'Enables the display of informative tooltips while editing pages or posts in the back-end, supporting both visual and textual editors.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/tooltip-glossary-editor-tooltip-addon-wordpress-creativeminds/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPGlossaryTootlipGlossaryEditorTooltipS.png',
		),
		array(
			'title' => 'Glossary Visual Widgets Add-on',
			'description' => 'Add-on for the Tooltip Glossary plugin that lets you add seven visually engaging and fun widgets to your glossary.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-tooltip-glossary-widgets-add-wordpress/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPGlossaryTootlipGlossaryVisualWidgetsS.png',
		),
		array(
			'title' => 'Glossary PeepSo Integration Add-on',
			'description' => 'Integrate the Glossary Tooltip plugin seamlessly with the PeepSo social network. Parse PeepSo content to display tooltips for glossary terms.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/glossary-peepso-integration-add-wordpress-creativeminds/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPGlossaryTootlipGlossaryPeepSoiIntegrationS.png',
		),
	),
	'plugin-specials'        => array(
		array(
			'title' => 'Reviews and Rating Plugin',
			'description' => 'Allow visitors and users to submit reviews and ratings, and display them on any product, posts, or pages.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/customer-reviews-plugin-wordpress/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPCustomerReviewsS.png',
		),
		array(
			'title' => 'RSS Post Importer Plugin',
			'description' => 'Support importing and displaying external posts using RSS, Atom feeds and scraping tool to your WordPress site.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/rss-post-importer-plugin-wordpress-creativeminds/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPRSSPostImporterS.png',
		),
		array(
			'title' => 'Registration and Invitation Codes',
			'description' => 'Adds a registration and login popup to your WP site. Supports invitation codes, email verification and assign user roles.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/registration-and-invitation-codes-plugin-for-wordpress/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPUserRegistrationAndInvitationCodesS.png',
		),
		array(
			'title' => 'Booking Calendar',
			'description' => 'Enable customers to effortlessly schedule appointments and make payments directly through your website.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/schedule-appointments-manage-bookings-plugin-wordpress/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBookingCalendarS.png',
		),
		array(
			'title' => 'Questions and Answers Plugin',
			'description' => 'Experience a mobile-responsive discussion forum where members can post questions, answers, and comments, with integrated payment support.',
			'link' => 'https://www.cminds.com/cm-answer-store-page-content/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPQuestionsAndAnswersS.png',
		),
		array(
			'title' => 'Map Locations Manager',
			'description' => 'Efficiently manage map locations and enable location finding using Google Maps. Includes support for detailed location descriptions, images, and videos.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/multiple-locations-google-maps/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPMapLocationsandStoreLocaterS.png',
		),
	),
	'plugin-bundles'        => array(
		array(
			'title' => '99+ Free Pass Plugins Suite',
			'description' => 'Get all CM 99+ WordPress plugins and addons. Includes unlimited updates and one year of priority support.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBundleWPSuiteS.png',
		),
		array(
			'title' => 'Essential Publishing Plugin Package',
			'description' => 'Enhance your WordPress publishing with a bundle of seven plugins that elevate content generation, presentation, and user engagement on your site.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/essential-wordpress-publishing-tools-bundle/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBundlePublishingS.png',
		),
		array(
			'title' => 'Essential Content Marketing Tools',
			'description' => 'Enhance your WordPress content marketing with seven plugins for improved content generation, presentation, and user engagement.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/essential-wordpress-content-marketing-tools-bundle/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBundleContentS.png',
		),
		array(
			'title' => 'Essential Security Plugins',
			'description' => 'Enhance your WordPress security with a bundle of five plugins that provide additional ways to protect your content and site from spammers, hackers, and exploiters.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/essential-wordpress-security-tools-plugin-bundle/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBundleSecurityS.png',
		),
	),
	'plugin-services'        => array(
		array(
			'title' => 'WordPress Custom Hourly Support',
			'description' => 'Hire our expert WordPress developers on an hourly basis, offering a-la-carte service to craft your custom WordPress solution.',
			'link' => 'https://www.cminds.com/wordpress-services/wordpress-custom-hourly-support-package/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPServicesHourlySupportS.png',
		),
		array(
			'title' => 'Performance and Optimization Analysis',
			'description' => 'Receive a comprehensive review of your WordPress website with optimization suggestions to enhance its speed and performance.',
			'link' => 'https://www.cminds.com/wordpress-services/wordpress-performance-and-speed-optimization-analysis-service/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPServicesPerformanceS.png',
		),
		array(
			'title' => 'WordPress Plugin Installation',
			'description' => 'We offer professional installation and configuration of plugins or add-ons on your site, tailored to your specified requirements.',
			'link' => 'https://www.cminds.com/wordpress-services/plugin-installation-service-for-wordpress-by-creativeminds/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPServicesExtensionInstallationS.png',
		),
		array(
			'title' => 'WordPress Consulting',
			'description' => 'Purchase consulting hours to receive assistance in designing or planning your WordPress solution. Our expert consultants are here to help bring your vision to life.',
			'link' => 'https://www.cminds.com/wordpress-services/consulting-planning-hourly-support-service-wordpress-creativeminds/#description',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPServicesConsultingS.png',
		),
	),
);
?>