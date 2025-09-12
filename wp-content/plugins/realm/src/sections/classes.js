const bergThemeFolderName =
	(typeof BERG_RUNTIME_THEME_FOLDER_NAME !== 'undefined'
		? BERG_RUNTIME_THEME_FOLDER_NAME // eslint-disable-line no-undef
		: undefined) || 'berg-theme-child';
const coreComponentsRealm = require(`../../../../themes/${bergThemeFolderName}/assets/json/realm-core-components.json`);
const coreComponentsTheme = require(`../../../../themes/${bergThemeFolderName}/assets/json/realm-core-components-theme.json`);

module.exports = {
	coreComponentsRealm,
	coreComponentsTheme,
};
