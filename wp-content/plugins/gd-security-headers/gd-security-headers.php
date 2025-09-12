<?php
/**
 * Plugin Name:       GD Security Headers
 * Plugin URI:        https://plugins.dev4press.com/gd-security-headers/
 * Description:       Configure various security related HTTP headers, including Content Security Policy, Referrer Policy and more. All headers can be added to .HTACCESS file.
 * Author:            Milan Petrovic
 * Author URI:        https://www.dev4press.com/
 * Text Domain:       gd-security-headers
 * Version:           1.8
 * Requires at least: 5.5
 * Tested up to:      6.6
 * Requires PHP:      7.4
 * Network:           true
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * == Copyright ==
 * Copyright 2008 - 2024 Milan Petrovic (email: support@dev4press.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */

$gdsih_dirname_basic = dirname( __FILE__ ) . '/';
$gdsih_urlname_basic = plugins_url( '/gd-security-headers/' );

define( 'GDSIH_PATH', $gdsih_dirname_basic );
define( 'GDSIH_URL', $gdsih_urlname_basic );
define( 'GDSIH_D4PLIB', $gdsih_dirname_basic . 'd4plib/' );

/* D4PLIB */
if ( ! defined( 'D4PLIB_PATH' ) ) {
	define( 'D4PLIB_PATH', GDSIH_PATH . 'd4plib/' );
}

if ( ! defined( 'D4PLIB_URL' ) ) {
	define( 'D4PLIB_URL', GDSIH_URL . 'd4plib/' );
}

require_once( GDSIH_D4PLIB . 'd4p.core.php' );
/* D4PLIB */

d4p_includes( array(
	array( 'name' => 'datetime', 'directory' => 'core' ),
	array( 'name' => 'scope', 'directory' => 'core' ),
	array( 'name' => 'wpdb', 'directory' => 'core' ),
	array( 'name' => 'plugin', 'directory' => 'plugin' ),
	array( 'name' => 'errors', 'directory' => 'plugin' ),
	array( 'name' => 'settings', 'directory' => 'plugin' ),
	array( 'name' => 'ip', 'directory' => 'classes' ),
	'functions',
	'sanitize',
	'access',
	'wp',
), GDSIH_D4PLIB );

require_once( GDSIH_PATH . 'core/version.php' );
require_once( GDSIH_PATH . 'core/settings.php' );
require_once( GDSIH_PATH . 'core/functions.php' );
require_once( GDSIH_PATH . 'core/plugin.php' );

require_once( GDSIH_PATH . 'core/objects/core.db.php' );

global $_gdsih_core, $_gdsih_settings, $_gdsih_db;

$_gdsih_settings = new gdsih_core_settings();
$_gdsih_core     = new gdsih_core_plugin();
$_gdsih_db       = new gdsih_core_db();

function gdsih() : gdsih_core_plugin {
	global $_gdsih_core;

	return $_gdsih_core;
}

function gdsih_settings() : gdsih_core_settings {
	global $_gdsih_settings;

	return $_gdsih_settings;
}

function gdsih_db() : gdsih_core_db {
	global $_gdsih_db;

	return $_gdsih_db;
}

if ( D4P_ADMIN ) {
	d4p_includes( array(
		array( 'name' => 'admin', 'directory' => 'plugin' ),
		array( 'name' => 'functions', 'directory' => 'admin' ),
	), GDSIH_D4PLIB );

	require_once( GDSIH_PATH . 'core/admin/plugin.php' );
}
