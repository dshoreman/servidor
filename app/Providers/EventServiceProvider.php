<?php

namespace Servidor\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Servidor\Projects\Applications\ApplyAppNginxConfig;
use Servidor\Projects\Applications\CreateSystemUser;
use Servidor\Projects\Applications\DeployApp;
use Servidor\Projects\Applications\ProjectAppSaved;
use Servidor\Projects\Redirects\ApplyRedirectNginxConfig;
use Servidor\Projects\Redirects\ProjectRedirectSaved;
use Servidor\Projects\ReloadNginxService;
use Servidor\Projects\ToggleProjectVisibility;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProjectAppSaved::class => [
            CreateSystemUser::class,
            ApplyAppNginxConfig::class,
            DeployApp::class,
            ToggleProjectVisibility::class,
            ReloadNginxService::class,
        ],
        ProjectRedirectSaved::class => [
            ApplyRedirectNginxConfig::class,
            ToggleProjectVisibility::class,
            ReloadNginxService::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];
}
