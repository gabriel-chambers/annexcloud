<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$panels = array(
	'index' => array(
		'title' => __( 'Tools Index', 'gd-security-headers' ),
		'icon'  => 'wrench',
		'info'  => __( 'All plugin tools are split into several panels, and you can access each starting from the right.', 'gd-security-headers' ),
	),
);

if ( gdsih_scope()->is_master_network_admin() ) {
	$panels['reportslog'] = array(
		'title'       => __( 'Reports Logs Cleanup', 'gd-security-headers' ),
		'icon'        => 'trash-o',
		'break'       => __( 'Cleanup', 'gd-security-headers' ),
		'button'      => 'submit',
		'button_text' => 'Clean Log',
		'info'        => __( 'Remove old reports from CSP and XXP reports logs.', 'gd-security-headers' ),
	);
	$panels['recheck']    = array(
		'title'       => __( 'Recheck and Update', 'gd-security-headers' ),
		'icon'        => 'refresh',
		'break'       => __( 'Maintenance', 'gd-security-headers' ),
		'button'      => 'none',
		'button_text' => '',
		'info'        => __( 'Recheck plugin database tables, check for new templates and clean cache.', 'gd-security-headers' ),
	);
	$panels['export']     = array(
		'title'       => __( 'Export Settings', 'gd-security-headers' ),
		'icon'        => 'download',
		'button'      => 'button',
		'button_text' => __( 'Export', 'gd-security-headers' ),
		'info'        => __( 'Export all plugin settings into file.', 'gd-security-headers' ),
	);
	$panels['import']     = array(
		'title'       => __( 'Import Settings', 'gd-security-headers' ),
		'icon'        => 'upload',
		'button'      => 'submit',
		'button_text' => __( 'Import', 'gd-security-headers' ),
		'info'        => __( 'Import all plugin settings from export file.', 'gd-security-headers' ),
	);
	$panels['remove']     = array(
		'title'       => __( 'Reset / Remove', 'gd-security-headers' ),
		'icon'        => 'remove',
		'button'      => 'submit',
		'button_text' => __( 'Remove', 'gd-security-headers' ),
		'info'        => __( 'Remove selected plugin settings, database tables and optionally disable plugin.', 'gd-security-headers' ),
	);
}

include( GDSIH_PATH . 'forms/shared/top.php' );

?>

    <form method="post" action="" enctype="multipart/form-data" id="gdsih-tools-form">
		<?php settings_fields( 'gd-security-headers-tools' ); ?>
        <input type="hidden" value="<?php echo $_panel; ?>" name="gdsihtools[panel]"/>
        <input type="hidden" value="postback" name="gdsih_handler"/>

        <div class="d4p-content-left">
            <div class="d4p-panel-scroller d4p-scroll-active">
                <div class="d4p-panel-title">
                    <i aria-hidden="true" class="fa fa-wrench"></i>
                    <h3><?php esc_html_e( 'Tools', 'gd-security-headers' ); ?></h3>
					<?php if ( $_panel != 'index' ) { ?>
                        <h4><i aria-hidden="true" class="fa fa-<?php echo $panels[ $_panel ]['icon']; ?>"></i> <?php echo $panels[ $_panel ]['title']; ?></h4>
					<?php } ?>
                </div>
                <div class="d4p-panel-info">
					<?php echo $panels[ $_panel ]['info']; ?>
                </div>
				<?php if ( $_panel != 'index' && $panels[ $_panel ]['button'] != 'none' ) { ?>
                    <div class="d4p-panel-buttons">
                        <input id="gdsih-tool-<?php echo $_panel; ?>" class="button-primary" type="<?php echo $panels[ $_panel ]['button']; ?>" value="<?php echo $panels[ $_panel ]['button_text']; ?>"/>
                    </div>
				<?php } ?>
                <div class="d4p-return-to-top">
                    <a href="#wpwrap"><?php esc_html_e( 'Return to top', 'gd-security-headers' ); ?></a>
                </div>
            </div>
        </div>
        <div class="d4p-content-right">
			<?php

			if ( $_panel == 'index' ) {
				foreach ( $panels as $panel => $obj ) {
					if ( $panel == 'index' ) {
						continue;
					}

					$url = 'admin.php?page=gd-security-headers-' . $_page . '&panel=' . $panel;

					if ( isset( $obj['break'] ) ) { ?>

                        <div style="clear: both"></div>
                        <div class="d4p-panel-break d4p-clearfix">
                            <h4><?php echo $obj['break']; ?></h4>
                        </div>
                        <div style="clear: both"></div>

					<?php } ?>

                    <div class="d4p-options-panel">
                        <i aria-hidden="true" class="fa fa-<?php echo $obj['icon']; ?>"></i>
                        <h5><?php echo $obj['title']; ?></h5>
                        <div>
                            <a class="button-primary" href="<?php echo $url; ?>"><?php esc_html_e( 'Tools Panel', 'gd-security-headers' ); ?></a>
                        </div>
                    </div>

					<?php
				}
			} else {
				include( GDSIH_PATH . 'forms/tools/' . $_panel . '.php' );
			}

			?>
        </div>
    </form>

<?php

include( GDSIH_PATH . 'forms/shared/bottom.php' );
