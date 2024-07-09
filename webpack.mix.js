const mix = require('laravel-mix');

mix
    .styles([
        'public/assets/css/bootstrap.min.css',
        'public/assets/css/icons.min.css',
        'public/assets/css/app.min.css',
        'public/assets/css/custom.min.css',
        'public/assets/toastify/toastify.min.css'
    ], 'public/assets/css/minipay.css')
    .version();

mix
    .scripts([
        'public/assets/js/pages/plugins/lord-icon-2.1.0.js',
        'public/assets/toastify/toastify.js'
    ], 'public/assets/js/minipay.js')
    .version();