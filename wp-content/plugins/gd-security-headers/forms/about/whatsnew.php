<?php include( GDSIH_PATH . 'forms/about/minor.php' ); ?>

<div class="d4p-about-whatsnew">
    <div class="d4p-whatsnew-section d4p-whatsnew-heading">
        <div class="d4p-layout-grid">
            <div class="d4p-layout-unit whole align-center">
                <h2>HTTP Security Headers</h2>
                <p class="lead-description">
                    Add them to WordPress website, the easy way
                </p>
                <p>
                    A new plugin that can add various HTTP Security Headers to WordPress powered website, with reports logging capabilities (for CSP and XXP) and support for .htaccess
                </p>
            </div>
        </div>
    </div>

    <div class="d4p-whatsnew-section">
        <div class="d4p-layout-grid">
            <div class="d4p-layout-unit half align-left">
                <h3>Content Security Policy</h3>
                <p>
                    The most important HTTP header that can limit what content browser will load and display, preventing various types of attacks like cross-site scripting and data injection. It also includes reporting feature allowing browsers to report back which URL and CSP rule was violating the content policy.
                </p>
            </div>
            <div class="d4p-layout-unit half align-left">
                <h3>X-XSS Protection</h3>
                <p>
                    Very simple HTTP header for protecting against a wide range of cross-site scripting attacks configured to block all attempts. It also has the reporting feature for browsers to report which URL and what request body was used to attempt the XSS attack (only some browsers can report on this for now).
                </p>
            </div>
        </div>
    </div>

    <div class="d4p-whatsnew-section">
        <div class="d4p-layout-grid">
            <div class="d4p-layout-unit half align-left">
                <h3>More Security Headers</h3>
                <p>
                    And, there are more HTTP security headers plugin can apply, including Content-Type Options, Strict Transport Security, Frame Option (if you use CSP, you don't need this one) and Referrer Policy.
                </p>
            </div>
            <div class="d4p-layout-unit half align-left">
                <h3>Support for .HTACCESS</h3>
                <p>
                    If you use Apache server, the plugin can build and add all the HTTP headers into .HTACCESS file. This way, headers will be applied to all files coming from your website, not just WordPress generated pages.
                </p>
            </div>
        </div>
    </div>
</div>
