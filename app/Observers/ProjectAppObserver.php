<?php

namespace Servidor\Observers;

use Illuminate\Support\Str;
use Servidor\Projects\Application;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class ProjectAppObserver
{
    public function saved(Application $app): void
    {
        /** @var \Servidor\Projects\Project */
        $project = $app->project;

        if ($app->template()->requiresUser() && !$app->system_user) {
            SystemUser::createCustom((new LinuxUser([
                'name' => Str::slug($project->name),
            ]))->setCreateHome(true));
        }

        if ($app->source_repository) {
            $app->writeNginxConfig();
            $app->template()->pullCode();
        }

        $project->is_enabled ? $app->template()->enable() : $app->template()->disable();

        exec('sudo systemctl reload-or-restart nginx.service');
    }
}
