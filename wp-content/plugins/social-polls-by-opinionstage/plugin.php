<?php
/**
 * Poll, Survey & Quiz Maker Plugin by Opinion Stage
 *
 * @package   OpinionStageWordPressPlugin
 *
 * @wordpress-plugin
 * Plugin Name: Poll, Survey & Quiz Maker Plugin by Opinion Stage
 * Plugin URI:  https://www.opinionstage.com
 * Description: Add a highly engaging poll, survey, quiz or contact form builder to your site. You can add the poll, survey, quiz or form to any post/page or to the sidebar.
 * Version:     19.11.0
 * Author:      OpinionStage.com
 * Author URI:  https://www.opinionstage.com
 * Text Domain: social-polls-by-opinionstage
 */

use Opinionstage\Core\Module;
use Opinionstage\Infrastructure\Helper;
use Opinionstage\Modules\ModulesProvider;
require_once __DIR__ . '/src/vendor/autoload.php';


defined( 'ABSPATH' ) || die();

define( 'OPINIONSTAGE_PLUGIN_FILE', __FILE__ );
define( 'OPINIONSTAGE_PLUGIN_DIR', plugin_dir_path( OPINIONSTAGE_PLUGIN_FILE ) );

$opinionstage_settings = array();

// don't even try to load any configuration settings,
// if WordPress is not in debug mode,
// as configuration settings are only for plugin development.
if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
	$opinionstage_dev_cfg_path = plugin_dir_path( __FILE__ ) . 'dev.ini';
	if ( file_exists( $opinionstage_dev_cfg_path ) ) {
        Helper::error_log( "loading configuration from file $opinionstage_dev_cfg_path" );
		$opinionstage_settings = parse_ini_file( $opinionstage_dev_cfg_path );
	}
}

define( 'OPINIONSTAGE_WIDGET_VERSION', '19.11.0' );

define( 'OPINIONSTAGE_WIDGET_API_KEY', 'wp35e8' );
define( 'OPINIONSTAGE_UTM_SOURCE', 'wordpress' );
define( 'OPINIONSTAGE_UTM_CAMPAIGN', 'WPMainPI' );
define( 'OPINIONSTAGE_UTM_MEDIUM', 'link' );
define( 'OPINIONSTAGE_UTM_CONNECT_MEDIUM', 'connect' );
define(
	'OPINIONSTAGE_UTM_PARAMETERS',
	array(
		'utm_source'   => OPINIONSTAGE_UTM_SOURCE,
		'utm_campaign' => OPINIONSTAGE_UTM_CAMPAIGN,
		'utm_medium'   => OPINIONSTAGE_UTM_MEDIUM,
		'o'            => OPINIONSTAGE_WIDGET_API_KEY,
	)
);
define( 'OPINIONSTAGE_SERVER_BASE', isset( $opinionstage_settings['server_base'] ) ? $opinionstage_settings['server_base'] : 'https://www.opinionstage.com' );
define( 'OPINIONSTAGE_API_PATH', OPINIONSTAGE_SERVER_BASE . '/api/v1' );
define( 'OPINIONSTAGE_LOGIN_PATH', OPINIONSTAGE_SERVER_BASE . '/api/wp/v1/auth/new' );
define( 'OPINIONSTAGE_CONTENT_POPUP_CLIENT_WIDGETS_API', OPINIONSTAGE_SERVER_BASE . '/api/wp/v1/my/widgets' );
define( 'OPINIONSTAGE_CONTENT_POPUP_CLIENT_WIDGETS_API_RECENT_UPDATE', OPINIONSTAGE_SERVER_BASE . '/api/wp/v1/my/widgets/recent-update' );

define(
	'OPINIONSTAGE_REDIRECT_TEMPLATES_API_UTM',
	add_query_arg(
		OPINIONSTAGE_UTM_PARAMETERS,
		OPINIONSTAGE_SERVER_BASE . '/api/wp/redirects/templates'
	)
);
define(
	'OPINIONSTAGE_REDIRECT_CREATE_WIDGET_API_UTM',
	add_query_arg(
		OPINIONSTAGE_UTM_PARAMETERS,
		OPINIONSTAGE_SERVER_BASE . '/api/wp/redirects/widgets/new'
	)
);

define( 'OPINIONSTAGE_OPTIONS_KEY', 'opinionstage_widget' );



define( 'OPINIONSTAGE_MENU_SLUG', 'opinionstage-settings' );
define( 'OPINIONSTAGE_GETTING_STARTED_SLUG', 'opinionstage-getting-started' );
define( 'OPINIONSTAGE_HELP_RESOURCE_SLUG', 'opinionstage-help-resource' );

define( 'OPINIONSTAGE_LOGIN_CALLBACK_SLUG', 'opinionstage-login-callback' );
define( 'OPINIONSTAGE_DISCONNECT_PAGE', 'opinionstage-disconnect-page' );
define( 'OPINIONSTAGE_CONTENT_LOGIN_CALLBACK_SLUG', 'opinionstage-content-login-callback-page' );
define(
	'OPINIONSTAGE_LIVE_CHAT_URL_UTM',
	add_query_arg(
		OPINIONSTAGE_UTM_PARAMETERS,
		OPINIONSTAGE_SERVER_BASE . '/live-chat/'
	)
);

define('OPINIONSTAGE_REQUIRED_PHP_VERSION', '5.2' );

if ( ! version_compare( PHP_VERSION, OPINIONSTAGE_REQUIRED_PHP_VERSION, '>=' ) ) {
	add_action( 'admin_notices', 'opinionstage_fail_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) {
	add_action( 'admin_notices', 'opinionstage_fail_wp_version' );
} else {
	// phpcs:disable Squiz.Commenting.FunctionComment
	function opinionstage_plugin_activated( $plugin ) {
		// Check if active plugin file is plugin.php on plugin activate hook.
		if ( plugin_basename( __FILE__ ) === $plugin ) {
			$redirect_to = Helper::is_user_logged_in() ? OPINIONSTAGE_MENU_SLUG : OPINIONSTAGE_GETTING_STARTED_SLUG;
			wp_safe_redirect( 'admin.php?page=' . $redirect_to );
			exit();
		}
	}
	add_action( 'activated_plugin', 'opinionstage_plugin_activated' );

	// Check if another OpinionStage plugin already installed and display warning message.
	if ( Helper::check_plugin_available( 'opinionstage_popup' ) ) {
        /**
         * Notify about other OpinionStage plugin already available
         */
        add_action( 'admin_notices', function (){
            echo "<div id='opinionstage-warning' class='error'><p><B>".__("Opinion Stage Plugin is already installed")."</B>".__(', please remove "<B>Popup for Interactive Content by Opinion Stage</B>" and use the available "<B>Poll & Quiz tools by Opinion Stage</B>" plugin')."</p></div>";
		});
	} else {
		add_action( 'plugins_loaded', function (){
            $os_options = (array) get_option(OPINIONSTAGE_OPTIONS_KEY);
            $os_options['version'] = OPINIONSTAGE_WIDGET_VERSION;

            // For backward compatibility
            if ( !isset($os_options['sidebar_placement_active']) ) {
                $os_options['sidebar_placement_active'] = 'false';
            }

            update_option(OPINIONSTAGE_OPTIONS_KEY, $os_options);
		});
	}

	register_deactivation_hook( __FILE__, 'opinionstage_plugin_deactivate' );
	function opinionstage_plugin_deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

		check_admin_referer( "deactivate-plugin_{$plugin}" );

		delete_option( 'oswp_installation_date' );
	}
}
/**
 * Opinionstage admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function opinionstage_fail_php_version() {
	/* translators: %s: PHP version */
	$message      = sprintf( esc_html__( 'Poll, Survey & Quiz Maker Plugin by Opinion Stage requires PHP version %s+, plugin is currently NOT RUNNING.', 'social-polls-by-opinionstage' ), OPINIONSTAGE_REQUIRED_PHP_VERSION );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * Opinionstage admin notice for minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 1.5.0
 *
 * @return void
 */
function opinionstage_fail_wp_version() {
	/* translators: %s: WordPress version */
	$message      = sprintf( esc_html__( 'Poll, Survey & Quiz Maker Plugin by Opinion Stage requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'social-polls-by-opinionstage' ), '4.7' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

class Opinionstage {

    use Module;

    /** @var string */
    public $plugin_path;

    /** @var string */
    public $plugin_url;


    /**
     * @return void
     */
    public function init() {

        $this->plugin_path = __DIR__ . '/';

        $this->plugin_url = plugin_dir_url( __FILE__ );

        ModulesProvider::get_instance();
    }
}

function opinionstage() {
    return Opinionstage::get_instance();
}

opinionstage();
