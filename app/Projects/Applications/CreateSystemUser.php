<?php

namespace Servidor\Projects\Applications;

use Illuminate\Support\Str;
use Servidor\Projects\Application;
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
        $reason = $this->shouldPreventCreation($app);

        if (\is_string($reason)) {
            ProjectProgress::dispatch($project, $step->skip($reason));

            return;
        }

        $user = new LinuxUser([
            'name' => Str::slug($project->name),
        ]);

        SystemUser::createCustom($user->setCreateHome(true));

        ProjectProgress::dispatch($project, $step->complete());
    }

    private function shouldPreventCreation(Application $app): bool|string
    {
        if (!$app->template()->requiresUser()) {
            return ProgressStep::REASON_REQUIRED;
        }

        if ($app->system_user) {
            return ProgressStep::REASON_EXISTS;
        }

        return false;
    }
}
