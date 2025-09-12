/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import {__} from '@wordpress/i18n';
import {
	PanelBody,
	PanelRow,
	Button,
	SelectControl,
	TextControl,
} from "@wordpress/components";
import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from "@wordpress/block-editor";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @param {Object} [props]           Properties passed from the editor.
 * @param {string} [props.className] Class name generated for the block.
 *
 * @return {WPElement} Element to render.
 */

const Edit = (props) => {

	const {attributes, setAttributes} = props;

	const {images, direction, speed} = attributes;

	/* Upload Images as logos */
	const ALLOWED_MEDIA_TYPES = ["image"];

	function LogoImageUploader({mediaIDs, onSelect}) {
		return (
			<MediaUploadCheck>
				<MediaUpload
					onSelect={onSelect}
					allowedTypes={ALLOWED_MEDIA_TYPES}
					value={mediaIDs}
					render={({open}) => (
						<div className="logo-button-wrapper">
							<Button onClick={open} className="button button-large">
								{mediaIDs.length < 1 ? "Upload / Select Logos" : "Edit Logos"}
							</Button>
						</div>
					)}
					gallery
					multiple
				/>
			</MediaUploadCheck>
		);
	}

	/* On gallery Images select function */
	const onSelect = (items) => {
		setAttributes({
			images: items.map((item) => {
				return {
					mediaID: parseInt(item.id, 10),
					mediaURL: item.url,
					alt: item.alt,
					title: item.caption,
				};
			}),
		});
	};

	//set direction
	const setDirection = (val) => {
		setAttributes({
			direction: val,
		});
	};

	//set speed
	const setSpeed = (val) => {
		setAttributes({
			speed: val,
		});
	};

	return [
		<InspectorControls>
			<PanelBody title={__("General Settings", "")}>
				<PanelRow>
					<SelectControl
						label="Moving Direction"
						value={direction}
						options={[
							{label: "Left", value: "left"},
							{label: "Right", value: "right"}
						]}
						onChange={setDirection}
					/>
				</PanelRow>
				<PanelRow>
					<TextControl
						label="Moving Speed (milliseconds)"
						type="text"
						value={speed}
						onChange={setSpeed}
					/>
				</PanelRow>
			</PanelBody>
		</InspectorControls>
		,
		<div className={`moving-log-slider-wrapper-wrap`}>
			{images.length >= 1 ? (
				<div className="clients-slider-wrapper" data-direction={direction}
						data-speed={speed}>
					<div className={`clients-wrap`}>
						<ul className={`clients-list`}>
							{images.map((item) => (
								<li>
									<div>
										<img
											src={item.thumbnail || item.mediaURL}
											alt={item.alt ? item.alt : " "}
											title={item.caption ? item.caption : " "}
										/>
									</div>
								</li>
							))}
						</ul>
					</div>
				</div>
			) : (
				<p>
					Please add logos to Logo slider <br/>
					<small>
						Better if you can add Same dimension for all Logo Images
					</small>
				</p>
			)}
			<LogoImageUploader
				mediaIDs={images.map((item) => item.mediaID)}
				onSelect={onSelect}
			/>
		</div>
	];
}

export default Edit;