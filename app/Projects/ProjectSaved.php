<?php

namespace Servidor\Projects;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\ItemNotFoundException;

class ProjectSaved
{
    use SerializesModels;

    public function __construct(
        private Project $project,
    ) {
    }

    public function getAppOrRedirect(): Application|Redirect|null
    {
        try {
            return $this->project->applications->firstOrFail();
        } catch (ItemNotFoundException $_) {
            return $this->project->redirects->first();
        }
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
