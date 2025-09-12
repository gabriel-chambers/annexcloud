<div class="d4p-group d4p-group-extra d4p-group-important">
    <h3><?php esc_html_e( 'Important', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
		<?php esc_html_e( 'This tool can remove plugin settings saved in the WordPress options table and all database tables added by the plugin.', 'gd-security-headers' ); ?><br/><br/>
		<?php esc_html_e( 'Deletion operations are not reversible, and it is highly recommended to create database backup before proceeding with this tool.', 'gd-security-headers' ); ?>
		<?php esc_html_e( 'If you choose to remove plugin settings, once that is done, all settings will be reinitialized to default values if you choose to leave plugin active.', 'gd-security-headers' ); ?>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php esc_html_e( 'Remove plugin settings', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdsihtools[remove][settings]" value="on"/> <?php esc_html_e( 'All Plugin Settings', 'gd-security-headers' ); ?>
        </label>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php esc_html_e( 'Remove .HTACCESS rules', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdsihtools[remove][htaccess]" value="on"/> <?php esc_html_e( 'All rules added to .HTACCESS', 'gd-security-headers' ); ?>
        </label>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php esc_html_e( 'Remove database data and tables', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdsihtools[remove][drop]" value="on"/> <?php esc_html_e( 'Remove plugins database tables and all data in them', 'gd-security-headers' ); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdsihtools[remove][truncate]" value="on"/> <?php esc_html_e( 'Remove all data from database tables', 'gd-security-headers' ); ?>
        </label><br/>
        <hr/>
        <p><?php esc_html_e( 'Database tables that will be affected', 'gd-security-headers' ); ?>:</p>
        <ul style="list-style: inside disc;">
            <li><?php echo gdsih_db()->csp_reports; ?></li>
            <li><?php echo gdsih_db()->xxp_reports; ?></li>
        </ul>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php esc_html_e( 'Disable Plugin', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdsihtools[remove][disable]" value="on"/> <?php esc_html_e( 'Disable plugin', 'gd-security-headers' ); ?>
        </label>
    </div>
</div>
