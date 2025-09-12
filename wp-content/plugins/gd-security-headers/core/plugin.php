<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_core_plugin extends d4p_plugin_core {
	public $svg_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDMyIDMyIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zOnNlcmlmPSJodHRwOi8vd3d3LnNlcmlmLmNvbS8iIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtzdHJva2UtbGluZWpvaW46cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6MjsiPgogICAgPHBhdGggZD0iTTE2LDBDMTQuMTAxLDAuNzMgMTEuOTI5LDEuMjkzIDkuNTk1LDEuNjE3QzcuMTk5LDEuOTUgNC44OTksMS45OTQgMi44MzMsMS43OTFDMi41OTMsMi44IDIuNDY2LDMuODUyIDIuNDY2LDQuOTMyTDIuNDY2LDE4LjQ2NkMyLjQ2NiwyNS45MzYgMTYsMzIgMTYsMzJDMTYsMzIgMjkuNTM0LDI1LjkzNiAyOS41MzQsMTguNDY2TDI5LjUzNCw0LjkzMkMyOS41MzQsMy44NTIgMjkuNDA3LDIuOCAyOS4xNjcsMS43OTFDMjcuMSwxLjk5NCAyNC44MDEsMS45NSAyMi40MDUsMS42MTdDMjAuMDcxLDEuMjkzIDE3Ljg5OSwwLjczIDE2LDBaTTE2LDIuNDY2QzE3LjYwNiwzLjA4MyAxOS40NDQsMy41NiAyMS40MTgsMy44MzRDMjMuNDQ0LDQuMTE1IDI1LjM4OSw0LjE1MiAyNy4xMzgsMy45ODFDMjcuMzQxLDQuODM0IDI3LjQ0OSw1LjcyNCAyNy40NDksNi42MzhMMjcuNDQ5LDE4LjA4NkMyNy40NDksMjQuNDA1IDE2LjAwMSwyOS41MzQgMTYuMDAxLDI5LjUzNEMxNi4wMDEsMjkuNTM0IDQuNTUzLDI0LjQwNCA0LjU1MywxOC4wODZMNC41NTMsNi42MzhDNC41NTMsNS43MjMgNC42Niw0LjgzNSA0Ljg2NCwzLjk4MUM2LjYxMiw0LjE1MiA4LjU1Nyw0LjExNSAxMC41ODQsMy44MzRDMTIuNTU4LDMuNTYgMTQuMzk2LDMuMDgzIDE2LjAwMiwyLjQ2NkwxNiwyLjQ2NlpNMTYsNC4wOUMxNC41ODcsNC42MzMgMTIuOTcsNS4wNTMgMTEuMjMyLDUuMjk0QzkuNDQ5LDUuNTQxIDcuNzM3LDUuNTc0IDYuMTk5LDUuNDI0QzYuMDIxLDYuMTc1IDUuOTI2LDYuOTU4IDUuOTI2LDcuNzYyTDUuOTI2LDE3LjgzNkM1LjkyNiwyMy4zOTcgMTYsMjcuOTExIDE2LDI3LjkxMUMxNiwyNy45MTEgMjYuMDc0LDIzLjM5NyAyNi4wNzQsMTcuODM2TDI2LjA3NCw3Ljc2MkMyNi4wNzQsNi45NTggMjUuOTc5LDYuMTc1IDI1LjgwMSw1LjQyNEMyNC4yNjMsNS41NzUgMjIuNTUxLDUuNTQyIDIwLjc2OCw1LjI5NEMxOS4wMyw1LjA1MiAxNy40MTMsNC42MzMgMTYsNC4wOVpNMTguODU3LDIwLjI4NUwxNiwxNy44MjhMMTMuMTQzLDIwLjI4NUwxMy4xNDMsMTEuNzE0TDE4Ljg1NywxMS43MTRMMTguODU3LDIwLjI4NVoiIHN0eWxlPSJmaWxsOnJnYigxNjcsMTcwLDE3Myk7ZmlsbC1ydWxlOm5vbnplcm87Ii8+Cjwvc3ZnPgo=';

	public $enqueue = true;
	public $cap = 'gd-security-headers-standard';
	public $plugin = 'gd-security-headers';

	/** @var d4p_datetime_core */
	public $datetime;

	private $_ip;
	private $_ua;

	/** @var bool|gdsih_component_csp */
	private $_csp = false;
	/** @var bool|gdsih_component_xxp */
	private $_xxp = false;
	/** @var bool|gdsih_component_headers */
	private $_hdr = false;
	/** @var bool|gdsih_component_feature_policy */
	private $_fep = false;

	public function __construct() {
		parent::__construct();

		if ( ! defined( 'GDSIH_HTACCESS_FILE_NAME' ) ) {
			define( 'GDSIH_HTACCESS_FILE_NAME', '.htaccess' );
		}

		$this->url      = GDSIH_URL;
		$this->datetime = new d4p_datetime_core();
	}

	public function ip() {
		return $this->_ip;
	}

	public function ua() {
		return $this->_ua;
	}

	public function plugins_loaded() {
		parent::plugins_loaded();

		$this->_ip = d4p_visitor_ip();
		$this->_ua = d4p_user_agent();

		define( 'GDSIH_WPV', intval( $this->wp_version ) );
		define( 'GDSIH_WPV_MAJOR', substr( $this->wp_version, 0, 3 ) );
		define( 'GDSIH_WP_VERSION', $this->wp_version_real );

		do_action( 'gdsih_load_settings' );

		do_action( 'gdsih_plugin_init' );

		$this->load();
	}

	public function load() {
		add_action( 'gdsih_saved_the_settings', array( $this, 'htaccess' ) );

		if ( gdsih_settings()->get( 'mode', 'csp' ) != 'disable' ) {
			require_once( GDSIH_PATH . 'core/objects/core.csp.build.php' );
			require_once( GDSIH_PATH . 'core/objects/core.csp.php' );

			$this->_csp = new gdsih_component_csp();
		}

		if ( gdsih_settings()->get( 'x_xss_protection', 'xxp' ) ) {
			require_once( GDSIH_PATH . 'core/objects/core.xxp.php' );

			$this->_xxp = new gdsih_component_xxp();
		}

		if ( gdsih_settings()->get( 'protection', 'feature' ) ) {
			require_once( GDSIH_PATH . 'core/objects/core.feature.build.php' );
			require_once( GDSIH_PATH . 'core/objects/core.feature.php' );

			$this->_fep = new gdsih_component_feature_policy();
		}

		require_once( GDSIH_PATH . 'core/objects/core.headers.php' );

		$this->_hdr = new gdsih_component_headers();
	}

	public function htaccess() {
		require_once( GDSIH_PATH . 'core/objects/core.htaccess.php' );

		$htaccess = new gdsih_core_htaccess();
		$status   = $htaccess->check();

		if ( $status['automatic'] ) {
			gdsih_settings()->set( 'htaccess_available', true, 'core' );

			if ( gdsih_settings()->get( 'htaccess' ) ) {
				gdsih_settings()->set( 'htaccess_added', true, 'core' );

				$htaccess->write();
			} else {
				gdsih_settings()->set( 'htaccess_added', false, 'core' );

				$htaccess->reset();
			}

			gdsih_settings()->save( 'core' );
		}
	}

	public function build_headers_to_array() : array {
		$list = array();

		if ( $this->_csp !== false ) {
			$list['content-security-policy'] = $this->_csp->csp->build( true );
		}

		if ( $this->_xxp !== false ) {
			$list['x-xss-protection'] = $this->_xxp->build( true );
		}

		if ( $this->_fep !== false ) {
			$headers = $this->_fep->fep->build( true );

			if ( ! empty( $headers ) ) {
				foreach ( $headers as $header ) {
					$parts                           = explode( ' ', $header, 2 );
					$list[ strtolower( $parts[0] ) ] = $header;
				}
			}
		}

		$list += $this->_hdr->build( true );

		return $list;
	}
}
