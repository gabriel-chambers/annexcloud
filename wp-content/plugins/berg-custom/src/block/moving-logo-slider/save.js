/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
import {InnerBlocks} from '@wordpress/block-editor';

const save = (props) => {

	const {attributes} = props;

	return <InnerBlocks.Content {...attributes} />;
}

export default save;