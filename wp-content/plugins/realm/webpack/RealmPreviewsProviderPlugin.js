const DefinePlugin = require('webpack').DefinePlugin;
const { globSync } = require('glob');
const path = require('path');

class RealmPreviewsProvider {
	constructor({ realmSectionsPath }) {
		this.realmSectionsPath =
			realmSectionsPath || path.resolve(__dirname, '../src/sections');
	}

	apply(compiler) {
		const realmPreviewFiles = globSync('!(block-template)**/template.js', {
			cwd: this.realmSectionsPath,
		});
		let realmPreviews = [];
		realmPreviewFiles.forEach((filePath) => {
			const fullPath = path.resolve(this.realmSectionsPath, filePath);
			const sectionTemplate = require(fullPath);
			if (sectionTemplate && Array.isArray(sectionTemplate)) {
				realmPreviews = realmPreviews.concat(sectionTemplate); // when template file contains more than one templates
			} else if (sectionTemplate && typeof sectionTemplate === 'object') {
				realmPreviews.push(sectionTemplate);
			}
		});
		const previewString = JSON.stringify(realmPreviews);
		new DefinePlugin({
			REALM_SECTION_PREVIEWS: previewString,
		}).apply(compiler);
	}
}

module.exports = RealmPreviewsProvider;
