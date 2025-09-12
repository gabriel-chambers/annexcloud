<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gdsih_core_scope extends d4p_core_scope {
	public static function instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new gdsih_core_scope();
		}

		return $instance;
	}
}

class gdsih_core_settings extends d4p_plugin_settings_corex {
	public $base = 'gdsih';
	public $scope = 'network';

	public $features = array();

	public $settings = array(
		'core'     => array(
			'activated'          => 0,
			'htaccess_added'     => false,
			'htaccess_available' => false,
		),
		'settings' => array(
			'htaccess' => false,
		),
		'feature'  => array(
			'variant'    => 'both',
			'protection' => false,
		),
		'csp'      => array(
			'mode'                => 'report',
			'log'                 => true,
			'log_original_policy' => false,
			'log_force_ssl'       => false,
			'log_url'             => '',

			'extra_google_adsense'     => false,
			'extra_google_analytics'   => false,
			'extra_google_fonts'       => false,
			'extra_google_maps'        => false,
			'extra_google_translate'   => false,
			'extra_google_youtube'     => false,
			'extra_google_tag_manager' => false,
			'extra_gravatar'           => true,
			'extra_gleam'              => false,
			'extra_vimeo'              => false,
			'extra_instagram'          => false,
			'extra_paypal'             => false,
			'extra_wordpress'          => false,

			'cdn' => array(),

			'auto_inline_rule'      => true,
			'auto_eval_rule'        => true,
			'auto_data_rule'        => true,
			'auto_blob_rule'        => true,
			'auto_mediastream_rule' => true,
			'auto_filesystem_rule'  => true,

			'upgrade_insecure_requests' => false,
			'block_all_mixed_content'   => false,
			'disown_opener'             => false,

			'default_basic'          => 'self',
			'default_custom'         => array(),
			'script_basic'           => 'no',
			'script_custom'          => array(),
			'style_basic'            => 'no',
			'style_custom'           => array(),
			'img_basic'              => 'no',
			'img_custom'             => array(),
			'connect_basic'          => 'no',
			'connect_custom'         => array(),
			'font_basic'             => 'no',
			'font_custom'            => array(),
			'object_basic'           => 'no',
			'object_custom'          => array(),
			'media_basic'            => 'no',
			'media_custom'           => array(),
			'child_basic'            => 'no',
			'child_custom'           => array(),
			'manifest_basic'         => 'no',
			'manifest_custom'        => array(),
			'form-action_basic'      => 'no',
			'form-action_custom'     => array(),
			'base-uri_basic'         => 'no',
			'base-uri_custom'        => array(),
			'frame-ancestors_basic'  => 'no',
			'frame-ancestors_custom' => array(),
			'prefetch_basic'         => 'no',
			'prefetch_custom'        => array(),
			'worker_basic'           => 'no',
			'worker_custom'          => array(),
			'frame_basic'            => 'no',
			'frame_custom'           => array(),
		),
		'xxp'      => array(
			'x_xss_protection' => false,
			'log'              => false,
			'log_force_ssl'    => false,
		),
		'headers'  => array(
			'x_content_type_nosniff'     => true,
			'x_frame_options_sameorigin' => false,
			'strict_transport_security'  => false,
			'referrer_policy'            => false,

			'referrer_policy_value'              => 'no-referrer-when-downgrade',
			'strict_transport_security_max_age'  => 31536000,
			'strict_transport_security_extra'    => 'includeSubDomains',
			'x_frame_options_sameorigin_value'   => 'SAMEORIGIN',
			'x_frame_options_sameorigin_domains' => '',
		),
	);

	protected function constructor() {
		$this->info = new gdsih_core_info();

		$this->features = array(
			'accelerometer'                => _x( 'Accelerometer', 'Permissions Policy Directive', 'gd-security-headers' ),
			'ambient-light-sensor'         => _x( 'Ambient Light Sensor', 'Permissions Policy Directive', 'gd-security-headers' ),
			'autoplay'                     => _x( 'Autoplay', 'Permissions Policy Directive', 'gd-security-headers' ),
			'browsing-topics'              => _x( 'Browsing Topics', 'Permissions Policy Directive', 'gd-security-headers' ),
			'camera'                       => _x( 'Camera', 'Permissions Policy Directive', 'gd-security-headers' ),
			'display-capture'              => _x( 'Display Capture', 'Permissions Policy Directive', 'gd-security-headers' ),
			'encrypted-media'              => _x( 'Encrypted Media', 'Permissions Policy Directive', 'gd-security-headers' ),
			'fullscreen'                   => _x( 'Full Screen', 'Permissions Policy Directive', 'gd-security-headers' ),
			'geolocation'                  => _x( 'GEO Location', 'Permissions Policy Directive', 'gd-security-headers' ),
			'gyroscope'                    => _x( 'Gyroscope', 'Permissions Policy Directive', 'gd-security-headers' ),
			'hid'                          => _x( 'Human Interface Devices', 'Permissions Policy Directive', 'gd-security-headers' ),
			'identity-credentials-get'     => _x( 'Identity Credentials', 'Permissions Policy Directive', 'gd-security-headers' ),
			'idle-detection'               => _x( 'Idle Detection', 'Permissions Policy Directive', 'gd-security-headers' ),
			'local-fonts'                  => _x( 'Local Fonts', 'Permissions Policy Directive', 'gd-security-headers' ),
			'magnetometer'                 => _x( 'Magnetometer', 'Permissions Policy Directive', 'gd-security-headers' ),
			'microphone'                   => _x( 'Microphone', 'Permissions Policy Directive', 'gd-security-headers' ),
			'midi'                         => _x( 'MIDI', 'Permissions Policy Directive', 'gd-security-headers' ),
			'otp-credentials'              => _x( 'OTP Credentials', 'Permissions Policy Directive', 'gd-security-headers' ),
			'payment'                      => _x( 'Payment', 'Permissions Policy Directive', 'gd-security-headers' ),
			'picture-in-picture'           => _x( 'Picture In Picture', 'Permissions Policy Directive', 'gd-security-headers' ),
			'publickey-credentials-create' => _x( 'Public Key Credentials Create', 'Permissions Policy Directive', 'gd-security-headers' ),
			'publickey-credentials-get'    => _x( 'Public Key Credentials Get', 'Permissions Policy Directive', 'gd-security-headers' ),
			'screen-wake-lock'             => _x( 'Screen Wake Lock', 'Permissions Policy Directive', 'gd-security-headers' ),
			'serial'                       => _x( 'Serial', 'Permissions Policy Directive', 'gd-security-headers' ),
			'storage-access'               => _x( 'Storage Access', 'Permissions Policy Directive', 'gd-security-headers' ),
			'usb'                          => _x( 'USB', 'Permissions Policy Directive', 'gd-security-headers' ),
			'web-share'                    => _x( 'Web Share', 'Permissions Policy Directive', 'gd-security-headers' ),
			'xr-spatial-tracking'          => _x( 'XR Spatial Tracking', 'Permissions Policy Directive', 'gd-security-headers' ),
		);

		foreach ( array_keys( $this->features ) as $feature ) {
			$this->settings['feature'][ $feature . '_basic' ]  = 'no';
			$this->settings['feature'][ $feature . '_custom' ] = array();
		}

		add_action( 'gdsih_load_settings', array( $this, 'init' ) );
	}

	protected function _db() {
		require_once( GDSIH_PATH . 'core/admin/install.php' );

		gdsih_install_database();
	}

	protected function _name( $name ) : string {
		return 'dev4press_' . $this->info->code . '_' . $name;
	}
}

/** @return gdsih_core_scope */
function gdsih_scope() {
	return gdsih_core_scope::instance();
}
