<?php

namespace ContentEmbed;

class AssetManager {

	const GUTENBERG_BLOCK_NAME = 'content-embed/hubspot-embed-block';

	// Script names
	const GUTENBERG               = 'content-embed-gutenberg';
	const GUTENBERG_EMBED_TRACKER = 'content-embed-usage-tracker-script';
	const HS_EMBED_STYLES         = 'content-embed-styles';
	const EMBED_INJECTOR          = 'content-embed-injector';
	const ADMIN_EMBED_TRACKER     = 'content-embed-usage-tracker-script-admin';

	// For tracking post deletions
	const HS_EMBED_DELETED_TRANSIENT = 'hubspot_content_embed_permanently_deleted_ids';
	const LAST_SYNCED_OPTION         = 'hubspot_content_embed_last_sync';

	// For usage tracking
	const EMBED_INIT_TRANSIENT = 'content_embed_did_init';
}
