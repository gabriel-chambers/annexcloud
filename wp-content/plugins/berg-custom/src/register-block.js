/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { getBlockType, registerBlockType } from "@wordpress/blocks";

const registerBlock = (name, settings = {}) => {
	if (getBlockType(name)) {
		return;
	}

	const blockSettings = {
		...settings,
		category: settings.category ? settings.category : "berg",
	};

	// Register the block.
	registerBlockType(name, blockSettings);

	return blockSettings;
};

export default registerBlock;
