<?php

namespace ContentEmbed;

use ContentEmbed\AssetManager;

class BlockListing {

	private static function should_track( array $tracking_data ): bool {
		return isset( $tracking_data['embedIds'] ) && ! empty( $tracking_data['embedIds'] );
	}

	private static function is_block_valid( array $block ): bool {
		return AssetManager::GUTENBERG_BLOCK_NAME === $block['blockName'] &&
			isset( $block['attrs'] ) &&
			isset( $block['attrs']['embedId'] );
	}

	private static function extract_embed_ids( $blocks ) {
		$embed_ids = array();
		foreach ( $blocks as $block ) {
			if ( self::is_block_valid( $block ) ) {
				$embed_ids[] = $block['attrs']['embedId'];
			}

			$inner_blocks = $block['innerBlocks'];
			// Recurse if there's an inner block (e.g. column or something)
			if ( isset( $inner_blocks ) & ! empty( $inner_blocks ) ) {
				$embed_ids = array_merge( $embed_ids, self::extract_embed_ids( $inner_blocks ) );
			}
		}

		return $embed_ids;
	}

	private static function get_post_tracking_data( \WP_Post $post ) {
		if ( ! is_object( $post ) || ! isset( $post->post_content ) ) {
			return null;
		}

		$blocks    = parse_blocks( $post->post_content );
		$published = ( 'publish' === $post->post_status );

		$embed_ids = self::extract_embed_ids( $blocks );

		return array(
			'embedIds'  => $embed_ids,
			'pageId'    => $post->ID,
			'pageUrl'   => get_the_permalink( $post ),
			'pageTitle' => get_the_title( $post ),
			'published' => $published,
		);
	}

	private static function get_all_posts_and_pages() {
		$args = array(
			'post_type'      => array( 'post', 'page' ),
			'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'posts_per_page' => -1, // -1 => Get all posts
		);

		return get_posts( $args );
	}

	public static function get_content_embed_blocks() {
		$posts_pages = self::get_all_posts_and_pages();
		if ( empty( $posts_pages ) ) {
			return null;
		}

		$embeds_tracking_data = array_map( array( self::class, 'get_post_tracking_data' ), $posts_pages );
		return array_values( array_filter( $embeds_tracking_data, array( self::class, 'should_track' ) ) );
	}
}
