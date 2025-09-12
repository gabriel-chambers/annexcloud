=== GD Security Headers ===
Contributors: GDragoN
Donate link: https://plugins.dev4press.com/gd-security-headers/
Version: 1.8
Tags: dev4press, security, csp, content security policy, permission policy, feature policy, referrer policy, xss, security headers
Requires at least: 5.5
Requires PHP: 7.4
Tested up to: 6.6
Stable tag: trunk
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Configure various security-related HTTP headers, including CSP, XSS, Referrer Policy and more.

== Description ==
Configure various security-related HTTP headers, including Content Security Policy, Feature Policy, Referrer Policy and more. For CSP and XSS plugin supports report logging with 2 additional database tables to store reports from browsers.

= Supported security headers =
The plugin has support for the following HTTP headers:

* Content Security Policy (CSP) - with reporting
* XSS Protection (XXP) - with reporting
* Feature Policy (Permissions Policy)
* Content Type - No Sniff Policy
* Strict Transport Security
* Referrer Policy
* Frame Options

For CSP, the plugin allows you to set rules for all currently supported directives, additional settings including setting the policy in Report or Live mode. The plugin also includes special extensions that can automatically fill CSP rules for popular Google services you might be using on your website (Fonts, Maps, Adsense, Analytics, TagManager and more) and other popular services (Gravatar, Instagram, PayPal Vimeo and more).

And, for Feature Policy (or Permissions Policy), the plugin allows you to set rules for all currently supported rules (over 25 rules, supported by different browsers).

= FLoC / Browsing Topics =
Permissions Policy rules list includes 'browsing-topics' rule that can be used to disable Google's new tracking method 'Browsing Topics API' (which replaced 'Federated Learning of Cohorts' or 'FLoC').

= Methods for adding headers =
The plugin can add all the generated headers into HTACCESS file (for Apache web servers), and they will be applied to all files, not just WordPress generated content. If your website is not using Apache (or .HTACCESS), all rules are generated with each page request and will work with any server type.

And, if you don't use Apache web server, the plugin has a panel where it displays generated headers for most popular servers: Apache, Nginx and IIS, and you can copy generated headers to add to server configuration files.

= About the plugin =
* More information about [GD Security Headers](https://plugins.dev4press.com/gd-security-headers/)
* Support and Knowledge Base for [GD Security Headers](https://support.dev4press.com/kb/product/gd-security-headers/)

== Installation ==
= General Requirements =
* PHP: 7.4 or newer

= PHP Notice =
* Plugin doesn't work with PHP 7.3 or older versions.

= WordPress Requirements =
* WordPress: 5.5 or newer

= WordPress Notice =
* Plugin doesn't work with WordPress 5.4 or older versions.

= Basic Installation =
* Plugin folder in the WordPress plugins folder must be `gd-security-headers`.
* Upload `gd-security-headers` folder to the `/wp-content/plugins/` directory.
* Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==
= Does plugin work with WordPress MultiSite installations? =
Yes. In Multisite installation, the plugin is available for configuration on the Network level, and headers are configured for all sites in the network at once.

= Where can I configure the plugin? =
The plugin has its own top-level item in the WordPress admin side menu: GD Security Headers. This will open a panel with global plugin settings. In Multisite installation, a plugin panel is in the Network administration.

= Can I translate the plugin to my language? =
Yes. The POT file is provided as a base for translation. Translation files should go into Languages directory.

== Changelog ==
= 1.8 (2024.06.07) =
* New: system requirements: PHP 7.4 or newer
* New: tested with WordPress 6.4 to 6.6
* New: strict transport security: extra value for 'preload' flag
* Edit: updated list of permissions policy elements
* Edit: updated permissions policy, Browsing Topics replacing FLoC
* Edit: added more information for some settings
* Edit: changes to default values for some settings
* Edit: d4pLib 2.8.17

= 1.7.1 (2023.10.29) =
* Edit: improvements to the CSP logs panel input processing
* Edit: improvements to the CSP logs panel arguments sanitization
* Edit: improvements to the log classes PHP code
* Fix: union based SQL injection vulnerability with the CSP logs panel

= 1.7 (2023.08.24) =
* New: system requirements: PHP 7.3 or newer
* New: tested with WordPress 6.0 to 6.3
* Edit: various improvements to display escaping and sanitation
* Edit: various improvements to the core code
* Edit: d4pLib 2.8.15
* Fix: reflected XSS vulnerability with error message handling

= 1.6.1 (2022.05.16) =
* New: tested with WordPress 6.0

= 1.6 (2022.02.04) =
* New: system requirements: PHP 7.2 or newer
* New: system requirements: WordPress 5.3 or newer
* New: tested with WordPress 5.9
* New: csp addon: send reports to custom log URL
* New: csp addon: support for 'base-uri' directive
* New: csp addon: predefined rules list for Instagram
* Edit: csp addon: updated various predefined rules lists
* Edit: csp addon: updated settings information about some rules
* Edit: d4pLib 2.8.14
* Fix: csp addon: few typos in the rules names
* Fix: csp addon: minor issues with saving settings

= 1.5 (2021.04.20) =
* New: feature/permissions policy addon: support for 'interest-cohort'
* New: feature/permissions policy addon: dashboard information widget
* Edit: feature/permissions policy addon: expanded information in the settings panel
* Edit: feature/permissions policy addon: improved values explanations
* Fix: feature/permissions policy addon: few typos in the rules names

= 1.4 (2020.10.05) =
* New: csp addon: generate predefined rules for one or more CDN's
* New: csp addon: predefined rules list for WordPress.org
* New: csp addon: support for 'prefetch-src' directive
* New: feature policy addon: support for updated 'permission-policy' version
* New: feature policy addon: expanded list of policies that can be included
* Edit: csp addon: improved settings organization showing CSP rule levels
* Edit: feature policy addon: included support information for some policies
* Edit: d4pLib 2.8.13
* Fix: csp addon: problem with generating the rules with 'all' basic value
* Fix: feature policy addon: few minor issues with building rules

= 1.3 (2020.05.08) =
* Edit: csp addon: expanded some of the google based preset rules
* Edit: d4pLib 2.8.8
* Fix: x-frame policy: invalid headers generated when not using .htaccess
* Fix: strict-transport-security policy: invalid headers generated when not using .htaccess
* Fix: referer policy: invalid headers generated when not using .htaccess
* Fix: feature policy: problem printing empty policy header

= 1.2 (2019.12.05) =
* New: support for feature policy header
* New: csp addon: predefined rules list for Google YouTube
* New: csp addon: predefined rules list for Google Tag Manager
* New: csp addon: predefined rules list for Gravatar
* New: csp addon: predefined rules list for Gleam
* New: csp addon: predefined rules list for Vimeo
* New: csp addon: auto generated rules for some special data sources
* Edit: csp addon: expanded some Google based preset rules
* Edit: csp addon: various improvements in the generator
* Edit: d4pLib 2.8.2

= 1.1.1 (2019.08.15) =
* Edit: d4pLib 2.7.6
* Fix: problem with saving the plugin settings in some cases

= 1.1 (2019.05.11) =
* New: panel with generated headers for various servers
* New: headers panel: for apache servers
* New: headers panel: for nginx servers
* New: headers panel: for iis servers
* New: new method for building the HTACCESS headers
* Edit: improved additional headers object
* Edit: updated rules for Google Analytics
* Edit: do not run when WordPress runs CRON
* Edit: removed some unused code and strings

= 1.0 (2019.03.21) =
* First plugin version

== Upgrade Notice ==
= 1.8 =
Various improvements.

= 1.7 =
Various updated and fixes.

== Screenshots ==
1. Plugin Dashboard
2. CSP Reports
3. Various Headers settings
4. XSS Protection settings
5. Content Security Policy settings
6. Global settings
7. Generated security headers
8. Tools
9. HTACCESS with security headers
