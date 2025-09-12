<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$panels = array(
	'index' => array(
		'title' => __( 'Settings Index', 'gd-security-headers' ),
		'icon'  => 'cogs',
		'scope' => 'network',
		'info'  => __( 'All plugin settings are split into panels, and you access each starting from the right.', 'gd-security-headers' ),
	),
);

if ( gdsih_scope()->is_master_network_admin() ) {
	$panels['global']  = array(
		'title' => __( 'Global', 'gd-security-headers' ),
		'icon'  => 'cog',
		'scope' => 'network',
		'break' => __( 'Basic', 'gd-security-headers' ),
		'info'  => __( 'From this panel you control global, common plugin settings.', 'gd-security-headers' ),
	);
	$panels['csp']     = array(
		'title' => __( 'Content Security Policy', 'gd-security-headers' ),
		'icon'  => 'flag',
		'scope' => 'network',
		'break' => __( 'Headers', 'gd-security-headers' ),
		'info'  => __( 'From this panel you control CSP settings.', 'gd-security-headers' ),
	);
	$panels['xxp']     = array(
		'title' => __( 'X XSS Protection', 'gd-security-headers' ),
		'icon'  => 'window-close',
		'scope' => 'network',
		'info'  => __( 'From this panel you control XXP settings.', 'gd-security-headers' ),
	);
	$panels['feature'] = array(
		'title' => __( 'Feature/Permissions Policy', 'gd-security-headers' ),
		'icon'  => 'cubes',
		'scope' => 'network',
		'info'  => __( 'From this panel you control Feature Policy header settings.', 'gd-security-headers' ),
	);
	$panels['msh']     = array(
		'title' => __( 'More Headers', 'gd-security-headers' ),
		'icon'  => 'tags',
		'scope' => 'network',
		'info'  => __( 'From this panel you control other security headers settings.', 'gd-security-headers' ),
	);
}

include( GDSIH_PATH . 'forms/shared/top.php' );

$scope = is_multisite() ? gdsih_scope()->get_scope() : $panels[ $_panel ]['scope'];

?>

    <form method="post" action="" autocomplete="off">
		<?php settings_fields( 'gd-security-headers-settings' ); ?>
        <input type="hidden" value="postback" name="gdsih_handler"/>
        <input type="hidden" value="<?php echo $scope; ?>" name="gdsih_scope"/>

        <div class="d4p-content-left">
            <div class="d4p-panel-scroller d4p-scroll-active">
                <div class="d4p-panel-title">
                    <i aria-hidden="true" class="fa fa-cogs"></i>
                    <h3><?php esc_html_e( 'Settings', 'gd-security-headers' ); ?></h3>
					<?php if ( $_panel != 'index' ) { ?>
                        <h4><i aria-hidden="true" class="fa fa-<?php echo $panels[ $_panel ]['icon']; ?>"></i> <?php echo $panels[ $_panel ]['title']; ?></h4>
					<?php } ?>
                </div>
                <div class="d4p-panel-info">
					<?php echo $panels[ $_panel ]['info']; ?>
                </div>
				<?php if ( $_panel != 'index' ) { ?>
                    <div class="d4p-panel-buttons">
                        <input type="submit" value="<?php esc_html_e( 'Save Settings', 'gd-security-headers' ); ?>" class="button-primary">
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
							<?php if ( isset( $obj['type'] ) ) { ?>
                                <span><?php echo $obj['type']; ?></span>
							<?php } ?>
                            <a class="button-primary" href="<?php echo $url; ?>"><?php esc_html_e( 'Settings Panel', 'gd-security-headers' ); ?></a>
                        </div>
                    </div>

					<?php if ( isset( $obj['break_next'] ) ) { ?>

                        <div style="clear: both"></div>
                        <div class="d4p-panel-break d4p-clearfix">
                            <h4><?php echo $obj['break_next']; ?></h4>
                        </div>
                        <div style="clear: both"></div>

					<?php }
				}
			} else {
				d4p_includes( array(
					array( 'name' => 'settings', 'directory' => 'admin' ),
					array( 'name' => 'walkers', 'directory' => 'admin' ),
					array( 'name' => 'functions', 'directory' => 'admin' ),
				), GDSIH_D4PLIB );

				include( GDSIH_PATH . 'core/admin/options.php' );

				$options = new gdsih_admin_settings();

				$panel  = gdsih_admin()->panel;
				$groups = $options->get( $panel );

				$render       = new d4pSettingsRender( $panel, $groups );
				$render->base = 'gdsihvalue';
				$render->render();
			}

			?>
        </div>
    </form>

<?php

include( GDSIH_PATH . 'forms/shared/bottom.php' );
