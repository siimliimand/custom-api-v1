<?php

return [
    'env' => env('ENV', 'dev'),
    'debug' => env('DEBUG', true),

    // db
    'db' => [
        'host' => env('DB_HOST','localhost'),
        'db' => env('DB_NAME','example'),
        'user' => env('DB_USER','root'),
        'pass' => env('DB_PASS',''),
        'charset' => env('DB_CHARSET','utf8mb4')
    ],

    // redis
    'redis' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', '6379')
    ],

    // Google login
    'google' => [
        'app_name' => env('GOOGLE_APP_NAME', ''),
        'client_id' => env('GOOGLE_CLIENT_ID', '')
    ]
];
