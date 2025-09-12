/**
 * External dependencies
 */
import { castArray } from 'lodash';
/**
 * WordPress dependencies
 */
import { createElement, memo, useMemo } from "@wordpress/element";
import { useSelect } from '@wordpress/data';
import { BlockList, BlockEditorProvider, store as blockEditorStore } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';

export const LiveBlockPreview = (props) => {
	const {
		onClick
	} = props;
	return createElement("div", {
		tabIndex: 0,
		role: "button",
		onClick: onClick,
		onKeyPress: onClick
	}, createElement(Disabled, null, createElement(BlockList, null)));
}

export const BlockPreview = (props) => {
	const {
		blocks,
		__experimentalOnClick
	} = props;
	const originalSettings = useSelect(select => select(blockEditorStore).getSettings(), []);
	const settings = useMemo(() => {
		const _settings = {
			...originalSettings
		};
		_settings.__experimentalBlockPatterns = [];
		return _settings;
	}, [originalSettings]);
	const renderedBlocks = useMemo(() => castArray(blocks), [blocks]);

	if (!blocks || blocks.length === 0) {
		return null;
	}

	return createElement(BlockEditorProvider, {
		value: renderedBlocks,
		settings: settings
	}, createElement(LiveBlockPreview, {
		onClick: __experimentalOnClick
	}));
}

export default memo(BlockPreview);
