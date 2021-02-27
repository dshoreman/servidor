const mix = require('laravel-mix');

mix.options({
    webpackConfig: {
        devServer: {
            disableHostCheck: true,
        },
    },
});

mix.js('resources/js/app.js', 'public/js').vue();

mix.sass('resources/sass/app.scss', 'public/css').options({
    processCssUrls: false,
}).sass('resources/sass/themes/default.scss', 'public/css/theme.light.css').options({
    processCssUrls: false,
});

mix.sass('resources/sass/themes/darkmode.scss', 'public/css/theme.dark.css')
    .sass('resources/sass/themes/darkmode-custom.scss', 'public/css/theme.dark-custom.css')
    .copy('node_modules/semantic-ui-sass/icons', 'public/fonts');
