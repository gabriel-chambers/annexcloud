<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$_panel = gdsih_admin()->panel === false ? 'whatsnew' : gdsih_admin()->panel;

if ( ! in_array( $_panel, array( 'changelog', 'whatsnew', 'info', 'dev4press' ) ) ) {
	$_panel = 'whatsnew';
}

include( GDSIH_PATH . 'forms/about/header.php' );

include( GDSIH_PATH . 'forms/about/' . $_panel . '.php' );

include( GDSIH_PATH . 'forms/about/footer.php' );
