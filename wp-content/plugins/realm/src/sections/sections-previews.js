let previews = [];
// eslint-disable-next-line no-undef
if (REALM_SECTION_PREVIEWS) {
	try {
		const sectionPreviews =
			typeof REALM_SECTION_PREVIEWS === 'string' // eslint-disable-line no-nested-ternary
				? JSON.parse(REALM_SECTION_PREVIEWS) // eslint-disable-line no-undef
				: typeof REALM_SECTION_PREVIEWS !== 'undefined' && // eslint-disable-line no-undef
				  Array.isArray(REALM_SECTION_PREVIEWS) // eslint-disable-line no-undef
				? REALM_SECTION_PREVIEWS // eslint-disable-line no-undef
				: [];
		if (Array.isArray(sectionPreviews)) {
			previews = sectionPreviews;
		}
	} catch (err) {
		if (process.env.NODE_END !== 'production') console.error(err); // eslint-disable-line no-console
	}
}
export default previews;
