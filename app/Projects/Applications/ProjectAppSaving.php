<?php

namespace Servidor\Projects\Applications;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Application;
use Servidor\Projects\Project;

class ProjectAppSaving
{
    use SerializesModels;

    private Project $project;

    public function __construct(
        private Application $app,
    ) {
        \assert($app->project instanceof Project);

        $this->project = $app->project;
    }

    public function getApp(): Application
    {
        return $this->app;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
