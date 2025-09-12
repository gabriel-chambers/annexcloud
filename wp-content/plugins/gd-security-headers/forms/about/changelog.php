<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="d4p-group d4p-group-changelog">
    <h3><?php esc_html_e( 'Version', 'gd-security-headers' ); ?> 1</h3>
    <div class="d4p-group-inner">
        <h4>Version: 1.8 / june 7 2024</h4>
        <ul>
            <li><strong>new</strong> system requirements: PHP 7.4 or newer</li>
            <li><strong>new</strong> tested with WordPress 6.4 to 6.6</li>
            <li><strong>new</strong> strict transport security: extra value for 'preload' flag</li>
            <li><strong>edit</strong> updated list of permissions policy elements</li>
            <li><strong>edit</strong> updated permissions policy, Browsing Topics replacing FLoC</li>
            <li><strong>edit</strong> added more information for some settings</li>
            <li><strong>edit</strong> changes to default values for some settings</li>
            <li><strong>edit</strong> d4pLib 2.8.17</li>
        </ul>

        <h4>Version: 1.7.1 / october 29 2023</h4>
        <ul>
            <li><strong>edit</strong> improvements to the CSP logs panel input processing</li>
            <li><strong>edit</strong> improvements to the CSP logs panel arguments sanitization</li>
            <li><strong>edit</strong> improvements to the log classes PHP code</li>
            <li><strong>fix</strong> union based SQL injection vulnerability with the CSP logs panel</li>
        </ul>

        <h4>Version: 1.7 / august 24 2023</h4>
        <ul>
            <li><strong>new</strong> system requirements: PHP 7.3 or newer</li>
            <li><strong>new</strong> tested with WordPress 6.0 to 6.3</li>
            <li><strong>edit</strong> various improvements to display escaping and sanitation</li>
            <li><strong>edit</strong> various improvements to the core code</li>
            <li><strong>edit</strong> d4pLib 2.8.15</li>
            <li><strong>fix</strong> reflected XSS vulnerability with error message handling</li>
        </ul>

        <h4>Version: 1.6.1 / may 16 2022</h4>
        <ul>
            <li><strong>new</strong> tested with WordPress 6.0</li>
        </ul>

        <h4>Version: 1.6 / february 4 2022</h4>
        <ul>
            <li><strong>new</strong> system requirements: PHP 7.2 or newer</li>
            <li><strong>new</strong> system requirements: WordPress 5.3 or newer</li>
            <li><strong>new</strong> tested with WordPress 5.9</li>
            <li><strong>new</strong> csp addon: send reports to custom log URL</li>
            <li><strong>new</strong> csp addon: support for 'base-uri' directive</li>
            <li><strong>new</strong> csp addon: predefined rules list for Instagram</li>
            <li><strong>edit</strong> csp addon: updated various predefined rules lists</li>
            <li><strong>edit</strong> csp addon: updated settings information about some rules</li>
            <li><strong>edit</strong> d4pLib 2.8.14</li>
            <li><strong>fix</strong> csp addon: few typos in the rules names</li>
            <li><strong>fix</strong> csp addon: minor issues with saving settings</li>
        </ul>

        <h4>Version: 1.5 / april 20 2021</h4>
        <ul>
            <li><strong>new</strong> feature/permissions policy addon: support for 'interest-cohort'</li>
            <li><strong>new</strong> feature/permissions policy addon: dashboard information widget</li>
            <li><strong>edit</strong> feature/permissions policy addon: expanded information in the settings panel</li>
            <li><strong>edit</strong> feature/permissions policy addon: improved values explanations</li>
            <li><strong>fix</strong> feature/permissions policy addon: few typos in the rules names</li>
        </ul>

        <h4>Version: 1.4 / october 5 2020</h4>
        <ul>
            <li><strong>new</strong> csp addon: generate predefined rules for one or more CDN's</li>
            <li><strong>new</strong> csp addon: predefined rules list for WordPress.org</li>
            <li><strong>new</strong> csp addon: support for 'prefetch-src' directive</li>
            <li><strong>new</strong> feature policy addon: support for updated 'permissions-policy' version</li>
            <li><strong>new</strong> feature policy addon: expanded list of policies that can be included</li>
            <li><strong>edit</strong> csp addon: improved settings organization showing CSP rule levels</li>
            <li><strong>edit</strong> feature policy addon: included support information for some policies</li>
            <li><strong>edit</strong> d4pLib 2.8.13</li>
            <li><strong>fix</strong> csp addon: problem with generating the rules with 'all' basic value</li>
            <li><strong>fix</strong> feature policy addon: few minor issues with building rules</li>
        </ul>

        <h4>Version: 1.3 / may 8 2020</h4>
        <ul>
            <li><strong>edit</strong> csp addon: expanded some of the google based preset rules</li>
            <li><strong>edit</strong> d4pLib 2.8.8</li>
            <li><strong>fix</strong> x-frame policy: invalid headers generated when not using .htaccess</li>
            <li><strong>fix</strong> strict-transport-security policy: invalid headers generated when not using .htaccess</li>
            <li><strong>fix</strong> referer policy: invalid headers generated when not using .htaccess</li>
            <li><strong>fix</strong> feature policy: problem printing empty policy header</li>
        </ul>

        <h4>Version: 1.2 / december 5 2019</h4>
        <ul>
            <li><strong>new</strong> support for feature policy header</li>
            <li><strong>new</strong> csp addon: predefined rules list for Google YouTube</li>
            <li><strong>new</strong> csp addon: predefined rules list for Google Tag Manager</li>
            <li><strong>new</strong> csp addon: predefined rules list for Gravatar</li>
            <li><strong>new</strong> csp addon: predefined rules list for Gleam</li>
            <li><strong>new</strong> csp addon: predefined rules list for Vimeo</li>
            <li><strong>new</strong> csp addon: auto generated rules for some special data sources</li>
            <li><strong>edit</strong> csp addon: expanded some of the google based preset rules</li>
            <li><strong>edit</strong> csp addon: various improvements in the generator</li>
            <li><strong>edit</strong> d4pLib 2.8.2</li>
        </ul>

        <h4>Version: 1.1.1 / august 15 2019</h4>
        <ul>
            <li><strong>edit</strong> d4pLib 2.7.6</li>
            <li><strong>fix</strong> problem with saving the plugin settings in some cases</li>
        </ul>

        <h4>Version: 1.1 / may 11 2019</h4>
        <ul>
            <li><strong>new</strong> panel with generated headers for various servers</li>
            <li><strong>new</strong> headers panel: for apache servers</li>
            <li><strong>new</strong> headers panel: for nginx servers</li>
            <li><strong>new</strong> headers panel: for iis servers</li>
            <li><strong>new</strong> method for building the HTACCESS headers</li>
            <li><strong>edit</strong> improved additional headers object</li>
            <li><strong>edit</strong> updated rules for google analytics</li>
            <li><strong>edit</strong> do not run when WordPress runs CRON</li>
            <li><strong>edit</strong> removed some unused code and strings</li>
        </ul>

        <h4>Version: 1.0 / march 21 2019</h4>
        <ul>
            <li><strong>new</strong> first official version</li>
        </ul>
    </div>
</div>
