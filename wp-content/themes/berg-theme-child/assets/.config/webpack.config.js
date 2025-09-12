const path = require('path'),
  glob = require('glob'),
  TerserPlugin = require('terser-webpack-plugin'),
  MiniCssExtractPlugin = require('mini-css-extract-plugin'),
  webpack = require('webpack'),
  CopyWebpackPlugin = require('copy-webpack-plugin'),
  RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts'),
  WebpackNotifierPlugin = require('webpack-notifier'),
  ESLintPlugin = require('eslint-webpack-plugin'),
  StylelintPlugin = require('stylelint-webpack-plugin');

module.exports = (_env, argv) => {
  const isProduction = argv.mode == 'production';
  const coreFilesAllowed = _env.core ?? false;

  const entryObj = {
    'js/main': glob.sync('./js/**/*.js', {
      ignore: [
        './js/admin/**/*.js',
        './js/berg/counterup.js',
        './js/berg/lottie-animator.js',
        './js/berg/post-block.js',
        './js/berg/slick-slider.js',
        './js/berg/fancybox-override.js',
        './js/pdf_embd_blk_xt.js'
      ]
    }),
    'js/counterup': path.resolve(__dirname, '../js/berg/counterup.js'),
    'js/lottie': path.resolve(__dirname, '../js/berg/lottie-animator.js'),
    'js/select2': path.resolve(__dirname, '../js/berg/post-block.js'),
    'js/slick': path.resolve(__dirname, '../js/berg/slick-slider.js'),
    'js/fancybox': path.resolve(__dirname, '../js/berg/fancybox-override.js'),
    'js/admin_scripts': path.resolve(__dirname, '../js/admin/index.js'),
    'css/admin-styles': path.resolve(__dirname, '../scss/admin-styles.scss'),
    'css/editor-styles': path.resolve(__dirname, '../scss/editor-styles.scss'),
    'css/style': path.resolve(__dirname, '../scss/style.scss'),
    'js/pdf_embd_blk_xt': path.resolve(__dirname, '../js/pdf_embd_blk_xt.js'),
  }

  if(coreFilesAllowed){
    Object.assign(entryObj, { 'css/core-components': glob.sync('./../../../plugins/berg/src/block/**/*---default.scss')});
  }

  return {
    devtool: isProduction ? false : 'eval',
    watchOptions: {
      ignored: ['vendor', 'inc', 'node_modules'],
    },
    entry: entryObj,
    output: {
      path: path.resolve(__dirname, '../../dist'),
      filename: '[name].js',
      clean: {
        keep: 'css/core-components',
      },
    },
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
      parser: {
        javascript: {
          exportsPresence: 'error',
        },
      },
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env'],
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
            { loader: 'css-loader', options: { url: true, sourceMap: true } },
            { loader: 'postcss-loader', options: { sourceMap: true } },
            { loader: 'sass-loader', options: { sourceMap: true } },
          ],
        },
        {
          test: /\.css$/i,
          use: ['style-loader', 'css-loader'],
        },
      ],
    },
    plugins: [
      new StylelintPlugin(),
      new ESLintPlugin(),
      new WebpackNotifierPlugin({
        title: 'Berg Theme Child',
        emoji: true,
        alwaysNotify: true,
        excludeWarnings: true,
        contentImage: path.join(__dirname, 'logo-berg.png'),
      }),
      new RemoveEmptyScriptsPlugin(),
      new MiniCssExtractPlugin(),
      new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
        _: 'underscore',
        'window.Isotope': 'Isotope'
      }),
      new webpack.SourceMapDevToolPlugin({ filename: '[name][ext].map' }),
      new CopyWebpackPlugin({
        patterns: [
          { from: './images/*', to: '../dist/' },
          { from: './fonts/*', to: '../dist/' },
        ],
        options: {
          concurrency: 100,
        },
      }),
    ],
    resolve: {
      extensions: ['.js', '.scss'],
      symlinks: false,
      modules: [path.resolve(__dirname, '..', 'node_modules')],
      alias: {
        '~bergBlockPath': path.resolve(__dirname, '../../../../plugins/berg/src/block/'),
      },
    },
    optimization: {
      minimize: isProduction,
      minimizer: [new TerserPlugin()],
    },
  };
};
