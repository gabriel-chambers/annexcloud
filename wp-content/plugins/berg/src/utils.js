const sha1 = require("js-sha1");

export const setUniqueIdAttribute = (
  props,
  attributeName,
  prefix = "",
  suffix = "",
  excludeAttrs = [],
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
  if (Array.isArray(excludeAttrs)) {
    excludeAttrs.forEach(attrName => {
      if (newAttributesObject[attrName]) {
        delete newAttributesObject[attrName];
      }
    });
  }
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

export const addLockAttributesSupport = (attributes) => {
  return {
    ...attributes,
    lockAttributes: {
      type: 'array',
      default: []
    }
  };
};

export const renderIfNotLocked = ({ attributes }, attribute, element) => {
  const { lockAttributes } = attributes;
  let isLocked = false;
  if (!Array.isArray(lockAttributes)) {
    isLocked = true;
  } else if (Array.isArray(attribute)) {
    isLocked = lodash.intersection(lockAttributes, attribute).length > 0;
  } else if (typeof attribute === 'string') {
    isLocked = lockAttributes.indexOf(attribute) !== -1;
  }
  return !isLocked ? element : null;
};
