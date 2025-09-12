import domReady from '@wordpress/dom-ready';
import './placeholder-block';
import RealmButton from './realm-button';

// eslint-disable-next-line import/no-unresolved
import 'SectionInserter/scss/styles.scss';

const buttonWrapper = document.createElement('div');
buttonWrapper.classList.add('realm-insert-library-button__wrapper');
// eslint-disable-next-line no-undef
const reactRoot = ReactDOM.createRoot(buttonWrapper);
reactRoot.render(<RealmButton isHeader />);

domReady(() => {
	let timeout = null;
	const unsubscribe = wp.data.subscribe(() => {
		const toolbar = document.querySelector('.edit-post-header-toolbar');
		if (!toolbar) return;

		if (
			!toolbar.querySelector(
				'.realm-insert-library-button__wrapper > button'
			) &&
			!timeout
		) {
			toolbar.append(buttonWrapper);
			timeout = setTimeout(() => {
				if (
					document.querySelector(
						'.realm-insert-library-button__wrapper > button'
					)
				) {
					unsubscribe();
				}
				timeout = null;
			}, 0);
		}
	});
});
