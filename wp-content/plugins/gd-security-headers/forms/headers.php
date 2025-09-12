<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include( GDSIH_PATH . 'forms/shared/top.php' );

$list = gdsih()->build_headers_to_array();

?>

    <div class="d4p-content-left">
        <div class="d4p-panel-scroller d4p-scroll-active">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-code"></i>
                <h3><?php esc_html_e( 'Headers', 'gd-security-headers' ); ?></h3>
            </div>
            <div class="d4p-panel-info">
				<?php esc_html_e( 'All HTTP headers generated so they can be copied into server side config. Headers are generated based on the plugin settings.', 'gd-security-headers' ) ?>
                <br/><br/><strong><?php esc_html_e( 'If you are not sure how to do that, please consult with your website hosting company support.', 'gd-security-headers' ) ?></strong>
            </div>
            <div class="d4p-return-to-top">
                <a href="#wpwrap"><?php esc_html_e( 'Return to top', 'gd-security-headers' ); ?></a>
            </div>
        </div>
    </div>
    <div class="d4p-content-right">
        <div class="d4p-group d4p-group-extra d4p-group-important">
            <h3><?php esc_html_e( 'Apache Server', 'gd-security-headers' ); ?></h3>
            <div class="d4p-group-inner">
                <p><?php esc_html_e( 'If you use Apache server, this plugin can add headers automatically into HTACCESS file. But, if you have disabled HTACCESS or you prefer using global server config, you can copy rules from here.', 'gd-security-headers' ); ?></p>
                <div class="gdsih-code-block">
                <pre>&lt;IfModule mod_headers.c><br/><?php

	                $first = true;
	                foreach ( $list as $key => $value ) {
		                if ( ! $first ) {
			                echo '<br/>';
		                }

		                echo '  # ' . $key . '<br/>';
		                echo '  Header set ' . $value . '<br/>';

		                $first = false;
	                }

	                ?>&lt;/IfModule></pre>
                </div>
            </div>
        </div>

        <div class="d4p-group d4p-group-extra d4p-group-important">
            <h3><?php esc_html_e( 'Nginx Server', 'gd-security-headers' ); ?></h3>
            <div class="d4p-group-inner">
                <p><?php esc_html_e( 'If you use NGINX server, you can copy rules from here to add to the server \'conf\' file.', 'gd-security-headers' ); ?></p>
                <div class="gdsih-code-block">
                <pre><?php

	                $first = true;
	                foreach ( $list as $key => $value ) {
		                if ( ! $first ) {
			                echo '<br/>';
		                }

		                echo '# ' . $key . '<br/>';
		                echo 'add_header ' . $value . ';<br/>';

		                $first = false;
	                }

	                ?></pre>
                </div>
            </div>
        </div>

        <div class="d4p-group d4p-group-extra d4p-group-important">
            <h3><?php esc_html_e( 'IIS Server', 'gd-security-headers' ); ?></h3>
            <div class="d4p-group-inner">
                <p><?php esc_html_e( 'If you use IIS server, you can copy rules from here to add to the server \'Web.config\' file.', 'gd-security-headers' ); ?></p>
                <div class="gdsih-code-block">
                <pre>&lt;system.webServer>
  &lt;httpProtocol>
    &lt;customHeaders><br/><?php

	                foreach ( $list as $key => $value ) {
		                $parts = explode( ' ', $value, 2 );

		                echo '      &lt;add name="' . $parts[0] . '" value=' . $parts[1] . ' /><br/>';
	                }

	                ?>    &lt;/customHeaders>
  &lt;httpProtocol>
&lt;system.webServer></pre>
                </div>
            </div>
        </div>
    </div>

<?php

include( GDSIH_PATH . 'forms/shared/bottom.php' );
