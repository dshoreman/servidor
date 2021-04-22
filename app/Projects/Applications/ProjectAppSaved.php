<?php

namespace Servidor\Projects\Applications;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Application;
use Servidor\Projects\Project;

class ProjectAppSaved
{
    use SerializesModels;

    public Application $app;

    public Project $project;

    public function __construct(Application $app)
    {
        assert($app->project instanceof Project);

        $this->app = $app;
        $this->project = $app->project;
    }
}
