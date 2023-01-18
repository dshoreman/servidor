<?php

namespace Servidor\Projects\Redirects;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;

class ProjectRedirectSaving
{
    use SerializesModels;

    private Project $project;

    public function __construct(
        private Redirect $redirect,
    ) {
        \assert($redirect->project instanceof Project);

        $this->project = $redirect->project;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getRedirect(): Redirect
    {
        return $this->redirect;
    }
}
