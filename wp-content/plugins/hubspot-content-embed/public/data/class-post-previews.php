<?php

namespace ContentEmbed;

use ContentEmbed\AssetManager;

if ( ! class_exists( 'WP' ) ) {
	die();
}

class PostPreviews {

	const HS_PREVIEW_KEY = 'hs_embed_wp_preview_id';
	const NONCE_PREFIX   = 'hubspot_post_preview_';

	public function __construct() {
		if ( is_admin() ) {
			add_action( 'wp_ajax_set_hs_preview_id', array( 'ContentEmbed\PostPreviews', 'set_hs_preview_id' ) );
			register_post_meta(
				'',
				self::HS_PREVIEW_KEY,
				array(
					'single'  => true,
					'type'    => 'string',
					'default' => '',
				)
			);
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_preview_script' ) );
		} else {
			add_action( 'pre_get_posts', array( __CLASS__, 'show_public_preview' ) );
		}
	}

	public static function show_public_preview( \WP_Query $query ) {
		if (
			$query->is_main_query()
			&& $query->is_preview()
			&& $query->is_singular()
			&& isset( $_GET[ self::HS_PREVIEW_KEY ] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This data is not being processed no need for nonce
		) {
			add_filter( 'posts_results', array( __CLASS__, 'set_post_to_publish' ), 10, 2 );
		}
	}

	/**
	 * Updates post to be published if it can be previewed
	 */
	public static function set_post_to_publish( $posts ) {
		remove_filter( 'posts_results', array( __CLASS__, 'set_post_to_publish' ), 10 );
		if ( empty( $posts ) ) {
			return $posts;
		}
		$post            = $posts[0];
		$post_preview_id = get_post_meta( $post->ID, self::HS_PREVIEW_KEY, true );

		if ( self::can_preview_post( $post_preview_id ) ) {
			$posts[0]->post_status = 'publish';
		}

		return $posts;
	}


	/**
	 * Validates the user can preview the post by checking that the query parameter matches the post preview id in the post meta
	 */
	private static function can_preview_post( $post_preview_id ) {
		// For a post to be previewable:
		// i) Referrer is HubSpot
		// ii) URL and post both have the same hs_preview_id
		$referrer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		if ( ! $referrer ) {
			return false;
		}

		$referrer_url = wp_parse_url( $referrer );
		if ( empty( $referrer_url['host'] ) ) {
			return false;
		}

		$hs_url_pattern = '/^(local|app)\.hubspot(qa)?\.com$/';
		if ( ! preg_match( $hs_url_pattern, strtolower( $referrer_url['host'] ) ) ) {
			return false;
		}

		$url_preview_id = isset( $_GET[ self::HS_PREVIEW_KEY ] ) ? sanitize_text_field( wp_unslash( $_GET[ self::HS_PREVIEW_KEY ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This data is not being processed no need for nonce

		if ( ! $url_preview_id || ! $post_preview_id ) {
			return false;
		}

		return $url_preview_id === $post_preview_id;
	}

	/**
	 * Checks the post meta object to see if preview enabled.
	 * Returns false if it's empty, true otherwise. No other validation.
	 */
	public static function is_preview_enabled( \WP_Post $post ) {
		$preview_query_id = get_post_meta( $post->ID, self::HS_PREVIEW_KEY, true );

		if ( ! $preview_query_id ) {
			return false;
		}
		return $preview_query_id;
	}

	public static function enqueue_preview_script() {
		if ( function_exists( 'get_current_screen' ) && get_current_screen()->is_block_editor() ) {
			$post            = get_post();
			$preview_id      = self::is_preview_enabled( $post );
			$preview_enabled = $preview_id ? true : false;

			// We need to do this nesting so that these actually get cast as booleans. What a bizarre language.
			$settings = array(
				'preview' => array(
					'previewEnabled' => $preview_enabled,
					'previewId'      => $preview_id,
				),
				'nonce'   => wp_create_nonce( self::NONCE_PREFIX . $post->ID ),
			);
			wp_localize_script(
				AssetManager::GUTENBERG,
				'contentEmbedPagePreview',
				$settings
			);
		}
	}

	/**
	 * Ajax request that disable/enables the preview based on the ID.
	 */
	public static function set_hs_preview_id() {
		$preview_post_id = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : false;
		check_ajax_referer( self::NONCE_PREFIX . $preview_post_id );

		$preview_enabled = isset( $_POST['preview_enabled'] ) ? (bool) filter_var( wp_unslash( $_POST['preview_enabled'] ), FILTER_VALIDATE_BOOLEAN ) : false;
		$preview_id      = isset( $_POST['preview_id'] ) ? (string) sanitize_text_field( wp_unslash( $_POST['preview_id'] ) ) : false;

		if ( ! isset( $preview_post_id, $preview_enabled, $preview_id ) ) {
			wp_send_json_error( 'incomplete_data' );
		}

		if ( ! current_user_can( 'edit_post', $preview_post_id ) ) {
			wp_send_json_error( 'cannot_edit' );
		}

		if ( $preview_enabled ) {
			update_post_meta( $preview_post_id, self::HS_PREVIEW_KEY, $preview_id );
		} else {
			// Clear the ID
			update_post_meta( $preview_post_id, self::HS_PREVIEW_KEY, '' );
		}

		wp_send_json_success(
			array(
				'previewEnabled' => $preview_enabled,
				'previewId'      => $preview_id,
			)
		);
	}
}
