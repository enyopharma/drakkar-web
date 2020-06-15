const path = require('path')

const ManifestPlugin = require('webpack-manifest-plugin')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
const MiniCssExtractPlugin = require("mini-css-extract-plugin")

module.exports = (env, argv) => {
    return {
        entry: {
            app: './assets/js/app.js',
            form: './assets/js/form/index.ts',
            table: './assets/js/table/index.ts',
        },
        devtool: argv.mode == 'development' ? 'inline-source-map' : false,
        output: {
            filename: '[name].[contenthash].js',
            path: path.resolve(__dirname, './public/build'),
            publicPath: '/build/',
        },
        resolve: {
            extensions: ['.js', '.ts', '.tsx', '.scss'],
        },
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env', '@babel/preset-react']
                        },
                    },
                },
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'sass-loader',
                    ],
                },
                {
                    test: /\.tsx?$/,
                    exclude: /node_modules/,
                    use: [
                        {
                            loader: "ts-loader",
                        },
                    ],
                },
                {
                    enforce: "pre",
                    test: /\.js$/,
                    loader: "source-map-loader"
                },
            ],
        },
        plugins: [
            new ManifestPlugin,
            new CleanWebpackPlugin,
            new MiniCssExtractPlugin({
                filename: '[name].[contenthash].css',
            }),
        ],
        optimization: {
            moduleIds: 'hashed',
            runtimeChunk: 'single',
        },
    }
}
