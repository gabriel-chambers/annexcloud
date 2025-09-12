const path = require("path"),
  externals = require("./externals"),
  CssMinimizerPlugin = require("css-minimizer-webpack-plugin"),
  glob = require("glob"),
  MiniCssExtractPlugin = require("mini-css-extract-plugin"),
  RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts"),
  webpack = require("webpack"),
  TerserPlugin = require("terser-webpack-plugin"),
  WebpackNotifierPlugin = require("webpack-notifier")

module.exports = (_env, argv) => {
  const isProduction = argv.mode == "production"
  return {
    devtool: isProduction ? false : "eval",
    entry: {
      editor_blocks_scripts: path.resolve(__dirname, "../src/blocks.js"),
      frontend_blocks_scripts: path.resolve(__dirname, "../src/block-frontend.js"),
      editor_blocks_styles: glob.sync("./src/block/*/sass/editor.scss"),
      frontend_blocks_styles: glob.sync("./src/block/*/sass/style.scss"),
    },
    output: {
      path: path.resolve(__dirname, "../dist"),
      publicPath: path.resolve(__dirname, "../dist"),
      filename: "[name].js",
      clean: true,
      sourceMapFilename: "[file].map?v=[hash]",
    },
    // Permit importing @wordpress/* packages.
    externals,
    optimization: {
      minimize: isProduction,
      minimizer: [
        new TerserPlugin(),
        new CssMinimizerPlugin({
          minimizerOptions: {
            preset: [
              "default",
              {
                discardComments: { removeAll: true },
              },
            ],
          },
        }),
      ],
      splitChunks: {
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            chunks: "initial",
            name: "common_vendor",
            priority: 10,
            enforce: true,
          },
        },
      },
    },
    resolve: {
      extensions: [".js", ".scss"],
      preferRelative: true,
      symlinks: false,
      alias: {
        "~bergcustom": path.resolve(__dirname, "../src/"),
        "~berg": path.resolve(__dirname, "../../berg/src"),
      },
    },
    stats: {
      all: false,
      assets: true,
      colors: true,
      errors: true,
      performance: false,
      timings: true,
      warnings: false,
    },
    module: {
      parser: {
        javascript: {
          exportsPresence: "error",
        },
      },
      strictExportPresence: true,
      rules: [
        {
          test: /\.js$/,
          exclude: /(node_modules|bower_components)/,
          use: {
            loader: "babel-loader",
            options: {
              presets: ["@babel/preset-env"],
              cacheDirectory: true,
              plugins: [
                "@babel/plugin-proposal-class-properties",
                "@babel/plugin-transform-destructuring",
                "@babel/plugin-proposal-object-rest-spread",
                "@babel/plugin-proposal-optional-chaining",
                [
                  "@babel/plugin-transform-react-jsx",
                  {
                    pragma: "wp.element.createElement",
                  },
                ],
              ],
            },
          },
        },
        {
          test: /\.s(a|c)ss$/,
          use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
        },
        {
          test: /\.(jpe?g|png|gif|svg)$/i,
          type: "asset",
        },
      ],
    },
    plugins: [
      new WebpackNotifierPlugin({
        title: "Berg Custom",
        emoji: true,
        alwaysNotify: true,
        contentImage: path.join(__dirname, "logo-berg.png"),
        excludeWarnings: true,
      }),
      new RemoveEmptyScriptsPlugin(),
      new webpack.SourceMapDevToolPlugin({
        filename: "[name][ext].map",
      }),
      new MiniCssExtractPlugin({
        filename: "[name].css",
        ignoreOrder: true,
      }),
    ],
    watchOptions: {
      ignored: ["**/*.php"],
    },
  }
}
