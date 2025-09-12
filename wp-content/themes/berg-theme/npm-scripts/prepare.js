const fs = require('fs');
const processPath = process.cwd();
if (fs.existsSync(`${processPath}/.git`)) {
	require('./../assets/node_modules/husky').install('assets/.husky');
}
