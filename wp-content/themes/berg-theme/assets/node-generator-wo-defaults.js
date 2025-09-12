/* TODO: DEPRECATED, remove this file */
const fs = require('fs');
const path = require('path');
const sassMapToJson = require('sass-maps-to-json');

const coreFiles = {};
const themeFiles = {};
var coreFilesSCSS = '';
var themeFilesSCSS = '';

const getAllScssFiles = (dir) => {
	if (!fs.existsSync(dir) || dir.includes('.git')) return [];
	return fs.readdirSync(dir).reduce((files, file) => {
		const name = path.join(dir, file);
		var scss = file.match(/^bs-+[-.\w]+\.scss$/i);
		if (scss && Array.isArray(scss)) {
			let filePathRelative = path
				.relative(path.resolve(__dirname, `scss/`), name)
				.replace(/\\/g, '/');

			if (file.split('---').length > 1) {
				// get core files
				// let _key = file.split("---")[0];
				// coreFiles[_key] = coreFiles[_key] || [];
				// coreFiles[_key].push(file.replace(/\.scss$/i, ""));
				// coreFilesSCSS += `@import "${filePathRelative.replace(
				//   /\\.scss$/i,
				//   ""
				// )}";\n`;
			} else {
				// theme file
				let _key = file.split('--')[0];
				themeFiles[_key] = themeFiles[_key] || [];
				themeFiles[_key].push(file.replace(/\.scss$/i, ''));
				themeFilesSCSS += `@import "${filePathRelative.replace(
					/\\.scss$/i,
					''
				)}";\n`;
			}
		} else if (file == '_color-palette.scss') {
			sassMapToJson({
				src: name,
				dest: './json/color-palette.json',
			});
		}

		const isDirectory = fs.statSync(name).isDirectory();
		return isDirectory
			? [...files, ...getAllScssFiles(name)]
			: [...files, name];
	}, []);
};

const writeAllintoFiles = (filename, data) => {
	fs.writeFile(path.resolve(__dirname, `${filename}`), data, function (err) {
		if (err) {
			return console.log(err);
		}
		console.log(`The ${filename} file was saved!`);
	});
};

function themeCssClasstListBuilder() {
	getAllScssFiles(path.resolve(__dirname, '../../../plugins/berg/src/block'));
	getAllScssFiles(path.resolve(__dirname, 'scss'));

	// JSON
	writeAllintoFiles('json/core-components.json', JSON.stringify(coreFiles));
	writeAllintoFiles(
		'json/core-components--theme.json',
		JSON.stringify(themeFiles)
	);

	// SCSS
	writeAllintoFiles('scss/_core-components.scss', coreFilesSCSS);
	writeAllintoFiles('scss/_core-components--theme.scss', themeFilesSCSS);

	console.log('Done!');
}
themeCssClasstListBuilder();
