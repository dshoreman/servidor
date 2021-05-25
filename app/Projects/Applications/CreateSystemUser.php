<?php

namespace Servidor\Projects\Applications;

use Illuminate\Support\Str;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class CreateSystemUser
{
    public function handle(ProjectAppSaved $event): void
    {
        $app = $event->getApp();
        $project = $event->getProject();

        $step = new ProgressStep('user.create', 'Creating system user', 35);
        ProjectProgress::dispatch($project, $step);

        if (!$app->template()->requiresUser()) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_REQUIRED));

            return;
        }

        if ($app->system_user) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_EXISTS));

            return;
        }

        $user = new LinuxUser([
            'name' => Str::slug($project->name),
        ]);

        SystemUser::createCustom($user->setCreateHome(true));

        ProjectProgress::dispatch($project, $step->complete());
    }
}
