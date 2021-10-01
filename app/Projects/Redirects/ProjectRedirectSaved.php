<?php

namespace Servidor\Projects\Redirects;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;

class ProjectRedirectSaved
{
    use SerializesModels;

    private Redirect $redirect;

    private Project $project;

    public function __construct(Redirect $redirect)
    {
        \assert($redirect->project instanceof Project);

        $this->redirect = $redirect;
        $this->project = $redirect->project;
    }

    public function getAppOrRedirect(): Redirect
    {
        return $this->getRedirect();
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
