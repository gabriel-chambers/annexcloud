<?php

namespace ContentEmbed;

use ContentEmbed\BlockListing;
use ContentEmbed\AssetManager;

class EmbedTracking {

	public function __construct() {
		add_action( 'before_delete_post', array( $this, 'set_delete_transient' ), 10, 2 );
	}

	public static function set_delete_transient( $post_id, $post ) {

		// Delete_post runs for every revision...(?) so check the number of runs so only run on first time
		if ( empty( $post_id ) || empty( $post ) || 'inherit' === $post->post_status ) {
			return;
		}

		$deleted_posts = get_transient( AssetManager::HS_EMBED_DELETED_TRANSIENT ) ?: array(); // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
		if ( ! in_array( $post_id, $deleted_posts, true ) ) {
			$deleted_posts[] = array(
				'pageId'  => $post_id,
				'pageUrl' => $post->guid,
			);
		}

		set_transient( AssetManager::HS_EMBED_DELETED_TRANSIENT, $deleted_posts, 5000 );
	}

	public static function forward_bulk_tracking_data() {
		$blocks_listing = BlockListing::get_content_embed_blocks();
		$deleted_posts  = get_transient( AssetManager::HS_EMBED_DELETED_TRANSIENT );
		if ( ! empty( $deleted_posts ) ) {

			delete_transient( AssetManager::HS_EMBED_DELETED_TRANSIENT );
		}

		?>
		<script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function() {
					var scriptTag = document.createElement('script');
					scriptTag.textContent = `
						var blocks_listing = <?php echo wp_json_encode( $blocks_listing ); ?>;
						var deleted_posts = <?php echo wp_json_encode( $deleted_posts ); ?>;

						window.contentEmbedProxy.trackBulkContentEmbedData(blocks_listing, deleted_posts);
					`;
					document.body.appendChild(scriptTag);
			});
		</script>
		<?php
	}

	public static function track_plugin_activation() {
		?>
		<script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function() {
					var scriptTag = document.createElement('script');
					scriptTag.textContent = 'window.contentEmbedProxy.trackPluginActivation();';
					document.body.appendChild(scriptTag);
			});
		</script>
		<?php
	}
}
