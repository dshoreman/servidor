<?php

namespace Servidor\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Servidor\Projects\CalculateSteps;
use Servidor\Projects\ProjectSaved;
use Servidor\Projects\Redirects\ApplyRedirectNginxConfig;
use Servidor\Projects\Redirects\PrepareRedirectSsl;
use Servidor\Projects\Redirects\ProjectRedirectSaved;
use Servidor\Projects\Redirects\ProjectRedirectSaving;
use Servidor\Projects\ReloadNginxService;
use Servidor\Projects\Services\ApplyNginxConfig;
use Servidor\Projects\Services\CreateSystemUser;
use Servidor\Projects\Services\DeployApp;
use Servidor\Projects\Services\PrepareSsl;
use Servidor\Projects\Services\ProjectServiceSaved;
use Servidor\Projects\Services\ProjectServiceSaving;
use Servidor\Projects\ToggleProjectVisibility;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProjectSaved::class => [
            ToggleProjectVisibility::class,
            ReloadNginxService::class,
        ],
        ProjectServiceSaving::class => [
            CalculateSteps::class,
            PrepareSsl::class,
        ],
        ProjectServiceSaved::class => [
            CreateSystemUser::class,
            ApplyNginxConfig::class,
            DeployApp::class,
            ToggleProjectVisibility::class,
            ReloadNginxService::class,
        ],
        ProjectRedirectSaving::class => [
            CalculateSteps::class,
            PrepareRedirectSsl::class,
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
