/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { Panel, PanelBody } from '@wordpress/components';
import { withSelect } from '@wordpress/data';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */

import React from 'react';
import Select from 'react-select';

const { useEffect, useState } = wp.element;
const { dispatch, select } = wp.data;

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
const Edit = (props) => {
	const { blockClasses } = props;
	const { realmCoreComponents = {}, realmCoreComponentsTheme = {} } =
		blockClasses || {};

	const { clientId, attributes, setAttributes } = props;
	const { modulePrefix, realmClassNames } = attributes;

	const [sectionClasses] = useState([]);

	const changeRealmSectionClass = (val) => {
		setAttributes({
			realmClassNames: val ? val : [],
		});
		updateSectionClass();
	};

	const updateSectionClass = () => {
		const sectionBlock =
			select('core/block-editor').getBlocksByClientId(clientId)[0]
				.innerBlocks[0];
		const sectionBlockNewClassNames = { realmClassNames: realmClassNames };
		dispatch('core/block-editor').updateBlockAttributes(
			sectionBlock.clientId,
			sectionBlockNewClassNames
		);
	};

	useEffect(() => {
		updateSectionClass();
	}, [realmClassNames]);

	useEffect(() => {
		// Set default class
		if (
			realmCoreComponents[modulePrefix] &&
			realmCoreComponents[modulePrefix].length > 0
		) {
			sectionClasses.push({
				value: `${realmCoreComponents[modulePrefix]}`,
				label: 'Default',
			});
		}

		// Set theme classes
		if (
			realmCoreComponentsTheme[modulePrefix] &&
			realmCoreComponentsTheme[modulePrefix].length > 0
		) {
			realmCoreComponentsTheme[modulePrefix].map((className, index) => {
				let classLabel = className
					.replace(modulePrefix + '--', '')
					.split('-');
				classLabel.map((word, index) => {
					classLabel[index] = word[0].toUpperCase() + word.slice(1);
				});
				sectionClasses.push({
					value: className,
					label: classLabel.join(' '),
				});
			});
		}
	}, [blockClasses]);

	return [
		<InspectorControls>
			<PanelBody title={__('Realm Class', '')} initialOpen={true}>
				<Select
					isMulti
					value={realmClassNames}
					name="realmClasses"
					options={sectionClasses}
					onChange={changeRealmSectionClass}
				/>
			</PanelBody>
		</InspectorControls>,
		<InnerBlocks renderAppender={false} />,
	];
};

export default withSelect((select) => {
	const theme = select('core').getCurrentTheme();
	const { block_classes: blockClasses = {} } = theme || {};
	return {
		blockClasses,
	};
})(Edit);
