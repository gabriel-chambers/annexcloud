<?php
/**
 * Class Opinionstage.
 *
 * @package   OpinionStageWordPressPlugin
 */

defined( 'ABSPATH' ) || die();

require_once OPINIONSTAGE_PLUGIN_DIR . 'includes/logging.php';

/**
 * Class OpinionStageAdminPageLoader
 *
 * Opinionstage Admin Page Loader is responsible for
 * loading functional files and assets in wp-admin.
 */
class OpinionStageAdminPageLoader {
	/**
	 * Instance.
	 *
	 * @var bool | void
	 */
	protected static $instance = false;
	/**
	 * Page slug.
	 *
	 * @var bool
	 */
	protected $slug = false;
	/**
	 * Assets path.
	 *
	 * @var string
	 */
	protected $assets_path = 'resources/';
	/**
	 * Helpers Path.
	 *
	 * @var string
	 */
	protected $helpers_path = 'helpers/';
	/**
	 * Views Path.
	 *
	 * @var string
	 */
	protected $views_path = 'views/';

	/**
	 * OpinionStageAdminPageLoader constructor.
	 */
	protected function __construct() {
		include_once plugin_dir_path( __FILE__ ) . 'content-popup.php';
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_fonts' ) );

        $this->prepare_slug();

		if ( false !== $this->slug ) {
			$this->load_file();
			add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
			add_action( 'admin_head', array( $this, 'maybe_load_header' ) );
			add_action( 'admin_footer', array( $this, 'load_footer_function' ) );
		}
	}

	/**
	 * Prepare slug.
	 */
	public function prepare_slug() {
		if ( ! empty( $_REQUEST['page'] ) ) {
			$qry_str_check_os = sanitize_text_field( $_REQUEST['page'] );
			$qry_str_check_os = explode( '-', $qry_str_check_os );
			if ( 'opinionstage' === $qry_str_check_os[0] ) {
				$this->slug = str_replace( 'opinionstage-', '', sanitize_text_field( $_REQUEST['page'] ) );
			}
		}
	}

	/**
	 * Load dynamic functional file.
	 */
	public function load_file() {

		if ( file_exists( plugin_dir_path( __FILE__ ) . $this->assets_path . 'common.php' ) ) {
			include_once plugin_dir_path( __FILE__ ) . $this->assets_path . 'common.php';
		}

		if ( file_exists( plugin_dir_path( __FILE__ ) . $this->assets_path . $this->slug . '.php' ) ) {
			include_once plugin_dir_path( __FILE__ ) . $this->assets_path . $this->slug . '.php';
		}
	}

	/**
	 * Load dynamic assets files.
	 */
	public function load_assets() {
		$function_name = str_replace( '-', '_', $this->slug );
		$function_name = 'opinionstage_' . $function_name . '_load_resources';
		if ( function_exists( $function_name ) ) {
			call_user_func( $function_name );
		}

		if ( function_exists( 'opinionstage_common_load_resources' ) ) {
			opinionstage_common_load_resources();
		}
	}

	/**
	 * Load required fonts
	 *
	 * @return void
	 */
	public function enqueue_fonts() {
		wp_enqueue_style(
			'opinionstage-fonts',
			'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@400;500;600;700&display=swap',
			null,
			OPINIONSTAGE_WIDGET_VERSION
		);
	}

	/**
	 * Maybe load function in head.
	 */
	public function maybe_load_header() {
		$function_name_header = str_replace( '-', '_', $this->slug );
		$function_name_header = 'opinionstage_' . $function_name_header . '_load_header';
		if ( function_exists( $function_name_header ) ) {
			call_user_func( $function_name_header );
		}

		$function_name_header_common = 'opinionstage_common_load_header';
		if ( function_exists( $function_name_header_common ) ) {
			call_user_func( $function_name_header_common );
		}
	}

	/**
	 * Maybe load function in footer.
	 */
	public function load_footer_function() {
		$function_name_footer = str_replace( '-', '_', $this->slug );
		$function_name_footer = 'opinionstage_' . $function_name_footer . '_load_footer';
		if ( function_exists( $function_name_footer ) ) {
			call_user_func( $function_name_footer );
		}
		$function_name_footer_common = 'opinionstage_common_load_footer';
		if ( function_exists( $function_name_footer_common ) ) {
			call_user_func( $function_name_footer_common );
		}
	}

	/**
	 * Maybe load template file.
	 */
	public function maybe_load_template_file() {

		$file_name = str_replace( '-', '_', $this->slug );
		$file_name = $file_name . '.php';
		if ( file_exists( plugin_dir_path( __FILE__ ) . $this->helpers_path . 'common.php' ) ) {
			include plugin_dir_path( __FILE__ ) . $this->helpers_path . 'common.php';
		}
		if ( file_exists( plugin_dir_path( __FILE__ ) . $this->helpers_path . $file_name ) ) {
			include plugin_dir_path( __FILE__ ) . $this->helpers_path . $file_name;
		}
		if ( file_exists( plugin_dir_path( __FILE__ ) . $this->views_path . $file_name ) ) {
			include plugin_dir_path( __FILE__ ) . $this->views_path . $file_name;
		}
	}

	/**
	 * Get Instance.
	 *
	 * @return bool|OpinionStageAdminPageLoader|void
	 */
	public static function get_instance() {
		if ( false === self::$instance ) {
			self::$instance = new OpinionStageAdminPageLoader();
		}
		return self::$instance;
	}
}

OpinionStageAdminPageLoader::get_instance();
