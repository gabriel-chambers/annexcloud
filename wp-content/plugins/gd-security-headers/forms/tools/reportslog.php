<?php

$list = array(
	'd000' => __( 'Remove all logged data', 'gd-security-headers' ),
	'd001' => __( 'Remove logged data older than 1 day', 'gd-security-headers' ),
	'd003' => __( 'Remove logged data older than 3 days', 'gd-security-headers' ),
	'd007' => __( 'Remove logged data older than 7 days', 'gd-security-headers' ),
	'd014' => __( 'Remove logged data older than 14 days', 'gd-security-headers' ),
	'd030' => __( 'Remove logged data older than 30 days', 'gd-security-headers' ),
	'd060' => __( 'Remove logged data older than 60 days', 'gd-security-headers' ),
	'd090' => __( 'Remove logged data older than 90 days', 'gd-security-headers' ),
	'd180' => __( 'Remove logged data older than 180 days', 'gd-security-headers' ),
	'd365' => __( 'Remove logged data older than 1 year', 'gd-security-headers' ),
);

?>
<div class="d4p-group d4p-group-extra d4p-group-important">
    <h3><?php esc_html_e( 'Important', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
		<?php esc_html_e( 'This will remove old entries from CSP and XXP reports log. This operation is not reversible.', 'gd-security-headers' ); ?>
    </div>
</div>

<div class="d4p-group">
    <h3><?php esc_html_e( 'Reports fo Cleanup', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Select Tables', 'gd-security-headers' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" class="widefat" name="gdsihtools[reportslog][csp]" value="on"/> <?php esc_html_e( 'CSP Reports', 'gd-security-headers' ); ?>
                    </label><br/>
                    <label>
                        <input type="checkbox" class="widefat" name="gdsihtools[reportslog][xxp]" value="on"/> <?php esc_html_e( 'XXP Reports', 'gd-security-headers' ); ?>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="d4p-group">
    <h3><?php esc_html_e( 'Report Cleanup Period', 'gd-security-headers' ); ?></h3>
    <div class="d4p-group-inner">
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Select Period', 'gd-security-headers' ); ?></th>
                <td>
                    <div class="d4p-setting-select">
						<?php d4p_render_select( $list, array( 'selected' => 'd365', 'name' => 'gdsihtools[reportslog][period]', 'class' => 'widefat' ) ); ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
