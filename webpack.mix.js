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

mix.version();

mix.js('resources/js/app.js', 'public/js').vue({
    version: 3,
    options: {
      compilerOptions: {
        compatConfig: {
          MODE: 2,
        },
      },
    },
  }).sass('resources/sass/app.scss', 'public/css').sourceMaps();

mix.webpackConfig(() => {
    return {
      resolve: {
        alias: {
          vue: "@vue/compat",
        }
      },
    }
  })

mix.scripts([
    'resources/js/card-search.js',
    'resources/js/frontend.js',
    'resources/js/reader.js',
    'resources/js/dark.js',
    'node_modules/jquery-touchswipe/jquery.touchSwipe.min.js',
], 'public/js/reader.js');

mix.js('resources/js/bootstrap.js', 'public/js');

mix.scripts([
    'resources/js/card-search.js',
    'resources/js/bscustomfile.min.js',
    'resources/js/backend.js',
    'resources/js/dark.js',
    'node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
    'node_modules/blueimp-load-image/js/load-image.all.min.js',
    'node_modules/blueimp-tmpl/js/tmpl.min.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload-process.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload-image.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload-ui.js',
    'resources/js/jquery.fileupload-setup.js',
], 'public/js/admin.js');

mix.styles([
    'resources/css/frontend.css',
    'resources/css/reader.css',
    'resources/css/dark.css',
], 'public/css/reader.css');

mix.styles([
    'node_modules/blueimp-file-upload/css/jquery.fileupload.css',
    'node_modules/blueimp-file-upload/css/jquery.fileupload-ui.css',
    'resources/css/admin.css',
    'resources/css/dark.css',
], 'public/css/admin.css');

mix.copyDirectory('node_modules/blueimp-file-upload/img', 'public/img');
mix.copyDirectory('resources/js/lang', 'public/js/lang');
mix.copy('node_modules/blueimp-tmpl/js/tmpl.min.js.map', 'public/js');
