/*eslint-disable-next-line  import/no-unresolved*/
import edit from '@common/class-selector/edit';

import save from './save';
import './scss/editor.scss';
import './scss/style.scss';

import blockSettings from './block.json';

export const name = 'e25m-realm/r-blbslr-l4-rlm1';
export const settings = {
	...blockSettings,
	edit,
	save,
};
