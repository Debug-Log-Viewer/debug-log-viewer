const webpack = require('webpack');
const path = require('path');

module.exports = {
    entry: './public/assets/js/app.js',
    output: {
        filename: './bundle.js',
        path: path.resolve(__dirname, 'public/dist'),
    },
    mode: 'development',
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
        }),
    ],
};
