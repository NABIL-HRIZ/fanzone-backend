<?php


return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'docs/*', 'api/documentation'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('APP_URL', 'fanzone-backend-production.up.railway.app'),
        env('FRONTEND_URL', 'https://fanzone-frontend.vercel.app/'),
        'fanzone-backend-production.up.railway.app',
        'http://127.0.0.1:8000'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
