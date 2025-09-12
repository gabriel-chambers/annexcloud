/*eslint-disable-next-line  import/no-unresolved*/
import edit from '@common/class-selector/edit';
import save from './save';
import blockSettings from './block.json';

import './scss/style.scss';
import './scss/editor.scss';

export const name = 'e25m-realm/r-crdstk-l3-anxcld2';
export const settings = {
	...blockSettings,
	edit,
	save,
};
