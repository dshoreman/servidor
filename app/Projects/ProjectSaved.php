<?php

namespace Servidor\Projects;

use Illuminate\Queue\SerializesModels;

class ProjectSaved
{
    use SerializesModels;

    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getService(): ?ProjectService
    {
        $services = $this->project->services;

        if (0 === $services->count()) {
            return null;
        }

        $service = $services->first();
        \assert($service instanceof ProjectService);

        return $service;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
