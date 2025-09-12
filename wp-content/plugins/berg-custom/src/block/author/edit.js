import { __ } from '@wordpress/i18n';
import {
    Disabled,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
} from "@wordpress/components";
import {InspectorControls} from "@wordpress/block-editor";
import ServerSideRender from "@wordpress/server-side-render";

const Edit = ( props ) => {

	const { attributes, setAttributes } = props;
	const { selectedTemplate } = attributes;
	const innerElements = [];

	const setSelectedTemplate = (selectedTemplateVal) => {
		setAttributes({
			selectedTemplate: selectedTemplateVal,
		});
	};

	const setPrefix = (prefixVal) => {
		setAttributes({
			prefix: prefixVal,
		});
	};

	if (selectedTemplate == "basic") {
		innerElements.push(
			<PanelRow>
				<TextControl
					label="Prefix"
					type="text"
					value={attributes.prefix}
					onChange={setPrefix}
				/>
			</PanelRow>,
		);
	}

	return [
		<InspectorControls>
            <PanelBody title={__("General Settings", "")}>
				<PanelRow>
					<SelectControl
						label="Template"
						value={attributes.selectedTemplate}
						options={[
							{ label: "Basic", value: "basic" },
							{ label: "Bio", value: "bio" },
						]}
						onChange={setSelectedTemplate}
					/>
				</PanelRow>
				{innerElements}
            </PanelBody>
        </InspectorControls>
		,
		<Disabled>
            <ServerSideRender block="e25m-custom/author" attributes={attributes}/>
        </Disabled>
	];
}

export default Edit;