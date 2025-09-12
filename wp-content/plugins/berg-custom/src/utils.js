const sha1 = require("js-sha1");

export const setUniqueIdAttribute = (
	props,
	attributeName,
	prefix = "",
	suffix = ""
) => {
	const { setAttributes, attributes, name } = props;
	let currentAttributeValue = attributes[attributeName].toString();
	if (typeof currentAttributeValue === "undefined") return 0;

	if (typeof prefix !== "undefined") {
		currentAttributeValue = currentAttributeValue.replace(prefix, "");
	}

	if (typeof suffix !== "undefined") {
		currentAttributeValue = currentAttributeValue.replace(suffix, "");
	}

	//Removing the unique id (Ex: sectionBlockId) and adding the blockName attribute to attributes object
	const { [attributeName]: remove, ...newAttributesObject } = {
		...attributes,
		blockName: name,
	};
	// Converting the attributes object into a string
	var attributesString = JSON.stringify(newAttributesObject);
	//Generating a new hashed id using all the attribute values
	var hashHex = sha1.hex(attributesString);
	// Updating the id if changed
	if (
		currentAttributeValue == null ||
		currentAttributeValue == "0" ||
		currentAttributeValue != hashHex
	) {
		setAttributes({
			[attributeName]: `${prefix}${hashHex}${suffix}`,
		});
	}
};
