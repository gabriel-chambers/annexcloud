<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$_classes = array(
	'd4p-wrap',
	'wpv-' . GDSIH_WPV,
	'd4p-page-' . gdsih_admin()->page,
	'd4p-panel',
	'd4p-panel-' . $_panel,
);

$_tabs = array(
	'whatsnew'  => __( 'What&#8217;s New', 'gd-security-headers' ),
	'info'      => __( 'Info', 'gd-security-headers' ),
	'changelog' => __( 'Changelog', 'gd-security-headers' ),
	'dev4press' => __( 'Dev4Press', 'gd-security-headers' ),
);

?>

<div class="<?php echo join( ' ', $_classes ); ?>">
    <h1><?php printf( __( 'Welcome to GD Security Headers&nbsp;%s', 'gd-security-headers' ), gdsih_settings()->info_version ); ?></h1>
    <p class="d4p-about-text">
		<?php esc_html_e( 'Configure various security related HTTP headers, including Content Security Policy, Referrer Policy and more. All headers can be added to .HTACCESS file.', 'gd-security-headers' ); ?>
    </p>
    <div class="d4p-about-badge" style="background-color: #69426A;">
        <img height="92" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDMyIDMyIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zOnNlcmlmPSJodHRwOi8vd3d3LnNlcmlmLmNvbS8iIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtzdHJva2UtbGluZWpvaW46cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6MjsiPgogICAgPHBhdGggZD0iTTE2LDBDMTQuMTAxLDAuNzMgMTEuOTI5LDEuMjkzIDkuNTk1LDEuNjE3QzcuMTk5LDEuOTUgNC44OTksMS45OTQgMi44MzMsMS43OTFDMi41OTMsMi44IDIuNDY2LDMuODUyIDIuNDY2LDQuOTMyTDIuNDY2LDE4LjQ2NkMyLjQ2NiwyNS45MzYgMTYsMzIgMTYsMzJDMTYsMzIgMjkuNTM0LDI1LjkzNiAyOS41MzQsMTguNDY2TDI5LjUzNCw0LjkzMkMyOS41MzQsMy44NTIgMjkuNDA3LDIuOCAyOS4xNjcsMS43OTFDMjcuMSwxLjk5NCAyNC44MDEsMS45NSAyMi40MDUsMS42MTdDMjAuMDcxLDEuMjkzIDE3Ljg5OSwwLjczIDE2LDBaTTE2LDIuNDY2QzE3LjYwNiwzLjA4MyAxOS40NDQsMy41NiAyMS40MTgsMy44MzRDMjMuNDQ0LDQuMTE1IDI1LjM4OSw0LjE1MiAyNy4xMzgsMy45ODFDMjcuMzQxLDQuODM0IDI3LjQ0OSw1LjcyNCAyNy40NDksNi42MzhMMjcuNDQ5LDE4LjA4NkMyNy40NDksMjQuNDA1IDE2LjAwMSwyOS41MzQgMTYuMDAxLDI5LjUzNEMxNi4wMDEsMjkuNTM0IDQuNTUzLDI0LjQwNCA0LjU1MywxOC4wODZMNC41NTMsNi42MzhDNC41NTMsNS43MjMgNC42Niw0LjgzNSA0Ljg2NCwzLjk4MUM2LjYxMiw0LjE1MiA4LjU1Nyw0LjExNSAxMC41ODQsMy44MzRDMTIuNTU4LDMuNTYgMTQuMzk2LDMuMDgzIDE2LjAwMiwyLjQ2NkwxNiwyLjQ2NlpNMTYsNC4wOUMxNC41ODcsNC42MzMgMTIuOTcsNS4wNTMgMTEuMjMyLDUuMjk0QzkuNDQ5LDUuNTQxIDcuNzM3LDUuNTc0IDYuMTk5LDUuNDI0QzYuMDIxLDYuMTc1IDUuOTI2LDYuOTU4IDUuOTI2LDcuNzYyTDUuOTI2LDE3LjgzNkM1LjkyNiwyMy4zOTcgMTYsMjcuOTExIDE2LDI3LjkxMUMxNiwyNy45MTEgMjYuMDc0LDIzLjM5NyAyNi4wNzQsMTcuODM2TDI2LjA3NCw3Ljc2MkMyNi4wNzQsNi45NTggMjUuOTc5LDYuMTc1IDI1LjgwMSw1LjQyNEMyNC4yNjMsNS41NzUgMjIuNTUxLDUuNTQyIDIwLjc2OCw1LjI5NEMxOS4wMyw1LjA1MiAxNy40MTMsNC42MzMgMTYsNC4wOVpNMTguODU3LDIwLjI4NUwxNiwxNy44MjhMMTMuMTQzLDIwLjI4NUwxMy4xNDMsMTEuNzE0TDE4Ljg1NywxMS43MTRMMTguODU3LDIwLjI4NVoiIHN0eWxlPSJmaWxsOndoaXRlO2ZpbGwtcnVsZTpub256ZXJvOyIvPgo8L3N2Zz4K"/>
		<?php printf( __( 'Version %s', 'gd-security-headers' ), gdsih_settings()->info_version ); ?>
    </div>

    <h2 class="nav-tab-wrapper wp-clearfix">
		<?php

		foreach ( $_tabs as $_tab => $_label ) {
			echo '<a href="admin.php?page=gd-security-headers-about&panel=' . $_tab . '" class="nav-tab' . ( $_tab == $_panel ? ' nav-tab-active' : '' ) . '">' . $_label . '</a>';
		}

		?>
    </h2>

    <div class="d4p-about-inner">