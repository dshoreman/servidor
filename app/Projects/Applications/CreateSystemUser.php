<?php

namespace Servidor\Projects\Applications;

use Illuminate\Support\Str;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class CreateSystemUser
{
    public function handle(ProjectAppSaved $event): void
    {
        if ($event->app->template()->requiresUser() && !$event->app->system_user) {
            /** @var \Servidor\Projects\Project */
            $project = $event->app->project;

            $user = new LinuxUser([
                'name' => Str::slug($project->name),
            ]);

            SystemUser::createCustom($user->setCreateHome(true));
        }
    }
}
