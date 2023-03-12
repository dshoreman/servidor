<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => 'Servidor',
    'debug' => (bool) env('APP_DEBUG', false),
    'env' => env('APP_ENV', 'production'),

    'url' => env('APP_URL', 'http://servidor.local') . ':8042',
    'asset_url' => env('ASSET_URL'),
    'registration_enabled' => env('APP_REGISTRATION', false),

    'locale' => 'en',
    'timezone' => 'UTC',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_GB',

    'key' => env('APP_KEY', ''),
    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    'providers' => [
        // Laravel Framework Service Providers...
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        // Package Service Providers...

        // Application Service Providers...
        Servidor\Providers\AppServiceProvider::class,
        Servidor\Providers\AuthServiceProvider::class,
        Servidor\Providers\BroadcastServiceProvider::class,
        Servidor\Providers\EventServiceProvider::class,
        Servidor\Providers\RouteServiceProvider::class,
    ],

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => Servidor\Example\ExampleClass::class,
    ])->toArray(),
];
