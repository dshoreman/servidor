<?php

return [
    'default' => env('MAIL_MAILER', 'smtp'),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'system@servidor.local'),
        'name' => env('MAIL_FROM_NAME', 'Servidor Mailer'),
    ],
    'mailers' => [
        'array' => [
            'transport' => 'array',
        ],
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],
        'mailgun' => [
            'transport' => 'mailgun',
        ],
        'postmark' => [
            'transport' => 'postmark',
        ],
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => '/usr/sbin/sendmail -t -i',
        ],
        'ses' => [
            'transport' => 'ses',
        ],
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],
    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
];
