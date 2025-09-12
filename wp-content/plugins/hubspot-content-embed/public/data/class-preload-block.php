<?php

namespace ContentEmbed;

class PreloadBlocks {

	const CONTENT_EMBEDS_META_KEY = 'content_embed_domains';
	const NONCE_PREFIX            = 'hubspot_content_embed_';
	const HS_UTK                  = 'hubspotutk';

	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
		if ( is_admin() ) {
			add_action( 'wp_ajax_update_embeds_listing', array( __CLASS__, 'update_embeds_listing' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'add_nonce_script' ) );
		}
		add_action( 'wp_head', array( __CLASS__, 'inject_preload_links' ), 1 );
	}

	private static function get_embed_preload_tag( string $embed_domain, string $query_string ) {
		if ( ! empty( $query_string ) ) {
			$embed_domain .= '?' . $query_string;
		}

		return '<link rel="preload" as="fetch" href="' . esc_url( $embed_domain ) . '" crossorigin="anonymous">';
	}

	/**
	 * Build the query string to append to the preloaded URL.
	 * Make sure the keep this in sync with the hsEmbedInjector.
	 */
	private static function build_query_string() {
		$query_params = $_GET; // phpcs:ignore -- This isn't processing data no nonce necessary
		$hsutk        = $_COOKIE[ self::HS_UTK ] ?? ''; // phpcs:ignore -- This isn't processing data just building a query string
		if ( ! empty( $hsutk ) ) {
			$query_params['hsutk'] = $hsutk;
		}
		return http_build_query( $query_params );
	}

	/**
	 * Inject preload tags for each embed block domain.
	 * The array should be unique so not worried about duplicates.
	 */
	public static function inject_preload_links() {
		// Don't try the preload on previews because the embeds have cache busters
		if ( ! is_singular() || is_preview() ) {
			return;
		}

		$post_id      = get_the_ID();
		$query_string = self::build_query_string();

		// Shift to unnest the meta key
		$domains = (array) get_post_meta( $post_id, self::CONTENT_EMBEDS_META_KEY );
		$domains = count( $domains ) ? array_shift( $domains ) : array();

		foreach ( $domains as $embed_domain ) {
			echo self::get_embed_preload_tag( $embed_domain, $query_string ); // phpcs:ignore -- Content escaped in method above
		}
	}

	/**
	 * Add the AJAX nonce to the editor.
	 */
	public static function add_nonce_script() {
		if ( ! function_exists( 'get_current_screen' ) || ! get_current_screen()->is_block_editor() ) {
			return;
		}
		$post_id = get_the_ID();
		wp_localize_script(
			AssetManager::GUTENBERG,
			'contentEmbedMetaNonce',
			wp_create_nonce( self::NONCE_PREFIX . $post_id ),
		);
	}

	/**
	 * Ajax request to update content embeds listing
	 */
	public static function update_embeds_listing() {
		$post_id            = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';
		$content_embed_urls = isset( $_POST[ self::CONTENT_EMBEDS_META_KEY ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::CONTENT_EMBEDS_META_KEY ] ) ) : '';
		$nonce              = self::NONCE_PREFIX . $post_id;
		if ( ! isset( $post_id, $content_embed_urls ) ||
			! check_ajax_referer( $nonce, false, false ) ) {
			wp_send_json_error( 'Invalid request' );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( 'cannot_edit' );
		}

		$content_embed_urls = (array) json_decode( $content_embed_urls, true );
		$updated            = update_post_meta( $post_id, self::CONTENT_EMBEDS_META_KEY, $content_embed_urls );

		wp_send_json_success( array( 'success' => $updated ) );
	}

	/**
	 * Register the post meta key that gets used to track embed domains
	 */
	public static function register_meta() {
		register_post_meta(
			'',
			self::CONTENT_EMBEDS_META_KEY,
			array(
				'single'  => true,
				'type'    => 'array',
				'default' => array(),
			)
		);
	}
}
