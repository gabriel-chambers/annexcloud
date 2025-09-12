import { useBlockProps } from '@wordpress/block-editor';
import { Icon } from '@wordpress/components';
import RealmButton from './../realm-button';

import './editor.scss';

export default function Edit({ clientId }) {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<span>
				<Icon icon="editor-kitchensink" />
				Realm Gallery
			</span>
			<RealmButton placeholderBlockClientId={clientId} />
		</div>
	);
}
