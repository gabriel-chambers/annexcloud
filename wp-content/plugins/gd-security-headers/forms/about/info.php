<div class="d4p-group d4p-group-import d4p-group-about">
    <h3>GD Security Headers</h3>
    <div class="d4p-group-inner">
        <ul>
            <li><?php esc_html_e( 'Version', 'gd-security-headers' ); ?>: <span><?php echo gdsih_settings()->info_version; ?></span></li>
            <li><?php esc_html_e( 'Status', 'gd-security-headers' ); ?>: <span><?php echo ucfirst( gdsih_settings()->info_status ); ?></span></li>
            <li><?php esc_html_e( 'Edition', 'gd-security-headers' ); ?>: <span><?php echo ucfirst( gdsih_settings()->info_edition ); ?></span></li>
            <li><?php esc_html_e( 'Build', 'gd-security-headers' ); ?>: <span><?php echo gdsih_settings()->info_build; ?></span></li>
            <li><?php esc_html_e( 'Date', 'gd-security-headers' ); ?>: <span><?php echo gdsih_settings()->info_updated; ?></span></li>
        </ul>
        <hr style="margin: 1em 0 .7em; border-top: 1px solid #eee"/>
        <ul>
            <li><?php esc_html_e( 'First released', 'gd-security-headers' ); ?>: <span><?php echo gdsih_settings()->info_released; ?></span></li>
        </ul>
    </div>
</div>

<div class="d4p-group d4p-group-import d4p-group-about">
    <h3><?php esc_html_e( 'System Requirements', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <ul>
            <li><?php esc_html_e( 'WordPress', 'gd-security-headers' ); ?>: <span><?php echo gdsih_settings()->info_wordpress; ?></span></li>
            <li><?php esc_html_e( 'PHP', 'gd-security-headers' ); ?>: <span><?php echo gdsih_settings()->info_php; ?></span></li>
            <li><?php esc_html_e( 'MySQL', 'gd-security-headers' ); ?>: <span><?php echo gdsih_settings()->info_mysql; ?></span></li>
        </ul>
    </div>
</div>

<div class="d4p-group d4p-group-import d4p-group-about">
    <h3><?php esc_html_e( 'Knowledge Base and Support Forums', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <ul>
            <li><?php echo sprintf( __( 'To learn more about the plugin, check out plugin %s articles and FAQ. To get additional help, you can use %s.', 'gd-security-headers' ),
					'<a target="_blank" href="https://support.dev4press.com/kb/product/gd-security-headers/">' . __( 'knowledge base', 'gd-security-headers' ) . '</a>',
					'<a target="_blank" href="https://support.dev4press.com/forums/forum/plugins/gd-security-headers/">' . __( 'support forum', 'gd-security-headers' ) . '</a>' ); ?></li>
        </ul>
    </div>
</div>

<div class="d4p-group d4p-group-import d4p-group-about">
    <h3><?php esc_html_e( 'Important Links', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <ul>
            <li><?php esc_html_e( 'On WordPress.org', 'gd-security-headers' ); ?>:
                <span><a href="https://wordpress.org/plugins/gd-security-headers/" target="_blank">wordpress.org/plugins/gd-security-headers</a></span></li>
            <li><?php esc_html_e( 'On Dev4Press', 'gd-security-headers' ); ?>:
                <span><a href="https://plugins.dev4press.com/gd-security-headers/" target="_blank">plugins.dev4press.com/gd-security-headers</a></span></li>
        </ul>
    </div>
</div>
