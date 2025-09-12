<input type="hidden" value="<?php echo network_admin_url( 'admin.php?page=gd-security-headers-tools&gdsih_handler=getback&run=export&_ajax_nonce=' . wp_create_nonce( 'dev4press-plugin-export' ) ); ?>" id="gdsih-export-url"/>

<div class="d4p-group d4p-group-export d4p-group-important">
    <h3><?php esc_html_e( 'Important', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
		<?php esc_html_e( 'With this tool you export all plugin settings into plain text file using JSON format. Do not modify export file, change can make it unusable.', 'gd-security-headers' ); ?>
    </div>
</div>