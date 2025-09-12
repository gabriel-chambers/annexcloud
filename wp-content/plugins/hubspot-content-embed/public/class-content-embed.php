<?php

namespace ContentEmbed;

use ContentEmbed\admin\ContentEmbedAdmin;
use ContentEmbed\EmbedTracking;
use Leadin\AssetsManager;
use Leadin\admin\AdminConstants;
use ContentEmbed\Updater;
use ContentEmbed\AssetManager;
use ContentEmbed\PreloadBlocks;

class ContentEmbed {

	public function __construct() {
		if ( function_exists( 'register_block_type' ) ) {
			// Gutenberg is active.
			add_action( 'init', array( $this, 'register_gutenberg_block' ) );
		}

		add_filter( 'script_loader_tag', array( $this, 'add_type_attribute' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_frontend_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_scripts' ), 11 );

		if ( is_admin() ) {
			new Updater();
			new EmbedTracking();
			new ContentEmbedAdmin();
		}

		// TODO: Do we want this to be behind a global setting as well as being per-post?
		// TODO: Enable this once the relevant changes have been made on backend.
		// new PostPreviews();
		new PreloadBlocks();
	}


	/**
	 * Adds basic info about plugin to window inside of the Gutenberg editor
	 */
	public static function add_content_embed_config() {
		$default_domain              = get_option( 'hs_embed_plugin_embed_domain' );
		$content_embed_config_object = array(
			'defaultEmbedDomain' => $default_domain,
			'pluginVersion'      => HS_EMBED_VERSION,
			'pluginPath'         => HS_EMBED_PLUGIN_BASE_PATH,
			'injectorPath'       => HS_EMBED_PLUGIN_JS_BASE_PATH . '/hsEmbedInjector.js',
		);

		$content_embed_config = 'window.contentEmbedConfig = ' . wp_json_encode( $content_embed_config_object ) . ';';

		// Make sure the script loads before the block.
		wp_add_inline_script( AssetManager::GUTENBERG, $content_embed_config, 'before' );
	}


	/**
	 * Registers the block inside of the editor
	 */
	public static function register_gutenberg_block() {
		wp_register_style( AssetManager::HS_EMBED_STYLES, HS_EMBED_PLUGIN_ASSETS_BASE_PATH . '/main.css', array(), HS_EMBED_VERSION );
		wp_enqueue_style( AssetManager::HS_EMBED_STYLES );

		wp_register_script( AssetManager::GUTENBERG, HS_EMBED_PLUGIN_JS_BASE_PATH . '/gutenberg.js', array( 'wp-blocks', 'wp-element', 'wp-i18n' ), HS_EMBED_VERSION, true );
		wp_register_script( AssetManager::EMBED_INJECTOR, HS_EMBED_PLUGIN_JS_BASE_PATH . '/hsEmbedInjector.js', array(), HS_EMBED_VERSION, true );

		wp_set_script_translations( AssetManager::GUTENBERG, 'contentembed', __DIR__ . '/../languages' );

		register_block_type(
			AssetManager::GUTENBERG_BLOCK_NAME,
			array(
				'editor_script'   => AssetManager::GUTENBERG,
				'render_callback' => function ( $attribs, $content ) {
					wp_enqueue_script( AssetManager::EMBED_INJECTOR );

					return $content;
				},
			)
		);

		self::add_content_embed_config();
	}


	/**
	 * Adds embed tracker to Gutenberg block editor and passes in the placeholder image URL
	 */
	public static function enqueue_editor_scripts() {
		wp_enqueue_script( AssetManager::GUTENBERG_EMBED_TRACKER, HS_EMBED_PLUGIN_JS_BASE_PATH . '/embedEditorTracker.js', array( 'jquery' ), HS_EMBED_VERSION, true );

		wp_localize_script(
			AssetManager::GUTENBERG,
			'hs_content_embed_preview',
			array(
				'placeholder_image_url' => plugin_dir_url( __DIR__ ) . 'public/assets/images/mobile-onboarding-embed-tracking-code.svg',
			)
		);

	}


	/**
	 * For script_loader_filter to add `type = module` to scripts
	 */
	public static function add_type_attribute( $tag, $handle, $src ) {
		// https://stackoverflow.com/a/59594789
		if ( AssetManager::EMBED_INJECTOR !== $handle ) {
			return $tag;
		}
		// change the script tag by adding type="module" and return it.
		$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- This is augments scripts for module type.
		return $tag;
	}


	/**
	 * Adds embed injector script to site pages
	 */
	public static function add_frontend_scripts() {
		wp_register_script( AssetManager::EMBED_INJECTOR, HS_EMBED_PLUGIN_JS_BASE_PATH . '/hsEmbedInjector.js', array(), HS_EMBED_VERSION, true );
		wp_enqueue_script( AssetManager::EMBED_INJECTOR );
	}
}
