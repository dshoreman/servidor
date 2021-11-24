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

    public function getAppOrRedirect(): Application|Redirect|null
    {
        /** @var array<Application>|Collection<Application> $apps */
        $apps = $this->project->applications;
        \assert($apps instanceof Collection);

        /** @var array<Redirect>|Collection<Redirect> $redirects */
        $redirects = $this->project->redirects;
        \assert($redirects instanceof Collection);

        return $apps->first() ?? $redirects->first();
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
