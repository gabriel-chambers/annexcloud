import { SelectControl } from "@wordpress/components";

//Tag Selector
const TagSelector = (props) => {
	const { label, value, onChange } = props;
	const dropdownValues = ["h1", "h2", "h3", "h4", "h5", "h6", "p", "span"];
	let dropdownOptions = [];
	dropdownValues.forEach((element) =>
		dropdownOptions.push({
			value: element,
			label: element,
		})
	);
	return (
		<>
			<SelectControl
				label={label}
				value={value}
				options={dropdownOptions}
				onChange={onChange}
			/>
		</>
	);
};

export default TagSelector;
