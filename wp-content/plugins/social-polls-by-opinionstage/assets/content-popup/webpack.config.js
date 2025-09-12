var path = require('path')

module.exports = function (env = {}) {
  return {
    entry: ['./index.js', './widget-list.js'],

    output: {
      filename: 'index.js',
      path: path.resolve(__dirname, 'build')
    },

    module: {
      rules: [
        {
          test: /\.js$/,
          loader: 'babel-loader',
          exclude: /node_modules/,
        },
        {
          test: /\.scss$/,
          use: [
            'style-loader',
            {
              loader: 'css-loader',
              options: {
                sourceMap: !env.production
              }
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: !env.production
              }
            },
            {
              loader: 'postcss-loader',
              options: {
                sourceMap: !env.production
              }
            },
          ]
        }
      ]
    },

    resolve: {
      alias: {
        'vue$': 'vue/dist/vue.esm.js',
      }
    },

    watchOptions: {
      ignored: [
        /node_modules/
      ]
    }
  }
}
