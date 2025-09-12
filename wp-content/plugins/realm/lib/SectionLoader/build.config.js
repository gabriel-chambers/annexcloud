const path = require('path');
// const { globSync } = require('glob');

module.exports = {
	entrypoints: (() => {
		const realm = path.resolve(__dirname, '../../src/sections/index.js');
		// Removed realm_fe on 21/11/30 by Ravisha in favour of
		// building front end scripts via child theme
		// const globOptions = {
		// 	cwd: path.resolve(__dirname, '../../src/sections'),
		// };
		// const realmFe = globSync('!(block-template)**/js/*.js', globOptions)
		// 	.map((x) => path.resolve(__dirname, '../../src/sections', x));
		return { realm };
	})(),
	cacheGroups: {
		realm_vendors: {
			test: /node_modules/,
			name: 'realm_vendors',
			priority: 10,
			chunks: (chunk) => chunk.name === 'realm',
			enforce: true,
		},
		realm_fe_vendors: {
			test: /node_modules/,
			name: 'realm_fe_vendors',
			priority: 10,
			chunks: (chunk) => chunk.name === 'realm_fe',
			enforce: true,
		},
	},
};
