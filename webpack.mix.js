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

mix.js('resources/js/app.js', 'public/js').vue()
    .sass('resources/sass/app.scss', 'public/css').sourceMaps();

mix.scripts([
    'node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
    'node_modules/blueimp-load-image/js/load-image.all.min.js',
    'node_modules/blueimp-tmpl/js/tmpl.min.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload-process.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload-image.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload-ui.js',
    ], 'public/js/jquery.fileupload-all.js');

mix.styles([
    'node_modules/blueimp-file-upload/css/jquery.fileupload.css',
    'node_modules/blueimp-file-upload/css/jquery.fileupload-ui.css',
], 'public/css/jquery.fileupload-all.css');

mix.copyDirectory('node_modules/blueimp-file-upload/img', 'public/img');
mix.copy('node_modules/blueimp-tmpl/js/tmpl.min.js.map', 'public/js');
mix.copy('node_modules/jquery-touchswipe/jquery.touchSwipe.min.js', 'public/js');
