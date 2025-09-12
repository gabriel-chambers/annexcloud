import React, { useEffect } from 'react';
import { Button } from '@wordpress/components';
import { createBlock, parse, getBlockAttributes } from '@wordpress/blocks';
import { merge, cloneDeep } from 'lodash';
import { useState } from '@wordpress/element';

/**
 * Given an array of InnerBlocks templates or Block Objects,
 * returns an array of created Blocks from them.
 * @param {Array} innerBlocksOrTemplate Nested blocks or InnerBlocks templates.
 * @returns {Object[]} Array of Block objects.
 */
function createBlocksFromTemplate(innerBlocksOrTemplate = []) {
	return innerBlocksOrTemplate.map((innerBlock) => {
		const innerBlockTemplate = Array.isArray(innerBlock)
			? innerBlock
			: [innerBlock.name, innerBlock.attributes, innerBlock.innerBlocks];

		const [name, attributes, innerBlocks = []] = innerBlockTemplate;

		return createBlock(
			name,
			merge(cloneDeep(getBlockAttributes(name, '')), attributes),
			createBlocksFromTemplate(innerBlocks)
		);
	});
}

const InitIndicator = () => {
	return (
		<div className="init-wrapper">
			<div className="loader">
				<span className="spin-icon"></span>
			</div>
		</div>
	);
};

export default function Section({
	handleCloseModal,
	section,
	holderBlock,
	onPreview,
}) {
	const [sectionInitialized, setSectionInitialized] = useState(false);

	useEffect(() => {
		if (sectionInitialized === true) {
			insertSection();
		}
	}, [sectionInitialized]);

	const insertSection = () => {
		if (holderBlock && holderBlock.clientId) {
			const blockContent =
				typeof section.content === 'object'
					? section.content
					: parse(section.content).shift();
			const block = createBlock(
				blockContent.blockName || blockContent.name,
				{
					...(blockContent.attributes || {}),
					...(blockContent.attrs || {}),
				},
				createBlocksFromTemplate(blockContent.innerBlocks)
			);
			wp.data
				.dispatch('core/block-editor')
				.replaceBlock(holderBlock.clientId, block)
				.then(() => {
					handleCloseModal();
				});
		}
	};

	return (
		<div className="section">
			<div className="section__sku">
				<span>
					{section?.content?.blockName?.replace('e25m-realm/', '')}
				</span>
				<button
					onClick={() => onPreview(section)}
					className="dashicons dashicons-images-alt2"
				></button>
			</div>

			<Button type="button" onClick={() => setSectionInitialized(true)}>
				<div className="section__image-wrapper">
					<img
						className="section__image"
						src={section.image || section?.images[0]}
						alt={section.name}
					/>
				</div>
				<span className="section__title">{section.name}</span>
				{section.tags && section.tags.length && (
					<div className="tag-wrapper">
						{section.tags.map((tag, i) => (
							<code key={i} className="tag-wrapper__tag">
								{tag}
							</code>
						))}
					</div>
				)}
				{sectionInitialized && <InitIndicator />}
			</Button>
			{sectionInitialized && <InitIndicator />}
		</div>
	);
}
