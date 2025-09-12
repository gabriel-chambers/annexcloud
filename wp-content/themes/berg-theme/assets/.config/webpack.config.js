const path = require('path'),
	glob = require('glob'),
	TerserPlugin = require('terser-webpack-plugin'),
	MiniCssExtractPlugin = require('mini-css-extract-plugin'),
	webpack = require("webpack"),
	CopyWebpackPlugin = require("copy-webpack-plugin"),
	WebpackNotifierPlugin = require('webpack-notifier'),
	RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = (_env, argv) => {
	const isProduction = argv.mode == 'production';
	return {
		devtool: isProduction ? false : "eval",
		watchOptions: {
			ignored: ['./*', 'vendor', 'inc', 'node_modules'],
		},
		entry: {
			'js/vendor': glob.sync('./js/vendor/**.js'),
			'js/admin_scripts': path.resolve(__dirname, '../js/admin/index.js'),
			'css/vendor': glob.sync('./scss/vendor/*.scss'),
		},
		output: {
			path: path.resolve(__dirname, '../../dist'),
			filename: '[name].js',
			clean: true,
		},
		// Clean up build output
		stats: {
			all: false,
			assets: true,
			colors: true,
			errors: true,
			performance: true,
			timings: true,
			warnings: false,
		},
		module: {
			strictExportPresence: true,
			rules: [
				{
					test: /\.js$/,
					exclude: /node_modules/,
					use: {
						loader: "babel-loader",
						options: {
							presets: ["@babel/preset-env"],
						},
					},
				},
				{
          test: /\.(png|jpe?g|gif|svg|webp)$/i,
          type: 'javascript/auto',
          exclude: [/fonts/],
          use: [
            {
              loader: 'file-loader',
              options: {
                outputPath: 'images/',
                name: '[name].[ext]?v=[hash]',
                esModule: false,
              },
            },
          ],
        },
        {
          test: /\.(woff|woff2|eot|ttf|otf|svg)$/,
          type: 'javascript/auto',
          include: [/fonts/],
          use: [
            {
              loader: 'file-loader',
              options: {
                outputPath: 'fonts/',
                name: '[name].[ext]?v=[hash]',
                esModule: false,
              },
            },
          ],
        },
        {
          test: /\.s[ac]ss$/i,
          use: [
            MiniCssExtractPlugin.loader,
            { loader: 'css-loader', options: { url: true, sourceMap: true }, },
            { loader: 'postcss-loader', options: { sourceMap: true } },
            { loader: 'sass-loader', options: { sourceMap: true } },
          ],
        },
        {
          test: /\.css$/i,
          use: [
            'style-loader',
            'css-loader'
          ],
        },
			],
		},
		plugins: [
			new WebpackNotifierPlugin({
				title: 'Berg Theme',
				emoji: true,
				alwaysNotify: true,
				excludeWarnings: true,
				contentImage: path.join(__dirname, 'logo-berg.png'),
			}),
			new RemoveEmptyScriptsPlugin(),
			new MiniCssExtractPlugin(),
			new webpack.ProvidePlugin({
				$: "jquery",
				jQuery: "jquery",
				"window.jQuery": "jquery",
				_: "underscore",
				"window.Isotope": "Isotope",
			}),
			new webpack.SourceMapDevToolPlugin({ filename: "[name][ext].map" }),
			new CopyWebpackPlugin({
				patterns: [
					{ from: "./images/*", to: "../dist/" },
					{ from: "./fonts/*", to: "../dist/" },
				],
				options: {
					concurrency: 100,
				},
			}),
		],
		resolve: {
			extensions: ['.js', '.scss'],
			symlinks: false,
		},
		optimization: {
			minimize: isProduction,
			minimizer: [new TerserPlugin()],
		},
	};
};
