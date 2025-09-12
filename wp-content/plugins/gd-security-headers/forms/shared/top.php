<?php

do_action( 'gdsih_admin_panel_top' );

$pages  = gdsih_admin()->menu_items;
$_page  = gdsih_admin()->page;
$_panel = gdsih_admin()->panel;

$_real_page = $_page;

if ( ! empty( $panels ) ) {
	if ( empty( $_panel ) ) {
		$_panel = 'index';
	}

	$_available = array_keys( $panels );

	if ( ! in_array( $_panel, $_available ) ) {
		$_panel              = 'index';
		gdsih_admin()->panel = false;
	}
}

$_classes = array( 'd4p-wrap', 'wpv-' . GDSIH_WPV, 'd4p-page-' . $_real_page );

if ( $_panel !== false ) {
	$_classes[] = 'd4p-panel';
	$_classes[] = 'd4p-panel-' . $_panel;
}

$_message = '';
$_color   = '';

if ( isset( $_GET['message'] ) && $_GET['message'] != '' ) {
	$msg = d4p_sanitize_slug( $_GET['message'] );

	switch ( $msg ) {
		case 'saved':
			$_message = __( 'Settings are saved.', 'gd-security-headers' );
			break;
		case 'imported':
			$_message = __( 'Import operation completed.', 'gd-security-headers' );
			break;
		case 'nothing':
			$_message = __( 'Nothing done.', 'gd-security-headers' );
			break;
		default:
			$_message = apply_filters( 'gdsih_admin_message_text', '', $msg );
			break;
	}
}

?>
<div class="<?php echo join( ' ', $_classes ); ?>">
    <div class="d4p-header">
        <div class="d4p-navigator">
            <ul>
                <li class="d4p-nav-button">
                    <a href="#"><i aria-hidden="true" class="<?php echo d4p_get_icon_class( $pages[ $_page ]['icon'] ); ?>"></i> <?php echo $pages[ $_page ]['title']; ?></a>
                    <ul>
						<?php

						foreach ( $pages as $page => $obj ) {
							if ( $page != $_page ) {
								echo '<li><a href="admin.php?page=gd-security-headers-' . $page . '"><i aria-hidden="true" class="' . ( d4p_get_icon_class( $obj['icon'], 'fw' ) ) . '"></i> ' . $obj['title'] . '</a></li>';
							} else {
								echo '<li class="d4p-nav-current"><i aria-hidden="true" class="' . ( d4p_get_icon_class( $obj['icon'], 'fw' ) ) . '"></i> ' . $obj['title'] . '</li>';
							}
						}

						?>
                    </ul>
                </li>
				<?php if ( ! empty( $panels ) ) { ?>
                    <li class="d4p-nav-button">
                        <a href="#"><i aria-hidden="true" class="<?php echo d4p_get_icon_class( $panels[ $_panel ]['icon'] ); ?>"></i> <?php echo $panels[ $_panel ]['title']; ?>
                        </a>
                        <ul>
							<?php

							foreach ( $panels as $panel => $obj ) {
								if ( $panel != $_panel ) {
									$extra = $panel != 'index' ? '&panel=' . $panel : '';

									echo '<li><a href="admin.php?page=gd-security-headers-' . $_real_page . $extra . '"><i aria-hidden="true" class="' . ( d4p_get_icon_class( $obj['icon'], 'fw' ) ) . '"></i> ' . $obj['title'] . '</a></li>';
								} else {
									echo '<li class="d4p-nav-current"><i aria-hidden="true" class="' . ( d4p_get_icon_class( $obj['icon'], 'fw' ) ) . '"></i> ' . $obj['title'] . '</li>';
								}
							}

							?>
                        </ul>
                    </li>
				<?php } ?>
            </ul>
        </div>
        <div class="d4p-plugin">
            GD Security Headers
        </div>
    </div>
	<?php

	if ( $_message != '' ) {
		echo '<div class="updated">' . esc_html( $_message ) . '</div>';
	}

	?>
    <div class="d4p-content">
