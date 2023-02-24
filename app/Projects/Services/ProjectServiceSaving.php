<?php

namespace Servidor\Projects\Services;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;

class ProjectServiceSaving
{
    use SerializesModels;

    private Project $project;

    public function __construct(
        private ProjectService $service,
    ) {
        \assert($service->project instanceof Project);

        $this->project = $service->project;
    }

    public function getService(): ProjectService
    {
        return $this->service;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
