<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include( GDSIH_PATH . 'forms/shared/top.php' );
include( GDSIH_PATH . 'core/objects/core.statistics.php' );

?>

    <div class="d4p-plugin-dashboard">
        <div class="d4p-content-left">
            <div class="d4p-dashboard-badge" style="background-color: #69426A">
                <div aria-hidden="true" class="d4p-plugin-logo">
                    <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDMyIDMyIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zOnNlcmlmPSJodHRwOi8vd3d3LnNlcmlmLmNvbS8iIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtzdHJva2UtbGluZWpvaW46cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6MjsiPgogICAgPHBhdGggZD0iTTE2LDBDMTQuMTAxLDAuNzMgMTEuOTI5LDEuMjkzIDkuNTk1LDEuNjE3QzcuMTk5LDEuOTUgNC44OTksMS45OTQgMi44MzMsMS43OTFDMi41OTMsMi44IDIuNDY2LDMuODUyIDIuNDY2LDQuOTMyTDIuNDY2LDE4LjQ2NkMyLjQ2NiwyNS45MzYgMTYsMzIgMTYsMzJDMTYsMzIgMjkuNTM0LDI1LjkzNiAyOS41MzQsMTguNDY2TDI5LjUzNCw0LjkzMkMyOS41MzQsMy44NTIgMjkuNDA3LDIuOCAyOS4xNjcsMS43OTFDMjcuMSwxLjk5NCAyNC44MDEsMS45NSAyMi40MDUsMS42MTdDMjAuMDcxLDEuMjkzIDE3Ljg5OSwwLjczIDE2LDBaTTE2LDIuNDY2QzE3LjYwNiwzLjA4MyAxOS40NDQsMy41NiAyMS40MTgsMy44MzRDMjMuNDQ0LDQuMTE1IDI1LjM4OSw0LjE1MiAyNy4xMzgsMy45ODFDMjcuMzQxLDQuODM0IDI3LjQ0OSw1LjcyNCAyNy40NDksNi42MzhMMjcuNDQ5LDE4LjA4NkMyNy40NDksMjQuNDA1IDE2LjAwMSwyOS41MzQgMTYuMDAxLDI5LjUzNEMxNi4wMDEsMjkuNTM0IDQuNTUzLDI0LjQwNCA0LjU1MywxOC4wODZMNC41NTMsNi42MzhDNC41NTMsNS43MjMgNC42Niw0LjgzNSA0Ljg2NCwzLjk4MUM2LjYxMiw0LjE1MiA4LjU1Nyw0LjExNSAxMC41ODQsMy44MzRDMTIuNTU4LDMuNTYgMTQuMzk2LDMuMDgzIDE2LjAwMiwyLjQ2NkwxNiwyLjQ2NlpNMTYsNC4wOUMxNC41ODcsNC42MzMgMTIuOTcsNS4wNTMgMTEuMjMyLDUuMjk0QzkuNDQ5LDUuNTQxIDcuNzM3LDUuNTc0IDYuMTk5LDUuNDI0QzYuMDIxLDYuMTc1IDUuOTI2LDYuOTU4IDUuOTI2LDcuNzYyTDUuOTI2LDE3LjgzNkM1LjkyNiwyMy4zOTcgMTYsMjcuOTExIDE2LDI3LjkxMUMxNiwyNy45MTEgMjYuMDc0LDIzLjM5NyAyNi4wNzQsMTcuODM2TDI2LjA3NCw3Ljc2MkMyNi4wNzQsNi45NTggMjUuOTc5LDYuMTc1IDI1LjgwMSw1LjQyNEMyNC4yNjMsNS41NzUgMjIuNTUxLDUuNTQyIDIwLjc2OCw1LjI5NEMxOS4wMyw1LjA1MiAxNy40MTMsNC42MzMgMTYsNC4wOVpNMTguODU3LDIwLjI4NUwxNiwxNy44MjhMMTMuMTQzLDIwLjI4NUwxMy4xNDMsMTEuNzE0TDE4Ljg1NywxMS43MTRMMTguODU3LDIwLjI4NVoiIHN0eWxlPSJmaWxsOndoaXRlO2ZpbGwtcnVsZTpub256ZXJvOyIvPgo8L3N2Zz4K"/>
                </div>
                <h3>GD Security Headers</h3>

                <h5>
					<?php

					esc_html_e( 'Version', 'gd-security-headers' );
					echo ': ' . gdsih_settings()->info->version;

					if ( gdsih_settings()->info->status != 'stable' ) {
						echo ' - <span class="d4p-plugin-unstable" style="color: #fff; font-weight: 900;">' . strtoupper( gdsih_settings()->info->status ) . '</span>';
					}

					?>

                </h5>
            </div>

            <div class="d4p-buttons-group">
                <a class="button-secondary" href="admin.php?page=gd-security-headers-settings"><i aria-hidden="true" class="fa fa-cogs fa-fw"></i> <?php esc_html_e( 'Settings', 'gd-security-headers' ); ?>
                </a>
                <a class="button-secondary" href="admin.php?page=gd-security-headers-tools"><i aria-hidden="true" class="fa fa-wrench fa-fw"></i> <?php esc_html_e( 'Tools', 'gd-security-headers' ); ?>
                </a>
            </div>

            <div class="d4p-buttons-group">
                <a class="button-secondary" href="admin.php?page=gd-security-headers-about"><i aria-hidden="true" class="fa fa-info-circle fa-fw"></i> <?php esc_html_e( 'About', 'gd-security-headers' ); ?>
                </a>
            </div>
        </div>
        <div class="d4p-content-right">
			<?php

			include( GDSIH_PATH . 'forms/dashboard/headers.php' );
			include( GDSIH_PATH . 'forms/dashboard/permissions.php' );
			include( GDSIH_PATH . 'forms/dashboard/reports.php' );

			?>
        </div>
    </div>

<?php

include( GDSIH_PATH . 'forms/shared/bottom.php' );
