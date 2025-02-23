const webpack = require("webpack");
const path = require("path");

module.exports = {
    entry: path.resolve(__dirname, "public/assets/js/app.js"),
    output: {
        filename: "bundle.js",
        path: path.resolve(__dirname, "public/dist"),
    },
    mode: "production", // Use "development" if debugging
    devtool: false, // No source maps for production
    externals: {
        jquery: "jQuery", // Avoid bundling WP jQuery
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
        }),
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/, // Ignore node_modules for performance
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["@babel/preset-env"],
                    },
                },
            },
        ],
    },
};
