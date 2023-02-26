<?php

namespace Servidor\Projects\Services;

use Illuminate\Support\Str;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;
use Servidor\Projects\ProjectService;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class CreateSystemUser
{
    public function handle(ProjectServiceSaved $event): void
    {
        $service = $event->getService();
        $project = $event->getProject();

        if ('redirect' === $service->getType()) {
            return;
        }

        $step = new ProgressStep('user.create', 'Creating system user', 35);
        ProjectProgress::dispatch($project, $step->start());
        $reason = $this->shouldPreventCreation($service);

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

    private function shouldPreventCreation(ProjectService $service): bool|string
    {
        if (!$service->template()->requiresUser()) {
            return ProgressStep::REASON_REQUIRED;
        }

        if ($service->system_user) {
            return ProgressStep::REASON_EXISTS;
        }

        return false;
    }
}
