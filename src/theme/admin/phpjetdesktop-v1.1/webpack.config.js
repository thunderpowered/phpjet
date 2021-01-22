const webpack = require('webpack');

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
        path: __dirname + '/../../../../dist/web/theme/admin/phpjetdesktop-v1.1',
        // publicPath: '/',
        filename: 'js/desktop.js'
    }
    // devServer: {
    //     contentBase: './',
    //     hot: true
    // },
    // plugins: [
    //     new webpack.HotModuleReplacementPlugin()
    // ]
};