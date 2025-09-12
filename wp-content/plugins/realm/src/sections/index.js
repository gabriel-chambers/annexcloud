import {
	getCategories,
	setCategories,
	registerBlockCollection,
	getBlockType,
	registerBlockType,
} from '@wordpress/blocks';

if (typeof registerBlockCollection !== 'undefined') {
	registerBlockCollection('e25m-realm', {
		title: 'Realm',
		icon: 'book-alt',
	});
} else {
	setCategories([
		...getCategories(),
		{
			slug: 'realm',
			title: 'Realm',
			icon: 'editor-kitchensink',
		},
	]);
}

const registerModulesRecursively = (r) => {
	r.keys().forEach((key) => {
		const { name, settings } = r(key);
		try {
			const registerBlock = (blockName, blockSettings = {}) => {
				if (
					getBlockType(blockName) ||
					blockName.indexOf('block-template') >= 0
				) {
					return;
				}
				const blockSettingsObj = {
					...blockSettings,
					category: blockSettings.category
						? blockSettings.category
						: 'realm',
				};
				registerBlockType(blockName, blockSettingsObj);
				return blockSettingsObj;
			};
			return name && settings && registerBlock(name, settings);
		} catch (error) {
			console.error(`Could not register ${name} Realm sections`); // eslint-disable-line
		}
	});
};

registerModulesRecursively(require.context('./', true, /index\.js$/));
