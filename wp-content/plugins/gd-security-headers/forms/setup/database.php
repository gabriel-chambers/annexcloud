<h3 style="margin-top: 0;"><?php esc_html_e( 'Database Tables', 'gd-security-headers' ); ?></h3>
<?php

require_once( GDSIH_PATH . 'core/admin/install.php' );

$list_db = gdsih_install_database();

if ( ! empty( $list_db ) ) {
	echo '<h4>' . __( 'Upgrade Notices', 'gd-security-headers' ) . '</h4>';
	echo join( '<br/>', $list_db );
}

echo '<h4>' . __( 'Tables Check', 'gd-security-headers' ) . '</h4>';
$check = gdsih_check_database();

$msg = array();
foreach ( $check as $table => $data ) {
	if ( $data['status'] == 'error' ) {
		$_proceed  = false;
		$_error_db = true;

		$msg[] = '<span class="gdpc-error">[' . __( 'ERROR', 'gd-security-headers' ) . '] - <strong>' . $table . '</strong>: ' . $data['msg'] . '</span>';
	} else {
		$msg[] = '<span class="gdpc-ok">[' . __( 'OK', 'gd-security-headers' ) . '] - <strong>' . $table . '</strong></span>';
	}
}

echo join( '<br/>', $msg );
