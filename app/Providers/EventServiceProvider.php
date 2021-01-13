<?php

namespace Servidor\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Servidor\Observers\ProjectAppObserver;
use Servidor\Projects\Application;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();

        Application::observe(ProjectAppObserver::class);
    }
}
