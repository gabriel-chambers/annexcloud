const fs = require('fs');
const path = require('path');
const sassMapToJson = require('sass-maps-to-json');
const glob = require('glob');
const coreFiles = {};
const themeFiles = {};
const realmCoreFiles = {};
const realmThemeFiles = {};
const coreClasses = {};
const themeClasses = {};
let coreFilesSCSS = '';
let realmCoreFilesSCSS = '';
let themeFilesSCSS = '';
let realmThemeFilesSCSS = '';
const getAllScssFiles = dir => {
  if (!fs.existsSync(dir) || dir.includes('.git')) return [];
  return fs.readdirSync(dir).reduce((files, file) => {
    const name = path.join(dir, file);
    let scss = file.match(/^bs-{1,3}[-.\w]+\.scss$/i);
    if (scss && Array.isArray(scss)) {
      let filePathRelative = path.relative(path.resolve(__dirname, `scss/`), name).replace(/\\/g, '/');
      if (file.split('---').length > 1) {
        // get core files
        let _key = file.split('---')[0];
        coreFiles[_key] = coreFiles[_key] || [];
        coreFiles[_key].push(file.replace(/\.scss$/i, ''));
        coreFilesSCSS += `@import "${filePathRelative.replace(/\\.scss$/i, '')}";\n`;
      } else {
        // theme file
        let _key = file.split('--')[0];
        themeFiles[_key] = themeFiles[_key] || [];
        themeFiles[_key].push(file.replace(/\.scss$/i, ''));
        themeFilesSCSS += `@import "${filePathRelative.replace(/\\.scss$/i, '')}";\n`;
      }
    } else if (file == '_color-palette.scss') {
      sassMapToJson({
        src: name,
        dest: './json/color-palette.json',
      });
    }
    const isDirectory = fs.statSync(name).isDirectory();
    if (isDirectory && !name.endsWith('.original')) {
      return [...files, ...getAllScssFiles(name)];
    } else {
      return !isDirectory ? [...files, name] : files;
    }
  }, []);
};

// Realm plugin classes and styles generator
const realmClassGenerator = dir => {
  if (!fs.existsSync(dir) || dir.includes('.git')) return;
  return fs.readdirSync(dir).reduce((files, file) => {
    const name = path.join(dir, file);
    if (!name.includes('.js') && !name.includes('.gitignore')) {
      const scss = glob.sync('**/{r,r2}-*.scss', {
        cwd: name,
      });
      if (scss && Array.isArray(scss)) {
        if (typeof scss[0] != 'undefined') {
          scss.forEach(scssFile => {
            let filePathRelative = path.relative(path.resolve('scss/'), `${name}/${scssFile}`).replace(/\\/g, '/');
            let fileName = path.parse(filePathRelative).name;
            if (fileName.split('---').length > 1) {
              // get core files
              let _key = fileName.split('---')[0];
              realmCoreFiles[_key] = coreFiles[_key] || [];
              realmCoreFiles[_key].push(fileName.replace(/\.scss$/i, ''));

              realmCoreFilesSCSS += `@import "${filePathRelative.replace(/\\.scss$/i, '')}";\n`;
            } else {
              // theme file
              let _key = fileName.split('--')[0];
              realmThemeFiles[_key] = realmThemeFiles[_key] || [];
              realmThemeFiles[_key].push(fileName.replace(/\.scss$/i, ''));
              realmThemeFilesSCSS += `@import "${filePathRelative.replace(/\\.scss$/i, '')}";\n`;
            }
          });
        }
      }
    }
    const isDirectory = fs.statSync(name).isDirectory();
    if (isDirectory && !name.endsWith('.original')) {
      return [...files, ...getAllScssFiles(name)];
    } else {
      return !isDirectory ? [...files, name] : files;
    }
  }, []);
};
const writeAllintoFiles = (filename, data) => {
  fs.writeFile(path.resolve(`${filename}`), data, function (err) {
    if (err) {
      return console.log(err);
    }
    console.log(`The ${filename} file was saved!`);
  });
};

const argumentExists = key => {
  return process.argv.includes(key);
};

function themeCssClasstListBuilder() {
  getAllScssFiles(path.resolve(__dirname, 'scss'));
  // Commented by Ravisha 2021-12-03 since these will get copied to the child theme
  // realmClassGenerator(
  // 	path.resolve(__dirname, '../../../plugins/realm/src/sections')
  // );
  realmClassGenerator(path.resolve(__dirname, 'scss/realm-custom'));
  // JSON
  writeAllintoFiles('json/realm-core-components.json', JSON.stringify(realmCoreFiles));
  writeAllintoFiles('json/realm-core-components-theme.json', JSON.stringify(realmThemeFiles));
  writeAllintoFiles('json/core-components--theme.json', JSON.stringify(themeFiles));
  // SCSS
  writeAllintoFiles('scss/_core-components--theme.scss', themeFilesSCSS);
  writeAllintoFiles('scss/_realm-core-components.scss', realmCoreFilesSCSS); //realm core scss files
  writeAllintoFiles('scss/_realm-core-components--theme.scss', realmThemeFilesSCSS); //realm core theme scss files

  // Assigning all class values to a single object
  themeClasses['coreComponentsTheme'] = themeFiles;
  themeClasses['realmCoreComponents'] = realmCoreFiles;
  themeClasses['realmCoreComponentsTheme'] = realmThemeFiles;
  // Generating the theme-classes.php file
  const themeClassesFileContent = "<?php \n return json_decode('" + JSON.stringify(themeClasses) + "');";
  writeAllintoFiles('../inc/theme-classes.php', themeClassesFileContent);

  if (argumentExists('--core')) {
    getAllScssFiles(path.resolve(__dirname, '../../../plugins/berg/src/block'));
    // JSON
    writeAllintoFiles('json/core-components.json', JSON.stringify(coreFiles));
    // Assigning all default class values to the classes object
    coreClasses['coreComponents'] = coreFiles;
    // Generating the core-classes.php file
    const coreClassesFileContent = "<?php \n return json_decode('" + JSON.stringify(coreClasses) + "');";
    writeAllintoFiles('../inc/core-classes.php', coreClassesFileContent);
  }
}
themeCssClasstListBuilder();
