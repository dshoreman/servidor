const mix = require('laravel-mix');

mix.options({
    webpackConfig: {
        devServer: {
            disableHostCheck: true
        }
    }
});

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/core.scss', 'public/css').options({ processCssUrls: false })
    .sass('resources/sass/app.scss', 'public/css').options({ processCssUrls: false })
    .sass('resources/sass/themes/darkmode.scss', 'public/css/dark-theme.css')
    .sass('resources/sass/themes/darkmode-custom.scss', 'public/css/dark-theme.custom.css')
    .copy('node_modules/semantic-ui-sass/icons', 'public/fonts')
