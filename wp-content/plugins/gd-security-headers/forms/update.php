<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$_classes = array( 'd4p-wrap', 'wpv-' . GDSIH_WPV, 'd4p-page-update' );

?>
<div class="<?php echo join( ' ', $_classes ); ?>">
    <div class="d4p-header">
        <div class="d4p-plugin">
            GD Security Headers
        </div>
    </div>
    <div class="d4p-content">
        <div class="d4p-content-left">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-magic"></i>
                <h3><?php esc_html_e( 'Update', 'gd-security-headers' ); ?></h3>
            </div>
            <div class="d4p-panel-info">
				<?php esc_html_e( 'Before you continue, make sure plugin was successfully updated.', 'gd-security-headers' ); ?>
            </div>
        </div>
        <div class="d4p-content-right">
            <div class="d4p-update-info">
				<?php

				include( GDSIH_PATH . 'forms/setup/database.php' );

				gdsih_settings()->set( 'install', false, 'info' );
				gdsih_settings()->set( 'update', false, 'info', true );

				?>

                <h3><?php esc_html_e( 'All Done', 'gd-security-headers' ); ?></h3>
				<?php esc_html_e( 'Update completed.', 'gd-security-headers' ); ?>

                <br/><br/><a class="button-primary" href="<?php echo network_admin_url( 'admin.php?page=gd-security-headers-about' ); ?>"><?php esc_html_e( 'Click here to continue.', 'gd-security-headers' ); ?></a>
            </div>
        </div>
    </div>
</div>