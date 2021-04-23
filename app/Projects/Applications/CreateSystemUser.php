<?php

namespace Servidor\Projects\Applications;

use Illuminate\Support\Str;
use Servidor\Events\ProjectProgress;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class CreateSystemUser
{
    public function handle(ProjectAppSaved $event): void
    {
        /** @var \Servidor\Projects\Project */
        $project = $event->app->project;

        if (!$event->app->template()->requiresUser()) {
            ProjectProgress::dispatch($project, 'Skipping system user, not required.');

            return;
        }

        if ($event->app->system_user) {
            ProjectProgress::dispatch($project, 'Skipping system user, it already exists.');

            return;
        }

        ProjectProgress::dispatch($project, 'Creating system user...');

        $user = new LinuxUser([
            'name' => Str::slug($project->name),
        ]);

        SystemUser::createCustom($user->setCreateHome(true));

        ProjectProgress::dispatch($project, ' done.' . PHP_EOL);
    }
}
