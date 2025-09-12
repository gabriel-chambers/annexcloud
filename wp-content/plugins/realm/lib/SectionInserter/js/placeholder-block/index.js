import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';

registerBlockType('e25m-realm/section-holder', {
	apiVersion: 2,
	title: 'Realm Section Holder',
	description: 'Root holder element for Realm section',
	icon: 'editor-kitchensink',
	supports: {
		html: false,
		customClassName: false,
		inserter: false,
	},
	edit,
});
