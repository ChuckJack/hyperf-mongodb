<?php
declare(strict_types=1);

return [
    'default' => [
        'host'     => [
            env('MONGODB_HOST', '127.0.0.1')
        ],
        'port'     => (int)env('MONGODB_PORT', 27017),
        'database' => env('MONGODB_DB', 'test'),
        'username' => env('MONGODB_USER', 'root'),
        'password' => env('MONGODB_PASS', '123456'),
        'auth_db'  => env('MONGODB_AUTH_DB', 'test'),
        'options'  => [
            'database' => env('MONGODB_DB', 'admin'),
        ],
        'pool'     => [
            'min_connections' => 10,
            'max_connections' => 100,
            'connect_timeout' => 10.0,
            'wait_timeout'    => 3.0,// 90
            'heartbeat'       => -1,
            'max_idle_time'   => (float)env('DB_MAX_IDLE_TIME', 60),
        ],
    ],
];