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
        if ($app->template()->requiresUser() && !$app->system_user) {
            SystemUser::createCustom((new LinuxUser([
                'name' => Str::slug($app->project->name),
            ]))->setCreateHome(true));
        }
    }
}
