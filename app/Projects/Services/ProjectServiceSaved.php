<?php

namespace Servidor\Projects\Services;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;

class ProjectServiceSaved
{
    use SerializesModels;

    private ProjectService $service;

    private Project $project;

    public function __construct(ProjectService $service)
    {
        $this->service = $service;
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
