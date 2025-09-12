/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';


export const name = 'e25m-custom/read-time'

export const settings = {
	title: __( 'Read Time', 'read-time' ),
	description: __(
		'Fetches and displays the post read time of the current page or post.',
		'read-time'
	),
	icon: 'clock',
	category: 'widgets',
	supports: {
		// Removes support for an HTML mode.
		html: false,
	},

	attributes: '',

	edit: Edit,
	save:save,
}
