// Symfony encore configuration
var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('./app/http/public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create web/build/app.js and web/build/app.css
    .addEntry('app', './app/http/assets/js/app.js')
    .addEntry('form', './app/http/assets/js/form/index.js')
    .addEntry('search', './app/http/assets/js/search/index.js')
    .addEntry('table', './app/http/assets/js/table/index.js')

    // enable runtime chunks
    .enableSingleRuntimeChunk()

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning()

    // enable reactjs
    .enableReactPreset()
    ;

module.exports = Encore.getWebpackConfig();
