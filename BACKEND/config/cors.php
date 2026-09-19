<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // Menentukan rute URL yang diizinkan untuk diakses oleh aplikasi luar (API & Keamanan Sanctum)
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Menentukan metode HTTP yang diizinkan untuk permintaan lintas domain (Cross-Origin) (* berarti semua: GET, POST, PUT, DELETE, dll.)
    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
