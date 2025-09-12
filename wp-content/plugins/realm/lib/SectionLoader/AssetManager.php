<?php

namespace E25m\Realm\SectionLoader;

use E25m\Realm\traits\AssetHelper;

/**
 *
 */
class AssetManager
{
	use AssetHelper;

	const REALM_VENDORS_SCRIPT_ID 		= 'e25m-realm-vendors';
	const REALM_FE_VENDORS_SCRIPT_ID 	= 'e25m-realm-fe-vendors';
	const REALM_FE_SCRIPT_ID 			= 'e25m-realm-scripts';
	const REALM_SCRIPT_ID 				= 'e25m-realm';
	const REALM_FE_STYLE_ID 			= 'e25m-realm-styles';
	const REALM_EDITOR_STYLE_ID 		= 'e25m-realm-editor-styles';

	private $context;

	function __construct(array $context)
	{
		$this->context = $context;
	}

	/**
	 * Enqueue all scripts and styles required for Realm
	 *
	 * @return void
	 */
	public function enqueueAssets() : void
	{
		// Javascript requires to control blocks and styles for front end
		add_action('enqueue_block_assets', function () {
			$realm_fe_deps_and_version = $this->getScriptDependenciesAndVersion('realm_fe');
			if (file_exists($this->context['path'] . "dist/realm_fe_vendors.js")) {
				wp_enqueue_script(
					AssetManager::REALM_FE_VENDORS_SCRIPT_ID,
					$this->context['url'] . 'dist/realm_fe_vendors.js'
				);
				if (!in_array(AssetManager::REALM_FE_VENDORS_SCRIPT_ID, $realm_fe_deps_and_version['dependencies'])) {
					$realm_fe_deps_and_version['dependencies'][] = AssetManager::REALM_FE_VENDORS_SCRIPT_ID;
				}
			}

			if (file_exists($this->context['path'] . "dist/realm_fe.js")) {
				wp_enqueue_script(
					AssetManager::REALM_FE_SCRIPT_ID,
					$this->context['url'] . 'dist/realm_fe.js',
					$realm_fe_deps_and_version['dependencies'],
					$this->context['version'],
					true
				);
			}

			wp_enqueue_style(
				AssetManager::REALM_FE_STYLE_ID,
				$this->context['url'] . 'dist/style-realm.css',
				[],
				$this->context['version']
			);
		});

		// Block editor styles and blocks
		add_action('enqueue_block_editor_assets', function () {
			$realm_deps_and_version = $this->getScriptDependenciesAndVersion('realm');
			if (file_exists($this->context['path'] . "dist/realm_vendors.js")) {
				wp_enqueue_script(
					AssetManager::REALM_VENDORS_SCRIPT_ID,
					$this->context['url'] . 'dist/realm_vendors.js'
				);
				if (!in_array(AssetManager::REALM_VENDORS_SCRIPT_ID, $realm_deps_and_version['dependencies'])) {
					$realm_deps_and_version['dependencies'][] = AssetManager::REALM_VENDORS_SCRIPT_ID;
				}
			}
			wp_enqueue_script(
				AssetManager::REALM_SCRIPT_ID,
				$this->context['url'] . 'dist/realm.js',
				$realm_deps_and_version['dependencies'],
				$this->context['version']
			);
			wp_enqueue_style(
				AssetManager::REALM_EDITOR_STYLE_ID,
				$this->context['url'] . 'dist/realm.css',
				[],
				$this->context['version']
			);
		});
	}
}
