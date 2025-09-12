const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
// const BergRuntimeVars = require('./webpack/BergRuntimeVarsPlugin');
const RealmPreviewsProvider = require('./webpack/RealmPreviewsProviderPlugin');
const TerserPlugin = require('terser-webpack-plugin');

const sectionLoaderConfigs = require('./lib/SectionLoader/build.config');
const sectionInserterConfigs = require('./lib/SectionInserter/build.config');

const isProduction = process.env.NODE_ENV === 'production';

// Turn off parallel option of TerserPlugin
defaultConfig.optimization.minimizer = [
	new TerserPlugin({
		parallel: false,
		terserOptions: {
			sourceMap: !isProduction,
			output: {
				comments: /translators:/i,
			},
			compress: {
				passes: 2,
			},
			mangle: {
				reserved: ['__', '_n', '_nx', '_x'],
			},
		},
		extractComments: false,
	}),
];

module.exports = {
	...defaultConfig,
	devtool: 'source-map', // generate source maps in both dev and prod for easy debugging
	output: {
		...defaultConfig.output,
		path: path.resolve(__dirname, 'dist'), // output to dist folder
	},
	entry: () =>
		new Promise((resolve) =>
			resolve({
				...sectionLoaderConfigs.entrypoints,
				...sectionInserterConfigs.entrypoints,
			})
		),
	resolve: {
		...defaultConfig.resolve,
		alias: {
			...defaultConfig.resolve.alias,
			...sectionInserterConfigs.aliases,
			Realm: path.resolve(__dirname, 'src'),
			'@common': path.resolve(__dirname, 'src/common-components'),
			'~': `${path.resolve(__dirname, 'node_modules')}/`,
		},
	},
	optimization: {
		...defaultConfig.optimization,
		splitChunks: {
			...defaultConfig.optimization.splitChunks,
			cacheGroups: {
				...defaultConfig.optimization.splitChunks.cacheGroups,
				...sectionLoaderConfigs.cacheGroups,
				...sectionInserterConfigs.cacheGroups,
			},
		},
	},
	// Remove unnecessary plugins
	plugins: defaultConfig.plugins
		.filter((plugin) => {
			try {
				return (
					['LiveReloadPlugin'].indexOf(plugin.constructor.name) === -1
				);
			} catch (error) {
				//
			}
			return true;
		})
		.concat([
			// new BergRuntimeVars({
			// 	configFilePath: path.resolve(
			// 		__dirname,
			// 		'../../../wp-config.php'
			// 	),
			// 	prefix: 'BERG_RUNTIME_',
			// }),
			new RealmPreviewsProvider({
				realmSectionsPath: path.resolve(__dirname, 'src/sections'),
			}),
		]),
	externals: {
		react: 'React',
		'react-dom': 'ReactDOM',
	},
};
