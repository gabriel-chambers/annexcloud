const fs = require('fs');
const path = require('path');

if (fs.existsSync(path.resolve(__dirname, 'node_modules')) &&
	!fs.existsSync(path.resolve(__dirname, 'node_modules', "webpack"))
) {
	console.error("\033[0;31mSorry, Build commands doesn't work in this version of the plugin.\033[0m")
	process.exit(1);
}
