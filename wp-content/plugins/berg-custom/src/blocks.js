/**
 * This is the file that Webpack is compiling into editor_blocks.js
 */

/** 
 * Internal dependencies
 */

/**
 * External dependencies
 */
import registerBlock from "~bergcustom/register-block";
import { supportsBlockCollections } from "~bergcustom/collections";
//import { i18n } from 'berg'

/**
 * WordPress dependencies
 */
import {
	getCategories,
	setCategories,
	registerBlockCollection,
} from "@wordpress/blocks";
import { __ } from "@wordpress/i18n";

// Register our block collection or category (WP <= 5.3).
if (supportsBlockCollections()) {
	registerBlockCollection("e25m-custom", {
		title: "Berg Custom",
		icon: "book-alt",
	});
} else {
	setCategories([
		...getCategories(),
		{
			slug: "berg-custom",
			title: "Berg Custom",
			icon: "book-alt",
		},
	]);
}

// Import all index.js and register all the blocks found (if name & settings are exported by the script)
const importAllAndRegister = (r) => {
	r.keys().forEach((key) => {
		const { name, settings } = r(key);
		try {
			// console.log(name, settings)
			return name && settings && registerBlock(name, settings);
		} catch (error) {
			console.error(`Could not register ${name} block`); // eslint-disable-line
		}
	});
};

importAllAndRegister(require.context("./block", true, /index\.js$/));
