<?php

namespace Servidor\Observers;

use Illuminate\Support\Str;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class ProjectAppObserver
{
    public function saved(Application $app): void
    {
        /** @var \Servidor\Projects\Project */
        $project = $app->project;

        if ($app->template()->requiresUser() && !$app->system_user) {
            $this->createSystemUser($project->name);
        }

        if ($app->source_repository && $app->domain_name) {
            $this->syncWithNginx($project, $app);
            exec('sudo systemctl reload-or-restart nginx.service');
        }
    }

    private function createSystemUser(string $name): void
    {
        $user = new LinuxUser([
            'name' => Str::slug($name),
        ]);

        SystemUser::createCustom($user->setCreateHome(true));
    }

    private function syncWithNginx(Project $project, Application $app): void
    {
        $app->writeNginxConfig();

        if ($project->is_enabled) {
            $app->template()->pullCode(true);

            return;
        }

        $app->template()->disable();
    }
}
