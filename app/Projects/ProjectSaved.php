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

    public function getApp(): ?Application
    {
        /** @var array<Application>|Collection<Application>|null $apps */
        $apps = $this->project->applications ?? null;

        if (!$apps || ($apps instanceof Collection && $apps->isEmpty())) {
            return null;
        }

        return \is_array($apps)
            ? array_values($apps)[0]
            : $apps->first();
    }

    public function getAppOrRedirect(): Application|Redirect|null
    {
        return $this->getApp() ?? $this->getRedirect();
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getRedirect(): ?Redirect
    {
        /** @var array<Redirect>|Collection<Redirect>|null $redirects */
        $redirects = $this->project->redirects ?? null;

        if (!$redirects || ($redirects instanceof Collection && $redirects->isEmpty())) {
            return null;
        }

        return \is_array($redirects)
            ? array_values($redirects)[0]
            : $redirects->first();
    }
}
