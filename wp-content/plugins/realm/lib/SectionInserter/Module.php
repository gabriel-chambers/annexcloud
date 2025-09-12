<?php

namespace E25m\Realm\SectionInserter;

use E25m\Realm\Interfaces\ModuleInterface;

/**
 *
 */
class Module implements ModuleInterface
{
	private $context;

	public function is_enabled() : bool
	{
		return apply_filters(
			'enable_e25_realm_section_inserter_button',
			defined('E25_REALM_BUTTON_ENABLED')
				? E25_REALM_BUTTON_ENABLED
				: true
		);
	}

	public function activate(array $context): void
	{
		(new AssetManager($context))->enqueueAssets();
	}
}
