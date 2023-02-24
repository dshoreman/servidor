<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;

class ProjectSaved
{
    use SerializesModels;

    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getAppOrRedirect(): ProjectService|Redirect|null
    {
        /** @var array<ProjectService>|Collection<ProjectService> $services */
        $services = $this->project->services;
        \assert($services instanceof Collection);

        /** @var array<Redirect>|Collection<Redirect> $redirects */
        $redirects = $this->project->redirects;
        \assert($redirects instanceof Collection);

        return $services->first() ?? $redirects->first();
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
