const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.options({
    hmrOptions: {
        host: 'cms.local',  // site's host name
        port: 8081
    }
});
mix.webpackConfig({
    plugins: [
        // new BundleAnalyzerPlugin()
    ],
    devServer: {
        watchOptions: {
            exclude: [/bower_components/, /node_modules/]
        },
    },
    resolve: {
        extensions: ['.js', '.json', '.vue'],
        alias: {
            '~': path.join(__dirname, './resources/js')
        }
    },
    output: {
        chunkFilename: 'js/[name].[chunkhash].js',
        // publicPath: mix.config.hmr ? '//0.0.0.0:8082' : '/'
    }
})

// mix.browserSync({
//     proxy: process.env.APP_URL
// })

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');
