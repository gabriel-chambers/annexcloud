const path = require('path');
const { globSync } = require('glob');

module.exports = {
	entrypoints: (() => {
		const realmButton = globSync('index.js', {
			cwd: path.resolve(__dirname, 'js'),
		}).map((x) => path.resolve(__dirname, 'js', x));
		return { realm_button: realmButton };
	})(),
	cacheGroups: {
		realm_button_vendors: {
			test: /node_modules/,
			name: 'realm_button_vendors',
			priority: 10,
			chunks: (chunk) => chunk.name === 'realm_button',
			enforce: true,
		},
	},
	aliases: {
		SectionInserter: path.resolve(__dirname),
	},
};
