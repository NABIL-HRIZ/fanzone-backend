<?php


return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'docs/*', 'api/documentation'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('APP_URL', 'http://localhost:8000'),
        env('FRONTEND_URL', 'http://localhost:5173'),
        'http://localhost:8000',
        'http://127.0.0.1:8000'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
