const fs = require('fs');
const { globSync } = require('glob');
const husky = require('husky');
const processPath = process.cwd();

if (
	fs.existsSync(`${processPath}/.git`) &&
	fs.lstatSync(`${processPath}/.git`).isDirectory()
) {
	husky.install();
}

// Install husky in each sections
globSync(
	'*',
	{
		cwd: `${processPath}/src/sections`,
	},
	(err, matches) => {
		if (err) throw err;
		matches.forEach((match) => {
			const sectionPath = `${processPath}/src/sections/${match}`;
			if (
				fs.existsSync(`${sectionPath}/.git`) &&
				fs.lstatSync(`${sectionPath}/.git`).isDirectory()
			) {
				process.chdir(sectionPath);
				husky.install(`${sectionPath}/.husky`);
				const wpScriptsPath = 'npx wp-scripts';
				husky.set(
					`${sectionPath}/.husky/pre-commit`,
					`${wpScriptsPath} lint-js --fix ./**/*.js && ${wpScriptsPath} lint-style --fix ./**/*.scss \r\ngit add -u`
				);
			}
		});
	}
);
