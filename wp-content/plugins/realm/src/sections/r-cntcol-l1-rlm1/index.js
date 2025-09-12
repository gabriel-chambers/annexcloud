/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './scss/style.scss';
import './scss/editor.scss';
/**
 * Internal dependencies
 */

/* eslint-disable-next-line import/no-unresolved */
import Edit from '@common/class-selector/edit';
import save from './save';

import blockSettings from './block.json';

export const name = 'e25m-realm/r-cntcol-l1-rlm1';
export const settings = {
	...blockSettings,
	edit: Edit,
	save,
};
