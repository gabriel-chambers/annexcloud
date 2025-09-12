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
import {
	Panel,
	PanelBody,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';

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
	const {
		modulePrefix,
		realmClassNames,
		numberOfElements,
		minColSize,
		composition,
		compositionTemplate,
	} = attributes;

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

	const replaceParentBlock = (rowBlockWithUpdates, parentBlockClientId) => {
		wp.data
			.dispatch('core/block-editor')
			.replaceBlock(parentBlockClientId, rowBlockWithUpdates);
	};

	const getDynamicRowBlock = () => {
		const { innerBlocks } = wp.data
			.select('core/block-editor')
			.getBlock(clientId);
		const sectionBlock = innerBlocks.shift();
		let dynamicRowBlock = null;
		sectionBlock.innerBlocks.forEach((rowBlock, index) => {
			if (rowBlock.attributes.rowClass === 'rlm-increment-row') {
				dynamicRowBlock = rowBlock;
			}
		});
		return dynamicRowBlock;
	};

	const getColumnProps = (num) => {
		let colSize = Math.floor(12 / num);
		let colSizeMobile = 12;
		colSize = colSize > minColSize ? colSize : minColSize;
		return {
			sizeXl: colSize,
			sizeLg: colSize,
			sizeMd: colSize,
			sizeSm: colSizeMobile,
			sizeXs: colSizeMobile,
			colClassList: [
				'bs-column',
				`col-${colSizeMobile}`,
				`col-xl-${colSize}`,
				`col-lg-${colSize}`,
				`col-md-${colSize}`,
				`col-sm-${colSizeMobile}`,
				`col-xs-${colSizeMobile}`,
			],
		};
	};

	const incrementBlocks = (
		numberToInsert,
		totalCount,
		currentComposition
	) => {
		const dynamicRowBlock = getDynamicRowBlock();
		const colProps = getColumnProps(totalCount);
		const {
			element: elem,
			wrapper: wrp,
			elementProps: elemProps,
			elementContainer: elemCont,
		} = currentComposition;
		if (dynamicRowBlock) {
			const dynamicRowBlockClientId = dynamicRowBlock.clientId;
			const dynamicRowBlockAttributes = dynamicRowBlock.attributes;
			const currentInnerBlocks = dynamicRowBlock.innerBlocks;
			if (
				typeof compositionTemplate === 'object' &&
				compositionTemplate.wrapperType &&
				compositionTemplate.blockTemplate
			) {
				const innerBlocksCopy = [...currentInnerBlocks];
				for (let i = 0; i < numberToInsert; i++) {
					const [blockName, blockAttr, blockInnerBlocks] =
						compositionTemplate.blockTemplate;
					innerBlocksCopy.push(
						wp.blocks.createBlock(
							blockName,
							blockAttr,
							wp.blocks.createBlocksFromInnerBlocksTemplate(
								blockInnerBlocks
							)
						)
					);
				}
				const updatedWrapperBlock = wp.blocks.createBlock(
					wrp,
					dynamicRowBlockAttributes,
					innerBlocksCopy
				);
				replaceParentBlock(
					updatedWrapperBlock,
					dynamicRowBlockClientId
				);
			} else {
				const newInnerBlocks = [];
				currentInnerBlocks.forEach((val, index) => {
					const bl = wp.blocks.createBlock(elemCont, { ...colProps });
					const blx = wp.blocks.createBlock(
						elemCont,
						{ ...colProps },
						bl.innerBlocks.concat(val.innerBlocks)
					);
					newInnerBlocks.push(blx);
				});

				for (let i = 0; i < numberToInsert; i += 1) {
					const bl = wp.blocks.createBlock(elemCont, { ...colProps });
					const blx = wp.blocks.createBlock(
						elemCont,
						{ ...colProps },
						bl.innerBlocks.concat(
							wp.blocks.createBlock(elem, elemProps)
						)
					);
					newInnerBlocks.push(blx);
				}

				const rowBlockWithUpdates = wp.blocks.createBlock(
					wrp,
					dynamicRowBlockAttributes,
					newInnerBlocks
				);
				replaceParentBlock(
					rowBlockWithUpdates,
					dynamicRowBlockClientId
				);
			}
		}
	};

	const decrementBlocks = (totalCount, currentComposition) => {
		const dynamicRowBlock = getDynamicRowBlock();
		const { wrapper: wrp } = currentComposition;
		if (dynamicRowBlock) {
			const dynamicRowBlockClientId = dynamicRowBlock.clientId;
			const dynamicRowBlockAttributes = dynamicRowBlock.attributes;
			const currentInnerBlocks = dynamicRowBlock.innerBlocks;
			const newInnerBlocks = [...currentInnerBlocks];
			while (newInnerBlocks.length > totalCount) {
				newInnerBlocks.pop();
			}
			const rowBlockWithUpdates = wp.blocks.createBlock(
				wrp,
				dynamicRowBlockAttributes,
				newInnerBlocks
			);
			replaceParentBlock(rowBlockWithUpdates, dynamicRowBlockClientId);
		}
	};

	const handleColumnCountUpdates = (newValue) => {
		if (newValue != numberOfElements) {
			if (newValue > numberOfElements) {
				incrementBlocks(
					newValue - numberOfElements,
					newValue,
					composition
				);
			} else {
				decrementBlocks(newValue, composition);
			}
			setAttributes({ numberOfElements: parseInt(newValue.toString()) });
		}
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
			<PanelBody title={__('Increment', '')} initialOpen={true}>
				<NumberControl
					isShiftStepEnabled={true}
					value={numberOfElements}
					onChange={handleColumnCountUpdates}
					min={1}
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
