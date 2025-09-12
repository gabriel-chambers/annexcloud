import React, { Fragment } from 'react';
import { useState } from '@wordpress/element';
import { Button, Modal } from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';
import RealmGallery from './../realm-gallery';

export default function RealmButton(props) {
	const [modalIsOpen, setModalIsOpen] = useState(false);
	const [holderBlock, setHolderBlock] = useState(null);

	const getExistingHolderBlock = () => {
		let existingHolderBlock;
		if (typeof props.placeholderBlockClientId === 'string') {
			existingHolderBlock = wp.data
				.select('core/block-editor')
				.getBlock(props.placeholderBlockClientId);
		}
		return existingHolderBlock;
	};
	const openModal = () => {
		let existingHolderBlock = getExistingHolderBlock();
		if (existingHolderBlock) {
			setHolderBlock(existingHolderBlock);
			setModalIsOpen(true);
		} else {
			const blockInsertionPoint = wp.data
				.select('core/block-editor')
				.getBlockInsertionPoint();
			wp.data
				.dispatch('core/block-editor')
				.insertBlock(
					blockInsertionPoint,
					blockInsertionPoint.index,
					blockInsertionPoint.rootClientId
				);
			const holderBlockInstance = createBlock(
				'e25m-realm/section-holder'
			);
			wp.data
				.dispatch('core/block-editor')
				.insertBlock(
					holderBlockInstance,
					blockInsertionPoint.index,
					blockInsertionPoint.rootClientId
				)
				.then((response) => {
					const insertedHolderBlock = response
						? response.blocks.shift()
						: holderBlockInstance;
					setHolderBlock(insertedHolderBlock);
					setModalIsOpen(true);
				});
		}
	};
	const closeModal = () => {
		new Promise((resolve) => {
			let existingHolderBlock = getExistingHolderBlock();
			if (holderBlock && holderBlock.clientId && !existingHolderBlock) {
				const holderBlockInstance = wp.data
					.select('core/block-editor')
					.getBlock(holderBlock.clientId);
				if (holderBlockInstance) {
					return wp.data
						.dispatch('core/block-editor')
						.removeBlock(holderBlockInstance.clientId)
						.then(resolve);
				}
			}
			resolve();
		}).then(() => {
			setModalIsOpen(false);
		});
	};

	return (
		<Fragment>
			<Button
				text={props.isHeader ? 'Realm' : 'Open Gallery'}
				icon={props.isHeader ? 'editor-kitchensink' : undefined}
				onClick={openModal}
				isPrimary={!props.isHeader}
			/>
			{modalIsOpen && (
				<Modal onRequestClose={closeModal} title="Realm Sections">
					<RealmGallery
						closeModal={closeModal}
						holderBlock={holderBlock}
					/>
				</Modal>
			)}
		</Fragment>
	);
}
