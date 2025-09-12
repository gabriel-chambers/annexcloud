<?php
// prevent direct access to thi file:
if (defined('WP_UNINSTALL_PLUGIN')) {

	require( plugin_dir_path( __FILE__ ).'plugin.php' );
    delete_option(OPINIONSTAGE_OPTIONS_KEY);
}