"use strict";

module.exports = {
    entry: [
        './src/index.jsx'
    ],
    module: {
        rules: [{
            test: /\.jsx?$/,
            exclude: /node_modules/,
            loader: require.resolve('babel-loader'),
        }]
    },
    resolve: {
        extensions: ['.js', '.jsx']
    },
    output: {
        path: __dirname + '/../../../../dist/web/theme/admin/phpjetdesktop',
        filename: 'js/desktop.js'
    }
};