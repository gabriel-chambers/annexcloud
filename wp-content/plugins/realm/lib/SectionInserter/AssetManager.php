<?php

namespace E25m\Realm\SectionInserter;

use E25m\Realm\traits\AssetHelper;

/**
 *
 */
class AssetManager
{
	use AssetHelper;

	const REALM_BUTTON_VENDORS_SCRIPT_ID 	= 'e25m-realm-button-vendors';
	const REALM_BUTTON_SCRIPT_ID 			= 'e25m-realm-button';
	const REALM_BUTTON_STYLES_ID 			= 'e25m-realm-button-styles';

	private $context;

	function __construct(array $context)
	{
		$this->context = $context;
	}

	/**
	 * Enqueue all scripts and styles required for Realm button
	 *
	 * @return void
	 */
	public function enqueueAssets() : void
	{
		add_action('enqueue_block_editor_assets', function () {
			$realm_button_deps_and_version = $this->getScriptDependenciesAndVersion('realm_button');
			if (file_exists($this->context['path'] . "dist/realm_button_vendors.js")) {
				wp_enqueue_script(
					AssetManager::REALM_BUTTON_VENDORS_SCRIPT_ID,
					$this->context['url'] . 'dist/realm_button_vendors.js'
				);
				if (!in_array(AssetManager::REALM_BUTTON_VENDORS_SCRIPT_ID, $realm_button_deps_and_version['dependencies'])) {
					$realm_button_deps_and_version['dependencies'][] = AssetManager::REALM_BUTTON_VENDORS_SCRIPT_ID;
				}
			}
			wp_enqueue_script(
				AssetManager::REALM_BUTTON_SCRIPT_ID,
				$this->context['url'] . 'dist/realm_button.js',
				$realm_button_deps_and_version['dependencies'],
				$this->context['version']
			);
            wp_enqueue_style(
                AssetManager::REALM_BUTTON_STYLES_ID,
                $this->context['url'] . 'dist/realm_button.css',
                [],
                $this->context['version']
            );
		});
	}
}
