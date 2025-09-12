import { InnerBlocks, InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

export default function Edit(props) {
	const { setAttributes, attributes, clientId } = props;
	const {
		numberOfElements,
		minColSize,
		composition,
		compositionTwo,
		enableSlider,
		showSliderToggle,
		autoConversion,
		conversionPoint,
	} = attributes;
	const {
		element,
		wrapper,
		elementProps,
		wrapperProps,
		elementContainer,
		elementContainerProps,
	} = composition;
	const initialBlurbsTemplate = new Array(1).fill([
		elementContainer,
		{ ...elementContainerProps },
		[[element, elementProps]],
	]);
	const template = [
		['e25m/section', {}, [[wrapper, wrapperProps, initialBlurbsTemplate]]],
	];

	const getRowBlock = () => {
		const { innerBlocks } = wp.data
			.select('core/block-editor')
			.getBlock(clientId);
		const rowBlock = innerBlocks.shift();
		return rowBlock;
	};
	const getColumnProps = (num, isSlider) => {
		if (isSlider) {
			return {};
		}
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
		currentComposition,
		isSlider
	) => {
		const sectionBlock = getRowBlock();
		const colProps = getColumnProps(totalCount, isSlider);
		const {
			element: elem,
			wrapper: wrp,
			elementProps: elemProps,
			wrapperProps: wrpProps,
			elementContainer: elemCont,
			elementContainerProps: elemContProps,
		} = currentComposition;
		if (sectionBlock) {
			const sectionBlockClientId = sectionBlock.clientId;
			const newBlocks = [];
			const currentInnerBlocks = sectionBlock.innerBlocks[0].innerBlocks;
			currentInnerBlocks.forEach((val, index) => {
				const bl = wp.blocks.createBlock(elemCont, { ...colProps });
				const blx = wp.blocks.createBlock(
					elemCont,
					{ ...colProps },
					bl.innerBlocks.concat(val.innerBlocks)
				);
				newBlocks.push(blx);
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
				newBlocks.push(blx);
			}

			const rowBlockWithUpdates = wp.blocks.createBlock(
				wrp,
				wrpProps,
				newBlocks
			);
			const withSection = wp.blocks.createBlock('e25m/section', {}, [
				rowBlockWithUpdates,
			]);
			wp.data
				.dispatch('core/block-editor')
				.replaceBlock(sectionBlockClientId, withSection);
		}
	};
	const decrementBlocks = (totalCount, isSlider, currentComposition) => {
		const sectionBlock = getRowBlock();
		const {
			element: elem,
			wrapper: wrp,
			elementProps: elemProps,
			wrapperProps: wrpProps,
			elementContainer: elemCont,
			elementContainerProps: elemContProps,
		} = currentComposition;
		const colConfig = getColumnProps(totalCount, isSlider);
		if (sectionBlock) {
			const sectionBlockClientId = sectionBlock.clientId;
			const currentInnerBlocks = sectionBlock.innerBlocks[0].innerBlocks;
			const newBlocks = [];
			for (let i = 0; i < totalCount; i += 1) {
				const temp = currentInnerBlocks[i];
				const bl = wp.blocks.createBlock(elemCont, { ...colConfig });
				const blx = wp.blocks.createBlock(
					elemCont,
					{ ...colConfig },
					bl.innerBlocks.concat(temp.innerBlocks)
				);
				newBlocks.push(blx);
			}

			const rowBlockWithUpdates = wp.blocks.createBlock(
				wrp,
				wrpProps,
				newBlocks
			);
			const withSection = wp.blocks.createBlock('e25m/section', {}, [
				rowBlockWithUpdates,
			]);
			wp.data
				.dispatch('core/block-editor')
				.replaceBlock(sectionBlockClientId, withSection);
		}
	};
	const handleBlurbCountUpdates = (newValue) => {
		if (newValue > numberOfElements) {
			if (
				autoConversion === true &&
				conversionPoint < parseInt(newValue.toString())
			) {
				incrementBlocks(
					newValue - numberOfElements,
					newValue,
					compositionTwo,
					enableSlider
				);
			} else {
				incrementBlocks(
					newValue - numberOfElements,
					newValue,
					composition,
					enableSlider
				);
			}
		} else {
			if (autoConversion === true) {
				if (conversionPoint < parseInt(newValue.toString())) {
					decrementBlocks(newValue, true, compositionTwo);
				} else {
					decrementBlocks(newValue, false, composition);
				}
			} else {
				decrementBlocks(newValue, enableSlider, composition);
			}
		}
		setAttributes({ numberOfElements: parseInt(newValue.toString()) });
	};
	return [
		<InspectorControls>
			<Panel header="Settings">
				<PanelBody>
					<PanelRow>
						<NumberControl
							isShiftStepEnabled={true}
							value={numberOfElements}
							onChange={handleBlurbCountUpdates}
							min={1}
						/>
					</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>,
		<div className="bs-convertible-section-slider__container">
			<InnerBlocks template={template} orientation="horizontal" />
		</div>,
	];
}
