/* TODO: DEPRECATED, remove this file */
const fs = require('fs');
const path = require('path');
const sassMapToJson = require('sass-maps-to-json');
const glob = require('glob');
const coreFiles = {};
const themeFiles = {};
const realmCoreFiles = {};
const realmThemeFiles = {};
var coreFilesSCSS = '';
var realmCoreFilesSCSS = '';
var themeFilesSCSS = '';
var realmThemeFilesSCSS = '';
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
				let _key = file.split('---')[0];
				coreFiles[_key] = coreFiles[_key] || [];
				coreFiles[_key].push(file.replace(/\.scss$/i, ''));
				// coreFiles.push(file.replace(/\.scss$/i, ""));
				coreFilesSCSS += `@import "${filePathRelative.replace(
					/\\.scss$/i,
					''
				)}";\n`;
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

// Realm plugin classes and styles generator
const realmClassGenerator = (dir) => {
	if (!fs.existsSync(dir) || dir.includes('.git')) return;
	return fs.readdirSync(dir).reduce((files, file) => {
		const name = path.join(dir, file);
		if (!name.includes('.js') && !name.includes('.gitignore')) {
			const scss = glob.sync('**/r-*.scss', {
				cwd: name,
			});
			if (scss && Array.isArray(scss)) {
				if (typeof scss[0] != 'undefined') {
					scss.forEach((scssFile) => {
						let filePathRelative = path
							.relative(
								path.resolve(__dirname, `scss/`),
								`${name}/${scssFile}`
							)
							.replace(/\\/g, '/');
						let fileName = path.parse(filePathRelative).name;
						if (fileName.split('---').length > 1) {
							// get core files
							let _key = fileName.split('---')[0];
							realmCoreFiles[_key] = coreFiles[_key] || [];
							realmCoreFiles[_key].push(
								fileName.replace(/\.scss$/i, '')
							);

							realmCoreFilesSCSS += `@import "${filePathRelative.replace(
								/\\.scss$/i,
								''
							)}";\n`;
						} else {
							// theme file
							let _key = fileName.split('--')[0];
							realmThemeFiles[_key] = realmThemeFiles[_key] || [];
							realmThemeFiles[_key].push(
								fileName.replace(/\.scss$/i, '')
							);
							realmThemeFilesSCSS += `@import "${filePathRelative.replace(
								/\\.scss$/i,
								''
							)}";\n`;
						}
					});
				}
			}
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
	// Commented by Ravisha 2021-12-03 since these will get copied to the child theme
	// realmClassGenerator(
	// 	path.resolve(__dirname, '../../../plugins/realm/src/sections')
	// );
	realmClassGenerator(path.resolve(__dirname, 'scss/realm-custom'));
	// JSON
	writeAllintoFiles('json/core-components.json', JSON.stringify(coreFiles));
	writeAllintoFiles(
		'json/realm-core-components.json',
		JSON.stringify(realmCoreFiles)
	);
	writeAllintoFiles(
		'json/realm-core-components-theme.json',
		JSON.stringify(realmThemeFiles)
	);
	writeAllintoFiles(
		'json/core-components--theme.json',
		JSON.stringify(themeFiles)
	);
	// SCSS
	writeAllintoFiles('scss/_core-components.scss', coreFilesSCSS);
	writeAllintoFiles('scss/_core-components--theme.scss', themeFilesSCSS);
	writeAllintoFiles('scss/_realm-core-components.scss', realmCoreFilesSCSS); //realm core scss files
	writeAllintoFiles(
		'scss/_realm-core-components--theme.scss',
		realmThemeFilesSCSS
	); //realm core theme scss files
	console.log('Done!');
}
themeCssClasstListBuilder();
